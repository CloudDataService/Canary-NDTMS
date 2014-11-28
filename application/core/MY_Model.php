<?php
/**
 * Base model
 *
 * @author CR
 */

class MY_Model extends CI_Model
{


	/**
	 * The database table to use
	 *
	 * @var string
	 */
	protected $_table;


	/**
	 * The primary key, by default set to `id`, for use in some functions.
	 *
	 * @var string
	 */
	protected $_primary = 'id';


	/**
	 * Order by data. Array of arrays: Array($col, $sort[asc|desc])
	 *
	 * @var array
	 */
	protected $_order = array();


	/**
	 * SQL limit value
	 *
	 * @var int
	 */
	protected $_limit;


	/**
	 * SQL offset value
	 *
	 * @param int
	 */
	protected $_offset;


	/**
	 * Specify the lookup type (where or like) for each filterable parameter/db col.
	 *
	 * If the db column isn't here - it doesn't get filtered on.
	 *
	 * @var array
	 */
	protected $_filter_types = array(
		'where' => array(),
		'like' => array(),
		'in' => array(),
		'function' => array(),
	);


	protected $_filter_data = array();


	protected $_filter_sql_extra = array();


	/**
	 * Wrapper to __construct for when loading
	 * class is a superclass to a regular controller,
	 * i.e. - extends Base not extends Controller.
	 *
	 * @return void
	 */
	public function MY_Model() { $this->__construct(); }


	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->db->protect_identifiers($this->_table);
	}




	/**
	 * Set values for ORDER BY SQL
	 *
	 * @param string $col		Column name to order on
	 * @param string $sort		Sort type. ASC or DESC.
	 * @return $this
	 */
	public function order_by($col, $sort = 'ASC')
	{
		$this->_order[] = array($col, $sort);
		return $this;
	}




	/**
	 * Apply SQL limit
	 */
	public function limit($value, $offset = NULL)
	{
		$this->_limit = (int) $value;
		if ($offset !== NULL)
		{
			$this->_offset = (int) $offset;
		}
		return $this;
	}




	/**
	 * Take an array of keys and values and apply them as a filter to the database.
	 *
	 * Checks for valid columns in the filter_types array first and then fills an
	 * object instance array of the columns and data.
	 */
	public function set_filter($params = array())
	{
		if (empty($params))
		{
			return $this;
		}

		// Loop through the valid filter columns.
		// This ensures we don't query on anything we shouldn't be.
		foreach ($this->_filter_types as $type => $fields)
		{
			// Loop through the fields that are acceptable for this type (where/like)
			foreach ($fields as $f)
			{
				// Get the filter param value for this field
				if (array_key_exists($f, $params))
				{
					$value = $params[$f];
					if ( ! is_array($value) && strlen($value) == 0)
					{
						$value = NULL;
					}
				}
				else
				{
					$value = NULL;
				}

				if (is_array($value) && $type === 'in')
				{
					$this->_filter_data[$type][$f] = $value;
				}
				elseif ($type === 'function' && array_key_exists($f, $params) && method_exists($this, '_filter_' . $f))
				{
					$this->_filter_data[$type]['_filter_' . $f] = $value;
				}
				elseif ($value !== NULL && ! is_array($value) && $type !== 'in')
				{
					// Add the value and field to instance array if present
					$this->_filter_data[$type][$f] = $value;
				}
			}
		}

		return $this;
	}




	/**
	 * Clear the filter array data
	 *
	 * @param string $param		If present, only clear this param from ALL types where it is used. If not used, ALL filter params will be cleared
	 * @return $this
	 */
	public function clear_filter($param = NULL)
	{
		if ($param === NULL)
		{
			$this->_filter_data = array();
		}
		else
		{
			foreach ($this->_filter_types as $type => $params)
			{
				if (isset($this->_filter_data[$type]["_filter_{$param}"]))
				{
					unset($this->_filter_data[$type]["_filter_{$param}"]);
				}
			}
		}

		return $this;
	}




	/**
	 * Get all rows from the table
	 *
	 * @return array
	 */
	public function get_all()
	{
		$sql = 'SELECT *
				FROM `' . $this->_table . '`
				WHERE 1 = 1 ' .
				$this->filter_sql() .
				$this->order_sql() .
				$this->limit_sql();

		return $this->db->query($sql)->result_array();
	}




	/**
	 * Get one row from the table where ID matches supplied ID
	 *
	 * @param mixed $id		Primary key ID of row to retrieve
	 * @return array
	 */
	public function get($id = 0)
	{
		$sql = 'SELECT
					*
				FROM
					`' . $this->_table . '`
				WHERE
					`' . $this->_primary . '` = ?
				LIMIT 1';

		return $this->db->query($sql, array($id))->row_array();
	}




	/**
	 * Get rows where the key matches value
	 */
	public function get_by($key, $value, $where_extra = '')
	{
		$sql = 'SELECT *
				FROM `' . $this->_table . '`
				WHERE `' . $key .'` = ?' .
				$where_extra;

		$query = $this->db->query($sql, array($value));

		if ($query->num_rows() == 1 && (int) $this->_limit == 1)
		{
			return $query->row_array();
		}
		else
		{
			return $query->result_array();
		}
	}




	/**
	 * Insert a new record into the DB table with the column values set by the array.
	 *
	 * @param array $data		Array of column names => values to set
	 * @return mixed		Auto-increment ID on success, FALSE on failure
	 */
	public function insert($data = array())
	{
		$query = $this->db->query($this->db->insert_string($this->_table, $data));
		if ($query)
		{
			$id = $this->db->insert_id();
			// insert_id() returns 0 on non-auto-generated IDs - in this case, need to return result of $query
			return ($id === 0) ? $query : $id;
		}
		else
		{
			return FALSE;
		}
	}




	/**
	 * Perform an update query based on parameters.
	 *
	 * Example:
	 * 	update(6, array('foo' => 'bar'), 'active = 1 AND date >= NOW()')
	 *
	 * @param int $id		ID/primary key value of row to update
	 * @param array $data		Array of db cols => values to set
	 * @param string $where_extra		In addition to updating on primary key - additional clause
	 * @return bool
	 */
	public function update($id, $data = array(), $where_extra = '')
	{
		$where = ' `' . $this->_primary . '` = ' . $this->db->escape($id) . ' ';

		if ($where_extra != '')
		{
			$where .= ' AND ' . $where_extra;
		}

		$query = $this->db->query($this->db->update_string($this->_table, $data, $where));
		return ($query) ? $id : $query;
	}




	public function set($id = 0, $data = array(), $where_extra = '')
	{
		if ($id === 0)
		{
			return $this->insert($data);
		}
		else
		{
			return $this->update($id, $data, $where_extra);
		}
	}




	/**
	 * Delete an item
	 *
	 * @param mixed $id		ID of value in the primary key field of the row you want to delete
	 */
	public function delete($id)
	{
		$sql = 'DELETE FROM `' . $this->_table . '`
				WHERE `' . $this->_primary . '` = ?
				LIMIT 1';
		return $this->db->query($sql, array($id));
	}




	/**
	 * Count all rows in table without any filtering
	 */
	 public function count_all()
	 {
		if (isset($this->_primary))
		{
			$sql = 'SELECT COUNT(`' . $this->_primary . '`) AS total
					FROM `' . $this->_table . '`
					WHERE 1 = 1
					' . $this->filter_sql();

			$row = $this->db->query($sql)->row_array();
			return (int) $row['total'];
		}
		else
		{
			return $this->db->count_all($this->_table);
		}
	 }




	/**
	 * Get the filter SQL
	 *
	 * @param string $operator		Specify whether to use AND or OR
	 * @return the SQL string
	 */
	protected function filter_sql($operator = 'AND')
	{
		$str = '';

		if ( ! empty($this->_filter_data['like']))
		{
			foreach ($this->_filter_data['like'] as $col => $val)
			{
				$str .= " $operator `$col` LIKE '%" . $this->db->escape_like_str($val) . "%' \n";
			}
		}

		if ( ! empty($this->_filter_data['where']))
		{
			foreach ($this->_filter_data['where'] as $col => $val)
			{
				$str .= " $operator `$col` = " . $this->db->escape($val) . " \n";
			}
		}

		if ( ! empty($this->_filter_data['in']))
		{
			foreach ($this->_filter_data['in'] as $col => $vals)
			{
				foreach ($vals as &$val)
				{
					$val = $this->db->escape($val);
				}

				$val_str = implode(',', $vals);
				if ( ! empty($val_str)) $str .= " $operator `$col` IN ($val_str) \n";
			}
		}

		if ( ! empty($this->_filter_data['function']))
		{
			foreach ($this->_filter_data['function'] as $fn => $param)
			{
				$str .= ' ' . call_user_func_array(array($this, $fn), array($param, $operator)) . " \n";
			}
		}

		if ( ! empty($this->_filter_sql_extra))
		{
			$str .= implode(" \n ", $this->_filter_sql_extra);
		}

		return $str;
	}




	/**
	 * Return the SQL string for ORDER BY statement
	 */
	protected function order_sql()
	{
		if (empty($this->_order))
		{
			return '';
		}

		$strings = array();

		foreach ($this->_order as $order)
		{
			$strings[] = '`' . $order[0] . '` ' . $order[1] . ' ';
		}

		return ' ORDER BY ' . implode(', ', $strings);
	}




	/**
	 * Return the SQL string for the LIMIT statement
	 */
	protected function limit_sql()
	{
		if ($this->_offset == '' && $this->_limit == '')
		{
			return '';
		}

		$offset = $this->_offset;

		if ($this->_offset == 0)
		{
			$offset = '';
		}
		else
		{
			$offset .= ', ';
		}

		return " LIMIT " . $offset . $this->_limit;
	}




	/**
	 * Retrieve a dropdown-friendly array of table data in key => value format.
	 *
	 * Examples:
	 *
	 * 	dropdown('c_name');		// uses primary key value from class
	 * 	dropdown('c_id', 'c_name');		// specify primary key and the value
	 * 	dropdown('c_id', 'c_name', 'c_enabled');		// only where c_enabled = 1
	 *
	 * @param string $key		The ID column. If not present, uses instance variable
	 * @param string $value		The value column to get. Must be present
	 * @param string $enabled		The boolean "enabled" column to check equals 1
	 *
	 * @return array
	 */
	function dropdown()
	{
		$args =& func_get_args();

		if (count($args) == 3)
		{
			list($key, $value, $enabled) = $args;
		}
		if (count($args) == 2)
		{
			list($key, $value) = $args;
			$enabled = '';
		}
		else
		{
			$key = $this->_primary;
			$value = $args[0];
			$enabled = '';
		}

		// Build SQL string to select the key and value

		$sql = " SELECT `$key`, `$value` FROM `{$this->_table}` WHERE 1 = 1 ";

		if ($enabled != '')
		{
			$sql .= " AND `$enabled` = 1 ";
		}

		$sql .= " ORDER BY `$value` ASC ";

		$result = $this->db->query($sql)->result_array();

		$options = array();

		foreach ($result as $row)
		{
			$options[$row[$key]] = $row[$value];
		}

		return $options;
	}


}




