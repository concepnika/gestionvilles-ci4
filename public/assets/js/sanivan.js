// JavaScript - JQuery source code pour Camion Sanivan
window.globalVar = 'Test';
function setWebcamSize() {
	var bodyRect = document.body.getBoundingClientRect();
	var bodyWidth = bodyRect.width;
}

function showBoite(id) {
	if ($("#chkTransbo_" + id).is(':checked')) {
		$("#spBoite_" + id).show();
	}
	else {
		$("#spBoite_" + id).hide();
	}
}
function closeWin() {
	window.close();
}
function chgPoint(value, id) {
	let result = value.replace(".", ",");
	$("#" + id).val(result);
}
function showScanPage(iddepotoire, idinfoville) {
	var depot = $("#depotoire_" + iddepotoire).val();
	if (depot.trim() == '') {
		alert("Vous devez choisir un depotoire.");
		return false;
	}
	var urlName = '/tablettes_san/scan/ticket/' + idinfoville + '/' + depot + '/' + $("#dteActive").val() + '/' + iddepotoire; 
	window.open(urlName, '_blank');
}
function verifDepotBac(id) {
	if (($("#depotoire_" + id).val() != '') && ($("#nbBac_" + id).val() > 0)) {
		$("#btnScanner_" + id).prop('disabled', false);
	}
}
function addDepotoire(idville,idcedule) {
	var urlName = '/tablettes_san/adddepot/' + idville + '/' + idcedule;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (msg) {
		$("#content_" + idville).append(msg);
	});
}
function postDepotoire(id) {
	var boite = (($("#Boite_" + id).val() != '') ? $("#Boite_" + id).val() : 'none');
	var istransbo = (($("#chkTransbo_" + id).is(':checked')) ? 1 : 0);
	var isavecmat = (($("#chkADomper_" + id).is(':checked')) ? 1 : 0);
	var tonnage = $("#Tonnage_" + id).val();
	tonnage = tonnage.replace(".", ",");
	if (tonnage.trim() == '') {
		tonnage = '0';
	}
	var urlName = '/tablettes_san/senddepotinfo/' + id + '/' +
		encodeURIComponent(tonnage) + '/' + encodeURIComponent($("#depotoire_" + id).val()) + '/' +
		encodeURIComponent(boite) + '/' + istransbo + '/' + isavecmat;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		var objData = JSON.parse(data);
		$("#spTicket").html(objData.ticket);
	});
}
function PostVilleSani(idinfoville) {
	var finville = (($("#HreFin_" + idinfoville).val() == '') ? 'none' : $("#HreFin_" + idinfoville).val());	
	var finbac = (($("#nbBac_" + idinfoville).val() == '') ? '0' : $("#nbBac_" + idinfoville).val());	
	var urlName = '/tablettes_san/sendvilleinfo/' + idinfoville + '/' + encodeURIComponent($("#HreDeb_" + idinfoville).val()) + '/' +
		encodeURIComponent(finville) + '/' + finbac;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#divvalid").html(data);
	});
}
function setActiveCedule(idville, id) {
	var urlName = '/tablettes_san/cedule/' + idville + '/' + id;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#lstdepot").html(data);

	});
}
function postCamSani() {
	var fincam = (($("#HreFin_cam").val() == '') ? 'none' : $("#HreFin_cam").val());
	var urlName = '/tablettes_san/sendcaminfo/' + encodeURIComponent($("#NOCAMION").val()) + '/' +
		encodeURIComponent($("#KiloDeb").val()) + '/' +
		encodeURIComponent($("#KiloFin").val()) + '/' + encodeURIComponent($("#Fuel").val()) + '/' +
		$("#dteActive").val() + '/' + $("#idcamjour").val() + '/' + encodeURIComponent($("#HreDeb_cam").val()) + '/' +
		encodeURIComponent(fincam);
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#divvalid").html(data);
	});
}

