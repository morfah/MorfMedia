<?php
// make folder
$morf_folder = dirname(__FILE__) . "/metadata/" . $folder . "/";
if (!is_dir($morf_folder))
	mkdir($morf_folder);

// high-res poster image. slow.
$morf_video_shell = escapeshellarg(dirname(__FILE__) . "/" . $morf_mediafilepath . $morf_mediafilename);
$morf_image_shell = escapeshellarg(dirname(__FILE__) . "/metadata/" . $folder . "/" . $morf_mediafilename . ".jpg");
//exec("ffmpeg -i $morf_video_shell -deinterlace -an -ss 300 -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $morf_image_shell 2>&1");
exec("ffmpeg -i $morf_video_shell -deinterlace -an -ss 300 -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $morf_image_shell > /dev/null 2>/dev/null &");

//exec("ffmpeg -i $morf_video_shell -itsoffset -300 -vcodec mjpeg -vframes 1 -an -f mjpeg $morf_image_shell 2>&1");

// low-res blurry antispoiler image.
$morf_image_antispoiler_shell = escapeshellarg(dirname(__FILE__) . "/metadata/" . $folder . "/" . $morf_mediafilename . "_antispoiler.jpg");
//exec("convert $morf_image_shell -resize 160x90 -blur 10x10 $morf_image_antispoiler_shell 2>&1");
exec("convert $morf_image_shell -resize 160x90 -blur 10x10 $morf_image_antispoiler_shell > /dev/null 2>/dev/null &");
?>