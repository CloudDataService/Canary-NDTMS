// JavaScript Document
var milliseconds = 900000;

var count_down_timeout;

var timeout;

$.datepicker.setDefaults({
    dateFormat: "dd/mm/yy",
    closeText: "Done",
    prevText: "Prev",
    nextText: "Next",
    currentText: "Today",
    monthNames: ["January","February","March","April","May","June","July","August","September","October","November","December"],
    monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
    dayNamesMin: ["Su","Mo","Tu","We","Th","Fr","Sa"],
    weekHeader: "Wk",
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: "",
    changeMonth: true,
    changeYear: true,
    yearRange: "c-75:c",
    showButtonPanel: true
});

$(function() {
	if (typeof(jQuery.validator) !== 'undefined') {
		jQuery.validator.addMethod("valid_past_date", function(value, element) {
			var parts = value.match(/(\d+)/g);

			if (value.length === 0) {
				return false;
			}

			var date_current = new Date(parts[2], parts[1]-1, parts[0]);
			var date_valid = new Date();

			return (date_current <= date_valid);
		}, "The date is invalid as its in the future");

		jQuery.validator.addMethod("valid_future_date", function(value, element) {
			var parts = value.match(/(\d+)/g);

			if (value.length === 0) {
				return false;
			}

			var date_12_week = new Date(parts[2], parts[1]-1, parts[0]);
			var date_valid = new Date();

			return (date_current >= date_valid);
		}, "The date is invalid as its in the past");
	}
});


// Multi-add to list module
var multi_add = (function($) {

    var $select = null;
    var $add = null;
    var $list = null;


    var init = function(settings) {
        $select = $(settings.select);
        $add = $(settings.add);
        $list = $(settings.list);
        $multi_add_hidden_field = settings.hidden_field;

        $add.on("click", function(e) {
            e.preventDefault();
            add_item();
        });

        $list.on("click", "img.action-remove", function(e) {
            var $li = $(this).parent("li").remove();
        });

    }


    var add_item = function() {
        // Get selected item value
        var id = $select.val();
        var text = $select.find("option:selected").text();

        // The select item?
        if(id == '') {
            return false;
        }

        // Already in the list?
        var $existing = $list.find("li["+ $multi_add_hidden_field +"-data-id='" + id + "']");

        if ($existing.length > 0) {
            alert(text + " has already been added.");
            return false;
        }

        var $input = $('<input>').attr("type", "hidden").attr("name", ($multi_add_hidden_field + '[]')).val(id);
        var $li = $('<li '+ $multi_add_hidden_field +'-data-id="' + id + '"><img src="/img/style/x14.png" title="Remove" class="action-remove"> <span>' + text + '</span></li>');

        $input.appendTo($li);
        $li.appendTo($list);
    };


    return {
        init: init
    };

})(jQuery);


// Multi-add to list module, that works for agencies because they have double fields
var agency_add = (function($) {

    var $select = null;
    var $add = null;
    var $list = null;

    var init = function(settings) {
        $select = $(settings.select);
        $valfield = $(settings.valfield);
        $add = $(settings.add);
        $list = $(settings.list);
        $hidden_field = settings.hidden_field;

        $add.on("click", function(e) {
            e.preventDefault();
            add_item();
        });

        $list.on("click", "img.action-remove", function(e) {
            var $li = $(this).parent("li").remove();
        });

    };


    var add_item = function() {
        // Get selected item value
        var id = $select.val();
        var thedate = $valfield.val();
        var text = $select.find("option:selected").text();

        // The select item?
        if(id == '') {
            return false;
        }

        // Already in the list?
        var $existing = $list.find("li["+ $hidden_field +"-data-id='" + id + "']");

        if ($existing.length > 0) {
            alert(text + " has already been added.");
            return false;
        }

        var $input = $('<input>').attr("type", "hidden").attr("name", ($hidden_field + '['+ id +']')).val(thedate);
        var $li = $('<li '+ $hidden_field +'-data-id="' + id + '"><img src="/img/style/x14.png" title="Remove" class="action-remove"> <span>'
                        + text + ', added to recovery on '+ thedate
                        + '</span></li>');

        $input.appendTo($li);
        $li.appendTo($list);
    };


    return {
        init: init
    };

})(jQuery);


