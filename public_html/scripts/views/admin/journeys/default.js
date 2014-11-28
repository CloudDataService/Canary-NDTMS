// JavaScript Document
$(document).ready(function () {

	// if ndtms valid button is clicked
    $('#ndtms_valid_btn').click(function () {

		// get href of button
        var url = this.href;

        // get ndtms valid div
        var ndtms_valid_div = $("#ndtms_valid_div");

        // if it doesn't exist
        if (ndtms_valid_div.length == 0) {

            // append it to the DOM and return element
            ndtms_valid_div = $('<div id="ndtms_valid_div" style="display:hidden;" title="This journey is not NDTMS valid"></div>').appendTo('body');
        }

        // load remote content
        ndtms_valid_div.load(
            url,
            {},
            function(responseText, textStatus, XMLHttpRequest) {
                // open dialog
                ndtms_valid_div.dialog({width: 650,
                    resizable: false,
                    modal: true,
                    zIndex: 1
                });
            }
        );

        // prevent default action of link
        return false;
    });


    // if appointmnet button is clicked
    $('.appointment_btn').click(function () {

        // get href of button
        var url = this.href;

        // get div
        var appointment_div = $("#appointment_div");

        // if it doesn't exist
        if (appointment_div.length == 0) {

            // append it to the DOM and return element
            appointment_div = $('<div id="appointment_div" style="display:none;" title="Set appointment"></div>').appendTo('body');
        }

        // load remote content
        appointment_div.load(
            url,
            {},
            function(responseText, textStatus, XMLHttpRequest) {

                // open dialog
                appointment_div.dialog({width: 650,
                    resizable: false,
                    modal: true,
                    zIndex: 1
                });

                // bind validation rules
                $('#appointment_form').validate({
                    rules: {
                        ja_date_offered: {
                            british_date: true
                        },
                        ja_date: {
                            british_date: true,
                            required: true
                        },
                        ja_rc_id: {
                            required: true
                        },
                        ja_length: {
                            digits: true
                        }
                    },
                    submitHandler: function (form) {

                        var $url = $(form).attr('action');
                        var $data = $(form).serialize();

                        $.post($url, $data,
                            function(data) {
                                appointment_div.dialog('close').dialog('destroy');

                                $('div.breadcrumbs').action(data);
                            }
                        );

                        return false;
                    }
                });

                function ja_attended($this)
                {
                    if($this.val() == '1')
                    {
                        $('#ja_dr_tr').fadeOut(400, function () {
                            $('#ja_length_tr').fadeIn();
                        });
                    }
                    else if($this.val() == '2')
                    {
                        $('#ja_length_tr').fadeOut(400, function () {
                            $('#ja_dr_tr').fadeIn();
                        });
                    }
                    else
                    {
                        $('#ja_length_tr').fadeOut();
                        $('#ja_dr_tr').fadeOut();
                    }
                }

                ja_attended($('#ja_attended'));

                $('#ja_attended').change(function () {
                    ja_attended($(this));
                });
            }
        );

        // prevent default action of link
        return false;
    });


    // if appointmnet button is clicked
    $('.event_btn').click(function () {

        // get href of button
        var url = this.href;

        // get div
        var event_div = $("#event_div");

        // if it doesn't exist
        if (event_div.length == 0) {

            // append it to the DOM and return element
            event_div = $('<div id="event_div" style="display:hidden;" title="Set event"></div>').appendTo('body');
        }

        // load remote content
        event_div.load(
            url,
            {},
            function(responseText, textStatus, XMLHttpRequest) {

				// Chain the dropdown boxes
				$("#je_et_id").chained("#et_ec_id");
				$("tr[data-cat]").hide();

				$("#et_ec_id").on("change", function() {
					var id = $(this).find("option:selected").val();
					$("tr[data-cat]").hide();
					$("tr[data-cat='" + id + "']").show();
				}).trigger("change");

				function ja_attended($this)
				{
					if($this.val() == '1')
					{
						$('#ja_dr_tr').fadeOut(400, function () {
							$('#ja_length_tr').fadeIn();
						});
					}
					else if($this.val() == '2')
					{
						$('#ja_length_tr').fadeOut(400, function () {
							$('#ja_dr_tr').fadeIn();
						});
					}
					else
					{
						$('#ja_length_tr').fadeOut();
						$('#ja_dr_tr').fadeOut();
					}
				}

				ja_attended($('#ja_attended'));

				$('#ja_attended').change(function () {
					ja_attended($(this));
				});


                // open dialog
                event_div.dialog({width: 650,
                    resizable: false,
                    modal: true,
                    zIndex: 1
                });

                // bind validation rules
                $('#event_form').validate({
                    rules: {
                        je_date: {
                            british_date: true,
                            required: true,
                            valid_past_date: true
                        },
                        je_et_id: {
                            required: true
                        }
                    },
                    submitHandler: function (form) {

                        var $url = $(form).attr('action');
                        var $data = $(form).serialize();

                        $.post($url, $data,
                            function(data) {
                                event_div.dialog('close').dialog('destroy');

                                $('div.breadcrumbs').action(data);
                            }
                        );

                        return false;
                    }
                });
            });

        // prevent default action of link
        return false;
    });


    // if appointmnet button is clicked
    $('.note_btn').click(function () {

        // get href of button
        var url = this.href;

        // get div
        var note_div = $("#note_div");

        // if it doesn't exist
        if (note_div.length == 0) {

            // append it to the DOM and return element
            note_div = $('<div id="note_div" style="display:hidden;" title="Set note"></div>').appendTo('body');
        }

        // load remote content
        note_div.load(
            url,
            {},
            function(responseText, textStatus, XMLHttpRequest) {

                // open dialog
                note_div.dialog({width: 650,
                    resizable: false,
                    modal: true,
                    zIndex: 1
                });

                // bind validation rules
                $('#notes_form').validate({
                    rules: {
                        jn_date: {
                            british_date: true,
                            required: true
                        },
                        jn_rc_id: {
                            required: true
                        },
                        jn_notes: {
                            required: true
                        }
                    },
                    submitHandler: function (form) {

                        var $url = $(form).attr('action');
                        var $data = $(form).serialize();

                        $.post($url, $data,
                            function(data) {
                                note_div.dialog('close').dialog('destroy');

                                $('div.breadcrumbs').action(data);
                            }
                        );

                        return false;
                    }
                });
            });

        // prnote default action of link
        return false;
    });


    // if appointmnet button is clicked
    $('.modality_btn').click(function () {

        // get href of button
        var url = this.href;

        // get div
        var mod_div = $("#modality_div");

        // if it doesn't exist
        if (mod_div.length === 0) {
            // append it to the DOM and return element
            mod_div = $('<div id="modality_div" style="display:hidden;" title="Set modality"></div>').appendTo('body');
        }

        // load remote content
        mod_div.load(
            url,
            {},
            function(responseText, textStatus, XMLHttpRequest) {

                // open dialog
                mod_div.dialog({
                    width: 650,
                    resizable: false,
                    modal: true,
                    zIndex: 1
                });

                // bind validation rules
                $('#modality_form').validate({
                    rules: {
                        mod_cpdate: {
                            british_date: true,
                            required: true
                        },
                        mod_treatment: {
                            required: true
                        },
                        mod_refdate: {
                            british_date: true,
                            required: true
                        },
                        mod_firstapptdate: {
                            british_date: true,
                            required: true
                        },
                        mod_intsetting: {
                            required: true
                        },
                        mod_start: {
                            british_date: true,
                            required: false
                        },
                        mod_end: {
                            british_date: true,
                            required: false
                        },
                        mod_exit: {
                            required: false
                        }
                    },
                    submitHandler: function (form) {
                        var $url = $(form).attr('action');
                        var $data = $(form).serialize();

                        $.post($url, $data,
                            function(data) {
                                mod_div.dialog('close').dialog('destroy');

                                $('div.breadcrumbs').action(data);
                            }
                        );

                        return false;
                    }
                });
            });

        // prnote default action of link
        return false;
    });


    // if family button is clicked
    $('.family_btn').click(function () {

        // get href of button
        var url = this.href;
        // get div
        var family_div = $("#family_div");

        // if it doesn't exist
        if (family_div.length === 0) {
            // append it to the DOM and return element
            family_div = $('<div id="family_div" style="display:hidden;" title="Set family"></div>').appendTo('body');
        }

        // load remote content
        family_div.load(
            url,
            {},
            function(responseText, textStatus, XMLHttpRequest) {

                // open dialog
                family_div.dialog({
                    width: 1000,
                    resizable: false,
                    modal: true,
                    zIndex: 1
                });


				$('#family_member_form').validate({
					rules: {
						f_fname: {
							required: true
						},
						f_sname: {
							required: true
						}
					},
					submitHandler: function (form) {

						$('#loading').show();

						// make ajax request
						$.ajax({
							url: "/admin/family/add_family_member",
							global: false,
							type: 'POST',
							data: ({ci_csrf_token : $('input[name=ci_csrf_token]').val(),
									j_id : $('#j_id').val(),
									f_fname : $('#f_fname').val(),
									f_sname : $('#f_sname').val(),
									f_date_of_birth : $('#f_date_of_birth').val(),
									f_rel_type: $("#f_rel_type").val()
							}),
							dataType: "html",
							success: function(html) {

								// remove no results row
								$('tr#no_family_members').remove();

								$('#family_members').append(html);

								// clear the form
								$('#add_family_member').parent('td').siblings('td').children('input').val('');

								// rebind hover event to tr
								$('tr.row').hover(
									function () {
										$(this).addClass('hover');
									},
									function () {
										$(this).removeClass('hover');
									}
								);

								$('#loading').hide();

								$("input[name='f_fname']").focus();
							}
						});

						return false;
					}
				});

				// bind validation rules
				$('#notes_form').validate({
					rules: {
						/*
						mod_cpdate: {
							british_date: true,
							required: true
						},
						mod_treatment: {
							required: true
						}
						*/
					},
					submitHandler: function (form) {
						var $url = $(form).attr('action');
						var $data = $(form).serialize();

						$.post($url, $data,
							function(data) {
								family_div.dialog('close').dialog('destroy');
								$('div.breadcrumbs').action(data);
							}
						);

						return false;
					}
				});
            });

        // prnote default action of link
        return false;
    });


    // if appointmnet button is clicked
    $('.approve_btn').click(function () {

        // get href of button
        var url = this.href;

        // get div
        var approve_div = $("#approve_div");

        // if it doesn't exist
        if (approve_div.length == 0) {

            // append it to the DOM and return element
            approve_div = $('<div id="approve_div" style="display:hidden;" title="Submit for approval"></div>').appendTo('body');
        }

        // load remote content
        approve_div.load(
            url,
            {},
            function(responseText, textStatus, XMLHttpRequest) {

                // open dialog
                approve_div.dialog({width: 650,
                    resizable: false,
                    modal: true,
                    zIndex: 1
                });

                // bind validation rules
                $('#approve_form').validate({
                    rules: {
                    /*
                        jn_date: {
                            british_date: true,
                            required: true
                        },
                        jn_rc_id: {
                            required: true
                        },
                        jn_notes: {
                            required: true
                        }
                    */
                    },
                    submitHandler: function (form) {

                        var $url = $(form).attr('action');
                        var $data = $(form).serialize();

                        $.post($url, $data,
                            function(data) {
                                approve_div.dialog('close').dialog('destroy');

                                $('div.breadcrumbs').action(data);
                            }
                        );

                        return false;
                    }
                });
            });

        // prnote default action of link
        return false;
    });


    // if status button is clicked
    $('.status_btn').click(function () {

        // get href of button
        var url = this.href;

        // get div
        var status_div = $("#status_div");

        // if it doesn't exist
        if (status_div.length == 0) {

            // append it to the DOM and return element
            status_div = $('<div id="status_div" style="display:hidden;" title="Approve status change"></div>').appendTo('body');
        }

        // load remote content
        status_div.load(
            url,
            {},
            function(responseText, textStatus, XMLHttpRequest) {

                // open dialog
                status_div.dialog({width: 650,
                    resizable: false,
                    modal: true,
                    zIndex: 1
                });

                // bind validation rules
                $('#status_form').validate({
                    rules: {
                    /*
                        jn_date: {
                            british_date: true,
                            required: true
                        },
                        jn_rc_id: {
                            required: true
                        },
                        jn_notes: {
                            required: true
                        }
                    */
                    },
                    submitHandler: function (form) {

                        var $url = $(form).attr('action');
                        var $data = $(form).serialize();

                        $.post($url, $data,
                            function(data) {
                                status_div.dialog('close').dialog('destroy');
                                location.reload();
                                $('div.breadcrumbs').action(data);
                            }
                        );

                        return false;
                    }
                });
            });

        // prnote default action of link
        return false;
    });

	//If the postcode changes, search and update the ONS code.
	$('body').on('change', '#ci_post_code', function() {
		//prep for the local authority to change
		$('#ci_authority_name').val('');
		$('#ci_authority_code').val('');
		$('#ci_authority_label').html( 'Searching...' );

		//console.log('postcode changed');
		var postcode = $('#ci_post_code').val();

		$.getJSON('/admin/ajax/locauthority/null/'+ postcode +'/false', function(data) {

			//console.log('update ons');
			if(data.ons_code == undefined)
			{
				//console.log('no ONS found');
				$('#ci_authority_name').val('');
				$('#ci_authority_code').val('');
				$('#ci_authority_label').html( 'Not found' );
			}
			else
			{
				//console.log(data.ons_code);
				$('#ci_authority_name').val(data.ons_name);
				$('#ci_authority_code').val(data.ons_code);
				$('#ci_authority_label').html( data.ons_name + ' (' +  data.ons_code + ')' );
			}
		});

	});

});
