var evenPost = '';
var ActiveVille = '';
var NewVille = '';
var ActifChauffeur = '';
var ActifCamion = '';
var ActifAide = '';
var ActiveCedule = 0;
var selectedDay = '';
var Deleted = 0;
var optChauffeur = '';
var optCamion = '';
var myTimer;
var mySessionCheck;
var limitcedule = '0';
var typerh = 1;
var typecam = 1;
var dropfrom = 0;
var ActiveBox = '';
//-------------------------
var startVerifReqDel = 0;
var adminTimeout = '';
var SaniTimer = '';
var polylineMapInstance = null;

//----------------------------------------------------------------
// Curseur d'attente pendant les requetes AJAX de la cedule.
// Les appels etaient synchrones (async:false) et gelaient toute la
// page; en async ils restent serialises cote serveur par la session
// PHP (meme ordre d'execution), mais le navigateur reste reactif.
$(document).ajaxSend(function (e, xhr, opt) {
	if (opt.url && opt.url.indexOf('/sanivan/') >= 0) {
		document.body.style.cursor = 'progress';
	}
});
$(document).ajaxComplete(function (e, xhr, opt) {
	if (opt.url && opt.url.indexOf('/sanivan/') >= 0) {
		document.body.style.cursor = 'auto';
	}
});

//----------------------------------------------------------------
function setLanguage(lang) {
	//document.location = "/proticlub/setlangue?langue=" + lang;
	$.post(
		"/villes/setlangue", //url to call
		{
			langue: lang
		}, //POST data to send
		function (data) { //callback to output data received back from controller

			document.location = data;
		}
	);

}
//----------------------------------------------------------------
function _AdminVerif() {
	//alert(1);
	clearTimeout(adminTimeout);
	var urlName = "/admin/verifreqdel";
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (data) {
		if (data !== "") {
			var objData = JSON.parse(data);
			if (objData.havedelcom == 1) {
				$('#spDelNote').css('display', 'block');
				$('#btnDelNote').css('color', 'red');
				$('#btnDelNote').css('backgroundColor', 'white');
			}

			if ($("#divFooterSan").length > 0) {
				$("#divCedule").html(objData.requetes);
			}
		}
		adminTimeout = setTimeout("_AdminVerif()", 300000);
	});
	//
	//

}

//----------------------------------------------------------------
function ModifieUsager(iduser) {
	var urlName = '/co_gestion/updateUser?email=' + $("#email").val() +
		'&nom=' + $("#nom").val() + '&prenom=' + $("#prenom").val() + '&pass=' + $("#pass").val() +
		'&iduser=' + iduser;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		//var obj = $.parseJSON(data);
		if (data === 'OK') {
			alert('Changements enregistrés');
		}
		else {
			alert('Une erreur est survenue.');
		}
	});
}
function DeleteUsager(iduser) {
	if (confirm("Etes-vous certain de vouloir supprimer cet usager")) {
		var urlName = '/admin/remove?iduser=' + iduser;
		$.ajax({
			url: urlName,
			context: document.body
		}).done(function (data) {
			document.location = '/admin/liste_usagers';
		});
	}
}
function ShowUsager(iduser) {
	document.location = '/admin/show?iduser=' + iduser;
}
function ShowHide(id) {
	if ($('#' + id).css('display') !== 'none')
		$('#' + id).hide();
	else
		$('#' + id).show();
}
function ShowHideTop(id) {
	if ($('#' + id).css('display') !== 'none') {
		$('#' + id).hide();
		$('#divShowCed').css('margin-top', '0px');
		$('#divLeftScroll').css('margin-top', '0px');
		$('#divRightScroll').css('margin-top', '0px');
	}
	else {
		$('#' + id).show();
		$('#divShowCed').css('margin-top', '60px');
		$('#divLeftScroll').css('margin-top', '58px');
		$('#divRightScroll').css('margin-top', '58px');
  }
}
function Unlock() {
	var urlName = '/session/debloque';
	var passw = $("#pwd").val();
	$.ajax({
		async: true,
		type: "POST",
		data: {
			pass: passw
		},
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			document.location = msg;
		}
	});
}
function updRow(id, oldrang, value, acol, from, dateactive) {
	var urlName = '/sanivan/row/' + id + '/' + oldrang + '/' + value + "/" + acol + "/" + acol + "/" + from + "/" + dateactive;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#body_" + acol).html(msg);
		}
	});
}
function SetChauffeur(id, dateactive) {

	var arrInfo = id.split('_');
	var urlName = "/sanivan/setemploye/" + arrInfo[1] + "/1/" + $("#" + id).val() + "/" + dateactive;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = '/sanivan/aujourdhui/1/' + $("#dteActivePage1").val() + '?activedrop=' + ActiveDrop +
			'&typecedule=' + activecedule + '&notab=' + notab + '&AideIsShow=' + AideIsShow;
	});
}
function SetAide(id, dateactive) {
	var arrInfo = id.split('_');
	var urlName = "/sanivan/setemploye/" + arrInfo[1] + "/2/" + $("#" + id).val() + "/" + dateactive;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = '/sanivan/aujourdhui/1/' + $("#dteActivePage1").val() + '?activedrop=' + ActiveDrop +
			'&typecedule=' + activecedule + '&notab=' + notab + '&AideIsShow=' + AideIsShow;
	});
}
function SetStartChauf(id, dateactive, from) {
	var arrInfo = id.split('_');
	var urlName = "/sanivan/setstart/" + arrInfo[1] + "/1/" + dateactive + '?from=' + from + '&refreshonly=1' + ' &activedrop =' + ActiveDrop + '&limit=' + limitcedule + '&typerh=' + $("#typechauf").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").html(objData.today);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}

	});
}
function SetStartAide(id, dateactive, from) {
	var arrInfo = id.split('_');
	var urlName = "/sanivan/setstart/" + arrInfo[1] + "/2/" + dateactive + '?from=' + from + '&refreshonly=1' + '&activedrop=' + ActiveDrop + '&limit=' + limitcedule + '&typerh=' + $("#typechauf").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").html(objData.today);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});
}
function SetStartCam(id, dateactive, from) {
	var arrInfo = id.split('_');
	var urlName = "/sanivan/setstart/" + arrInfo[1] + "/3/" + dateactive + '?from=' + from + '&refreshonly=1' + '&activedrop=' + ActiveDrop + '&limit=' + limitcedule + '&typerh=' + $("#typechauf").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").html(objData.today);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		$("#hs_dispo_cam").html(objData.hs_cam);
		$("#non_dispo_cam").html(objData.non_dispo_cam);
		$("#non_dispo_chauf").html(objData.non_dispo_chauf);
		$("#non_dispo_aide").html(objData.non_dispo_aide);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});
}
function dragStart(event) {
	ActiveVille = event.target.id;
	NewVille = ActiveVille;
}
function allowDrop(ev) {
	ev.preventDefault();
}

function drag(ev) {
	ev.dataTransfer.setData("text", ev.target.id);
}
function TransfertTocol(id) {
	var urlName = '';
	var arrData = id.split('_');
	var idville = arrData[2];
	var OldCol = arrData[3];
	var Acol = arrData[1];
	var from = arrData[4];
	urlName = "/sanivan/updville/" + idville + "/" + Acol + "/" + OldCol + "/" + from + "/1/" + ActiveDrop + "/" + $("#dteActivePage1").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#divCedule").html(objData.today);
			$("#spRearEnd").html(objData.spRearEnd);
			$("#spRobot").html(objData.nbRobot);
			$("#spTotalCam").html(objData.nbAllCam);
		}

	});
}
function dropcol(ev, Acol, from) {
	ev.preventDefault();
	var urlName = '';
	var needToReload = '1';
	var arrData = ev.dataTransfer.getData("text").split('_');
	var idville = arrData[1];
	var OldCol = arrData[2];
	if (OldCol == 0) {
		urlName = "/sanivan/chkload/" + Acol + "/" + from + "/" + $("#dteActivePage1").val();
		$.ajax({
			type: "POST",
			url: urlName
		}).done(function (data) {
			needToReload = data;
			urlName = "/sanivan/addville/" + idville + "/" + Acol + "/" + from + "/" + ActiveDrop + "/" + needToReload + "/" + $("#dteActivePage1").val();
			$.ajax({
				type: "POST",
				url: urlName
			}).done(function (msg) {
				if (msg != '') {
					var objData = JSON.parse(msg);
					$("#divCedule").html(objData.today);
					$("#spRearEnd").html(objData.spRearEnd);
					$("#spRobot").html(objData.nbRobot);
					$("#spTotalCam").html(objData.nbAllCam);
				}
			});
		});
	}
	else {
		urlName = "/sanivan/updville/" + idville + "/" + Acol + "/" + OldCol + "/" + from + "/1/" + ActiveDrop + "/" + $("#dteActivePage1").val();
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				var objData = JSON.parse(msg);
				$("#divCedule").html(objData.today);
				$("#spRearEnd").html(objData.spRearEnd);
				$("#spRobot").html(objData.nbRobot);
				$("#spTotalCam").html(objData.nbAllCam);
			}
		});
	}
}
function dropTransBO(ev, transBo, boiteTrBo, typemat) {
	ev.preventDefault();
	var id = ev.dataTransfer.getData("text");
	var arrData = id.split('_');
	var noboite = arrData[1];
	var noType = arrData[2];
	var from = arrData[3];
	if ((from == 2) && (boiteTrBo.trim() == '')) {
		alert(boiteTrBo + '-Vous devez TRANFERER une boite de TRANSBO IN avant de TRANSBORDER.');
		return;
	}
	else if ((from == 1) && (boiteTrBo.trim() != '')) {
		if (confirm('Ce type de REBUT a la boite ' + boiteTrBo + '. \n Voulez-vous la changer pour la boite ' + noboite + '?') == false) {
			return;
		}
    }
	if ((noType.toLowerCase() != typemat.toLowerCase()) && (noType != '99') ) {

		if (confirm('Ce type de REBUT est incompatible. \n Voulez-vous TRANSBORDER ce REBUT pareil?') == false) {
			return;
		}

	}
	var montant = 75;
	if (noboite == 'remorque') {
		montant = prompt("Veillez entrer le montant pour cette Remorque", "75,00");
		if (montant == null) {
			return;
		}
	}
	else {
		montant = prompt("Veillez entrer le montant pour cette Boite", "150,00");
		if (montant == null) {
			return;
		}
    }
	var urlName = "/rolloff/tranfertboitetotrbo/" + noboite + "/" + transBo + "/" + from + '?montant=' + montant;
	document.location = urlName;
	//$.ajax({
	//	type: "POST",
	//	url: urlName
	//}).done(function (data) {
	//	var objData = JSON.parse(data);
	//	$('#tboBoxTransbo').html(objData.boitesistransbo);
	//	$('#transbo_in').html(objData.boitesincours);
	//	$('#tboBoxTransboMat').html(objData.boiteshavemat);
	//});
}
function EditTransBO(idtransbo) {
	//-----------------------
	var urlName = "/rolloff/getboiteintrbo/" + idtransbo;
	//document.location = urlName;
	$.ajax({
		url: urlName
	}).done(function (data) {
		var objData = JSON.parse(data);
		var hauteur = (objData.nbrow * 120);
		$('#infoTrans').val(idtransbo);
		$('#modalBodyEdit').height(hauteur);
		$('#btnModalDelTransBo').prop('disabled', ((objData.nbrow == 0)? false : true ));
		$('#tboEditBoxTransbo').html(objData.boitesintransbo);
		$('#tfoEditBoxTransbo').html(objData.sumboitesintransbo);
		$("#selEditTransbo_in").val('').change();
		$("#selEditTypeTransbo_in").val('').change();
		$('#modalEditTrans').modal('show');
	});

}
function updBoiteToTRB() {
	var value = $('#selUpdTransbo_in').val();
	var urlName = "/rolloff/updboitetotrbo/" + $('#infoTrans').val() + '/' + value;
	document.location = urlName;
}
function chgBoiteToTRB(id) {
	$('#infoTrans').val(id);
	$('#btnTRB_1').hide();
	$('#btnTRB_2').hide();
	$('#modalUpdTransBoMat').modal('show');
}
function setBoiteToTRB(value) {
	$('#infoTrans').val(value);
	$('#modalTransBoMat').modal('show');
}
function sendBoiteToTRB() {
	var value = $('#infoTrans').val();
	var arrVal = value.split('_');
	var urlName = "/rolloff/setboitetotrbo/" + arrVal[0] + '/' + $('#selTransbo_in').val();
	document.location = urlName;
	//$.ajax({
	//	type: "POST",
	//	url: urlName
	//}).done(function (data) {
	//	var objData = JSON.parse(data);
	//	$('#tboBoxTransbo').html(objData.boitesistransbo);
	//	$('#transbo_in').html(objData.boitesincours);
	//	$('#tboBoxTransboMat').html(objData.boiteshavemat);		
	//});
}
function dropville(ev, typeced) {
	ev.preventDefault();
	var arrData = ev.dataTransfer.getData("text").split('_');
	//alert(ev.dataTransfer.getData("text"));
	var idville = arrData[1];
	var OldCol = arrData[2];
	var urlName = "/cedule/addsecteur/" + idville;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {

		$("#titreShowDrop").empty();
		if (limitcedule == '1') {
			$("#titreShowDrop").html('REAR END: ' + msg);
		}
		else {
			$("#titreShowDrop").html('ROBOT: ' + msg);
    }
		$("#edtCeduleDrop").val(typeced);
		$("#edtVilleDrop").val(idville);
		$("#edtOldColDrop").val(OldCol);
		setNewVille(typeced, idville, OldCol);
	});
}
function getModalVille() {
	$('#modalAddVille').modal('show');
}
function VilleCreate() {
	var radioValue = $("input[name='radTypeCam']:checked").val();
	setNewVille(radioValue, $("#selVilleslist").val(), 0)
}
function setNewVille(typeced, idville, OldCol) {
	//-------------------------------------
	if (OldCol == '0') {
		urlName = "/sanivan/addville/" + idville + "/1/" + typeced + "/" + $("#dteActivePage1").val() + '?limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val();

		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				var objData = JSON.parse(msg);
				$("#divCedule").html(objData.today);
			}
		});
	}
	else {
		urlName = "/sanivan/updville/" + idville + "/" + typeced + "/" + OldCol + "/" + typeced + "/" + $("#dteActivePage1").val() + '?limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val();
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				var objData = JSON.parse(msg);
				$("#divCedule").html(objData.today);
				$("#spRearEnd").html(objData.spRearEnd);
				$("#spRobot").html(objData.nbRobot);
				$("#spTotalCam").html(objData.nbAllCam);
				$("#divDropCamion").html(objData.CamionDrop);
				$("#divDropRH").html(objData.ChauffeurDrop);
				if (objData.limitcedule === "1") {
					$("#chkRearLoad").prop('checked', true);
				}
				else if (objData.limitcedule === "2") {
					$("#chkRobot").prop('checked', true);
				}
			}
		});
	}
}
function dropchauffeur(ev, id) {
	ev.preventDefault();
	var urlName = '';
	var idexistant = 0;
	var arrData = ev.dataTransfer.getData("text").split('_');
	var idchauffeur = arrData[1];
	if (arrData.length > 2) {
		idexistant =  arrData[2];
	}
	activeRH = (($("#chkShowConge").is(':checked')) ? 0 : 1);
	urlName = "/sanivan/chauffeur/" + id + "/" + idchauffeur + "/" + $("#dteActivePage1").val() + '/' + idexistant + '?typecedule=' + activecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val() + '&action=' + activeRH;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").html(objData.today);
		$("#divDropCamion").html(objData.CamionDrop);
		$("#divDropRH").html(objData.ChauffeurDrop);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});
}
function dropreturnemp(ev) {
	ev.preventDefault();
	var urlName = '';
	var fieldname = '';
	var arrData = ev.dataTransfer.getData("text").split('_');
	if (arrData[0] == 'tdchauffeur') {
		fieldname = 'idchauffeurs';
	}
	else {
		fieldname = 'HELPERS_1';
  }
	var idchauffeur = arrData[1];
	var id = arrData[2];
	activeRH = (($("#chkShowConge").is(':checked')) ? 0 : 1);
	urlName = "/sanivan/removeempl/" + id + "/" + idchauffeur + "/" + $("#dteActivePage1").val() + '/' + fieldname + '?typecedule=' + activecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val() + '&action=' + activeRH;
	//document.location = urlName;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").html(objData.today);
		$("#divDropCamion").html(objData.CamionDrop);
		$("#divDropRH").html(objData.ChauffeurDrop);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});
}
function dropreturcam(ev) {
	ev.preventDefault();
	var urlName = '';
	var arrData = ev.dataTransfer.getData("text").split('_');
	var id = arrData[2];
	urlName = "/sanivan/removecam/" + id + "/" + $("#dteActivePage1").val() + '?typecedule=' + activecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val() + '&typecam=' + typecam ;
	//document.location = urlName;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").html(objData.today);
		$("#divDropCamion").html(objData.CamionDrop);
		$("#divDropRH").html(objData.ChauffeurDrop);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});
}
function dropligne(ev, id, typecedule) {
	ev.preventDefault();
	var urlName = '';
	var idexistant = 0;
	var arrData = ev.dataTransfer.getData("text").split('_');
	var fielddropped = arrData[0];
	var fieldvalue = arrData[1];
	if (arrData.length > 2) {
		idexistant = arrData[2];
	}
	switch (fielddropped) {
		case 'chauffeur':
			urlName = "/sanivan/chauffeurligne/" + id + "/" + fieldvalue + "/" + $("#dteActivePage1").val() + '/' + idexistant + '?typecedule=' + typecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val();
			break;
		case 'camion':
			urlName = "/sanivan/camionligne/" + id + "/" + idexistant + "/" + fieldvalue + "/" + $("#dteActivePage1").val() + '?typecedule=' + typecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val();
			break;
		case 'aide':
			urlName = "/sanivan/aideligne/" + id + "/" + fieldvalue + "/" + $("#dteActivePage1").val() + "/" + idexistant + '?typecedule=' + typecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val();
			break;
		case 'lblville':
			// Ville deposee sur une autre ville: adopte le superviseur de la
			// cible et prend sa position (meme section) ou assignation seule.
			if ((arrData.length > 3) && (arrData[3] != id)) {
				urlName = "/sanivan/villereorder/" + arrData[3] + "/" + id + "/" + $("#dteActivePage1").val() + '?typecedule=' + typecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val();
			}
			break;
	}
	if (urlName == '') {
		return;
	}
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").html(objData.today);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});
}
// Retire l'assignation superviseur d'un groupe de ville (menu d'actions).
function clearSupVille(idgrp) {
	var urlName = "/sanivan/villesuperviseur/" + idgrp + "/0/" + $("#dteActivePage1").val() +
		'?typecedule=' + activecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val() + '&typecam=' + typecam;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#divCedule").html(objData.today);
			$("#spRearEnd").html(objData.spRearEnd);
			$("#spRobot").html(objData.nbRobot);
			$("#spTotalCam").html(objData.nbAllCam);
			if (objData.limitcedule === "1") {
				$("#chkRearLoad").prop('checked', true);
			}
			else if (objData.limitcedule === "2") {
				$("#chkRobot").prop('checked', true);
			}
		}
	});
}
// Affiche/cache la ligne separatrice d'un superviseur (case du menu).
// L'etat est conserve cote serveur en BD (cedules_supvis), global et
// limite a la journee affichee; la cedule est rechargee.
function toggleSupVisible(supidx, chk) {
	var urlName = "/sanivan/superviseurvisible/" + supidx + "/" + ((chk.checked) ? 1 : 0) + "/" + $("#dteActivePage1").val() +
		'?typecedule=' + activecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val() + '&typecam=' + typecam;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#divCedule").html(objData.today);
			$("#spRearEnd").html(objData.spRearEnd);
			$("#spRobot").html(objData.nbRobot);
			$("#spTotalCam").html(objData.nbAllCam);
			if (objData.limitcedule === "1") {
				$("#chkRearLoad").prop('checked', true);
			}
			else if (objData.limitcedule === "2") {
				$("#chkRobot").prop('checked', true);
			}
		}
	});
}
// Drop d'une ville sur la ligne separatrice d'un superviseur:
// assigne le groupe de ville a ce superviseur pour la journee affichee.
function dropSupVille(ev, idsup) {
	ev.preventDefault();
	var arrData = ev.dataTransfer.getData("text").split('_');
	if ((arrData[0] != 'lblville') || (arrData.length < 4)) {
		return;
	}
	var urlName = "/sanivan/villesuperviseur/" + arrData[3] + "/" + idsup + "/" + $("#dteActivePage1").val() +
		'?typecedule=' + activecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val() + '&typecam=' + typecam;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#divCedule").html(objData.today);
			$("#spRearEnd").html(objData.spRearEnd);
			$("#spRobot").html(objData.nbRobot);
			$("#spTotalCam").html(objData.nbAllCam);
			if (objData.limitcedule === "1") {
				$("#chkRearLoad").prop('checked', true);
			}
			else if (objData.limitcedule === "2") {
				$("#chkRobot").prop('checked', true);
			}
		}
	});
}

function dropcam(ev, id) {
	ev.preventDefault();
	var urlName = '';
	var arrData = ev.dataTransfer.getData("text").split('_');
	var cam = arrData[1];
	var oldid = arrData[2];
	urlName = "/sanivan/camion/" + id + "/" + oldid + "/" + cam + "/" + $("#dteActivePage1").val() + '?typecedule=' + activecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val() + '&typecam=' + typecam;
	//document.location = urlName;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").html(objData.today);
		$("#divDropCamion").html(objData.CamionDrop);
		$("#divDropRH").html(objData.ChauffeurDrop);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});
}
function droproute(ev, id) {
	ev.preventDefault();
	var urlName = '';
	//alert(ev.dataTransfer.getData("text"));
	var arrData = ev.dataTransfer.getData("text").split('_');
	var route = arrData[1];
	RouteLier = (($("#chkRouteDeux").is(':checked')) ? 1 : 0);
	urlName = "/sanivan/route/insupd/" + id + "/" + route + "/" + $("#dteActivePage1").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//var objData = JSON.parse(msg);
		//alert(msg);
		var arrMsg = msg.split('_');
		var lbl = '<label  id="rte_' + id + '" title="' + arrMsg[0] +
			'" style="width: 140px;background-color: black;color:white;font-weight:bolder;font-size: medium; margin-top: 5px; ">' +
			arrMsg[1] + '</label > ';
		$("#tdroute_" + id).html(lbl);
	});
}
function dropsecteur(ev, id) {
	ev.preventDefault();
	var urlName = '';
	var arrData = ev.dataTransfer.getData("text").split('_');
	var secteur = arrData[1];
	var oldid = arrData[2];
	urlName = "/sanivan/secteur/" + id + "/" + oldid + "/" + $("#dteActivePage1").val() + "?secteur=" + secteur + '&typecedule=' + activecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val() + '&typecam=' + typecam;
	//alert(urlName);
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").html(objData.today);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});
}
function dropaide(ev, id) {
	ev.preventDefault();
	var urlName = '';
	var idexistant = 0;
	var arrData = ev.dataTransfer.getData("text").split('_');
	var idaide = arrData[1];
	if (arrData.length > 2) {
		idexistant = arrData[2];
	}
	urlName = "/sanivan/aide/" + id + "/" + idaide + "/" + $("#dteActivePage1").val() + "/" + idexistant + '?typecedule=' + activecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").empty();
		$("#divCedule").html(objData.today);
		$("#divDropCamion").html(objData.CamionDrop);
		//$("#divDropRH").html(objData.ChauffeurDrop);
		activeRH = (($("#chkShowConge").is(':checked')) ? 0 : 1);
		var urlName = '/sanivan/rhtodrop/' + $("#typechauf").val() + '/' + $("#dteActivePage1").val() + '?action=' + activeRH;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			$("#divDropRH").html(msg);
		});
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});
}
function SetProduit(id) {
	var urlName = '';
	var arrData = id.split('_');
	var idcedule = arrData[1];
	var typeprod = $('#produit_' + idcedule).val();
	urlName = "/sanivan/typeprod/" + idcedule + "/" + typeprod;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#produit_" + idcedule).val(objData.produit);
		$("#produit_" + idcedule).css("background-color", objData.color);
		$("#trproduit_" + idcedule).css("background-color", objData.color);
	});
}
function dragMouseDown(e) {
	e = e || window.event;
	e.preventDefault();
	// get the mouse cursor position at startup:
	pos3 = e.clientX;
	pos4 = e.clientY;
	document.onmouseup = closeDragElement;
	// call a function whenever the cursor moves:
	document.onmousemove = elementDrag;
}