function PostChaufSani() {
	var finchauf = (($("#HreFin").val() == '') ? 'none' : $("#HreFin").val());
	var urlName = '/tablettes_san/sendchaufinfo/' + $("#idchauffeur").val() + '/' + encodeURIComponent($("#HreDeb").val()) + '/' +
		encodeURIComponent(finchauf) + '/0/' + $("#dteActive").val() + '/' + $("#idsanivanrh").val();
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#divvalid").html(data);
	});
}
function PostChaufExtra() {
	var finchauf = (($("#HreFin_extra").val() == '') ? 'none' : $("#HreFin_extra").val());
	var urlName = '/tablettes_san/sendchaufextra/' + $("#idchauffeur").val() + '/' + encodeURIComponent($("#HreDeb_extra").val()) + '/' +
		encodeURIComponent(finchauf) + '/0/' + $("#idtypeextra").val() + '/' + $("#idsanivanrh").val();
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#divvalid").html(data);
	});
}
function PostAideSani(idaide, num, idsani) {
	var finaide = (($("#HreFin_" + num).val() == '') ? 'none' : $("#HreFin_" + num).val());
	var urlName = '/tablettes_san/sendaideinfo/' + idaide + '/' + $("#HreDeb_" + num).val() + '/' +
		finaide + '/0/' + $("#dteActive").val() + '/' + idsani;
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#divvalid").html(data);
	});
}
function setActiveVille(id) {
	$('a').removeClass("btn-primary");
	$('#btn_' + id).removeClass("btn-outline-secondary");
	$('#btn_' + id).addClass("btn-primary");
}
function ShowTabSan(id) {
	$('#btnRhCam').removeClass("btn-primary");
	$('#btnDepotoire').removeClass("btn-primary");
	$('#btnLocalisation').removeClass("btn-primary");
	//------------------------------------------
	$('#btnRhCam').addClass("btn-outline-info");
	$('#btnDepotoire').addClass("btn-outline-info");
	$('#btnLocalisation').addClass("btn-outline-info");
	//-----------------------------------------
	$('#' + id).removeClass("btn-outline-info");
	$('#' + id).addClass("btn-primary");
	//-----------------------------------------
	$('#divRhCam').hide();
	$('#divDepotoire').hide();
	$('#divLocalisation').hide();
	//-----------------------------------------
	switch (id) {
		case 'btnRhCam':
			$('#divRhCam').show();
			break;
		case 'btnDepotoire':
			$('#divDepotoire').show();
			break;
		case 'btnLocalisation':

			$('#divLocalisation').show();
			break;
	}
}
//-------------------------------------------------------------------------
function getRteInfo(id) {
	var urlName = '/tablettes_san/inforoute/' + id + '?from=1';
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#divvalid").html(data);

	});
	$('a').removeClass("btn-primary");
	$('#btn_' + id).removeClass("btn-outline-secondary");
	$('#btn_' + id).addClass("btn-primary");
}
//-------------------------------------------------------------------------
function sendInfoCam() {
	var urlName = '/tablettes_san/sendcaminfo/' + $("#NOCAMION").val() + '/' + $("#kiloDeb").val() + '/' + $("#kiloFin").val()
		+ '/' + $("#Fuel").val() + '/' + $("#dteActive").val();
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#divvalid").html(data);
	});
}
//-------------------------------------------------------------------------
function ScanTicket(id) {
	var urlName = '/tablettes_san/scan/ticket/' + id;

}
//-------------------------------------------------------------------------
function newcalc(id, idcam, from) {
	var test = $("#Tonnage1_" + idcam).val();
	var tonnage1 = ((test.replace('.', '_') == '') ? '0' : test.replace('.', '_'));
	var ticket1 = (($("#Ticket1_" + idcam).val() == '') ? ' ' : $("#Ticket1_" + idcam).val());
	var depotoire1 = (($("#depotoire1_" + idcam).val() == '') ? ' ' : $("#depotoire1_" + idcam).val());
	test = $("#kiloDeb_" + idcam).val();
	var kiloDeb = test.replace('.', '_');
	test = $("#kiloFin_" + idcam).val();
	var kiloFin = test.replace('.', '_');
	test = $("#Fuel_" + idcam).val();
	var Fuel = test.replace('.', '_');
	test = $("#Tonnage2_" + idcam).val();
	var tonnage2 = ((test.replace('.', '_') == '') ? '0' : test.replace('.', '_'));
	var depotoire2 = (($("#depotoire2_" + idcam).val() == '') ? ' ' : $("#depotoire2_" + idcam).val());
	var ticket2 = (($("#Ticket2_" + idcam).val() == '') ? ' ' : $("#Ticket2_" + idcam).val());
	test = $("#Tonnage3_" + idcam).val();
	var tonnage3 = ((test.replace('.', '_') == '') ? '0' : test.replace('.', '_'));
	var depotoire3 = (($("#depotoire3_" + idcam).val() == '') ? ' ' : $("#depotoire3_" + idcam).val());
	var ticket3 = (($("#Ticket3_" + idcam).val() == '') ? ' ' : $("#Ticket3_" + idcam).val());
	test = $("#Tonnage4_" + idcam).val();
	var tonnage4 = ((test.replace('.', '_') == '') ? '0' : test.replace('.', '_'));
	var depotoire4 = (($("#depotoire4_" + idcam).val() == '') ? ' ' : $("#depotoire4_" + idcam).val());
	var ticket4 = (($("#Ticket4_" + idcam).val() == '') ? ' ' : $("#Ticket4_" + idcam).val());
	var urlName = '/sanivan/postcam/' + kiloDeb + '/' + kiloFin
		+ '/' + Fuel + '/' + ticket1 + '/' +
		tonnage1 + '/' + (($("#debut1_" + idcam).val() == '') ? ' ' : $("#debut1_" + idcam).val()) + "/" + (($("#fin1_" + idcam).val() == '') ? ' ' : $("#fin1_" + idcam).val()) + "/" +
		ticket2 + '/' + tonnage2 + '/' + (($("#debut2_" + idcam).val() == '') ? ' ' : $("#debut2_" + idcam).val()) + "/" + (($("#fin2_" + idcam).val() == '') ? ' ' : $("#fin2_" + idcam).val()) + "/" +
		ticket3 + '/' +
		tonnage3 + '/' + (($("#debut3_" + idcam).val() == '') ? ' ' : $("#debut3_" + idcam).val()) + "/" + (($("#fin3_" + idcam).val() == '') ? ' ' : $("#fin3_" + idcam).val()) + "/" +
		ticket4 + '/' +
		tonnage4 + '/' + (($("#debut4_" + idcam).val() == '') ? ' ' : $("#debut4_" + idcam).val()) + "/" + (($("#fin4_" + idcam).val() == '') ? ' ' : $("#fin4_" + idcam).val()) + "/" + id + "/" + idcam + "/" + $("#dteCalcul").val() + "/" + from + "?depotoire1=" + $("#depotoire1_" + idcam).val()
		+ "&depotoire2=" + $("#depotoire2_" + idcam).val() + "&depotoire3=" + $("#depotoire3_" + idcam).val() + "&depotoire4=" + $("#depotoire4_" + idcam).val();
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#divvalid").html(data);
	});
}
function newcalcsuivis(id) {
	var deb = $("#StartTime_" + id).val();
	var fin = $("#EndTime_" + id).val();
	var dindeb = $("#dinerdeb_" + id).val();
	var urlName = '/sanivan/postsuivis/' + deb + '/' + fin + '/' + dindeb + '/' + id + '/' + $("#dteCalcul").val();
	$.ajax({
		url: urlName,
		context: document.body
	}).done(function (data) {
		$("#divvalid").html(data);

	});
}
function CalculJour() {
	var urlName = "/sanivan/jour_validation/" + $('#dteCalcul').val();
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$("#divvalid").html(msg);
	});
}
function CalculJourChauffeur() {
	var urlName = "/sanivan/jour_validation_chauffeur/" + $('#dteCalcul').val();
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		$("#divvalid").html(msg);
	});
}
//-----------------------------------------------------------------------------------------------------
function getCamSani() {
	var urlName = "/tablettes_san/camion/suivis/" + $("#edtcamion").val();
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var obj = JSON.parse(msg);
		$("#edtkmDepart").val(parseInt(obj.KiloDeb));
		if (Number(obj.KiloDeb) > 0) {
			$("#btnLogin").prop('disabled', false);
		}
		$("#edtKMFin").val(parseInt(obj.KiloFin));
		$("#edtCarburant").val(parseInt(obj.Fuel));
	});
}
function getSuivisChauffeurs(camion) {
	var urlName = "/tablettes/camion/suivis/" + camion;
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		var obj = JSON.parse(msg);
		$("#edtkmDepart").val(obj.KiloDeb);
		if (Number(obj.KiloDeb) > 0) {
			$("#btnLogin").prop('disabled', false);
		}
		$("#edtKMFin").val(obj.KiloFin);
		$("#edtCarburant").val(obj.Fuel);
	});
}
//------------------------------------------------------------------------------
function VerifKM(value) {
	if ($.isNumeric(value)) {
		if (Number(value) > 0) {
			$("#edtkmDepart").val(parseInt(value));
			PostCamionJour();
			$("#btnLogin").prop('disabled', false);
		}
		else {
			$("#btnLogin").prop('disabled', true);
		}
	}
	else {
		$("#edtkmDepart").val();
		$("#btnLogin").prop('disabled', true);
  }
}
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
function OpenCommande() {
	var urlName = "/tablettes/opencom";
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//--------------
		$("#divInfoLiv").html(msg);
	});
}
//------------------------------------------------------------------------------
function postCamRoues(value) {
	var urlName = "/tablettes/post/7?value=" + value;
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//--------------
		
	});
}
//------------------------------------------------------------------------------
function setcbbBoite(value) {
	//var urlName = "/tablettes/setboite/" + value;
	//$.ajax({
	//	async: false,
	//	type: "POST",
	//	url: urlName
	//}).done(function (msg) {
	//	//--------------
	//	if (msg == 'OK') {
	//		$("#edtBoites").val(value);			
	//		$('#div1').show();
	//		$("#div0").prop('disabled', false);
	//		$("#divRoute24").prop('disabled', false);
	//		$("#divRoute24").show();
	//		$("#div0").show();
	//		$("#div0").prop('disabled', false);
	//		$("#btnTermineLiv").prop('disabled', false);

 //   }
	//});
}
function ValidEmplacement(value) {
	if ((value == '') || ($("#edtBoites").val() == '')) {
		alert('Vous devez choisir une Boite et un Emplacement \npour terminer le transfert.');
		$("#btnTerminerTrans").prop('disabled', true);
		$("#cbbEmplacement").val('');
	}
	else {
		$("#btnTerminerTrans").prop('disabled', false);
	}
}
function TerminerTrans() {
	var boxval = $("#edtBoitesTrans").val();
	var box = boxval.substring(0, 1);

	if (box.toUpperCase() == 'B') {
		boxval = boxval.substring(1, boxval.length);
	}
	var urlName = "/tablettes/terminertrans?boite=" + boxval + '&emplacement=' + $("#cbbEmplacement").val();

	document.location = urlName;
}
function setcbbBoiteEnPlace(value) {
	var urlName = "/tablettes/setboiteenplace/" + value;
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//--------------
		if (msg == 'OK') {
			$("#BoiteEnPlace").val(value);
    }
	});
}
function setBoiteToTrans(value) {
	var urlName = "/tablettes_san/setboite/" + value;
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//--------------
		//if (msg == 'OK') {
		//	$("#edtBoites").val(value);
    //}
	});
}
function setBoiteToTrans(value) {
	$("#edtBoites").val(value);
}
function AjusteMultiLayout(layout) {
	switch (parseInt(layout)) {
		case 1:
			$("#lbl_1").css('color', 'darkred');
			$("#lbl_2").css('color', 'black');
			$("#lbl_3").css('color', 'black');
			$("#lbl_4").css('color', 'black');
			$("#tdVidage").css('display', 'none');
			$("#divBoite").css('display', 'block');
			$("#btnTermineLiv").css('display', 'block');
			break;
		case 2:
			$("#lbl_1").css('color', 'black');
			$("#lbl_2").css('color', 'darkred');
			$("#lbl_3").css('color', 'black');
			$("#lbl_4").css('color', 'black');
			$("#tdVidage").css('display', 'block');
			$("#divBoite").css('display', 'block');
			$("#btnTermineLiv").css('display', 'none');
			break;
		case 3:
			$("#lbl_1").css('color', 'black');
			$("#lbl_2").css('color', 'black');
			$("#lbl_3").css('color', 'darkred');
			$("#lbl_4").css('color', 'black');
			$("#tdVidage").css('display', 'block');
			$("#divBoite").css('display', 'none');
			$("#btnTermineLiv").css('display', 'none');
			break;
		case 4:
			$("#lbl_1").css('color', 'black');
			$("#lbl_2").css('color', 'black');
			$("#lbl_3").css('color', 'black');
			$("#lbl_4").css('color', 'darkred');
			$("#tdVidage").css('display', 'block');
			$("#divBoite").css('display', 'none');
			$("#btnTermineLiv").css('display', 'none');
			break;
	}
	ActiveLayout = layout;
	var urlName = "/tablettes/ajustelayout/" + layout;
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//--------------
		if (msg == 'OK') {
			//-------------------
		}
	});
}
//------------------------------------------------------------------------------
function setEmplacement(value) {
	if (($("#cbbDepotoire").val() == '') || ($("#Poids").val() == '')) {
		alert('Vous selectionner entrer un Depotoire et entrer un Tonnage, avant de selectionner un Emplacement.');
		$("#cbbEmplacement").val('');
	}
	else if (($("#edtBoites").val() == '') && (ActiveLayout != 3) && (typecommande != 2)) {
		alert('Vous devez entrer une BOITE pour terminer votre commande.');
		$("#edtBoites").css("background-color", 'red');
		$("#cbbBoites").css("background-color", 'red');
		$("#cbbEmplacement").val('');
	}
	else if (($("#BoiteEnPlace").val() == '') && (typecommande == 2)){
		alert('Vous devez entrer une BOITE EN PLACE pour terminer votre commande.');
		$("#BoiteEnPlace").css("background-color", 'red');
		$("#cbbBoitesEnPlace").css("background-color", 'red');
		$("#cbbEmplacement").val('');
	}
	else {
		var urlName = "/tablettes/getisscan_ep";
		$.ajax({
			async: false,
			type: "POST",
			url: urlName
		}).done(function (msg) {
			var res = (parseInt(msg) != 1);
			if (res) {
				ReturnAction = 3;
				ShowScanBoxEnPlace();
			}
			else {
				var urlName = "/tablettes/setemplacement?emplacement=" + value + "&noboite=" + $("#BoiteEnPlace").val();
				$.ajax({
					async: false,
					type: "POST",
					url: urlName
				}).done(function (msg) {
					//--------------
					if (msg == 'OK') {
						$('#div1').show();
						$('#div356').show();
						$('#divRoute24').show();
						$('#div7').show();
					}
				});				
			}
		});
		//var urlName = "/tablettes/setemplacement?emplacement=" + value + "&noboite=" + $("#BoiteEnPlace").val();
		//$.ajax({
		//	async: false,
		//	type: "POST",
		//	url: urlName
		//}).done(function (msg) {
		//	//--------------
		//	if (msg == 'OK') {
		//		$('#div1').show();
		//		$('#div356').show();
		//		$('#divRoute24').show();
		//		$('#div7').show();
		//	}
		//});
  }

}
//------------------------------------------------------------------------------

