// JavaScript source code
var typederamassage = 1;

function getVille(value) {
    if (value == '') {
        $('#btnDeploie').prop("disabled", true);
        $('#btnPost').prop("disabled", true);
        $("#nbRearEnd").val('');
        $("#nbRobot").val('');
        $('#btnPrevAnnee').prop("disabled", true);
        $('#optAnnee').prop("disabled", true);
        $('#btnNextAnnee').prop("disabled", true);
        $('#btnPrevMois').prop("disabled", true);
        $('#optMois').prop("disabled", true);
        $('#btnNextMois').prop("disabled", true);

    }
    else {
        var cam = $('input[name="rad"]:checked').val();
        var urlName = "/sanivan/getinfoville?idville=" + value + "&selectedDay=" +
            $("#optAnnee").val() + '-' + $("#optMois").val() + '-01&typederamassage=' +
            $("#optRamassages").val() + "&typecam=" + cam + '&from=1&moisseul=' + moisseul;
        $.ajax({
            async: false,
            type: "POST",
            url: urlName
        }).done(function (msg) {
            if (msg == "") {
                alert('Une erreur est survenue. Recommencer.');
            }
            else {
                var objData = JSON.parse(msg);
                if (moisseul == 1) {
                    $("#calendar").html(objData.calendrier);
                }
                else {
                    $("#calendar1").html(objData.calendrier1);
                    $("#calendar2").html(objData.calendrier2);
                    $("#calendar3").html(objData.calendrier3);
                    $("#calendar4").html(objData.calendrier4);
                    $("#calendar5").html(objData.calendrier5);
                    $("#calendar6").html(objData.calendrier6);
                    $("#calendar7").html(objData.calendrier7);
                    $("#calendar8").html(objData.calendrier8);
                    $("#calendar9").html(objData.calendrier9);
                    $("#calendar10").html(objData.calendrier10);
                    $("#calendar11").html(objData.calendrier11);
                    $("#calendar12").html(objData.calendrier12);
                    $("#divRevenu").html(objData.revenu);
                    //$("#liNomenclature").html(objData.nomenclature);
                }
            }
        });
    }

}
function dropCollecte(ev, jour) {
    ev.preventDefault();
    var arrData = ev.dataTransfer.getData("text").split('_');
    var datetopost = arrData[1];
    var idram = arrData[2]; 
    var urlName = "/sanivan/transfert/collecte/" + datetopost + '/' + idram + '/' + jour +
        '?manuel=' + (($("#chkManuel").is(':checked')) ? 1 : 0);
    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg == "") {
            alert('Une erreur est survenue. Recommencer.');
        }
        else {
            var objData = JSON.parse(msg);
            if (moisseul == 1) {
                $("#calendar").html(objData.calendrier);
            }
            else {
                $("#calendar1").html(objData.calendrier1);
                $("#calendar2").html(objData.calendrier2);
                $("#calendar3").html(objData.calendrier3);
                $("#calendar4").html(objData.calendrier4);
                $("#calendar5").html(objData.calendrier5);
                $("#calendar6").html(objData.calendrier6);
                $("#calendar7").html(objData.calendrier7);
                $("#calendar8").html(objData.calendrier8);
                $("#calendar9").html(objData.calendrier9);
                $("#calendar10").html(objData.calendrier10);
                $("#calendar11").html(objData.calendrier11);
                $("#calendar12").html(objData.calendrier12);
                $("#divRevenu").html(objData.revenu);
            }
        }
    });
}
function dropCollecteForDel(ev) {
    ev.preventDefault();
    var arrData = ev.dataTransfer.getData("text").split('_');
    var datetopost = arrData[1];
    var idram = arrData[2]; 
    var urlName = "/sanivan/remove/collecte/" + datetopost + '/' + idram +
        '?manuel=' + (($("#chkManuel").is(':checked')) ? 1 : 0);
    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg == "") {
            alert('Une erreur est survenue. Recommencer.');
        }
        else {
            var objData = JSON.parse(msg);
            if (moisseul == 1) {
                $("#calendar").html(objData.calendrier);
            }
            else {
                $("#calendar1").html(objData.calendrier1);
                $("#calendar2").html(objData.calendrier2);
                $("#calendar3").html(objData.calendrier3);
                $("#calendar4").html(objData.calendrier4);
                $("#calendar5").html(objData.calendrier5);
                $("#calendar6").html(objData.calendrier6);
                $("#calendar7").html(objData.calendrier7);
                $("#calendar8").html(objData.calendrier8);
                $("#calendar9").html(objData.calendrier9);
                $("#calendar10").html(objData.calendrier10);
                $("#calendar11").html(objData.calendrier11);
                $("#calendar12").html(objData.calendrier12);
                $("#divRevenu").html(objData.revenu);
            }
        }
    });
}
function AddRamassage(datetopost) {
    var cam = $('input[name="rad"]:checked').val();
    //----------------------------------------------------------------------------
    var urlName = "/sanivan/postcalendrier/" + $("#optVilles").val() + "?selectedDay=" +
        datetopost + '&typederamassage=' + $("#optRamassages").val() + "&typecam=" + cam +
        '&from=1&moisseul=' + moisseul;
    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg == "") {
            alert('Une erreur est survenue. Recommencer.');
        }
        else {
            //--------------------------------------------
            $("#td_" + datetopost).html(msg);
        }
    });
}
function deploieCalendrier() {
    var cam = $('input[name="rad"]:checked').val();
    //----------------------------------------------------------------------------
    var lun = 0;
    var mar = 0;
    var mer = 0;
    var jeu = 0;
    var ven = 0;
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
    //----------------------------------------------------------------------------
    var freq = $("#optFrequences").val();
    var urlName = "/sanivan/postcaldeploie?idville=" + $("#optVilles").val() + "&selectedDay=" +
        $("#optAnnee").val() + '-' + $("#optMois").val() + '-01&typederamassage=' +
        $("#optRamassages").val() + "&typecam=" + cam + '&from=1&moisseul=' + moisseul +
        '&dtestartdeploie=' + $("#dteStartDeploie").val() +
        '&dteenddeploie=' + $("#dteEndDeploie").val() + "&lun=" + lun + "&mar=" + mar + "&mer=" + mer +
        "&jeu=" + jeu + "&ven=" + ven + "&frequence=" + freq;
    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg == "") {
            alert('Une erreur est survenue. Recommencer.');
        }
        else {
            var objData = JSON.parse(msg);
            //--------------------------------------------
            if (moisseul == 1) {
                $("#calendar").html(objData.calendrier);
            }
            else {
                $("#calendar1").html(objData.calendrier1);
                $("#calendar2").html(objData.calendrier2);
                $("#calendar3").html(objData.calendrier3);
                $("#calendar4").html(objData.calendrier4);
                $("#calendar5").html(objData.calendrier5);
                $("#calendar6").html(objData.calendrier6);
                $("#calendar7").html(objData.calendrier7);
                $("#calendar8").html(objData.calendrier8);
                $("#calendar9").html(objData.calendrier9);
                $("#calendar10").html(objData.calendrier10);
                $("#calendar11").html(objData.calendrier11);
                $("#calendar12").html(objData.calendrier12);
            }
        }
    });
}
function redeployeCalendrier() {
    var urlName = "/sanivan/rePostAllVilles?dtedeb=" + $("#dteStartDeploie").val() +
        "&dtefin=" + $("#dteEndDeploie").val() + "&idville=" + $("#optVilles").val();
    document.location = urlName;
}
//-------------------------------------------------------------------------------
function setManuel() {
    var ismanuel = (($("#chkManuel").is(':checked')) ? 1 : 0);
    if (ismanuel == 1) {
        $("#divDeploye").css('display', 'none');
    }
    else {
        $("#divDeploye").css('display', 'block');
    }
    var cam = $('input[name="rad"]:checked').val();
    var urlName = "/sanivan/getinfoville?idville=" + $("#optVilles").val() + "&selectedDay=" +
        $("#optAnnee").val() + '-' + $("#optMois").val() + '-01&typederamassage=' +
        $("#optRamassages").val() + "&typecam=" + cam + '&from=1&manuel=' + ismanuel + '&moisseul=' + moisseul;
    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg == "") {
            alert('Une erreur est survenue. Recommencer.');
        }
        else {
            var objData = JSON.parse(msg);
            if (moisseul == 1) {
                $("#calendar").html(objData.calendrier);
            }
            else {
                $("#calendar1").html(objData.calendrier1);
                $("#calendar2").html(objData.calendrier2);
                $("#calendar3").html(objData.calendrier3);
                $("#calendar4").html(objData.calendrier4);
                $("#calendar5").html(objData.calendrier5);
                $("#calendar6").html(objData.calendrier6);
                $("#calendar7").html(objData.calendrier7);
                $("#calendar8").html(objData.calendrier8);
                $("#calendar9").html(objData.calendrier9);
                $("#calendar10").html(objData.calendrier10);
                $("#calendar11").html(objData.calendrier11);
                $("#calendar12").html(objData.calendrier12);
            }
        }
    });
}
//-------------------------------------------------------------------------------
function cancelCalendrier() {
    var cam = $('input[name="rad"]:checked').val();
    var urlName = "/sanivan/delcaldeploie?idville=" + $("#optVilles").val() + "&selectedDay=" +
        $("#optAnnee").val() + '-' + $("#optMois").val() + '-01&typederamassage=' +
        $("#optRamassages").val() + "&typecam=" + cam + '&from=1&moisseul=' + moisseul +
        '&dtestartdeploie=' + $("#dteStartDeploie").val() +
        '&dteenddeploie=' + $("#dteEndDeploie").val();
    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg == "") {
            alert('Une erreur est survenue. Recommencer.');
        }
        else {
            var objData = JSON.parse(msg);
            if (moisseul == 1) {
                $("#calendar").html(objData.calendrier);
            }
            else {
                $("#calendar1").html(objData.calendrier1);
                $("#calendar2").html(objData.calendrier2);
                $("#calendar3").html(objData.calendrier3);
                $("#calendar4").html(objData.calendrier4);
                $("#calendar5").html(objData.calendrier5);
                $("#calendar6").html(objData.calendrier6);
                $("#calendar7").html(objData.calendrier7);
                $("#calendar8").html(objData.calendrier8);
                $("#calendar9").html(objData.calendrier9);
                $("#calendar10").html(objData.calendrier10);
                $("#calendar11").html(objData.calendrier11);
                $("#calendar12").html(objData.calendrier12);
            }
        }
    });
}
//-------------------------------------------------------------------------------
function gotoMois() {
    var urlName = "/sanivan/getinfoville?idville=" + $("#optVilles").val() + "&selectedDay=" +
        $("#optAnnee").val() + '-' + $("#optMois").val() + '-01&typederamassage=1' +
        '&typecam=0&from=2&manuel=0&moisseul=1';
    document.location = urlName;
}
function gotoAnnee() {
    var urlName = "/sanivan/getinfoville?idville=" + $("#optVilles").val() + "&selectedDay=" +
        $("#optAnnee").val() + '-' + $("#optMois").val() + '-01&typederamassage=1' +
        '&typecam=0&from=2&manuel=0&moisseul=0';
    document.location = urlName;
}
//-------------------------------------------------------------------------------