function elementDrag(e) {
	e = e || window.event;
	e.preventDefault();
	// calculate the new cursor position:
	pos1 = pos3 - e.clientX;
	pos2 = pos4 - e.clientY;
	pos3 = e.clientX;
	pos4 = e.clientY;
	// set the element's new position:
	elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
	elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
}

function closeDragElement() {
	/* stop moving when mouse button is released:*/
	document.onmouseup = null;
	document.onmousemove = null;
}
function RemoveVilleCreate(id,ACol) {
	$("#idgrptodelete").val(id);
	$("#colgrptodelete").val(ACol);
	$("#modalRemoveVille").modal("show");
}
function DeleteVilleCreate() {
	urlName = "/sanivan/deleteville/" + $("#idgrptodelete").val() + "/" + activecedule + "/" + $("#colgrptodelete").val() + "/" + $("#dteActivePage1").val() + '?limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			$("#divCedule").html(objData.today);
			$("#spRearEnd").html(objData.spRearEnd);
			$("#spRobot").html(objData.nbRobot);
			$("#spTotalCam").html(objData.nbAllCam);
			if (objData.limitcedule === "1") {
				$("#chkRearLoad").prop('checked', true);
			}
			else if (objData.limitcedule === "2") {
				$("#chkRobot").prop('checked', true);
			}
		}
	});
}
function DeleteLigneCedule() {
	var urlName = "/sanivan/deleteevent/" + evenPost.id;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = "/sanivan/calendrier";
	});
}
function CloseModalCedule() {
	$("#modalCedule").modal("hide");
}
function RemoveLigneCedule() {
	var urlName = "/sanivan/deleterow/" + $('#activeIdCedule').val() + "/" + activecedule + "/" + $("#dteActivePage1").val() + '?limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val() + '&typecam=' + typecam;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			$("#divCedule").html(objData.today);
			$("#spRearEnd").html(objData.spRearEnd);
			$("#spRobot").html(objData.nbRobot);
			$("#spTotalCam").html(objData.nbAllCam);
			$("#divDropCamion").html(objData.CamionDrop);
			$("#divDropRH").html(objData.ChauffeurDrop);
			if (objData.limitcedule === "1") {
				$("#chkRearLoad").prop('checked', true);
			}
			else if (objData.limitcedule === "2") {
				$("#chkRobot").prop('checked', true);
			}
		}
	});
}
function DeleteRessValid(id) {
	var urlName = "/sanivan/deletesuivis/" + id;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = "/sanivan/validation";
	});
}
function DeleteCamionValid(id) {
	var urlName = "/sanivan/deletecam/" + id;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = "/sanivan/validation";
	});
}
function changeChauffeur() {
	var arrInfo = evenPost.description.split(', ');
	ActifChauffeur = arrInfo[2];
}
function changeAide() {
	var arrInfo = evenPost.description.split(', ');
	ActifAide = arrInfo[4];
}
function changeCamion() {
	var arrInfo = evenPost.description.split(', ');
	ActifAide = arrInfo[0];
}
function UpdRetour(id) {
	var arrId = id.split('_');
	var urlName = "/sanivan/vide_materiel/" + arrId[1] + '/' + $("#dteActivePage1").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = msg;
	});
}
function UpdRetourHome() {
	var urlName = "/sanivan/vide_materiel/" + $('#idsuiviscam').val() + '/' + ActiveDate;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = "/admin";
	});
}
function UpdViderMat(id) {
	var arrId = id.split('_');
	var urlName = "/sanivan/vide_materiel/" + arrId[1] + '/' + $("#dteActivePage1").val() + '?limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val();
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (msg) {
		document.location = msg;
	});
}
function UpdViderMatHome() {
	var urlName = "/sanivan/vide_materiel/" + $('#idsuiviscam').val() + '/' + ActiveDate;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = msg;
	});
}
function UpdRetourRHHome() {
	var urlName = "/sanivan/retourrh/" + $('#idsuivisrh').val() + '/' + ActiveDate;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = "/admin";
	});
}
function UpdRetourRHList() {
	var currentdate = new Date();
	var mois = currentdate.getMonth() + 1;
	var annee = currentdate.getFullYear();
	var actived = annee + '-' + mois + '-' + currentdate.getDate();
	var urlName = "/sanivan/retourrh/" + $('#idsuivisrhlist').val() + '/' + actived;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = "/sanivan/showsuivisrhlist/" + actived + "?from = 0";
	});
}
function PostSuivis(id) {
	var arrId = id.split('_');
	var urlName = "/sanivan/jour_validation/" + $('#dteCalcul').val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			$("#tbvalid").html(msg);
		}
	});
}
function PostSuivisCamMat() {
	var urlName = "/sanivan/camionout/" + $('#idsuiviscam').val() + "?nocamion=" + $('#camion').val() + "&datestart=" + $('#dtestart').val() + "&typemat=" + $('#typemat').val() +
		"&notehs=" + $('#notemat').val() + "&idstatut=2&action=2";
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = msg;
	})
}
function PostSuivisCamHS() {
	var urlName = "/sanivan/camionout/" + $('#idsuiviscam').val() + "?nocamion=" + $('#camionhs').val() + "&datestart=" + $('#dtestarths').val() + "&dateend=" +
		$('#dteendhs').val() + "&notehs=" + $('#notehs').val() + "&idstatut=1&action=2";
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = msg;
	});

}
function PostSuivisRH() {
	var chkshow = (($("#showavis").is(':checked')) ? 1 : 0);
	var urlName = "/sanivan/ressourse/" + $('#selRH').val() + "/" + $('#selRHabsence').val() + "/" + $('#dtestartrh').val() + "?dteretour=" +
		$('#dteendrh').val() + "&noterh=" + $('#noterh').val() + "&showavis=" + chkshow +
		"&avistoshow=" + $('#avistoshow').val() + "&idsuivis=" + $('#idsuivisrh').val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = msg;
	});
}
function PostSuivisRHList() {
	var chkshow = (($("#showavislist").is(':checked')) ? 1 : 0);
	var urlName = "/sanivan/ressourse/" + $('#selRHlist').val() + "/" + $('#selRHabsencelist').val() + "/" + $('#dtestartrhlist').val() + "?dteretour=" +
		$('#dteendrhlist').val() + "&noterh=" + $('#noterh').val() + "&showavis=" + chkshow +
		"&avistoshow=" + $('#avistoshowlist').val() + "&idsuivis=" + $('#idsuivisrhlist').val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var currentdate = new Date();
		var mois = currentdate.getMonth() + 1;
		var annee = currentdate.getFullYear();
		var actived = annee + '-' + mois + '-' + currentdate.getDate();
		document.location = "/sanivan/showsuivisrhlist/" + actived + "?from = 0";
	});
}

function ShowHideVilleToday(id) {
	if ($('#' + id).css('display') !== 'none') {
		$('#' + id).hide();

		//$('#divCedule').addClass("col-md-12");
		$('#divShowHide').text('Afficher les villes');
	}
	else {
		//$('#divCedule').removeClass("col-md-12");
		//$('#divCedule').addClass("col-md-8");

		$('#' + id).show();
		$('#divShowHide').text('Cacher les villes');
	}
}
function ShowHideCreateEmp(id) {
	if ($('#' + id).css('display') !== 'none') {
		$('#' + id).hide();
		$('#divCreateCed').css('margin-left', '0px');
	}
	else {
		$('#divVilles').hide();
		$('#' + id).show();
		//ActiveDrop = 2;
		$('#divCreateCed').css('margin-left', '210px');
	}
}
function ShowHideCreateCam(id) {
	if ($('#' + id).css('display') !== 'none') {
		$('#' + id).hide();
		$('#divCreateCed').css('margin-left', '0px');
		//ActiveDrop = 0;
	}
	else {
		$('#divVilles').hide();
		$('#' + id).show();
		//ActiveDrop = 2;
		$('#divChauffeurs').hide();
		$('#divCreateCed').css('margin-left', '230px');
	}
}
function ShowHideRouteToday(id) {
	if ($('#' + id).css('display') !== 'none') {
		$('#' + id).hide();
		$('#btnShowRouteCD').text('Afficher Villes');
		$('#divShowCed').css('margin-left', '230px');
		$('#divChauffeurs').show();
	}
	else {
		$('#' + id).show();
		$('#btnShowRouteCD').text('Cacher Villes');
		$('#divShowCed').css('margin-left', '320px');
		$('#divChauffeurs').hide();
	}
}
function ChangeSizeOfDiv(bodych) {
	//var objHeight = $('body').height;
	//var objwidth = $('body').width;
	//alert($('body').width());
}
function ShowHideCreate(id) {
	if ($('#' + id).css('display') !== 'none') {
		$('#' + id).hide();
		$('#divCreateCed').css('margin-left', '0px');
		//ActiveDrop = 0;
	}
	else {
		$('#divChauffeurs').hide();
		$('#' + id).show();
		//ActiveDrop = 1;
		$('#divCreateCed').css('margin-left', '210px');
	}
}
function ShowHide(id) {
	if ($('#' + id).css('display') !== 'none') {
		$('#' + id).hide();
		$('#divCalendar').removeClass("col-md-9");
		$('#divCalendar').addClass("col-md-12");
		$('#divShowHide').text('Afficher les villes');
	}
	else {
		$('#divCalendar').removeClass("col-md-12");
		$('#divCalendar').addClass("col-md-9");
		$('#' + id).show();
		$('#divShowHide').text('Cacher les villes');
	}
}
function ShowChauf() {
	$('#divDropChauffeur').show();
	$('#divDropAide').hide();
	$('#liAide').removeClass("active");
	$('#liChauf').addClass("active");
}
function ShowAide() {
	$('#divDropChauffeur').hide();
	$('#divDropAide').show();
	$('#liChauf').removeClass("active");
	$('#liAide').addClass("active");
}
function ShowAllRH() {
	activeRH = (($("#chkShowConge").is(':checked')) ? 0 : 1);
	var urlName = "/sanivan/rhtodrop/" + $('#typechauf').val() + '/' + ActiveDate + '?action=' + activeRH;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$("#divDropRH").html(msg);
	});
}
function HideAllRH() {
	activeRH = 1;
	if ($('#divDropAide').css('display') !== 'none') {
		activeRH = 2;
  }
	var urlName = "/sanivan/showhideconge/" + ActiveDate + '?action=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			$("#divDropChauffeur").html(objData.chauffeurs);
			$("#divDropAide").html(objData.aides);
			$("#chkShowAllRH").show();
			$("#chkHideAllRH").hide();
		}
	});
}
function CancelEvent() {
	document.location = "/sanivan/calendrier";
}
function updEvent() {
	var dt = evenPost.start.format();
	var urlName = '/sanivan/updevent/' + evenPost.id + '/' + dt;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		//var objData = JSON.parse(data);
	});

}
function PostEvent() {
	var post = 'upd';
	var dt = evenPost.start.format();
	if (evenPost.id === 0) {
		post = 'add';
	}
	var urlName = '/sanivan/add_upd/' + $("#typeprod").val() + '/' + $("#camion").val() + '/' + $("#chauffeur").val() + '/' +
		$("#aide").val() + '/' + evenPost.id + '/' + ActiveVille + '/' + dt + '/' + $("#plTitre").html() + '/' + post;
	$.ajax({
		url: urlName,
		type: "POST"
	}).done(function (data) {
		$("#divCalendar").html(data);
		//document.location = '/sanivan/calendrier'; 
	});

}
function ShowLigneCedule(id) {
	var arrId = id.split('_');
	ActiveCedule = arrId[1];
	var urlName = "/sanivan/lignecedule/" + ActiveCedule;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		var objData = JSON.parse(data);
		$("#activeIdCedule").val(ActiveCedule);
		$("#plTitre").html(objData.VilleNom);
		$("#typecam").val(objData.TypeeCedule).change();
		$("#camionligne").html(objData.lstcamion);
		$("#camionligne").val(objData.NOCAMION).change();
		$("#typechauf").val(((objData.TypeeCedule == 1) ? 1 : 3)).change();
		$("#chauffeur").html(objData.lstchauffeur);
		$("#chauffeur").val(objData.idchauffeurs).change();
		$("#aide").html(objData.lstaide);
		$("#aide").val(objData.Aide).change();
		$("#secteur").val(objData.Secteur).change();
		$("#modalCedule").modal("show");
	});

}
function ShowCedule(id) {
	var arrId = id.split('_');
	ActiveCedule = arrId[1];
	var urlName = "/sanivan/afficher/" + ActiveCedule;
	clearTimeout(myTimer);
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		var objData = JSON.parse(data);
		$("#plTitre").html(objData.VilleNom);
		$("#typecamModal").html(objData.lsttypecam);
		$("#camionligne").html(objData.lstcamion);
		$("#camionligne").val(objData.NOCAMION).change();
		//$("#typechaufModal").val(((objData.TypeCedule == 1)? '1' : '3')).change();
		$("#chauffeur").html(objData.lstchauffeur);
		$("#chauffeur").val(objData.idchauffeurs).change();
		$("#aide").html(objData.lstaide);
		//$("#aide").val(objData.Aide).change();
		$("#secteur").val(objData.Secteur).change();
		$('#activeIdCedule').val(objData.ID);
		$("#modalCedule").modal("show");
	});

}
function ShowRH() {
	$("#showMenu").popover('hide');
	$("#modalrh").modal("show");
}
function ShowCamion(typecam, from) {
	$("#showMenu").popover('hide');
	switch (typecam) {
		case 1:
			$("#idsuiviscam").val(0);
			$("#camionhs").val('').change();
			$("#dtestarths").val('');
			$("#dteendhs").val('');
			$("#notehs").val('');
			$("#btnEndHS").hide();
			$("#modalhs").modal("show");
			break;
		case 2:
			$("#idsuiviscam").val(0);
			$("#camion").val('').change();
			$("#dtestart").val('');
			$("#dteend").val('');
			$("#typemat").val('').change();
			break;
			$("#qte").val('');
			$("#notemat").val('');
			$("#btnEndMat").html('Confirmer Vidé');
			$("#btnEndMat").hide();
			$("#modalmat").modal("show");
	}
}
/**
 * Affiche le modal de gestion des polylines pour une cédule
 * @param {number} idcedule - ID de la cédule
 * @param {number} idsecteur - ID du secteur
 * @param {number} idville - ID de la ville
 */
function showPolylineModal(idcedule, idsecteur, idville) {
	// Stocker les infos
	$('#modalPolylineIdCedule').val(idcedule);
	$('#modalPolylineIdVille').val(idville);

	// Charger les parcours disponibles pour la ville (panneau gauche)
	loadAvailablePolylines(idville, idcedule);

	// Charger les polylines déjà sélectionnées (panneau droit)
	loadSelectedPolylines(idcedule);

	// Afficher le modal
	$('#modalPolyline').modal('show');
}

/**
 * Charge la liste des parcours disponibles pour une ville (panneau gauche)
 * @param {number} idville - ID de la ville
 * @param {number} idcedule - ID de la cédule
 */
function loadAvailablePolylines(idville, idcedule) {
	var $container = $('#polylineAvailableList');
	$container.html('<p class="text-muted">Chargement...</p>');

	$.ajax({
		url: '/sanivan/getsecteurs_by_ville',
		type: 'GET',
		data: { idville: idville },
		dataType: 'json',
		success: function (response) {
			if (response.ok && response.secteurs && response.secteurs.length > 0) {
				var html = '';
				$.each(response.secteurs, function (i, poly) {
					html += '<div class="polyline-available-item" data-id="' + poly.id + '" '
						+ 'style="padding:8px 10px;margin-bottom:4px;border:1px solid #ddd;border-radius:4px;cursor:pointer;background:#fff;transition:background 0.2s;" '
						+ 'onmouseover="this.style.background=\'#e8f4fd\'" '
						+ 'onmouseout="this.style.background=\'#fff\'" '
						+ 'onclick="addPolylineToCedule(' + poly.id + ')">' 
						+ '<i class="fa fa-map-marker" style="color:#4a90e2;margin-right:8px;"></i>'
						+ '<span>' + $('<span>').text(poly.nom).html() + '</span>'
						+ '</div>';
				});
				$container.html(html);
			} else {
				$container.html('<p class="text-muted">Aucun parcours disponible pour cette ville</p>');
			}
		},
		error: function () {
			$container.html('<p class="text-danger">Erreur lors du chargement des parcours</p>');
		}
	});
}

/**
 * Ajoute un parcours à la cédule depuis le panneau gauche
 * @param {number} idpolyline - ID du parcours à ajouter
 */
function addPolylineToCedule(idpolyline) {
	var idcedule = $('#modalPolylineIdCedule').val();
	if (!idcedule || !idpolyline) return;
	togglePolyline(idcedule, idpolyline, true);
}

/**
 * Rafraîchit la liste des parcours disponibles (panneau gauche)
 * Appelée après un toggle, suppression, etc.
 */
function loadPolylinesBySecteurModal() {
	var idville = $('#modalPolylineIdVille').val();
	var idcedule = $('#modalPolylineIdCedule').val();
	if (idville) {
		loadAvailablePolylines(idville, idcedule);
	}
}
/**
 * Enregistre le nouveau polyline sélectionné
 */
function saveModalPolyline() {
	var idcedule = $('#modalPolylineIdCedule').val();
	var idpolyline = $('#selModalPolyline').val();

	if (!idpolyline || idpolyline == 0) {
		alert('Veuillez sélectionner un parcours');
		return;
	}

	$.ajax({
		url: base_url + 'sanivan/setpolylinecedule',
		type: 'POST',
		data: {
			idcedule: idcedule,
			idpolyline: idpolyline
		},
		success: function (response) {
			if (response === 'OK') {
				$('#modalPolyline').modal('hide');
				// Recharger la page pour afficher les changements
				location.reload();
			} else {
				alert('Erreur : ' + response);
			}
		},
		error: function () {
			alert('Erreur de communication avec le serveur');
		}
	});
}
function SupprimerPolyline(id) { 
	if (!confirm('Voulez-vous vraiment supprimer ce parcours ?')) {
		return;
	}
	var urlName = base_url + 'sanivan/polylinesectremo/' + id;
	$.ajax({
		url: urlName,
		type: 'POST',
	}).done(function (data) {
		var objData = JSON.parse(data);
		if (objData.result === 'ok') {
			// Recharger la page
			alert('Suppression du parcours réussie')
			location.reload();
		} else {
			alert('Erreur : ' + response);
		}

	});
}
/**
 * Supprime le polyline d'une cédule
 */
function removeModalPolyline() {
	if (!confirm('Voulez-vous vraiment supprimer ce parcours ?')) {
		return;
	}

	var idcedule = $('#modalPolylineIdCedule').val();

	$.ajax({
		url: base_url + 'sanivan/removepolylinecedule',
		type: 'POST',
		data: {
			idcedule: idcedule
		},
		success: function (response) {
			if (response === 'OK') {
				$('#modalPolyline').modal('hide');
				// Recharger la page
				location.reload();
			} else {
				alert('Erreur : ' + response);
			}
		},
		error: function () {
			alert('Erreur de communication');
		}
	});
}
function PostCedule() {
	ActiveDate = $('#dteActive').val();
	activeRH = (($("#chkShowConge").is(':checked')) ? 0 : 1);
	var urlName = '/sanivan/post?secteur=' + $("#secteur").val() +
		'&camion=' + $("#camionligne").val() +
		'&idchauffeur=' + $("#chauffeur").val() +
		'&idaide=' + $("#aide").val() +
		'&dateactive=' + $('#dteActivePage1').val() +
		'&id=' + $('#activeIdCedule').val() +
		'&activedrop=' + ActiveDrop +
		'&typecedule=' + activecedule +
		'&notab=' + notab +
		'&limit=' + limitcedule +
		'&typerh=' + $("#typechauf").val() +
		'&typecam=' + typecam +
		'&action=' + activeRH;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		var objData = JSON.parse(data);
		$("#divCedule").html(objData.today);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		$("#divDropCamion").html(objData.CamionDrop);
		$("#divDropRH").html(objData.ChauffeurDrop);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});

}
function PostLigneCedule() {
	var start = 0;
	ActiveDate = $("#dteActivePage1").val();
	var urlName = '/sanivan/post/' + $("#secteur").val() + '/' + $("#camion").val() + '/' + $("#chauffeur").val() + '/' +
		$("#aide").val() + '/' + ActiveCedule + '/' + start + '/' + $("#dteActivePage1").val() + '/createcedule' + '?limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val();
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		if (data !== "") {
			var objData = JSON.parse(data);
			$("#produit_" + ActiveCedule).val(objData.TypeDeProduit);
			$("#chauffeur_" + ActiveCedule).val(objData.NomChauffeur);
			$("#aide_" + ActiveCedule).val(objData.Aide);
			$("#camion_" + ActiveCedule).val(objData.NOCAMION);
			if (objData.limitcedule === "1") {
				$("#chkRearLoad").prop('checked', true);
			}
			else if (objData.limitcedule === "2") {
				$("#chkRobot").prop('checked', true);
			}
		}
	});
}
function gotoSelectDay() {
	document.location = '/sanivan/aujourdhui/1/' + ActiveDate;
}
function _SessionExpired() {
	var currentdate = new Date();
	var mois = currentdate.getMonth() + 1;
	var annee = currentdate.getFullYear();
	switch (currentdate.getMonth()) {
		case 12:
			mois = '01';
			annee = currentdate.getFullYear() + 1;
			break;
		case 1:
		case 2:
		case 3:
		case 4:
		case 5:
		case 6:
		case 7:
		case 8:
		case 9:
			mois = '0' + mois;
			break;
	}
	ActiveDate = annee + '-' + mois + '-' + currentdate.getDate();
	var curheure = currentdate.getHours();
	if (parseInt( currentdate.getHours()) > 11) {
		var jourSemaine = currentdate.getDay();
		var jour = currentdate.getDate() + 1;
		switch (jourSemaine) {
			case 5:
				jour = currentdate.getDate() + 3;
				break;
			case 6:
				jour = currentdate.getDate() + 2;
				break;
			default:
				jour = currentdate.getDate() + 1;
		}
		switch (jour) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
				jour = '0' + jour;
				break;
		}
		ActiveDate = annee + '-' + mois + '-' + jour;
	}
	else {
		var jourSemaine1 = currentdate.getDay();
		var jour1 = currentdate.getDate();
		switch (jourSemaine1) {
			case 5:
				jour1 = currentdate.getDate() + 3;
				break;
			case 6:
				jour1 = currentdate.getDate() + 2;
				break;
			default:
				jour1 = currentdate.getDate();
		}
		switch (jour) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
				jour1 = '0' + jour;
				break;
		}
		ActiveDate = annee + '-' + mois + '-' + jour1;
	}
	//setTimeout("_SessionExpired()", 1 * 60 * 1000);
	var urlName = "/sanivan/aujourdhui/3/" + ActiveDate;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (data) {
		if (data !== "") {
			var objData = JSON.parse(data);
			//$("#divCreate").html(objData.createtoday);
			$("#divCedule").html(objData.today);
			$("#spRearEnd").html(objData.spRearEnd);
			$("#spRobot").html(objData.nbRobot);
			$("#divDropCamion").html(objData.CamionDrop);
			$("#divDropRH").html(objData.ChauffeurDrop);
			$("#non_dispo_chauf").html(objData.non_dispo_chauf);
			$("#hs_dispo_cam").html(objData.hs_cam);
			$("#non_dispo_cam").html(objData.non_dispo_cam);
			$("#non_dispo_aide").html(objData.non_dispo_aide);
			$("#spTotalCam").html(objData.nbAllCam);
			$("#tboRHhres").html(objData.listhresaides);
			$("#dteRH").val(objData.dtetoday);
			if (objData.limitcedule === "1") {
				$("#chkRearLoad").prop('checked', true);
			}
			else if (objData.limitcedule === "2") {
				$("#chkRobot").prop('checked', true);
			}

		}
	});
	//document.location = "/enviro_laval/tv?load=IJZoUzzS1Ec03Z9zvg==&dateactive=" + ActiveDate;
	//
	
}
function _TodaySessionExpired() {
	activeRH = (($("#chkShowConge").is(':checked')) ? 0 : 1);
	var urlName = "/sanivan/aujourdhui/5/" + ActiveDate + '?typecedule=' + activecedule + '&activedrop=' + ActiveDrop +
		'&notab=' + notab + '&limit=' + limitcedule + '&typerh=' + $("#typechauf").val() + '&typecam=' + typecam + '&action=' + activeRH;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (data) {
		if (data !== "") {
			var objData = JSON.parse(data);
			//$("#divCreate").html(objData.createtoday);
			$("#divCedule").html(objData.today);
			$("#spRearEnd").html(objData.spRearEnd);
			$("#spRobot").html(objData.nbRobot);
			$("#divDropCamion").html(objData.CamionDrop);
			$("#divDropRH").html(objData.ChauffeurDrop);
			$("#non_dispo_chauf").html(objData.non_dispo_chauf);
			$("#hs_dispo_cam").html(objData.hs_cam);
			$("#non_dispo_cam").html(objData.non_dispo_cam);
			$("#non_dispo_aide").html(objData.non_dispo_aide);
			$("#spTotalCam").html(objData.nbAllCam);
			$("#tboRHhres").html(objData.listhresaides);
			$("#dteRH").val(objData.dtetoday);
			if (objData.limitcedule === "1") {
				$("#chkRearLoad").prop('checked', true);
			}
			else if (objData.limitcedule === "2") {
				$("#chkRobot").prop('checked', true);
			}

		}
	});
	//
	//myTimer = setTimeout("_TodaySessionExpired()", 1 * 60 * 1000);

}
function setMessageView() {
	var urlName = "/sanivan/postviewhs";
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			//$("#tboRO").html(msg);
		}
	});
}
function checkSession() {
	var urlName = "/session/checksession";
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg == '1') {
			document.location = "/session/bloque";
			clearTimeout(mySessionCheck);
			clearTimeout(myTimer);
		}
		else {
			switch (msg) {
				case '2':
				case '3':
				case '4':
				case '5':
					//myTimer = setTimeout("_TodaySessionExpired()", 1 * 60 * 1000);
					break;
				default:
					//mySessionCheck = setTimeout("checkSession()", 20 * 60 * 1000);
      }
    }
	});
	//

}
function LoadCreateCedule(typecedule, nbjour) {
	var urlName = "/sanivan/getdate/" + nbjour + "/" + $('#dteActive').val();
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var newdate = '';
		if (msg !== "") {
			newdate = msg;
		}
		document.location = "/sanivan/cedule/" + typecedule + "/" + nbjour + "/" + newdate;
	});
}

