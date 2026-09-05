<?php
session_start();
if(!isset($_SESSION["username"]) && !isset($_SESSION["usertype"]) && $_SESSION["usertype"] != 1) {
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

$return = '/src/controls.riskregister.php';

// Simple language chooser
$lengui = array(
    'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
    'title' => array ('en' => 'Risk register - Information Security Management System', 'lt' => 'Rizikų registras - Informacijos saugos valdymo sistema', 'pl' => 'Ryzyk register - System zarządzania bezpieczeństwem informacji'),
    'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
    'nav_title' => array ('en' => 'Public view of Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistemos viešas vaizdas', 'pl' => 'Publiczny widok Systemu Zarządzania Bezpieczeństwem Informacji'),
    'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
    'cookie' => array ('en' => 'Wrong HTTP Cookie', 'lt' => 'Neteisingas HTTP Cookie', 'pl' => 'Blędne HTTP Cookie'),
    'action-manage' => array ('en' => 'Manage records of riskregister & control measures', 'lt' => 'Valdyti rizikų registro ir kontrolės priemonių įrašus', 'pl' => 'Wprowadzenie wpisów do ryzyk register i kontrol'),
    'button-manage' => array ('en' => 'Manage records', 'lt' => 'Valdyti įrašus', 'pl' => 'Kierować wpisów'),
    'risk_type' => array ('en' => 'Risk type:', 'lt' => 'Rizikos rūšis:', 'pl' => 'Rodzaj ryzyki:'),
    'risk_type1' => array ('en' => 'asset\'s worth', 'lt' => 'vertybė', 'pl' => 'wartość'),
    'risk_type2' => array ('en' => 'process', 'lt' => 'procesas', 'pl' => 'proces'),
    'risk_type3' => array ('en' => 'system', 'lt' => 'sistema', 'pl' => 'system'),
    'risk_name' => array ('en' => 'Risk name:', 'lt' => 'Rizikos pavadinimas:', 'pl' => 'Nazwa ryzyki:'),
    'threat' => array ('en' => 'Explanation of threat:', 'lt' => 'Grėsmės paaiškinimas:', 'pl' => 'Wyjaśnienie groźby:'),
    'vulnerability' => array ('en' => 'Explanation of vulnerability:', 'lt' => 'Pažeidžiamumo paaiškinimas:', 'pl' => 'Wyjaśnienie wrażliwośći:'),
    'impact' => array ('en' => 'Value of plausible impact:', 'lt' => 'Galimo poveikio reikšmė:', 'pl' => 'Wartość prawdopodobnego wpływu:'),
    'likelihood' => array ('en' => 'Value of plausible likelihood:', 'lt' => 'Galimos tikimybės reikšmė:', 'pl' => 'Wartość prawdopodobieństwa:'),
    'total_risk_level' => array ('en' => 'Total level of risk', 'lt' => 'Rizikos lygis iš viso', 'pl' => 'Level ryzyku w totale'),
    'owner' => array ('en' => 'Risk owner', 'lt' => 'Rizikos savininkas', 'pl' => 'Właściciel ryzyki'),
    'decision' => array ('en' => 'Decision of treatment', 'lt' => 'Tvarkymo sprendimas', 'pl' => 'Decyzja o leczeniu'),
    'decision1' => array ('en' => 'fix', 'lt' => 'taisyti', 'pl' => 'naprawić'),
    'decision2' => array ('en' => 'accept', 'lt' => 'priimti', 'pl' => 'akcept'),
    'decision3' => array ('en' => 'avoid', 'lt' => 'išvengti', 'pl' => 'unikać'),
    'decision4' => array ('en' => 'transfer', 'lt' => 'perkelti', 'pl' => 'transfer'),
    'path' => array ('en' => 'Path of a treatment', 'lt' => 'Tvarkymo kelias', 'pl' => 'Droga leczenia'),
    'risk_change' => array ('en' => 'Change of risk', 'lt' => 'Rizikos pokytis', 'pl' => 'Zmiana ryzyku'),
    'risk_change_placeholder' => array ('en' => '(e.g.: -1)', 'lt' => '(pvz.: -1)', 'pl' => '(np.: -1)'),
    'control_un' => array ('en' => 'Control\'s No.', 'lt' => 'Kontrolės Nr.', 'pl' => 'Kontrol Nr.'),
    'control_name' => array ('en' => 'Control\'s name', 'lt' => 'Kontrolės pavadinimas', 'pl' => 'Nazwisko kontroli'),
    'control_description' => array ('en' => 'Control\'s description', 'lt' => 'Kontrolės aprašas', 'pl' => 'Opis kontroli'),
    'applicability_status' => array ('en' => 'Applicability status', 'lt' => 'Pritaikomumo būsena', 'pl' => 'Stosowania status'),
    'applicability_status0' => array ('en' => 'Not applicable', 'lt' => 'Nepritaikoma', 'pl' => 'Nie stosowane'),
    'applicability_status1' => array ('en' => 'Applicable', 'lt' => 'Pritaikoma', 'pl' => 'Stosowane'),
    'justification_text' => array ('en' => 'Justification', 'lt' => 'Pagrindimas', 'pl' => 'Usprawiedliwienie'),
    'implementation_status' => array ('en' => 'Implementation status', 'lt' => 'Įgyvendinimo būsena', 'pl' => 'Położenie zastosowania'),
    'implementation_status1' => array ('en' => 'Planned', 'lt' => 'Planuojama', 'pl' => 'Planowany'),
    'implementation_status2' => array ('en' => 'Implemented', 'lt' => 'Įgyvendinta', 'pl' => 'Zastosowany'),
    'implementation_status3' => array ('en' => 'Partial implementation', 'lt' => 'Ne visiškai įgyvendinta', 'pl' => 'Częściowy zastosowanie'),
    'implementation_status4' => array ('en' => 'Not implemented', 'lt' => 'Neįgyvendinta', 'pl' => 'Nie zastosowany'),
    'control_owner_name' => array ('en' => 'Control\'s owner', 'lt' => 'Kontrolės savininkas', 'pl' => 'Właściciel kontroli'),
    'err' => array ('en' => 'Incorrect submission', 'lt' => 'Neteisinga pateiktis', 'pl' => 'Poddanie się jest blędna'),
    'err1' => array ('en' => 'Wrong unique number', 'lt' => 'Neteisingas unikalus numeris', 'pl' => 'Nieprawidłowy unikat numer'),
    'serr1' => array ('en' => 'Wrong version of document.', 'lt' => 'Neteisinga dokumento versija.', 'pl' => 'Nieprawilna wersja dokumentu.')
);

include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);			

