<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php

// Get resize parameters
$resize = (@$_GET["resize"] <> "");
$width = (@$_GET["width"] <> "") ? $_GET["width"] : 0;
$height = (@$_GET["height"] <> "") ? $_GET["height"] : 0;
if (@$_GET["width"] == "" && @$_GET["height"] == "") {
	$width = EW_THUMBNAIL_DEFAULT_WIDTH;
	$height = EW_THUMBNAIL_DEFAULT_HEIGHT;
}
$quality = (@$_GET["quality"] <> "") ? $_GET["quality"] : EW_THUMBNAIL_DEFAULT_QUALITY;

// Resize image from physical file
if (@$_GET["fn"] <> "") {
	$fn = ew_StripSlashes($_GET["fn"]);
	$fn = str_replace("\0", "", $fn);
	$fn = ew_IncludeTrailingDelimiter(ew_AppRoot(), TRUE) . $fn;
	if (file_exists($fn) || fopen($fn, "rb") !== FALSE) { // Allow remote file
		$pathinfo = pathinfo($fn);
		$ext = strtolower($pathinfo["extension"]);
		if (in_array($ext, explode(",", EW_IMAGE_ALLOWED_FILE_EXT))) {
			$size = @getimagesize($fn);
			if ($size)
				header("Content-type: {$size['mime']}");
			echo ew_ResizeFileToBinary($fn, $width, $height, $quality);
		}
	}
	exit();
}
?>
