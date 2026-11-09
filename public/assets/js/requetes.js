
/*
Positions
*/
var stack_topleft = { "dir1": "down", "dir2": "right", "push": "top" };
var stack_bottomleft = { "dir1": "right", "dir2": "up", "push": "top" };
var stack_bottomright = { "dir1": "up", "dir2": "left", "firstpos1": 15, "firstpos2": 15 };
var stack_bar_top = { "dir1": "down", "dir2": "right", "push": "top", "spacing1": 0, "spacing2": 0 };
var stack_bar_bottom = { "dir1": "up", "dir2": "right", "spacing1": 0, "spacing2": 0 };
function setDateRequete(noreq) {

}
function setRequeteVue(noreq) {

}
function setReponseVue(noreq) {

}
//----------------------------------------------------------------
function _verifReq() {
	//alert(1);
	clearTimeout(adminTimeout);
	var urlName = "/villes/requetes/verifnewinfo";
	$.ajax({
		async: true,
		type: "POST",
		url: urlName
	}).done(function (data) {
		if (data !== "") {
			var objData = JSON.parse(data);
			if (objData.idreq != '') {
				$('#edtIdReqVille').val(objData.idreq);
				$('#btnModalReq').val(objData.NomVilleReq);
				$('#btnModalReq').css('display', 'block');
			}
			else {
				$('#edtIdReqVille').val('');
				$('#btnModalReq').val('');
				$('#btnModalReq').css('display', 'none');
			}
			//-----------------------------------------
			if (objData.idrep != '') {
				$('#edtIdRepVille').val(objData.idrep);
				$('#btnModalRep').val(objData.NomVilleRep);
				$('#btnModalRep').css('display', 'block');
			}
			else {
				$('#edtIdRepVille').val(objData.idrep);
				$('#btnModalRep').val(objData.NomVilleRep);
				$('#btnModalRep').css('display', 'none');
			}
			if ((objData.idreq != '') || (objData.idrep != '')) {
				$('#modalRequeteWarning').modal('show');
			}
		}
		adminTimeout = setTimeout("_verifReq()", 30000);
	});
}
function showNewRequete() {
	var urlName = '/villes/show/requete/' + $('#edtIdReqVille').val();
	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
}
function showNewReponse() {
	var urlName = '/villes/show/reponse/' + $('#edtIdRep Ville').val();
	window.open(urlName, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=" + screen.width + ",height=" + screen.height);
}