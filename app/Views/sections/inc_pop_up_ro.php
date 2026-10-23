<?php

?>


<!-- SECTION RH  -->

  <div class="modal fade" id="modalRollOffRH" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="plTitreMsg_1" style="color:white;text-align:center;">Commande pour CLIENT</h3>
                <h4 class="modal-title" id="plTitreMsg_2" style="color:white;text-align:center;">Sélection du chauffeur&nbsp;!</h4>
                
			</div>
            <div class="modal-body" style="min-height:200px;min-width:200px">
              <form>
                  <input type="hidden" id="edtIdCommande" value="" />
                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Type de Resources humaines</label> <br />
                            <select class="form-control" onchange="SetChauffeursFromType(this.value)" name="selRollOffTypeRH" id="selRollOffTypeRH" style="width:200px;" required>
                                <option value="0">Choisir</option>
                                <?php echo ($typerh ?? '') ; ?>
                            </select>
                        </div> 
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Resources humaines</label> <br />
                            <select class="form-control"  name="selRollOffRH" id="selRollOffRH" style="width:400px;font-size:large; font-weight:bold;line-height:30px" required>
                                <?php echo ($chauffeurs ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />
                           
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="PostRollOffRH()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
        <button type="button" onclick="RemoveRollOffRH()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Supprimer</button>        
        <button type="button"  class="btn btn-warning" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
      </div>
        </div>
    </div>
</div>
<!-- SECTION CAMIONS  -->

  <div class="modal fade" id="modalRollOffCam" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="plTitreMsgCam_1" style="color:white;text-align:center;">Commande pour CLIENT</h3>
                <h4 class="modal-title" id="plTitreMsgCam_2" style="color:white;text-align:center;">Sélection du Camion&nbsp;!</h4>
                
			</div>
            <div class="modal-body" style="min-height:150px;min-width:200px">
              <form>
                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Camions RollOff</label> <br />
                            <select class="form-control"  name="selCamionRO" id="selCamionRO" required>
                                <option value="">Choisir</option>
                                <?php echo ($camions ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />
                           
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="PostRollOffCam()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
        <button type="button" onclick="RemoveRollOffCam()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Supprimer</button>        
        <button type="button"  class="btn btn-warning" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
      </div>
        </div>
    </div>
</div>
<!-- SECTION BOÎTES  -->

  <div class="modal fade" id="modalRollOffBoite" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="plTitreMsgBoite_1" style="color:white;text-align:center;">Commande pour CLIENT</h3>
                <h4 class="modal-title" id="plTitreMsgBoite_2" style="color:white;text-align:center;">Sélection de la boîte&nbsp;!</h4>
                
			</div>
            <div class="modal-body" style="min-height:150px;min-width:200px">
              <form>
                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Boîtes RollOff</label> <br />
                            <select class="form-control" name="selBoiteRO" id="selBoiteRO" required>
                                <option value="">Choisir</option>
                                <?php echo ($listboitespop ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />
                           
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="PostRollOffBoite()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
        <button type="button" onclick="RemoveRollOffBoite()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Supprimer</button>        
        <button type="button"  class="btn btn-warning" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
      </div>
        </div>
    </div>
</div>

  <div class="modal fade" id="modalRollOffBoiteEnPlace" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="plTitreMsgBoite_1EnPlace" style="color:white;text-align:center;">Commande pour CLIENT</h3>
                <h4 class="modal-title" id="plTitreMsgBoite_2EnPlace" style="color:white;text-align:center;">Sélection de la boîte&nbsp;!</h4>
                
			</div>
            <div class="modal-body" style="min-height:150px;min-width:200px">
              <form>
                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Boîtes RollOff</label> <br />
                            <select class="form-control" name="selBoiteROEnPlace" id="selBoiteROEnPlace" required>
                                <option value="">Choisir</option>
                                <?php echo ($listboitespop ?? '') ; ?>
                            </select>
                        </div>     
                           <br /> <br />
                           
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="PostRollOffBoiteEnPlace()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
        <button type="button" onclick="RemoveRollOffBoiteEnPlace()" class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Supprimer</button>        
        <button type="button"  class="btn btn-warning" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
      </div>
        </div>
    </div>
</div>
  
<div class="modal fade" id="modalEditBoite" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="min-height:80%;width:1000px;">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="plTitreMsgUpd" style="color:white;text-align:center;">Edition du camion:&nbsp;</h3>
                
			</div>
      <div id="divEditCam">
        <section class="panel panel-featured-left">
        <div  class="panel-body">
          <section class="card">
				<form action="" method="post" enctype="multipart/form-data" id="form_0"  name="form_0" >
														<table border="0" cellspacing="0" cellpadding="10" class="txtnormal">
															 <tr>
                                <td> <label style="font-weight: bold; "> &nbsp; &nbsp;<input class="form-check-input" type="checkbox" id="chkDelete" />&nbsp; Supprimé &nbsp; &nbsp;</label></td>
                                 
                                 <td colspan="2"><h4># Boîte :</h4><br> <input class="form-control"  id="edtNoBoite" type="text" value="" style="width:100%">
                                 <td> &nbsp;&nbsp;&nbsp;</td>
                             
                              <td colspan="2"><h4>Type de Boîte :</h4><br> <select class="form-control" id="selTypeBoite">
                                    <option value="">Choisir</option>
                                      <?php echo ($addTypesBoite ?? '') ; ?>
                                    </select></td>
                            </tr>
                            <tr>
                                 <td colspan="5"><h4>Emplacement :</h4><br> 
                                  <input class="form-control"  id="edtPlace" type="text" value="" style="width:100%" readonly>
                                 <td> &nbsp;&nbsp;&nbsp;</td>

                            </tr>
                             <tr>
                              <td colspan="5"><h4>Notes :</h4><br> 
                                  <textarea rows="7" id="edtNotes" cols="100" > 

                                  </textarea>
                              </td>
                              <td> &nbsp;&nbsp;&nbsp;</td>
                            </tr>
                             <tr>
                                <td colspan="5">&nbsp;</td>
															</tr>
                            <tr>
                              <td ><button type="button" onclick="sendBoiteInfo()" class="btn-success btn" > Enregistrer </button></td>
																	<td >		<button type="button" onclick="ShowHideNika('divAddBoites')" class="btn-danger btn" > Annuler </button></td>
                            </tr>
														</table>
													</form>

          </section>
          </div>

      </section>
      </div>
			<div class="modal-footer">
            <button type="button" id="btnUpdCamion" onclick="sendRHInfo()" class="btn-primary btn"  style="margin:4px 5px 0 0;" data-dismiss="modal"> Enregistrer </button>
            <button type="button"  class="btn btn-danger" data-dismiss="modal" style="margin:4px 5px 0 0;">Fermer</button>
      </div>
        </div>
    </div>
</div>

<!-- SECTION TYPE CAMIONS  -->

  <div class="modal fade" id="modalRollOff10Roues" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="plTitreTypeCam_1" style="color:white;text-align:center;">Commande pour CLIENT</h3>
                <h4 class="modal-title" id="plTitreMsgCam_2" style="color:white;text-align:center;">Sélection du type de Camion&nbsp;!</h4>
                
			</div>
            <div class="modal-body" style="min-height:150px;min-width:200px">
              <form>
                  
                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <label style="font-weight: bold; ">Type de Camions RollOff</label> <br />
                            <select class="form-control" name="selTypeCamionRO" id="selTypeCamionRO" required>
                                <option value="0">10 roues</option>
                                <option value="1">12 roues</option>
                            </select>
                        </div>     
                           <br /> <br />
                           
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="PostRollOffTypeCam()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>        
        <button type="button"  class="btn btn-warning" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
      </div>
        </div>
    </div>
</div>

<!-- SECTION TYPE CAMIONS  -->

  <div class="modal fade" id="modalRollOffChangeDate" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="plTitreMsgDate_1" style="color:white;text-align:center;">Commande pour CLIENT</h3>
                <h4 class="modal-title" id="plTitreMsgCam_2" style="color:white;text-align:center;">Modification de la date de livraison&nbsp;!</h4>
                
			</div>
            <div class="modal-body" style="min-height:200px;min-width:200px">
              <form>
                  
                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    
                        
                        <div class="col-sm-7 col-xs-12">
                          <input type="hidden" id="edtNewDate" value="" />
                          <label id="lblDateChange" style="font-weight: bold; ">
                          </label>
                        </div>     
                           <br /> <br />
                           
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" onclick="PostRollOffChangeDate()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>        
        <button type="button"  class="btn btn-warning" data-dismiss="modal" style="margin:4px 5px 0 0;">Annuler</button>
      </div>
        </div>
    </div>
</div>

   <div class="modal fade" id="modalTransBoMat" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" style="color:white;text-align:center;">Type de Déchet pour TRANS_BO</h3>
                <input type="hidden" id="infoTrans" value="" />
			</div>
            <div class="modal-body" style="min-height:200px;min-width:200px">
              <form>
                  
                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    

                        <div class="col-sm-7 col-xs-12">
                          <select class="form-control" id="selTransbo_in">
                               <option value="">Choisir</option>
                               <option value="amiante_Amiante">Amiante</option>
                               <option value="asphalte_Asphalte">Asphalte</option>
                               <option value="bardeau_Bardeau">Bardeau</option>
                               <option value="beton_Beton">Béton</option>
                               <option value="betonarme_BetonArme">Béton armé</option>
                               <option value="bois_Bois">Bois</option>
                               <option value="branches_Branches">Branches</option>
                               <option value="carton_Carton">Carton</option>
                               <option value="compost_Compost">Compost</option>
                               <option value="dechets_Dechets">Déchets</option>
                               <option value="demolition_Demolition">Démolition</option>
                               <option value="fumier_Fumier">Fumier</option>
                               <option value="materiauxsec_MateriauxSec">Matériaux sec</option>
                               <option value="metal_Metal">Métal</option>
                               <option value="org_Organique">Organique</option>
                               <option value="recy_Recyclage">Recyclage</option>
                               <option value="sapins_Sapins">Sapins</option>
                          </select>
                        </div>     
                           <br /> <br />
                           
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" id="btnTRB_1" onclick="sendBoiteToTRB()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;display:block">Enregistrer</button>
        <button type="button" id="btnTRB_2" onclick="updBoiteToTRB()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;display:none">Enregistrer</button>
      </div>
        </div>
    </div>
</div>
   <div class="modal fade" id="modalUpdTransBoMat" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title"  style="color:white;text-align:center;">Type de Déchet pour TRANS_BO</h3>
                <input type="hidden" id="infoTrans" value="" />
			</div>
            <div class="modal-body" style="min-height:200px;min-width:200px">
              <form>
                  
                    <div class="form-group">

                      <div id="divHS" style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    

                        <div class="col-sm-7 col-xs-12">
                          <select class="form-control" id="selUpdTransbo_in">
                            <option value="0">Choisir</option>
                              <?php 
                              echo ($boitesincours ?? '');
                              ?>

                          </select>
                        </div>     
                           <br /> <br />
                           
                      </div>  
				          </div>
					  </form>
            </div>
			<div class="modal-footer">
        <button type="button" id="btnTRB_2" onclick="updBoiteToTRB()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>
      </div>
        </div>
    </div>
</div>
 

 <div class="modal fade" id="modalEditTrans" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title"  style="color:white;text-align:center;">Informations pour TRANS_BO</h3>
                <input type="hidden" id="infoTrans" value="" />
			</div>
            <div class="modal-body" id="modalBodyEdit" style="min-height:500px;min-width:200px;">
              <form>
                  <div class="input-group input-group-sm"  style="width:300px;margin-left:100px;margin-bottom:30px">
                     <label  class="form-control" style="font-weight:bold;width:185px;font-size:16px;">Boîtes TRANS_BO</label>
                      <span class="input-group-btn">

                          <select class="form-control" style="width:115px;height:30px" onclick="selChangeBoite(this.value)" id="selEditTransbo_in">
                            <option value="0">Choisir</option>
                              <?php 
                              echo ($boitesincours ?? '');
                              ?>

                          </select>
                        <button type="button" id="btnModalPostBoite" class="btn btn-success btn-flat" onclick="updBoiteTransBO()" disabled  title="Modifier">
                        <i class='fa fa-check' ></i>&nbsp;</button>
                        </span>     

				          </div>
                    <div class="input-group input-group-sm"  style="width:300px;margin-left:100px;margin-bottom:30px">
                     <label  class="form-control" style="font-weight:bold;width:185px;font-size:16px;">Déchets TRANS_BO</label>
                      <span class="input-group-btn">

                          <select class="form-control" style="width:115px;height:30px" onclick="selChangeTypeDec(this.value)"  id="selEditTypeTransbo_in">
                            <option value="0">Choisir</option>
                               <option value="amiante_Amiante">Amiante</option>
                               <option value="asphalte_Asphalte">Asphalte</option>
                               <option value="bardeau_Bardeau">Bardeau</option>
                               <option value="beton_Beton">Béton</option>
                               <option value="betonarme_BetonArme">Béton armé</option>
                               <option value="bois_Bois">Bois</option>
                               <option value="branches_Branches">Branches</option>
                               <option value="carton_Carton">Carton</option>
                               <option value="compost_Compost">Compost</option>
                               <option value="dechets_Dechets">Déchets</option>
                               <option value="demolition_Demolition">Démolition</option>
                               <option value="fumier_Fumier">Fumier</option>
                               <option value="materiauxsec_MateriauxSec">Matériaux sec</option>
                               <option value="metal_Metal">Métal</option>
                               <option value="org_Organique">Organique</option>
                               <option value="recy_Recyclage">Recyclage</option>
                               <option value="sapins_Sapins">Sapins</option>
                          </select>
                        <button type="button" class="btn btn-success btn-flat"  id="btnModalPostDechet" onclick="updDechetTransBO()" disabled title="Modifier">
                  <i class='fa fa-check' ></i>&nbsp;</button>
                        </span>     

				          </div>
                    <div class="form-group">
                      <div style="padding-left: 10%;padding-bottom:10px;padding-top:10px;margin-top:10px;">    

                        <div class="col-sm-7 col-xs-12">
                          <table  class="table table-bordered">
                             <thead>
                               <tr><th colspan="4" style="text-align:center;">
                                  <h4 class="modal-title"  style="text-align:center;">Liste des boîtes TRANSBORDÉES</h4>
                                 </th>
                              </tr>
                               <tr>
                                 <th style="text-align:center;">
                                  <label  style="text-align:center;font-weight:bold;">Boîtes</label>
                                 </th>
                                 <th style="text-align:center;">
                                  <label  style="text-align:center;font-weight:bold;">Montant</label>
                                 </th>
                                 <th style="text-align:center;">
                                  <label  style="text-align:center;font-weight:bold;">Matériaux</label>
                                 </th>
                                 <th style="text-align:center;">
                                  <label  style="text-align:center;font-weight:bold;">&nbsp;&nbsp;</label>
                                 </th>
                                 <th style="text-align:center;">
                                  <label  style="text-align:center;font-weight:bold;">&nbsp;&nbsp;</label>
                                 </th>
                              </tr>
                            </thead>
                            <tbody id="tboEditBoxTransbo">

                            </tbody>
                            <tfoot id="tfoEditBoxTransbo">

                            </tfoot>
                          </table>
                        </div>     
                           
                      </div>  
                      </div> 

				          
					  </form>
            </div>
			<div class="modal-footer">
        <div class="form-group" style="padding-bottom:10px;">
          <h3 style="height:40px;text-align:center;">Après avoir valider vos informations <br /> 
          <button type="button" class="btn btn-link" style="font-size:22px" onclick="ShowClientRollOff(12958,'none')">Cliquez pour créer la commande</button></h3>
        </div>
        <div class="jumbotron">
            <div style="width:400px;width:100%;">
              <h3 class= "btn-danger" style="height:40px;text-align:center;">Pour Supprimer cette TRANSBO </h3>
              <div style="text-align:left;padding-left:10px;padding-right:10px"> Vous devez remettre toutes les boîtes 
                <span style="font-weight:bold;font-size:18px;color:red;">Transbordées
                </span>&nbsp;avec matériel avant de pouvoir la supprimer
              </div>
              <button type="button" id="btnModalDelTransBo"  onclick="delTransBO()" class="btn btn-danger" 
                      data-dismiss="modal" disabled style="margin:4px 5px 0 0;">Supprimer</button>
              <button type="button"  class="btn btn-warning" onclick="Reload()" data-dismiss="modal" style="margin:4px 5px 0 0;">Fermer</button>                          
            </div>
                            
         </div>
        <!--<button type="button"  onclick="upInfoToTRB()" class="btn btn-primary" data-dismiss="modal" style="margin:4px 5px 0 0;">Enregistrer</button>-->

      </div>
        </div>
    </div>
</div>
