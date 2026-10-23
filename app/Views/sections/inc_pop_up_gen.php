<?php

?>

    <div class="modal fade" id="modalmat" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Camion Avec Matériel&nbsp;!</h3>
                
			</div>
            <div class="modal-body" style="min-height:600px;">
              <form>

                    <div class="form-group">

                      <div div="divMat" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                          <label style="font-weight: bold; "># Camions  </label> <br />
														<h3 id="titrecam"></h3>
                          <select class="form-control" onchange="getTypeCamPopup(this.value, 2)" id="selTypeCamMat">
                            <option value="1" selected>REAR END</option>
                              <option value="2">ROBOT</option>
                           </select>
                            <select class="form-control" name="camion" id="camion" required>
                                <option value="">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                            <input id="idsuiviscam" type="hidden" />
                        </div>     
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Date avec matériel </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dtestart" id="dtestart" />
                        </div>   
                        <div class="col-sm-7 col-xs-12" style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Type de matériel</label> <br />
                            <select class="form-control" name="typemat" id="typemat" required>
                                <option value="">Choisir</option>
                                <?php echo ($typededechet ?? '') ; ?>
                            </select>
                        </div>
                        <div class="col-sm-7 col-xs-12"  style="margin-bottom:1%">
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="4" cols="50"  id="notemat"></textarea>
                        </div>    
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="UpdViderMatHome()" id="btnEndMat" class="btn btn-success" data-content="" data-dismiss="modal"  style="margin:4px 5px 0 0;display:none"></button>
                <button type="button" onclick="PostSuivisCamMat()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalhs" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Camion Hors-service&nbsp;!</h3>
                
			</div>
            <div class="modal-body" style="min-height:500px;">
              <form>

                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; "># Camions  </label> <br />
                           <select class="form-control" onchange="getTypeCamPopup(this.value, 1)" id="selTypeCamHS">
                            <option value="1" selected >REAR END</option>
                              <option value="2">ROBOT</option>
                           </select>
                            <select class="form-control" name="camionhs" id="camionhs" required>
                                <option value="">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />
                           
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date hors service </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dtestarths" id="dtestarths" />
                        </div>     
                           <br /> <br />    
                        
                        <div class="col-sm-7 col-xs-12" >
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="4" cols="50" id="notehs" > 

                            </textarea>
                        </div>    
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="UpdRetourHome()" id="btnEndHS" class="btn btn-success" data-content="" data-dismiss="modal"  style="margin:4px 5px 0 0;display:none">Retour immédiat</button>
                <button type="button" onclick="PostSuivisCamHS()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalrh" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Absence Ressource Humaine&nbsp;!</h3>
                
			</div>
            <div class="modal-body" style="min-height:570px;">
              <form>

                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Resources humaines</label> <br />
                            <select class="form-control" name="selRH" id="selRH" required>
                                <option value="">Choisir</option>
                                <?php echo ($employes ?? '') ; ?>
                            </select>
                            <input id="idsuivisrh" type="hidden" />
                        </div>     
                           <br /> <br />
                           
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date début de l'absence </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value=""name="dtestartrh" id="dtestartrh" />
                        </div>      
                           <br /> <br />
                        <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">Date retour prévu </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dteendrh" id="dteendrh" />
                        </div>   
                           <br /> <br />
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Type d'absence</label> <br />
                            <select class="form-control" name="selRHabsence" id="selRHabsence" required>
                                <option value="">Choisir</option>
                                <?php echo ($typeabsences ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />  
                        
                        <div class="col-sm-7 col-xs-12" style="margin-top:10px" >
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="3" cols="50" id="noterh" ></textarea>
                        </div> 
                      <br /> <br />
                      <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">&nbsp;&nbsp;&nbsp; </label> <br />
                           
                        </div> 
                      <br /> <br />
                        <div class="col-sm-12 col-xs-12" >
                          <label style="font-weight: bold; ">Avis pour retour</label>
                        <br />
                          <div class="btn-group"><label style="font-weight: bold;color:darkred; ">
                          <input id="showavis" type="checkbox"  /> Afficher l'avis de retour</label><br /></div>  
                           <div class="btn-group"> <textarea rows="3" cols="50" id="avistoshow" ></textarea>
                        </div>   
                      </div>  </div> 
				          </div>
					  </form>
            </div>
			      <div class="modal-footer">
                <button type="button" onclick="UpdRetourRHHome()" id="btnEndRH" class="btn btn-success" data-content="" data-dismiss="modal"  style="margin:4px 5px 0 0;display:none">Retour immédiat</button>
                <button type="button" onclick="PostSuivisRH()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalrhlist" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Absence Ressource Humaine&nbsp;!</h3>
                
			</div>
            <div class="modal-body" style="min-height:570px;">
              <form>

                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Resources humaines</label> <br />
                            <select class="form-control" name="selRHlist" id="selRHlist" required>
                                <option value="">Choisir</option>
                                <?php echo ($employes ?? '') ; ?>
                            </select>
                            <input id="idsuivisrhlist" type="hidden" />
                        </div>     
                           <br /> <br />
                           
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date début de l'absence </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value=""name="dtestartrhlist" id="dtestartrhlist" />
                        </div>      
                           <br /> <br />
                        <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">Date retour </label> <br />
                           <input type="date" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" name="dteendrhlist" id="dteendrhlist" />
                        </div>  
                           <br /> <br />
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Type d'absence</label> <br />
                            <select class="form-control" name="selRHabsencelist" id="selRHabsencelist" required>
                                <option value="">Choisir</option>
                                <?php echo ($typeabsences ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />  
                        
                        <div class="col-sm-7 col-xs-12" style="margin-top:10px" >
                          <label style="font-weight: bold; ">Notes</label> <br />
                            <textarea rows="3" cols="50" id="noterhlist" ></textarea>
                        </div> 
                      <br /> <br />
                      <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">&nbsp;&nbsp;&nbsp; </label> <br />
                           
                        </div> 
                      <br /> <br />
                        <div class="col-sm-12 col-xs-12" >
                          <label style="font-weight: bold; ">Avis pour retour</label>
                        <br />
                          <div class="btn-group"><label style="font-weight: bold;color:darkred; ">
                          <input id="showavislist" type="checkbox"  /> Afficher l'avis de retour</label><br /></div>  
                           <div class="btn-group"> <textarea rows="3" cols="50" id="avistoshowlist" ></textarea>
                        </div>   
                      </div>  </div> 
                  </div>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="UpdRetourRHList()" id="btnEndRHlist" class="btn btn-success" data-content="" data-dismiss="modal"  style="margin:4px 5px 0 0;display:none">Retour immédiat</button>
                <button type="button" onclick="PostSuivisRHList()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
                <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalRemoveCedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-danger">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsg" style="color:white;text-align:center;">Suppression de cédule&nbsp;</h3>
                
      </div>
            <div class="modal-body" style="min-height:350px;">
              <form>
               <div class="form-group">

                <div style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">
                  <div class="col-sm-7 col-xs-12 box box-info">
                    <div class="box-header">
                      <h3 class="box-title">Sélection du type de cédule</h3>
                    </div>
                    <label>
                      <input type="checkbox" onclick="VerifDelInfo()" name="chkRearEnd" id="chkRearEnd" >&nbsp; &nbsp; REAR END&nbsp; &nbsp;
                    </label>
                    <label>
                      <input type="checkbox" onclick="VerifDelInfo()" name="chkRobot" id="chkRobot" >&nbsp; &nbsp;  ROBOT
                    </label>
                  </div>
                </div>
              </div>
                    <div class="form-group" style="margin-top:10px">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Date de début </label> <br />
                           <input type="date" onchange="VerifDelInfo()" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px"  data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dtestartdel" />
                        </div>    
                           <br /> <br />
                          
                        
                        <div class="col-sm-7 col-xs-12">
                           <label style="font-weight: bold; ">Date de fin </label> <br />
                           <input type="date" onchange="VerifDelInfo()" class="form-control" style="font-size:medium;font-weight:bold;height:40px;margin-top:5px" data-toggle="popover"  data-trigger="hover" data-placement="bottom"title="Calendrier" data-content="Cliquer pour afficher le calendrier" value="" id="dteenddel" />
                        </div>    
                           <br /> <br />

                      </div>  
                  </div> <br /><br />


            </form>
            </div>
      <div class="modal-footer">
                <button type="button" id="btnDelCedule" onclick="RemoveCedule()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;" disabled>Supprimer</button>
                <button type="button"  class="btn btn-default" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
            </div>
        </div>
    </div>
</div>


  <div class="modal fade" id="modalRequeteWarning" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-block-md">
      <div class="modal-content">
        <div  class="modal-header btn-danger">
          <h2 class="modal-title">Alerte(s) Requête / Réponse</h2>
        </div>
        <div  class="modal-body">
                      <div id="divInfoReq" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        <input type="hidden" id="edtIdReqVille" value="" />
                        <input type="button" onclick="showNewRequete()" class="btn btn-info" id="btnModalReq" style="display:none"  value="" />
                        <br />
                        <input type="hidden" id="edtIdRepVille" value=""  />
                        <input type="button" class="btn btn-info" id="btnModalRep" style="display:none" value="" />
                      </div> 
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>

<div class="modal fade" id="modalAddGPX" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h3 class="modal-title" id="mytitre"> POLYLINE: Ajout fichier .GPX
          </h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <form
            method="post" enctype="multipart/form-data" id="frmGPX" name="frmGPX">
            <div class="card-body">
              <table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
                <tr>
                  <td >
                    <label style="font-weight:bold;font-size:medium">VILLE</label> 
                  </td>"
                  <td>
                    <select id="selVilleModal" name="selVilleModal" class="form-control" >
                      
                    </select>
                  </td>
                </tr>
                <tr>
                  <td >
                    <label style="font-weight:bold;font-size:medium">SECTEUR</label> 
                  </td>"
                  <td>
                    <select id="selSecteurModal" name="selSecteurModal" class="form-control" >
                      
                    </select>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    <h4>
                      T&eacute;l&eacute;verser
                    </h4>
                    <br />
                    <input class="form-control" style="width:450px"
                      name="fileGPX" id="fileGPX" type="file" />
                  </td>

                </tr>
                <tr>
                  <td colspan="2">
                    <h4>Titre</h4>
                    <br />
                    <input class="form-control" style="width:100%" name="titre" id="titre" type="text" />
                    <input name="url" id="url" type="hidden" />
                    <input name="fileext" id="fileext" type="hidden" />
                  </td>

                </tr>
                <tr>
                  <td colspan="2"> &nbsp;&nbsp;
                  </td>
                </tr>

              </table>
            </div>

          </form>


        </div>

        <!-- Modal footer -->
        <div class="modal-footer" style="background-color:Window;">
          <button type="button" onclick="SubmitGPX()" class="btn-primary btn"> Enregistrer </button>
          <button type="button" data-dismiss="modal" class="btn-danger btn"> Annuler </button>

        </div>

      </div>
    </div>
  </div>

<div class="modal fade" id="modalAddFromLocation" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h3 class="modal-title" id="mytitre"> AJOUT POLYLINE
          </h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          
            <form method="post" action="/sanivan/postloctopoly" id="frmFromLoc" name="frmFromLoc">
            <div class="card-body">
              <table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
                <tr>
                  <td >
                    <label style="font-weight:bold;font-size:medium">VILLE</label> 
                  </td>"
                  <td>
                    <select id="selVilleFromLoc" name="selVilleFromLoc" class="form-control" >
                      
                    </select>
                  </td>
                </tr>
                <tr>
                  <td >
                    <label style="font-weight:bold;font-size:medium">SECTEUR</label> 
                  </td>"
                  <td>
                    <select id="selSecteurFromLoc" name="selSecteurFromLoc" class="form-control" >
                      
                    </select>
                  </td>
                </tr>

                <tr>
                  <td >
                    <label style="font-weight:bold;font-size:medium">Chauffeur</label> 
                  </td>"
                  <td>
                    <select id="selChauffeurFromLoc" name="selChauffeurFromLoc" class="form-control" >
                      
                    </select>
                  </td>
                    </tr>
                <tr>
                <tr>
                  <td >
                    <label style="font-weight:bold;font-size:medium">Date du ramassage</label> 
                  </td>"
                  <td>
                    <input type="date" id="dteRamassage" name="dteRamassage" style="width:180px" />
                  </td>
                    </tr>
                <tr>
                  <td colspan="2">
                    <h4>Titre</h4>
                    <br />
                    <input class="form-control" style="width:100%" name="titreFromLoc" id="titreFromLoc" type="text" />
                  </td>

                </tr>
                <tr>
                  <td colspan="2"> &nbsp;&nbsp;
                  </td>
                </tr>

              </table>
            </div>
            </form>
          


        </div>

        <!-- Modal footer -->
        <div class="modal-footer" style="background-color:Window;">
          <button type="button" onclick="SubmitFromLoc()"  class="btn-primary btn"> Enregistrer </button>
          <button type="button" data-dismiss="modal" class="btn-danger btn"> Annuler </button>

        </div>
        
      </div>
    </div>
  </div>