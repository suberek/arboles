<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "_2_especiesinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$p_2_especies_delete = NULL; // Initialize page object first

class cp_2_especies_delete extends c_2_especies {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Table name
	var $TableName = '2_especies';

	// Page object name
	var $PageObjName = 'p_2_especies_delete';

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

		// Table object (_2_especies)
		if (!isset($GLOBALS["_2_especies"])) {
			$GLOBALS["_2_especies"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["_2_especies"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", '2_especies', TRUE);

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
			$this->Page_Terminate("_2_especieslist.php");
		}

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
			$this->Page_Terminate("_2_especieslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in _2_especies class, _2_especiesinfo.php

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
		$this->id_especie->setDbValue($rs->fields('id_especie'));
		$this->id_familia->setDbValue($rs->fields('id_familia'));
		$this->NOMBRE_FAM->setDbValue($rs->fields('NOMBRE_FAM'));
		$this->NOMBRE_CIE->setDbValue($rs->fields('NOMBRE_CIE'));
		$this->NOMBRE_COM->setDbValue($rs->fields('NOMBRE_COM'));
		$this->TIPO_FOLLA->setDbValue($rs->fields('TIPO_FOLLA'));
		$this->ORIGEN->setDbValue($rs->fields('ORIGEN'));
		$this->ICONO->Upload->DbValue = $rs->fields('ICONO');
		$this->imagen_completo->Upload->DbValue = $rs->fields('imagen_completo');
		$this->imagen_hoja->Upload->DbValue = $rs->fields('imagen_hoja');
		$this->imagen_flor->Upload->DbValue = $rs->fields('imagen_flor');
		$this->descripcion->setDbValue($rs->fields('descripcion'));
		$this->medicinal->setDbValue($rs->fields('medicinal'));
		$this->comestible->setDbValue($rs->fields('comestible'));
		$this->perfume->setDbValue($rs->fields('perfume'));
		$this->avejas->setDbValue($rs->fields('avejas'));
		$this->mariposas->setDbValue($rs->fields('mariposas'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id_especie->DbValue = $row['id_especie'];
		$this->id_familia->DbValue = $row['id_familia'];
		$this->NOMBRE_FAM->DbValue = $row['NOMBRE_FAM'];
		$this->NOMBRE_CIE->DbValue = $row['NOMBRE_CIE'];
		$this->NOMBRE_COM->DbValue = $row['NOMBRE_COM'];
		$this->TIPO_FOLLA->DbValue = $row['TIPO_FOLLA'];
		$this->ORIGEN->DbValue = $row['ORIGEN'];
		$this->ICONO->Upload->DbValue = $row['ICONO'];
		$this->imagen_completo->Upload->DbValue = $row['imagen_completo'];
		$this->imagen_hoja->Upload->DbValue = $row['imagen_hoja'];
		$this->imagen_flor->Upload->DbValue = $row['imagen_flor'];
		$this->descripcion->DbValue = $row['descripcion'];
		$this->medicinal->DbValue = $row['medicinal'];
		$this->comestible->DbValue = $row['comestible'];
		$this->perfume->DbValue = $row['perfume'];
		$this->avejas->DbValue = $row['avejas'];
		$this->mariposas->DbValue = $row['mariposas'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// id_especie
		// id_familia

		$this->id_familia->CellCssStyle = "white-space: nowrap;";

		// NOMBRE_FAM
		// NOMBRE_CIE
		// NOMBRE_COM
		// TIPO_FOLLA
		// ORIGEN
		// ICONO
		// imagen_completo
		// imagen_hoja
		// imagen_flor
		// descripcion
		// medicinal
		// comestible
		// perfume
		// avejas
		// mariposas

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id_especie
			$this->id_especie->ViewValue = $this->id_especie->CurrentValue;
			$this->id_especie->ViewCustomAttributes = "";

			// NOMBRE_FAM
			$this->NOMBRE_FAM->ViewValue = $this->NOMBRE_FAM->CurrentValue;
			$this->NOMBRE_FAM->ViewCustomAttributes = "";

			// NOMBRE_CIE
			$this->NOMBRE_CIE->ViewValue = $this->NOMBRE_CIE->CurrentValue;
			$this->NOMBRE_CIE->ViewCustomAttributes = "";

			// NOMBRE_COM
			$this->NOMBRE_COM->ViewValue = $this->NOMBRE_COM->CurrentValue;
			$this->NOMBRE_COM->ViewCustomAttributes = "";

			// TIPO_FOLLA
			$this->TIPO_FOLLA->ViewValue = $this->TIPO_FOLLA->CurrentValue;
			$this->TIPO_FOLLA->ViewCustomAttributes = "";

			// ORIGEN
			$this->ORIGEN->ViewValue = $this->ORIGEN->CurrentValue;
			$this->ORIGEN->ViewCustomAttributes = "";

			// ICONO
			if (!ew_Empty($this->ICONO->Upload->DbValue)) {
				$this->ICONO->ImageAlt = $this->ICONO->FldAlt();
				$this->ICONO->ViewValue = ew_UploadPathEx(FALSE, $this->ICONO->UploadPath) . $this->ICONO->Upload->DbValue;
			} else {
				$this->ICONO->ViewValue = "";
			}
			$this->ICONO->ViewCustomAttributes = "";

			// imagen_completo
			if (!ew_Empty($this->imagen_completo->Upload->DbValue)) {
				$this->imagen_completo->ImageAlt = $this->imagen_completo->FldAlt();
				$this->imagen_completo->ViewValue = ew_UploadPathEx(FALSE, $this->imagen_completo->UploadPath) . $this->imagen_completo->Upload->DbValue;
			} else {
				$this->imagen_completo->ViewValue = "";
			}
			$this->imagen_completo->ViewCustomAttributes = "";

			// imagen_hoja
			if (!ew_Empty($this->imagen_hoja->Upload->DbValue)) {
				$this->imagen_hoja->ImageAlt = $this->imagen_hoja->FldAlt();
				$this->imagen_hoja->ViewValue = ew_UploadPathEx(FALSE, $this->imagen_hoja->UploadPath) . $this->imagen_hoja->Upload->DbValue;
			} else {
				$this->imagen_hoja->ViewValue = "";
			}
			$this->imagen_hoja->ViewCustomAttributes = "";

			// imagen_flor
			if (!ew_Empty($this->imagen_flor->Upload->DbValue)) {
				$this->imagen_flor->ImageAlt = $this->imagen_flor->FldAlt();
				$this->imagen_flor->ViewValue = ew_UploadPathEx(FALSE, $this->imagen_flor->UploadPath) . $this->imagen_flor->Upload->DbValue;
			} else {
				$this->imagen_flor->ViewValue = "";
			}
			$this->imagen_flor->ViewCustomAttributes = "";

			// medicinal
			$this->medicinal->ViewValue = $this->medicinal->CurrentValue;
			$this->medicinal->ViewCustomAttributes = "";

			// comestible
			$this->comestible->ViewValue = $this->comestible->CurrentValue;
			$this->comestible->ViewCustomAttributes = "";

			// perfume
			$this->perfume->ViewValue = $this->perfume->CurrentValue;
			$this->perfume->ViewCustomAttributes = "";

			// avejas
			$this->avejas->ViewValue = $this->avejas->CurrentValue;
			$this->avejas->ViewCustomAttributes = "";

			// mariposas
			$this->mariposas->ViewValue = $this->mariposas->CurrentValue;
			$this->mariposas->ViewCustomAttributes = "";

			// id_especie
			$this->id_especie->LinkCustomAttributes = "";
			$this->id_especie->HrefValue = "";
			$this->id_especie->TooltipValue = "";

			// NOMBRE_FAM
			$this->NOMBRE_FAM->LinkCustomAttributes = "";
			$this->NOMBRE_FAM->HrefValue = "";
			$this->NOMBRE_FAM->TooltipValue = "";

			// NOMBRE_CIE
			$this->NOMBRE_CIE->LinkCustomAttributes = "";
			$this->NOMBRE_CIE->HrefValue = "";
			$this->NOMBRE_CIE->TooltipValue = "";

			// NOMBRE_COM
			$this->NOMBRE_COM->LinkCustomAttributes = "";
			$this->NOMBRE_COM->HrefValue = "";
			$this->NOMBRE_COM->TooltipValue = "";

			// TIPO_FOLLA
			$this->TIPO_FOLLA->LinkCustomAttributes = "";
			$this->TIPO_FOLLA->HrefValue = "";
			$this->TIPO_FOLLA->TooltipValue = "";

			// ORIGEN
			$this->ORIGEN->LinkCustomAttributes = "";
			$this->ORIGEN->HrefValue = "";
			$this->ORIGEN->TooltipValue = "";

			// ICONO
			$this->ICONO->LinkCustomAttributes = "";
			$this->ICONO->HrefValue = "";
			$this->ICONO->HrefValue2 = $this->ICONO->UploadPath . $this->ICONO->Upload->DbValue;
			$this->ICONO->TooltipValue = "";

			// imagen_completo
			$this->imagen_completo->LinkCustomAttributes = "";
			$this->imagen_completo->HrefValue = "";
			$this->imagen_completo->HrefValue2 = $this->imagen_completo->UploadPath . $this->imagen_completo->Upload->DbValue;
			$this->imagen_completo->TooltipValue = "";

			// imagen_hoja
			$this->imagen_hoja->LinkCustomAttributes = "";
			$this->imagen_hoja->HrefValue = "";
			$this->imagen_hoja->HrefValue2 = $this->imagen_hoja->UploadPath . $this->imagen_hoja->Upload->DbValue;
			$this->imagen_hoja->TooltipValue = "";

			// imagen_flor
			$this->imagen_flor->LinkCustomAttributes = "";
			$this->imagen_flor->HrefValue = "";
			$this->imagen_flor->HrefValue2 = $this->imagen_flor->UploadPath . $this->imagen_flor->Upload->DbValue;
			$this->imagen_flor->TooltipValue = "";

			// medicinal
			$this->medicinal->LinkCustomAttributes = "";
			$this->medicinal->HrefValue = "";
			$this->medicinal->TooltipValue = "";

			// comestible
			$this->comestible->LinkCustomAttributes = "";
			$this->comestible->HrefValue = "";
			$this->comestible->TooltipValue = "";

			// perfume
			$this->perfume->LinkCustomAttributes = "";
			$this->perfume->HrefValue = "";
			$this->perfume->TooltipValue = "";

			// avejas
			$this->avejas->LinkCustomAttributes = "";
			$this->avejas->HrefValue = "";
			$this->avejas->TooltipValue = "";

			// mariposas
			$this->mariposas->LinkCustomAttributes = "";
			$this->mariposas->HrefValue = "";
			$this->mariposas->TooltipValue = "";
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
				$sThisKey .= $row['id_especie'];
				$this->LoadDbValues($row);
				@unlink(ew_UploadPathEx(TRUE, $this->ICONO->OldUploadPath) . $row['ICONO']);
				@unlink(ew_UploadPathEx(TRUE, $this->imagen_completo->OldUploadPath) . $row['imagen_completo']);
				@unlink(ew_UploadPathEx(TRUE, $this->imagen_hoja->OldUploadPath) . $row['imagen_hoja']);
				@unlink(ew_UploadPathEx(TRUE, $this->imagen_flor->OldUploadPath) . $row['imagen_flor']);
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "_2_especieslist.php", $this->TableVar);
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
if (!isset($p_2_especies_delete)) $p_2_especies_delete = new cp_2_especies_delete();

// Page init
$p_2_especies_delete->Page_Init();

// Page main
$p_2_especies_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$p_2_especies_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var p_2_especies_delete = new ew_Page("p_2_especies_delete");
p_2_especies_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = p_2_especies_delete.PageID; // For backward compatibility

// Form object
var f_2_especiesdelete = new ew_Form("f_2_especiesdelete");

// Form_CustomValidate event
f_2_especiesdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
f_2_especiesdelete.ValidateRequired = true;
<?php } else { ?>
f_2_especiesdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($p_2_especies_delete->Recordset = $p_2_especies_delete->LoadRecordset())
	$p_2_especies_deleteTotalRecs = $p_2_especies_delete->Recordset->RecordCount(); // Get record count
if ($p_2_especies_deleteTotalRecs <= 0) { // No record found, exit
	if ($p_2_especies_delete->Recordset)
		$p_2_especies_delete->Recordset->Close();
	$p_2_especies_delete->Page_Terminate("_2_especieslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $p_2_especies_delete->ShowPageHeader(); ?>
<?php
$p_2_especies_delete->ShowMessage();
?>
<form name="f_2_especiesdelete" id="f_2_especiesdelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="_2_especies">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($p_2_especies_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl__2_especiesdelete" class="ewTable ewTableSeparate">
<?php echo $_2_especies->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($_2_especies->id_especie->Visible) { // id_especie ?>
		<td><span id="elh__2_especies_id_especie" class="_2_especies_id_especie"><?php echo $_2_especies->id_especie->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->NOMBRE_FAM->Visible) { // NOMBRE_FAM ?>
		<td><span id="elh__2_especies_NOMBRE_FAM" class="_2_especies_NOMBRE_FAM"><?php echo $_2_especies->NOMBRE_FAM->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->NOMBRE_CIE->Visible) { // NOMBRE_CIE ?>
		<td><span id="elh__2_especies_NOMBRE_CIE" class="_2_especies_NOMBRE_CIE"><?php echo $_2_especies->NOMBRE_CIE->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->NOMBRE_COM->Visible) { // NOMBRE_COM ?>
		<td><span id="elh__2_especies_NOMBRE_COM" class="_2_especies_NOMBRE_COM"><?php echo $_2_especies->NOMBRE_COM->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->TIPO_FOLLA->Visible) { // TIPO_FOLLA ?>
		<td><span id="elh__2_especies_TIPO_FOLLA" class="_2_especies_TIPO_FOLLA"><?php echo $_2_especies->TIPO_FOLLA->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->ORIGEN->Visible) { // ORIGEN ?>
		<td><span id="elh__2_especies_ORIGEN" class="_2_especies_ORIGEN"><?php echo $_2_especies->ORIGEN->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->ICONO->Visible) { // ICONO ?>
		<td><span id="elh__2_especies_ICONO" class="_2_especies_ICONO"><?php echo $_2_especies->ICONO->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->imagen_completo->Visible) { // imagen_completo ?>
		<td><span id="elh__2_especies_imagen_completo" class="_2_especies_imagen_completo"><?php echo $_2_especies->imagen_completo->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->imagen_hoja->Visible) { // imagen_hoja ?>
		<td><span id="elh__2_especies_imagen_hoja" class="_2_especies_imagen_hoja"><?php echo $_2_especies->imagen_hoja->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->imagen_flor->Visible) { // imagen_flor ?>
		<td><span id="elh__2_especies_imagen_flor" class="_2_especies_imagen_flor"><?php echo $_2_especies->imagen_flor->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->medicinal->Visible) { // medicinal ?>
		<td><span id="elh__2_especies_medicinal" class="_2_especies_medicinal"><?php echo $_2_especies->medicinal->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->comestible->Visible) { // comestible ?>
		<td><span id="elh__2_especies_comestible" class="_2_especies_comestible"><?php echo $_2_especies->comestible->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->perfume->Visible) { // perfume ?>
		<td><span id="elh__2_especies_perfume" class="_2_especies_perfume"><?php echo $_2_especies->perfume->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->avejas->Visible) { // avejas ?>
		<td><span id="elh__2_especies_avejas" class="_2_especies_avejas"><?php echo $_2_especies->avejas->FldCaption() ?></span></td>
<?php } ?>
<?php if ($_2_especies->mariposas->Visible) { // mariposas ?>
		<td><span id="elh__2_especies_mariposas" class="_2_especies_mariposas"><?php echo $_2_especies->mariposas->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$p_2_especies_delete->RecCnt = 0;
$i = 0;
while (!$p_2_especies_delete->Recordset->EOF) {
	$p_2_especies_delete->RecCnt++;
	$p_2_especies_delete->RowCnt++;

	// Set row properties
	$_2_especies->ResetAttrs();
	$_2_especies->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$p_2_especies_delete->LoadRowValues($p_2_especies_delete->Recordset);

	// Render row
	$p_2_especies_delete->RenderRow();
?>
	<tr<?php echo $_2_especies->RowAttributes() ?>>
<?php if ($_2_especies->id_especie->Visible) { // id_especie ?>
		<td<?php echo $_2_especies->id_especie->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_id_especie" class="control-group _2_especies_id_especie">
<span<?php echo $_2_especies->id_especie->ViewAttributes() ?>>
<?php echo $_2_especies->id_especie->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->NOMBRE_FAM->Visible) { // NOMBRE_FAM ?>
		<td<?php echo $_2_especies->NOMBRE_FAM->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_NOMBRE_FAM" class="control-group _2_especies_NOMBRE_FAM">
<span<?php echo $_2_especies->NOMBRE_FAM->ViewAttributes() ?>>
<?php echo $_2_especies->NOMBRE_FAM->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->NOMBRE_CIE->Visible) { // NOMBRE_CIE ?>
		<td<?php echo $_2_especies->NOMBRE_CIE->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_NOMBRE_CIE" class="control-group _2_especies_NOMBRE_CIE">
<span<?php echo $_2_especies->NOMBRE_CIE->ViewAttributes() ?>>
<?php echo $_2_especies->NOMBRE_CIE->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->NOMBRE_COM->Visible) { // NOMBRE_COM ?>
		<td<?php echo $_2_especies->NOMBRE_COM->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_NOMBRE_COM" class="control-group _2_especies_NOMBRE_COM">
<span<?php echo $_2_especies->NOMBRE_COM->ViewAttributes() ?>>
<?php echo $_2_especies->NOMBRE_COM->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->TIPO_FOLLA->Visible) { // TIPO_FOLLA ?>
		<td<?php echo $_2_especies->TIPO_FOLLA->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_TIPO_FOLLA" class="control-group _2_especies_TIPO_FOLLA">
<span<?php echo $_2_especies->TIPO_FOLLA->ViewAttributes() ?>>
<?php echo $_2_especies->TIPO_FOLLA->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->ORIGEN->Visible) { // ORIGEN ?>
		<td<?php echo $_2_especies->ORIGEN->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_ORIGEN" class="control-group _2_especies_ORIGEN">
<span<?php echo $_2_especies->ORIGEN->ViewAttributes() ?>>
<?php echo $_2_especies->ORIGEN->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->ICONO->Visible) { // ICONO ?>
		<td<?php echo $_2_especies->ICONO->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_ICONO" class="control-group _2_especies_ICONO">
<span>
<?php if ($_2_especies->ICONO->LinkAttributes() <> "") { ?>
<?php if (!empty($_2_especies->ICONO->Upload->DbValue)) { ?>
<img src="<?php echo $_2_especies->ICONO->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $_2_especies->ICONO->ViewAttributes() ?>>
<?php } elseif (!in_array($_2_especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($_2_especies->ICONO->Upload->DbValue)) { ?>
<img src="<?php echo $_2_especies->ICONO->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $_2_especies->ICONO->ViewAttributes() ?>>
<?php } elseif (!in_array($_2_especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->imagen_completo->Visible) { // imagen_completo ?>
		<td<?php echo $_2_especies->imagen_completo->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_imagen_completo" class="control-group _2_especies_imagen_completo">
<span>
<?php if ($_2_especies->imagen_completo->LinkAttributes() <> "") { ?>
<?php if (!empty($_2_especies->imagen_completo->Upload->DbValue)) { ?>
<img src="<?php echo $_2_especies->imagen_completo->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $_2_especies->imagen_completo->ViewAttributes() ?>>
<?php } elseif (!in_array($_2_especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($_2_especies->imagen_completo->Upload->DbValue)) { ?>
<img src="<?php echo $_2_especies->imagen_completo->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $_2_especies->imagen_completo->ViewAttributes() ?>>
<?php } elseif (!in_array($_2_especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->imagen_hoja->Visible) { // imagen_hoja ?>
		<td<?php echo $_2_especies->imagen_hoja->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_imagen_hoja" class="control-group _2_especies_imagen_hoja">
<span>
<?php if ($_2_especies->imagen_hoja->LinkAttributes() <> "") { ?>
<?php if (!empty($_2_especies->imagen_hoja->Upload->DbValue)) { ?>
<img src="<?php echo $_2_especies->imagen_hoja->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $_2_especies->imagen_hoja->ViewAttributes() ?>>
<?php } elseif (!in_array($_2_especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($_2_especies->imagen_hoja->Upload->DbValue)) { ?>
<img src="<?php echo $_2_especies->imagen_hoja->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $_2_especies->imagen_hoja->ViewAttributes() ?>>
<?php } elseif (!in_array($_2_especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->imagen_flor->Visible) { // imagen_flor ?>
		<td<?php echo $_2_especies->imagen_flor->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_imagen_flor" class="control-group _2_especies_imagen_flor">
<span>
<?php if ($_2_especies->imagen_flor->LinkAttributes() <> "") { ?>
<?php if (!empty($_2_especies->imagen_flor->Upload->DbValue)) { ?>
<img src="<?php echo $_2_especies->imagen_flor->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $_2_especies->imagen_flor->ViewAttributes() ?>>
<?php } elseif (!in_array($_2_especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($_2_especies->imagen_flor->Upload->DbValue)) { ?>
<img src="<?php echo $_2_especies->imagen_flor->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $_2_especies->imagen_flor->ViewAttributes() ?>>
<?php } elseif (!in_array($_2_especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->medicinal->Visible) { // medicinal ?>
		<td<?php echo $_2_especies->medicinal->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_medicinal" class="control-group _2_especies_medicinal">
<span<?php echo $_2_especies->medicinal->ViewAttributes() ?>>
<?php echo $_2_especies->medicinal->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->comestible->Visible) { // comestible ?>
		<td<?php echo $_2_especies->comestible->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_comestible" class="control-group _2_especies_comestible">
<span<?php echo $_2_especies->comestible->ViewAttributes() ?>>
<?php echo $_2_especies->comestible->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->perfume->Visible) { // perfume ?>
		<td<?php echo $_2_especies->perfume->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_perfume" class="control-group _2_especies_perfume">
<span<?php echo $_2_especies->perfume->ViewAttributes() ?>>
<?php echo $_2_especies->perfume->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->avejas->Visible) { // avejas ?>
		<td<?php echo $_2_especies->avejas->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_avejas" class="control-group _2_especies_avejas">
<span<?php echo $_2_especies->avejas->ViewAttributes() ?>>
<?php echo $_2_especies->avejas->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($_2_especies->mariposas->Visible) { // mariposas ?>
		<td<?php echo $_2_especies->mariposas->CellAttributes() ?>>
<span id="el<?php echo $p_2_especies_delete->RowCnt ?>__2_especies_mariposas" class="control-group _2_especies_mariposas">
<span<?php echo $_2_especies->mariposas->ViewAttributes() ?>>
<?php echo $_2_especies->mariposas->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$p_2_especies_delete->Recordset->MoveNext();
}
$p_2_especies_delete->Recordset->Close();
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
f_2_especiesdelete.Init();
</script>
<?php
$p_2_especies_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$p_2_especies_delete->Page_Terminate();
?>
