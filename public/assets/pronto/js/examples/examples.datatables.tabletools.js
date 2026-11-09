/*
Name: 			Tables / Advanced - Examples
Written by: 	Okler Themes - (http://www.okler.net)
Theme Version: 	4.1.0
*/

(function ($) {

	'use strict';

	var datatableInit = function () {
		var $table = $('#datatable-tabletools');

		var table = $table.dataTable({
			sDom: '<"text-right mb-md"T><"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p',
			buttons: [
				{
					extend: 'print',
					text: 'Print',
					exportOptions: {
						columns: [0, 1, 2, 3, 4, 5, 6, 7]
					},
				},
				{
					extend: 'excelHtml5',
					text: 'Excel',
					exportOptions: {
						columns: [0, 1, 2, 3, 4, 5, 6, 7]
					},
				},
				{
					extend: 'pdfHtml5',
					title: 'Requete: ',
					orientation: 'landscape',
					pageSize: 'LEGAL',
					text: 'PDF',
					customize: function (doc) {
						var rowCount = doc.content[1].table.body.length;
						for (i = 0; i < rowCount + 1; i++) {
							doc.content[1].table.body[i][1].alignment = 'center';
						};
					},
					exportOptions: {
						columns: [0, 1, 2, 3, 4, 5, 6, 7]
					},
					customize: function (doc) {
						var colCount = new Array();
						$('#datatable-tabletools').find('tbody tr:first-child td').each(function () {
							if ($(this).attr('colspan')) {
								for (var i = 1; i <= $(this).attr('colspan'); $i++) {
									colCount.push('*');
								}
							} else { colCount.push('*'); }
						});

						doc.content[1].table.widths = colCount;

					}
				}
			],
			"language": {
				"sProcessing": "Traitement en cours ...",
				"sLengthMenu": "Afficher _MENU_ lignes",
				"sZeroRecords": "Aucun résultat trouvé",
				"sEmptyTable": "Aucune donnée disponible",
				"sInfo": "Lignes _START_ à _END_ sur _TOTAL_",
				"sInfoEmpty": "Aucune ligne affich&Eacute;e",
				"sInfoFiltered": "(Filtrer un maximum de_MAX_)",
				"sInfoPostFix": "",
				"sSearch": "Chercher:",
				"sUrl": "",
				"sInfoThousands": ",",
				"sLoadingRecords": "Chargement...",
				"oPaginate": {
					"sFirst": "Premier", "sLast": "Dernier", "sNext": "Suivant", "sPrevious": decodeURIComponent("Pr&eacute;c&eacute;dent")
				},
				"oAria": {
					"sSortAscending": ": Trier par ordre croissant", "sSortDescending": ": Trier par ordre décroissant"
				}
			}
		});

		$('<div />').addClass('dt-buttons mb-2 pb-1 text-end').prependTo('#datatable-tabletools_wrapper');

		$table.DataTable().buttons().container().prependTo('#datatable-tabletools_wrapper .dt-buttons');

		$('#datatable-tabletools_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-outline-secondary');
	};

	$(function () {
		datatableInit();
	});

}).apply(this, [jQuery]);
