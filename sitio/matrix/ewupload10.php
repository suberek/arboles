<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php
require('uploadhandler.php');

// Upload handler
class cUploadHandler extends UploadHandler {

	// Override initialize()
	protected function initialize() {
		if ($this->get_server_var("REQUEST_METHOD") == "GET" && isset($_GET["delete"]))
			$this->delete();
		else
			parent::initialize();
	}

	// Override get_user_id()
	protected function get_user_id() {
		global $uploadid;
		@session_start();
		$id = EW_UPLOAD_TEMP_FOLDER_PREFIX . session_id();
		if ($uploadid <> "") 
			$id .= "/" . $uploadid;
		return $id;
	}

	// Override handle_file_upload()
	protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
		$index = null, $content_range = null) {

		// Delete all files in directory if replace
		if (@$_POST["replace"] == "1") {
			$upload_dir = $this->get_upload_path();
			if ($ar = glob($upload_dir . "/*.*")) {
				foreach($ar as $v) {
		    		@unlink($v);
				}
			}
			foreach ($this->options["image_versions"] as $version => $options) {
				if (!empty($version)) {
					if ($ar = glob($upload_dir . "/" . $version . "/*.*")) {
						foreach($ar as $v){
							@unlink($v);
						}
					}
				}
			}
		}
		return parent::handle_file_upload($uploaded_file, $name, $size, $type, $error, $index, $content_range);
	}

	// Override post()
	public function post($print_response = true) {
		$ar = parent::post(FALSE);
		if (array_key_exists($this->options["param_name"], $ar)) {
			$ar["files"] = $ar[$this->options["param_name"]]; // Set key as "files" for jquery.fileupload-ui.js
			unset($ar[$this->options["param_name"]]);
		}
		return $this->generate_response($ar, $print_response);
	}

	// Override upcount_name_callback()
	protected function upcount_name_callback($matches) {
        $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        $ext = isset($matches[2]) ? $matches[2] : '';
        return '('.$index.')'.$ext;
    }

	// Override upcount_name()
    protected function upcount_name($name) {
        return preg_replace_callback(
            '/(?:(?:\(([\d]+)\))?(\.[^.]+))?$/',
            array($this, 'upcount_name_callback'),
            $name,
            1
        );
    }
}
$Language = new cLanguage();

// Set up upload parameters
$uploadid = (@$_GET["id"] <> "") ? $_GET["id"] : ((@$_POST["id"] <> "") ? $_POST["id"] : "");
$filetypes = (EW_UPLOAD_ALLOWED_FILE_EXT == "") ? "/.+$/i" : "/.[" . str_replace(",", "|", EW_UPLOAD_ALLOWED_FILE_EXT) . "]$/i";
$url = ew_FullUrl() . (($uploadid <> "") ? "?id=" . $uploadid : ""); // Add id for display and delete
$options = array(
	"param_name" => $uploadid,
	"user_dirs" => TRUE,
	"download_via_php" => TRUE,
	"script_url" => $url,
	"upload_dir" => ew_UploadPathEx(TRUE, EW_UPLOAD_DEST_PATH),
	"upload_url" => ew_UploadPathEx(FALSE, EW_UPLOAD_DEST_PATH),
	"max_file_size" => EW_MAX_FILE_SIZE,
	"accept_file_types" => $filetypes,
	"image_versions" => array(
		EW_UPLOAD_THUMBNAIL_FOLDER => array(
			"max_width" => EW_UPLOAD_THUMBNAIL_WIDTH,
			"max_height" => EW_UPLOAD_THUMBNAIL_HEIGHT,
			"jpeg_quality" => EW_THUMBNAIL_DEFAULT_QUALITY,
			"png_quality" => 9
		)
	)
);
$error_messages = array(
	1 => $Language->Phrase("UploadErrMsg1"),
	2 => $Language->Phrase("UploadErrMsg2"),
	3 => $Language->Phrase("UploadErrMsg3"),
	4 => $Language->Phrase("UploadErrMsg4"),
	6 => $Language->Phrase("UploadErrMsg6"),
	7 => $Language->Phrase("UploadErrMsg7"),
	8 => $Language->Phrase("UploadErrMsg8"),
	'post_max_size' => $Language->Phrase("UploadErrMsgPostMaxSize"),
	'max_file_size' => $Language->Phrase("UploadErrMsgMaxFileSize"),
	'min_file_size' => $Language->Phrase("UploadErrMsgMinFileSize"),
	'accept_file_types' => $Language->Phrase("UploadErrMsgAcceptFileTypes"),
	'max_number_of_files' => $Language->Phrase("UploadErrMsgMaxNumberOfFiles"),
	'max_width' => $Language->Phrase("UploadErrMsgMaxWidth"),
	'min_width' => $Language->Phrase("UploadErrMsgMinWidth"),
	'max_height' => $Language->Phrase("UploadErrMsgMaxHeight"),
	'min_height' => $Language->Phrase("UploadErrMsgMinHeight")
);
ob_end_clean();
$upload_handler = new cUploadHandler($options, TRUE, $error_messages);
?>
