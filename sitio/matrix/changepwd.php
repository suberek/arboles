<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$changepwd = NULL; // Initialize page object first

class cchangepwd extends cusuarios {

	// Page ID
	var $PageID = 'changepwd';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Page object name
	var $PageObjName = 'changepwd';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-error ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<table class=\"ewStdTable\"><tr><td><div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div></td></tr></table>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		return TRUE;
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (usuarios)
		if (!isset($GLOBALS["usuarios"])) {
			$GLOBALS["usuarios"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["usuarios"];
		}
		if (!isset($GLOBALS["usuarios"])) $GLOBALS["usuarios"] = &$this;

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'changepwd', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// User profile
		$UserProfile = new cUserProfile();
		$UserProfile->LoadProfile(@$_SESSION[EW_SESSION_USER_PROFILE]);

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn() || $Security->IsSysAdmin())
			$this->Page_Terminate("login.php");
		$Security->LoadCurrentUserLevel($this->ProjectID . 'usuarios');

		// Update last accessed time
		if ($UserProfile->IsValidUser(session_id())) {
			if (!$Security->IsSysAdmin())
				$UserProfile->SaveProfileToDatabase(CurrentUserName()); // Update last accessed time to user profile
		} else {
			echo $Language->Phrase("UserProfileCorrupted");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $OldPassword = "";
	var $NewPassword = "";
	var $ConfirmedPassword = "";

	// 
	// Page main
	//
	function Page_Main() {
		global $conn, $Language, $Security, $gsFormError;
		global $Breadcrumb;
		$Breadcrumb = new cBreadcrumb;
		$Breadcrumb->Add("changepwd", "<span id=\"ewPageCaption\">" . $Language->Phrase("ChangePwdPage") . "</span>", ew_CurrentUrl());
		$bPostBack = ew_IsHttpPost();
		$bValidate = TRUE;
		if ($bPostBack) {
			$this->OldPassword = ew_StripSlashes(@$_POST["opwd"]);
			$this->NewPassword = ew_StripSlashes(@$_POST["npwd"]);
			$this->ConfirmedPassword = ew_StripSlashes(@$_POST["cpwd"]);
			$bValidate = $this->ValidateForm($this->OldPassword, $this->NewPassword, $this->ConfirmedPassword);
			if (!$bValidate) {
				$this->setFailureMessage($gsFormError);
			}
		}
		$bPwdUpdated = FALSE;
		if ($bPostBack && $bValidate) {

			// Setup variables
			$sUsername = $Security->CurrentUserName();
			$sFilter = str_replace("%u", ew_AdjustSql($sUsername), EW_USER_NAME_FILTER);

			// Set up filter (Sql Where Clause) and get Return SQL
			// SQL constructor in usuarios class, usuariosinfo.php

			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			if ($rs = $conn->Execute($sSql)) {
				if (!$rs->EOF) {
					$rsold = $rs->fields;
					if (ew_ComparePassword($rsold['password'], $this->OldPassword)) {
						$bValidPwd = TRUE;
						$bValidPwd = $this->User_ChangePassword($rsold, $sUsername, $this->OldPassword, $this->NewPassword);
						if ($bValidPwd) {
							$rsnew = array('password' => $this->NewPassword); // Change Password
							$sEmail = $rsold['email'];
							$rs->Close();
							$conn->raiseErrorFn = 'ew_ErrorFn';
							$bValidPwd = $this->Update($rsnew);
							$conn->raiseErrorFn = '';
							if ($bValidPwd)
								$bPwdUpdated = TRUE;
						} else {
							$this->setFailureMessage($Language->Phrase("InvalidNewPassword"));
							$rs->Close();
						}
					} else {
						$this->setFailureMessage($Language->Phrase("InvalidPassword"));
					}
				} else {
					$rs->Close();
				}
			}
		}
		if ($bPwdUpdated) {
			if (@$sEmail <> "") {

				// Load Email Content
				$Email = new cEmail();
				$Email->Load("phptxt/changepwd.txt");
				$Email->ReplaceSender(EW_SENDER_EMAIL); // Replace Sender
				$Email->ReplaceRecipient($sEmail); // Replace Recipient
				$Email->ReplaceContent('<!--$Password-->', $this->NewPassword);
				$Email->Charset = EW_EMAIL_CHARSET;
				$Args = array();
				$Args["rs"] = &$rsnew;
				$bEmailSent = FALSE;
				if ($this->Email_Sending($Email, $Args))
					$bEmailSent = $Email->Send();

				// Send email failed
				if (!$bEmailSent)
					$this->setFailureMessage($Email->SendErrDescription);
			}
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("PasswordChanged")); // Set up success message
			$this->Page_Terminate("index.php"); // Exit page and clean up
		}
	}

