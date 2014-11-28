<?php

function year_start()
{
	$month = (int) date('m');
	$year = ($month >= 4 ? date('Y') : (int) date('Y') - 1);
	return '01/04/' . $year;
}




function year_end()
{
	$month = (int) date('m');
	$year = ($month >= 4 ? date('Y') + 1 : (int) date('Y'));
	return '31/03/' . $year;
}