function LoadRHhres() {
	var urlName = '/sanivan/gethrrh/' + $('#dteRH').val();
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#tboRHhres").html(data);
	});
}
function loadToday(nbjour) {
	var urlName = "/sanivan/getdate/" + nbjour + "/" + $('#dteActivePage1').val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			$('#dteActivePage1').val(msg);
			ActiveDate = $('#dteActivePage1').val();
			var urlName = '/sanivan/aujourdhui/4/' + $('#dteActivePage1').val() + '?typecedule=' + activecedule + '&limit=' + limitcedule + '&activedrop=' + ActiveDrop + '&typerh=' + $("#typechauf").val() + '&typecam=' + typecam;
			$.ajax({
				url: urlName,
				context: document.body
			}).done(function (data) {
				var objData = JSON.parse(data);
				$('#dteActive').val(ActiveDate);
				$("#divCedule").html(objData.today);
				$("#spRearEnd").html(objData.spRearEnd);
				$("#spRobot").html(objData.nbRobot);
				$("#spTotalCam").html(objData.nbAllCam);
				$("#divDropCamion").html(objData.CamionDrop);
				$("#divDropRH").html(objData.ChauffeurDrop);
				if (objData.limitcedule === "1") {
					$("#chkRearLoad").prop('checked', true);
				}
				else if (objData.limitcedule === "2") {
					$("#chkRobot").prop('checked', true);
				}

			});
		}
	});
}
function loadTodayCR() {
	ActiveDate = $("#dteActivePage1").val();
	var urlName = '/sanivan/aujourdhui/4/' + $("#dteActivePage1").val() + '?activedrop=' + ActiveDrop +
		'&typecedule=' + activecedule + '&notab=' + notab + '&AideIsShow=' + AideIsShow + '&limit=' + limitcedule + '&typerh=' + $("#typechauf").val() ;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		var objData = JSON.parse(data);
		$("#divCedule").html(objData.today);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}

	});
}
function LoadTodayPage1(nbjour) {

	ActiveDate = $('#dteActivePage1').val();
	document.location = '/sanivan/aujourdhui/1/' + ActiveDate + '?activedrop=' + ActiveDrop +
		'&typecedule=' + activecedule + '&notab=' + notab + '&AideIsShow=' + AideIsShow;
}
function ReLoadToday(page) {
	switch (page) {
		case 1:
			document.location = base_url + "sanivan/calendrier";
			break;
		case 2:
			document.location = base_url + "sanivan/aujourdhui/1/" + ActiveDate;
			break;
		case 3:
			document.location = base_url + "sanivan/validation";
			break;
		case 4:
			document.location = base_url + "admin";

	}

	//
}

function getWeekToCopy() {
	var radioValue = $("input[name='r2']:checked").val();
	var urlName = "/sanivan/getweek/" + radioValue + "/" + $("#dteFrom").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		
		$("#showInfo").show();
		//
		$("#calendar").html(msg);
		$("#calendar").show();
		$("#divCalendar").hide();
	});
}
function submitCopy() {
	if (confirm('Attention! Etes-vous certain de vouloir copier la cedule?')) {
		$("#divCalendar").hide();
		//$("#btnSubmit").prop("disabled", true);
		$("#copy_popup").modal("show");
		var formdata = $("#frmCopy").serialize();
		var urlName = "/sanivan/transfert_copie?" + formdata;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				$("#copy_popup").modal("hide");
				alert(msg);
				
			}
			else {
				//-------------------
			}
		});
  }


}
function ShowRemoveLigne() {
	$("#showMenu").popover('hide');
	$("#modalRemoveLigneCedule").modal("show");
}
function ShowRemoveCedule() {
	$("#showMenu").popover('hide');
	$("#filtre").collapse('toggle');
	$("#modalRemoveCedule").modal("show");
}
function RemoveCedule() {
	var chkrearend = (($("#chkRearEnd").is(':checked')) ? 1 : 0);
	var chkrobos = (($("#chkRobot").is(':checked')) ? 1 : 0);
	var message = '<span style="color: red;"> <b> Attention!</b></span> Êtes-vous certain de vouloir supprimer la cédule du <b>' +
		$("#dtestartdel").val() + '</b> au <b>' + $("#dteenddel").val() + '</b>?' +
		' <br /> <br /><span class="btn-warning"> Cette action ne pourra pas être annulée.</span>';
	BootstrapDialog.confirm({
		title: 'ATTENTION',
		message: message,
		type: BootstrapDialog.TYPE_DANGER, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
		closable: true, // <-- Default value is false
		draggable: true, // <-- Default value is false
		btnCancelLabel: 'Annuler', // <-- Default value is 'Cancel',
		btnOKLabel: 'Supprimer', // <-- Default value is 'OK',
		btnOKClass: 'btn-danger', // <-- If you didn't specify it, dialog type will be used,
		callback: function (result) {
			// result will be true if button was click, while it will be false if users close the dialog directly.
			if (result) {
				var urlName = "/sanivan/removecedule/" + chkrearend + "/" + chkrobos + "/" + $("#dtestartdel").val() + "/" + $("#dteenddel").val();
				$.ajax({
					async: true,
					type: "POST",
					url: urlName
				}).done(function (msg) {
					BootstrapDialog.show({
						title: 'Suppression de cédule',
						message: 'Votre de mande de suppression a été effectuée avec succès',
						closable: true,
						buttons: [ {
							label: 'Fermer',
							action: function (dialogRef) {
								dialogRef.close();
							}
						}]
					});
				});
			} else {
				//alert('Nope.');
			}
		}
	});
}
function ShowAddCam() {
	$("#modalAddCamion").modal("show");
}
function ShowCamionInfo(id) {
	var arrId = id.split('_');
	var urlName = "/sanivan/camioninfo?camion=" + arrId[1];
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		var objData = JSON.parse(data);		
		$("#edtCam").val(objData.NoCamion);
		$("#plTitreMsgUpd").html('Edition du camion:&nbsp;' + objData.NoCamion);
		$("#selTypeCamUpd").val(objData.TypeCamion).change();
		if (objData.isdeleted === "1") {
			$("#chkDelete").prop('checked', true);
		}
		else {
			$("#chkdelete").prop('checked', false);
		}
		if (objData.IsAutomatique === "1") {
			$("#chkAuto").prop('checked', true);
		}
		else {
			$("#chkAuto").prop('checked', false);
		}
		if (objData.IsManuel === "1") {
			$("#chkManuel").prop('checked', true);
		}
		else {
			$("#chkManuel").prop('checked', false);
		}
		if (objData.IsVerseur === "1") {
			$("#chkVerseur").prop('checked', true);
		}
		else {
			$("#chkVerseur").prop('checked', false);
		}
		if (objData.HaveWinch === "1") {
			$("#chkTreuil").prop('checked', true);
		}
		else {
			$("#chkTreuil").prop('checked', false);
		}
		$("#modalEditCamion").modal("show");
	});

}
function ShowRHInfo(id) {
	var urlName = "/sanivan/rhinfo/" + id;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		var objData = JSON.parse(data);
		$("#edtID").val(objData.id);
		$("#edtNomUpd").val(objData.Nom);
		$("#edtPrenomUpd").val(objData.Prenom);
		$("#plTitreMsgUpd").html('Edition:&nbsp;' + objData.Prenom + ' ' + objData.Nom);
		$("#selTypeRHUpd").val(objData.idreshum).change();
		$("#edtCourrielUpd").val(objData.email);
		$("#edtTel1Upd").val(objData.tel1);
		$("#edtTel2Upd").val(objData.tel2);
		$("#edtCodeUsagerUpd").val(objData.login);
		if (objData.isdeleted === "1") {
			$("#chkDelete").prop('checked', true);
		}
		else {
			$("#chkdelete").prop('checked', false);
		}
		if (objData.DIM === "1") {
			$("#chkDIMUpd").prop('checked', true);
		}
		else {
			$("#chkDIMUpd").prop('checked', false);
		}
		if (objData.LUN === "1") {
			$("#chkLUNUpd").prop('checked', true);
		}
		else {
			$("#chkLUNUpd").prop('checked', false);
		}
		if (objData.MAR === "1") {
			$("#chkMARUpd").prop('checked', true);
		}
		else {
			$("#chkMARUpd").prop('checked', false);
		}
		if (objData.MER === "1") {
			$("#chkMERUpd").prop('checked', true);
		}
		else {
			$("#chkMERUpd").prop('checked', false);
		}
		if (objData.JEU === "1") {
			$("#chkJEUUpd").prop('checked', true);
		}
		else {
			$("#chkJEUUpd").prop('checked', false);
		}
		if (objData.VEN === "1") {
			$("#chkVENUpd").prop('checked', true);
		}
		else {
			$("#chkVENUpd").prop('checked', false);
		}
		if (objData.SAM === "1") {
			$("#chkSAMUpd").prop('checked', true);
		}
		else {
			$("#chkSAMUpd").prop('checked', false);
		}
		if (objData.isbroker === "1") {
			$("#chkBrokerUpd").prop('checked', true);
		}
		else {
			$("#chkBrokerUpd").prop('checked', false);
		}
		if (objData.havescan === "1") {
			$("#chkHaveScanUpd").prop('checked', true);
		}
		else {
			$("#chkHaveScanUpd").prop('checked', false);
		}
		$("#modalEditRH").modal("show");
	});

}
function SupprimerCam(nocamion) {
	var urlName = "/sanivan/setisdeletedcam/" + nocamion;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//----------------
	});
}
function sendCamionInfo() {
	var nocamion = $("#edtCam").val();
	var urlName = "";
	var isdel = 0;
	var auto = 0;
	var manuel = 0;
	var treuil = 0;
	var verseur = 0;
	if ($("#nocamiontoadd").val() != '') {
		if ($("#chkAutoAdd").prop('checked') == true) {
			auto = 1;
		}
		if ($("#chkManuelAdd").prop('checked') == true) {
			manuel = 1;
		}
		if ($("#chkTreuilAdd").prop('checked') == true) {
			treuil = 1;
		}
		if ($("#chkVerseurAdd").prop('checked') == true) {
			verseur = 1;
		}
		urlName = "/sanivan/sendcamioninfo/" + $("#selTypeCamion").val() + "/" + isdel + "/" + auto + "/" + manuel + "/" + treuil + "/" + verseur + "?camion=" + $("#nocamiontoadd").val();
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			//----------------
			document.location = '/sanivan/editcam/0';
		});
	}
	else {
		if ($("#chkDelete").prop('checked') == true) {
			isdel = 1;
		}
		if ($("#chkAuto").prop('checked') == true) {
			auto = 1;
		}
		if ($("#chkManuel").prop('checked') == true) {
			manuel = 1;
		}
		if ($("#chkTreuil").prop('checked') == true) {
			treuil = 1;
		}
		if ($("#chkVerseur").prop('checked') == true) {
			verseur = 1;
		}
		urlName = "/sanivan/sendcamioninfo/" + $("#selTypeCamUpd").val() + "/" + isdel + "/" + auto + "/" + manuel + "/" + treuil + "/" + verseur + "?camion=" + nocamion;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			//----------------
			$("#edtCam").val('');
			$("#plTitreMsgUpd").html('Edition du camion:&nbsp;');
			$("#chkdelete").prop('checked', false);
			$("#chkAuto").prop('checked', false);
			$("#chkManuel").prop('checked', false);
			$("#chkVerseur").prop('checked', false);
			$("#chkTreuil").prop('checked', false);
			//-------------------------------------
			$("#isdel_" + nocamion).html(((isdel == 1) ? 'Oui&nbsp;' : 'Non&nbsp;'));
			if (isdel == 1) {
				$('#isdel_' + nocamion).addClass("label label-danger");
			}
			else {
				$('#isdel_' + nocamion).removeClass("label label-danger");
			}
			$("#selTypeCamion_" + nocamion).html($("#selTypeCamUpd option:selected").text());
			$("#selTypeCamUpd").val('').change();
			$("#treuil_" + nocamion).html(((treuil == 1) ? 'Oui&nbsp;' : 'Non&nbsp;'));
			if (treuil == 1) {
				$('#treuil_' + nocamion).addClass("label label-success");
			}
			else {
				$('#treuil_').removeClass("label label-success");
			}
			$("#manuel_" + nocamion).html(((manuel == 1) ? 'Oui&nbsp;' : 'Non&nbsp;'));
			if (manuel == 1) {
				$('#manuel_' + nocamion).addClass("label label-success");
			}
			else {
				$('#manuel_').removeClass("label label-success");
			}
			$("#auto_" + nocamion).html(((auto == 1) ? 'Oui&nbsp;' : 'Non&nbsp;'));
			if (auto == 1) {
				$('#auto_' + nocamion).addClass("label label-success");
			}
			else {
				$('#auto_' + nocamion).removeClass("label label-success");
			}
			$("#verseur_" + nocamion).html(((verseur == 1) ? 'Oui&nbsp;' : 'Non&nbsp;'));
			if (verseur == 1) {
				$('#verseur_' + nocamion).addClass("label label-success");
			}
			else {
				$('#verseur_' + nocamion).removeClass("label label-success");
			}
		});
	}

}
function sendRHInfo() {
	var urlName = "";
	var isdel = 0;
	var dim = 0;
	var lun = 0;
	var mar = 0;
	var mer = 0;
	var jeu = 0;
	var ven = 0;
	var sam = 0;
	var isbroker = 0
	var havescan = 0;
	if ($("#edtID").val() == '0') {
		if ($("#chkDIMAdd").prop('checked') == true) {
			dim = 1;
		}
		if ($("#chkLUNAdd").prop('checked') == true) {
			lun = 1;
		}
		if ($("#chkMARAdd").prop('checked') == true) {
			mar = 1;
		}
		if ($("#chkMERAdd").prop('checked') == true) {
			mer = 1;
		}
		if ($("#chkJEUAdd").prop('checked') == true) {
			jeu = 1;
		}
		if ($("#chkVENAdd").prop('checked') == true) {
			ven = 1;
		}
		if ($("#chkSAMAdd").prop('checked') == true) {
			sam = 1;
		}
		if ($("#chkBrokerAdd").prop('checked') == true) {
			isbroker = 1;
		}
		if ($("#chkHaveScanAdd").prop('checked') == true) {
			havescan = 1;
		}
		var field = "&nom=" + $("#edtNomAdd").val() + "&prenom=" + $("#edtPrenomAdd").val() + "&typerh=" + $("#selTypeRHAdd").val() + "&tel1=" + $("#edtTel1Add").val() + "&tel2=" + $("#edtTel2Add").val() + '&havescan=' + havescan;

		urlName = "/sanivan/postrhinfo/" + $("#edtID").val() + "/" + isdel + "/" + dim + "/" + lun + "/" + mar + "/" + mer + "/" + jeu + "/" + ven + "/" + sam + "/" + isbroker +
			"?email=" + $("#edtCourrielAdd").val() + field;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			//----------------
			document.location = '/sanivan/showrh/0';
		});
	}
	else {
		if ($("#chkDIMUpd").prop('checked') == true) {
			dim = 1;
		}
		if ($("#chkLUNUpd").prop('checked') == true) {
			lun = 1;
		}
		if ($("#chkMARUpd").prop('checked') == true) {
			mar = 1;
		}
		if ($("#chkMERUpd").prop('checked') == true) {
			mer = 1;
		}
		if ($("#chkJEUUpd").prop('checked') == true) {
			jeu = 1;
		}
		if ($("#chkVENUpd").prop('checked') == true) {
			ven = 1;
		}
		if ($("#chkSAMUpd").prop('checked') == true) {
			sam = 1;
		}
		if ($("#chkBrokerUpd").prop('checked') == true) {
			isbroker = 1;
		}
		if ($("#chkHaveScanUpd").prop('checked') == true) {
			havescan = 1;
		}
		var field = "&nom=" + $("#edtNomUpd").val() + "&prenom=" + $("#edtPrenomUpd").val() + "&typerh=" + $("#selTypeRHUpd").val() +
			"&tel1=" + $("#edtTel1Upd").val() + "&tel2=" + $("#edtTel2Upd").val() + "&login=" + $("#edtCodeUsagerUpd").val() + "&pass=" + $("#edtpassUpd").val() + '&havescan=' + havescan;
		urlName = "/sanivan/postrhinfo/" + $("#edtID").val() + "/" + isdel + "/" + dim + "/" + lun + "/" + mar + "/" + mer + "/" + jeu + "/" + ven + "/" + sam + "/" + isbroker + "?email=" + $("#edtCourrielUpd").val() + field;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			//----------------
			var urlName = "/sanivan/showrh/" + Deleted + "?typerh=" + $("#selTypeRH").val() + '&valtofind=' + $("#edtFind").val() + '&from=1';
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				if (msg != '') {
					$("#tboProd").html(msg);
				}
			});
			
		});
	}

}
function SetTypeProd(id, dateactive) {

	var arrInfo = id.split('_');
	var urlName = "/sanivan/settypeprod/" + arrInfo[1] + "/" + $("#" + id).val() + "/" + dateactive;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#divCedule").html(objData.today);
		$("#spRearEnd").html(objData.spRearEnd);
		$("#spRobot").html(objData.nbRobot);
		$("#spTotalCam").html(objData.nbAllCam);
		if (objData.limitcedule === "1") {
			$("#chkRearLoad").prop('checked', true);
		}
		else if (objData.limitcedule === "2") {
			$("#chkRobot").prop('checked', true);
		}
	});
}
function sendSecteurInfo(id) {
	var urlName = "";
	var isdel = 0;
	if (id == '0') {
		var polylines = $('#selPolylinesAdd').val() || [];
		urlName = "/sanivan/postsecteurinfo/0/" + isdel + "?nom=" + $("#edtNomAdd").val() +
			"&idville=" + $("#selVilleAdd").val() +
			"&polylines=" + JSON.stringify(polylines) +
			'&valtofind=' + $("#edtFind").val() + '&from=1';
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			$("#tboProd").html(msg);
			// Réinitialiser les multiselects après le chargement
			initializeMultiselects();
		});
	}
	else {
		if ($("#chkDelete_" + id).prop('checked') == true) {
			isdel = 1;
		}
		var polylines = $('#selPolylines_' + id).val() || [];
		urlName = "/sanivan/postsecteurinfo/" + id + "/" + isdel +
			"?nom=" + $("#edtNom_" + id).val() +
			"&idville=" + $("#selVille_" + id).val() +
			"&polylines=" + JSON.stringify(polylines) +
			'&valtofind=' + $("#edtFind").val() + '&from=1';
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			$("#tboProd").html(msg);
			// Réinitialiser les multiselects après le chargement
			initializeMultiselects();
		});
	}
}
function initializeMultiselects() {
	$('.multiselect-polylines').each(function () {
		$(this).multiselect({
			includeSelectAllOption: true,
			selectAllText: 'Tout sélectionner',
			nonSelectedText: 'Aucune sélection',
			nSelectedText: ' sélectionnées',
			enableFiltering: true,
			filterPlaceholder: 'Rechercher',
			buttonWidth: '200px',
			maxHeight: 300
		});
	});

	$('.select-ville-secteur').off('change').on('change', function () {
		var secteurId = $(this).data('secteur-id');
		loadPolylinesByVille($(this).val(), 'selPolylines_' + secteurId);
	});
}
/**
 * Charge les polylines dans un select selon ville et secteur
 * @param {number} idville - ID de la ville
 * @param {number} idsecteur - ID du secteur
 * @param {string} selectId - ID du select à remplir
 * @param {number} selectedId - ID du polyline sélectionné (optionnel)
 */
function loadPolylinesBySecteurVille(idville, idsecteur, selectId, selectedId) {
	if (!idville || idville <= 0) {
		$('#' + selectId).html('<option value="0">-- Sélectionner une ville d\'abord --</option>');
		return;
	}

	var selectedIdParam = selectedId ? '&selectedId=' + selectedId : '';
	var idsecteurParam = idsecteur ? '&idsecteur=' + idsecteur : '';

	$.ajax({
		url: base_url + 'sanivan/getpolylinesbysecteurvville',
		type: 'GET',
		data: 'idville=' + idville + idsecteurParam + selectedIdParam,
		success: function (response) {
			$('#' + selectId).html(response);
		},
		error: function () {
			$('#' + selectId).html('<option value="0">-- Erreur chargement --</option>');
		}
	});
}
/**
 * Charge les polylines quand on change de secteur
 * @param {number} idcedule - ID de la cédule
 * @param {number} idsecteur - ID du secteur choisi
 * @param {number} idville - ID de la ville
 */
function loadPolylinesBySecteur(idcedule, idsecteur, idville) {
	// Réinitialiser le select de polyline
	var $polylineSelect = $('#selPolyline_' + idcedule);
	$polylineSelect.html('<option value="0">-- Chargement... --</option>');

	// Sauvegarder le secteur
	$.ajax({
		url: base_url + 'sanivan/setsecteurcedule',
		type: 'POST',
		data: {
			idcedule: idcedule,
			idsecteur: idsecteur
		},
		success: function (response) {
			if (response === 'OK') {
				// Charger les polylines du secteur
				$.ajax({
					url: base_url + 'sanivan/getpolylinesbysecteur',
					type: 'GET',
					data: {
						idville: idville,
						idsecteur: idsecteur,
						selectedId: 0
					},
					success: function (options) {
						$polylineSelect.html(options);
					},
					error: function () {
						$polylineSelect.html('<option value="0">-- Erreur --</option>');
					}
				});
			} else {
				alert('Erreur : ' + response);
			}
		},
		error: function () {
			alert('Erreur de communication');
		}
	});
}

/**
 * Enregistre la polyline sélectionnée
 * @param {number} idcedule - ID de la cédule
 */
function savePolylineCedule(idcedule) {
	var idpolyline = $('#selPolyline_' + idcedule).val();

	if (!idpolyline || idpolyline == 0) {
		return; // Pas de sélection
	}

	$.ajax({
		url: base_url + 'sanivan/setpolylinecedule',
		type: 'POST',
		data: {
			idcedule: idcedule,
			idpolyline: idpolyline
		},
		success: function (response) {
			if (response === 'OK') {
				// Feedback visuel
				$('#selPolyline_' + idcedule).addClass('bg-success');
				setTimeout(function () {
					$('#selPolyline_' + idcedule).removeClass('bg-success');
				}, 1000);
			} else {
				alert('Erreur : ' + response);
			}
		},
		error: function () {
			alert('Erreur de communication');
		}
	});
}
/**
 * Toggle une polyline (cocher/décocher)
 * @param {number} idcedule - ID de la cédule
 * @param {number} idpolyline - ID de la polyline
 * @param {boolean} isChecked - État de la checkbox
 */
