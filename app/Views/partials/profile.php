<?php

?>
						<div style="margin-left:4%" >
                <h3 class="text-dark mb-4">Profil</h3>
								<p> &nbsp;&nbsp; </p>
								<p> &nbsp;&nbsp;&nbsp; </p>
                <div class="row">
                    <!--<div class="col-6">
											<div class="card mb-5">
												<div class="card-header py-3">
													<p class="text-primary m-0 font-weight-bold">informations générales</p>
												</div>
												<div class="card-body text-center shadow">
													<form>
														<div class="form-row">
															<div class="col">
																<div class="form-group">
																	<label for="username">
																		<strong>Nom du site</strong>
																	</label>
																	<input class="form-control" type="text" placeholder="Nom du site" name="username" value="<?php echo ($option->Cie ?? '');?>" />
																</div>
															</div>
															<div class="col">
																<div class="form-group">
																	<label for="email">
																		<strong>Adresse courriel</strong>
																	</label>
																	<input class="form-control" type="email" placeholder="Adresse courriel" name="email" value="<?php echo ($option->Courriel ?? '');?>" />
																</div>
															</div>
														</div>
														<div class="form-row">
															<div class="col">
																<div class="form-group">
																	<label for="first_name">
																		<strong>Prénom</strong>
																	</label>
																	<input class="form-control" type="text" placeholder="Prénom" name="first_name" <?php echo ($option->Prenom ?? '');?> />
																</div>
															</div>
															<div class="col">
																<div class="form-group">
																	<label for="last_name">
																		<strong>Nom</strong>
																	</label>
																	<input class="form-control" type="text" placeholder="Nom" name="last_name" <?php echo ($option->Nom ?? '');?> />
																</div>
															</div>
														</div>

														<img class="mb-3 mt-4" src="assets/img/logo.jpg"  height="130" />
														<div class="mb-3">
															<button class="btn btn-primary btn-sm" type="button">Ajouter une photo ou un Logo</button>
														</div>
														<div class="form-group">
															<button class="btn btn-primary btn-sm" type="submit">Enregistrer</button>
														</div>
													</form>
												</div>
											</div>
                     </div>-->
										<div class="col-6">
                        <div class="card shadow mb-6">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 font-weight-bold">informations du contact</p>
                            </div>
                            <div class="card-body">
                                <form action="auth/postProfil">
                                    <div class="form-row">
                                        <div class="col">
                                            <div class="form-group"><label for="username"><strong>Code usager</strong></label>
<input class="form-control" type="text" placeholder="Code usager" name="username" value="<?php echo ($user->login ?? '');?>" readonly/></div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group"><label for="email"><strong>Adresse courriel</strong></label><input class="form-control" type="email" placeholder="Adresse courriel" name="email" value="<?php echo ($user->email ?? '');?>" readonly></div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col">
                                            <div class="form-group"><label for="first_name"><strong>Prénom</strong></label><input class="form-control" type="text" placeholder="Prénom" name="first_name" value="<?php echo ($user->prenom ?? '');?>"></div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group"><label for="last_name"><strong>Nom</strong></label><input class="form-control" type="text" placeholder="Nom" name="last_name" value="<?php echo ($user->nom ?? '');?>"></div>
                                        </div>
                                    </div>
																	<div class="form-row">
																		<div class="col">
																			<div class="form-group">
																				<label for="first_name">
																					<strong>Mot de passe</strong>
																				</label>
																				<input class="form-control" type="password" placeholder="Mot de passe" name="first_name" value="" />
																			</div>
																		</div>
																		<div class="col">
																			<div class="form-group">
																				<label for="password">
																					<strong>Confirmer le mot de passe</strong>
																				</label>
																				<input class="form-control" type="password" placeholder="Cnfirmer le mot de passe" name="last_name" value="" />
																			</div>
																		</div>
																	</div>
                                    <div class="form-group"><button class="btn btn-primary btn-sm"  type="submit">Enregistrer</button></div>
                                </form>
                            </div>
                        </div>

                    </div>
									
							</div>
						</div>


