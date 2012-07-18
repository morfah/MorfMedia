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
$folder_series = dirname($folder);
if($folder_series == ".")
	$folder_series = $folder;
$folder_series_url = rawurlencode($folder_series);

$morf_seriespath="nas/Serier/";
$morf_series = scandir(dirname(__FILE__) . "/" . $morf_seriespath);

$morf_mediafilepath= $morf_seriespath . $folder . "/";
$morf_mediafiles = glob($morf_mediafilepath . "*.{avi,mkv}", GLOB_BRACE);
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
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $morf_mediafilename_html;?></title>
	<meta charset="UTF-8">
	<link rel="icon" type="image/png" href="movie-icon.png">
	<link href="css/watch.css" rel="stylesheet">
	<script type="text/javascript">
		//autoscroll script
		function ScrollToCurrentlyplaying(){
			try{
				document.getElementById("movies").scrollTop = document.getElementById("<?php echo $folder_series_url;?>").offsetTop;
				document.getElementById("episodes").scrollTop = document.getElementById("<?php echo $morf_mediafilename_url;?>").offsetTop;
			}
			catch (err){
				//alert(err);
			}
		}
	</script>
		  
	<!-- adds some more features to the html5 player -->
	<link href="video-js/dist/video-js.css" rel="stylesheet">
	<script src="video-js/dist/video.js"></script>
	
<?php if($is_mkv):?>
	<!-- adds .ass subtitle support -->
	<link href="html5-ass-subtitles/example.css" rel="stylesheet">
	<script src="html5-ass-subtitles/assparser_utils.js" type="text/javascript"></script>
	<script src="html5-ass-subtitles/assparser.js" type="text/javascript"></script>
	<script src="html5-ass-subtitles/example.js" type="text/javascript"></script>
	<script type="text/javascript">
		//.ass script
		function videoTimeUpdate(){
			try{
				ASSCaptionsUpdate();
			}
			catch (err){
				//alert(err);
			}
		}
		
		function loadSubtitles(){
			try{
				prepareCaptions();
				fetchASSFile("<?php echo $morf_metadata_subtitles_url;?>");
			}
			catch(err){
				//alert(err);
			}
		}
	</script>
<?php endif;?>
</head>
<body onload="ScrollToCurrentlyplaying(); loadSubtitles();">
<div id="fullscreen">

<?php
	if (!file_exists(dirname(__FILE__) . "/metadata/" . $folder . "/" . $morf_mediafilename . ".jpg"))
		require("thumbnail.php");
		
		//animes only? FIXME: this always assumes track id3 is subtitles
	if (!file_exists(dirname(__FILE__) . "/metadata/" . $folder . "/" . $morf_mediafilename . ".ass") && $is_mkv)
		require("subtitles.php");
?>

<div class="sidepanel movies" id="movies">
<?php // ------------------- SERIES -------------------
for($i=2; $i < count($morf_series); $i++){
	if (!file_exists(dirname(__FILE__) . "/metadata/" . $morf_series[$i] . ".jpg"))
		require("thumbnail_imdb.php");
?>
	<span class="item<?php if ($morf_series[$i] == $folder_series):?> currentlyplaying<?php endif;?>" id="<?php echo rawurlencode($morf_series[$i]);?>">
		<a href="?folder=<?php echo rawurlencode($morf_series[$i]);?>&amp;id=0&amp;mode=<?php echo $mode;?>">
			<img src="metadata/<?php echo rawurlencode($morf_series[$i]);?>.jpg" alt="Missing image.">
		<br><span class="smalltit"><?php echo htmlentities($morf_series[$i], ENT_COMPAT, "UTF-8");?></span>
		</a>
		
		<!--<form action="thumbnail_imdb.php" method="post" enctype="multipart/form-data" style="display:none;">
			<input type="file" name="<?php echo $morf_series[$i];?>">
			<input type="submit" value="Go!">
			<input type="hidden" name="posterimage" value="<?php echo $morf_series[$i];?>">
			<input type="hidden" name="returnurl" value="<?php echo $_SERVER["REQUEST_URI"];?>">
		</form>-->
		
	</span>
<?php
}
?>
</div>

