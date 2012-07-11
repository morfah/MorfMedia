<?php
$morf_video_shell = escapeshellarg(dirname(__FILE__) . "/" . $morf_mediafilepath . $morf_mediafilename);
$morf_subs_shell  = escapeshellarg(dirname(__FILE__) . "/metadata/" . $folder . "/" . $morf_mediafilename . ".ass");
exec("mkvextract tracks $morf_video_shell 3:$morf_subs_shell 2>&1");
?>