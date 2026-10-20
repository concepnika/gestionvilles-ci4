<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <title>Robert Daoust</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  </head>

  <body>
    <p><?php echo ci3lang('send_pass_bonjour');?> <?php echo $nom;?>,</p>

    <p><?php echo ci3lang('send_pass_requis');?><br>
    <?php echo $password;?>
    </p>
    <br>
    <p>- - -<br>
    <?php echo ci3lang('send_pass_equipe');?></p>

  </body>
</html>