if(!$connection) {
	die("Connection failed: " .mysqli_connect_error());
}

if(isset($_POST['btnManage']) && isset($_POST["control_uns"]) && isset($_POST["risk_change"]) && is_array($_POST["control_uns"]) && is_array($_POST["risk_change"])) {
	$un = (int)$_POST["isms_un"];
	$control_uns = $_POST["control_uns"];
	$risk_change = $_POST["risk_change"];
	$control = $_POST['manage_sec'];
	
	$cnt_c = count($control_uns);
	$cnt_r = count($risk_change);
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
        if($cnt_c != $cnt_r) {
	    echo '<script> alert("'.$lengui['err'][$current].'") </script>';
	    exit;
        }
	
	$checkqry = "SELECT
				IF(EXISTS(SELECT 1 FROM risk_register WHERE risk_un = ? LIMIT 1), 1, 0) AS one;";
				
	$chstmt = mysqli_prepare($connection, $checkqry);
	mysqli_stmt_bind_param($chstmt,'i', $un);
	mysqli_stmt_execute($chstmt);
	$result2 = mysqli_stmt_get_result($chstmt);
	
	if(mysqli_num_rows($result2) > 0) {
	    $rowCheckData = mysqli_fetch_assoc($result2);
		
	    if($rowCheckData['one']== 0) {
		echo '<script> alert("'.$lengui['err1'][$current].'") </script>';
	    } else {
			$removeReq = "DELETE FROM implemented_controls_a WHERE risk_register_un=?;";
	    	
			$removeprepare = mysqli_prepare($connection, $removeReq);
			mysqli_stmt_bind_param($removeprepare, 'i', $un);
			mysqli_stmt_execute($removeprepare);
			
			foreach($control_uns as $key => $value) {
			if(!empty($risk_change[$key])) {
			    $insert = "INSERT INTO implemented_controls_a (risk_register_un,statement_of_applicability_un,risk_change) VALUES (?,?,?);";
	
			    $insertprepare = mysqli_prepare($connection, $insert);
			    mysqli_stmt_bind_param($insertprepare,'iii', $un, $control_uns[$key], $risk_change[$key]);
			    mysqli_stmt_execute($insertprepare);
			}
			}
		}
	}
	mysqli_free_result($result2);
}
 