function setHARWOOD(value) {
	if ($("#chkHARWOOD").is(':checked')) {
		if (($("#BoiteEnPlace").val() == '') && (typecommande == 2)) {
			alert('Vous devez entrer une BOITE EN PLACE avant de choisir COUR (2117 HARWOOD).');
			$("#BoiteEnPlace").css("background-color", 'red');
			$("#cbbBoitesEnPlace").css("background-color", 'red');
			$("#cbbEmplacement").val('');
			$("#chkHARWOOD").prop('checked', false);
		}
		else {

			var urlName = "/tablettes/setharwood?ischeck=";
			$.ajax({
				async: false,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				//--------------
				if (msg == 1) {
					document.location = '/tablettes/reload/0';
				}
				else {
					$('#divDepotoire').hide();
					$('#divEmplacement').hide();
					$('#divPoids').hide();
					$('#div1').css('display', 'block');
					$('#div356').css('display', 'block');
					$('#divRoute24').css('display', 'block');
					$('#div7').css('display', 'block');
        }

			});
    }
  }
	else {
		$('#divDepotoire').show();
		$('#divEmplacement').show();
		$('#divPoids').show();
		$('#div1').css('display', 'none');
		$('#div356').css('display', 'none');
		$('#divRoute24').css('display', 'none');
		$('#div7').css('display', 'none');
  }
}
//------------------------------------------------------------------------------
function _updTabletteView() {
	var urlName = "/tablettes/updview";
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg !== "") {
			$("#idinfo").val('');
			$("#idinfo").val(msg);
		}
	});
}
//------------------------------------------------------------------------------
function PostCamionJour() {
	var urlName = "/tablettes/camion/post?camion=" + $("#edtcamion").val() + "&kilofin=0" 
		+ "&kilodeb=" + $("#edtkmDepart").val() + "&carburant=0";
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//--------------
	});
}
//------------------------------------------------------------------------------
function postGeneral() {
	var urlName = '/tablettes/postgen?edtkmDepart=' + $('#edtkmDepart').val() + '&edtCarburant=' + $('#edtCarburant').val() +
		'&edtKMFin=' + $('#edtKMFin').val() + '&StartTime=' + $('#StartTime').val() + '&EndTime=' + $('#EndTime').val();
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//--------------
		if (msg == 'OK') {
			alert('Informations enregistrees');
        }
	});
}
function AjusteBoiteTrans(event) {
	if (event.which == 13) {
		var boxval = $("#edtBoitesTrans").val();
		var box = boxval.substring(0, 1);
		if (box.toUpperCase() == 'B') {
			event.preventDefault();
			$("#edtBoitesTrans").val(boxval.substring(1, boxval.length));
			$("#edtBoitesTrans").prop("readonly", true);
		}
	}
}
function resetScanTrans() {
	$("#edtBoitesTrans").prop("readonly", false);
	$("#edtBoitesTrans").focus();
}