<div class="sidepanel episodes" id="episodes">
<?php // ------------------- EPSISODES -------------------
for($i=0; $i < count($morf_mediafiles); $i++){
?>
	<span class="item<?php if ($i == $morf_mediaid):?> currentlyplaying<?php endif;?>" id="<?php echo rawurlencode(basename($morf_mediafiles[$i]));?>">
		<a href="?folder=<?php echo $folder_url ?>&amp;id=<?php echo $i;?>&amp;mode=<?php echo $mode;?>">
			<img src="metadata/<?php echo str_replace("%2F","/", rawurlencode($folder));?>/<?php echo rawurlencode(basename($morf_mediafiles[$i]));?>.jpg" 
				title="<?php htmlentities($morf_mediafiles[$i], ENT_COMPAT, "UTF-8");?>" 
				alt="Image missing." class="thumbnail">
				<!--onmouseover="this.src='metadata/<?php echo str_replace("%2F","/", rawurlencode($folder));?>/<?php echo rawurlencode(basename($morf_mediafiles[$i]));?>.jpg'"
				onmouseout="this.src='metadata/<?php echo str_replace("%2F","/", rawurlencode($folder));?>/<?php echo rawurlencode(basename($morf_mediafiles[$i]));?>_antispoiler.jpg'"-->
			<br><span class="smalltit"><?php echo htmlentities(basename($morf_mediafiles[$i]), ENT_COMPAT, "UTF-8");?></span>
		</a>
	</span>
<?php
}
?>
</div>
<br>
<div id="content">

<span class="smalltit"><?php echo $folder_html;?></span><br>
<span class="bigtit"><?php echo $morf_mediafilename_html;?></span>

<?php // ------------------- HTML5 -------------------
if ($mode == "html5"):
?>
<video id="my_video_1" class="video-js vjs-default-skin" controls="controls" preload="none" width="1280" height="720"
	poster="<?php echo $morf_metadata_poster_url;?>" data-setup="{}" ontimeupdate="videoTimeUpdate()">
	<source src="<?php echo $morf_movie_url;?>" type="video/mp4">
	<!--<source src="<?php echo $morf_movie_url;?>" type='video/mp4; codecs="avc1.64001E, mp4a.40.2"'>-->
	<!--<source src="<?php echo $morf_movie_url;?>" type='video/mp4; codecs="a_ac3, avc"'>-->
</video>
<?php // ------------------- DIVX -------------------
elseif ($mode == "divx"):
?>
<object width="1280" height="720" data="<?php echo $morf_movie_url;?>">
	<param name="classid" value="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616">
	<param name="codebase" value="http://go.divx.com/plugin/DivXBrowserPlugin.cab">
	<param name="custommode" value="none">
	<param name="previewImage" value="<?php echo $morf_metadata_poster_url;?>">
	<param name="autoPlay" value="true">
	<param name="src" value="<?php echo $morf_movie_url;?>">
	
	<embed type="video/divx" src="<?php echo $morf_movie_url;?>"
		custommode="none" width="1280" height="720" autoPlay="true" 
		previewImage="<?php echo $morf_metadata_poster_url;?>"
		pluginspage="http://go.divx.com/plugin/download/">
</object>
<?php // ------------------- VLC -------------------
elseif ($mode == "vlc"):
?>
<object width="1280" height="720" data="<?php echo $morf_movie_url;?>">
	<param name="classid" value="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921">
	<param name="codebase" value="http://download.videolan.org/pub/videolan/vlc/last/win32/axvlc.cab">
	<param name="target" value="<?php echo $morf_movie_url;?>">
	<embed type="application/x-vlc-plugin" pluginspage="http://www.videolan.org" version="VideoLAN.VLCPlugin.2"
		width="1280" height="720"
		target="<?php echo $morf_movie_url;?>"
		id="vlc">
