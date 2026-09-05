<?php
session_start();
if(!isset($_SESSION["username"])) {
	header("Location: Login.php?action=login");  
}

require_once __DIR__ . '/language/language.php';
    
$supported = [
    'en' => 'English',
    'lt' => 'Lietuvių',
    'pl' => 'Polska'
];

$current = defined('SITE_LANG') ? SITE_LANG : ($_COOKIE['site_lang'] ?? 'en');

$return = '/src/sagi.php';

// Simple language chooser
$lengui = array(
'langu' => array ('en' => 'en', 'lt' => 'lt', 'pl' => 'pl'),
'title' => array ('en' => 'Public view - Information Security Management System', 'lt' => 'Viešas vaizdas - Informacijos saugos valdymo sistema', 'pl' => 'Widok publiczny - System zarządzania bezpieczeństwem informacji'),
'nav_page' => array ('en' => 'Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistema', 'pl' => 'System zarządzania bezpieczeństwem informacji'),
'nav_title' => array ('en' => 'Public view of Information Security Management System', 'lt' => 'Informacijos saugos valdymo sistemos viešas vaizdas', 'pl' => 'Publiczny widok Systemu Zarządzania Bezpieczeństwem Informacji'),
'logout' => array ('en' => 'Log Out', 'lt' => 'Atsijungti', 'pl' => 'Wyloguj'),
'following_info' => array ('en' => 'Following information about risk. No.', 'lt' => 'Sekanti informacija apie riziką. Nr.', 'pl' => 'Następna informacja o ryzyku. Nr.'),
'risk_type' => array ('en' => 'Type of a risk:', 'lt' => 'Rizikos rūšis:', 'pl' => 'Rodzaj ryzyki:'),
'risk_type1' => array ('en' => 'asset\'s worth', 'lt' => 'vertybė', 'pl' => 'wartość'),
'risk_type2' => array ('en' => 'process', 'lt' => 'procesas', 'pl' => 'proces'),
'risk_type3' => array ('en' => 'system', 'lt' => 'sistema', 'pl' => 'system'),
'risk_name' => array ('en' => 'Name of a risk:', 'lt' => 'Rizikos pavadinimas:', 'pl' => 'Nazwa ryzyki:'),
'threat' => array ('en' => 'Explanation of a threat:', 'lt' => 'Grėsmės paaiškinimas:', 'pl' => 'Wyjaśnienie groźby:'),
'vulnerability' => array ('en' => 'Explanation of a vulnerability:', 'lt' => 'Pažeidžiamumo paaiškinimas:', 'pl' => 'Wyjaśnienie wrażliwośći:'),
'impact' => array ('en' => 'Value of an plausible impact:', 'lt' => 'Galimo poveikio reikšmė:', 'pl' => 'Wartość prawdopodobnego wpływu:'),
'likelihood' => array ('en' => 'Value of a plausible likelihood:', 'lt' => 'Galimos tikimybės reikšmė:', 'pl' => 'Wartość prawdopodobieństwa:'),
'owner' => array ('en' => 'Owner of a risk', 'lt' => 'Rizikos savininkas', 'pl' => 'Właściciel ryzyki'),
'decision' => array ('en' => 'Decision of a treatment', 'lt' => 'Tvarkymo sprendimas', 'pl' => 'Decyzja o leczeniu'),
'decision1' => array ('en' => 'fix', 'lt' => 'taisyti', 'pl' => 'naprawić'),
'decision2' => array ('en' => 'accept', 'lt' => 'priimti', 'pl' => 'akcept'),
'decision3' => array ('en' => 'avoid', 'lt' => 'išvengti', 'pl' => 'unikać'),
'decision4' => array ('en' => 'transfer', 'lt' => 'perkelti', 'pl' => 'transfer'),
'path' => array ('en' => 'Path of a treatment', 'lt' => 'Tvarkymo kelias', 'pl' => 'Droga leczenia')
);

include("credens.php");

$connection = mysqli_connect($servername, $user, $pw, $db);