function togglePolyline(idcedule, idpolyline, isChecked) {
	console.log('=== Toggle Polyline ===');
	console.log('idcedule:', idcedule);
	console.log('idpolyline:', idpolyline);
	console.log('isChecked:', isChecked);

	// Validation
	if (!idcedule || !idpolyline) {
		console.error('Paramètres manquants !');
		alert('Erreur : Paramètres manquants');
		return;
	}

	$.ajax({
		url: '/sanivan/togglepolylinecedule',
		type: 'POST',
		data: {
			idcedule: idcedule,
			idpolyline: idpolyline,
			isChecked: isChecked ? 'true' : 'false'
		},
		dataType: 'json',
		success: function (response) {
			console.log('Réponse serveur:', response);

			if (response.ok) {
				console.log('✅ Polyline mise à jour avec succès');

				// Marquer qu'une modification a eu lieu
				window.polylineChanged = true;

				// Recharger les polylines sélectionnées dans le modal
				loadSelectedPolylines(idcedule);

				// Afficher un message de succès
				showNotification('Parcours ' + (isChecked ? 'ajouté' : 'supprimé') + ' avec succès', 'success');

				// Fermer le modal puis recharger la page avec la date active
				var activeDate = (typeof ActiveDate !== 'undefined' && ActiveDate) ? ActiveDate : ($('#dteActivePage1').val() || '');
				console.log('[togglePolyline] reload avec date:', activeDate);
				try {
					sessionStorage.setItem('cedule_scroll', window.scrollY || 0);
					if (idcedule) sessionStorage.setItem('cedule_focus', idcedule);
				} catch (e) { /* ignore */ }
				// Fermer le modal d'abord, puis rediriger
				$('#modalPolyline').modal('hide');
				setTimeout(function () {
					if (activeDate) {
						window.location.href = '/sanivan/aujourdhui/1/' + activeDate;
					} else {
						window.location.reload();
					}
				}, 300);
			} else {
				console.error('❌ Erreur:', response.error);
				alert('Erreur : ' + (response.error || 'Erreur inconnue'));

				// Recharger les checkboxes pour revenir à l'état initial
				loadPolylinesBySecteurModal();
			}
		},
		error: function (xhr, status, error) {
			//console.error('=== Erreur AJAX ===');
			//console.error('Status HTTP:', xhr.status);
			//console.error('Status Text:', xhr.statusText);
			//console.error('Response:', xhr.responseText);
			//console.error('Error:', error);

			alert('Erreur lors de la mise à jour:\n' +
				'Status: ' + xhr.status + '\n' +
				'Message: ' + (xhr.responseText || error));

			// Recharger les checkboxes
			loadPolylinesBySecteurModal();
		}
	});
}
// Fonction helper pour afficher des notifications
function showNotification(message, type) {
	// Si vous avez une lib de notifications (toastr, sweetalert, etc.)
	// sinon, simple alert
	console.log('[' + type.toUpperCase() + '] ' + message);
}
/**
 * Charge les polylines déjà sélectionnées pour une cédule
 * @param {number} idcedule - ID de la cédule
 */
function loadSelectedPolylines(idcedule) {
	$.ajax({
		url: '/sanivan/getselectedpolylines',
		type: 'GET',
		data: { idcedule: idcedule },
		dataType: 'json',
		success: function (response) {
			console.log('Polylines sélectionnées:', response);

			if (response.ok && response.polylines.length > 0) {
				var html = '';
				$.each(response.polylines, function (i, poly) {
					// poly.id = association id (cedule_polylines.id), poly.nom
					var polylineName = $('<div>').text(poly.nom || 'Parcours').html();
					html += '<div class="selected-polyline-row" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;border-bottom:1px solid #eee;padding-bottom:6px;">'
						+ '  <div class="polyline-name" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-right:8px;">'
						+ '<i class="fa fa-map-marker" aria-hidden="true" style="color:#4a90e2;margin-right:6px;"></i>'
						+ '<span class="polyline-clickable" onclick="showPolylineSingle(' + poly.id + ', \'' + (poly.nom || 'Parcours').replace(/'/g, "\\'") + '\', ' + poly.idville + ')" style="cursor:pointer;color:#4a90e2;text-decoration:underline;">' + polylineName + '</span>'
						+ '  </div>'
						+ '  <div class="polyline-actions" style="margin-left:8px;display:flex;gap:6px;flex-shrink:0;">'
						+ '<button type="button" class="btn btn-sm btn-warning" onclick="movePolylineFirst(' + poly.assoc_id + ')" title="Placer en premier">'
						+ '<i class="fa fa-arrow-up"></i>'
						+ '</button>'
						+ '<button type="button" class="btn btn-sm btn-danger" onclick="removePolyline(' + poly.assoc_id + ')" title="Supprimer">'
						+ '<i class="fa fa-times"></i>'
						+ '</button>'
						+ '  </div>'
						+ '</div>';
				});
				$('#selectedPolylinesList').html(html);
			} else {
				$('#selectedPolylinesList').html('<span class="text-muted">Aucun parcours sélectionné</span>');
			}
		},
		error: function (xhr, status, error) {
			console.error('Erreur chargement polylines sélectionnées:', error);
			$('#selectedPolylinesList').html('<span class="text-danger">Erreur chargement parcours</span>');
		}
	});
}
function movePolylineFirst(assocId) {
	if (!confirm('Placer ce parcours en premier pour cette cédule ?')) return;
	$.ajax({
		url: '/sanivan/movepolylinefirst',
		type: 'POST',
		data: { id: assocId },
		dataType: 'json'
	}).done(function (res) {
		if (res && res.ok) {
			// Indique qu'une modification a eu lieu (sera prise en compte à la fermeture du modal)
			window.polylineChanged = true;
			// Fermer le modal : le handler 'hidden.bs.modal' fera le reload en gardant la position
			$('#modalPolyline').modal('hide');
		} else {
			alert('Erreur repositionnement : ' + (res && res.error ? res.error : 'erreur serveur'));
		}
	}).fail(function () {
		alert('Erreur de communication');
	});
}

$(function () {
	// Restauration du scroll + mise en évidence de la ligne si nécessaire
	var savedScroll = sessionStorage.getItem('cedule_scroll');
	var savedFocus = sessionStorage.getItem('cedule_focus');

	if (savedScroll !== null) {
		// petit délai pour laisser le layout se stabiliser avant de scroller
		setTimeout(function () {
			try {
				window.scrollTo(0, parseInt(savedScroll, 10) || 0);
			} catch (e) { /* ignore */ }
			// si on a un id de cédule, essayer de surligner sa ligne
			if (savedFocus) {
				var $tr = $('#tr_' + savedFocus);
				if ($tr.length) {
					$tr.css('transition', 'background-color 0.6s');
					$tr.css('background-color', '#fffa8c'); // surlignage
					setTimeout(function () { $tr.css('background-color', ''); }, 1200);
				}
			}
			// cleanup
			sessionStorage.removeItem('cedule_scroll');
			sessionStorage.removeItem('cedule_focus');
		}, 80);
	}

	// Note : le handler 'hidden.bs.modal' pour le reload est défini plus bas
	// (ligne ~6426) et gère déjà la redirection avec la bonne date active.
	// Ne pas ajouter de handler supplémentaire ici pour éviter un conflit
	// qui ferait un location.reload() vers la mauvaise date.
});
/**
 * Supprime une polyline
 * @param {number} id - ID de l'association cedule_polylines
 */
function removePolyline(id) {
	if (!confirm('Supprimer ce parcours ?')) {
		return;
	}

	var idcedule = $('#modalPolylineIdCedule').val();
   var urlName = base_url + 'sanivan/removepolyline/' + id;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
       if (objData.ok || objData.result === 'ok') {
			window.polylineChanged = true;
			loadSelectedPolylines(idcedule);
			loadPolylinesBySecteurModal(); // Rafraîchir les checkboxes
			// Fermer le modal puis recharger avec la date active
			var activeDate = (typeof ActiveDate !== 'undefined' && ActiveDate) ? ActiveDate : ($('#dteActivePage1').val() || '');
			try {
				sessionStorage.setItem('cedule_scroll', window.scrollY || 0);
				if (idcedule) sessionStorage.setItem('cedule_focus', idcedule);
			} catch (e) { /* ignore */ }
			$('#modalPolyline').modal('hide');
			setTimeout(function () {
				if (activeDate) {
					window.location.href = '/sanivan/aujourdhui/1/' + activeDate;
				} else {
					window.location.reload();
				}
			}, 300);
		} else {
			alert('Erreur lors de la suppression');
		}
	});
}
/**
 * Supprime toutes les polylines d'une cédule
 */
function clearAllPolylines() {
	if (!confirm('Supprimer tous les parcours de cette cédule ?')) {
		return;
	}

	var idcedule = $('#modalPolylineIdCedule').val();

	$.ajax({
		url: base_url + 'sanivan/clearAllPolylines',
		type: 'POST',
		data: { idcedule: idcedule },
		dataType: 'json',
		success: function (response) {
			if (response.ok) {
				window.polylineChanged = true;
				loadSelectedPolylines(idcedule);
				loadPolylinesBySecteurModal(); // Rafraîchir les checkboxes
				alert('Tous les parcours ont été supprimés');
				// Fermer le modal puis recharger avec la date active
				var activeDate = (typeof ActiveDate !== 'undefined' && ActiveDate) ? ActiveDate : ($('#dteActivePage1').val() || '');
				try {
					sessionStorage.setItem('cedule_scroll', window.scrollY || 0);
					if (idcedule) sessionStorage.setItem('cedule_focus', idcedule);
				} catch (e) { /* ignore */ }
				$('#modalPolyline').modal('hide');
				setTimeout(function () {
					if (activeDate) {
						window.location.href = '/sanivan/aujourdhui/1/' + activeDate;
					} else {
						window.location.reload();
					}
				}, 300);
			} else {
				alert('Erreur lors de la suppression');
			}
		},
		error: function (xhr, status, error) {
			console.error('Erreur suppression:', error);
			alert('Erreur lors de la suppression');
		}
	});
}
/**
 * Charge les checkboxes de polylines pour un secteur
 * @param {number} idcedule
 * @param {number} idsecteur
 * @param {number} idville
 */
function loadPolylineCheckboxes(idcedule, idsecteur, idville) {
	var $container = $('#polylineCheckboxContainer');
	$container.html('<div class="text-muted">Chargement...</div>');

	$.ajax({
		url: base_url + 'sanivan/getpolylinecheckboxes',
		type: 'GET',
		data: {
			idcedule: idcedule,
			idsecteur: idsecteur,
			idville: idville
		},
		success: function (html) {
			$container.html(html);
		},
		error: function () {
			$container.html('<div class="text-danger">Erreur de chargement</div>');
		}
	});
}
/**
 * Gère le changement de secteur pour une cédule
 * @param {number} idcedule - ID de la cédule
 * @param {number} idville - ID de la ville
 * @param {number} idsecteur - ID du secteur
 */
function onSecteurChangeCedule(idcedule, idville, idsecteur) {
	var selectId = 'selPolyline_' + idcedule;
	loadPolylinesBySecteurVille(idville, idsecteur, selectId, 0);
}


/**
 * Enregistre le polyline sélectionné pour une cédule
 * @param {number} idcedule - ID de la cédule
 */
function savePolylineCedule(idcedule) {
	var idpolyline = $('#selPolyline_' + idcedule).val();

	if (!idpolyline || idpolyline == 0) {
		alert('Veuillez sélectionner un parcours');
		return;
	}

	$.ajax({
		url: base_url + 'sanivan/setpolylinecedule',
		type: 'POST',
		data: {
			idcedule: idcedule,
			idpolyline: idpolyline
		},
		success: function (response) {
			if (response === 'OK') {
				// Message de succès discret via feedback visuel
				$('#selPolyline_' + idcedule).addClass('bg-success');
				setTimeout(function () {
					$('#selPolyline_' + idcedule).removeClass('bg-success');
				}, 1000);
			} else {
				alert('Erreur lors de l\'enregistrement : ' + response);
			}
		},
		error: function () {
			alert('Erreur de communication avec le serveur');
		}
	});
}
/**
 * Affiche les polylines d'un secteur sur une carte
 * @param {number} idsecteur - ID du secteur
 * @param {string} secteurName - Nom du secteur
 * @param {number} idville - ID de la ville
 * @param {number} idcedule - ID de la cdule
 */
function showPolylineMap(idsecteur, secteurName, idville, idcedule) {

	// Vérifier si le modal existe
	if ($('#modalMapPolylines').length === 0) {
		alert('Le modal n\'existe pas dans la page. Vérifiez secteurs.php');
		return;
	}

	// Mettre à jour le titre du modal
	$('#spSecteurName').text(secteurName);

	// Initialiser la carte si première ouverture
	if (!polylineMapInstance) {
		polylineMapInstance = L.map('polylineMap').setView([46.8, -71.2], 10);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '© OpenStreetMap contributors',
			maxZoom: 19
		}).addTo(polylineMapInstance);
	}

	// Afficher le modal
	$('#modalMapPolylines').modal('show');

	// Recharger la taille de la carte après l'affichage puis charger les polylines
	setTimeout(function () {
		if (polylineMapInstance) {
			polylineMapInstance.invalidateSize();
			// Si un idcedule est fourni (non null/undefined et > 0), on charge les polylines associées à la cédule
			if (typeof idcedule !== 'undefined' && idcedule !== null && parseInt(idcedule) > 0) {
				loadPolylinesForSecteurCedule(idsecteur, idcedule);
			} else {
				// comportement historique : toutes les polylines du secteur
				loadPolylinesOnMap(idsecteur);
			}
		}
	}, 300);
}
// Remplacez l'ancienne showPolylineSingle par cette implémentation robuste
function ensureLeafletLoaded() {
	return new Promise(function (resolve, reject) {
		if (window.L) return resolve();
		// CSS
		if (!document.getElementById('leaflet-css')) {
			var lcss = document.createElement('link');
			lcss.id = 'leaflet-css';
			lcss.rel = 'stylesheet';
			lcss.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
			document.head.appendChild(lcss);
		}
		// JS
		if (document.getElementById('leaflet-js')) {
			var existing = document.getElementById('leaflet-js');
			if (existing.complete || existing.readyState === 'loaded' || existing.readyState === 'complete') {
				// déjà chargé ou en cours, attendre une courte durée
				setTimeout(function () { (window.L ? resolve() : reject(new Error('Leaflet non disponible'))); }, 50);
			} else {
				existing.addEventListener('load', function () { resolve(); });
				existing.addEventListener('error', function () { reject(new Error('Erreur chargement leaflet.js')); });
			}
			return;
		}
		var s = document.createElement('script');
		s.id = 'leaflet-js';
		s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
		s.async = true;
		s.onload = function () { resolve(); };
		s.onerror = function () { reject(new Error('Erreur chargement leaflet.js')); };
		document.body.appendChild(s);
	});
}

function showPolylineSingle(idpolyline, nom, idville) {
	// fallback modal/map selectors — on conserve le comportement existant
	var modalSelector = '#modalMapPolylinesCedule';
	var mapDivId = 'polylineMapCedule';
	if ($(modalSelector).length === 0) {
		modalSelector = '#modalMapPolylines';
		mapDivId = 'polylineMap';
	}

	if ($(modalSelector).length === 0) {
		alert('Le modal de visualisation des parcours est introuvable.');
		return;
	}

	$('#spSecteurNameCedule, #spSecteurName').text(nom || 'Parcours');

	// Charger Leaflet si nécessaire puis afficher la polyline
	ensureLeafletLoaded().then(function () {
		// Choisir ou initialiser l'instance de carte
		var mapInstanceName = (mapDivId === 'polylineMapCedule') ? 'polylineMapCedule' : 'polylineMapInstance';
		if (!window[mapInstanceName]) {
			try {
				window[mapInstanceName] = L.map(mapDivId).setView([46.8, -71.2], 11);
				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: '© OpenStreetMap contributors',
					maxZoom: 19
				}).addTo(window[mapInstanceName]);
			} catch (e) {
				console.error('Impossible d\'initialiser la carte Leaflet', e);
				alert('Erreur initialisation carte.');
				return;
			}
		}

		var mapInstance = window[mapInstanceName];
		var layerHolderName = (mapDivId === 'polylineMapCedule') ? '_cedulePolylineLayer' : '_polylineMapCeduleLayer';

		$(modalSelector).modal('show');

		setTimeout(function () {
			try { mapInstance.invalidateSize(); } catch (e) { /* ignore */ }

			// Retirer TOUTES les anciennes couches (polylines et layer groups)
			// pour éviter que les parcours précédents restent affichés
			try {
				if (window[layerHolderName]) {
					mapInstance.removeLayer(window[layerHolderName]);
					window[layerHolderName] = null;
				}
			} catch (e) { /* ignore */ }
			// Nettoyer aussi le layer group utilisé par showPolylineMapFromCedule
			try {
				if (typeof polylineLayersGroupCedule !== 'undefined' && polylineLayersGroupCedule) {
					polylineLayersGroupCedule.clearLayers();
				}
			} catch (e) { /* ignore */ }
			// Nettoyer toutes les polylines restantes sur la carte
			try {
				mapInstance.eachLayer(function (layer) {
					if (layer instanceof L.Polyline || layer instanceof L.LayerGroup) {
						mapInstance.removeLayer(layer);
					}
				});
			} catch (e) { /* ignore */ }

			// Charger la polyline via l'endpoint serveur
			$.ajax({
				url: base_url + 'sanivan/getpolylinegeojsonbyid/' + encodeURIComponent(idpolyline),
				type: 'GET',
				dataType: 'json',
				success: function (resp) {
					if (!resp || !resp.ok || !resp.polyline || !resp.polyline.coords || resp.polyline.coords.length === 0) {
						alert('Aucun parcours disponible pour cet ID.');
						return;
					}
					var coords = resp.polyline.coords.map(function (c) { return [c[0], c[1]]; });
					window[layerHolderName] = L.polyline(coords, { color: '#4a90e2', weight: 5 }).addTo(mapInstance);
					var bounds = window[layerHolderName].getBounds();
					if (bounds && bounds.isValid()) {
						mapInstance.fitBounds(bounds, { padding: [20, 20] });
					} else {
						mapInstance.setView(coords[0], 13);
					}
				},
				error: function () {
					alert('Erreur lors du chargement du parcours.');
				}
			});
		}, 200);
	}).catch(function (err) {
		console.error('Erreur chargement Leaflet:', err);
		alert('Impossible de charger la librairie de carte (Leaflet).');
	});
}
/**
 * Charge les polylines depuis le serveur et les affiche sur la carte
 * @param {number} idsecteur - ID du secteur
 */
function loadPolylinesOnMap(idsecteur) {
	// Nettoyer les anciennes couches (sauf les tuiles)
	polylineMapInstance.eachLayer(function (layer) {
		if (layer instanceof L.Polyline) {
			polylineMapInstance.removeLayer(layer);
		}
	});

	// Afficher un loader
	var $mapDiv = $('#polylineMap');
	$mapDiv.append('<div id="mapLoader" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(255,255,255,0.9);padding:20px;border-radius:8px;z-index:1000;"><i class="fa fa-spinner fa-spin fa-3x"></i><br/>Chargement...</div>');
	
	$.ajax({
		url: '/sanivan/getpolylinesgeojson/' + idsecteur,
		type: 'GET',
		dataType: 'json',
		success: function (response) {
			$('#mapLoader').remove();

			if (!response || !response.ok) {
				alert('Erreur lors du chargement des polylines : ' + (response.error || 'Erreur inconnue'));
				return;
			}

			if (!response.polylines || response.polylines.length === 0) {
				alert('Aucun parcours trouvé pour ce secteur.');
				return;
			}

			var bounds = L.latLngBounds();
			var colors = ['#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'];

			response.polylines.forEach(function (poly, index) {
				var color = colors[index % colors.length];

				// Créer le polyline
				var polyline = L.polyline(poly.coords, {
					color: color,
					weight: 4,
					opacity: 0.8
				}).addTo(polylineMapInstance);

				// Ajouter un popup avec les infos
				polyline.bindPopup(
					'<b>' + poly.nom + '</b><br/>' +
					'Ville: ' + (poly.ville || 'N/A') + '<br/>' +
					'Points: ' + poly.coords.length
				);

				// Étendre les bounds
				bounds.extend(polyline.getBounds());
			});

			// Ajuster la vue pour afficher tous les polylines
			if (bounds.isValid()) {
				polylineMapInstance.fitBounds(bounds, { padding: [50, 50] });
			}
		},
		error: function (xhr, status, error) {
			$('#mapLoader').remove();
			console.error('Erreur AJAX:', status, error);
			alert('Erreur de communication avec le serveur.');
		}
	});
}

// Nettoyer la carte lors de la fermeture du modal (UNE SEULE FOIS au chargement)
$(document).ready(function () {
	$('#modalMapPolylines').on('hidden.bs.modal', function () {
		if (polylineMapInstance) {
			polylineMapInstance.eachLayer(function (layer) {
				if (layer instanceof L.Polyline) {
					polylineMapInstance.removeLayer(layer);
				}
			});
		}
	});
});
function sendVilleInfo(id) {
	var urlName = "";
	var couleur = $("#edtColorAdd").val();
	couleur = couleur.substr(1, 6);
	var isdel = 0;
	if (id == '0')  {
		urlName = "/sanivan/postvilleinfo/0/" + isdel + "?nom=" + $("#edtNomAdd").val() + "&couleur=" + couleur +
			'&valtofind=' + $("#edtFind").val() + '&from=1&sendinfo=' + $("#selSendAdd").val();
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			//----------------
			$("#edtNomAdd").val('');
			$("#edtColorAdd").val('');
			$("#tboProd").html(msg);
			ShowHideNika('divAddVille');
		});
	}
	else {
		if ($("#chkDelete_" + id).prop('checked') == true) {
			isdel = 1;
		}
		var couleur = $("#selColor_" + id).val();
		couleur = couleur.substring(1, couleur.length);
		urlName = "/sanivan/postvilleinfo/" + id + "/" + isdel + "?nom=" + $("#edtNom_" + id).val() + "&deplacement=" + $("#edtDeplacement_" + id).val() + "&couleur=" + couleur + '&valtofind=' + $("#edtFind").val() + '&from=1';
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			//----------------
			$("#tboProd").html(msg);
		});
	}

}
function ShowHideNika(id) {
	if ($('#' + id).css('display') !== 'none')
		$('#' + id).hide();
	else
		$('#' + id).show();
}
function ShowHideDrop(id) {
	if (id == 'chkCamDrop') {
		if ($("#chkCamDrop").is(':checked')) {
			$('#divcam').show();
			$('#divRH').hide();
			$("#chkRHDrop").prop('checked', false);
			$("#chkShowConge").prop('checked', false);
		}
		else {
			$('#divcam').hide();
    }
  }
	else if (id == 'chkRHDrop') {
		if ($("#chkRHDrop").is(':checked')) {
			$('#divRH').show();
			$('#divcam').hide();
			$("#chkCamDrop").prop('checked', false);
			activeRH = (($("#chkShowConge").is(':checked')) ? 0 : 1);
			var urlName = '/sanivan/rhtodrop/' + $("#typechauf").val() + '/' + $("#dteActivePage1").val() + '?action=' + activeRH;
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				$("#divDropRH").html(msg);
			});
    }
		else {
			$('#divRH').hide();
    }		

  }

}
function ShowHideVilleSecteur(value) {
	if (value == '1') {
		$('#divDropSecteur').hide();
		$('#divDropVille').show();
  }
	else {
		$('#divDropSecteur').show();
		$('#divDropVille').hide();
  }

}
function ShowDictionnaire(id) {
	//alert(id);
	for (iter = 1; iter < 13; iter++) {
		$('#diction_' + iter).removeClass("active-diction");
		$('#diction_' + iter).removeClass("diction");
		$('#diction_' + iter).addClass("diction");
	}
	$('#diction_' + id).removeClass("diction");
	$('#diction_' + id).addClass("active-diction");
	//$('#diction_' + id).blur();
	if (id == 10) {
		$('#btnShowHide').hide();
	}
	else {
		$('#btnShowHide').show();
    }
	urlName = "/sanivan/dictionnaires/" + id + "/2";
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//----------------
		$('#divDiction').html(msg);
	});
	urlName = "/sanivan/dictionnaires/" + id + "/3";
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//----------------
		$('#divToAdd').html(msg);
	});
}

