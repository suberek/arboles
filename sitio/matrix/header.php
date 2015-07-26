<?php

// Compatibility with PHP Report Maker
if (!isset($Language)) {
	include_once "ewcfg10.php";
	include_once "ewshared10.php";
	$Language = new cLanguage();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $Language->ProjectPhrase("BodyTitle") ?></title>
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">
<link rel="stylesheet" href="phpcss/jquery.fileupload-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo EW_PROJECT_STYLESHEET_FILENAME ?>">
<?php if (ew_IsMobile()) { ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="phpcss/ewmobile.css">
<?php } ?>
<?php if (@$gsExport == "print" && @$_GET["pdf"] == "1" && EW_PDF_STYLESHEET_FILENAME <> "") { ?>
<link rel="stylesheet" type="text/css" href="<?php echo EW_PDF_STYLESHEET_FILENAME ?>">
<?php } ?>
<script type="text/javascript" src="<?php echo ew_jQueryFile("jquery-%v.min.js") ?>"></script>
<?php if (ew_IsMobile()) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo ew_jQueryFile("jquery.mobile-%v.min.css") ?>">
<script type="text/javascript">
jQuery(document).bind("mobileinit", function() {
	jQuery.mobile.ajaxEnabled = false;
	jQuery.mobile.ignoreContentEnabled = true;
});
</script>
<script type="text/javascript" src="<?php echo ew_jQueryFile("jquery.mobile-%v.min.js") ?>"></script>
<?php } ?>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="jqueryfileupload/jquery.ui.widget.js"></script>
<script type="text/javascript" src="jqueryfileupload/jqueryfileupload.min.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="phpjs/eweditor.js"></script>
<link href="calendar/calendar.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="calendar/calendar.min.js"></script>
<script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="calendar/calendar-setup.js"></script>
<script type="text/javascript" src="phpjs/ewcalendar.js"></script>
<script type="text/javascript">
var EW_LANGUAGE_ID = "<?php echo $gsLanguage ?>";
var EW_DATE_SEPARATOR = "/" || "/"; // Default date separator
var EW_DECIMAL_POINT = "<?php echo $DEFAULT_DECIMAL_POINT ?>";
var EW_THOUSANDS_SEP = "<?php echo $DEFAULT_THOUSANDS_SEP ?>";
var EW_MAX_FILE_SIZE = <?php echo EW_MAX_FILE_SIZE ?>; // Upload max file size
var EW_UPLOAD_ALLOWED_FILE_EXT = "<?php echo EW_UPLOAD_ALLOWED_FILE_EXT ?>"; // Allowed upload file extension

// Ajax settings
var EW_LOOKUP_FILE_NAME = "ewlookup10.php"; // Lookup file name
var EW_AUTO_SUGGEST_MAX_ENTRIES = <?php echo EW_AUTO_SUGGEST_MAX_ENTRIES ?>; // Auto-Suggest max entries

// Common JavaScript messages
var EW_DISABLE_BUTTON_ON_SUBMIT = true;
var EW_IMAGE_FOLDER = "phpimages/"; // Image folder
var EW_UPLOAD_URL = "<?php echo EW_UPLOAD_URL ?>"; // Upload url
var EW_UPLOAD_THUMBNAIL_WIDTH = <?php echo EW_UPLOAD_THUMBNAIL_WIDTH ?>; // Upload thumbnail width
var EW_UPLOAD_THUMBNAIL_HEIGHT = <?php echo EW_UPLOAD_THUMBNAIL_HEIGHT ?>; // Upload thumbnail height
var EW_USE_JAVASCRIPT_MESSAGE = false;
<?php if (ew_IsMobile()) { ?>
var EW_IS_MOBILE = true;
<?php } else { ?>
var EW_IS_MOBILE = false;
<?php } ?>
</script>
<script type="text/javascript" src="phpjs/jsrender.min.js"></script>
<script type="text/javascript" src="phpjs/ewp10.js"></script>
<script type="text/javascript" src="phpjs/userfn10.js"></script>
<script type="text/javascript">
<?php echo $Language->ToJSON() ?>
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="<?php echo ew_ConvertFullUrl("favicon.ico") ?>"><link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo ew_ConvertFullUrl("favicon.ico") ?>">
<meta name="generator" content="PHPMaker v10.0.2">
</head>
<body>
<?php if (ew_IsMobile()) { ?>
<div data-role="page">
	<div data-role="header">
		<a href="mobilemenu.php"><?php echo $Language->Phrase("MobileMenu") ?></a>
		<h1 id="ewPageTitle"></h1>
	<?php if (IsLoggedIn()) { ?>
		<a href="logout.php"><?php echo $Language->Phrase("Logout") ?></a>
	<?php } elseif (substr(ew_ScriptName(), 0 - strlen("login.php")) <> "login.php") { ?>
		<a href="login.php"><?php echo $Language->Phrase("Login") ?></a>
	<?php } ?>
	</div>
<?php } ?>
<?php if (@!$gbSkipHeaderFooter) { ?>
<div class="ewLayout">
<?php if (!ew_IsMobile()) { ?>
	<!-- header (begin) --><!-- *** Note: Only licensed users are allowed to change the logo *** -->
  <div id="ewHeaderRow" class="ewHeaderRow"><img src="phpimages/logo-152x152.png" alt="" style="border: 0;">	</div>
	<!-- header (end) -->
<?php } ?>
<?php if (ew_IsMobile()) { ?>
	<div data-role="content" data-enhance="false">
	<table id="ewContentTable" class="ewContentTable">
		<tr>
<?php } else { ?>
	<!-- content (begin) -->
	<table id="ewContentTable" cellspacing="0" class="ewContentTable">
		<tr><td id="ewMenuColumn" class="ewMenuColumn">
			<!-- left column (begin) -->
<?php include_once "ewmenu.php" ?>
			<!-- left column (end) -->
		</td>
<?php } ?>
		<td id="ewContentColumn" class="ewContentColumn">
			<!-- right column (begin) -->
				<p class="ewSiteTitle"><?php echo $Language->ProjectPhrase("BodyTitle") ?></p>
<?php } ?>