function ChangeAnnee(value) {
    var annee = value;
    if (value == 1) {
        annee = parseInt($("#optAnnee").val()) - 1;
    }
    else if (value == 2) {
        annee = parseInt($("#optAnnee").val()) + 1;
    }
    $("#optAnnee").val(annee);
    var ismanuel = (($("#chkManuel").is(':checked')) ? 1 : 0);
    var cam = $('input[name="rad"]:checked').val();
    var urlName = "/sanivan/getinfoville?idville=" + $("#optVilles").val() + "&selectedDay=" +
        $("#optAnnee").val() + '-' + $("#optMois").val() + '-01&typederamassage=' +
        $("#optRamassages").val() + "&typecam=" + cam + '&from=1&manuel=' + ismanuel + '&moisseul=' + moisseul;
    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg == "") {
            alert('Une erreur est survenue. Recommencer.');
        }
        else {
            var objData = JSON.parse(msg);
            if (moisseul == 1) {
                $("#calendar").html(objData.calendrier);
            }
            else {
                $("#calendar1").html(objData.calendrier1);
                $("#calendar2").html(objData.calendrier2);
                $("#calendar3").html(objData.calendrier3);
                $("#calendar4").html(objData.calendrier4);
                $("#calendar5").html(objData.calendrier5);
                $("#calendar6").html(objData.calendrier6);
                $("#calendar7").html(objData.calendrier7);
                $("#calendar8").html(objData.calendrier8);
                $("#calendar9").html(objData.calendrier9);
                $("#calendar10").html(objData.calendrier10);
                $("#calendar11").html(objData.calendrier11);
                $("#calendar12").html(objData.calendrier12);
            }
        }
    });
}
function ChangeMois(value) {
    var mois = value;
    if (value == 'prev') {
        if (parseInt($("#optMois").val()) > 1) {
            mois = parseInt($("#optMois").val()) - 1;
        }
        else if (parseInt($("#optMois").val()) == 1) {
            mois = '12';
            $("#optAnnee").val(parseInt($("#optAnnee").val()) - 1);
        }
    }
    else if (value == 'next') {
        if (parseInt($("#optMois").val()) < 12) {
            mois = parseInt($("#optMois").val()) + 1;
        }
        else if (parseInt($("#optMois").val()) == 12) {
            mois = '01';
            $("#optAnnee").val(parseInt($("#optAnnee").val()) + 1);
        }
    }
    if (parseInt(mois) < 10) {
        mois = '0' + mois;
    }
    $("#optMois").val(mois);
    var ismanuel = (($("#chkManuel").is(':checked')) ? 1 : 0);
    var cam = $('input[name="rad"]:checked').val();
    var urlName = "/sanivan/getinfoville?idville=" + $("#optVilles").val() + "&selectedDay=" +
        $("#optAnnee").val() + '-' + $("#optMois").val() + '-01&typederamassage=' +
        $("#optRamassages").val() + "&typecam=" + cam + '&from=1&manuel=' + ismanuel + '&moisseul=' + moisseul;
    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        if (msg == "") {
            alert('Une erreur est survenue. Recommencer.');
        }
        else {
            var objData = JSON.parse(msg);
            if (moisseul == 1) {
                $("#calendar").html(objData.calendrier);
            }
            else {
                $("#calendar1").html(objData.calendrier1);
                $("#calendar2").html(objData.calendrier2);
                $("#calendar3").html(objData.calendrier3);
                $("#calendar4").html(objData.calendrier4);
                $("#calendar5").html(objData.calendrier5);
                $("#calendar6").html(objData.calendrier6);
                $("#calendar7").html(objData.calendrier7);
                $("#calendar8").html(objData.calendrier8);
                $("#calendar9").html(objData.calendrier9);
                $("#calendar10").html(objData.calendrier10);
                $("#calendar11").html(objData.calendrier11);
                $("#calendar12").html(objData.calendrier12);
            }

        }
    });
}
function toCedule() {
    $("#modalDeploie").modal("show");
}
function showRevenuVille() {
    if ($('#divShowRev').css('display') !== 'none') {
        $('#divShowRev').hide();
    }
    else {
        $('#divShowRev').show();
    }
}
function sendRev(typeRamassage) {

    var urlName = "/sanivan/senrev?idville=" + $("#optVilles").val() +
        "&MontFixe=" + $("#MontFixe_" + typeRamassage).val() +
        "&typederamassage=" + typeRamassage + '&MontRedevance=' +
        $("#MontRedevance_" + typeRamassage).val() + '&MontTonne=' +
        $("#MontTonne_" + typeRamassage).val() + "&inHour=" + $("#inHour_" + typeRamassage).val() + '&Disposition=' + $("#Disposition_" + typeRamassage).val() +
        '&AuBac=' + $("#AuBac_" + typeRamassage).val() + "&nbCollecte=" + $("#nbCollecte_" + typeRamassage).val();

    $.ajax({
        async: false,
        type: "POST",
        url: urlName
    }).done(function (msg) {
        alert(msg);
    });
}
function calculerRevenu() {
    var idville = $("#optVilles").val();
    var dateDeb = $("#dteRevDebut").val();
    var dateFin = $("#dteRevFin").val();
    if (!idville || idville == '0') {
        alert("Veuillez sélectionner une ville.");
        return;
    }
    if (!dateDeb || !dateFin) {
        alert("Veuillez sélectionner les dates de début et de fin.");
        return;
    }
    $("#spanRevMsg").text("Calcul en cours...");
    $.ajax({
        async: true,
        type: "POST",
        url: "/sanivan/calculrevenu",
        data: {
            idville: idville,
            date_debut: dateDeb,
            date_fin: dateFin
        }
    }).done(function (msg) {
        try {
            var obj = JSON.parse(msg);
            if (obj.status === 'ok') {
                $("#divRevResultat").html(obj.html);
                $("#spanRevMsg").css("color", "green").text(obj.message);
            } else {
                $("#spanRevMsg").css("color", "red").text(obj.message);
            }
        } catch (e) {
            $("#spanRevMsg").css("color", "red").text("Erreur lors du calcul.");
        }
    });
}
function voirRevenu() {
    var idville = $("#optVilles").val();
    var dateDeb = $("#dteRevDebut").val();
    var dateFin = $("#dteRevFin").val();
    if (!idville || idville == '0') {
        alert("Veuillez sélectionner une ville.");
        return;
    }
    if (!dateDeb || !dateFin) {
        alert("Veuillez sélectionner les dates de début et de fin.");
        return;
    }
    $("#spanRevMsg").text("Chargement...");
    $.ajax({
        async: true,
        type: "POST",
        url: "/sanivan/getrevresultat",
        data: {
            idville: idville,
            date_debut: dateDeb,
            date_fin: dateFin
        }
    }).done(function (msg) {
        try {
            var obj = JSON.parse(msg);
            if (obj.status === 'ok') {
                $("#divRevResultat").html(obj.html);
                $("#spanRevMsg").css("color", "green").text("");
            } else {
                $("#spanRevMsg").css("color", "red").text(obj.message);
            }
        } catch (e) {
            $("#spanRevMsg").css("color", "red").text("Erreur lors du chargement.");
        }
    });
}