function getInfoCamOnRoad(id) {
	//--------------
	$("#allchauffeur").val(0);
	var arrID = id.split('_');
	//deleteMarkers();
	var urlName = '/sanivan/routeforallcam/' + $("#dtedeb").val() + '?idchauffeur=' + arrID[1] + '&allchauffeur=' + $("#allchauffeur").val();
	document.location = urlName;
	//$.ajax({
	//  url: urlName,
	//  context: document.body
	//}).done(function (data) {
	//  //var str = base_url + 'assets/img/google/cam_sm_' + arrID[2] + '.png';
	//  //$('#ImgTruckSani').attr('src', str);
	//  map.setCenter(data);
	//  deleteMarkers();
	//  addMarkerCam(data);
	//  setMapOnAll(map)
	//});
}
function showMapInfoChauf() {
	$("#infoChauffeur").show();
	$("#map").hide();
}
function showMap() {
	$("#infoChauffeur").hide();
	$("#map").show();
}
function getDateChangeRo(dte) {
	//--------------
	$("#allchauffeur").val(1);
	var urlName = '/sanivan/routeforallcam/' + dte + '?idchauffeur=0&camchauf=&allchauffeur=1';
	document.location = urlName;
}     
//---------------------------------------------------------
function postTextSani(value, id) {
	var urlName = '/tablettes_san/postinfosani/' + value + '/' + id +
		'?idinfoville=' + $("#idinfoville").val() +
		'&idaide=' + $("#HELPERS_" + id.substr(id.length - 1)).val();
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//--------------
		//if (msg == 'OK') {
		//	alert('Informations enregistrees');
		//}
	});
}

