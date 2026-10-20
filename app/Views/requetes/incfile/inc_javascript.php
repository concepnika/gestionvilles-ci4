<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script src="/assets/pronto/vendor/jquery/jquery.js"></script>
<script src="/assets/pronto/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
<script src="/assets/pronto/vendor/popper/umd/popper.min.js"></script>
<script src="/assets/pronto/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/pronto/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="/assets/pronto/vendor/common/common.js"></script>
<script src="/assets/pronto/vendor/nanoscroller/nanoscroller.js"></script>
<script src="/assets/pronto/vendor/magnific-popup/jquery.magnific-popup.js"></script>
<script src="/assets/pronto/vendor/jquery-placeholder/jquery.placeholder.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.smartmenus/1.1.0/jquery.smartmenus.min.js" integrity="sha256-L+Zo9Q4bGfdY06BqwMYLDoacazH6GrQxkLavPdT0a44=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.smartmenus/1.1.0/addons/bootstrap/jquery.smartmenus.bootstrap.min.js" integrity="sha256-yQjrAGXVhM4+j4+qFh1W4wg7dPC3IjJ9BId2RO3peEY=" crossorigin="anonymous"></script>
<!-- Specific Page Vendor -->
<script src="/assets/pronto/js/examples/examples.header.menu.js"></script>
<script src="/assets/pronto/js/examples/examples.ecommerce.form.js"></script>
<script src="/assets/pronto/vendor/dropzone/dropzone.js"></script>
<script src="/assets/pronto/vendor/pnotify/pnotify.custom.js"></script>
<script src="/assets/pronto/vendor/summernote/summernote-bs4.js"></script>
<script src="/assets/pronto/vendor/jquery-maskedinput/jquery.maskedinput.js"></script>
<script src="/assets/vendor/pnotify/pnotify.custom.js"></script>
<script src="/assets/pronto/vendor/dropzone/dropzone.js"></script>
<script src="/assets/pronto/vendor/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
<!-- Theme Base, Components and Settings -->
<script src="/assets/pronto/js/theme.js"></script>

<!-- Theme Custom -->
<script src="/assets/pronto/js/custom.js"></script>

<!-- Theme Initialization Files -->
<script src="/assets/pronto/js/theme.init.js"></script>

<script src="/assets/js/nika.js"></script>
<script src="/assets/js/examples.notifications.js"></script>
<script src="/assets/js/examples/examples.modals.js"></script>
<script>
    setTimeout("checkSession()", 7600 * 2);
    //var lastActiveId = "0";
    //$(document).ready(function() {
    //  $('#suivi').summernote();
    //});
    //------------------------------------------------------------

  $("input[data-bootstrap-switch]").each(function(){
		$(this).bootstrapSwitch('state', $(this).prop('checked'));
	})

	
</script>




<script>

	
	$(function () {
	$('[data-toggle="tooltip"]').tooltip()
	})
	
	$(document).ready(function () {
	$('[data-toggle="popover"]').popover();
	});

	$(document).ready(function() {
		var cur_stus;
		$('#accordion dd').hide();
		$('#accordion dt').attr('stus', '');

		$('#accordion dt').click(function(){
			cur_stus = $(this).attr('stus');
			if(cur_stus != "active")
			{
				//reset everthing - content and attribute
				$('#accordion dd').slideUp();
				$('#accordion dt').attr('stus', '');
				$('#accordion dt').removeClass('active');

				//then open the clicked data
				$(this).next().slideDown();
				$(this).attr('stus', 'active');
				$(this).addClass('active');
			}
			//Remove else part if do not want to close the current opened data
			else
			{
				$(this).next().slideUp();
				$(this).attr('stus', '');
				$(this).removeClass('active');
			}
			return false;
		});
	});
	
	
	$(window).scroll(function () {
	if ($(this).scrollTop() >= 1500) {
	  $('#return-to-top').fadeIn(200);    // Fade in the arrow
	} else {
	  $('#return-to-top').fadeOut(200);   // Else fade out the arrow
	}
	});
	$('#return-to-top').click(function () {      // When arrow is clicked
	$('body,html').animate({
	  scrollTop: 0                       // Scroll to top of body
	}, 500);
	});



	$(document).ready(function() {
		/*
		 *  Simple image gallery. Uses default settings
		 */

		$('.fancybox').fancybox();
		/*
		 *  Different effects
		 */

		// Change title type, overlay closing speed
		$(".fancybox-effects-a").fancybox({
			helpers: {
				title : {
					type : 'outside'
				},
				overlay : {
					locked : false
				}
			}
		});
	});



</script>



