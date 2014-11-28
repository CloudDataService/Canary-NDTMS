// JavaScript Document
$(document).ready(function () {

	$('div.header_toggle').hover(function () {
		$(this).css({'cursor' : 'pointer'});
	});

	$('div.header_toggle').click(function () {

		var $this = $(this);

		if($this.children('span').hasClass('expand'))
		{
			$this.children('span').removeClass('expand').addClass('collapse').text('Click to collapse');

			var scrollHeight = $(this).next('div.toggle').fadeIn().attr("scrollHeight");

			window.scroll(0, $(this).next('div.toggle').offset().top);
		}
		else
		{
			$this.next('div.toggle').fadeOut(400, function () {
				$this.children('span').removeClass('collapse').addClass('expand').text('Click to expand');
			});
		}
	});

	//mailmerge pdf generator
	$("input#pdf_btn").live("click", function(event){
		//alert('make pdf');
		event.preventDefault();
		// Get required DOM elements
		var button = $("input#pdf_btn");
		var link_div = $("div#pdf_links");
		var mmtype = $("select#mm_id").val();
		var journeyid = $("input#j_id").val();
		var loading = $("img#loading");
		// Hide the button and show the loading image
		button.hide();
		loading.show();
		// Make AJAX request to generate the PDF
		$.ajax({
			type: "post",
			dataType: "json",
			data: $("form#filter_form").serialize(),
			url: "/admin/mail-merge/make_pdf/" + mmtype + "?j_id="+journeyid,
			success: function(res) {
				if (res.status == "ok") {
					// Add a download link
					var link = $("<a>")
						.attr("href", res.file_url)
						.attr("target", "_blank")
						.attr("mm_type", mmtype)
						.text(" "+res.file_title+" PDF ");
					// Show the link
					link_div.append(link).show();
					link_div.append('<br />');
					//don't let them generate that mail merge again?
					$("select#mm_id option[value='"+mmtype+"']").remove();
					//put the button back, so other letter types can be made
					loading.hide();
					button.show();
					// Update the CSRF element so more AJAX requests can be made
					$("input[name=ci_csrf_token]").val(get_cookie("ci_csrf_token"));
				} else if (res.status == "err") {
					// Error in JSON response? Show on page.
					button.hide();
					var errmsg = $("<span>").css("color","darkred").text(res.msg);
					link_div.empty();
					errmsg.appendTo(link_div);
					link_div.show();
				}
			},
			error: function(xhr) {
				// Not a HTTP 200 response. Something went wrong but we don't know what.
				button.hide();
				var errmsg = $("<span>")
					.css("color","darkred")
					.html("Error " + xhr.status + ".<br> PDF not available at this time.");
				link_div.empty();
				errmsg.appendTo(link_div);
				link_div.show();
			}
		});
	});


});