	// Validate form
	function ValidateForm($opwd, $npwd, $cpwd) {
		global $Language, $gsFormError;

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;

		// Initialize form error message
		$gsFormError = "";
		if ($opwd == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterOldPassword"));
		}
		if ($npwd == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterNewPassword"));
		}
		if ($npwd <> $cpwd) {
			ew_AddMessage($gsFormError, $Language->Phrase("MismatchPassword"));
		}

		// Return validate result
		$valid = ($gsFormError == "");

		// Call Form CustomValidate event
		$sFormCustomError = "";
		$valid = $valid && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $valid;
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'
	function Message_Showing(&$msg, $type) {

		// Example:
		//if ($type == 'success') $msg = "your success message";

	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// User ChangePassword event
	function User_ChangePassword(&$rs, $usr, $oldpwd, &$newpwd) {

		// Return FALSE to abort
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($changepwd)) $changepwd = new cchangepwd();

// Page init
$changepwd->Page_Init();

// Page main
$changepwd->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$changepwd->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<script type="text/javascript">
var fchangepwd = new ew_Form("fchangepwd");

// Extend form with Validate function
fchangepwd.Validate = function() {
	var fobj = this.Form;
	if (!this.ValidateRequired)
		return true; // Ignore validation
	if  (!ew_HasValue(fobj.opwd))
		return this.OnError(fobj.opwd, ewLanguage.Phrase("EnterOldPassword"));
	if  (!ew_HasValue(fobj.npwd))
		return this.OnError(fobj.npwd, ewLanguage.Phrase("EnterNewPassword"));
	if  (fobj.npwd.value != fobj.cpwd.value)
		return this.OnError(fobj.cpwd, ewLanguage.Phrase("MismatchPassword"));

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj)) return false;
	return true;
}

// Extend form with Form_CustomValidate function
fchangepwd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Requires js validation
<?php if (EW_CLIENT_VALIDATE) { ?>
fchangepwd.ValidateRequired = true;
<?php } else { ?>
fchangepwd.ValidateRequired = false;
<?php } ?>
</script>
<?php $Breadcrumb->Render(); ?>
<?php $changepwd->ShowPageHeader(); ?>
<?php
$changepwd->ShowMessage();
?>
<form name="fchangepwd" id="fchangepwd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<div class="ewChangepwdContent">
	<div class="control-group">
		<label class="control-label" for="opwd"><?php echo $Language->Phrase("OldPassword") ?></label>
		<div class="controls"><input type="password" name="opwd" id="opwd" class="input-large" placeholder="<?php echo $Language->Phrase("OldPassword") ?>"></div>
	</div>
	<div class="control-group">
		<label class="control-label" for="npwd"><?php echo $Language->Phrase("NewPassword") ?></label>
		<div class="controls"><input type="password" name="npwd" id="npwd" class="input-large" placeholder="<?php echo $Language->Phrase("NewPassword") ?>"></div>
	</div>
	<div class="control-group">
		<label class="control-label" for="cpwd"><?php echo $Language->Phrase("ConfirmPassword") ?></label>
		<div class="controls"><input type="password" name="cpwd" id="cpwd" class="input-large" placeholder="<?php echo $Language->Phrase("ConfirmPassword") ?>"></div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("ChangePwdBtn") ?></button>
		</div>
	</div>
</div>
</form>
<script type="text/javascript">
fchangepwd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$changepwd->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$changepwd->Page_Terminate();
?>
