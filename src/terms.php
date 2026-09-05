<?php 
session_start();
if(!isset($_SESSION["username"]) || (isset($_SESSION["usertype"]) && $_SESSION["usertype"] != 1)) {
	header("Location: Login.php?action=login");  
}

require_once __DIR__ . '/notphish.php';
require_once __DIR__ . '/language/language.php';

$supported = [
        'en' => 'English',
        'lt' => 'Lietuvių',
        'pl' => 'Polska'
    ];
    
    $current = defined('SITE_LANG') ? SITE_LANG : ($_COOKIE['site_lang'] ?? 'en');
    
    $return = '/src/terms.php';
    
    // Simple language chooser
    $lengui = array(
        'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
        'title' => array ('en' => 'Terms', 'lt' => 'Sąlygos', 'pl' => 'Warunki'),
        'site' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
        'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wylogui'),
        'welcome' => array ('en' => 'Welcome', 'lt' => 'Sveikiname', 'pl' => 'Witamy'),
        'name' => array ('en' => 'Name:', 'lt' => 'Vardas:', 'pl' => 'Imię:'),
        'type' => array ('en' => 'Type:', 'lt' => 'Rūšis:', 'pl' => 'Typ:'),
        'administrator' => array ('en' => 'Administrator', 'lt' => 'Administratorius', 'pl' => 'Administrator'),
        'created' => array ('en' => 'Created:', 'lt' => 'Sukurta:', 'pl' => 'Utworzono:'),
        'initiate' => array ('en' => 'Initiate:', 'lt' => 'Inicijuoti:', 'pl' => 'Inicjować:'),
        'home' => array ('en' => 'Home', 'lt' => 'Pradžia', 'pl' => 'Strona główna'),
        'changepass' => array ('en' => 'Password change', 'lt' => 'Keisti slaptažodį', 'pl' => 'Zmiana hasła'),
        'terms' => array ('en' => 'Terms', 'lt' => 'Sąlygos', 'pl' => 'Warunki'),
        'removeacc' => array ('en' => 'Remove this account', 'lt' => 'Pašalinti šią paskyrą', 'pl' => 'Usuń to konto'),
        'cookie' => array ('en' => 'Data of Cookie is wrong.', 'lt' => 'Neteisingi slapuko duomenys.', 'pl' => 'Błędne dane Cookie.'),
        'err1' => array ('en' => 'Enter required fields.', 'lt' => 'Įvesti reikalaujamus laukus.', 'pl' => 'Wprowadz wymaganych pól.'),
        'err7' => array ('en' => 'Wrong User Details No. 7.', 'lt' => 'Neteisinga naudotojo pateiktis Nr. 7.', 'pl' => 'Błędne dane użytkownika Nr. 7.'),
        'err8' => array ('en' => 'Wrong User Details No. 8.', 'lt' => 'Neteisinga naudotojo pateiktis Nr. 8.', 'pl' => 'Błędne dane użytkownika Nr. 8.'),
        'success' => array ('en' => 'Terms was updated.', 'lt' => 'Sąlygos buvo atnaujintos.', 'pl' => 'Warunki było zaktualizowane.'),        
        'table' => array ('en' => 'Official information of the Site', 'lt' => 'Oficiali svetainės informacija', 'pl' => 'Oficjalne informacje o Stronie'),
        'one' => array ('en' => "HTTP Cookie technology is mandatory for this Site. The Site does not deploy special HTTP Cookies for advertisement or user tracking. This Site uses 'site_lang' & 'site_notphish' HTTP Cookies while first one is for user's language preference change & second one - protective measure from phishing attacks. The mandatory 'PHPSESSID' HTTP Cookie allows for each user to get user's unique account, get a hold of role-based access control.", 'lt' => "HTTP slapukų technologija yra privaloma šiai Svetainei. Svetainė nediegia specialių HTTP slapukų reklamai ar vartotojų sekimui. Ši Svetainė naudoja 'site_lang' & 'site_notphish' HTTP slapukus, kai pirmasis yra skirtas vartotojo kalbos pasirinkimo keitimui, o antrasis - apsauginė priemonė nuo sukčiavimo atakų. Privalomas 'PHPSESSID' HTTP slapukas leidžia kiekvienam vartotojui gauti vartotojo unikalią paskyrą, gauti vaidmenimis pagrįstą prieigos kontrolę.", 'pl' => "Technologia HTTP Cookie jest obowiązkowa dla tej Strony. Strona nie wdraża specjalnych HTTP Cookie do celów reklamowych lub śledzenia użytkowników. Ta Strona używa 'site_lang' & 'site_notphish' HTTP Cookie, podczas gdy pierwszy jest dla zmiany preferencji językowych użytkownika, a drugi - środek ochronny przed atakami phishingowymi. Obowiązkowe 'PHPSESSID' HTTP Cookie pozwala każdemu użytkownikowi uzyskać unikalny rachunek użytkownika, uzyskać kontrolę dostępu opartą na rolach."),
        'two' => array ('en' => "The Site's security representative, data protection officer, computer security incident response team accept any Security, Privacy and Cybersecurity breach Notification(s) according the Resposible Disclosure Terms.", 'lt' => "Svetainės saugumo atstovas, duomenų apsaugos pareigūnas, kompiuterinio saugumo incidentų reagavimo komanda priima bet kokį Saugumo, Privatumo ir Kibernetinio saugumo pažeidimo pranešimą(-us) pagal atsakingos informacijos atskleidimo sąlygas.", 'pl' => "Przedstawiciel ds. bezpieczeństwa witryny, inspektor ochrony danych, zespół reagowania na incydenty komputerowe akceptują wszelkie powiadomienia o naruszeniu bezpieczeństwa, prywatności i cyberbezpieczeństwa zgodnie z warunkami odpowiedzialnego ujawniania informacji."),
        'three' => array ('en' => 'Information about storage & management & disposal of User data are available in the Terms of Privacy.', 'lt' => 'Informacija apie Vartotojo duomenų saugojimą, tvarkymą ir šalinimą pateikiama privatumo sąlygose.', 'pl' => 'Informacje o przechowywaniu, zarządzaniu i usuwaniu danych użytkownika są dostępne w warunkach prywatności.'),
        'four' => array ('en' => "Agreement between service provider and service consumer, copyright compliance or services's License Terms offer to service consumers are found in the Terms of Service.", 'lt' => "Paslaugų teikėjo ir paslaugų vartotojo susitarimas, autorių teisių laikymasis arba paslaugų licencijos sąlygų pasiūlymas paslaugų vartotojams yra pateiktas Paslaugų teikimo sąlygose.", 'pl' => "Umowa między dostawcą usług a konsumentem usług, zgodność z prawem autorskim lub warunki licencji usług oferowanych konsumentom usług znajdują się w warunkach usługi."),
        'five' => array ('en' => "When service offer purchases of Goods (e.g. Colorful Photo Prints) and Services (e.g. subscription of newsletter & newspaper), then plausible disputes regarding communicated Quality of Goods and Services are found in the Return and Refund Policy Terms.", 'lt' => "Kai paslauga siūlo įsigyti prekes (pvz., spalvotas nuotraukų spausdinimas) ir paslaugas (pvz. naujienlaiškio ir laikraščio prenumerata), tada tikėtini ginčai dėl perduotos prekių ir paslaugų kokybės yra rasti grąžinimo ir pinigų grąžinimo politikos sąlygose.", 'pl' => "Gdy usługa oferuje zakup towarów (np. kolorowe wydruki zdjęć) i usług (np. subskrypcja biuletynu i gazety), wówczas wiarygodne spory dotyczące przekazywanej jakości towarów i usług znajdują się w warunkach polityki zwrotów i zwrotów."),
        'six' => array ('en' => "When service offer delivery of Goods (e.g. Colorful Photo Prints) and Services (e.g. payment of subscription, tracking of purchased Goods), then delivery rules, descriptions, terms and definitions are found in the Shipping Policy Terms.", 'lt' => "Kai paslauga siūlo prekių (pvz. spalvotų nuotraukų spaudinių) ir paslaugų (pvz. prenumeratos mokėjimo, įsigytų prekių sekimo) pristatymą, tada pristatymo taisyklės, aprašymai, sąlygos ir apibrėžimai pateikiami siuntimo politikos sąlygose.", 'pl' => "W przypadku oferowania przez serwis dostawy towarów (np. kolorowe wydruki zdjęć) i usług (np. płatność za subskrypcję, śledzenie zakupionych towarów), wówczas zasady dostawy, opisy, warunki i definicje znajdują się w warunkach polityki wysyłkowej."),
        'seven' => array ('en' => "Consumers must receive current Contact Information of Service provider, that are found in the Contact Information Terms.", 'lt' => "Vartotojai turi gauti aktualią paslaugos teikėjo kontaktinę informaciją, kuri yra nurodyta kontaktinės informacijos sąlygose.", 'pl' => "Konsumenci muszą otrzymać aktualne dane kontaktowe usługodawcy, które znajdują się w Warunkach danych kontaktowych."),
        'eight' => array ('en' => "Service provider include short information about jurisdictional entity who is offering this Service, that are available in the Legal notice.", 'lt' => "Paslaugos teikėjas pateikia trumpą informaciją apie jurisdikcinį subjektą, kuris siūlo šią paslaugą, kuri yra prieinama teisinėje informacijoje.", 'pl' => "Dostawca usług zawiera krótkie informacje o podmiocie jurysdykcyjnym, który oferuje tę usługę, które są dostępne w informacjach prawnych."),
	'approval' => array ('en' => 'Following approval in force', 'lt' => 'Sekantis įsigaliojantis patvirtinimas', 'pl' => 'Po zatwierdzeniu w mocy'),
	'password' => array ('en' => 'Your password', 'lt' => 'Tavo slaptažodis', 'pl' => 'Twoje hasło'),
        'accept' => array ('en' => 'I\'m accepting Terms of Service of this Site.', 'lt' => 'Priimu šio saito paslaugos sąlygas.', 'pl' => 'Akceptuję warunki korzystania z tej strony.'),
        'accepted' => array ('en' => 'You have accepted Terms of Service of this Site. Any update of Terms of Service could make this agreement void.', 'lt' => 'Priėmei šio saito paslaugos sąlygas. Paslaugos sąlygų atnaujinimas gali atšaukti šį susitarimą.', 'pl' => 'Zaakceptowałeś warunki korzystania z tej strony. Każda aktualizacja warunków korzystania może unieważnić tę umowę.'),
        'button' => array ('en' => 'Accept It', 'lt' => 'Priimti tai', 'pl' => 'Zaakceptuj to'),
        'question' => array ('en' => 'Do You want to proceed it further?', 'lt' => 'Ar pageidauji tęsti?', 'pl' => 'Chcesz to kontynuować?')
    );