function postDiction(id, notype) {
	var urlName = '';
	var isdel = 0;
	
	if (id > 0) {
		if ($("#chkDelete_" + id).prop('checked') == true) {
			isdel = 1;
		}
	}
	switch (notype) {
		case 1:
		case 2:
		case 5:
		case 6:
		case 8:
			var urlName = "/sanivan/postdictionnaire/" + isdel + "?id=" + id + "&nom=" + $('#edtNom_' + id).val() + "&notype=" + notype;
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				//----------------
				$("#isdel_" + id).html(((isdel == 1) ? 'Oui' : 'Non'));
				if (isdel == 1) {
					$('#isdel_' + id).addClass("label label-danger");
				}
				else {
					$('#isdel_' + id).removeClass("label label-danger");
				}
			});
			break;

		case 3:
		case 4:
			var couleur = $('#selColor_' + id).val();
			couleur = couleur.substring(1, couleur.length);
			var textcolor = $('#selTextColor_' + id).val();
			textcolor = textcolor.substring(1, textcolor.length);
			var urlName = "/sanivan/postdictionnaire/" + isdel + "?id=" + id + "&nom=" + $('#edtNom_' + id).val() + "&notype=" + notype +
				"&couleur=" + couleur + "&textcolor=" + textcolor;
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				//----------------
				$("#isdel_" + id).html(((isdel == 1) ? 'Oui' : 'Non'));
				if (isdel == 1) {
					$('#isdel_' + id).addClass("label label-danger");
				}
				else {
					$('#isdel_' + id).removeClass("label label-danger");
				}
			});
			break;

		case 7:
			var issanivan = 0;
			if ($("#chkSanivan_" + id).prop('checked') == true) {
				issanivan = 1;
			}
			var urlName = "/sanivan/postdictionnaire/" + isdel + "?id=" + id + "&nom=" + $('#edtNom_' + id).val() + "&notype=" + notype +
				"&typedepot=" + $('#edtTypeDepot_' + id).val() + "&prix=" + $('#edtPrix_' + id).val() +
				"&redevance=" + $('#edtRedevance_' + id).val() + "&adresse=" + $('#edtAdresse_' + id).val() +
				"&prix_sani=" + $('#edtPrixSani_' + id).val() + "&issanivan=" + issanivan;
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				//----------------
				$("#isdel_" + id).html(((isdel == 1) ? 'Oui' : 'Non'));
				if (isdel == 1) {
					$('#isdel_' + id).addClass("label label-danger");
				}
				else {
					$('#isdel_' + id).removeClass("label label-danger");
				}
				$("#issanivan_" + id).html(((issanivan == 1) ? 'Oui' : 'Non'));
				if (isdel == 1) {
					$('#issanivan_' + id).addClass("label label-danger");
				}
				else {
					$('#issanivan_' + id).removeClass("label label-danger");
				}
			});
			break;
		case 9:
			var typeinfo = $('#selTypeprix_' + id).val();
			alert(typeinfo);
			var urlName = "/sanivan/postdictionnaire/" + isdel + "?id=" + id + "&typeitem=" + $('#selTypeprix_' + id).val() + "&notype=" + notype +
				"&datestart=" + $('#dteStart_' + id).val() + "&prix=" + $('#edtPrix_' + id).val() +
				"&dateend=" + $('#dteFin_' + id).val();
			alert(urlName);
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				//----------------
				$("#isdel_" + id).html(((isdel == 1) ? 'Oui' : 'Non'));
				if (isdel == 1) {
					$('#isdel_' + id).addClass("label label-danger");
				}
				else {
					$('#isdel_' + id).removeClass("label label-danger");
				}
			});
			break;
		case 10:
			var urlName = "/sanivan/postcontenant/" + isdel + "?id=" + id + "&nom=" + $('#edtNom_' + id).val() + "&notype=" + notype +
				"&redevance=" + $('#edtRedevance_' + id).val() + "&prix=" + $('#edtPrix_' + id).val() + "&tonnage=" + $('#edtTonnage_' + id).val() +
				"&carburant=" + $('#edtCarburant_' + id).val() + "&carburant_site=" + $('#edtCarburant_site_' + id).val()
				+ "&demolition=" + $('#edtDemolition_' + id).val() + "&asphalte=" + $('#edtAsphalte_' + id).val()
				+ "&beton=" + $('#edtBeton_' + id).val() + "&beton_arme=" + $('#edtBeton_arme_' + id).val();
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				//----------------
				$("#isdel_" + id).html(((isdel == 1) ? 'Oui' : 'Non'));
				if (isdel == 1) {
					$('#isdel_' + id).addClass("label label-danger");
				}
				else {
					$('#isdel_' + id).removeClass("label label-danger");
				}
			});
			break;
		case 11:
			var couleur = $('#selColor_' + id).val();
			couleur = couleur.substring(1, couleur.length);
			var urlName = "/sanivan/postdictStatutRequete/" + isdel + "?id=" + id + "&description=" + $('#edtNom_' + id).val() + "&notype=" + notype +
				"&couleur=" + couleur;
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				//----------------
				$("#isdel_" + id).html(((isdel == 1) ? 'Oui' : 'Non'));
				if (isdel == 1) {
					$('#isdel_' + id).addClass("label label-danger");
				}
				else {
					$('#isdel_' + id).removeClass("label label-danger");
				}
			});
			break;
	}
}

function addToDiction(notype) {
	var urlName = '';
	var isdel = 0;
	switch (notype) {
		case 1:
		case 2:
		case 3:
		case 4:
		case 5:
		case 6:
		case 8:
		case 12:
			var urlName = "/sanivan/postdictionnaire/" + isdel + "?id=0&nom=" + $('#edtNomAdd').val() + "&notype=" + notype;
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				//----------------
				document.location = "/sanivan/dictionnaires/" + notype + "/1";
			});
			break;
		case 7:
			var urlName = "/sanivan/postdictionnaire/" + isdel + "?id=0&nom=" + $('#edtNomAdd').val() + "&notype=" + notype +
				"&typedepot=" + $('#edtTypeDepotAdd').val() + "&prix=" + $('#edtPrixAdd').val() +
				"&redevance=" + $('#edtRedevanceAdd').val() + "&adresse=" + $('#edtAdresseAdd').val();
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				//----------------
				document.location = "/sanivan/dictionnaires/" + notype + "/1";
			});
			break;
		case 9:
			var urlName = "/sanivan/postdictionnaire/" + isdel + "?id=0&typeitem=" + $('#edtNomAdd').val() + "&notype=" + notype +
				"&datestart=" + $('#dteStartAdd').val() + "&prix=" + $('#edtPrixAdd').val() +
				"&dateend=" + $('#dteFinAdd').val() + "&code=" + $('#edtCodeAdd').val();
			$.ajax({
				async: true,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				//----------------
				document.location = "/sanivan/dictionnaires/" + notype + "/1";
			});
			break;
	}

}
function LoadTodayRoolOFF(nbjour) {
	var urlName = "/rolloff/getdate/" + nbjour + "/" + $('#dteActive').val();
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var newdate = '';
		if (msg !== "") {
			newdate = msg;
		}
		document.location = "/rolloff/aujourdhui/1/" + newdate;
	});
}
//------------------------------------------------------------------------------
function getStatutCommande() {
	var urlName = "/rolloff/getstatut/" + $('#dteActive').val();
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			var objData = JSON.parse(msg);
			$("#badge_1").html(objData.NotSend);
			$("#badge_2").html(objData.InRoad);
			$("#badge_3").html(objData.IsComplete);
			$("#badge_4").html(objData.AllCom);
		}
	});
}
//------------------------------------------------------------------------------
function AfficherRollOff(show) {
	var urlName = "/rolloff/showsection?find=" + $('#edtFind').val() + "&dateactive=" + $('#dteActive').val() +
		"&show=" + show + "&endhide=" + EndIsChecked;
	$("#show_1").css("background-color", "");
	$("#show_2").css("background-color", "");
	$("#show_3").css("background-color", "");
	$("#show_4").css("background-color", "");
	$("#show_" + show).css("background-color", "indianred");
	ToShow = show;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			$("#tboRO").html(msg);
			$("#divMsgChauffeur").hide();
		}
	});
	getStatutCommande();
	myTimer = setTimeout("_updRollOffView()", 1 * 60 * 1000);
}

//------------------------------------------------------------------------------
function searchTable() {
	var urlName = "/rolloff/recherche/" + $('#edtFind').val() + "/" + $('#dteActive').val() + "?show=" + ToShow + "&endhide=" + EndIsChecked;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			$("#tboRO").html(msg);
			$("#divMsgChauffeur").hide();
		}
	});
	getStatutCommande();
	myTimer = setTimeout("_updRollOffView()", 1 * 60 * 1000);
}
//------------------------------------------------------------------------------
function ClearsearchTable() {
	$('#edtFind').val('');
	var urlName = "/rolloff/effacer_recherche/" + $('#dteActive').val() + "?show=" + ToShow + "&endhide=" + EndIsChecked;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			$("#tboRO").html(msg);
			$("#divMsgChauffeur").hide();
		}
	});
	getStatutCommande();
	myTimer = setTimeout("_updRollOffView()", 1 * 60 * 1000);
}
//------------------------------------------------------------------------------
function ReturnsearchTable(value, e) {

	var keycode = e.keyCode ? e.keyCode : e.charCode;

	if (keycode == 13) {
		$("*").css("cursor", "progress");
		var optsel = $("#cbFields").val();
		var urlName = "/rolloff/recherche/" + value + "/" + $('#dteActive').val() + "?show=" + ToShow + "&endhide=" + EndIsChecked;
		$.ajax({
			type: "POST",
			url: urlName
		}).done(function (msg) {

			if (msg != "") {
				$("#tboRO").html(msg);
				$("#divMsgChauffeur").hide();
			}
			$("*").css("cursor", "auto");
		});
	}
	getStatutCommande();
	myTimer = setTimeout("_updRollOffView()", 1 * 60 * 1000);
}
//-------------------------------------------------------------------------------
function ToogleTerminee() {
	if ($("#chkHideEnd").is(':checked')) {
		EndIsChecked = 1;
		$("#HideEnd").css("background-color", "darkred");
		$("#HideEnd").css("color", "white");
	}
	else  {
		EndIsChecked = 0;
		$("#HideEnd").css("background-color", "white");
		$("#HideEnd").css("color", "darkblue");
	}
	var urlName = "/rolloff/aujourdhui/2/" + $('#dteActive').val() + "?show=" + ToShow + "&endhide=" + EndIsChecked;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			$("#tboRO").html(msg);
			$("#divMsgChauffeur").hide();
		}
	});
	myTimer = setTimeout("_updRollOffView()", 1 * 60 * 1000);
}
//-------------------------------------------------------------------------------
function ShowActifChauffeur(value,badge,nomcomplet) {
	var urlName = "/rolloff/showchauffeur/" + value + "/" + $('#dteActive').val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			var titre = 'Affichage seulement des <span class="badge badge-danger" style="background-color:green;color:white;font-size:large;font-weight:bold;">' +
				badge + '</span> commandes  de ' + nomcomplet;
			$("#tboRO").html(objData.today);
			$("#msgChauffeur").html(titre);
			$("#divMsgChauffeur").show();
			//clearTimeout(myTimer);
		}
	});
}
//-------------------------------------------------------------------------------
function LimitActifChauffeur(value,badge,nomcomplet) {
	var urlName = "/rolloff/showchauffeur/" + value + "/" + $('#dteActive').val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			var titre = 'Affichage seulement des <span class="badge badge-danger" style="background-color:green;color:white;font-size:large;font-weight:bold;">' +
				badge + '</span> commandes  de ' + nomcomplet;
			$("#tboRO").html(objData.today);
			$("#msgChauffeur").html(titre);
			$("#divMsgChauffeur").show();
			//clearTimeout(myTimer);
		}
	});
}
//-------------------------------------------------------------------------------
function SetChauffeursFromType(value){
	var urlName = "/rolloff/setchauffeur/" + value ;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			$("#selRollOffRH").html(msg);
		}
	});
}
//-------------------------------------------------------------------------------
function ShowChauffeurInGoogle(id) {
	//var urlName = "/rolloff/setchauffeur/" + value;
	//$.ajax({
	//	async: false,
	//	type: "POST",
	//	url: urlName
	//}).done(function (msg) {
	//	if (msg !== "") {
	//		$("#selRollOffRH").html(msg);
	//	}
	//});
	var urlName = 'https://www.google.com/maps/search/?api=1&query=45.39943717978386,-74.07804732694905&key=AIzaSyBe729IJy8b9F55IVM3zz4P2bpTM1zFgE4';

	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
}
//-------------------------------------------------------------------------------
function ShowInGoogle(emplacement) {
	var urlName = 'https://www.google.com/maps/dir/?api=1&dir_action=navigate&destination=' + emplacement + '&travelmode=driving&key=AIzaSyBe729IJy8b9F55IVM3zz4P2bpTM1zFgE4';

	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
}
function ShowInGoogleNew(emplacement) {
	var urlName = 'https://www.google.com/maps/dir/?api=1&dir_action=navigate&destination=' + emplacement + '&travelmode=driving&key=AIzaSyBe729IJy8b9F55IVM3zz4P2bpTM1zFgE4';
	document.location = urlName;
}
//------------------------------------------------------------------------------
function ChangeRODate(value, idcommande) {

	BootstrapDialog.confirm({
		title: 'ATTENTION',
		message: 'Attention! Êtes-vous certain de vouloir transférer la cédule au ' + value + '? ',
		type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
		closable: true, // <-- Default value is false
		draggable: true, // <-- Default value is false
		btnCancelLabel: 'Annuler', // <-- Default value is 'Cancel',
		btnOKLabel: 'Exécuter', // <-- Default value is 'OK',
		btnOKClass: 'btn-warning', // <-- If you didn't specify it, dialog type will be used,
		callback: function (result) {
			// result will be true if button was click, while it will be false if users close the dialog directly.
			if (result) {
				var urlName = "/rolloff/changedate/" + idcommande + '/' + value + '/' +
					(($('#edtFind').val() == '') ? 'none' : $('#edtFind').val()) + "/" + $('#dteActive').val();
				$.ajax({
					async: true,
					type: "POST",
					url: urlName
				}).done(function (msg) {
					if (msg != "") {
						$("#tboRO").html(msg);
					}
				});
			} else {
				//alert('Nope.');
			}
		}
	});
}

function togglePop(id) {
	$('#' + id).popover('hide');
}
function ShowHSModal(cam) {
	var urlName = "/sanivan/showcam/" + cam + '?typehs=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			$("#idsuiviscam").val(objData.id);
			$("#camionhs").val(cam).change();
			$("#dtestarths").val(objData.datestart);
			$("#dteendhs").val(objData.dateretourprevu);
			$("#notehs").val(objData.note);
			$("#btnEndHS").show();
			$("#modalhs").modal("show");
		}
	});
}
function ShowAvecMatModal(cam) {
	var urlName = "/sanivan/showcam/" + cam + '?typehs=2';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			$("#idsuiviscam").val(objData.id);
			$("#camion").val(cam).change();
			$("#dtestart").val(objData.datestart);
			$("#dteend").val(objData.dateretourprevu);
			$("#typemat").val(objData.TypeDechet).change();
			$("#qte").val(objData.QteDechet);
			$("#notemat").val(objData.note);
			$("#btnEndMat").html('Confirmer Vidé');
			$("#btnEndMat").show();
			$("#modalmat").modal("show");
		}
	});
}
function ShowRHModal(id) {
	var urlName = "/sanivan/showmodalrh/" + id;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			
			$("#selRH").val(objData.idrh).change();
			$("#selRH").prop('disabled', true);
			$("#idsuivisrh").val(objData.id);
			$("#dtestartrh").val(objData.datestart);
			$("#dteendrh").val(objData.dateretourprevu);
			$("#selRHabsence").val(objData.idabsence).change();
			$("#noterh").val(objData.note);
			$("#avistoshow").val(objData.avistoshow);
			if (objData.showavis == 1) {
				$("#showavis").prop('checked', true);
			}
			else {
				$("#showavis").prop('checked', false);
            }
			$("#btnEndRH").show();
			$("#modalrh").modal("show");
		}
	});
}
function ShowRHModalFromList(id) {
	var urlName = "/sanivan/showmodalrh/" + id;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			$("#selRHlist").html(objData.listtype);
			//$("#selRH").val(objData.idrh).change();
			$("#selRHlist").prop('disabled', true);
			$("#idsuivisrhlist").val(objData.id);
			$("#dtestartrhlist").val(objData.datestart);
			$("#dteendrhlist").val(objData.dateretourprevu);
			$("#selRHabsencelist").html(objData.lstabsence);
			$("#noterhlist").val(objData.note);
			$("#avistoshowlist").val(objData.avistoshow);
			if (objData.showavis == 1) {
				$("#showavislist").prop('checked', true);
			}
			else {
				$("#showavislist").prop('checked', false);
            }
			$("#btnEndRHlist").show();
			$("#modalrhlist").modal("show");
		}
	});
}
function ShowModalHres() {
	var urlName = "/sanivan/gethrrh/" + $('#dteRH').val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			$("#tbomodalRHhres").html(msg);
			$("#titreShowAidesHres").html('Heures des Aides du &nbsp;' + $('#dteRH').val());
			$("#modalShowHres").modal("show");
		}
	});
}
function dropREHors(ev,typecam, statut) {
	ev.preventDefault();
	var TypeDeCamion = typecam;
	var arrData = ev.dataTransfer.getData("text").split('_');
	var cam = arrData[1];
	ActifCamion = cam;
	dropfrom = 0;
	switch (statut) {
		case 1:
			getTypeCamPopup(TypeDeCamion, 1);
			$("#camionhs").val(cam).change();
			$("#selTypeCamMat").val(typecam).change();
			$("#dtestarths").val(ActiveDate);
			$("#idsuiviscam").val(0);
			$("#notehs").val('');
			$("#btnEndHS").hide();
			$("#modalhs").modal("show");
			break;
		case 2:
			getTypeCamPopup(TypeDeCamion, 2);
			$("#camion").val(cam).change();
			$("#selTypeCamMat").val(typecam).change();
			$("#dtestart").val(ActiveDate);
			$("#idsuiviscam").val(0);
			$("#typemat").val('').change();
			$("#notemat").val('');
			$("#btnEndMat").html('Confirmer Vidé');
			$("#btnEndMat").hide();
			$("#modalmat").modal("show");
			break;
	}


}
function dropRHNonDispo(ev) {
	ev.preventDefault();
	dropfrom = 0;
	var arrData = ev.dataTransfer.getData("text").split('_');
	$("#selRH").prop('disabled', false);
	var rh = arrData[1];
	$("#selRH").val(rh).change();
	$("#idsuivisrh").val(0);
	$("#dtestartrh").val(ActiveDate);
	$("#selRHabsence").val('').change();
	$("#noterh").val('');
	$("#btnEndRH").hide();
	$("#avistoshow").val('');
	$("#showavis").prop('checked', false);
	$("#btnEndRH").hide();
	$("#modalrh").modal("show");
}
function dropREHorsTDB(ev,typecam, statut) {
	ev.preventDefault();
	var TypeDeCamion = typecam;
	var arrData = ev.dataTransfer.getData("text").split('_');
	var cam = arrData[1];
	//-----------------------------------------
	var urlName = "/sanivan/chkhsexist/" + cam + "/" + statut;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		
		if (msg == 1) {
			if (statut == 1)
				ShowHSModal(cam);
			else
				ShowAvecMatModal(cam);
		}
		else {
			ActifCamion = cam;
			dropfrom = 1;
			switch (statut) {
				case 1:
					getTypeCamPopup(TypeDeCamion, 1);
					$("#camionhs").val(cam).change();
					$("#selTypeCamMat").val(typecam).change();
					$("#dtestarths").val(ActiveDate);
					$("#idsuiviscam").val(0);
					$("#notehs").val('');
					$("#btnEndHS").hide();
					$("#modalhs").modal("show");
					break;
				case 2:
					getTypeCamPopup(TypeDeCamion, 2);
					$("#camion").val(cam).change();
					$("#selTypeCamMat").val(typecam).change();
					$("#dtestart").val(ActiveDate);
					$("#idsuiviscam").val(0);
					$("#typemat").val('').change();
					$("#notemat").val('');
					$("#btnEndMat").html('Confirmer Vidé');
					$("#btnEndMat").hide();
					$("#modalmat").modal("show");
					break;
			}
    }
	});
}
function dropRHNonDispoTDB(ev) {
	ev.preventDefault();
	var arrData = ev.dataTransfer.getData("text").split('_');
	$("#selRH").prop('disabled', false);
	var rh = arrData[1];
	var urlName = "/sanivan/chkrhexist/" + rh;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (parseInt( msg ) > 0) {
			ShowRHModal(msg);
		}
		else {
			$("#selRH").val(rh).change();
			$("#idsuivisrh").val(0);
			$("#dtestartrh").val(ActiveDate);
			$("#selRHabsence").val('').change();
			$("#noterh").val('');
			$("#btnEndRH").hide();
			dropfrom = 1;
			$("#avistoshow").val('');
			$("#showavis").prop('checked', false);
			$("#btnEndRH").hide();
			$("#modalrh").modal("show");
		}
	});

}
function updTableRang(id, oldrang, value, dtejour, acol, activecedule, idsup) {
	var urlName = '/sanivan/rang/' + id + '/' + oldrang + '/' + value + "/" + acol + "/" + dtejour + "/" + activecedule + '?limit=' + limitcedule + '&activedrop=1' + ((typeof idsup !== 'undefined') ? '&idsup=' + idsup : '');
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#divCedule").empty();
			$("#divCedule").html(objData.today);
			$("#spRearEnd").html(objData.spRearEnd);
			$("#spRobot").html(objData.nbRobot);
			$("#spTotalCam").html(objData.nbAllCam);
			if (objData.limitcedule === "1") {
				$("#chkRearLoad").prop('checked', true);
			}
			else if (objData.limitcedule === "2") {
				$("#chkRobot").prop('checked', true);
			}
		}
	});
}
function getCamForType(value) {
	typecam = value;
	var urlName = '/sanivan/camfromtype/' + value + '?activedate=' + $("#dteActivePage1").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			if (value == 2) {
				$('#liRearEnd').removeClass("active");
				$('#liRobot').addClass("active");
			}
			else {
				$('#liRobot').removeClass("active");
				$('#liRearEnd').addClass("active");
      }
			$("#divDropCamion").html(msg);
		}
	});
}
function getTypeCam(value) {
	var urlName = '/sanivan/typecam/' + value + '?activedate=' + $("#dteActivePage1").val() + '&camion=' +$("#camionligne").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#camionligne").html(msg);
		}
	});
}
function getTypeCamPopup(value, statut) {

	var urlName = '/sanivan/typecam/' + value + '?activedate=' + $("#dteActivePage1").val() + '&camion=' + ActifCamion;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			if (statut == 2) {
				$("#camion").html(msg);
			}
			else {
				$("#camionhs").html(msg);
      }

		}
	});
}
function getCamForTypeHome(value) {
	var urlName = '/sanivan/camfromtypetdb/' + value;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#divDropCam").html(msg);
		}
	});
}
function getRHForType(value) {
	var urlName = '/sanivan/rhfromtype/' + value;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#divDropChauf").html(msg);
		}
	});
}
function VerifAvis(value) {
	var urlName = '/sanivan/verifavis/' + value + '/' + $("#dteFrom").val() + '/' + $("input[name='r2']:checked").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboAbsents").html(msg);
			$('#divContainer').css('margin-left', '375px');
			$("#divMessages").show();
		}
	});
}
function setNbCam(value, idgrp) {
	var urlName = '/sanivan/nbcam/' + idgrp + '?value=' + value;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			//$("#tboAbsents").html(msg);
			//$('#divContainer').css('left', '375px');
			//$("#divMessages").show();
		}
	});
}
function radTypeTrans(value) {
	if (value == 2) {
		$("#divInfoTo").show();
		$("#divInfoFrom").show();
	}
	else {
		$("#divInfoTo").hide();
		$("#divInfoFrom").hide();
	}
}
function VerifDelInfo() {
	if ((($("#chkRearEnd").is(':checked')) || ($("#chkRobot").is(':checked'))) && ($("#dtestartdel").val() != '') && ($("#dteenddel").val() != '')) {
		$("#btnDelCedule").prop('disabled', false);
  }
}
function sendMailAvis() {
	var urlName = '/sanivan/sendmailavis?avis=' + $("#divAbsents").html() +
		'&dtefrom=' + $("#dteFrom").val() + '&dteto=' + $("#dteTo").val() +
		'&typecopie=' + $("input[name='r2']:checked").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			alert(msg);
		}
	});
}
function PopOverCamMat(cam) {
	$("#showcammat_" + cam).popover();
}
function setHresAides(idsuivis, field, value, from) {
	var actdate = ((from == '1') ? $('#dteRHWin').val() : $('#dteRH').val())
	var urlName = '/sanivan/posthresaides?field=' + field + '&idsuivis=' + idsuivis +
		'&value=' + value + '&activedate=' + actdate + '&from=' + from;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboRHhres").html(msg);
			$("#tbomodalRHhres").html(msg);
		}
	});
}
function ChangeHresAides(idsuivis, field, value, from) {
	var stripVal = value;
	stripVal = stripVal.replace('#', ' no: ');
	stripVal = stripVal.replace('&', ' et ');
	var urlName = '/sanivan/posthresaides?field=' + field + '&idsuivis=' + idsuivis + '&value=' + stripVal + '&activedate=' + $('#dteRH').val() + '&from=' + from;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			if (from == 1) {
				$("#tbomodalRHhres").html(msg);
			}
			else {
				$("#tboRHhres").html(msg);
      }


		}
	});
}
function EnterKeyHre(idsuivis, field, value, e, from) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var stripval = value;
		stripval = stripval.replace('#', ' no: ');
		stripval = stripval.replace('&', ' et ');
		var urlName = '/sanivan/posthresaides?field=' + field + '&idsuivis=' + idsuivis + '&value=' + stripval + '&activedate=' + $('#dteRH').val() + '&from=' + from;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				$("#tboRHhres").html(msg);
				$("#tbomodalRHhres").html(msg);
			}
		});
	}
}
function PostHresAides(idsuivis) {
	var Fstart = '?edtdeb_' + idsuivis + '=' + $("#edtdeb_" + idsuivis).val();
	var Fend = '&edtfin_' + idsuivis + '=' + $("#edtfin_" + idsuivis).val();
	var Fpause = '&edtpause_' + idsuivis + '=' + $("#edtpause_" + idsuivis).val();
	var stripNote = $("#edtnote_" + idsuivis).val();
	stripNote = stripNote.replace('#', ' no: ');
	stripNote = stripNote.replace('&', ' et ');
	var Fnote = '&edtnote_' + idsuivis + '=' + stripNote;
	var urlName = '/sanivan/postallinfohres' + Fstart + Fend + Fpause + Fnote + '&idsuivis=' + idsuivis + '&activedate=' + $('#dteRHWin').val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboRHhres").html(msg);
			$("#tbomodalRHhres").html(msg);
		}
	});
}
function ShowPageHres() {
	document.location = "/sanivan/showhresaides/" + $('#dteRH').val() + "?from=1";
}
function RemoveSecteur(id) {
	if (confirm('Voulez-vous supprimer ce secteur.')) {
		var urlName = '/sanivan/removesecteur/' + id;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			document.location = "/sanivan/showsecteur/0";
		});
	}
}
function RemoveRH(id) {
	if (confirm('Voulez-vous supprimer cette ressource.')) {
		var urlName = '/sanivan/removerh/' + id;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			document.location = "/sanivan/showrh/0?from=0";
		});
	}
}
function RemoveSuivisRH(id) {
	if (confirm('Voulez-vous supprimer ce suivis.')) {
		var urlName = '/sanivan/removesuivisrh/' + id;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			var currentdate = new Date();
			var mois = currentdate.getMonth() + 1;
			var annee = currentdate.getFullYear();
			var actived = annee + '-' + mois + '-' + currentdate.getDate();
			document.location = "/sanivan/showsuivisrhlist/" + actived;
		});
	}
}
function searchInTableRH() {
	
	var urlName = "/sanivan/showrh/" + Deleted + "?typerh=" + $("#selTypeRH").val() + '&valtofind=' + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function EntersearchTableRH(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName = "/sanivan/showrh/" + Deleted + "?typerh=" + $("#selTypeRH").val() + '&valtofind=' + value + '&from=1';
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				$("#tboProd").html(msg);
			}
		});
	}
}
function ClearsearchTableRH() {
	$('#edtFind').val('');
	var urlName = "/sanivan/showrh/" + Deleted + "?typerh=" + $("#selTypeRH").val() + '&valtofind=' + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function SelectTypeRH(value) {
	typerh = value;
	var urlName = "/sanivan/showrh/" + Deleted + "?typerh=" + value + '&valtofind=' + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function ShowDeleteRH(isdelete) {
	Deleted = isdelete;
	var urlName = "/sanivan/showrh/" + isdelete + "?typerh=" + $("#selTypeRH").val() + '&valtofind=' + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			if (isdelete == 1) {
				$("#lblShowDelRH_0").hide();
				$("#lblShowDelRH_1").show();
			}
			else {
				$("#lblShowDelRH_1").hide();
				$("#lblShowDelRH_0").show();
			}
			$("#tboProd").html(msg);
		}
	});
}