$isms_un = (int)$_POST['isms_un'];
$control = $_POST['manage_sec'];
	
$selectqry = "SELECT *, IF(risk_level_p='*',impact_v * likelihood_v,IF(risk_level_p='+',impact_v + likelihood_v,impact_v + likelihood_v)) AS risk_level, (SELECT SUM(risk_change) FROM implemented_controls_a WHERE risk_register_un = risk_register.risk_un) AS risk_change FROM risk_register WHERE risk_un = ?;";

$stmt = mysqli_prepare($connection, $selectqry);
mysqli_stmt_bind_param($stmt,'i', $isms_un);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0 && $control == $COOKIE_SECURITY) {
    $row = mysqli_fetch_assoc($result);
    
    $isms_un = $row["risk_un"];
    $isms_risk_name = $row["risk_name"];
    $isms_impact_v = (int)$row["impact_v"];
    $isms_likelihood_v = (int)$row["likelihood_v"];
    $isms_total_risk_level = $row["risk_level"] + $row["risk_change"];
    
    mysqli_free_result($result);
} else {
    header("Location: riskregister.php");
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lengui['langu'][$current]; ?>">
<head> 
<meta charset="utf-8">
<title><?php echo $lengui['title'][$current]; ?></title>
<link rel="icon" href="images/logo.webp" type="image/webp">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style>
label{
	font-size: 15px;
	color: white;
}

.form-control{
	font-size: 15px;
}
.head-div {
	display: flex;
	background-color: rgba(255, 255, 255, 0.5);
	border: 1px solid black;
	border-radius: 20px;
	margin-top: 10px;
	margin-left: 5px;
	margin-right: 5px;
}
		
.cell {
	width: 200px;
	text-align: justify;
	font-family: Helvetica, sans-serif;
	color: black;
	background-color: rgba(255, 255, 255, 0.5);
	padding: 10px;
}
.back {
	margin: 2px 20px;
}
</style>
</head>
<body style="background: #173457;">
	<!-- Navigation Bar -->
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#"><?php echo $lengui['nav_page'][$current]; ?></a>
			</div>
    
			<ul class="nav navbar-nav">
				<li class="active"><a href="#"><?php echo $lengui['nav_title'][$current]; ?></a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> <?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>

	<!-- Management Form -->
	<form name="manageForm" method="POST" action="controls.riskregister.php" enctype="multipart/form-data">
		<input type="hidden" name="isms_un" value="<?php echo $isms_un; ?>">
		<input type="hidden" name="manage_sec" value="<?php echo $COOKIE_SECURITY; ?>">	
		<center>
		<div>
			<table width="100%" >
				<tr>
					<td style="padding: 5px;">
						<div class="col-md-4 mb-3" style="text-align: center;">
							<h1 style="color: white;"><?php echo $lengui['action-manage'][$current]; ?></h1>
						</div>
					</td>
				</tr>

				<tr>
					<td class="view-cell">																						<!--total_risk_level-->
						<font style="margin-left: 20px; color: white; font-size: 15px; font-family: Helvetica, sans-serif;">
							<b><?php echo $lengui['total_risk_level'][$current]; ?></b> <?php echo htmlentities($isms_total_risk_level, ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>

				<tr>
					<td class="view-cell">																						<!--risk_name-->
						<font style="margin-left: 20px; color: white; font-size: 15px; font-family: Helvetica, sans-serif;">
							<b><?php echo $lengui['risk_name'][$current]; ?></b> <?php echo htmlentities($isms_risk_name, ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>

				<tr>
					<td class="view-cell">																						<!--risk_name-->
						<font style="margin-left: 20px; color: white; font-size: 15px; font-family: Helvetica, sans-serif;">
							<b><?php echo $lengui['impact'][$current]; ?></b> <?php echo htmlentities($isms_impact_v, ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>

				<tr>
					<td class="view-cell">																						<!--risk_name-->
						<font style="margin-left: 20px; color: white; font-size: 15px; font-family: Helvetica, sans-serif;">
							<b><?php echo $lengui['likelihood'][$current]; ?></b> <?php echo htmlentities($isms_likelihood_v, ENT_QUOTES, 'UTF-8'); ?>
						</font>
					</td>
				</tr>

<?php
            $selectqry2 = "	SELECT 
					soa.control_un,
					soa.control_name,
					soa.implementation_status,
					soa.control_owner_name,
					(SELECT risk_change FROM implemented_controls_a WHERE soa.control_un = statement_of_applicability_un AND risk_register_un = {$isms_un}) AS `risk_change`
				FROM 
					 statement_of_applicability AS soa
				ORDER BY 
					soa.implementation_status DESC,
					soa.applicability_status DESC;";
						
            $result2 = mysqli_query($connection, $selectqry2);
						
            if(mysqli_num_rows($result2)>0) {
                while ($row2 = mysqli_fetch_array($result2)) {
                    $isms_control_un = $row2["control_un"];
                    $isms_control_name = $row2["control_name"];
                    $isms_implementation_status = $row2["implementation_status"];
                    $isms_control_owner_name = $row2["control_owner_name"];
                    $isms_risk_change = $row2["risk_change"];
?>

				<tr>
					<td style="padding: 5px;">	<!--risk_change-->

						<div style="margin-top: 2%; z-index: 0;"><div style="margin-bottom: 10px; margin-left: 10%;">
							<div class="head-div">
								<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b><?php echo $lengui['control_name'][$current]; ?></b></div>
								<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($isms_control_name, ENT_QUOTES, 'UTF-8'); ?></div>
							</div>

							<div class="head-div">
								<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b><?php echo $lengui['implementation_status'][$current]; ?></b></div>
								<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($lengui['implementation_status'.$isms_implementation_status][$current], ENT_QUOTES, 'UTF-8'); ?></div>
							</div>

							<div class="head-div">
								<div class="cell" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;"><b><?php echo $lengui['control_owner_name'][$current]; ?></b></div>
								<div class="cell" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;"><?php echo htmlentities($isms_control_owner_name, ENT_QUOTES, 'UTF-8'); ?></div>
							</div>
						</div></div>
						<div class="col-md-4 mb-3">
							<label for="validation01"><?php echo $lengui['risk_change'][$current]; ?></label>
							<input type="hidden" name="control_uns[]" value="<?php echo htmlentities($isms_control_un, ENT_QUOTES, 'UTF-8'); ?>">
							<input type="text" name="risk_change[]" class="form-control" id="validation01" placeholder="<?php echo $lengui['risk_change_placeholder'][$current]; ?>" value="<?php echo htmlentities($isms_risk_change, ENT_QUOTES, 'UTF-8'); ?>">
						</div>
					</td>
				</tr>
<?php
                }
            }
            mysqli_free_result($result2);
?>

				<tr>
					<td colspan=2 style="padding-left:250px;">	<!--submit button-->
						<div class="col-md-4 mb-3" style="padding-top: 5px;">
							<button name="<?php echo 'btnManage'; ?>" class="btn btn-primary" type="submit" style="color: white; font-size: 15px; align:center;"><?php echo $lengui['button-manage'][$current]; ?></button>
						</div>
					</td>
				</tr>
			</table>
		</div>
		</center>
		<p class="back"><a href="/src/riskregister.php" target="_SELF"><i class="bi bi-arrow-return-left"></i></a></p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
	</form>
</body>
</html>
