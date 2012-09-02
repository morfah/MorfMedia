<?php
//check if someone is manually uploading a image
if (isset($_POST["posterimage"])){
	//debug
	/*echo "<pre>";
	var_dump ($_POST);
	var_dump($_FILES);
	echo "</pre>";*/

	$posterimage = $_POST["posterimage"];
	$files_posterimage = str_replace(" ", "_", $_POST["posterimage"]);
	
	$returnurl = $_POST["returnurl"];
	$morf_posterfile = dirname(__FILE__) . "/metadata/" . $posterimage . ".jpg";
	$morf_posterfile_shell = escapeshellarg($morf_posterfile);

	//checks and moves the file
	if (isset($_FILES[$files_posterimage]["tmp_name"]))
		if ($_FILES[$files_posterimage]["error"] == 0)
			if ($_FILES[$files_posterimage]["type"] == "image/jpeg")
				if (is_uploaded_file($_FILES[$files_posterimage]["tmp_name"]))
					move_uploaded_file($_FILES[$files_posterimage]["tmp_name"], $morf_posterfile);

	header("location: $returnurl");
}

//else we try to get a image from IMDB.
else{
	// poster image.
	$imdb = json_decode(file_get_contents("http://www.imdbapi.com/?t=" . rawurlencode($morf_shows[$i])));
	$morf_posterfile = dirname(__FILE__) . "/metadata/" . $morf_shows[$i] . ".jpg";
	$morf_posterfile_shell = escapeshellarg($morf_posterfile);
	$morf_posterurl_shell = escapeshellarg($imdb->{'Poster'});
	//echo "wget -O $morf_posterfile_shell $morf_posterurl_shell";
	exec("wget -O $morf_posterfile_shell $morf_posterurl_shell 2>&1");
}

//resize the image
if (filesize($morf_posterfile))
	exec("convert $morf_posterfile_shell -resize 160x9999 $morf_posterfile_shell 2>&1");
?>