function searchInTableCam() {
	var urlName = "/sanivan/editcam/" + Deleted + "?typecam=" + $("#selTypeCam").val() + '&valtofind=' + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function EntersearchTableCam(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName = "/sanivan/editcam/" + Deleted + "?typecam=" + $("#selTypeCam").val() + '&valtofind=' + value + '&from=1';
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				$("#tboProd").html(msg);
			}
		});
	}
}
function ClearsearchTableCam() {
	$('#edtFind').val('');
	var urlName = "/sanivan/editcam/" + Deleted + "?typecam=" + $("#selTypeCam").val() + '&valtofind=' + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function SelectTypeCam(value) {
	var urlName = "/sanivan/editcam/" + Deleted + "?typecam=" + value + '&valtofind=' + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function ShowDeleteCam(isdelete) {
	Deleted = isdelete;
	var urlName = "/sanivan/editcam/" + isdelete + "?typecam=" + $("#selTypeCam").val() + '&valtofind=' + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			if (isdelete == 1) {
				$("#lblShowDelCam_0").hide();
				$("#lblShowDelCam_1").show();
			}
			else {
				$("#lblShowDelCam_1").hide();
				$("#lblShowDelCam_0").show();
      }
			$("#tboProd").html(msg);
		}
	});
}
function searchInTableSEC() {
	var urlName = "/sanivan/showsecteur/0?valtofind=" + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function EntersearchTableSEC(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName = "/sanivan/showsecteur/0?valtofind=" + value + '&from=1';
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				$("#tboProd").html(msg);
			}
		});
	}
}
function ClearsearchTableSEC() {
	$('#edtFind').val('');
	var urlName = "/sanivan/showsecteur/0?valtofind=" + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function searchInTableSuivis() {
	var currentdate = new Date();
	var mois = currentdate.getMonth() = 1;
	var annee = currentdate.getFullYear();
	var actived = annee + '-' + mois + '-' + currentdate.getDate();
	document.location = "/sanivan/showsuivisrhlist/" + actived + "?valtofind=" + $("#edtFind").val() + "&from=1";
	
}
function EntersearchTableSuivis(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName = "/sanivan/showsecteur/0?valtofind=" + value + '&from=1';
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				$("#tboProd").html(msg);
			}
		});
	}
}
function ClearsearchTableSuivis() {
	$('#edtFind').val('');
	var urlName = "/sanivan/showsecteur/0?valtofind=" + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function searchInTableV() {
	var urlName = "/sanivan/showville/0?valtofind=" + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function EntersearchTableV(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName = "/sanivan/showville/0?valtofind=" + value + '&from=1';
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				$("#tboProd").html(msg);
			}
		});
	}
}
function ClearsearchTableV() {
	$('#edtFind').val('');
var urlName = "/sanivan/showville/0?valtofind=" + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}
function ShowDeleteV(isdelete) {
	Deleted = isdelete;
	var urlName = "/sanivan/showville/" + isdelete + '?&valtofind=' + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			if (isdelete == 1) {
				$("#lblShowDelV_0").hide();
				$("#lblShowDelV_1").show();
			}
			else {
				$("#lblShowDelV_1").hide();
				$("#lblShowDelV_0").show();
      }
			$("#tboProd").html(msg);
		}
	});
}

function searchInTableRHhres() {
	ActiveDate = $('#dteRH').val();
	var urlName = '/sanivan/aujourdhui/4/' + $('#dteRH').val() + '?activedrop=' + ActiveDrop + '&valtofind=' + $("#edtFind").val()  +
		'&typecedule=' + activecedule + '&notab=' + notab + '&AideIsShow=' + AideIsShow;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		var objData = JSON.parse(data);
		$("#tboRHhres").html(objData.listhresaides);
	});
}
function EntersearchTableRHhres(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		ActiveDate = $('#dteRH').val();
		var urlName = '/sanivan/aujourdhui/4/' + $('#dteRH').val() + '?activedrop=' + ActiveDrop + '&valtofind=' + value +
			'&typecedule=' + activecedule + '&notab=' + notab + '&AideIsShow=' + AideIsShow;
		$.ajax({
			url: urlName,
			context: document.body
		}).done(function (data) {
			var objData = JSON.parse(data);
			$("#tboRHhres").html(objData.listhresaides);
		});
	}
}
function ClearsearchTableRHhres() {
	$('#edtFind').val('');
	ActiveDate = $('#dteRH').val();
	var urlName = '/sanivan/aujourdhui/4/' + $('#dteRH').val() + '?activedrop=' + ActiveDrop + '&valtofind=' + $("#edtFind").val() +
		'&typecedule=' + activecedule + '&notab=' + notab + '&AideIsShow=' + AideIsShow;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		var objData = JSON.parse(data);
		$("#tboRHhres").html(objData.listhresaides);
	});
}
function AfficheBoites() {
	var urlName = base_url + 'rolloff/edition_boites?typeboite=tous&champ=&valtofind=&isdispo=all';
	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
}
function HistoriqueBoites() {
	var urlName = base_url + 'rolloff/historique_boites';
	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
}

function ShowBoiteMultiple(idcommande) {

	var urlName = "/rolloff/boitemultiple/" + idcommande;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (data) {
		var objData = JSON.parse(data);
		$("#tboBoitesEnPlace").html(objData.allenplace);
		$("#tboBoitesEnRoute").html(objData.allenroute);
		$("#tblHistoMulti").html(objData.histomulti);
		$("#modalMultiple").modal('show');
	});
}

function ShowBoiteEnRoute(idcommande) {
	var urlName = "/rolloff/boitemultiple/" + idcommande;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (data) {
		var objData = JSON.parse(data);

		alert(objData.allenroute);
		//$("#tboBoites").html(objData.allenroute);
	});
}
function ShowAllCommandeClient() {
	var urlName = '/rolloff/listeallcommandes?dteactive=' + $('#dteActive').val();
	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
}
function ShowAllLimitCommandeClient(idchauffeur) {
	var urlName = '/rolloff/listeallcommandes?dteactive=' + $('#dteActive').val() + '&idchauffeur=' + idchauffeur;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$("#tboProd").html(msg);
	});
}

function SubmitForm() {
	var value = $('#frmOption').serialize();
	var urlName = '/admin/optionssys/post?' + value;
	$.ajax({
		url: urlName,
		async: true,
		type: "POST",
	}).done(function (data) {
		//-----------------
		alert('Vos informations ont été enregistrées');
	});
}
function ChangeFieldOrderCam(sens, order) {
	$("#selOrderCam").val(order);
	var urlName = "/sanivan/showsuiviscamlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + $("#selStatutCam").val() + '&order=' + $("#selOrderCam").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuiviscam);
		}
	});
}
function ReOrderCam(sens) {
	$("#edtsens").val(sens)
	var urlName = "/sanivan/showsuiviscamlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + $("#selStatutCam").val() + '&order=' + $("#selOrderCam").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuiviscam);
		}
	});
}
function SelectTypeStatutCam(value) {
	var urlName = "/sanivan/showsuiviscamlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + value + '&order=' + $("#selOrderCam").val() + '&sens=' + $("#edtsens").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuiviscam);
		}
	});
}
function SelectOrderSuivisCam(value) {
	var urlName = "/sanivan/showsuiviscamlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + $("#selStatutCam").val() + '&order=' + value + '&sens=' + $("#edtsens").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuiviscam);
		}
	});
}
function LoadSuivisCam() {
	var urlName = "/sanivan/showsuiviscamlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + $("#selStatutCam").val() + '&order=' + $("#selOrderCam").val() + '&sens=' + $("#edtsens").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuiviscam);
		}
	});
}
function searchInTableSuivisCam() {
	var urlName = "/sanivan/showsuiviscamlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + $("#selStatutCam").val() + '&order=' + $("#selOrderCam").val() + '&sens=' + $("#edtsens").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuiviscam);
		}
	});

}
function EntersearchTableSuivisCam(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName = "/sanivan/showsuiviscamlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + $("#selStatutCam").val() + '&order=' + $("#selOrderCam").val() + '&sens=' + $("#edtsens").val();
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				var objData = JSON.parse(msg);
				$("#tbhProd").html(objData.entete);
				$("#tboProd").html(objData.listsuiviscam);
			}
		});
	}
}
function ClearsearchTableSuivisCam() {
	$('#edtFind').val('');
	var urlName = "/sanivan/showsuiviscamlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + $("#selStatutCam").val() + '&order=' + $("#selOrderCam").val() + '&sens=' + $("#edtsens").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuiviscam);
		}
	});
}
function ChangeFieldOrderRH(sens, order) {
	$("#selOrderRH").val(order);
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuivisrh);
		}
	});
}
function ChangeFieldOrderRapRO(sens, order) {
	$("#selOrderRH").val(order);
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuivisrh);
		}
	});
}
function ReOrderRH(sens) {
	$("#edtsens").val(sens);
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuivisrh);
		}
	});
}
function ReOrderRapRO(sens) {
	$("#edtsens").val(sens);
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuivisrh);
		}
	});
}
function SelectTypeStatutRH(value) {
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + value + '&order=' + $("#selOrderRH").val() + '&sens=' + $("#edtsens").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuivisrh);
		}
	});
}
function SelectOrderSuivisRH(value) {
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + value + '&sens=' + $("#edtsens").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuivisrh);
		}
	});
}
function LoadSuivisRH() {
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(msg);
		}
	});
}
function searchInTableSuivis() {
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(msg);
		}
	});

}
function EntersearchTableSuivis(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				$("#tbhProd").html(objData.entete);
				$("#tboProd").html(msg);
			}
		});
	}
}
function ClearsearchTableSuivis() {
	$('#edtFind').val('');
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(msg);
		}
	});
}
function LoadHistorique() {
	var urlName = "/rolloff/historique_boites?dtedeb=" + $("#dtedeb").val() + '&dtefin=' + $("#dtefin").val();
	document.location = urlName;
}
function LoadSurplusBoite() {
	var urlName = "/rolloff/surplus_boites?dtedeb=" + $("#dtedeb").val() + '&dtefin=' + $("#dtefin").val();
	document.location = urlName;
}
function ShowComRollOff(idcommande) {
	var urlName = '/bureau/commandes/rolloff?idcommande=' + idcommande + '&dateactive=' + selectedDay;
	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
}
function searchInTableHisto() {
	var urlName = "/rolloff/historique_boites?dtedeb=" + $("#dtedeb").val() + '&dtefin=' + $("#dtefin").val() + '&boite=' + $("#edtFind").val();
	document.location = urlName;
}
function EntersearchTableHisto(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;

	if (keycode == 13) {
		var urlName = "/rolloff/historique_boites?dtedeb=" + $("#dtedeb").val() + '&dtefin=' + $("#dtefin").val() + '&boite=' + value;
		document.location = urlName;

	}
}
function ClearsearchTableHisto() {
	$('#edtFind').val('');
	var urlName = "/rolloff/historique_boites?dtedeb=" + $("#dtedeb").val() + '&dtefin=' + $("#dtefin").val() + '&boite=' + $("#edtFind").val();
	document.location = urlName;
}
function addBoiteInfo() {
	var formval = $("#form_0").serialize();
	var urlName = "/rolloff/addboite?" + formval;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			document.location = '/rolloff/edition_boites?typeboite=tous&champ=&valtofind=&isdispo=all';
		}
	});
}
//------------------------------------------------------------------------------
function postBoiteInfo() {
	var formval = $("#formEditBoite").serialize();
	var urlName = "/rolloff/edtboite?" + formval;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			document.location = '/rolloff/edition_boites?typeboite=tous&champ=&valtofind=&isdispo=all';
		}
	});
}
//------------------------------------------------------------------------------
function ShowEditBoiteInfo(noboite) {
	var urlName = "/rolloff/getboite?noboite=" + noboite;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		var objData = JSON.parse(data);
		//alert(data);
		$("#edtNoBoiteEdit").val(noboite);
		$("#selTypeBoiteEdit").val(objData.TypeBoite);
		$("#edtPlaceEdit").val(objData.Place);
		$("#edtNotesEdit").html(objData.Notes);
		$("#modalEditBoite").modal("show");
	});


}
//------------------------------------------------------------------------------
function UpdDispo(noboite) {
	var isCheck = 0;
	if ($("#chkDispo_" + noboite).prop('checked') == true) {
		isCheck = 1;
	}
	var urlName = "/rolloff/postdispoboite?noboite=" + noboite + '&ischeck=' + isCheck;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			$("#tboRO").html(msg);
		}
	});
}
function UpdHaveMat(noboite) {
	var isCheck = 0;
	if ($("#chkHaveMat_" + noboite).prop('checked') == true) {
		isCheck = 1;
	}
	var urlName = "/rolloff/posthavematboite?noboite=" + noboite + '&ischeck=' + isCheck;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != "") {
			$("#tboRO").html(msg);
		}
	});
}
//------------------------------------ Version #4 ------------------------------------------
//----------------------------------------------------------------------------
function LoadCalendrierRH() {
	var urlName = "/sanivan/calrh?dtedeb=" + $("#dtedeb").val() + "&valtofind=" + $("#edtFind").val() + '&typerhdrop=' + $("#selTypeRHsearchDrop").val() +
		"&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&typerh=' + $("#selTypeRH").val();
	document.location = urlName;
}
function searchInSite() {
	var urlName = "/rolloff/recherche" + "?valtofind=" + $("#edtFindGen").val();
	document.location = urlName;
}
function EntersearchSite(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName = "/rolloff/recherche" + "?valtofind=" + value;
		document.location = urlName;
	}
}
function searchInTableCalendrierRH() {
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(msg);
		}
	});

}
function EntersearchTableCalendrierRH(value, e) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != '') {
				$("#tbhProd").html(objData.entete);
				$("#tboProd").html(msg);
			}
		});
	}
}
function ClearsearchTableCalendrierRH() {
	$('#edtFind').val('');
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&order=' + $("#selOrderRH").val() + '&sens=' + sens;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(msg);
		}
	});
}
function SelectTypeStatutCalendrierRH(value) {
	var urlName = "/sanivan/showsuivisrhlist/" + $("#dtedeb").val() + "?valtofind=" + $("#edtFind").val() + "&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + value + '&order=' + $("#selOrderRH").val() + '&sens=' + $("#edtsens").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var objData = JSON.parse(msg);
			$("#tbhProd").html(objData.entete);
			$("#tboProd").html(objData.listsuivisrh);
		}
	});
}
function SelectTypeCalendrierRH(value) {
	typerh = value;
	var urlName = "/sanivan/showrh/" + Deleted + "?typerh=" + value + '&valtofind=' + $("#edtFind").val() + '&from=1';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#tboProd").html(msg);
		}
	});
}


//----------------------------------------------------------------------------
function LoadCalendrierCam() {
	var urlName = "/sanivan/calcam?selMont=" + $("#selMont").val() + "&valtofind=" + $("#edtFind").val() +
		"&from=1&statut=" + $("#selStatutCam").val() +
		'&typecam=' + $("#selTypeCam").val();
	document.location = urlName;
}
//----------------------------------------------------------------------------
function ShowRHModalCal(id, newDate) {
	//var newDate = arguments[1];
	var urlName = "/sanivan/showmodalrh/" + id;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			$("#selRHCal").html(objData.listtype);
			$("#selRHCal").val(objData.idrh).change();
			$("#selRH").prop('disabled', true);
			$("#idsuivisrh").val(id);
			if (newDate == '') {
				var dte = objData.datestart;
				$("#dtestartrhCal").val(dte);
			}
			else {
				$("#dtestartrhCal").val(newDate);
			}
			//$("#dtestartrhCal").val(objData.datestart);
			$("#dteendrhCal").val(objData.dateretourprevu);
			$("#dtefinrhCal").val(objData.dateend);
			$("#selRHabsenceCal").html(objData.lstabsence)
			$("#selRHabsenceCal").val(objData.idabsence).change();
			$("#noterhCal").val(objData.note);
			$("#avistoshowCal").val(objData.avistoshow);
			if (objData.showavis == 1) {
				$("#showavisCal").prop('checked', true);
			}
			else {
				$("#showavisCal").prop('checked', false);
			}
			$("#btnEndRHCal").show();
			$("#modalrhcal").modal("show");
		}
	});
}
function ShowNewRHModalCal(id, nom, dteActive) {
	$("#nomCal").val(nom);
	$("#idrhnew").val(id);
	$("#dtestartrhnewCal").val(dteActive);
	$("#dteendrhnewCal").val('');
	$("#dtefinrhnewCal").val('');
	$("#selnewRHabsenceCal").val('all').change();
	$("#noterhnewCal").val('');
	$("#avistoshownewCal").val('');
	$("#showavisnewCal").prop('checked', false);
	$("#modalnewrhcal").modal("show");
}
function ShowCamModalCal(id, cam, statut, ActiveDate, TypeDeCamion) {
	//alert(id);
	if (id === 0) {
		switch (statut) {
			case 'HS':

				getTypeCamPopupCal(TypeDeCamion, cam);
				$("#camionhs").val(cam).change();
				//$("#selTypeCamMat").val(typecam).change();
				$("#dtestarths").val(ActiveDate);
				$("#idsuiviscam").val(0);
				$("#notehs").val('');
				$("#btnEndHS").hide();
				$("#modalhs").modal("show");
				break;
			case 'MAT':
				getTypeCamPopupCal(TypeDeCamion, cam);
				$("#camion").val(cam).change();
				//$("#selTypeCamMat").val(typecam).change();
				$("#dtestart").val(ActiveDate);
				$("#idsuiviscam").val(0);
				$("#typemat").val('').change();
				$("#notemat").val('');
				$("#btnEndMat").html('Confirmer Vidé');
				$("#btnEndMat").hide();
				$("#modalmat").modal("show");
				break;
		}
	}
	else {
		var urlName = "/sanivan/chkrcammodalexist/" + id;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {

			var objData = JSON.parse(msg);
			switch (objData.idstatut) {
				case '1':
					$("#idsuiviscam").val(objData.id);
					$("#selTypeCamHS").val(objData.TypeCamion).change();
					getTypeCamPopupCal(objData.TypeCamion, objData.nocamion);
					$("#camionhs").val(objData.nocamion).change();
					$("#dtestarths").val(objData.startStr);
					$("#dteendhs").val(objData.dateretourprevu);
					$("#notehs").val(objData.note);
					$("#btnEndHS").show();
					$("#modalhs").modal("show");
					break;
				case '2':
					$("#idsuiviscam").val(objData.id);
					$("#selTypeCamMat").val(objData.TypeCamion).change();
					getTypeCamPopupCal(objData.TypeCamion, objData.nocamion);
					$("#camion").val(objData.nocamion).change();
					$("#dtestart").val(objData.startStr);
					$("#dteend").val(objData.dateretourprevu);
					$("#typemat").val(objData.TypeDechet).change();
					$("#qte").val(objData.QteDechet);
					$("#notemat").val(objData.note);
					$("#btnEndMat").html('Confirmer Vidé');
					$("#btnEndMat").show();
					$("#modalmat").modal("show");
					break;
				case '3':
					$("#idsuiviscam").val(objData.id);
					$("#selTypeCamBris").val(objData.TypeCamion).change();
					getTypeCamPopupCal(objData.TypeCamion, objData.nocamion);
					$("#camionBris").val(objData.nocamion).change();
					$("#dtestartBris").val(objData.startStr);
					$("#hrestartBris").val(objData.hrestart);
					$("#dteendBris").val(objData.dateend);
					$("#hreendBris").val(objData.hreend);
					$("#noteBris").val(objData.note);
					$("#modalbris").modal("show");
					break;
			}
		});
	}

}

function getTypeCamPopupCal(value, camion) {

	var urlName = '/sanivan/typecamcal/' + value + '?camion=' + camion;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#camion").html(msg);
			$("#camionhs").html(msg);
		}
	});
}
function getCamForTypeCal(value) {
	typecam = value;
	var urlName = '/sanivan/camfromtypecal/' + value + '?activedate=' + $("#dteActivePage1").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			if (value == 2) {
				$('#liRearEnd').removeClass("active");
				$('#liRobot').addClass("active");
			}
			else {
				$('#liRobot').removeClass("active");
				$('#liRearEnd').addClass("active");
			}
			$("#divDropCamion").html(msg);
		}
	});
}

