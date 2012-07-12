<?php
if (!file_exists("config.php")){
	require_once "error.php";
	errorpage(1001, $_SERVER["SCRIPT_FILENAME"], $_SERVER['REQUEST_URI']."config.php");
	exit;
}

session_start(); // This should always be first (high up) on the page

require_once "config.php"; // Database connection
require_once "validate.php"; // Validation Functions
// timezone and charset
$sql = "SELECT timezone,charset FROM `site_settings` WHERE id_site=1";
$query = mysql_query($sql, $conn);
$fetch = mysql_fetch_array($query);
if ($fetch["charset"]!="") $charset = $fetch["charset"];
else $charset = "UTF-8";
if ($fetch["timezone"]!="") date_default_timezone_set($fetch["timezone"]);

// Logging in
if (isset($_POST['submit'])){
	if (valid($_POST['username'], 1, 20)){ 
		$sql = "SELECT * FROM users WHERE username='" . $_POST['username'] . "' AND password='" . sha1($_POST['password']) . "'";
		$result = mysql_query($sql);

		// If username and/or password wasn't found
		// send an error to the form
		if (mysql_num_rows($result) == 0){
			header("Location: logon.php?badlogin=1");
			exit;
		}

		// Set session with unique index
		$_SESSION['sess_id'] = mysql_result($result, 0, 'id_users');
		$_SESSION['sess_user'] = $_POST['username'];
		$_SESSION['sess_pass'] = sha1($_POST['password']);
		// Updating "last_login" 
		$sql2 = "UPDATE users SET last_login = '" . date('Y-m-d H:i:s') . "' WHERE id_users=" . $_SESSION['sess_id'];
		@mysql_query($sql2) or die("error...");
		if (isset($_POST["folder"]))
			header("Location: index.php?folder=" . $_POST["folder"]."&id=".$_POST["id"]."&mode=".$_POST["mode"]); // Redirecting to index.php
		else
			header("Location: index.php"); // Redirecting to index.php
		
		exit;
	} 
	else{
		header("Location: logon.php?badlogin=");
	}
}
 
// Logging out
if (isset($_GET['logout'])){
  session_unset();
  session_destroy();
  header("Location: logon.php");
  exit;
}

if (isset($_SESSION['sess_user'])){
	  header("Location: index.php");
	  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="Author" 
		  content="Kristian 'morfar' Johansson">
	<title>MorfMedia</title>
	<meta charset="<?php echo $charset;?>">
	<link rel="icon" 
		  type="image/png" 
		  href="movie-icon.png">
	<link rel="stylesheet" href="css/logon.css" type="text/css">
<script src="cookies.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript">
function javascript_cookies_check() {
	document.getElementById("submit").disabled = false;
	
	// Cookie check
	createCookie('cookie_check','enabled',1);
	if (readCookie('cookie_check')==null) {
		document.getElementById("cookie_error").style.display = "block";
		document.getElementById("submit").disabled = true;
	}
	eraseCookie('cookie_check');
}
</script>
</head>
<body onload="javascript_cookies_check()">

<?php
 
// If not logged in show the form, if logged in show 'logout' link
if (!isset($_SESSION['sess_user'])){
	// Show error message if login was incorrect
	if (isset($_GET['badlogin'])){?>
		<div class="infobox centerbox error">Wrong username or password!<br>Try again!</div>
	<?php }
	else if (isset($_GET['sessiontimeout'])){?>
		<div class="infobox centerbox warning">The login session is over. Please login here again.</div>
	<?php }
?> 
<p>&nbsp;</p>
<form action="logon.php" method="post" target="_top" id="loginform">
<table align="center" border="0" cellspacing="0" cellpadding="0">
<tr><th colspan="2"><?php echo $sitename?></th></tr>
<tr><td class="relatedLinks">Username:</td><td class="relatedLinks"><input name="username" type="text" id="username" tabindex="1"></td></tr>
<tr><td class="relatedLinks">Password:</td><td class="relatedLinks"><input name="password" type="password" id="password" tabindex="2"></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" align="right">
	<input name="submit" type="submit" id="submit" tabindex="3" value="Login" disabled="disabled">
<?php if(isset($_GET["folder"]) && isset($_GET["id"]) && isset($_GET["mode"])):?>
	<input name="folder" id="folder" type="hidden" value="<?php echo rawurlencode($_GET["folder"]);?>">
	<input name="id" id="id" type="hidden" value="<?php echo $_GET["id"];?>">
	<input name="mode" id="mode" type="hidden" value="<?php echo $_GET["mode"];?>">
<?php endif;?>
</td></tr>
</table>
</form>
<noscript>
<div class="errorbox" id="javascript_error" style="display:block;">
<div class="header">Javascript</div>
<div class="explaination">Javascript needs to be enabled for this site.</div>
<div class="solution">
	Troubleshooting:
	<ul>
		<li>Check your browser configuration or browser extensions. Make sure you enable Javascript support for at least this site.</li>
	</ul>
</div>
</div>
</noscript>
<div class="errorbox" id="cookie_error" style="display:none;">
<div class="header">Cookies</div>
<div class="explaination">Cookies needs to be enabled for this site.</div>
<div class="solution">
	Troubleshooting:
	<ul>
		<li>Check your browser configuration or browser extensions. Make sure you enable Cookies for at least this site.</li>
	</ul>
</div>
</div>
<?php
}
?>
</body>
</html>