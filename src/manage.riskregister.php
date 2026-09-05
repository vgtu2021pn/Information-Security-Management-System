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

$return = '/src/manage.riskregister.php';

// Simple language chooser
$lengui = array(
    'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
    'title' => array ('en' => 'Manegement of risk register - Information Security Management System', 'lt' => 'Rizikų registro vadyba - Informacijos saugos valdymo sistema', 'pl' => 'Kierowanie o ryzyk register - System zarządzania bezpieczeństwem informacji'),
    'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
    'nav_title' => array ('en' => 'Management of ISMS risk register', 'lt' => 'ISVS rizikų registro vadyba', 'pl' => 'Kierowanie o SZBI ryzyk register'),
    'nav_title_new' => array ('en' => 'Add new risk', 'lt' => 'Pridėti naują riziką', 'pl' => 'Dodawanie nowe ryzyko'),
    'nav_title_dis' => array ('en' => 'Discard this risk', 'lt' => 'Pašalinti esamą riziką', 'pl' => 'Usuwanie ten ryzyko'),
    'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
    'cookie' => array ('en' => 'Wrong HTTP Cookie', 'lt' => 'Neteisingas HTTP Cookie', 'pl' => 'Blędne HTTP Cookie'),
    'action-add' => array ('en' => 'Add records to the risk register', 'lt' => 'Įkelti naujus įrašus į rizikų registrą', 'pl' => 'Wprowadzenie nowych wpisów do ryzyk register'),
    'action-update' => array ('en' => 'Edit records of the risk register', 'lt' => 'Keisti rizikų registro įrašus', 'pl' => 'Zmiana wpisów w ryzyk register'),
    'button-add' => array ('en' => 'Add new records', 'lt' => 'Įkelti naujus įrašus', 'pl' => 'Wprowadzenie nowych wpisów'),
    'button-update' => array ('en' => 'Edit records', 'lt' => 'Keisti įrašus', 'pl' => 'Zmiana wpisów'),
    'impact-likelihood' => array ('en' => '(0-5)', 'lt' => '(0-5)', 'pl' => '(0-5)'),
    'usual' => array ('en' => 'Usual', 'lt' => 'Įprastas', 'pl' => 'Normalny'),
    'less-important' => array ('en' => 'Less important', 'lt' => 'Mažiau reikšmingas', 'pl' => 'Mniej oznaczający'),
    'risk_type' => array ('en' => 'Type of a risk', 'lt' => 'Rizikos rūšis', 'pl' => 'Rodzaj ryzyki'),
    'risk_type1' => array ('en' => 'asset\'s worth', 'lt' => 'vertybė', 'pl' => 'wartość'),
    'risk_type2' => array ('en' => 'process', 'lt' => 'procesas', 'pl' => 'proces'),
    'risk_type3' => array ('en' => 'system', 'lt' => 'sistema', 'pl' => 'system'),
    'risk_name' => array ('en' => 'Name of a risk', 'lt' => 'Rizikos pavadinimas', 'pl' => 'Nazwa ryzyki'),
    'threat' => array ('en' => 'Explanation of a threat', 'lt' => 'Grėsmės paaiškinimas', 'pl' => 'Wyjaśnienie groźby'),
    'vulnerability' => array ('en' => 'Explanation of a vulnerability', 'lt' => 'Pažeidžiamumo paaiškinimas', 'pl' => 'Wyjaśnienie wrażliwośći'),
    'impact' => array ('en' => 'Evaluation of an plausible impact', 'lt' => 'Galimo poveikio įvertinimas', 'pl' => 'Ocena o możliwość prawdopodobnego wpływu'),
    'likelihood' => array ('en' => 'Evaluation of a plausible likelihood', 'lt' => 'Galimos tikimybės įvertinimas', 'pl' => 'Ocena o możliwość prawdopodobieństwa'),
    'operation' => array ('en' => 'Evaluation type', 'lt' => 'Įvertinimo tipas', 'pl' => 'Typ oceny'),
    'owner' => array ('en' => 'Owner of a risk', 'lt' => 'Rizikos savininkas', 'pl' => 'Właściciel ryzyki'),
    'decision' => array ('en' => 'Decision of a treatment', 'lt' => 'Tvarkymo sprendimas', 'pl' => 'Decyzja o leczeniu'),
    'decision0' => array ('en' => 'Select', 'lt' => 'Pasirinkti', 'pl' => 'Wybieranie'),
    'decision1' => array ('en' => 'fix', 'lt' => 'taisyti', 'pl' => 'naprawić'),
    'decision2' => array ('en' => 'accept', 'lt' => 'priimti', 'pl' => 'akcept'),
    'decision3' => array ('en' => 'avoid', 'lt' => 'išvengti', 'pl' => 'unikać'),
    'decision4' => array ('en' => 'transfer', 'lt' => 'perkelti', 'pl' => 'transfer'),
    'path' => array ('en' => 'Path of a treatment', 'lt' => 'Tvarkymo kelias', 'pl' => 'Droga leczenia'),
    'err1' => array ('en' => 'Wrong unique number of a risk register', 'lt' => 'Neteisingas rizikos registro unikalus numeris', 'pl' => 'Nieprawidłowy unikat numer ryzyk registeru'),
    'serr1' => array ('en' => 'Wrong risk name.', 'lt' => 'Neteisingas rizikos pavadinimas.', 'pl' => 'Nieprawilna nazwa ryzyki.'),
    'serr2' => array ('en' => 'Wrong impact value.', 'lt' => 'Neteisingas poveikio įvertinimas.', 'pl' => 'Nieprawidłowa ocena o wpływu.'),
    'serr3' => array ('en' => 'Wrong likelihood value.', 'lt' => 'Neteisingas tikimybės įvertinimas.', 'pl' => 'Nieprawidłowa ocena o prawdopodobieństwa.'),
    'serr4' => array ('en' => 'Wrong risk owner name.', 'lt' => 'Neteisingas rizikos savininko vardas.', 'pl' => 'Nieprawilny właściciel ryzyki.'),
    'serr5' => array ('en' => 'Wrong path of a treatment.', 'lt' => 'Neteisingas tvarkymo kelias.', 'pl' => 'Nieprawidłowa droga leczenia.'),
    'serr6' => array ('en' => 'Incorrect risk type.', 'lt' => 'Blogas rizikos tipas.', 'pl' => 'Błędne rodzaj ryzyki.'),
    'serr7' => array ('en' => 'Incorrect evaluation type.', 'lt' => 'Blogas įvertinimo tipas.', 'pl' => 'Błędne typ oceny.'),
    'serr8' => array ('en' => 'Incorrect treatment decision.', 'lt' => 'Blogas tvarkymo kelias.', 'pl' => 'Błędna decyzja leczenia.')
);

