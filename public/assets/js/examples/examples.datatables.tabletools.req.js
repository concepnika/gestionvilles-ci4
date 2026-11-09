/*
Name: 			Tables / Advanced - Examples
Written by: 	Okler Themes - (http://www.okler.net)
Theme Version: 	4.1.0
*/

(function($) {

	'use strict';

	var datatableInit = function() {
		var $table = $('#datatable-tabletools');

		var table = $table.dataTable({
			buttons: [
				{
					extend: 'print',
					text: 'Imprimer',
					exportOptions: {
						columns: [0, 1, 3, 4, 5]
					},
				},
				{
					extend: 'excelHtml5',
					text: 'Excel',
					exportOptions: {
						columns: [0, 1, 3, 4, 5]
					},
				},
				{
					extend: 'pdfHtml5',
					title: 'Discussion',
					orientation: 'landscape',
					pageSize: 'LETTER',
					text: 'PDF',
					exportOptions: {
						columns: [0, 1, 3, 4, 5],
					},
					customize: function (doc) {
						doc.styles.tableBodyEven.alignment = 'center';
						doc.styles.tableBodyOdd.alignment = 'center';
						doc.content[1].table.widths = [100, 100, 150, 150, '*'];
					}, 
				}
			],
			"language": {
				"sProcessing": "Traitement en cours ...",
				"sLengthMenu": "Afficher _MENU_ lignes",
				"sZeroRecords": "Aucun résultat trouvé",
				"sEmptyTable": "Aucune donnée disponible",
				"sInfo": "Lignes _START_ de _END_ sur _TOTAL_",
				"sInfoEmpty": "Aucune ligne affich&Eacute;e",
				"sInfoFiltered": "(Filtrer un maximum de_MAX_)",
				"sInfoPostFix": "",
				"sSearch": "Chercher:",
				"sSearchPlaceholder": "Chercher:",
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

		$table.DataTable().buttons().container().prependTo( '#datatable-tabletools_wrapper .dt-buttons' );

		$('#datatable-tabletools_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-outline-secondary');
	};

	$(function() {
		datatableInit();
	});

}).apply(this, [jQuery]);
