<?php

?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-12 col-xl-10">
                <div class="card shadow-lg o-hidden border-0 my-5">
                    <div class="card-body p-0">
                        <div class="row">
							<div class="col-md-3">
							</div>
                            <div class="col-md-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h4 class="text-dark mb-4"><?php echo ci3lang('login_titre');?></h4>
                                    </div>
                                    <form class="user" action="auth/login">
                                        <div class="form-group"><input class="form-control form-control-user" type="email" id="email" aria-describedby="emailHelp" placeholder="<?php echo ci3lang('login_email');?>" name="email"></div>
                                        <div class="form-group"><input class="form-control form-control-user" type="password"id="password" placeholder="<?php echo ci3lang('login_password');?>" name="password"></div>
                                        <div class="form-group">

                                        </div><button class="btn btn-primary btn-block text-white btn-user" onclick="Login()" type="button"><?php echo ci3lang('login_connect');?></button>
                                        
                                        <hr>
                                    </form>
                                    <div class="text-center"><a class="small" href="forgot-password.html"><?php echo ci3lang('login_forget');?></a></div>
                                    
                                </div>
                            </div>
							<div class="col-md-3">
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

