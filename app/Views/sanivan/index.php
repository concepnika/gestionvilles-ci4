<?php 
ini_set("session.cookie_lifetime","43200");
session_start();

date_default_timezone_set("America/New_York");
require_once('../connexion.php');
require_once 'pagessite/sanivan.php';
require_once 'cedule.php';
/*
-----------------------------------
------ SCRIPT DE PROTECTION -------
DBProtect V1.2
-----------------------------------
 */
//error_reporting(0);
$condition = "" ;
if (isset($_POST['uname']))
{ // execution uniquement apres envoi du formulaire (test si la variable POST existe)
	$login = addslashes($_POST['uname']);
	//$pass = addslashes(md5($_POST['pass']));
	$login = $_POST['uname'];
	$pass = $_POST['psw'];
	$ClsUser = ClassCedule::LoginDriver($login, $pass);
	if (!empty($ClsUser))
	{
		//$_SESSION['ClsUser'] = array();
		$_SESSION = $ClsUser  ;
        $_SESSION['NoCamion'] = $_POST['Camion']  ;
		//header("Location:accueil.php");
		//echo ($_SESSION['ClsUser'] ?? '');
        $value = $ClsUser['id'];
        $query = "Select HELPERS_1, HELPERS_2 from cedules where idchauffeurs = '$value' and DateJour = CURDATE() " ;
        $Connect = new ClassConnect();
        $result = $Connect->dbprotect->query($query);
        $data = $result->fetch_assoc();
        if ( trim($data['HELPERS_1']) != '' )
        {
            $ActifID = $data['HELPERS_1']; 
            $cam = $_POST['Camion'];
            $query = "SELECT StartTime, EndTime, dinerdeb, dinerfin
                                                FROM suivis WHERE datejour = CURDATE() AND idrh = '$ActifID' " ;
            $result = $Connect->dbprotect->query($query);
            /** @var array $row */
            $row = $result->fetch_assoc();
            if ($result->num_rows == 0)
            {
                $query = "INSERT INTO suivis
                                        (idrh, datejour, NOCAMION)
                                                    VALUES ('$ActifID', CURDATE(), '$cam') ";
                $result = $Connect->dbprotect->query($query);
                $Connect->close();
                echo 'OK';

            }            
        }
        if ( trim($data['HELPERS_2']) != '' )
        {
            $ActifID = $data['HELPERS_2']; 
            $query = "SELECT StartTime, EndTime, dinerdeb, dinerfin
                                                FROM suivis WHERE datejour = CURDATE() AND idrh = '$ActifID' " ;
            $result = $Connect->dbprotect->query($query);
            /** @var array $row */
            $row = $result->fetch_assoc();
            if ($result->num_rows == 0)
            {
                $query = "INSERT INTO suivis
                                        (idrh, datejour)
                                                    VALUES ('$idrh', CURDATE()) ";
                $result = $Connect->dbprotect->query($query);
                $Connect->close();
                echo 'OK';

            }            
        }
		$condition = "OK" ;
	}
	else
		header("Location:index.php?erreur=login");
}
else if (isset($_REQUEST['motpasseoublie']))
{
//  $query = "SELECT * FROM reshumaine WHERE LOWER(login) = LOWER('$codeass')";

//  $Connect = new ClassConnect();

//  $result = $Connect->dbprotect->query($query);

//  if ($result->num_rows > 0) {
//    $row = $result->fetch_assoc();

//    $Connect->close();

//    return $row;
//  }
//  $TO = 'sdallaire@conceptnika.com';
//  $boundary = md5(uniqid(microtime(), TRUE));
//  $headers  = "From: " . "info@conceptnika.com"."\r\n"; // Entrer l'adresse de votre site ou l'adresse email de votre site.
//  $headers .= 'Mime-Version: 1.0'."\r\n";
//  $headers .= 'Content-Type: multipart/mixed;boundary='.$boundary."\r\n";
//  $headers .= "Content-Transfer-Encoding: base64\r\n";

//  $message = ""; // Lesser vide.

//  $subject = "Demande d'informations";
//  $titre = "Liste des produits";
//  $where = "";
//  // Message HTML
//  $msg = '--'.$boundary."\r\n";
//  $msg .= 'Content-type: text/html; charset=ISO-8859-1'."\r\n\r\n";
//  $msg .= 'Demande de: ' . $_POST['name'] .'<br><br>' . $_POST['email']. '<br><br>' . $_POST['message'] ;
//  $msg = utf8_decode($msg);
//  mail($TO, $subject, $msg, $headers) ;
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		if( $condition == "OK" )
		{

			echo '<meta http-equiv="refresh" content="0;URL=pagessite/index.php">';
			exit();
		}
	?>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Journ&eacute;e</title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <script src='/assets/css/fontawesome/all.css'></script>
    <link href="styles/style.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript"> 
    function SetCamion(id) {
        if ($('#' + id).val() != '')
        {
            $('#btnGo').removeAttr("disabled");
        }
    }  
    function GetCamion()
    {
        var urlname = 'pagessite/saniact.php?code=' + $('#uname').val() + 
                                                '&pass=' + $('#psw').val() ;
        $.ajax({
            url: urlname,
            type: 'POST',
            success: function (msg) {
                
                if (msg != '0')
                    $('#divCam').html(msg) ; 
            }
        });
    }
  </script>
</head>
<body>

	<h2>Acc&egrave;s Robert Daoust</h2>

	<form action="index.php" method="post" name="connect">
		<?php
			if (isset($_GET['erreur']) && ($_GET['erreur'] == "login"))
				echo '<strong class="erreur">Echec d\'authentification !!! &gt; login ou mot de passe incorrect</strong>';

			if (isset($_GET['erreur']) && ($_GET['erreur'] == "logout"))
			{
				$infoChauf = '';
				if (isset($_SESSION['login']))
				{
					$infoChauf = $_SESSION['Prenom']. ' '. $_SESSION['Nom'];
				}
				session_unset();

				echo '<strong class="reussite">D&eacute;connexion r&eacute;ussie... A bient&ocirc;t ' . $infoChauf.'!</strong> ';
			}
			if(isset($_GET['erreur']) && ($_GET['erreur'] == "intru"))
				echo '<strong class="erreur">Echec d\'authentification !!! &gt; Aucune session n\'est ouverte ou vous n\'avez pas les droits pour afficher cette page</strong>';

        ?>
		<div class="imgcontainer1">
			<img src="images/logo.png" alt="Avatar" class="avatar" />
		</div>

		<div class="container">
			<label for="uname">
				<b>Code usager</b>
			</label>
			<input type="text" placeholder="Code usager" name="uname" id="uname" required />

			<label for="psw">
				<b>Mot de passe</b>
			</label>
			<input onblur="GetCamion()" type="password" placeholder="Mot de passe" name="psw" id="psw" required />
            <div id="divCam" style="display: block;"> 
                <div id="divCam">
                 <select class="form-control" style="width:100px;margin-left:5px; " >
                              <option value="">    Choisir</option>
                 </select>
                </div>         
            </div>
			<button id="btnGo" disabled="disabled" type="summit">GO</button>
		</div>

		<div class="container" style="background-color:#f1f1f1">

			<span class="psw">
				<a href="index.php?motpasseoublie">Mot de passe oubli&eacute;?</a>
			</span>
		</div>
	</form>

</body>
</html>