</object>
<?php // ------------------- Quicktime -------------------
elseif ($mode == "qt"):
?>
<object width="1280" height="720" data="<?php echo $morf_movie_url;?>">
	<param name="classid" value="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B">
	<param name="codebase" value="http://www.apple.com/qtactivex/qtplugin.cab">
	<param name="src" value="<?php echo $morf_movie_url;?>">
	<param name="controller" value="true">
</object>
<?php // ------------------- Windows Media Player -------------------
elseif ($mode == "wmp"):
?>
<object width="1280" height="720" type="video/x-ms-asf" url="<?php echo $morf_movie_url;?>" data="<?php echo $morf_movie_url;?>">
	<param name="classid" value="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6">
	<param name="url" value="<?php echo $morf_movie_url;?>">
	<param name="filename" value="<?php echo $morf_movie_url;?>">
	<param name="autostart" value="1">
	<param name="uiMode" value="full">
	<param name="autosize" value="1">
	<param name="playcount" value="1"> 
	<embed type="application/x-mplayer2" src="<?php echo $morf_movie_url;?>" width="1280" height="720" autostart="true" showcontrols="true" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/">
</object>
<?php
endif; // ------------------------------------------
?>

<br>

<table width="1280" align="center">
	<tr>
		<td align="left">
			<a href="?folder=<?php echo rawurlencode($folder);?>&amp;id=<?php echo $morf_mediaid;?>&amp;mode=html5">
				<img src="icons/HTML5_Badge_32.png" width="16" height="16" alt="Image missing.">
				<?php if ($mode == "html5"):?><span class="highlight">HTML5</span><?php else: ?>HTML5<?php endif;?>
			</a> &bull;
			<a href="?folder=<?php echo rawurlencode($folder);?>&amp;id=<?php echo $morf_mediaid;?>&amp;mode=vlc">
				<img src="icons/vlc-icon.png" alt="Image missing.">
				<?php if ($mode == "vlc"):?><span class="highlight">VideoLAN</span><?php else: ?>VideoLAN<?php endif;?>
			</a> &bull;
			<a href="?folder=<?php echo rawurlencode($folder);?>&amp;id=<?php echo $morf_mediaid;?>&amp;mode=divx">
				<img src="icons/Divx-Player-icon.png" alt="Image missing.">
				<?php if ($mode == "divx"):?><span class="highlight">DivX Webplayer</span><?php else: ?>DivX Webplayer<?php endif;?>
			</a><!-- &bull;
			<a href="?folder=<?php echo rawurlencode($folder);?>&amp;id=<?php echo $morf_mediaid;?>&amp;mode=qt">
				<img src="icons/Applic-Quicktime-icon.png" alt="Image missing."> <?php if ($mode == "qt"):?><span class="highlight">QuickTime</span><?php else: ?>QuickTime<?php endif;?>
			</a> &bull;
			<a href="?folder=<?php echo rawurlencode($folder);?>&amp;id=<?php echo $morf_mediaid;?>&amp;mode=wmp">
				<img src="icons/Windows-Media-Player-10-icon.png" alt="Image missing."> <?php if ($mode == "wmp"):?><span class="highlight">Windows Media Player</span><?php else: ?>Windows Media Player<?php endif;?>
			</a>-->
		</td>
		<td align="right">
			<a href="logon.php?logout=1">
				<img src="icons/Apps-session-logout-icon.png" alt="Image missing.">
				Logout
			</a>
		</td>
	</tr>
</table>


<div class="smalltit">
<pre>
	<?php echo round((disk_free_space($morf_seriespath) / 1024 / 1024 / 1024), 2) . " GB free in " . $morf_seriespath;?>
</pre>
<?php
if ($is_mkv){
	//exec("mkvinfo \"" . dirname(__FILE__) . "/" . $morf_mediafilepath . $morf_mediafilename . "\" > mkvinfo.txt");
?>
<pre>
<?php //echo file_get_contents("mkvinfo.txt");?>
</pre>
<?php
}
?>
</div>

</div>

</div>
</body>
</html>