include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);			

if(!$connection) {
	die("Connection failed: " .mysqli_connect_error());
}

if(isset($_GET['discard'])) {
	$un = (int)$_GET['discard'];
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
	    	$removeReq = "DELETE FROM risk_register WHERE risk_un=?;";
	    	
	    	$removeprepare = mysqli_prepare($connection, $removeReq);
	    	mysqli_stmt_bind_param($removeprepare, 'i', $un);
	    	mysqli_stmt_execute($removeprepare);
	    }
	}
	mysqli_free_result($result2);
	header("Location: riskregister.php?remove_notice=1");
}

if(isset($_POST['btnAdd'])) {
	$risk_type = (int)$_POST["risk_type"];
	$risk_name = $_POST["risk_name"];
	$threat_exp = $_POST["threat_exp"];
	$vulnerability_exp = $_POST["vulnerability_exp"];
	$impact_v = (int)$_POST["impact_v"];
	$likelihood_v = (int)$_POST["likelihood_v"];
	$risk_level_p = $_POST["risk_level_p"];
	$risk_owner_name = $_POST["risk_owner_name"];
	$treatment_decision = (int)$_POST["treatment_decision"];
	$treatment_plan = $_POST["treatment_plan"];
	$control = $_POST['manage_sec'];
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
	    exit;
        }
	
	$insert = "INSERT INTO risk_register (risk_type,risk_name,threat_exp,vulnerability_exp,impact_v,likelihood_v,risk_level_p,risk_owner_name,treatment_decision,treatment_plan) VALUES (?,?,?,?,?,?,?,?,?,?);";
	
	$insertprepare = mysqli_prepare($connection, $insert);
	mysqli_stmt_bind_param($insertprepare,'isssiissis', $risk_type, $risk_name, $threat_exp, $vulnerability_exp, $impact_v, $likelihood_v, $risk_level_p, $risk_owner_name, $treatment_decision, $treatment_plan);
	mysqli_stmt_execute($insertprepare);
	header("Location: riskregister.php?create_notice=1");
}