require_once __DIR__ . '/credens.php';

$connection = mysqli_connect($servername, $user, $pw, $db);			

if(!$connection)
{
	die("Connection failed: " .mysqli_connect_error());
}
?>  

<!DOCTYPE html>  
<html lang="<?php echo $lengui['langu'][$current]; ?>">  
<head>
<meta charset="utf-8">
<title><?php echo $lengui['title'][$current]; ?></title>
<link rel="icon" href="images/logo.webp" type="image/webp">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<style>
a.active {
	color: #D34143;
}

.user-cell{
	padding: 0px;
}

table.view-table{
	width: 100%;
	margin-left: auto;
	margin-right: auto;
	background-color: rgba(192, 192, 192, 0.5);
	color: black;
	margin-bottom: 20px;
}

tr.view-row{
	border-collapse: collapse;
}

td.view-cell{
	padding: 10px 10px 10px 10px; /*top right bottom left*/
}

.edit-icons{
	height: 25px;
	width: 25px;
}

.edit-buttons{
	padding: 8px;
	border: 1px solid black;
	background-color: #173457;
	border-radius: 20px;
}

.edit-buttons:hover{
	background-color: rgba(250, 250, 250, 0.5);
	border: 0px;
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
</style>
</head>
<body style="background: #173457;">

	<!-- Navigation Bar -->
	
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#"><?php echo $lengui['site'][$current]; ?></a>
			</div>
    			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo htmlentities($_SESSION["username"], ENT_QUOTES, 'UTF-8'); ?> </a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span><?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>
	
<?php

/* Update Terms */

if(isset($_POST['btnUpdateTerms']) && ($_SESSION["usertype"] == 1))
{
	$oldpwUser = $_POST['oldpwUser'];
	$username = $_SESSION['username'];
	$usertype = (int)$_SESSION["usertype"];
	$control = $_POST['control'];
		
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$selectqry = "SELECT * FROM user WHERE username=? AND usertype=?;";
	$stmt = mysqli_prepare($connection, $selectqry);
	mysqli_stmt_bind_param($stmt,'si', $username, $usertype);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	
	if($oldpwUser=="") {
	    echo '<script>alert("'.$lengui['err1'][$current].'")</script>';
	} else {
		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_array($result)) {
				if(empty($row["termsofservice"])) {
					$termsofservice = ($_POST['termsofservice'] == 'on')? 1 : 0;
				}
				
				if(password_verify($oldpwUser, $row["password"])) {
					if(empty($row["termsofservice"])) {
						$update = "	UPDATE 
									user
								SET
									termsofservice = ?
								WHERE
									uno = ?;";
			
						$updateprepare = mysqli_prepare($connection, $update);
						mysqli_stmt_bind_param($updateprepare, 'ii', $termsofservice, $row["uno"]);
						mysqli_stmt_execute($updateprepare);
					}
					
					echo '<script>alert("'.$lengui['success'][$current].'")</script>';
				}
				else
				{
					echo '<script>alert("'.$lengui['err7'][$current].'")</script>';
				}				
			}
		}
		else  
		{
			echo '<script>alert("'.$lengui['err8'][$current].'")</script>';
		}
	}
	mysqli_free_result($result);
}
?>
	
	<!-- Displaying user info -->
 
	<div class="container" style="width: 100%; color: white; display: flex;">

