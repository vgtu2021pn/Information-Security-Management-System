<?php
    session_start();
    require_once __DIR__ . '/src/notphish.php';
    require_once __DIR__ . '/src/language/language.php';
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require __DIR__ . '/src/PHPMailer/Exception.php';
    require __DIR__ . '/src/PHPMailer/PHPMailer.php';
    require __DIR__ . '/src/PHPMailer/SMTP.php';
    
    $supported = [
        'en' => 'English',
        'lt' => 'Lietuvių',
        'pl' => 'Polska'
    ];
    
    $current = defined('SITE_LANG') ? SITE_LANG : ($_COOKIE['site_lang'] ?? 'en');
    
    $return = '/Login.php';
    
    // Simple language chooser
    $lengui = array(
        'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
        'approved' => array ('en' => 'Your Account was activated.', 'lt' => 'Jūsų paskyra aktyvuota.', 'pl' => 'Wasz konto aktywowany.'),
        'notapproved' => array ('en' => 'Your Account was left not activated.', 'lt' => 'Jūsų paskyra liko neaktyvuota.', 'pl' => 'Wasz konto nie aktywowany.'),
        'err1' => array ('en' => 'Enter username, password and 2FA.', 'lt' => 'Įvesti prisijungimo vardą, slaptažodį ir 2Fa.', 'pl' => 'Wpisz nazwę użytkownika, hasło i 2FA.'),
        'err2' => array ('en' => 'Account is not activated.', 'lt' => 'Paskyra nėra aktyvuota.', 'pl' => 'Konto nie jies aktywowany.'),
        'err3' => array ('en' => 'Wrong User Details No. 1.', 'lt' => 'Neteisinga naudotojo pateiktis Nr. 1.', 'pl' => 'Błędne dane użytkownika Nr. 1.'),
        'err4' => array ('en' => 'Wrong User Details No. 2.', 'lt' => 'Neteisinga naudotojo pateiktis Nr. 2.', 'pl' => 'Błędne dane użytkownika Nr. 2.'),
        'err5' => array ('en' => 'Enter username, password, repeat the password, email and comply with terms of service.', 'lt' => 'Įvesti naudotojo vardą, slaptažodį, pakartotą slaptažodį, el. paštą ir sutikti su paslaugos sąlygomis.', 'pl' => 'Wpisz nazwę użytkownika, hasło, powtórz hasło, email i zaakceptuj warunki korzystania z usługi.'),
        'err6' => array ('en' => 'Choose a different username.', 'lt' => 'Pasirinkti kitą naudotojo vardą.', 'pl' => 'Wybierz inną nazwę użytkownika.'),
        'err7' => array ('en' => 'Password fields does not match.', 'lt' => 'Slaptažodžio laukai nesutampa.', 'pl' => 'Pole hasła nie pasują.'),
        'err8' => array ('en' => 'Wrong E-mail address.', 'lt' => 'Neteisingas el. pašto adresas.', 'pl' => 'Błędne adres email.'),
        'cookie' => array ('en' => 'Data of Cookie is wrong.', 'lt' => 'Neteisingi slapuko duomenys.', 'pl' => 'Błędne dane Cookie.'),
        'notice' => array ('en' => 'Registration was a success.', 'lt' => 'Sėkmingai įgyvendinta registracija.', 'pl' => 'Rejestracja zakończona pomyślnie.'),
        'notice2fa' => array ('en' => '2FA request was completed.', 'lt' => 'Sėkmingai įgyvendinta 2FA užklausa.', 'pl' => '2FA zakończona pomyślnie.'),
        'login' => array ('en' => 'Login', 'lt' => 'Prisijungimas', 'pl' => 'Logowania'),
        'updated' => array ('en' => 'Password was updated and sent over email', 'lt' => 'Slaptažodis atnaujintas ir išsiųstas el. paštu', 'pl' => 'Hasło zostało zaktualizowane i wysłane na email'),
        'signup' => array ('en' => 'Sign Up', 'lt' => 'Prisiregistruoti', 'pl' => 'Zarejestruj się'),
        '2fa' => array ('en' => '2FA', 'lt' => '2FA', 'pl' => '2FA'),
        'hour' => array ('en' => 'for one hour', 'lt' => 'valandai', 'pl' => 'na godzinę'),
        'spec' => array ('en' => 'Spectator', 'lt' => 'Stebėtojas', 'pl' => 'Widz'),
        'entuser' => array ('en' => 'Enter your username', 'lt' => 'Įvesti naudotojo vardą', 'pl' => 'Wprowadź nazwę użytkownika'),
        'entpass' => array ('en' => 'Enter your password', 'lt' => 'Įvesti slaptažodį', 'pl' => 'Wprowadź hasło'),
        'entcode' => array ('en' => 'Enter received 2FA', 'lt' => 'Įvesti gautą 2FA', 'pl' => 'Wprowadź otrzymany 2FA'),
        'signin' => array ('en' => 'Sign In', 'lt' => 'Prisijungti', 'pl' => 'Zaloguj'),
        'forgotpass' => array ('en' => 'Forgot password?', 'lt' => 'Pamiršai slaptažodį?', 'pl' => 'Zapomniałeś hasła?'),
        'admin' => array ('en' => 'Administrator', 'lt' => 'Administratorius', 'pl' => 'Administrator'),
        'fname' => array ('en' => 'First Name', 'lt' => 'Vardas', 'pl' => 'Imię'),
        'lname' => array ('en' => 'Last Name', 'lt' => 'Pavardė', 'pl' => 'Nazwisko'),
        'username' => array ('en' => 'Username', 'lt' => 'Naudotojo vardas', 'pl' => 'Nazwa użytkownika'),
        'email' => array ('en' => 'E-mail', 'lt' => 'El. paštas', 'pl' => 'Email'),
        'crtpass' => array ('en' => 'Create your password', 'lt' => 'Sukurti slaptažodį', 'pl' => 'Utwórz hasło'),
        'rptpass' => array ('en' => 'Repeat your password', 'lt' => 'Pakartoti slaptažodį', 'pl' => 'Powtórz hasło'),
        'terms' => array ('en' => "I'm accepting Terms of Service of this Site.", 'lt' => 'Priimu šio saito iškeltas paslaugos sąlygas.', 'pl' => 'Akceptuję warunki korzystania z tej strony'),
        'termspage' => array ('en' => "Terms of Service", 'lt' => 'Paslaugos sąlygos', 'pl' => 'Warunki usługi'),
        'privacypage' => array ('en' => "Terms of Privacy", 'lt' => 'Privatumo sąlygos', 'pl' => 'Warunki prywatności'),
        'alreadymemb' => array ('en' => 'Already Member?', 'lt' => 'Jau esi narys?', 'pl' => 'Już jesteś członkiem?'),
        'serr1' => array ('en' => 'Too short.', 'lt' => 'Per trumpas.', 'pl' => 'Za krótko.'),
        'serr2' => array ('en' => 'Too long.', 'lt' => 'Per ilgas.', 'pl' => 'Za długo.'),
        'serr3' => array ('en' => 'Special characters are not allowed.', 'lt' => 'Specialieji ženklai neleidžiami.', 'pl' => 'Nie można używać znaków specjalnych.'),
        'serr4' => array ('en' => 'Incorrect first name.', 'lt' => 'Neteisingas vardas.', 'pl' => 'Błędne imię.'),
        'serr5' => array ('en' => 'Incorrect last name.', 'lt' => 'Neteisinga pavardė.', 'pl' => 'Błędne nazwisko.'),
        'serr6' => array ('en' => 'Incorrect username.', 'lt' => 'Neteisingas naudotojo vardas.', 'pl' => 'Nieprawidłowa nazwa użytkownika.'),
        'serr7' => array ('en' => 'Incorrect e-mail.', 'lt' => 'Neteisingas el. paštas.', 'pl' => 'Nieprawidłowy email.'),
        'serr8' => array ('en' => 'Password is empty.', 'lt' => 'Slaptažodis yra tuščias.', 'pl' => 'Hasło jest puste.'),
        'serr9' => array ('en' => 'Passwords does not match.', 'lt' => 'Slaptažodžiai nesutampa', 'pl' => 'Hasła nie pasują.'),
        'serr10' => array ('en' => "You don\'t meet mandatory requirements of this service.", 'lt' => 'Neatitinki privalomų paslaugos reikalavimų.', 'pl' => 'Nie spełniasz wymogów tego stanowiska.')
    );
    
    require_once __DIR__ . '/src/credens.php';
    
    if(isset($_GET["approval"]) && isset($_GET["lang"]) && isset($_GET["ao"])) {
    	if($_GET["lang"] == 1) {
    	    $lang = 'en';
    	} elseif($_GET["lang"] == 2) {
    	    $lang = 'lt';
    	} else {
    	    $lang = 'pl';
    	}
    	
    	$uno = intval($_GET["ao"]);
    	$app = preg_replace('/[^a-z0-9]/u', '', $_GET["approval"]);
    	
    	$connection = mysqli_connect($servername, $user, $pw, $db);
    
    	if(!$connection) {
    	    die("Connection failed: " .mysqli_connect_error());
    	}
    		
    	$selectqry_app = "SELECT `uno` FROM `user` WHERE `uno`=? AND `confirmation`=?;";
    	$stmt = mysqli_prepare($connection, $selectqry_app);
    	mysqli_stmt_bind_param($stmt,'is', $uno, $app);
    	mysqli_stmt_execute($stmt);
    	$result = mysqli_stmt_get_result($stmt);
    	
    	if(mysqli_num_rows($result) > 0) {
    	$selectqry_update = "UPDATE `user` SET `activated`=1 WHERE `uno`=? AND `confirmation`=?;";
    	    $ustmt = mysqli_prepare($connection, $selectqry_update);
    	    mysqli_stmt_bind_param($ustmt,'is', $uno, $app);
    	    mysqli_stmt_execute($ustmt);
    	    echo '<script> alert("'.$lengui['approved'][$lang].'") </script>';
    	} else {
    	    echo '<script> alert("'.$lengui['notapproved'][$lang].'") </script>';
    	}
    	mysqli_free_result($result);
    } else {
    	if(isset($_SESSION["usertype"])) {
    	    if((int)$_SESSION["usertype"] == 1) {
	    	header("Location: src/useraccount.php");
    	    } else {
	    	header("Location: src/sagi.php");
    	    }
    	}
    }
    
    if(isset($_GET["forgot"]) && !isset($_POST['btnForgot'])) {
    	$return = '/Login.php?forgot=1';
    	if($_GET["forgot"] == 1) {
    	?>
<!doctype html>
<html lang="<?php echo htmlspecialchars($current); ?>">
<head>
  <meta charset="utf-8">
  <title><?php echo $lengui['forgotpass'][$current]; ?></title>
  <link rel="icon" href="src/images/logo.webp" type="image/webp">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial; padding: 2rem; }
    p { margin: 10px; }
    #txtUsername { padding: 12px 20px; }
    #txtEmail { padding: 12px 20px; }
    .langs { display: flex; gap: 1rem; flex-wrap: wrap; margin: 10px; }
    .lang { padding: .5rem 1rem; border: 1px solid #ddd; border-radius: 6px; text-decoration: none; color: #111; }
    .lang.active { background: #0b78e3; color: #fff; border-color:#0b78e3; }
    .button { background-color: #6786ab; color: #fff; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; }
  </style>
</head>
<body>
<div class="langs">
  <?php foreach ($supported as $code => $label): 
    $url = '/src/language/language.php?lang=' . rawurlencode($code) . '&return=' . rawurlencode($return);
    $cls = ($code === $current) ? 'lang active' : 'lang';
  ?>
    <a class="<?php echo $cls; ?>" href="<?php echo $url; ?>"><?php echo htmlspecialchars($label); ?></a>
  <?php endforeach; ?>
</div>
<form name="forgotLoginForm" id="forgotLoginForm" action="" method="post" autocomplete="off">
<input name="username" id="txtUsername" type="text" class="form-control" placeholder="<?php echo $lengui['username'][$current]; ?>">
<input name="email" id="txtEmail" type="text" class="form-control" placeholder="<?php echo $lengui['email'][$current]; ?>">
<input type="hidden" name="controlForgot" value="<?php echo $COOKIE_SECURITY; ?>">
<input type="submit" name="btnForgot" class="button" value="Generate a new password">
</form>
<p><a href="Login.php" target="_SELF"><i class="bi bi-arrow-return-left"></i></a></p>
</body>
</html>
    	<?php
    		exit;	
    	}
    }
    
    if(isset($_GET["password_changed"])) {
	if($_GET["password_changed"] == 1) {
    		echo '<script>alert("'.$lengui['updated'][$current].'")</script>';
	}
    }
    
    $connection = mysqli_connect($servername, $user, $pw, $db);			

    if(!$connection) {
	die("Connection failed: " .mysqli_connect_error());
    }
					
    if(isset($_POST['btnLogin'])) {
        $usernameLogin = $_POST['usernameLogin'];
        $passwordLogin = $_POST['passwordLogin'];
        $codeLogin = $_POST['codeLogin'];
        $control = $_POST['control'];
        
        if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
    				   
        if($usernameLogin=="" || $passwordLogin=="" || $codeLogin=="") {
	    echo '<script> alert("'.$lengui['err1'][$current].'") </script>';
        }
        
	$selectqry = "SELECT usr.username, usr.activated, usr.password, req.code, req.info, IF(CURRENT_TIMESTAMP() < DATE_ADD(req.info, INTERVAL +1 HOUR), 'yes', 'no') AS eval FROM user AS usr INNER JOIN request AS req ON (usr.uno = req.user_uno) WHERE username=? LIMIT 1;";
	$stmt = mysqli_prepare($connection, $selectqry);
	mysqli_stmt_bind_param($stmt,'s', $usernameLogin);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	if(mysqli_num_rows($result) > 0) {
	    $row=mysqli_fetch_assoc($result);
	    if($row["activated"] == 0) {
                echo '<script>alert("'.$lengui['err2'][$current].'")</script>';
	    } else {
                if(password_verify($passwordLogin, $row["password"]) && password_verify($codeLogin, $row["code"]) && $row["eval"] == 'yes') {
                    $_SESSION["username"] = $usernameLogin;
                    $_SESSION["usertype"] = 1;
                    header("Location: src/useraccount.php");
                } else {
                    echo '<script>alert("'.$lengui['err3'][$current].'")</script>';
                }
	    }
	    mysqli_free_result($result);
         }
         else {
	    echo '<script>alert("'.$lengui['err4'][$current].'")</script>';  
        }
    }
    
    if(isset($_POST['btnForgot'])) {
        $username = $_POST['username'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = substr(md5(rand(100000,999999)), 12, 8);
        $hashed = password_hash($password,PASSWORD_DEFAULT);
        $control = $_POST['controlForgot'];
        
        if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
    	
        if($username=="" || $email=="") {
	    echo '<script> alert("'.$lengui['err8'][$current].'") </script>';
        }
        
	$selectqry = "SELECT uno, username, email FROM user WHERE username=? AND email=?";
	$stmt = mysqli_prepare($connection, $selectqry);
	mysqli_stmt_bind_param($stmt,'ss', $username, $email);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	if(mysqli_num_rows($result) > 0) {
	    $row=mysqli_fetch_assoc($result);
	    $selectqry_update = "UPDATE user SET password=? WHERE uno=?";
	    $stmt = mysqli_prepare($connection, $selectqry_update);
	    mysqli_stmt_bind_param($stmt,'si', $hashed, $row['uno']);
	    mysqli_stmt_execute($stmt);
	    
	    $mail = new PHPMailer(true);
	    try {
		//Settings
		$mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->Host       = 'smtp.hostname.com';
		$mail->SMTPAuth   = true;
		$mail->Username   = 'noreply@hostname.com';
		$mail->Password   = 'password123';
		$mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port       = 465;
		//Recipients
		$mail->setFrom('noreply@hostname.com', 'ISMS');
		$mail->addAddress($email, 'ISMS User'); //Add a recipient

		//Content
		$mail->CharSet = 'UTF-8'; 
		$mail->Encoding = 'base64';
		$mail->isHTML(true); //Set email format to HTML
		$mail->Subject = 'Dėl ISMS slaptažodžio';
		$mail->Body    = "Paskyros ".$username." naujasis slaptažodis: ".$password;
		$mail->AltBody = "Paskyros ".$username." naujasis slaptažodis: ".$password;
    				
		$mail->send();
		} catch (Exception $e) {
		    echo "{$mail->ErrorInfo}";
		}
		header("Location: Login.php?password_changed=1");
         }
         else {
	    echo '<script>alert("'.$lengui['err4'][$current].'")</script>';  
        }
        mysqli_free_result($result);
    }
				                                                         
    if(isset($_POST['btnSignUp'])) {
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$username = $_POST['usernameAdm'];
	$pw = $_POST['pw'];
	$hashedpw = password_hash($pw,PASSWORD_DEFAULT);
	$reppw = $_POST['reppw'];
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$activated = 0;
	$termsofservice = ($_POST['termsofservice'] == 'on')? 1 : 0;
        $control = $_POST['controlSignUp'];
	
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$checkqry = "SELECT uno, username FROM user WHERE username=?;";
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'s', $username);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if($username=="" || $pw=="" || $reppw=="" || $email=="" || $termsofservice==0) {
            echo '<script> alert("'.$lengui['err5'][$current].'") </script>';
        }
	elseif(mysqli_num_rows($result2) > 0) {
	    echo '<script> alert("'.$lengui['err6'][$current].'") </script>';    
	}
        elseif($pw!=$reppw) {			
	    echo '<script> alert("'.$lengui['err7'][$current].'") </script>';				    
	}
        else {
	    $insertAdm = "INSERT INTO user (first_name,last_name,username,email,password,activated,termsofservice) 
		VALUES (?,?,?,?,?,?,?)";
	    
	    $insertprepare = mysqli_prepare($connection, $insertAdm);
	    mysqli_stmt_bind_param($insertprepare, 'sssssii', $fname, $lname, $username, $email, $hashedpw,$activated,$termsofservice);
	    mysqli_stmt_execute($insertprepare);
	    
	    $selectqry = "SELECT uno, username, confirmation FROM user WHERE username=?;";
	    $stmt = mysqli_prepare($connection, $selectqry);
	    mysqli_stmt_bind_param($stmt,'s', $username);
	    mysqli_stmt_execute($stmt);
	    $result = mysqli_stmt_get_result($stmt);
	    
	    if(mysqli_num_rows($result) > 0) {
		$row=mysqli_fetch_assoc($result);
		$l = (($current == 'en')? 1 : (($current == 'lt')? 2 : 3));
		$mail = new PHPMailer(true);
		try {
		    //Settings
		    $mail->SMTPDebug = 0;
		    $mail->isSMTP();
		    $mail->Host       = 'smtp.hostname.com';
		    $mail->SMTPAuth   = true;
		    $mail->Username   = 'noreply@hostname.com';
		    $mail->Password   = 'password123';
		    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
		    $mail->Port       = 465;
		    //Recipients
		    $mail->setFrom('noreply@hostname.com', 'ISMS');
		    $mail->addAddress($email, 'ISMS User'); //Add a recipient

		    //Content
		    $mail->CharSet = 'UTF-8'; 
		    $mail->Encoding = 'base64';
		    $mail->isHTML(true); //Set email format to HTML
		    $mail->Subject = 'Dėl ISMS paskyros aktyvavimo';
		    $mail->Body    = "Patvirtinkite savo ".$username." paskyrą ISMS: <a href='https://hostname.com/Login.php?"."approval=".$row['confirmation']."&lang=".$l."&ao=".$row['uno']."' target='_BLANK'>https://hostname.com/Login.php?"."approval=".$row['confirmation']."&lang=".$l."&ao=".$row['uno']."</a>. Jeigu neteikėte paklausimo ISMS, tuomet atsiprašome už Jūsų sugaištą laiką. Pagarbiai, ISMS komanda.";
		    $mail->AltBody = "Paklausimo patvirtinimo adresas: https://hostname.com/Login.php?"."approval=".$row['confirmation']."&lang=".$l."&ao=".$row['uno']." Jeigu neteikėte paklausimo ISMS, tuomet atsiprašome už sugaištą Jūsų laiką. Pagarbiai, ISMS komanda.";
    				
		    $mail->send();
		    } catch (Exception $e) {
		        echo "{$mail->ErrorInfo}";
		    }
		    echo '<script> alert("'.$lengui['notice'][$current].'") </script>';
	    }
	}
	mysqli_free_result($result);
	mysqli_free_result($result2);
    }
    
    if(isset($_POST['btn2fa'])) {
	$username = $_POST['username2fa'];
	$email = filter_var($_POST['email2fa'], FILTER_SANITIZE_EMAIL);
	$scode = rand(100000,999999);
	$pcode = password_hash($scode, PASSWORD_ARGON2I);
        $control = $_POST['control2fa'];
        	
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$checkqry = "SELECT uno, username, email, activated FROM user WHERE username=? AND email=?;";
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'ss', $username, $email);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if($username=="" || $email=="") {
            echo '<script> alert("'.$lengui['err5'][$current].'") </script>';
        }
	elseif(mysqli_num_rows($result2) > 0) {
	    $row=mysqli_fetch_assoc($result2);
	    if($row['activated'] == 0) {
	        echo '<script> alert("'.$lengui['err2'][$current].'") </script>';
	    } else {
	        $removeReq = "DELETE FROM request WHERE user_uno=?;";
	        
	        $removeprepare = mysqli_prepare($connection, $removeReq);
	        mysqli_stmt_bind_param($removeprepare, 'i', $row['uno']);
	        mysqli_stmt_execute($removeprepare);
	        
	        $insertReq = "INSERT INTO request (user_uno,code) 
	            VALUES (?,?)";
	        
	        $insertprepare = mysqli_prepare($connection, $insertReq);
	        mysqli_stmt_bind_param($insertprepare, 'is', $row['uno'], $pcode);
	        mysqli_stmt_execute($insertprepare);
	        
	        $selectqry = "SELECT un, user_uno, code FROM request WHERE user_uno=?;";
	        $stmt = mysqli_prepare($connection, $selectqry);
	        mysqli_stmt_bind_param($stmt,'i', $row['uno']);
	        mysqli_stmt_execute($stmt);
	        $result = mysqli_stmt_get_result($stmt);
	        
	        if(mysqli_num_rows($result) > 0) {
		    $row=mysqli_fetch_assoc($result);
		    $mail = new PHPMailer(true);
		    try {
		        //Settings
		        $mail->SMTPDebug = 0;
		        $mail->isSMTP();
		        $mail->Host       = 'smtp.hostname.com';
		        $mail->SMTPAuth   = true;
		        $mail->Username   = 'noreply@hostname.com';
		        $mail->Password   = 'password123';
		        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
		        $mail->Port       = 465;
		        //Recipients
		        $mail->setFrom('noreply@hostname.com', 'ISMS');
		        $mail->addAddress($email, 'ISMS User'); //Add a recipient
                        
		        //Content
		        $mail->CharSet = 'UTF-8'; 
		        $mail->Encoding = 'base64';
		        $mail->isHTML(true); //Set email format to HTML
		        $mail->Subject = '2FA';
		        $mail->Body    = "2FA ".$lengui['hour'][$current]." ".$username.": ".$scode;
		        $mail->AltBody = "2FA ".$lengui['hour'][$current]." ".$username.": ".$scode;
		        
		        $mail->send();
		        } catch (Exception $e) {
		            echo "{$mail->ErrorInfo}";
		    }
		    sleep(5);
		    echo '<script> alert("'.$lengui['notice2fa'][$current].'") </script>';
	        }
	    }
	}
	mysqli_free_result($result);
	mysqli_free_result($result2);
    }
    
    if(isset($_POST['btnSpectator'])) {
	$termsofservice = ($_POST['termsofserviceSpectator'] == 'on')? 1 : 0;
        $control = $_POST['controlSignUp'];
    	
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
        
        if($termsofservice) {
	    $_SESSION["username"] = 'none';
	    $_SESSION["usertype"] = 0;
	    header("Location: src/sagi.php");
	}
    }
?>
<!DOCTYPE html>  
<html lang="<?php echo $lengui['langu'][$current]; ?>">
<head>
<meta charset="utf-8">
<title><?php echo $lengui['login'][$current]; ?></title>
<link rel="icon" href="src/images/logo.webp" type="image/webp">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<style>
@import url("https://fonts.googleapis.com/css?family=Lato");

/* Main Tabs */
label {
 background-color: #173457;
 color: white;
 display: inline-block;
 cursor: pointer;
 padding: 10px;
 font-size: 20px;
 border-color: black;
 border-style: solid;
 border-width: 0.5px;
 width:455px;
 opacity:0.95;
 border-radius: 10px;
}

label:hover {
 background-color: #02404b;
}

label input:checked {
 background-color: red;
}

.tab-radio {
 display: none;
}

/* Tabs behaviour, hidden if not checked/clicked */
.sub-tab-content,
.tab-content {
 display: none;
}

.tab-radio:checked + .tab-content,
.tab-radio:checked + .sub-tab-content {
 display: block;
}

/* Sub-tabs */
.sub-tabs-container label {
 background-color: #1f4147;
 color: white;
}

.sub-tabs-container label:hover {
 background-color: #50bcbf;
 color:black;
}

/* Tabs Content */
.tab-content {
 padding: 30px;
 background-color: #173457;
 border-radius: 10px;
 border-color: black;
 border-style: solid;
 border-width: 0.5px;
 box-shadow: 2px 10px 6px -3px rgba(0, 0, 0, 0.5);
 width:60%;
 height:80%;
 opacity:0.95;
 text-align:center;
}

/* General */

body {
 width: 90%;
 margin: 10px auto;
 background-image:url("src/images/bg2.png");
 background-repeat: repeat-y;
 background-size: cover;
 font-family: Lato, sans-serif;
 letter-spacing: 1px;
}

*, *:hover {
 transition: all .3s;
}

.button {
 background-color: #6786ab;
 color: black;
 padding: 12px 20px;
 border: none;
 border-radius: 4px;
 cursor: pointer;
}

.form-control {
 width:100%;
}

td {
 padding: 10px;
}

/* Language */

.langs { 
 display: flex; 
 gap: 1rem; 
 flex-wrap: wrap;
 margin: 10px;
 align-items: center;
 justify-content: center;
}

.lang { 
 padding: .5rem 1rem; 
 border: 1px solid #000; 
 border-radius: 6px; 
 text-decoration: none; 
 color: #fff;
 background-color: #173457;
 box-shadow: 2px 10px 6px -3px rgba(0, 0, 0, 0.5);
 opacity: 0.95;
}

.lang.active { 
 background: #0b78e3;
 border-color: #0b78e3; 
}

/* Footer */


.reads { 
 display: flex; 
 gap: 1rem; 
 flex-wrap: wrap;
 margin: 10px;
 align-items: center;
 justify-content: center;
}

.readme { 
 padding: .5rem 1rem; 
 border: 1px solid #000; 
 border-radius: 6px; 
 text-decoration: none; 
 color: #fff;
 background-color: #173457;
 box-shadow: 2px 10px 6px -3px rgba(0, 0, 0, 0.5);
 opacity: 0.95;
}

</style>
</head>

<body>
<section>
<center>
<div>

<div class="langs">
    <?php foreach ($supported as $code => $label): 
        $url = '/src/language/language.php?lang=' . rawurlencode($code) . '&return=' . rawurlencode($return);
        $cls = ($code === $current) ? 'lang active' : 'lang';
    ?>
        <a class="<?php echo $cls; ?>" href="<?php echo $url; ?>"><?php echo htmlspecialchars($label); ?></a>
    <?php endforeach; ?>
</div>

<div class="top-tabs-container">
    <label for="main-tab-1"><?php echo $lengui['login'][$current]; ?></label>
    <label for="main-tab-2"><?php echo $lengui['signup'][$current]; ?></label>
    <label for="main-tab-3"><?php echo $lengui['2fa'][$current]; ?></label>
    <label for="main-tab-4"><?php echo $lengui['spec'][$current]; ?></label>
</div>

<!-- Tab Container -->
<form name="loginSignupForm" id="loginSignupForm" action="" method="post">
<input class="tab-radio" id="main-tab-1" name="main-group" type="radio" checked="checked">

<div class="tab-content">
 
    <table style="padding:50px;">
        <tr>
            <div class="col">
                <td>
		    <input name="usernameLogin" id="txtUsername" type="text" class="form-control" placeholder="<?php echo $lengui['entuser'][$current]; ?>">
		</td>
            </div>
        </tr>
	<tr>
            <div class="col">
                <td>
		    <input name="passwordLogin" id="txtPassword" type="password" class="form-control" data-type="password" placeholder="<?php echo $lengui['entpass'][$current]; ?>" autocomplete="off">
		</td>
            </div>
        </tr>
 	<tr>
            <div class="col">
                <td>
		    <input name="codeLogin" id="txtCode" type="password" class="form-control" data-type="password" placeholder="<?php echo $lengui['entcode'][$current]; ?>" autocomplete="off"><input type="hidden" name="control" value="<?php echo $COOKIE_SECURITY; ?>">
		</td>
            </div>
        </tr>
        <tr>
            <td>
                <div class="" style="width:500px;margin:20px;">
                    <input name="btnLogin" type="submit" class="button" value="<?php echo $lengui['signin'][$current]; ?>" style="color:white; font-weight:bold;" id="btnSignin" onclick="JavaScript:return validateLoginForm();" > <br>
                </div>
                <div class="hr">
		</div>
                <div class="foot"> 
		    <a href="Login.php?forgot=1"><?php echo $lengui['forgotpass'][$current]; ?></a>
		</div>
            </td>
        </tr>
    </table>
</div>

<!-- Tab Container -->

<input class="tab-radio" id="main-tab-2" name="main-group" type="radio">

<div class="tab-content">

  <div class="sub-tabs-container">
    <label for="sub-tab2-1" style="width:150px;font-size:12.5px;height:40px;font-weight:bold;"><?php echo $lengui['admin'][$current]; ?></label>
  </div>
  
  <!-- Sub Tab -->
  <!-- NOTE: name="sub-group" will require to be unique to the tab, 
        ie: tab2 = sub-group2, tab3 = sub-group 3 etc. -->
  <!-- NOTE: id have to be unique. So for each sub tabs, the input id will have to change-->
  
  <input class="tab-radio" id="sub-tab2-1" name="sub-group2" type="radio" checked="checked">
  <div class="sub-tab-content">
        <table>
            <tr>
                <td class="sign-up-table">
                    <!--fname lname-->
                    <div class="">
                        <input  name="fname" id="txtFname" type="text" class="form-control" placeholder="<?php echo $lengui['fname'][$current]; ?>">
                    </div>
                </td>
                <td class="" style="width:50%;">
                    <!--lname-->
                    <div class="sign-up-table">
                        <input name="lname" id="txtLname" type="text" class="form-control" placeholder="<?php echo $lengui['lname'][$current]; ?>">
                    </div>
                </td>
            </tr>
            <tr>
                <td class="sign-up-table" style="width:50%;">
                    <!--username-->
                    <div class="">
                        <input name="usernameAdm" id="txtUsername" type="text" class="form-control" placeholder="<?php echo $lengui['username'][$current]; ?>">
                    </div>
                </td>
                <td class="sign-up-table">
                    <!--email-->
                    <div class="">
                        <input name="email" id="txtEmail" type="text" class="form-control" placeholder="<?php echo $lengui['email'][$current]; ?>">
                    </div>
		</td>
            </tr>
            <tr>
                <td class="sign-up-table">
                    <!--pass-->
                    <div class="">
                        <input name="pw" id="txtSignUpPassword" type="password" class="form-control" data-type="password" placeholder="<?php echo $lengui['crtpass'][$current]; ?>" autocomplete="off">
                    </div>
                </td>
                <td class="sign-up-table">
                    <!--rep pass-->
                    <div class="">
                        <input name="reppw" id="txtSignUpRepPass" type="password" class="form-control" data-type="password" placeholder="<?php echo $lengui['rptpass'][$current]; ?>" autocomplete="off">
                    </div>
                </td>
            </tr>
	    <tr>
		<td colspan="2" class="">
		    <input name="termsofservice" id="approveTermsOfService" type="checkbox">
		    <label for="approveTermsOfService"><?php echo $lengui['terms'][$current]; ?></label>
		    <input type="hidden" name="controlSignUp" value="<?php echo $COOKIE_SECURITY; ?>">
		</td>
	    </tr>
            <tr>
                <td colspan="2" class="sign-up-table">
                    <div class="" style="margin:20px;">
                        <input type="submit" name="btnSignUp" class="button" value="Sign Up" style="color:white; font-weight:bold;" id="btnSignUp" onclick="JavaScript:return validateSignupForm();">
                    </div>
                    <div class="hr">&nbsp;</div>
                    <div class="foot">
                        <label for="main-tab-1" style="font-size:16px;border:0px; width: 160px;"><?php echo $lengui['alreadymemb'][$current]; ?></label>
                    </div>
                </td>
            </tr>
        </table>
  </div>
</div>

<!-- Tab Container -->

<input class="tab-radio" id="main-tab-3" name="main-group" type="radio">

<div class="tab-content">
        <table>
	    <tr>
		 <td style="width:50%;">
                    <!--username-->
                    <div class="">
                        <input name="username2fa" style="width:100%;" type="text" class="form-control" placeholder="<?php echo $lengui['username'][$current]; ?>">
                    </div>
                </td>
                <td>
                    <!--email-->
                    <div class="">
                        <input name="email2fa" style="width:100%;" type="text" class="form-control" placeholder="<?php echo $lengui['email'][$current]; ?>">
                        <input type="hidden" name="control2fa" value="<?php echo $COOKIE_SECURITY; ?>">
                    </div>
		</td>
	    </tr>
            <tr>
                <td colspan="2" class="">
                    <div class="" style="margin:20px;">
                        <input type="submit" name="btn2fa" class="button" value="Continue" style="color:white; font-weight:bold;" id="btn2fa" onclick="JavaScript:return validate2faForm();">
                    </div>
                </td>
            </tr>
        </table>
</div>

<!-- Tab Container -->

<input class="tab-radio" id="main-tab-4" name="main-group" type="radio">

<div class="tab-content">
        <table>
	    <tr>
		<td colspan="2" class="">
		    <input name="termsofserviceSpectator" id="approveTermsOfServiceSpectator" type="checkbox">
		    <label for="approveTermsOfServiceSpectator"><?php echo $lengui['terms'][$current]; ?></label>
		    <input type="hidden" name="controlSpectator" value="<?php echo $COOKIE_SECURITY; ?>">
		</td>
	    </tr>
            <tr>
                <td colspan="2" class="">
                    <div class="" style="margin:20px;">
                        <input type="submit" name="btnSpectator" class="button" value="Continue" style="color:white; font-weight:bold;" id="btnSpectator" onclick="JavaScript:return validateSpectatorForm();">
                    </div>
                </td>
            </tr>
        </table>
</div>

</form>

<div class="reads">
        <a class="readme" href="/termsofservice_<?php echo $current; ?>.html"><?php echo $lengui['termspage'][$current]; ?></a>
        <a class="readme" href="/termsofprivacy_<?php echo $current; ?>.html"><?php echo $lengui['privacypage'][$current]; ?></a>
</div>

</div>
</center>
<script type="text/javascript">
	function validateLoginForm() {
	    var succeed = true;

	    var usr_name = document.querySelector( "input[name='usernameLogin']" );
	    let usr_name_v = usr_name.value;
	    var pass = document.querySelector( "input[name='passwordLogin']" );
	    let pass_v = pass.value;

	    let lUnsafeCharacters = /[\W|_]/g;
	    
	    if (usr_name_v.length < 1) {
		<?php echo "alert('".$lengui['serr1'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (usr_name_v.length > 25) {
		<?php echo "alert('".$lengui['serr2'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (usr_name_v.search(lUnsafeCharacters) > -1 || pass_v.search(lUnsafeCharacters) > -1){
		<?php echo "alert('".$lengui['serr3'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    
	    if(succeed == true) {
		document.getElementById('loginSignupForm').name='btnLogin';
		document.getElementById('loginSignupForm').action='';
		document.getElementById('loginSignupForm').submit();
		return(true);
	    } else {
		return(false);
	    }
	}
	
	function validateSignupForm() {
	    var succeed = true;
	    
	    var frt_name = document.querySelector( "input[name='fname']" );
	    let frt_name_v = frt_name.value;
	    var lst_name = document.querySelector( "input[name='lname']" );
	    let lst_name_v = lst_name.value;
	    var psd_name = document.querySelector( "input[name='username']" );
	    let psd_name_v = psd_name.value;
	    var eml_addr = document.querySelector( "input[name='email']" );
	    let eml_addr_v = eml_addr.value;
	    
	    var tos = document.getElementById( "approveTermsOfService" );
	    let tos_c = tos.checked;
	    
	    var pwd = document.querySelector( "input[name='pw']" );
	    let pwd_v = pwd.value;
	    var rtpwd = document.querySelector( "input[name='reppw']" );
	    let rtpwd_v = rtpwd.value;
	    
	    let lUnsafeCharacters = /[\W|_]/g;
	    let lemail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-z\-0-9]+\.)+[a-z]{2,}))$/;
	    
	    if (frt_name_v != '' && frt_name_v.length > 40) {
		<?php echo "alert('".$lengui['serr4'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (lst_name_v != '' && lst_name_v.length > 90) {
		<?php echo "alert('".$lengui['serr5'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (psd_name_v.length < 1 || psd_name_v.length > 25) {
		<?php echo "alert('".$lengui['serr6'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (psd_name_v != '' && psd_name_v.search(lUnsafeCharacters) > -1) {
		<?php echo "alert('".$lengui['serr3'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (eml_addr_v.length < 6 || eml_addr_v.length > 50) {
		<?php echo "alert('".$lengui['serr7'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (eml_addr_v != '' && !lemail.test(eml_addr_v)) {
		<?php echo "alert('".$lengui['serr7'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (pwd_v == '' || rtpwd_v == '') {
		<?php echo "alert('".$lengui['serr8'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (pwd_v != '' && pwd_v.search(lUnsafeCharacters) > -1) {
		<?php echo "alert('".$lengui['serr3'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (pwd_v != rtpwd_v) {
		<?php echo "alert('".$lengui['serr9'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (tos_c != true) {
		<?php echo "alert('".$lengui['serr10'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    
	    if(succeed == true) {
		document.getElementById('loginSignupForm').name='btnSignUp';
		document.getElementById('loginSignupForm').action='';
		document.getElementById('loginSignupForm').submit();
		return(true);
	    }else{
		return(false);
	    }
	}
	
	function validate2faForm() {
	    var succeed = true;
	    
	    var psd_name = document.querySelector( "input[name='username2fa']" );
	    let psd_name_v = psd_name.value;
	    var eml_addr = document.querySelector( "input[name='email2fa']" );
	    let eml_addr_v = eml_addr.value;
	    
	    let lUnsafeCharacters = /[\W|_]/g;
	    let lemail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-z\-0-9]+\.)+[a-z]{2,}))$/;
	    
	    if (psd_name_v.length < 1 || psd_name_v.length > 25) {
		<?php echo "alert('".$lengui['serr6'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (psd_name_v != '' && psd_name_v.search(lUnsafeCharacters) > -1) {
		<?php echo "alert('".$lengui['serr3'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (eml_addr_v.length < 6 || eml_addr_v.length > 50) {
		<?php echo "alert('".$lengui['serr7'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    if (eml_addr_v != '' && !lemail.test(eml_addr_v)) {
		<?php echo "alert('".$lengui['serr7'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    
	    if(succeed == true) {
		document.getElementById('loginSignupForm').name='btn2fa';
		document.getElementById('loginSignupForm').action='';
		document.getElementById('loginSignupForm').submit();
		return(true);
	    }else{
		return(false);
	    }
	}
	
	function validateSpectatorForm() {
	    var succeed = true;
	    var tos = document.getElementById( "approveTermsOfServiceSpectator" );
	    let tos_c = tos.checked;
	    
	    if (tos_c != true){
		<?php echo "alert('".$lengui['serr10'][$current]."');"; ?>
		succeed = false;
	    }// end if
	    
	    if(succeed == true){
		document.getElementById('loginSignupForm').name='btnSpectator';
		document.getElementById('loginSignupForm').action='';
		document.getElementById('loginSignupForm').submit();
		return(true);
	    }else{
		return(false);
	    }
	}
</script>
</body>
</html>