if(isset($_POST['btnManage'])) {
	$un = $_POST["isms_un"];
	$risk_type = (int)$_POST["risk_type"];
	$risk_name = $_POST["risk_name"];
	$threat_exp = $_POST["threat_exp"];
	$vulnerability_exp = $_POST["vulnerability_exp"];
	$impact_v = (int)$_POST["impact_v"];
	$likelihood_v = (int)$_POST["likelihood_v"];
	$risk_level_p = $_POST["risk_level_p"];
	$risk_owner_name = $_POST["risk_owner_name"];
	$treatment_decision = (int)$_POST["treatment_decision"];
	$treatment_plan = $_POST["treatment_plan"];
	$control = $_POST['manage_sec'];
        
	if($control != $COOKIE_SECURITY) {
	    echo '<script> alert("'.$lengui['cookie'][$current].'") </script>';
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
			$update = "UPDATE 
						risk_register 
					SET 
						risk_type = ?,
						risk_name = ?,
						threat_exp = ?,
						vulnerability_exp = ?,
						impact_v = ?,
						likelihood_v = ?,
						risk_level_p = ?,
						risk_owner_name = ?,
						treatment_decision = ?,
						treatment_plan = ?
					WHERE
						risk_un = ?;";
			
			$updateprepare = mysqli_prepare($connection, $update);
			mysqli_stmt_bind_param($updateprepare,'isssiissisi', $risk_type, $risk_name, $threat_exp, $vulnerability_exp, $impact_v, $likelihood_v, $risk_level_p, $risk_owner_name, $treatment_decision, $treatment_plan, $un);
			mysqli_stmt_execute($updateprepare);
		}
	}
	mysqli_free_result($result2);
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
.back {
	margin: 2px 20px;
}
</style>
</head>
<body style="background: #173457;">

<?php
$purpose = 'view';
$isms_un = (int)$_POST['isms_un'];
$control = $_POST['manage_sec'];
	
$selectqry = "SELECT * FROM risk_register WHERE risk_un = ?;";

$stmt = mysqli_prepare($connection, $selectqry);
mysqli_stmt_bind_param($stmt,'i', $isms_un);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0 && $control == $COOKIE_SECURITY) {
    $purpose = 'manage';
    $row = mysqli_fetch_assoc($result);
    
    $isms_un = $row["risk_un"];
    $isms_risk_type = $row["risk_type"];
    $isms_risk_name = $row["risk_name"];
    $isms_threat_exp = $row["threat_exp"];
    $isms_vulnerability_exp = $row["vulnerability_exp"];
    $isms_impact_v = $row["impact_v"];
    $isms_likelihood_v = $row["likelihood_v"];
    $isms_risk_level_p = $row["risk_level_p"];
    $isms_risk_owner_name = $row["risk_owner_name"];
    $isms_treatment_decision = $row["treatment_decision"];
    $isms_treatment_plan = $row["treatment_plan"];
} else {
    $isms_un = '';
    $isms_risk_type = 0;
    $isms_risk_name = '';
    $isms_threat_exp = '';
    $isms_vulnerability_exp = '';
    $isms_impact_v = 0;
    $isms_likelihood_v = 0;
    $isms_risk_level_p = '+';
    $isms_risk_owner_name = '';
    $isms_treatment_decision = 0;
    $isms_treatment_plan = '';
}
?>
	<!-- Navigation Bar -->
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#"><?php echo $lengui['nav_page'][$current]; ?></a>
			</div>
    
			<ul class="nav navbar-nav">
				<li class="active"><a href="#"><?php echo $lengui['nav_title'][$current]; ?></a></li>
