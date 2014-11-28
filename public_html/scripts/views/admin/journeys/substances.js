// JavaScript Document
$(document).ready(function() {

    $(".datepicker").datepicker();

    $('#alcohol_form').validate({
        rules: {
            jal_avg_daily_units: {
                digits: true,
                range: [0, 200]
            },
            jal_last_28_drinking_days: {
                digits: true,
                range: [0, 28]
            },
            jal_age_started_drinking: {
                digits: true
            }
        }
    });

    $('#drugs_form').validate({
        rules: {
            jd_substance_1_age: {
                digits: true
            },
            jd_hep_c_test_date: {
                british_date: true
            }
        }
    });

    // Enable autosave
    autosave.init("#alcohol_form, #drugs_form");

    //do you inject alcohol?
    $('select#jd_substance_1').change(function() {
        if ($('select#jd_substance_1').val() == '7000') {
            $('select#jd_substance_1_route').val('4');
        }
    });

});