if(!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lengui['langu'][$current]; ?>">
<head>
<meta charset="utf-8">
<title><?php echo $lengui['title'][$current]; ?></title>
<link rel="icon" href="images/logo.webp" type="image/webp">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<style>
body {
	font-family: Verdana, sans-serif;
	color: #FFFFFF;
	margin: 0;
}

* {
	box-sizing: border-box;
}

.row > .column {
	padding: 0 8px;
}

.row:after {
	content: "";
	display: table;
	clear: both;
}

.column {
	float: left;
	width: 25%;
}

/* The Modal (background) */
.modal {
	display: none;
	position: fixed;
	z-index: 1;
	padding-top: 100px;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	overflow: auto;
	background-color: black;
}

/* Modal Content */
.modal-content {
	position: relative;
	background-color: #fefefe;
	margin: auto;
	padding: 0;
	width: 90%;
	max-width: 1200px;
}

/* The Close Button */
.close {
	color: white;
	position: absolute;
	top: 10px;
	right: 25px;
	font-size: 35px;
	font-weight: bold;
}

.close:hover,
.close:focus {
	color: #999;
	text-decoration: none;
	cursor: pointer;
}

.mySlides {
	display: none;
}

.cursor {
	cursor: pointer;
}

/* Next & previous buttons */
.prev,
.next {
	cursor: pointer;
	position: absolute;
	top: 50%;
	width: auto;
	padding: 16px;
	margin-top: -50px;
	color: white;
	background-color: rgba(0, 0, 0, 0.5);
	font-weight: bold;
	font-size: 20px;
	transition: 0.6s ease;
	border-radius: 0 3px 3px 0;
	user-select: none;
	-webkit-user-select: none;
}

/* Position the "next button" to the right */
.next {
	right: 0;
	border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover,
.next:hover {
	background-color: rgba(0, 0, 0, 0.8);
}

/* Number text (1/3 etc) */
.numbertext {
	color: #f2f2f2;
	font-size: 12px;
	padding: 8px 12px;
	position: absolute;
	top: 0;
	font-family: Helvetica, sans-serif;
}

img {
	display: block;
	margin-right: auto;
	margin-left: auto;
	margin-bottom: -4px;
}

.caption-container {
	text-align: center;
	background-color: black;
	padding: 2px 16px;
	color: white;
}

.demo {
	opacity: 0.6;
}

.active,
.demo:hover {
	opacity: 1;
}

img.hover-shadow {
	transition: 0.3s;
}

.hover-shadow:hover {
	box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}

.vertical-menu {
	width:300px;
}

.vertical-menu a {
	background-color: #eee;
	color: black;
	display: block;
	padding: 12px;
	text-decoration: none;
}

.vertical-menu a:hover {
	background-color: #ccc;
}

.vertical-menu a.active {
	background-color: #04AA6D;
	color: white;
}
		
table.view-table {
	width: 60%;
	margin-left: auto;
	margin-right: auto;
	background-color: rgba(192, 192, 192, 0.5);
	color: black;
	margin-bottom: 20px;
}
		
tr.view-row {
	border-collapse: collapse;
}

tr.view-row:hover {
	cursor: pointer;
}
		
td.view-cell {
	padding: 5px 0px 5px 15px; /*top right bottom left*/
}
		
.numbertext {
	background-color: rgba(0, 0, 0, 0.5);
}

.view-head {
	background-color: rgba(23, 52, 86, 0.88);
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
		
.pagenumbers {
	font-size: 15px;
	color: white;
	text-decoration: none;
	padding: 8px;
	border-radius: 8px;
}
		
.pagenumbers:hover {
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}
		
.validate-buttons {
	width: 80px;
	color: white;
	font-family: Helvetica, sans-serif;
	cursor: pointer;
	border: 0px;
	border-radius: 20px;
	padding: 8px;
	font-weight: bold;
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
</style>
</head>
<body style="background: #173457;">

<!-- Language links -->

<div class="langs">
    <?php foreach ($supported as $code => $label): 
        $url = '/language/language.php?lang=' . rawurlencode($code) . '&return=' . rawurlencode($return);
        $cls = ($code === $current) ? 'lang active' : 'lang';
    ?>
        <a class="<?php echo $cls; ?>" href="<?php echo $url; ?>"><?php echo htmlspecialchars($label); ?></a>
    <?php endforeach; ?>
</div>

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
				<li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo isset($_SESSION["username"])? $_SESSION["username"] : ''; ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span><?php echo $lengui['logout'][$current]; ?></a></li>
			</ul>
		</div>
	</nav>

<!-- Brief view to expanded view -->
<table class="view-table">
	
<?php
    $selectqry = "SELECT * FROM risk_register;";
	$result = mysqli_query($connection, $selectqry);
	
	$total_rows = mysqli_num_rows($result);
	
	$limit = 5;
    // get the required number of pages
    $total_pages = ceil ($total_rows / $limit);
	
    // update the active page number
    if (!isset ($_GET['page']) ) { 
        $page_number = 1;  
    } else {
        $page_number = (int)$_GET['page'];
    }

    // get the initial page number
    $initial_page = ($page_number-1) * $limit;

    // get data of selected rows per page
    $getQuery = "SELECT * FROM risk_register ORDER BY risk_un DESC LIMIT " . $initial_page . ',' . $limit;
    $resultLimit = mysqli_query($connection, $getQuery);

    //display the retrieved result on the webpage 
    if(mysqli_num_rows($result)>0) {
        while ($row = mysqli_fetch_array($resultLimit)) {
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
			
?>
<!-- Brief view -->
					<tr class="view-row">
						<td class="view-cell view-head">
							<?php echo $lengui['following_info'][$current]; ?> <?php echo htmlentities($isms_un, ENT_QUOTES, 'UTF-8'); ?>
						</td>
					</tr>					
					
					<tr class="view-row">
						<td class="view-cell">																					<!--risk_type-->
							<font style="font-size: 20px; font-family: Tahoma, sans-serif;">
								<b><?php echo $lengui['risk_type'][$current]; ?></b> <?php echo htmlentities($lengui['risk_type'.$isms_risk_type][$current], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--risk_name-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['risk_name'][$current]; ?></b> <?php echo htmlentities($isms_risk_name, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--threat_exp-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['threat'][$current]; ?></b> <?php echo htmlentities($isms_threat_exp, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--vulnerability_exp-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['vulnerability'][$current]; ?></b> <?php echo htmlentities($isms_vulnerability_exp, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--impact_v-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['impact'][$current]; ?></b> <?php echo htmlentities($isms_impact_v, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--likelihood_v-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['likelihood'][$current]; ?></b> <?php echo htmlentities($isms_likelihood_v, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--risk_owner_name-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['owner'][$current]; ?></b> <?php echo htmlentities($isms_risk_owner_name, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--treatment_decision-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['decision'][$current]; ?></b> <?php echo htmlentities($lengui['decision'.$isms_treatment_decision][$current], ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row">
						<td class="view-cell">																						<!--treatment_plan-->
							<font style="font-size: 15px; font-family: Helvetica, sans-serif;">
								<b><?php echo $lengui['path'][$current]; ?></b> <?php echo htmlentities($isms_treatment_plan, ENT_QUOTES, 'UTF-8'); ?>
							</font>
						</td>
					</tr>
					
					<tr class="view-row" style="border-bottom: 10px solid #173457;">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
<?php
        }
    }
?>
	<tr class="view-row" style="border-bottom: 20px solid #173457;">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	<tr>
	
	<tr style="background: #173457;">
		<td colspan=2 style="">
			<div style="text-align: center;">
<?php
				for($page_number = 1; $page_number<= $total_pages; $page_number++) {  
?>
					<a href = "sagi.php?page=<?php echo $page_number; ?>" class="pagenumbers"><?php echo $page_number; ?></a> 	
<?php
				}
?>
			</div>
		</td>
	</tr>

</table>
</body>
</html>
