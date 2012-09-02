<?php
$morf_showspath="nas/Serier/";
$morf_shows = scandir(dirname(__FILE__) . "/" . $morf_showspath);
for ($i=2;count($morf_shows)>$i;$i++){
	$morf_mediafilepath= $morf_showspath . $morf_shows[$i] . "/";
	if (is_dir($morf_mediafilepath)){
		$folder = basename($morf_mediafilepath);
		$episodes = glob($morf_mediafilepath . "*.{avi,mkv}", GLOB_BRACE);
		if (count($episodes) > 0){
			sort($episodes); //Sort epsiodes alphabetically.
			for ($ii=0;$ii < count($episodes);$ii++){
				if (file_exists("metadata/" . $folder . "/" . basename($episodes[$ii]) . ".jpg"))
					echo " <span style=\"color:green;font-weight:bold;\">" . $episodes[$ii] . "</span>";
				else
					echo " <span style=\"color:red;font-weight:normal;\">" . $episodes[$ii] . "</span>";
				echo "<br>";
			}
		}
		
		//seasons		
		echo "<br>";
	}
}
?>