class Reports_model extends MY_Model
{


	/**
	 * Reports should be filtered on
	 *
	 * Location?
	 * Key Worker/Recovery Coach		j_rc_id
	 * Internal Service					?
	 * Journey Type						j_type IN('C','F')
	 * Referral from/to					j_date_of_referral
	 * Closure from/to
	 */
	protected $_filter_types = array(
		'where' => array('j_c_id', 'j_rc_id', 'j_status', 'c_catchment_area'),
		'like' => array(),
		'in' => array('j_status', 'j_type'),
		'function' => array('referral_from', 'referral_to', 'closure_from', 'closure_to'),
	);


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Filter: date of referral after date
	 */
	protected function _filter_referral_from($value = '', $operator = '')
	{
		return " $operator `j_date_of_referral` >= " . $this->db->escape($value) . " ";
	}


	/**
	 * Filter: date of referral before date
	 */
	protected function _filter_referral_to($value = '', $operator = '')
	{
		return " $operator `j_date_of_referral` <= " . $this->db->escape($value) . " ";
	}


	/**
	 * Filter: date of closure after date
	 */
	protected function _filter_closure_from($value = '', $operator = '')
	{
		return " $operator `j_closed_date` >= " . $this->db->escape($value) . " ";
	}


	/**
	 * Filter: date of closure before date
	 */
	protected function _filter_closure_to($value = '', $operator = '')
	{
		return " $operator `j_closed_date` <= " . $this->db->escape($value) . " ";
	}


	/**
	 * Filter: date of event from date
	 */
	protected function _filter_event_from($value = '', $operator = '')
	{
		return " $operator `je_datetime` >= " . $this->db->escape($value) . " ";
	}


	/**
	 * Filter: date of event to date
	 */
	protected function _filter_event_to($value = '', $operator = '')
	{
		return " $operator `je_datetime` <= " . $this->db->escape($value) . " ";
	}


}

/* End of file: ./application/core/MY_Model.php */