//---------------------------------------------------------
function setTabVille(id) {
	var idact = $('#idvilleactive').val();
	$('i').css('color', '');
	$('#star_' + id).css('color', 'blue');
	//------------------
	$('#btnVille_' + idact).removeAttr('style');
	$('#btnVille_' + idact).css('color', 'black');
	//----------------------------------------------------------------
	$('#btnVille_' + id).css('color', 'red');
	$('#btnVille_' + id).css('font-weight', 'bold');
	$('#btnTabVille').html($('#btnVille_' + id).html())
	//----------------------------------------------------------------
	$('#content_' + idact).css('display', 'none');
	$('#content_' + id).css('display', 'block');
	$('#idvilleactive').val(id);
	$('#lblHproduit').html($('#idProduit_' + id).val())
	$('#lblHproduit').css('backgroundColor', $('#tpProduit_' + id).val());
}
//---------------------------------------------------------
function setAvecMat(id, idcedule) {
	var checked = 0;
	if ($("#chkADomper_" + id).is(':checked')) {
		checked = 1;
	}
	var urlName = '/tablettes_san/setAvecMat/' + checked + '/' + id + '/' + idcedule;
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//--------------
		//if (msg == 'OK') {
		//	alert('Informations enregistrees');
		//}
	});
}
function chgChauffeur(value, idcedule) {
	if (($("#HreDeb").val() == '') || ($("#HreFin").val() == ''))
	{
		$("#chgChaufModal").modal('show');
		$("#idchauffeur").val($("#idrh").val());
		return false;
	}
	else {
		if (confirm("Voulez-vous changer de chauffeur?") == true) {
			var urlName = '/tablettes_san/chgchauf/' + value;
			$.ajax({
				async: false,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				$("#HreDeb_cam").val('');
				$("#HreFin_cam").val('');
			});
		}
		else {
			//$("#idchauffeur").val($("#idrh").val());
			return false;
		}
	}
	
}
function chgCamion(value) {
	//$("#chgCamModal").modal('show');
	var kdeb = parseFloat($("#KiloDeb").val());
	var kfin = parseFloat($("#KiloFin").val());
	var hdeb = $("#HreDeb_cam").val();
	var hfin = $("#HreFin_cam").val();
	if ((kdeb < 1) || (kfin < 1) || (hdeb.trim() == '') || (hfin.trim() == '')) {
		$("#chgCamModal").modal('show');
	}
	else {
		if (confirm("Voulez-vous changer de camion?") == true) {
			var urlName = '/tablettes_san/chgcam/' + value;
			$.ajax({
				async: false,
				type: "POST",
				url: urlName
			}).done(function (msg) {
				var obj = JSON.parse(msg);
				$("#KiloDeb").val(parseInt(obj.KiloDeb));
				$("#KiloFin").val(parseInt(obj.KiloFin));
				$("#Fuel").val(parseInt(obj.Fuel));
				$("#HreDeb_cam").val('');
				$("#HreFin_cam").val('');
			});
		}
		else {
			//document.location = '/tablettes_san/routes/travail';
		};
	}

}
function LoadSaniRH(nbjour) {
	var urlName = "/rapport/sani/" + $("#dtedeb").val()+ '/1';
	document.location = urlName;
}
//------------------------------------------------
function addChaufOnRoad() {
	var urlName = "/tablettes_san/add_acces/" + $("#dtedeb").val()
	var a = document.createElement('a');
	a.target = "_blank";
	a.href = urlName;
	a.click();
}
//------------------------------------------------
function createDayRH(value) {
	var urlName = '/tablettes_san/addchauf/' + $("#dtedeb").val() + '/' +  value;
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		if (msg != '') {
			var urlName = '/rapport/sani/' + $("#dtedeb").val() + '/0';
			document.location = urlName;
		}
	});
}
function DownloadFile(ticket) {
	var urlName = '/tablettes_san/download/' + ticket;
	$.ajax({
		async: false,
		type: "POST",
		url: urlName
	}).done(function (msg) {
		//------------------
	});
}