<?php if(empty($isms_un)) { ?>
				<li class="active"><a href="manage.riskregister.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
<?php } else { ?>
				<li><a href="manage.riskregister.php"><?php echo $lengui['nav_title_new'][$current]; ?></a></li>
				<li><a href="manage.riskregister.php?discard=<?php echo $isms_un; ?>".><?php echo $lengui['nav_title_dis'][$current]; ?></a></li>
<?php } ?>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="useraccount.php"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["username"]; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> <?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>

	<!-- Management Form -->
	<form name="manageForm" method="POST" action="manage.riskregister.php" enctype="multipart/form-data">
		<input type="hidden" name="isms_un" value="<?php echo $isms_un; ?>">
		<input type="hidden" name="manage_sec" value="<?php echo $COOKIE_SECURITY; ?>">	
		<center>
		<div>
			<table width="100%" >
				<tr>
					<td style="padding: 5px;">
						<div class="col-md-4 mb-3" style="text-align: center;">
							<h1 style="color: white;"><?php if($purpose == 'manage') { echo $lengui['action-update'][$current]; } else { echo $lengui['action-add'][$current]; } ?></h1>
						</div>
					</td>
				</tr>
		
				<tr>
					<td style="padding: 5px;">	<!--risk_type-->
						<div class="col-md-4 mb-3">
							<label for="validation01" ><?php echo $lengui['risk_type'][$current]; ?></label>
							<select name="risk_type" class="form-control" id="validation01">
								<option value="0" <?php if(empty($isms_risk_type)){ ?>selected="selected"<?php } ?>>--<?php echo $lengui['risk_type'][$current]; ?>--</option>
								<option value="1" <?php if($isms_risk_type == 1){ ?>selected="selected"<?php } ?>><?php echo $lengui['risk_type1'][$current]; ?></option>
								<option value="2" <?php if($isms_risk_type == 2){ ?>selected="selected"<?php } ?>><?php echo $lengui['risk_type2'][$current]; ?></option>
								<option value="3" <?php if($isms_risk_type == 3){ ?>selected="selected"<?php } ?>><?php echo $lengui['risk_type3'][$current]; ?></option>
							</select>
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">	<!--risk_name-->
						<div class="col-md-4 mb-3">
							<label for="validation02"><?php echo $lengui['risk_name'][$current]; ?></label>
							<input type="text" name="risk_name" class="form-control" id="validation02" placeholder="" value="<?php echo htmlentities($isms_risk_name, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>		
				
				<tr>
					<td style="padding: 5px;">	<!--threat_exp-->
						<div class="col-md-4 mb-3">
							<label for="validation03"><?php echo $lengui['threat'][$current]; ?></label>
							<textarea name="threat_exp" class="form-control" id="validation03" placeholder="" rows="5" required><?php echo htmlentities($isms_threat_exp, ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--vulnerability_exp-->
						<div class="col-md-4 mb-3">
							<label for="validation04"><?php echo $lengui['vulnerability'][$current]; ?></label>
							<textarea name="vulnerability_exp" class="form-control" id="validation04" placeholder="" rows="5" required><?php echo htmlentities($isms_vulnerability_exp, ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">	<!--impact-->
						<div class="col-md-4 mb-3">
							<label for="validation05"><?php echo $lengui['impact'][$current]; ?></label>
							<input type="text" name="impact_v" class="form-control" id="validation05" placeholder="<?php echo $lengui['impact-likelihood'][$current]; ?>" value="<?php echo htmlentities($isms_impact_v, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>
				
				<tr>
					<td style="padding: 5px;">	<!--likelihood-->
						<div class="col-md-4 mb-3">
							<label for="validation06"><?php echo $lengui['likelihood'][$current]; ?></label>
							<input type="text" name="likelihood_v" class="form-control" id="validation06" placeholder="<?php echo $lengui['impact-likelihood'][$current]; ?>" value="<?php echo htmlentities($isms_likelihood_v, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>
							
				<tr>
					<td style="padding: 5px;">	<!--operation-->
						<div class="col-md-4 mb-3">
							<label for="validation07" ><?php echo $lengui['operation'][$current]; ?></label>
							<select name="risk_level_p" class="form-control" id="validation07">
								<option value="*" <?php if($isms_risk_level_p == '*'){ ?>selected="selected"<?php } ?>><?php echo $lengui['usual'][$current]; ?></option>
								<option value="+" <?php if(empty($isms_risk_level_p) || $isms_risk_level_p == '+'){ ?>selected="selected"<?php } ?>><?php echo $lengui['less-important'][$current]; ?></option>
							</select>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--owner_name-->
						<div class="col-md-4 mb-3">
							<label for="validation08"><?php echo $lengui['owner'][$current]; ?></label>
							<input type="text" name="risk_owner_name" class="form-control" id="validation08" placeholder="" value="<?php echo htmlentities($isms_risk_owner_name, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--treatment_decision-->
						<div class="col-md-4 mb-3">
							<label for="validation09" ><?php echo $lengui['decision'][$current]; ?></label>
							<select name="treatment_decision" class="form-control" id="validation09">
								<option value="0" <?php if(empty($isms_treatment_decision)){ ?>selected="selected"<?php } ?>>--<?php echo $lengui['decision0'][$current]; ?>--</option>
								<option value="1" <?php if($isms_treatment_decision == 1){ ?>selected="selected"<?php } ?>><?php echo $lengui['decision1'][$current]; ?></option>
								<option value="2" <?php if($isms_treatment_decision == 2){ ?>selected="selected"<?php } ?>><?php echo $lengui['decision2'][$current]; ?></option>
								<option value="3" <?php if($isms_treatment_decision == 3){ ?>selected="selected"<?php } ?>><?php echo $lengui['decision3'][$current]; ?></option>
								<option value="4" <?php if($isms_treatment_decision == 4){ ?>selected="selected"<?php } ?>><?php echo $lengui['decision4'][$current]; ?></option>
							</select>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding: 5px;">	<!--treatment_path-->
						<div class="col-md-4 mb-3">
							<label for="validation10"><?php echo $lengui['path'][$current]; ?></label>
							<input type="text" name="treatment_plan" class="form-control" id="validation10" placeholder="" value="<?php echo htmlentities($isms_treatment_plan, ENT_QUOTES, 'UTF-8'); ?>" required>
						</div>	
					</td>
				</tr>
				
				<tr>
					<td colspan=2 style="padding-left:250px;">	<!--submit button-->
						<div class="col-md-4 mb-3" style="padding-top: 5px;">
							<button name="<?php if($purpose == 'manage') { echo 'btnManage'; } else { echo 'btnAdd'; } ?>" class="btn btn-primary" type="submit" style="color: white; font-size: 15px; align:center;"  onclick="JavaScript:return validateManageForm();"><?php if($purpose == 'manage') { echo $lengui['button-update'][$current]; } else { echo $lengui['button-add'][$current]; } ?></button>
						</div>
					</td>
				</tr>
			</table>
		</div>
		</center>
		<p class="back"><a href="/src/riskregister.php" target="_SELF"><i class="bi bi-arrow-return-left"></i></a></p>
	</form>

<!-- Language links -->

<div class="langs">
    <?php foreach ($supported as $code => $label): 
        $url = '/src/language/language.php?lang=' . rawurlencode($code) . '&return=' . rawurlencode($return);
        $cls = ($code === $current) ? 'lang active' : 'lang';
    ?>
        <a class="<?php echo $cls; ?>" href="<?php echo $url; ?>"><?php echo htmlspecialchars($label); ?></a>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
function validateManageForm() {
	var succeed = true;
	//validation
	
	var rsk_name = document.querySelector( "input[name='risk_name']" );
	let rsk_name_v = rsk_name.value;
	var imp_valu = document.querySelector( "input[name='impact_v']" );
	let imp_valu_v = imp_valu.value;
	var lik_valu = document.querySelector( "input[name='likelihood_v']" );
	let lik_valu_v = lik_valu.value;
	var rsk_ownr = document.querySelector( "input[name='risk_owner_name']" );
	let rsk_ownr_v = rsk_ownr.value;
	var trm_plan = document.querySelector( "input[name='treatment_plan']" );
	let trm_plan_v = trm_plan.value;
	
	var rsk_typ_v = $( "#validation01" ).val();
	var rsk_lvl_v = $( "#validation07" ).val();
	var trm_dec_v = $( "#validation09" ).val();
	
	if (rsk_name_v.length < 1 || rsk_name_v.length > 175){
		<?php echo "alert('".$lengui['serr1'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (imp_valu_v != 0 && imp_valu_v != 1 && imp_valu_v != 2 && imp_valu_v != 3 && imp_valu_v != 4 && imp_valu_v != 5){
		<?php echo "alert('".$lengui['serr2'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (lik_valu_v != 0 && lik_valu_v != 1 && lik_valu_v != 2 && lik_valu_v != 3 && lik_valu_v != 4 && lik_valu_v != 5){
		<?php echo "alert('".$lengui['serr3'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (rsk_ownr_v.length < 1 || rsk_ownr_v.length > 150){
		<?php echo "alert('".$lengui['serr4'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (trm_plan_v.length < 1 || trm_plan_v.length > 254){
		<?php echo "alert('".$lengui['serr5'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (rsk_typ_v != 1 && rsk_typ_v != 2 && rsk_typ_v != 3){
		<?php echo "alert('".$lengui['serr6'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (rsk_lvl_v != '+' || rsk_lvl_v != '*'){
		<?php echo "alert('".$lengui['serr7'][$current]."');"; ?>
		succeed = false;
	}// end if
	if (trm_dec_v != 1 && trm_dec_v != 2 && trm_dec_v != 3 && trm_dec_v != 4){
		<?php echo "alert('".$lengui['serr8'][$current]."');"; ?>
		succeed = false;
	}// end if
	    
	if(succeed == true){
		document.getElementById('manageForm').name=<?php if($purpose == 'manage') { echo "'".'btnManage'."'"; } else { echo "'".'btnAdd'."'"; } ?>;
		document.getElementById('manageForm').action='manage.riskregister.php';
		document.getElementById('manageForm').submit();
		return(true);
	}else {
		return(false);
	}
}
</script>
</body>
</html>