function PostSuivisCamMatCal() {
	var urlName = "/sanivan/camionout/" + $('#idsuiviscam').val() + "?nocamion=" + $('#camion').val() + "&datestart=" + $('#dtestart').val() + "&typemat=" + $('#typemat').val() +
		"&notehs=" + $('#notemat').val() + "&from=1&idstatut=2&action=2&dtedeb=" + $("#dtedeb").val() + "&valtofind=" + $("#edtFind").val() +
		"&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + $("#selStatutCam").val() + '&typecam=' + $("#selTypeCam").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = '/sanivan/calcam?statut=all' ;
	})
}
function PostSuivisCamHSCal() {
	var urlName = "/sanivan/camionout/" + $('#idsuiviscam').val() + "?nocamion=" + $('#camionhs').val() + "&datestart=" + $('#dtestarths').val() + "&dateend=" +
		$('#dteendhs').val() + "&notehs=" + $('#notehs').val() + "&from=1&idstatut=1&action=2&dtedeb=" + $("#dtedeb").val() + "&valtofind=" + $("#edtFind").val() +
		"&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + $("#selStatutCam").val() + '&typecam=' + $("#selTypeCam").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = '/sanivan/calcam?statut=all';
	});

}
function PostSuivisRHCal() {
	var chkshow = (($("#showavisCal").is(':checked')) ? 1 : 0);
	var urlName = "/sanivan/ressourse/" + $('#selRHCal').val() + "/" + $('#selRHabsenceCal').val() + "/" + $('#dtestartrhCal').val() + "?dteretour=" +
		$('#dteendrhCal').val() + "&noterh=" + encodeURIComponent($('#noterhCal').val()) + "&showavis=" + chkshow +
		"&from=1&avistoshow=" + encodeURIComponent($('#avistoshowCal').val()) + "&idsuivis=" + $('#idsuivisrh').val() + "&dtedeb=" + $("#dtedeb").val() + "&valtofind=" + $("#edtFind").val() +
		"&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&typerh=' + $("#selTypeRH").val();
	document.location = urlName;
}
function PostSuivisnewRHCal() {
	var chkshow = (($("#showavisnewCal").is(':checked')) ? 1 : 0);
	var urlName = "/sanivan/ressourse/" + $('#idrhnew').val() + "/" + $('#selnewRHabsenceCal').val() + "/" + $('#dtestartrhnewCal').val() + "?dteretour=" +
		$('#dteendrhnewCal').val() + "&noterh=" + $('#noterhnewCal').val() + "&showavisnewCal=" + chkshow +
		"&from=1&avistoshow=" + $('#avistoshownewCal').val() + "&idsuivis=" + $('#idsuivisnewrh').val() + "&dtedeb=" + $("#dtedeb").val() + "&valtofind=" + $("#edtFind").val() +
		"&from=1&dtefin=" + $("#dtefin").val() + '&absence=' + $("#selStatutRH").val() + '&typerh=' + $("#selTypeRH").val();
	document.location = urlName;
}
function UpdViderMatCal() {
	var urlName = "/sanivan/vide_materiel/" + $('#idsuiviscam').val() + '/' + ActiveDate;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = "/sanivan/calcam?dtedeb=" + $("#dtedeb").val() + "&valtofind=" + $("#edtFind").val() +
			"&from=1&dtefin=" + $("#dtefin").val() + '&statut=' + $("#selStatutCam").val() + '&typecam=' + $("#selTypeCam").val();
	});
}
//----------------------------------------------------------------------------
function showVilleMail(id) {
	var urlName = "/ville/showemail/" + id;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg.trim() != '') {
			var objData = JSON.parse(msg);
			$("#inputEmail1").val(objData.inputEmail1);
			$("#inputPassword1").val(objData.inputPassword1);
			$("#inputEmail2").val(objData.inputEmail2);
			$("#inputPassword2").val(objData.inputPassword2);
			$("#inputEmail3").val(objData.inputEmail3);
			$("#inputPassword3").val(objData.inputPassword3);
		}
		else {
			$("#inputEmail1").val('');
			$("#inputPassword1").val('');
			$("#inputEmail2").val('');
			$("#inputPassword2").val('');
			$("#inputEmail3").val('');
			$("#inputPassword3").val('');
		}
		$("#idville").val(id);
		$("#modal_invitation").modal("show");
	});
}
function PostEmail(id) {
	var urlName = "/ville/postmail/" + $('#idvillemodal').val() + '/' + id + '?email=' + $('#edtModalEmail_' + id).val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		if (objData.rep.trim() != '') {
			$('#msg_result').html(objData.rep);
			$("#msg_result").fadeIn(4000, function () {
				$("#msg_result").fadeOut(6000);
			});
			$('#btnmail_' + $('#idvillemodal').val()).addClass('btn-info');
			$('#btnmail_' + $('#idvillemodal').val()).html(' &nbsp;Courriels&nbsp;&nbsp;<span class="badge badge-primary">' + objData.nbMail + '</span>')
		}
	});
}
function addEmail() {
	var ligneToAdd = '<div class="input-group" style="width: 100%;"> <span class="input-group-btn" >' +
	'<h3'
		'<input class="form-control" style="width: 90%" id="edtModalEmail_0" type="text" value="" />' +
		'<a aria-label="Edit" title="Cliquer pour enregistrer" onclick="PostEmail(0)">&nbsp;<i class="fa fa-check btn btn-success" style="font-size:16px;"></i>&nbsp;</a></span></div>';
	$('#divMailModal').append(ligneToAdd);
}
//									#	DEBUT ROLLOFF RAPPORT
//----------------------------------------------------------------------------
function LoadHeuresRH(nbjour) {
	var urlName = "/rapport/dayro/" + $("#dtedeb").val() + '/1/' + nbjour;
	document.location = urlName;
}
function AddDayRap() {
	var urlName = "/rapport/dayro/" + $("#dtedeb").val() + '/1/0';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		urlName = "/rapport/dayro/" + $("#dtedeb").val() + '/1/0';
		document.location = urlName;		
	});

}
function ShowHideRap(id,fieldrow) {
	if ($('#' + id).css('display') !== 'none') {
		$('#' + id).hide();
		$('#lb_chauf_' + fieldrow).removeClass('btn-danger');
		$('#spShowHide_' + fieldrow).removeClass('glyphicon-chevron-up');
		$('#spShowHide_' + fieldrow).addClass('glyphicon-chevron-down');
		$('.separateur').hide();
    }

	else {
		$('.warning').hide();
		$('#lb_chauf_' + ActiveRow).removeClass('btn-danger');
		$('#spShowHide_' + ActiveRow).removeClass('glyphicon-chevron-up');
		$('#spShowHide_' + ActiveRow).addClass('glyphicon-chevron-down');
		$('.separateur').hide();
		ActiveRow = fieldrow;
		$('#' + id).show();
		$('#lb_chauf_' + fieldrow).addClass('btn-danger');
		$('#spShowHide_' + fieldrow).removeClass('glyphicon-chevron-down');
		$('#spShowHide_' + fieldrow).addClass('glyphicon-chevron-up');
    }
}
function ShowHidePrix(id,fieldrow) {
	if ($('#' + id).css('display') !== 'none') {
		$('#' + id).hide();
		//$('#lb_chauf_' + fieldrow).removeClass('btn-danger');
		$('#spShowHide_' + fieldrow).removeClass('glyphicon-chevron-up');
		$('#spShowHide_' + fieldrow).addClass('glyphicon-chevron-down');
    }

	else {
		$('.separateur').hide();
		//$('#lb_chauf_' + ActiveRow).removeClass('btn-danger');
		$('#spShowHide_' + ActiveRowPrix).removeClass('glyphicon-chevron-up');
		$('#spShowHide_' + ActiveRowPrix).addClass('glyphicon-chevron-down');
		ActiveRowPrix = fieldrow;
		$('#' + id).show();
		//$('#lb_chauf_' + fieldrow).addClass('btn-danger');
		$('#spShowHide_' + fieldrow).removeClass('glyphicon-chevron-down');
		$('#spShowHide_' + fieldrow).addClass('glyphicon-chevron-up');
    }
}
function ShowHideDivRap(id, li) {
	$('#divChauffeurs').hide();
	//$('#divCamions').hide();
	$('#divRapJour').hide();
	$('#liChauf').removeClass("li-info");
	//$('#liCam').removeClass("active");
	$('#liRapJour').removeClass("li-info");
	//------------------
	if (id == 'divChauffeurs')  {
		IsRap = 0;
	}
	else {
		IsRap = 1;
		var urlName = "/rapport/getrapportday/" + $('#dtedeb').val();
		$.ajax({
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != "") {
				$('#tbRapDay').html(msg);
			}
		});
	}
	$('#' + id).show();
	$('#' + li).addClass("li-info");
}
function PostTransBo() {
	var formval = $("#frmCommande").serialize();
	var OldIdCommande = $("#actifcommande").val();
	var urlName = "/bureau/postcommande/" + $("#actifcommande").val() + '/' + $("#selActifRH").val() + '?' + formval + '&idtransbo=' + $("#idtransbo").val();
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg == "OK") {
			document.location = "/rolloff/aujourdhui/11/" + ActiveDate;
		}
	});
}
function CancelComTransbo() {
	document.location = "/rolloff/aujourdhui/11/" + ActiveDate;
}
function selChangeBoite(value) {
	$("#btnModalPostBoite").prop('disabled', ((value.trim() == '0') ? true : false));
}
function selChangeTypeDec(value) {
	$("#btnModalPostDechet").prop('disabled', ((value.trim() == '0') ? true : false));
}
function updBoiteTransBO() {
	var newBox = $("#selEditTransbo_in").val();
	var urlName = "/rolloff/updbox/" + $("#infoTrans").val() + '/' + newBox;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg == "OK") {
			$("#btnBoite_" + $("#infoTrans").val()).html(newBox);
		}
	});
}
function updDechetTransBO() {
	var dechet = $("#selEditTypeTransbo_in").val();
	var urlName = "/rolloff/upddechet/" + $("#infoTrans").val() + '/' + dechet;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {

		$("#btn_" + $("#infoTrans").val()).html(msg);
		
	});
}
function EditLigneTransBO(id) {
	var urlName = "/rolloff/updmontant/" + id + '/' + $("#montant_" + id).val();
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#spModalNbRow").html(objData.NbRow);
		$("#edtModalTotal").val(objData.Total);		
	});
}
function Reload() {
	document.location = "/rolloff/aujourdhui/11/" + ActiveDate;
}
function delTransBO() {
	var urlName = "/rolloff/getnbrow/" + $("#infoTrans").val();
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg == '0') {
			alert('Vous devez supprimer toutes les ligne de cette Transbo.');
		}
		else {
			if (confirm("Etes-vous certain de vouloir supprimer cette Transbo"))
			var urlName = "/rolloff/deltransbo/" + $("#infoTrans").val();
			$.ajax({
				type: "POST",
				url: urlName
			}).done(function (msg) {
				if (msg == "OK") {
					document.location = "/rolloff/aujourdhui/11/" + ActiveDate;
				}
			});
        }
	});
}
function delLigneTransBO(id) {
	var urlName = "/rolloff/dellignetrans/" + id;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$('#tboEditBoxTransbo').html(objData.boitesintransbo);
		$('#tfoEditBoxTransbo').html(objData.sumboitesintransbo);
		$('#btnModalDelTransBo').prop('disabled', ((objData.nbrow == 0) ? false : true));
	});
}
//----------------------------------------------------------------------------
//									#	FIN ROLLOFF RAPPORT
function showLocation(position) {
	var latitude = position.coords.latitude;
	var longitude = position.coords.longitude;
	alert("Latitude : " + latitude + " Longitude: " + longitude);
	myTimer = setTimeout("getlocation()", 10000);
}

function errorHandler(err) {
	if (err.code == 1) {
		alert("Error: Access is denied!");
	} else if (err.code == 2) {
		alert("Error: Position is unavailable!");
	}
}

function getLocation() {
	alert(navigator.geolocation);
	if (navigator.geolocation) {

		// timeout at 60000 milliseconds (60 seconds)
		var options = { timeout: 10000 };
		navigator.geolocation.getCurrentPosition(showLocation, errorHandler, options);
	} else {
		alert("Sorry, browser does not support geolocation!");
	}
}
//----------------------------------------------------------
function chkSurplusLoc(id) {
	var checked = 0;
	if ($("#chkSurplusLoc_" + id).is(':checked')) {
		checked = 1;
		$("#SurplusLocation_" + id).prop("disabled", false);
	}
	else {
		$("#SurplusLocation_" + id).prop("disabled", true);
	}
	urlName = "/rapport/surplusloc/" + id + '/' + checked;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$('#revenunet_' + id).html(objData.revnet);
		$('#RevBrut_' + id).val(objData.revbrut);
		$('#CoutDepotoire_' + id).val(objData.depense);
		$('#surpluspoids_' + id).val(objData.surpluspoids);
		$('#prixsurpluspoids_' + id).val(objData.prixsurpluspoids);
		$('#SurplusLocation_' + id).val(objData.surpluslocation);
	});
}
function chkDeglacage(id) {
	var checked = 0;
	if ($("#chkDeglacage_" + id).is(':checked')) {
		$("#Deglacage_" + id).prop("disabled", false);
		checked = 1;
	}
	else {
		$("#Deglacage_" + id).prop("disabled", true);
	}
	urlName = "/rapport/deglacage/" + id + '/' + checked;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$('#revenunet_' + id).html(objData.revnet);
		$('#RevBrut_' + id).val(objData.revbrut);
		$('#CoutDepotoire_' + id).val(objData.depense);
		$('#surpluspoids_' + id).val(objData.surpluspoids);
		$('#prixsurpluspoids_' + id).val(objData.prixsurpluspoids);
		$('#SurplusLocation_' + id).val(objData.surpluslocation);
	});
}
function chkLavage(id) {
	var checked = 0;
	if ($("#chkLavage_" + id).is(':checked')) {
		$("#Lavage_" + id).prop("disabled", false);
		checked = 1;
	}
	else {
		$("#Lavage_" + id).prop("disabled", true);
	}
	urlName = "/rapport/lavage/" + id + '/' + checked;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$('#revenunet_' + id).html(objData.revnet);
		$('#RevBrut_' + id).val(objData.revbrut);
		$('#CoutDepotoire_' + id).val(objData.depense);
		$('#surpluspoids_' + id).val(objData.surpluspoids);
		$('#prixsurpluspoids_' + id).val(objData.prixsurpluspoids);
		$('#SurplusLocation_' + id).val(objData.surpluslocation);
	});
}
function chkOntario(id) {
	var checked = 0;
	if ($("#chkOntario_" + id).is(':checked')) {
		$("#Ontario_" + id).prop("disabled", false);
		checked = 1
	}
	else {
		$("#Ontario_" + id).prop("disabled", true);
	}
	urlName = "/rapport/ontario/" + id + '/' + checked;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$("#Ontario_" + id).val(objData.Ontario);
		$("#revenu_" + id).html(objData.Revenu);
	});

}
  //----------------------------------------------------------
function ShowComRapDelete(idcommande) {
	//alert(idcommande);
	var currentdate = new Date();
	var mois = currentdate.getMonth() + 1;
	var annee = currentdate.getFullYear();
	var actived = annee + '-' + mois + '-' + currentdate.getDate();
	var urlName = '/bureau/commandes/deleted?idcommande=' + idcommande + '&dateactive=' + actived;
	//alert(urlName);
	//document.location = urlName;
	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
}
function ShowCommandeRap(idcommande) {
	var currentdate = new Date();
	var mois = currentdate.getMonth() + 1;
	var annee = currentdate.getFullYear();
	var actived = annee + '-' + mois + '-' + currentdate.getDate();
	var urlName = '/bureau/commandes/rolloff?idcommande=' + idcommande + '&dateactive=' + actived;
	//alert(urlName);
	//document.location = urlName;
	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
}
function setTransBo(id) {
	var IsChecked = $("#chkTransBo_" + id).prop('checked') ? 1 : 0;
	if ($("#chkTransBo_" + id).prop('checked') == true) {
		$("#lbltransbo_" + id).css("background-color", "lime");
		$("#lbltransbo_" + id).css("color", "white");
	}
	else {
		$("#lbltransbo_" + id).css("background-color", "white");
		$("#lbltransbo_" + id).css("color", "lime");
	}
	var urlName = "/rolloff/infotr/" + id + "/" + IsChecked;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (data) {
		//---------------
	});
}
function setMeloche(id) {
	var IsChecked = $("#chkMeloche_" + id).prop('checked') ? 1 : 0;
	if ($("#chkMeloche_" + id).prop('checked') == true) {
		$("#lblMeloche_" + id).css("background-color", "orange");
		$("#lblMeloche_" + id).css("color", "white");
	}
	else {
		$("#lblMeloche_" + id).css("background-color", "white");
		$("#lblMeloche_" + id).css("color", "orange");
	}
	var urlName = "/rolloff/infomeloche/" + id + "/" + IsChecked;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (data) {
		//---------------
	});
}
function ChangeROActifRH(value, id) {
	if (value != 0) {
		$("#" + id).css("color", "green");
	}
	else {
		$("#" + id).css("color", "red");
	}
	var arrID = id.split('_');
	var urlName = "/rolloff/setrorh/" + arrID[1] + "/" + value;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (data) {
		//---------------
	});
}
function postDayRapRO(NoDemande, nocamion) {
	var checked = 0;
	if ($("#chkSurplusLoc_" + NoDemande).is(':checked')) {
		checked = 1;
	}
	var urlName = "/rapport/postcommande?NoDemande=" + NoDemande +
		'&datejour=' + $('#dtedeb').val() + '&deglacage=' + $('#Deglacage_' + NoDemande).val() +
		'&surpluslocation=' + $('#SurplusLocation_' + NoDemande).val() + '&havesurpluslocation= ' + checked +
		'&lavage=' + $('#Lavage_' + NoDemande).val() + '&ajustprix=' + $('#AjustPrix_' + NoDemande).val()
		+ '&poids=' + $('#Poids_' + NoDemande).val() + '&nocamion=' + nocamion;
	//document.location = urlName;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var objData = JSON.parse(msg);
		$('#revenunet_' + NoDemande).html(objData.revnet);
		$('#RevBrut_' + NoDemande).val(objData.revbrut);
		$('#CoutDepotoire_' + NoDemande).val(objData.depense);
		$('#surpluspoids_' + NoDemande).val(objData.surpluspoids);
		$('#prixsurpluspoids_' + NoDemande).val(objData.prixsurpluspoids);
		$('#SurplusLocation_' + NoDemande).val(objData.surpluslocation);
	});
}
function EnterField(value, e, NoDemande, nocamion) {
	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 13) {
		var checked = 0;
		if ($("#chkSurplusLoc_" + NoDemande).is(':checked')) {
			checked = 1;
		}
		var urlName = "/rapport/postcommande?NoDemande=" + NoDemande +
			'&datejour=' + $('#dtedeb').val() + '&deglacage=' + $('#Deglacage_' + NoDemande).val() + '&lavage=' + $('#Lavage_' + NoDemande).val() +
			'&ajustprix=' + $('#AjustPrix_' + NoDemande).val() + '&havesurpluslocation= ' + checked +
			'&poids=' + $('#Poids_' + NoDemande).val() + '&nocamion=' + nocamion + '&surpluslocation=' + $('#SurplusLocation_' + NoDemande).val();
		//document.location = urlName;
		$.ajax({
			async: true,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			var objData = JSON.parse(msg);
			$('#revenunet_' + NoDemande).html(objData.revnet);
			$('#RevBrut_' + NoDemande).val(objData.revbrut);
			$('#CoutDepotoire_' + NoDemande).val(objData.depense);
			$('#surpluspoids_' + NoDemande).val(objData.surpluspoids);
			$('#prixsurpluspoids_' + NoDemande).val(objData.prixsurpluspoids);
			$('#SurplusLocation_' + NoDemande).val(objData.surpluslocation);
		});
	}
}
function showContenant(value) {
	document.location = "#tr_" + value;
}

function sendPostMail(noligne) {
	var urlName = '/ville/send/post/' + $("#idville").val() + "/" + noligne +
		"?email=" + encodeURIComponent($("#inputEmail" + noligne).val()) +
		"&pass=" + encodeURIComponent($("#inputPassword" + noligne).val());
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		alert(decodeURIComponent(msg));
	});
}
//----------------------------------------------------------------------------
function showRequete(id) {
	var urlName = "/requete/send/" + id;
	window.open(urlName, "",
		"toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" +
		screen.width + ",height=" + screen.height);
}
function showListRequetes() {
	var urlName = "/villes/liste/requetes";
	document.location = urlName;
	//window.open(urlName, "_blank",
	//	"toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" +
	//	screen.width + ",height=" + screen.height);
}
function GoBackWithRefresh(event) {
	if ('referrer' in document) {
		window.location = document.referrer;
		/* OR */
		//location.replace(document.referrer);
	} else {
		window.history.back();
	}
}

function sendFile(image) {
	var data = new FormData();
	data.append("image", image);
	//if you are using CI 3 CSRF
	$.ajax({
		data: data,
		type: "POST",
		url: "/villes/summernoteUpload",
		cache: false,
		contentType: false,
		processData: false,
		success: function (url) {
			//alert(url);
			var image = url;
			$('#description').summernote("insertImage", image);
		},
		error: function (data) {
			//alert(data);
			console.log(data);
		}
	});
}

function showModalDiscussions(idrequete) {
	var urlName = "/villes/show/reponse/" + idrequete;
	
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//---------------
		//$('#divRepAjout').html(msg);
		$('#confirme_add').modal('show');
	});
}

function SendInfoVille() {
	document.location = "/villes/inscription/getinfo?email=" + $("#mailModal").val();

}
function setInfoStatut(value) {
	$('#divDte').hide();
	$('#divAdr').hide();
	$('#divBac').hide();
	$('#divMat').hide();
	$("#dteCedule").prop('required', false);
	$("#adresse").prop('required', false);
	$("#noSerie").prop('required', false);
	$("#grandeur").prop('required', false);
	$("#couleur").prop('required', false);
	$("#matiere").prop('required', false);
	if ((value > 2) && (value < 6))
	{
		$('#divDte').show();
		$("#dteCedule").prop('required', true);
	}
	else if (value == 8) {
		$('#divAdr').show();
		$("#adresse").prop('required', true);
	}
	else if (value == 9) {
		$('#divBac').show();
		$("#noSerie").prop('required', true);
		$("#grandeur").prop('required', true);
		$("#couleur").prop('required', true);
	}
	else if (value == 10) {
		$('#divMat').show();
		$("#matiere").prop('required', true);
	}
}

function changeDate(value,idrequete,idville,idreponse) {
	if (confirm('Etes-vous certain de vouloir modifier la date d\'execution pour ' + value +'.')) {
		var urlName = "/villes/post-dtecedule/" + idrequete + '/' + idreponse + '/' + idville + '/' + value;
		document.location = urlName;
	}
}
function confirmeDate(idrequete) {
	var urlName = "/villes/confirm/date/" + idrequete;

	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//---------------
		$('#divFooterSan').html(msg);
	});
}
function postNewAdd(idrequete,idville) {
	if (confirm('Etes-vous certain de vouloir modifier l\'adresse pour ' + $("#adresse_" + idrequete).val())) {
		var urlName = "/villes/adresse/" + idrequete + '/' + idville + '/' + encodeURIComponent($("#adresse_" + idrequete).val());
		document.location = urlName;
	}
}
function setNomVille(value) {
	var urlName = "/villes/getnom/" + value;

	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$('#nomVille').html(msg);
	});
}
function GoogleVille() {
	var emplacement = $('#adresse').val();
	emplacement = emplacement.toLocaleUpperCase();
	var ville = $('#nomVille').html(); 
	ville = ville.toLocaleUpperCase();
	if (emplacement.trim() != '') {
		if (emplacement.indexOf(ville) == -1) {
			emplacement = ', ' + ville;
		}
		var urlName = 'https://www.google.com/maps/dir/?api=1&dir_action=navigate&destination=' + emplacement + '&travelmode=driving&key=AIzaSyBe729IJy8b9F55IVM3zz4P2bpTM1zFgE4';

		window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
	}

}
//---------------------------------------------- PDF