$(document).ready(function() {

    $('li.has_more').hover(
        function () {
            $('ul.sub_nav').hide();
            clearTimeout(timeout);
            $(this).children('ul.sub_nav').css({'display':'block'});
        },
        function () {
            var $this = $(this);
            timeout = setTimeout(function () { $this.children('ul.sub_nav').css({'display':'none'}); }, 500);
        }
    );

    if($('div.action').length) {
        $('div.action').fadeIn();
        setTimeout(function() { $('div.action').fadeOut('slow'); }, 2500);
    }

    $('a.action').click(function () {
        var element_clicked = $(this);
        var href = element_clicked.attr('href');
        var message = '<div id="action" title="Please confirm">' + element_clicked.attr('title') + '</div>'

        $('body').append(message);

        $( "#action" ).dialog({
            resizable: false,
            modal: true,
            buttons: {
                "Yes": function() {
                    $(this).dialog( "close" );

                    $('div#action').remove();

                    if(element_clicked.is("input"))
                    {
                        element_clicked.parent('form').submit();
                    }
                    else
                    {
                        document.location = href;
                    }
                },
                "No": function() {
                    $(this).dialog( "close" );

                    $('div#action').remove();

                    return false;
                }
            },
            close: function () {
                $('div#action').remove();
            }
        });

        return false;
    });

    $('tr.row').hover(
        function () {
            $(this).addClass('hover');
        },
        function () {
            $(this).removeClass('hover');
        }
    );

    $('tr.row').click(function () {
        if( ! $(this).hasClass('no_click'))
        {
            if($(this).children('td').children('a').attr('target') == "_blank")
            {
                var w = window.open($(this).children('td').children('a').attr('href'), '__blank', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=970,height=800');
                w.focus();
                return false;
            }
            else
            {
                window.location = $(this).children('td').children('a').attr('href');
            }
        }
    });

    $('a.window').click(function () {
        var w = window.open( $(this).attr('href'), '__blank', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=970,height=800');
        w.focus();
        return false;
    });

    $('a.back').click(function () {
        history.go(-1);
        return false;
    });


    // Paginate tables
    $('table.paginated').each(function() {
        var currentPage = 0;
        var $table = $(this);
        var numPerPage = $table.data("items") || 5;
        $table.bind('repaginate', function() {
            $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
            $(".event_notes").readmore({
                maxHeight: 51,
                moreLink: '<a href="#" class="readmore">[Show more]</a>',
                lessLink: '<a href="#" class="readmore">[Show less]</a>',
            });
        });
        $table.trigger('repaginate');
        var numRows = $table.find('tbody tr').length;
        var numPages = Math.ceil(numRows / numPerPage);
        var $pager = $('<div class="pagination"></div>').css({ "margin": "0 10px 10px 0" });
        for (var page = 0; page < numPages; page++) {
            $('<div class="digit"></div>').css("cursor", "pointer").html(page + 1).bind('click', {
                newPage: page
            }, function(event) {
                currentPage = event.data['newPage'];
                $table.trigger('repaginate');
                $(this).addClass('current').siblings().removeClass('current');
            }).appendTo($pager).addClass('clickable');
        }
        $pager.insertBefore($table).find('div.digit:first').addClass('current');
    });


});

// create action plugin
jQuery.fn.action = function (message) {
    $('div.action').remove();

    var html = '<div class="action"><p>' + message + '</p></div>';

    jQuery(this).prepend(html);

    window.scroll(0, $('body').offset().top);

    if($('div.action').length) {
        $('div.action').fadeIn();
        setTimeout(function() { $('div.action').fadeOut('slow'); }, 2500);
    }
};
