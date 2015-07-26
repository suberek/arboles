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

$usuarios_delete = NULL; // Initialize page object first

class cusuarios_delete extends cusuarios {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Table name
	var $TableName = 'usuarios';

	// Page object name
	var $PageObjName = 'usuarios_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
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
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
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

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'usuarios', TRUE);

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
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		$Security->TablePermission_Loaded();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("usuarioslist.php");
		}

		// Update last accessed time
		if ($UserProfile->IsValidUser(session_id())) {
			if (!$Security->IsSysAdmin())
				$UserProfile->SaveProfileToDatabase(CurrentUserName()); // Update last accessed time to user profile
		} else {
			echo $Language->Phrase("UserProfileCorrupted");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action
		$this->id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("usuarioslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in usuarios class, usuariosinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->id->setDbValue($rs->fields('id'));
		$this->nombre->setDbValue($rs->fields('nombre'));
		$this->password->setDbValue($rs->fields('password'));
		$this->_email->setDbValue($rs->fields('email'));
		$this->nombre_completo->setDbValue($rs->fields('nombre_completo'));
		$this->descripcion->setDbValue($rs->fields('descripcion'));
		$this->facebook->setDbValue($rs->fields('facebook'));
		$this->twitter->setDbValue($rs->fields('twitter'));
		$this->url->setDbValue($rs->fields('url'));
		$this->permisos->setDbValue($rs->fields('permisos'));
		$this->status->setDbValue($rs->fields('status'));
		$this->profile->setDbValue($rs->fields('profile'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id->DbValue = $row['id'];
		$this->nombre->DbValue = $row['nombre'];
		$this->password->DbValue = $row['password'];
		$this->_email->DbValue = $row['email'];
		$this->nombre_completo->DbValue = $row['nombre_completo'];
		$this->descripcion->DbValue = $row['descripcion'];
		$this->facebook->DbValue = $row['facebook'];
		$this->twitter->DbValue = $row['twitter'];
		$this->url->DbValue = $row['url'];
		$this->permisos->DbValue = $row['permisos'];
		$this->status->DbValue = $row['status'];
		$this->profile->DbValue = $row['profile'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// id
		// nombre
		// password
		// email
		// nombre_completo
		// descripcion
		// facebook
		// twitter
		// url
		// permisos
		// status
		// profile

		$this->profile->CellCssStyle = "white-space: nowrap;";
		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id
			$this->id->ViewValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// nombre
			$this->nombre->ViewValue = $this->nombre->CurrentValue;
			$this->nombre->ViewCustomAttributes = "";

			// email
			$this->_email->ViewValue = $this->_email->CurrentValue;
			$this->_email->ViewCustomAttributes = "";

			// nombre_completo
			$this->nombre_completo->ViewValue = $this->nombre_completo->CurrentValue;
			$this->nombre_completo->ViewCustomAttributes = "";

			// facebook
			$this->facebook->ViewValue = $this->facebook->CurrentValue;
			$this->facebook->ViewCustomAttributes = "";

			// twitter
			$this->twitter->ViewValue = $this->twitter->CurrentValue;
			$this->twitter->ViewCustomAttributes = "";

			// url
			$this->url->ViewValue = $this->url->CurrentValue;
			$this->url->ViewCustomAttributes = "";

			// permisos
			if ($Security->CanAdmin()) { // System admin
			if (strval($this->permisos->CurrentValue) <> "") {
				switch ($this->permisos->CurrentValue) {
					case $this->permisos->FldTagValue(1):
						$this->permisos->ViewValue = $this->permisos->FldTagCaption(1) <> "" ? $this->permisos->FldTagCaption(1) : $this->permisos->CurrentValue;
						break;
					case $this->permisos->FldTagValue(2):
						$this->permisos->ViewValue = $this->permisos->FldTagCaption(2) <> "" ? $this->permisos->FldTagCaption(2) : $this->permisos->CurrentValue;
						break;
					case $this->permisos->FldTagValue(3):
						$this->permisos->ViewValue = $this->permisos->FldTagCaption(3) <> "" ? $this->permisos->FldTagCaption(3) : $this->permisos->CurrentValue;
						break;
					default:
						$this->permisos->ViewValue = $this->permisos->CurrentValue;
				}
			} else {
				$this->permisos->ViewValue = NULL;
			}
			} else {
				$this->permisos->ViewValue = "********";
			}
			$this->permisos->ViewCustomAttributes = "";

			// status
			$this->status->ViewValue = $this->status->CurrentValue;
			$this->status->ViewCustomAttributes = "";

			// id
			$this->id->LinkCustomAttributes = "";
			$this->id->HrefValue = "";
			$this->id->TooltipValue = "";

			// nombre
			$this->nombre->LinkCustomAttributes = "";
			$this->nombre->HrefValue = "";
			$this->nombre->TooltipValue = "";

			// email
			$this->_email->LinkCustomAttributes = "";
			$this->_email->HrefValue = "";
			$this->_email->TooltipValue = "";

			// nombre_completo
			$this->nombre_completo->LinkCustomAttributes = "";
			$this->nombre_completo->HrefValue = "";
			$this->nombre_completo->TooltipValue = "";

			// facebook
			$this->facebook->LinkCustomAttributes = "";
			$this->facebook->HrefValue = "";
			$this->facebook->TooltipValue = "";

			// twitter
			$this->twitter->LinkCustomAttributes = "";
			$this->twitter->HrefValue = "";
			$this->twitter->TooltipValue = "";

			// url
			$this->url->LinkCustomAttributes = "";
			$this->url->HrefValue = "";
			$this->url->TooltipValue = "";

			// permisos
			$this->permisos->LinkCustomAttributes = "";
			$this->permisos->HrefValue = "";
			$this->permisos->TooltipValue = "";

			// status
			$this->status->LinkCustomAttributes = "";
			$this->status->HrefValue = "";
			$this->status->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['id'];
				$this->LoadDbValues($row);
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "usuarioslist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
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
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($usuarios_delete)) $usuarios_delete = new cusuarios_delete();

// Page init
$usuarios_delete->Page_Init();

// Page main
$usuarios_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$usuarios_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var usuarios_delete = new ew_Page("usuarios_delete");
usuarios_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = usuarios_delete.PageID; // For backward compatibility

// Form object
var fusuariosdelete = new ew_Form("fusuariosdelete");

// Form_CustomValidate event
fusuariosdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fusuariosdelete.ValidateRequired = true;
<?php } else { ?>
fusuariosdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($usuarios_delete->Recordset = $usuarios_delete->LoadRecordset())
	$usuarios_deleteTotalRecs = $usuarios_delete->Recordset->RecordCount(); // Get record count
if ($usuarios_deleteTotalRecs <= 0) { // No record found, exit
	if ($usuarios_delete->Recordset)
		$usuarios_delete->Recordset->Close();
	$usuarios_delete->Page_Terminate("usuarioslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $usuarios_delete->ShowPageHeader(); ?>
<?php
$usuarios_delete->ShowMessage();
?>
<form name="fusuariosdelete" id="fusuariosdelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="usuarios">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($usuarios_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_usuariosdelete" class="ewTable ewTableSeparate">
<?php echo $usuarios->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($usuarios->id->Visible) { // id ?>
		<td><span id="elh_usuarios_id" class="usuarios_id"><?php echo $usuarios->id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($usuarios->nombre->Visible) { // nombre ?>
		<td><span id="elh_usuarios_nombre" class="usuarios_nombre"><?php echo $usuarios->nombre->FldCaption() ?></span></td>
<?php } ?>
<?php if ($usuarios->_email->Visible) { // email ?>
		<td><span id="elh_usuarios__email" class="usuarios__email"><?php echo $usuarios->_email->FldCaption() ?></span></td>
<?php } ?>
<?php if ($usuarios->nombre_completo->Visible) { // nombre_completo ?>
		<td><span id="elh_usuarios_nombre_completo" class="usuarios_nombre_completo"><?php echo $usuarios->nombre_completo->FldCaption() ?></span></td>
<?php } ?>
<?php if ($usuarios->facebook->Visible) { // facebook ?>
		<td><span id="elh_usuarios_facebook" class="usuarios_facebook"><?php echo $usuarios->facebook->FldCaption() ?></span></td>
<?php } ?>
<?php if ($usuarios->twitter->Visible) { // twitter ?>
		<td><span id="elh_usuarios_twitter" class="usuarios_twitter"><?php echo $usuarios->twitter->FldCaption() ?></span></td>
<?php } ?>
<?php if ($usuarios->url->Visible) { // url ?>
		<td><span id="elh_usuarios_url" class="usuarios_url"><?php echo $usuarios->url->FldCaption() ?></span></td>
<?php } ?>
<?php if ($usuarios->permisos->Visible) { // permisos ?>
		<td><span id="elh_usuarios_permisos" class="usuarios_permisos"><?php echo $usuarios->permisos->FldCaption() ?></span></td>
<?php } ?>
<?php if ($usuarios->status->Visible) { // status ?>
		<td><span id="elh_usuarios_status" class="usuarios_status"><?php echo $usuarios->status->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$usuarios_delete->RecCnt = 0;
$i = 0;
while (!$usuarios_delete->Recordset->EOF) {
	$usuarios_delete->RecCnt++;
	$usuarios_delete->RowCnt++;

	// Set row properties
	$usuarios->ResetAttrs();
	$usuarios->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$usuarios_delete->LoadRowValues($usuarios_delete->Recordset);

	// Render row
	$usuarios_delete->RenderRow();
?>
	<tr<?php echo $usuarios->RowAttributes() ?>>
<?php if ($usuarios->id->Visible) { // id ?>
		<td<?php echo $usuarios->id->CellAttributes() ?>>
<span id="el<?php echo $usuarios_delete->RowCnt ?>_usuarios_id" class="control-group usuarios_id">
<span<?php echo $usuarios->id->ViewAttributes() ?>>
<?php echo $usuarios->id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($usuarios->nombre->Visible) { // nombre ?>
		<td<?php echo $usuarios->nombre->CellAttributes() ?>>
<span id="el<?php echo $usuarios_delete->RowCnt ?>_usuarios_nombre" class="control-group usuarios_nombre">
<span<?php echo $usuarios->nombre->ViewAttributes() ?>>
<?php echo $usuarios->nombre->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($usuarios->_email->Visible) { // email ?>
		<td<?php echo $usuarios->_email->CellAttributes() ?>>
<span id="el<?php echo $usuarios_delete->RowCnt ?>_usuarios__email" class="control-group usuarios__email">
<span<?php echo $usuarios->_email->ViewAttributes() ?>>
<?php echo $usuarios->_email->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($usuarios->nombre_completo->Visible) { // nombre_completo ?>
		<td<?php echo $usuarios->nombre_completo->CellAttributes() ?>>
<span id="el<?php echo $usuarios_delete->RowCnt ?>_usuarios_nombre_completo" class="control-group usuarios_nombre_completo">
<span<?php echo $usuarios->nombre_completo->ViewAttributes() ?>>
<?php echo $usuarios->nombre_completo->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($usuarios->facebook->Visible) { // facebook ?>
		<td<?php echo $usuarios->facebook->CellAttributes() ?>>
<span id="el<?php echo $usuarios_delete->RowCnt ?>_usuarios_facebook" class="control-group usuarios_facebook">
<span<?php echo $usuarios->facebook->ViewAttributes() ?>>
<?php echo $usuarios->facebook->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($usuarios->twitter->Visible) { // twitter ?>
		<td<?php echo $usuarios->twitter->CellAttributes() ?>>
<span id="el<?php echo $usuarios_delete->RowCnt ?>_usuarios_twitter" class="control-group usuarios_twitter">
<span<?php echo $usuarios->twitter->ViewAttributes() ?>>
<?php echo $usuarios->twitter->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($usuarios->url->Visible) { // url ?>
		<td<?php echo $usuarios->url->CellAttributes() ?>>
<span id="el<?php echo $usuarios_delete->RowCnt ?>_usuarios_url" class="control-group usuarios_url">
<span<?php echo $usuarios->url->ViewAttributes() ?>>
<?php echo $usuarios->url->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($usuarios->permisos->Visible) { // permisos ?>
		<td<?php echo $usuarios->permisos->CellAttributes() ?>>
<span id="el<?php echo $usuarios_delete->RowCnt ?>_usuarios_permisos" class="control-group usuarios_permisos">
<span<?php echo $usuarios->permisos->ViewAttributes() ?>>
<?php echo $usuarios->permisos->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($usuarios->status->Visible) { // status ?>
		<td<?php echo $usuarios->status->CellAttributes() ?>>
<span id="el<?php echo $usuarios_delete->RowCnt ?>_usuarios_status" class="control-group usuarios_status">
<span<?php echo $usuarios->status->ViewAttributes() ?>>
<?php echo $usuarios->status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$usuarios_delete->Recordset->MoveNext();
}
$usuarios_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fusuariosdelete.Init();
</script>
<?php
$usuarios_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$usuarios_delete->Page_Terminate();
?>
