
<script type="text/javascript">

  //----------------------------------------------------------
  function ShowHide(id) {
    if ($('#' + id).css('display') !== 'none')
      $('#' + id).hide();
    else
      $('#' + id).show();
  }
  //----------------------------------------------------------
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