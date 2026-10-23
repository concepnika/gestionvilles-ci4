<?php
 
?>
					<header class="page-header">
						<h2>Validation pour SANIVAN</h2>
					</header>
              <div class="card shadow">
                  <!-- Date range -->
                  <div class="form-group">


                    <div class="input-group">
                      <div class="input-group-addon">
                        <label>Date</label>
                        <input type="date" class="form-control pull-right" title="Cliquer pour afficher le calendrier" value="<?php echo ($ActiveDate ?? ''); ?>" id="dteCalcul" />
                      </div>
                      <div class="input-group-addon">
                        <button type="button" class="btn btn-primary pull-right" onclick="CalculJour()" style="margin: 6px;">
                          <i class="fa fa-refresh"></i>Calculer
                        </button>
                      </div>
                    </div>
                    <!-- /.input group -->
                  </div>

                  <!-- /.form group -->
                </div>
	<div class="row">

    <div class="col-md-12">
        <div class="panel-body">

              
          <div class="box box-primary">

                    <?php echo ($validsanivan ?? '') ; ?>

</div>
        </div>
    </div>
	</div>

<script type="text/javascript">
	$(function () {
    $('#tbljourvalid').DataTable({
			'dom': 'Bfrtip',
			'paging': true,
			'lengthChange': true,
			'pageLength': 25,
			'searching': true,
			'ordering': true,
			'info': true,
			'autoWidth': false,
			'rowReorder': true,
			'data-page-length': '25',
			'aaSorting': [[1, "asc"]],
			"language": {
				"sProcessing": "Traitement en cours...",
				"sSearch": "Rechercher&nbsp;:",
				"sLengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
				"sInfo": "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
				"sInfoEmpty": "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
				"sInfoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
				"sInfoPostFix": "",
				"sLoadingRecords": "Chargement en cours...",
				"sZeroRecords": "Aucun &eacute;l&eacute;ment &agrave; afficher",
				"sEmptyTable": "Aucune donn&eacute;e disponible dans le tableau",
				"oPaginate": {
					"sFirst": "Premier",
					"sPrevious": "Pr&eacute;c&eacute;dent",
					"sNext": "Suivant",
					"sLast": "Dernier"
				}
			}
		})
	})

</script>