<?php  
$username = $_SESSION["username"];

$selectUserDataqry = "	SELECT 
				uno,
				CONCAT(first_name, ' ', last_name) AS fullname,
				email,
				activated,
				termsofservice,
				created
			FROM
				user
			WHERE
				username = ?;";

$stmt = mysqli_prepare($connection, $selectUserDataqry);
mysqli_stmt_bind_param($stmt,'s', $username);
mysqli_stmt_execute($stmt);
$resultUserData = mysqli_stmt_get_result($stmt);

$rowUserData = mysqli_fetch_assoc($resultUserData);
?>
		
		<!-- User's data-->
		
		<div style="width: 30%; padding: 0px 50px 50px 50px;">
			<table border=0 style="width: 100%; ">
				<tr>
					<td align="center" colspan=2>
						<img src="images/user.jpg" style="height: 100px; width: 100px; border-radius: 50px;">
					</td>
				</tr>
				<tr>
					<td class="user-cell" align="center" colspan=2>
						<h1><?php echo $lengui['welcome'][$current]; ?> - <?php echo htmlentities($username, ENT_QUOTES, 'UTF-8'); ?></h1>
					</td>
				</tr>
<?php
if(!empty($rowUserData['fullname']))
{
?>
				<tr>
					<td class="user-cell"style="width: 50%"><b><?php echo $lengui['name'][$current]; ?></b></td>
					<td class="user-cell"><?php echo htmlentities($rowUserData['fullname'], ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
<?php
}
?>
				<tr>
					<td class="user-cell"><b><?php echo $lengui['type'][$current]; ?></b></td>
					<td class="user-cell"><?php echo $lengui['administrator'][$current]; ?></td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell"><b><?php echo $lengui['created'][$current]; ?></b></td>
					<td class="user-cell">
						<?php echo htmlentities($rowUserData['created'], ENT_QUOTES, 'UTF-8'); ?>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell" rowspan=9><b><?php echo $lengui['initiate'][$current]; ?></b></td>
					<td class="user-cell">
						<a href="useraccount.php"><span class="glyphicon glyphicon-home"></span> <?php echo $lengui['home'][$current]; ?></a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a href="userpasswd.php"><span class="glyphicon glyphicon-lock"></span> <?php echo $lengui['changepass'][$current]; ?></a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a class="active" href="terms.php"><span class="glyphicon glyphicon"></span> <?php echo $lengui['terms'][$current]; ?></a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>
				<tr>
					<td class="user-cell">
						<a href="userwipe.php"><span class="glyphicon glyphicon-erase"></span> <?php echo $lengui['removeacc'][$current]; ?></a>
					</td>
				</tr>
				<tr>
					<td colspan=2><hr></td>
				</tr>			
			</table>
		</div>
		
		
		<!-- Terms -->
		
		<div style="width: 55%; margin-left: 100px;">
			<table class="view-table" border=0>
				<tr class="view-row">
					<th class="view-cell">
						No.
					</th>	
					<th class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<?php echo $lengui['table'][$current]; ?>
						</font>
					</th>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						1.
					</td>									
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<?php echo $lengui['one'][$current]; ?>
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						2.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<?php echo $lengui['two'][$current]; ?>
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						3.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<?php echo $lengui['three'][$current]; ?>
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						4.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							 <?php echo $lengui['four'][$current]; ?>
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						5.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							 <?php echo $lengui['five'][$current]; ?>
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						6.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<?php echo $lengui['six'][$current]; ?>
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						7.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							 <?php echo $lengui['seven'][$current]; ?>
						</font>
					</td>
				</tr>
				<tr class="view-row">
					<td class="view-cell">
						8.
					</td>
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							 <?php echo $lengui['eight'][$current]; ?>
						</font>
					</td>
				</tr>
			</table>			

			<form name="updateTermsForm" id="updateTermsForm" action="terms.php" method="post">
			<table class="view-table" border=0>
				<tr class="view-row">
					<th class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<?php echo $lengui['approval'][$current]; ?>
						</font>
					</th>
				</tr>
<?php
if(empty($rowUserData['termsofservice']))
{
?>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="oldpwUser" type="password" class="form-control" data-type="password" placeholder="<?php echo $lengui['password'][$current]; ?>*" autocomplete="off"><input type="hidden" name="control" value="<?php echo $COOKIE_SECURITY; ?>">
						</font>
					</td>
				</tr>

				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="termsofservice" id="approveTermsOfService" type="checkbox">
							<label for="approveTermsOfService"><?php echo $lengui['accept'][$current]; ?></label>
						</font>
					</td>
				</tr>
<?php
}
else
{
?>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<?php echo $lengui['accepted'][$current]; ?>
						</font>
					</td>
				</tr>
<?php
}
if(empty($rowUserData['termsofservice']))
{
?>
				<tr class="view-row">
					<td class="view-cell">
						<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
							<input name="btnUpdateTerms" type="submit" class="button" value="<?php echo $lengui['button'][$current]; ?>" style="color:white; font-weight:bold; background-color: #173457;" id="btnUpdateTerms" onclick="JavaScript:return validateUpdateTermsForm();" />
						</font>
					</td>
				</tr>
<?php
}
?>
			</table>
			</form>
		</div>
	</div>
	<!-- Language chooser -->
		
	<div class="langs">
	<?php foreach ($supported as $code => $label): 
		$url = '/src/language/language.php?lang=' . rawurlencode($code) . '&return=' . rawurlencode($return);
		$cls = ($code === $current) ? 'lang active' : 'lang';
	?>
		<a class="<?php echo $cls; ?>" href="<?php echo $url; ?>"><?php echo htmlspecialchars($label); ?></a>
	<?php endforeach; ?>
	</div>
	<script type="text/javascript">
	function validateUpdateTermsForm() {
		const userResponse = confirm(<?php echo "'".$lengui['question'][$current]."'"; ?>);
		
	    if (userResponse) {
			document.getElementById('updateTermsForm').name='btnUpdateTerms';
			document.getElementById('updateTermsForm').action='terms.php';
			document.getElementById('updateTermsForm').submit();
	    }
	    else {
			document.getElementById('updateTermsForm').addEventListener('submit',
			function(event) {
				event.preventDefault();
			});
		}
	}
	</script>	
</body>  
</html>
