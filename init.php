<?php
session_start(); // Always first

if (isset($_GET["folder"]))
	$folder = $_GET["folder"];
else
	$folder = "Naruto";
	
if (isset($_GET["id"]))
	$morf_mediaid = $_GET["id"];
else
	$morf_mediaid = 0;


$folder_url = rawurlencode($folder);
$folder_html = htmlentities($folder, ENT_COMPAT, "UTF-8");
$folder_shows = dirname($folder);
if($folder_shows == ".")
	$folder_shows = $folder;
$folder_shows_url = rawurlencode($folder_shows);

$morf_showspath="nas/Serier/";
$morf_shows = scandir(dirname(__FILE__) . "/" . $morf_showspath);

$morf_mediafilepath= $morf_showspath . $folder . "/";
$morf_mediafiles = glob($morf_mediafilepath . "*.{avi,mkv,wmv}", GLOB_BRACE);
sort($morf_mediafiles); //Sort epsiodes alphabetically.

$morf_mediafilename = basename($morf_mediafiles[$morf_mediaid]);
$morf_mediafilename_url = rawurlencode($morf_mediafilename);
$morf_mediafilename_html = htmlentities($morf_mediafilename, ENT_COMPAT, "UTF-8");
if (substr($morf_mediafilename, -4) == ".mkv")
	$is_mkv = true;
else
	$is_mkv = false;

$morf_movie_url = str_replace("%2F","/", rawurlencode($morf_mediafilepath . $morf_mediafilename));
$morf_metadata_subtitles_url = str_replace("%2F","/", rawurlencode("metadata/" . $folder . "/" . $morf_mediafilename . ".ass"));
$morf_metadata_poster_url = str_replace("%2F","/", rawurlencode("metadata/" . $folder . "/" . $morf_mediafilename . ".jpg"));

$mode = "vlc";
if (isset($_GET["mode"]))
{
	if ($_GET["mode"] == "divx")
		$mode = "divx";
	elseif ($_GET["mode"] == "html5")
		$mode = "html5";
	elseif ($_GET["mode"] == "vlc")
		$mode = "vlc";
	elseif ($_GET["mode"] == "qt")
		$mode = "qt";
	elseif ($_GET["mode"] == "wmp")
		$mode = "wmp";
}


// Checks if admin logged in (session set)
if (!isset($_SESSION['sess_user'])){
	header("Location: logon.php?sessiontimeout=1&folder=".$folder_url."&id=".$morf_mediaid."&mode=".$mode);
	exit;
}

?>