function PrintExcelRequete() {
	var activeRH = (($("#chkShowTraitees").is(':checked')) ? 1 : 0);
	$('html,body').css('cursor', 'wait');
	var urlName = "/villes/requeteexcel?idville=" + $("#idville").val() + '&istraitees=' + activeRH +
		'&dtestart=' + $("#start").val() + '&dteend=' + $("#end").val() + '&from=1';
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$('html,body').css('cursor', 'pointer');
		$("#divModalPDF").html(msg);
		$("#showDownloadRepPDF").modal('show');
	});
}
function closePrintReq(){
	$("#showDownloadRepPDF").modal('hide');
}
function PrintRequete() {
	var activeRH = (($("#chkShowTraitees").is(':checked')) ? 1 : 0);
	var urlName = "/villes/requetepdf?idville=" + $("#idville").val() + '&istraitees=' + activeRH +
		'&dtestart=' + $("#start").val() + '&dteend=' + $("#end").val() + '&from=1';
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != 'none') {
			$("#divModalPDF").html(msg);
			$("#showDownloadRepPDF").modal('show');
		}
	});
}
function PrintReponse(idrequete) {
	var urlName = "/villes/reponsepdf/" + idrequete;
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$("#divModalPDF").html(msg);
		$("#showDownloadRepPDF").modal('show');
	});
}
function closePrintRep(){
	$("#showDownloadRepPDF").modal('hide');
}
function LimitVille(idville) {
	var activeRH = (($("#chkShowTraitees").is(':checked')) ? 1 : 0);
	var urlName = "/villes/liste/requetes?idville=" + idville + '&istraitees=' + activeRH +
		'&dtestart=' + $("#start").val() + '&dteend=' + $("#end").val() + '&from=1';
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$("#tboDelCom").html(msg);
	});
}
function showHideVillesTraitees(lang) {
	var activeRH = (($("#chkShowTraitees").is(':checked')) ? 1 : 0);
	var urlName = "/villes/requete/liste/" + lang + "?idville=" + $("#idville").val() + '&istraitees=' + activeRH +
		'&dtestart=' + $("#start").val() + '&dteend=' + $("#end").val() + '&from=1';
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$("#tboDelCom").html(msg);
	});
}
function showHideTraitees() {
	var activeRH = (($("#chkShowTraitees").is(':checked')) ? 1 : 0);
	var urlName = "/villes/liste/requetes?idville=" + $("#idville").val() + '&istraitees=' + activeRH +
		'&dtestart=' + $("#start").val() + '&dteend=' + $("#end").val() + '&from=1';
	$.ajax({
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$("#tboDelCom").html(msg);
	});
}
function showRowReq(iter) {
	switch (iter) {
		case '1':
			if (LastRow < ActiveRow + 1) {
				return false;
			}
			else {
				$('#trreq_' + ActiveRow).hide();
				ActiveRow = ActiveRow + 1;
				$('#trreq_' + ActiveRow).show();
			}
			break;
		case '-1':
			if (ActiveRow - 1 < 1) {
				return false;
			}
			else {
				$('#trreq_' + ActiveRow).hide();
				ActiveRow = ActiveRow - 1;
				$('#trreq_' + ActiveRow).show();
			}
			break;
	}
}
function addRoute() {
	var ligneToAdd = '<tr id="tr_0"><td><label style="font-size: 14px;">NON</label></td>' +
		'<td align="center"> <input class="form-control" style="width: 230px" id="edtNom_0" type="text" value="" /></td>' +
		'<td align="center"><input class="form-control" style="width: 400px" id="edtDescription_0"' +
		'type="text" value="" /></td>' +
		'<td  style="max-width:90px" align="center"><a class="btn btn-info" title="Cliquer pour creer ou afficher la route" disabled>' +
		'<i class="fa fa-road" style="font-size:16px;"></i> Route</a></td>' +
		'<td style="max-width:90px"  align="center"><a  title="Cliquer pour enregistrer" onclick="postRoute(0)">' +
			'<i class="fa fa-check btn btn-success" style="font-size:16px;"></i></a ></td></tr>';
	$('#tboRoutes tr:last').after(ligneToAdd);
}
function getTypeRoute(idville) {
	var urlName = '/sanivan/route/' + idville;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$("#divDropRoute").html(msg);
	});
}
function updateInfoRequete() {
	var urlName = "/villes/getinforeq";
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			$("#divRepNewAjout").html(msg);
			$("#newActionModal").modal('show');
		}
		
	});
	//

}
function setRepReqVue(idville) {
	var urlName = '/villes/confirm/' + idville;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		document.location = '/villes/requete/liste'
	});
}

function EnterearchTable(value, e) {

	var keycode = e.keyCode ? e.keyCode : e.charCode;
	if (keycode == 188) {
		var lng = value.length - 1;
		let value = value.substring(0, lng) + "%";
		$("#edtFind").val(value) ;
	}
	if (keycode == 13) {
		var urlName = "/bureau/recherche_commande?valuetofind=" + encodeURIComponent(value);
		$.ajax({
			type: "POST",
			url: urlName
		}).done(function (msg) {
			if (msg != "") {
				var objData = JSON.parse(msg);
				$("#tboclients").html(objData.listclients);
				$('#td_' + objData.idclient).removeClass("Actifclient");
				$("#ulListeCommandes").html(objData.dopcommande);
				$('#divlistdem135').html(objData.listdem135);
				AddNew();
				AddNewClient();
				SetActiveCommande(objData);
				SetActiveClient(objData);
				$("#tbocommandes").html(objData.listcommandes);
				AjusteButton('0');
				AjusteButtonClient('0');
			}
			else {
				alert('Aucun résultat pour votre recherche');
				AjusteButton('0');
				AjusteButtonClient('0');
			}
		});
	}
}
//--------------------------------------------------- ajout 20240805
function setEnTransit() {
	var entransit = ($("#chkTransit").prop('checked') == true) ? 1 : 0;
	if ($("#chkTransit").prop('checked') == true) {
		$("#lbltransit").css("background-color", "yellow");
	}
	else {
		$("#lbltransit").css("background-color", "white");
	}
	var urlName = '/rolloff/settransit?idcommande=' + $("#idlignecom").val() + '&boiteenplace=' + $("#selActifBoiteEnPlace").val() + '&entransit=' + entransit;
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//------------------
	});

}
function setBoiteEP() {
	var urlName = '/rolloff/setboiteep/' + $("#selActifBoiteEnPlace").val() + '/' + $("#idlignecom").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//-----------------------
	});

}
function transfertToCedule(id, typeced, groupe) {
	var urlName = '/sanivan/tranfertligne/' + id + '/' + typeced + '/' + groupe + '/' + $("#dteActivePage1").val();
	document.location = urlName;
}
//-------------------------------------------------------------------------------
function getInfoContenant(value) {
	if ((value == 10) || (value == 11)) {
		$("#divTrim").show();
	}
	else {
		$("#divTrim").hide();
	}
	var urlName = "/bureau/infocontenant/" + value + '?codeclient=' + $("#codeclient").val() + '&idclient=' + $("#clientid").val();
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			var objData = JSON.parse(msg);
			// INFO Contenant
			// prix, redevance, carburant, carburant_site, demolition, asphalte, beton, beton_arme, tonnage
			// CHAMPS Commande
			// Tonnage, Prix, Redevance, gazeline, carburant_site, fraisdistance, fraisdemolition, PrixVariation, AutreFrais
			$("#IsBeton").prop('checked', false);
			$("#IsBetonArme").prop('checked', false);
			$("#IsFumier").prop('checked', false);
			$("#IsBranches").prop('checked', false);
			$("#IsAsphalte").prop('checked', false);
			$("#IsTerre").prop('checked', false);
			$("#IsDechets").prop('checked', false);
			$("#IsBois").prop('checked', false);
			$("#IsMateriaux").prop('checked', false);
			$("#IsBardeau").prop('checked', false);
			$("#IsAmiante").prop('checked', false);
			$("#IsMetal").prop('checked', false);
			$("#IsCompost").prop('checked', false);
			$("#IsSapins").prop('checked', false);
			$("#IsOrganique").prop('checked', false);
			$("#IsRecBois").prop('checked', false);
			$("#IsCarton").prop('checked', false);
			$("#IsVerre").prop('checked', false);
			$("#IsDemolition").prop('checked', false);
			//---------------------------------------------
			$("#IsBeton").prop('disabled', true);
			$("#IsBetonArme").prop('disabled', true);
			$("#IsBardeau").prop('disabled', true);
			$("#IsDemolition").prop('disabled', true);
			switch (parseInt(value)) {
				case 1:
				case 14:
					$("#IsBeton").prop('disabled', false);
					$("#IsBetonArme").prop('disabled', false);
					$("#IsBardeau").prop('disabled', false);
					break;
				case 2:
					$("#IsBardeau").prop('disabled', false);
					break;
				case 6:
					$("#IsDemolition").prop('disabled', false);
					break;
				default:
					$("#IsBeton").prop('disabled', true);
					$("#IsBetonArme").prop('disabled', true);
					$("#IsBardeau").prop('disabled', true);
					$("#IsDemolition").prop('disabled', true);
					break;
			}
			var codeclie = parseInt($("#codeclient").val());

			$("#Tonnage").val(objData.tonnage);
			if (codeclie == 65) {
				$('#tdTonage').find('a, input, select, button, textarea').prop('disabled', true);
			}
			$("#Prix").val(objData.prix);
			$("#Redevance").val(objData.redevance);
			$("#gazeline").val(objData.carburant);
			$("#carburant_site").val(objData.carburant_site);
			$("#fraisdemolition").val(0);
			CalculPrix();
		}
	});
}
//------------------------
function SubmitGPX() {
	document.getElementById("frmGPX").action = '/sanivan/loadgpx';
	document.getElementById("frmGPX").submit();
}
function ShowModalGPX() {  
	var urlName = '/sanivan/getvillesecteur';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg.trim() != "") {
			
			var objData = JSON.parse(msg);
            $("#selVilleModal").html(objData.villes);
            $("#selSecteurModal").html(objData.secteurs);
	        $('#modalAddGPX').modal("show");
		}
	});

}
function SubmitFromLoc() {
	var info = $("#frmFromLoc").serialize();
    alert(info);
	document, location = '/sanivan/postloctopoly?' + info;
}
function ShowModalFromLoc() {  
	var urlName = '/sanivan/getvillesecteur';
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg.trim() != "") {
			
			var objData = JSON.parse(msg);
            $("#selVilleFromLoc").html(objData.villes);
			$("#selSecteurFromLoc").html(objData.secteurs);
			$("#selChauffeurFromLoc").html(objData.chauffeurs);
			$('#modalAddFromLocation').modal("show");
		}
	});

}
function ExecuteTolerance(id) {
    var urlName = '/sanivan/executeTolerance/' + id;
    document.location = urlName;
    //$.ajax({
    //    async: false,
    //    type: "POST",
    //    url: urlName
    //}).done(function (msg) {
    //    if (msg.trim() != "") {
    //        alert(msg);
    //        $("#lblParcoursReduit_" + id).html(msg);
    //    }
    //});
}
function OpenLocalisation(date) {
    var urlName = '/rolloff/routecam/' + date + "?idchauffeur=0";
    window.open(urlName, "_blank",
        "toolbar=yes,scrollbars=yes,resizable=yes,top=500", `width=${screen.availWidth},height=${screen.availHeight}`);
}
//--------------------------------------------------------------------
// Variables globales pour la carte des parcours
var polylineMapCedule = null;
var polylineLayersGroupCedule = null;
function testTogglePolyline() {
	var idcedule = $('#modalPolylineIdCedule').val();
	var idsecteur = $('#selModalSecteur').val();

	console.log('=== TEST ===');
	console.log('idcedule:', idcedule);
	console.log('idsecteur:', idsecteur);

	if (!idcedule) {
		alert('Aucune cédule sélectionnée !');
		return;
	}

	if (!idsecteur || idsecteur == 0) {
		alert('Aucun secteur sélectionné !');
		return;
	}

	// Test d'insertion manuelle
	$.ajax({
		url: '/sanivan/togglePolylineCedule',
		type: 'POST',
		data: {
			idcedule: idcedule,
			idpolyline: 1, // ID de test
			isChecked: 'true'
		},
		dataType: 'json',
		success: function (response) {
			console.log('Réponse test:', response);
			if (response.ok) {
				alert('✅ Test réussi ! L\'insertion fonctionne.');
			} else {
				alert('❌ Test échoué : ' + response.error);
			}
		},
		error: function (xhr) {
			console.error('Erreur test:', xhr.responseText);
			alert('❌ Erreur serveur : ' + xhr.status);
		}
	});
}
function showPolylineMapFromCedule(a, b, c) {
	// Accept either:
	// 1) showPolylineMapFromCedule(idsecteur, nomSecteur)
	// 2) showPolylineMapFromCedule(idcedule, idsecteur, nomSecteur)
	var idcedule = null, idsecteur = null, nomSecteur = '';
	// Cas clair : (idsecteur, nomSecteur)
	if (typeof a === 'number' && typeof b === 'string' && typeof c === 'undefined') {
		idsecteur = a;
		nomSecteur = b;
	}
	// Cas clair : (idcedule, idsecteur, nomSecteur)
	else if (typeof a === 'number' && typeof b === 'number' && typeof c === 'string') {
		idcedule = a;
		idsecteur = b;
		nomSecteur = c;
	}
	else {
		console.error('showPolylineMapFromCedule: signature invalide. Attendu (idsecteur, nomSecteur) ou (idcedule, idsecteur, nomSecteur).', { a: a, b: b, c: c });
		alert('Erreur interne: arguments invalides pour l\'affichage des parcours.');
		return;
	}

	if (!idsecteur || idsecteur === 0) {
		alert('Aucun secteur associé à ce parcours.');
		return;
	}

	$('#spSecteurNameCedule').text(nomSecteur || '');
	$('#modalMapPolylinesCedule').modal('show');

	// Quand le modal est affiché, initialiser la carte et charger les polylines.
	$('#modalMapPolylinesCedule').off('shown.bs.modal').on('shown.bs.modal', function () {
		initPolylineMapCedule(idsecteur, idcedule);
	});
}

/**
 * Initialise la carte Leaflet et charge les polylines
 * @param {number} idsecteur - ID du secteur
 * @param {number|null} idcedule - ID de la cédule (optionnel). Si fourni, n'affiche que les polylines sélectionnées pour cette cédule.
 */
function initPolylineMapCedule(idsecteur, idcedule) {
	// Détruire la carte existante si elle existe
	if (polylineMapCedule !== null) {
		polylineMapCedule.remove();
		polylineMapCedule = null;
	}
	polylineMapCedule = L.map('polylineMapCedule').setView([45.5017, -73.5673], 11);

	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
		maxZoom: 19
	}).addTo(polylineMapCedule);

	polylineLayersGroupCedule = L.layerGroup().addTo(polylineMapCedule);

	// Charger les polylines du secteur ; si idcedule fourni, n'afficher que les sélectionnées
	loadPolylinesForSecteurCedule(idsecteur, idcedule);
}

/**
 * Charge et affiche soit :
 *  - les polylines associées à une cédule (si idcedule fourni)
 *  - sinon, fallback vers getpolylinesgeojson (toutes les polylines du secteur)
 * @param {number} idsecteur
 * @param {number|null} idcedule
 */
function loadPolylinesForSecteurCedule(idsecteur, idcedule) {
	// Choisir la carte active et le groupe de couches le cas échéant
	var map = (typeof polylineMapCedule !== 'undefined' && polylineMapCedule) ? polylineMapCedule : polylineMapInstance;
	var layersGroup = (typeof polylineLayersGroupCedule !== 'undefined' && polylineLayersGroupCedule) ? polylineLayersGroupCedule : null;

	if (!map) {
		console.error('Aucune instance de carte disponible pour afficher les polylines.');
		return;
	}

	// Nettoyer les anciennes couches (sauf les tuiles)
	if (layersGroup) {
		layersGroup.clearLayers();
	} else {
		map.eachLayer(function (layer) {
			if (layer instanceof L.Polyline) {
				map.removeLayer(layer);
			}
		});
	}

	// Choisir le conteneur DOM pour le loader selon la carte
	var $mapDiv = (map === polylineMapCedule) ? $('#polylineMapCedule') : $('#polylineMap');
	$('#mapLoader').remove();
	$mapDiv.append('<div id="mapLoader" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(255,255,255,0.9);padding:20px;border-radius:8px;z-index:1000;"><i class="fa fa-spinner fa-spin fa-3x"></i><br/>Chargement...</div>');

	// Si idcedule valide -> récupérer uniquement les polylines sélectionnées
	if (typeof idcedule !== 'undefined' && idcedule !== null && parseInt(idcedule) > 0) {

		var url = '/sanivan/getselectedpolylines';
		console.log('Requête polylines sélectionnées ->', url, 'idcedule=', idcedule);

		$.ajax({
			url: url,
			type: 'GET',
			data: { idcedule: idcedule },
			dataType: 'json',
			timeout: 20000,
			success: function (response) {
				$('#mapLoader').remove();

				console.log('Réponse getselectedpolylines:', response);

				if (!response || response.ok !== true) {
					console.error('Réponse invalide ou ok!=true', response);
					alert('Erreur lors du chargement des parcours sélectionnés : réponse invalide du serveur.');
					return;
				}

				var polylines = response.polylines || [];
				if (polylines.length === 0) {
					alert('Aucun parcours sélectionné pour cette cédule.');
					return;
				}

				var bounds = L.latLngBounds();
				var colors = ['#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'];

				polylines.forEach(function (poly, index) {
					var coords = poly.coords || [];
					if (!coords || coords.length === 0) return;

					var color = colors[index % colors.length];
					var polyline = L.polyline(coords, {
						color: color,
						weight: 4,
						opacity: 0.9
					});

					if (layersGroup) {
						layersGroup.addLayer(polyline);
					} else {
						polyline.addTo(map);
					}

					var label = '<b>' + (poly.nom || 'Parcours') + '</b><br/>' +
						'Ville: ' + (poly.ville || 'N/A') + '<br/>' +
						'Points: ' + (coords ? coords.length : 0);
					polyline.bindPopup(label);

					try { bounds.extend(polyline.getBounds()); } catch (e) { /* ignore */ }
				});

				if (bounds.isValid()) {
					map.fitBounds(bounds, { padding: [50, 50] });
				}
			},
			error: function (xhr, status, error) {
				$('#mapLoader').remove();
				console.error('Erreur AJAX getselectedpolylines:', {
					status: xhr.status,
					statusText: xhr.statusText,
					responseText: xhr.responseText,
					error: error,
					readyState: xhr.readyState
				});
				// Message plus explicite pour débogage
				alert('Erreur de communication avec le serveur (parcours sélectionnés).\nStatus: ' + xhr.status + '\n' + (xhr.responseText ? xhr.responseText.slice(0, 400) : error));
			}
		});
	} else {
		// Fallback : charger toutes les polylines du secteur (comportement existant)
		$.ajax({
			url: '/sanivan/getpolylinesgeojson/' + idsecteur,
			type: 'GET',
			dataType: 'json',
			success: function (response) {
				$('#mapLoader').remove();

				if (!response || !response.ok) {
					alert('Erreur lors du chargement des polylines : ' + (response && response.error ? response.error : 'Erreur inconnue'));
					return;
				}

				var polylines = response.polylines || [];
				if (polylines.length === 0) {
					alert('Aucun parcours trouvé pour ce secteur.');
					return;
				}

				var bounds = L.latLngBounds();
				var colors = ['#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'];

				polylines.forEach(function (poly, index) {
					var coords = poly.coords || [];
					if (!coords || coords.length === 0) return;

					var color = colors[index % colors.length];
					var polyline = L.polyline(coords, {
						color: color,
						weight: 4,
						opacity: 0.8
					});

					if (layersGroup) {
						layersGroup.addLayer(polyline);
					} else {
						polyline.addTo(map);
					}

					polyline.bindPopup(
						'<b>' + (poly.nom || 'Parcours') + '</b><br/>' +
						'Ville: ' + (poly.ville || 'N/A') + '<br/>' +
						'Points: ' + (coords ? coords.length : 0)
					);

					try { bounds.extend(polyline.getBounds()); } catch (e) { }
				});

				if (bounds.isValid()) {
					map.fitBounds(bounds, { padding: [50, 50] });
				}
			},
			error: function (xhr, status, error) {
				$('#mapLoader').remove();
				console.error('Erreur AJAX getpolylinesgeojson:', status, error, xhr.responseText);
				alert('Erreur de communication avec le serveur (parcours secteur).');
			}
		});
	}
}

// Nettoyer la carte quand le modal se ferme
$('#modalMapPolylinesCedule').on('hidden.bs.modal', function () {
	if (polylineMapCedule !== null) {
		polylineMapCedule.remove();
		polylineMapCedule = null;
	}
});
// Rafraîchissement fiable à la fermeture du modal de polylines
$('#modalPolyline').off('hidden.bs.modal').on('hidden.bs.modal', function () {
	console.log('[modalPolyline] hidden - démarrage du rafraîchissement');

	// Si une modification a eu lieu (ajout/suppression/repositionnement),
	// faire un reload complet pour garantir l'affichage des parcours
	if (window.polylineChanged === true) {
		console.log('[modalPolyline] polylineChanged=true -> reload complet');
		try {
			sessionStorage.setItem('cedule_scroll', window.scrollY || 0);
			var fcId = $('#modalPolylineIdCedule').val();
			if (fcId) sessionStorage.setItem('cedule_focus', fcId);
		} catch (e) { /* ignore */ }
		// Reconstruire l'URL avec la date active (pas la date d'aujourd'hui)
		// Prioriser ActiveDate (variable globale mise à jour par loadToday)
		var activeDate = (typeof ActiveDate !== 'undefined' && ActiveDate) ? ActiveDate : ($('#dteActivePage1').val() || '');
		console.log('[modalPolyline] activeDate pour reload:', activeDate);
		if (activeDate) {
			window.location.href = '/sanivan/aujourdhui/1/' + activeDate;
		} else {
			window.location.reload();
		}
		return;
	}

	var idcedule = parseInt($('#modalPolylineIdCedule').val() || 0, 10);
	var idsecteur = parseInt($('#modalPolylineIdSecteur').val() || 0, 10);

	// Tentatives locales (non bloquantes)
	try {
		if (idcedule > 0 && typeof loadSelectedPolylines === 'function') {
			console.log('[modalPolyline] loadSelectedPolylines(', idcedule, ')');
			loadSelectedPolylines(idcedule);
		}
	} catch (e) {
		console.warn('[modalPolyline] erreur loadSelectedPolylines', e);
	}

	try {
		if (typeof loadPolylinesBySecteurModal === 'function' && idsecteur > 0) {
			console.log('[modalPolyline] loadPolylinesBySecteurModal() for secteur', idsecteur);
			loadPolylinesBySecteurModal();
		}
	} catch (e) {
		console.warn('[modalPolyline] erreur loadPolylinesBySecteurModal', e);
	}

	// Si la vue principale est visible, demander la mise à jour côté serveur
	if ($('#divCedule').length) {
		var actifDate = $('#dteActivePage1').val() || '';
		var url = '/sanivan/aujourdhui/4/' + actifDate + '?activedrop=' + (window.ActiveDrop || 1) +
			'&typecedule=' + (window.activecedule || 1) + '&limit=' + (window.limitcedule || 0);

		console.log('[modalPolyline] requête de rafraîchissement ->', url);

		$.ajax({
			url: url,
			type: 'POST',
			timeout: 15000
		}).done(function (data) {
			try {
				// Le serveur renvoie généralement une chaîne JSON -> parser comme les autres fonctions du fichier
				var obj = (typeof data === 'object') ? data : JSON.parse(data);

				if (obj && obj.today) {
					console.log('[modalPolyline] réponse OK - mise à jour #divCedule');
					$('#divCedule').html(obj.today);

					if (obj.spRearEnd !== undefined) $('#spRearEnd').html(obj.spRearEnd);
					if (obj.nbRobot !== undefined) $('#spRobot').html(obj.nbRobot);
					if (obj.nbAllCam !== undefined) $('#spTotalCam').html(obj.nbAllCam);
					if (obj.CamionDrop !== undefined) $('#divDropCamion').html(obj.CamionDrop);
					if (obj.ChauffeurDrop !== undefined) $('#divDropRH').html(obj.ChauffeurDrop);

					// conserver cochages éventuels (existant dans la réponse)
					if (obj.limitcedule === "1") {
						$("#chkRearLoad").prop('checked', true);
					} else if (obj.limitcedule === "2") {
						$("#chkRobot").prop('checked', true);
					}
				} else {
					console.warn('[modalPolyline] réponse inattendue:', obj);
					// fallback léger : tenter la fonction locale connue
					if (typeof loadTodayCR === 'function') {
						try { loadTodayCR(); return; } catch (e) { console.warn('loadTodayCR failed', e); }
					}
				}
			} catch (e) {
				console.error('[modalPolyline] impossible de parser la réponse', e);
				if (typeof loadTodayCR === 'function') {
					try { loadTodayCR(); return; } catch (err) { console.warn('loadTodayCR failed', err); }
				}
			}
		}).fail(function (xhr, status, error) {
			console.error('[modalPolyline] échec rafraîchissement', status, error, xhr && xhr.responseText ? xhr.responseText.slice(0, 400) : '');
			// fallback raisonnable : utiliser la fonction existante si disponible, sinon reloader
			if (typeof loadTodayCR === 'function') {
				try {
					console.log('[modalPolyline] fallback -> loadTodayCR()');
					loadTodayCR();
					return;
				} catch (e) {
					console.warn('[modalPolyline] loadTodayCR a échoué', e);
				}
			}
			// dernier recours (optionnel) : recharger la page
			console.log('[modalPolyline] fallback ultime -> location.reload()');
			setTimeout(function () { location.reload(); }, 200);
		});

	} else {
		console.log('[modalPolyline] #divCedule absent — pas de mise à jour nécessaire');
	}
});
// Nettoyer la carte quand le modal se ferme
$('#modalMapPolylinesCedule').on('hidden.bs.modal', function () {
	if (polylineMapCedule !== null) {
		polylineMapCedule.remove();
		polylineMapCedule = null;
	}
});
//------------------------------------------------------------------------