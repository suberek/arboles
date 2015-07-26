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

$p_2_especies_edit = NULL; // Initialize page object first

class cp_2_especies_edit extends c_2_especies {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Table name
	var $TableName = '2_especies';

	// Page object name
	var $PageObjName = 'p_2_especies_edit';

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
			define("EW_PAGE_ID", 'edit', TRUE);

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
		if (!$Security->CanEdit()) {
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

		// Create form object
		$objForm = new cFormObj();
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
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["id_especie"] <> "") {
			$this->id_especie->setQueryStringValue($_GET["id_especie"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->id_especie->CurrentValue == "")
			$this->Page_Terminate("_2_especieslist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("_2_especieslist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
		$this->ICONO->Upload->Index = $objForm->Index;
		if ($this->ICONO->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->ICONO->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->ICONO->CurrentValue = $this->ICONO->Upload->FileName;
		$this->imagen_completo->Upload->Index = $objForm->Index;
		if ($this->imagen_completo->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->imagen_completo->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->imagen_completo->CurrentValue = $this->imagen_completo->Upload->FileName;
		$this->imagen_hoja->Upload->Index = $objForm->Index;
		if ($this->imagen_hoja->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->imagen_hoja->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->imagen_hoja->CurrentValue = $this->imagen_hoja->Upload->FileName;
		$this->imagen_flor->Upload->Index = $objForm->Index;
		if ($this->imagen_flor->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->imagen_flor->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->imagen_flor->CurrentValue = $this->imagen_flor->Upload->FileName;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->id_especie->FldIsDetailKey) {
			$this->id_especie->setFormValue($objForm->GetValue("x_id_especie"));
		}
		if (!$this->NOMBRE_FAM->FldIsDetailKey) {
			$this->NOMBRE_FAM->setFormValue($objForm->GetValue("x_NOMBRE_FAM"));
		}
		if (!$this->NOMBRE_CIE->FldIsDetailKey) {
			$this->NOMBRE_CIE->setFormValue($objForm->GetValue("x_NOMBRE_CIE"));
		}
		if (!$this->NOMBRE_COM->FldIsDetailKey) {
			$this->NOMBRE_COM->setFormValue($objForm->GetValue("x_NOMBRE_COM"));
		}
		if (!$this->TIPO_FOLLA->FldIsDetailKey) {
			$this->TIPO_FOLLA->setFormValue($objForm->GetValue("x_TIPO_FOLLA"));
		}
		if (!$this->ORIGEN->FldIsDetailKey) {
			$this->ORIGEN->setFormValue($objForm->GetValue("x_ORIGEN"));
		}
		if (!$this->descripcion->FldIsDetailKey) {
			$this->descripcion->setFormValue($objForm->GetValue("x_descripcion"));
		}
		if (!$this->medicinal->FldIsDetailKey) {
			$this->medicinal->setFormValue($objForm->GetValue("x_medicinal"));
		}
		if (!$this->comestible->FldIsDetailKey) {
			$this->comestible->setFormValue($objForm->GetValue("x_comestible"));
		}
		if (!$this->perfume->FldIsDetailKey) {
			$this->perfume->setFormValue($objForm->GetValue("x_perfume"));
		}
		if (!$this->avejas->FldIsDetailKey) {
			$this->avejas->setFormValue($objForm->GetValue("x_avejas"));
		}
		if (!$this->mariposas->FldIsDetailKey) {
			$this->mariposas->setFormValue($objForm->GetValue("x_mariposas"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->id_especie->CurrentValue = $this->id_especie->FormValue;
		$this->NOMBRE_FAM->CurrentValue = $this->NOMBRE_FAM->FormValue;
		$this->NOMBRE_CIE->CurrentValue = $this->NOMBRE_CIE->FormValue;
		$this->NOMBRE_COM->CurrentValue = $this->NOMBRE_COM->FormValue;
		$this->TIPO_FOLLA->CurrentValue = $this->TIPO_FOLLA->FormValue;
		$this->ORIGEN->CurrentValue = $this->ORIGEN->FormValue;
		$this->descripcion->CurrentValue = $this->descripcion->FormValue;
		$this->medicinal->CurrentValue = $this->medicinal->FormValue;
		$this->comestible->CurrentValue = $this->comestible->FormValue;
		$this->perfume->CurrentValue = $this->perfume->FormValue;
		$this->avejas->CurrentValue = $this->avejas->FormValue;
		$this->mariposas->CurrentValue = $this->mariposas->FormValue;
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

			// descripcion
			$this->descripcion->ViewValue = $this->descripcion->CurrentValue;
			$this->descripcion->ViewCustomAttributes = "";

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

			// descripcion
			$this->descripcion->LinkCustomAttributes = "";
			$this->descripcion->HrefValue = "";
			$this->descripcion->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// id_especie
			$this->id_especie->EditCustomAttributes = "";
			$this->id_especie->EditValue = $this->id_especie->CurrentValue;
			$this->id_especie->ViewCustomAttributes = "";

			// NOMBRE_FAM
			$this->NOMBRE_FAM->EditCustomAttributes = "";
			$this->NOMBRE_FAM->EditValue = ew_HtmlEncode($this->NOMBRE_FAM->CurrentValue);
			$this->NOMBRE_FAM->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->NOMBRE_FAM->FldCaption()));

			// NOMBRE_CIE
			$this->NOMBRE_CIE->EditCustomAttributes = "";
			$this->NOMBRE_CIE->EditValue = ew_HtmlEncode($this->NOMBRE_CIE->CurrentValue);
			$this->NOMBRE_CIE->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->NOMBRE_CIE->FldCaption()));

			// NOMBRE_COM
			$this->NOMBRE_COM->EditCustomAttributes = "";
			$this->NOMBRE_COM->EditValue = ew_HtmlEncode($this->NOMBRE_COM->CurrentValue);
			$this->NOMBRE_COM->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->NOMBRE_COM->FldCaption()));

			// TIPO_FOLLA
			$this->TIPO_FOLLA->EditCustomAttributes = "";
			$this->TIPO_FOLLA->EditValue = ew_HtmlEncode($this->TIPO_FOLLA->CurrentValue);
			$this->TIPO_FOLLA->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->TIPO_FOLLA->FldCaption()));

			// ORIGEN
			$this->ORIGEN->EditCustomAttributes = "";
			$this->ORIGEN->EditValue = ew_HtmlEncode($this->ORIGEN->CurrentValue);
			$this->ORIGEN->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->ORIGEN->FldCaption()));

			// ICONO
			$this->ICONO->EditCustomAttributes = "";
			if (!ew_Empty($this->ICONO->Upload->DbValue)) {
				$this->ICONO->ImageAlt = $this->ICONO->FldAlt();
				$this->ICONO->EditValue = ew_UploadPathEx(FALSE, $this->ICONO->UploadPath) . $this->ICONO->Upload->DbValue;
			} else {
				$this->ICONO->EditValue = "";
			}
			if (!ew_Empty($this->ICONO->CurrentValue))
				$this->ICONO->Upload->FileName = $this->ICONO->CurrentValue;
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->ICONO);

			// imagen_completo
			$this->imagen_completo->EditCustomAttributes = "";
			if (!ew_Empty($this->imagen_completo->Upload->DbValue)) {
				$this->imagen_completo->ImageAlt = $this->imagen_completo->FldAlt();
				$this->imagen_completo->EditValue = ew_UploadPathEx(FALSE, $this->imagen_completo->UploadPath) . $this->imagen_completo->Upload->DbValue;
			} else {
				$this->imagen_completo->EditValue = "";
			}
			if (!ew_Empty($this->imagen_completo->CurrentValue))
				$this->imagen_completo->Upload->FileName = $this->imagen_completo->CurrentValue;
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->imagen_completo);

			// imagen_hoja
			$this->imagen_hoja->EditCustomAttributes = "";
			if (!ew_Empty($this->imagen_hoja->Upload->DbValue)) {
				$this->imagen_hoja->ImageAlt = $this->imagen_hoja->FldAlt();
				$this->imagen_hoja->EditValue = ew_UploadPathEx(FALSE, $this->imagen_hoja->UploadPath) . $this->imagen_hoja->Upload->DbValue;
			} else {
				$this->imagen_hoja->EditValue = "";
			}
			if (!ew_Empty($this->imagen_hoja->CurrentValue))
				$this->imagen_hoja->Upload->FileName = $this->imagen_hoja->CurrentValue;
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->imagen_hoja);

			// imagen_flor
			$this->imagen_flor->EditCustomAttributes = "";
			if (!ew_Empty($this->imagen_flor->Upload->DbValue)) {
				$this->imagen_flor->ImageAlt = $this->imagen_flor->FldAlt();
				$this->imagen_flor->EditValue = ew_UploadPathEx(FALSE, $this->imagen_flor->UploadPath) . $this->imagen_flor->Upload->DbValue;
			} else {
				$this->imagen_flor->EditValue = "";
			}
			if (!ew_Empty($this->imagen_flor->CurrentValue))
				$this->imagen_flor->Upload->FileName = $this->imagen_flor->CurrentValue;
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->imagen_flor);

			// descripcion
			$this->descripcion->EditCustomAttributes = "";
			$this->descripcion->EditValue = $this->descripcion->CurrentValue;
			$this->descripcion->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->descripcion->FldCaption()));

			// medicinal
			$this->medicinal->EditCustomAttributes = "";
			$this->medicinal->EditValue = $this->medicinal->CurrentValue;
			$this->medicinal->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->medicinal->FldCaption()));

			// comestible
			$this->comestible->EditCustomAttributes = "";
			$this->comestible->EditValue = $this->comestible->CurrentValue;
			$this->comestible->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->comestible->FldCaption()));

			// perfume
			$this->perfume->EditCustomAttributes = "";
			$this->perfume->EditValue = ew_HtmlEncode($this->perfume->CurrentValue);
			$this->perfume->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->perfume->FldCaption()));

			// avejas
			$this->avejas->EditCustomAttributes = "";
			$this->avejas->EditValue = ew_HtmlEncode($this->avejas->CurrentValue);
			$this->avejas->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->avejas->FldCaption()));

			// mariposas
			$this->mariposas->EditCustomAttributes = "";
			$this->mariposas->EditValue = ew_HtmlEncode($this->mariposas->CurrentValue);
			$this->mariposas->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->mariposas->FldCaption()));

			// Edit refer script
			// id_especie

			$this->id_especie->HrefValue = "";

			// NOMBRE_FAM
			$this->NOMBRE_FAM->HrefValue = "";

			// NOMBRE_CIE
			$this->NOMBRE_CIE->HrefValue = "";

			// NOMBRE_COM
			$this->NOMBRE_COM->HrefValue = "";

			// TIPO_FOLLA
			$this->TIPO_FOLLA->HrefValue = "";

			// ORIGEN
			$this->ORIGEN->HrefValue = "";

			// ICONO
			$this->ICONO->HrefValue = "";
			$this->ICONO->HrefValue2 = $this->ICONO->UploadPath . $this->ICONO->Upload->DbValue;

			// imagen_completo
			$this->imagen_completo->HrefValue = "";
			$this->imagen_completo->HrefValue2 = $this->imagen_completo->UploadPath . $this->imagen_completo->Upload->DbValue;

			// imagen_hoja
			$this->imagen_hoja->HrefValue = "";
			$this->imagen_hoja->HrefValue2 = $this->imagen_hoja->UploadPath . $this->imagen_hoja->Upload->DbValue;

			// imagen_flor
			$this->imagen_flor->HrefValue = "";
			$this->imagen_flor->HrefValue2 = $this->imagen_flor->UploadPath . $this->imagen_flor->Upload->DbValue;

			// descripcion
			$this->descripcion->HrefValue = "";

			// medicinal
			$this->medicinal->HrefValue = "";

			// comestible
			$this->comestible->HrefValue = "";

			// perfume
			$this->perfume->HrefValue = "";

			// avejas
			$this->avejas->HrefValue = "";

			// mariposas
			$this->mariposas->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->id_especie->FldIsDetailKey && !is_null($this->id_especie->FormValue) && $this->id_especie->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->id_especie->FldCaption());
		}
		if (!ew_CheckInteger($this->id_especie->FormValue)) {
			ew_AddMessage($gsFormError, $this->id_especie->FldErrMsg());
		}
		if (!$this->NOMBRE_FAM->FldIsDetailKey && !is_null($this->NOMBRE_FAM->FormValue) && $this->NOMBRE_FAM->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->NOMBRE_FAM->FldCaption());
		}
		if (!$this->NOMBRE_CIE->FldIsDetailKey && !is_null($this->NOMBRE_CIE->FormValue) && $this->NOMBRE_CIE->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->NOMBRE_CIE->FldCaption());
		}
		if (!$this->NOMBRE_COM->FldIsDetailKey && !is_null($this->NOMBRE_COM->FormValue) && $this->NOMBRE_COM->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->NOMBRE_COM->FldCaption());
		}
		if (!$this->TIPO_FOLLA->FldIsDetailKey && !is_null($this->TIPO_FOLLA->FormValue) && $this->TIPO_FOLLA->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->TIPO_FOLLA->FldCaption());
		}
		if (!$this->ORIGEN->FldIsDetailKey && !is_null($this->ORIGEN->FormValue) && $this->ORIGEN->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->ORIGEN->FldCaption());
		}
		if (!ew_CheckInteger($this->perfume->FormValue)) {
			ew_AddMessage($gsFormError, $this->perfume->FldErrMsg());
		}
		if (!ew_CheckInteger($this->avejas->FormValue)) {
			ew_AddMessage($gsFormError, $this->avejas->FldErrMsg());
		}
		if (!ew_CheckInteger($this->mariposas->FormValue)) {
			ew_AddMessage($gsFormError, $this->mariposas->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// id_especie
			// NOMBRE_FAM

			$this->NOMBRE_FAM->SetDbValueDef($rsnew, $this->NOMBRE_FAM->CurrentValue, "", $this->NOMBRE_FAM->ReadOnly);

			// NOMBRE_CIE
			$this->NOMBRE_CIE->SetDbValueDef($rsnew, $this->NOMBRE_CIE->CurrentValue, "", $this->NOMBRE_CIE->ReadOnly);

			// NOMBRE_COM
			$this->NOMBRE_COM->SetDbValueDef($rsnew, $this->NOMBRE_COM->CurrentValue, "", $this->NOMBRE_COM->ReadOnly);

			// TIPO_FOLLA
			$this->TIPO_FOLLA->SetDbValueDef($rsnew, $this->TIPO_FOLLA->CurrentValue, "", $this->TIPO_FOLLA->ReadOnly);

			// ORIGEN
			$this->ORIGEN->SetDbValueDef($rsnew, $this->ORIGEN->CurrentValue, "", $this->ORIGEN->ReadOnly);

			// ICONO
			if (!($this->ICONO->ReadOnly) && !$this->ICONO->Upload->KeepFile) {
				$this->ICONO->Upload->DbValue = $rs->fields('ICONO'); // Get original value
				if ($this->ICONO->Upload->FileName == "") {
					$rsnew['ICONO'] = NULL;
				} else {
					$rsnew['ICONO'] = $this->ICONO->Upload->FileName;
				}
			}

			// imagen_completo
			if (!($this->imagen_completo->ReadOnly) && !$this->imagen_completo->Upload->KeepFile) {
				$this->imagen_completo->Upload->DbValue = $rs->fields('imagen_completo'); // Get original value
				if ($this->imagen_completo->Upload->FileName == "") {
					$rsnew['imagen_completo'] = NULL;
				} else {
					$rsnew['imagen_completo'] = $this->imagen_completo->Upload->FileName;
				}
			}

			// imagen_hoja
			if (!($this->imagen_hoja->ReadOnly) && !$this->imagen_hoja->Upload->KeepFile) {
				$this->imagen_hoja->Upload->DbValue = $rs->fields('imagen_hoja'); // Get original value
				if ($this->imagen_hoja->Upload->FileName == "") {
					$rsnew['imagen_hoja'] = NULL;
				} else {
					$rsnew['imagen_hoja'] = $this->imagen_hoja->Upload->FileName;
				}
			}

			// imagen_flor
			if (!($this->imagen_flor->ReadOnly) && !$this->imagen_flor->Upload->KeepFile) {
				$this->imagen_flor->Upload->DbValue = $rs->fields('imagen_flor'); // Get original value
				if ($this->imagen_flor->Upload->FileName == "") {
					$rsnew['imagen_flor'] = NULL;
				} else {
					$rsnew['imagen_flor'] = $this->imagen_flor->Upload->FileName;
				}
			}

			// descripcion
			$this->descripcion->SetDbValueDef($rsnew, $this->descripcion->CurrentValue, NULL, $this->descripcion->ReadOnly);

			// medicinal
			$this->medicinal->SetDbValueDef($rsnew, $this->medicinal->CurrentValue, NULL, $this->medicinal->ReadOnly);

			// comestible
			$this->comestible->SetDbValueDef($rsnew, $this->comestible->CurrentValue, NULL, $this->comestible->ReadOnly);

			// perfume
			$this->perfume->SetDbValueDef($rsnew, $this->perfume->CurrentValue, NULL, $this->perfume->ReadOnly);

			// avejas
			$this->avejas->SetDbValueDef($rsnew, $this->avejas->CurrentValue, NULL, $this->avejas->ReadOnly);

			// mariposas
			$this->mariposas->SetDbValueDef($rsnew, $this->mariposas->CurrentValue, NULL, $this->mariposas->ReadOnly);
			if (!$this->ICONO->Upload->KeepFile) {
				if (!ew_Empty($this->ICONO->Upload->Value)) {
					if ($this->ICONO->Upload->FileName == $this->ICONO->Upload->DbValue) { // Overwrite if same file name
						$this->ICONO->Upload->DbValue = ""; // No need to delete any more
					} else {
						$rsnew['ICONO'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->ICONO->UploadPath), $rsnew['ICONO']); // Get new file name
					}
				}
			}
			if (!$this->imagen_completo->Upload->KeepFile) {
				if (!ew_Empty($this->imagen_completo->Upload->Value)) {
					if ($this->imagen_completo->Upload->FileName == $this->imagen_completo->Upload->DbValue) { // Overwrite if same file name
						$this->imagen_completo->Upload->DbValue = ""; // No need to delete any more
					} else {
						$rsnew['imagen_completo'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->imagen_completo->UploadPath), $rsnew['imagen_completo']); // Get new file name
					}
				}
			}
			if (!$this->imagen_hoja->Upload->KeepFile) {
				if (!ew_Empty($this->imagen_hoja->Upload->Value)) {
					if ($this->imagen_hoja->Upload->FileName == $this->imagen_hoja->Upload->DbValue) { // Overwrite if same file name
						$this->imagen_hoja->Upload->DbValue = ""; // No need to delete any more
					} else {
						$rsnew['imagen_hoja'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->imagen_hoja->UploadPath), $rsnew['imagen_hoja']); // Get new file name
					}
				}
			}
			if (!$this->imagen_flor->Upload->KeepFile) {
				if (!ew_Empty($this->imagen_flor->Upload->Value)) {
					if ($this->imagen_flor->Upload->FileName == $this->imagen_flor->Upload->DbValue) { // Overwrite if same file name
						$this->imagen_flor->Upload->DbValue = ""; // No need to delete any more
					} else {
						$rsnew['imagen_flor'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->imagen_flor->UploadPath), $rsnew['imagen_flor']); // Get new file name
					}
				}
			}

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = 'ew_ErrorFn';
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
					if (!$this->ICONO->Upload->KeepFile) {
						if (!ew_Empty($this->ICONO->Upload->Value)) {
							$this->ICONO->Upload->SaveToFile($this->ICONO->UploadPath, $rsnew['ICONO'], TRUE);
						}
						if ($this->ICONO->Upload->DbValue <> "")
							@unlink(ew_UploadPathEx(TRUE, $this->ICONO->OldUploadPath) . $this->ICONO->Upload->DbValue);
					}
					if (!$this->imagen_completo->Upload->KeepFile) {
						if (!ew_Empty($this->imagen_completo->Upload->Value)) {
							$this->imagen_completo->Upload->SaveToFile($this->imagen_completo->UploadPath, $rsnew['imagen_completo'], TRUE);
						}
						if ($this->imagen_completo->Upload->DbValue <> "")
							@unlink(ew_UploadPathEx(TRUE, $this->imagen_completo->OldUploadPath) . $this->imagen_completo->Upload->DbValue);
					}
					if (!$this->imagen_hoja->Upload->KeepFile) {
						if (!ew_Empty($this->imagen_hoja->Upload->Value)) {
							$this->imagen_hoja->Upload->SaveToFile($this->imagen_hoja->UploadPath, $rsnew['imagen_hoja'], TRUE);
						}
						if ($this->imagen_hoja->Upload->DbValue <> "")
							@unlink(ew_UploadPathEx(TRUE, $this->imagen_hoja->OldUploadPath) . $this->imagen_hoja->Upload->DbValue);
					}
					if (!$this->imagen_flor->Upload->KeepFile) {
						if (!ew_Empty($this->imagen_flor->Upload->Value)) {
							$this->imagen_flor->Upload->SaveToFile($this->imagen_flor->UploadPath, $rsnew['imagen_flor'], TRUE);
						}
						if ($this->imagen_flor->Upload->DbValue <> "")
							@unlink(ew_UploadPathEx(TRUE, $this->imagen_flor->OldUploadPath) . $this->imagen_flor->Upload->DbValue);
					}
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();

		// ICONO
		ew_CleanUploadTempPath($this->ICONO, $this->ICONO->Upload->Index);

		// imagen_completo
		ew_CleanUploadTempPath($this->imagen_completo, $this->imagen_completo->Upload->Index);

		// imagen_hoja
		ew_CleanUploadTempPath($this->imagen_hoja, $this->imagen_hoja->Upload->Index);

		// imagen_flor
		ew_CleanUploadTempPath($this->imagen_flor, $this->imagen_flor->Upload->Index);
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "_2_especieslist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("edit");
		$Breadcrumb->Add("edit", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($p_2_especies_edit)) $p_2_especies_edit = new cp_2_especies_edit();

// Page init
$p_2_especies_edit->Page_Init();

// Page main
$p_2_especies_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$p_2_especies_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var p_2_especies_edit = new ew_Page("p_2_especies_edit");
p_2_especies_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = p_2_especies_edit.PageID; // For backward compatibility

// Form object
var f_2_especiesedit = new ew_Form("f_2_especiesedit");

// Validate form
f_2_especiesedit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_id_especie");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($_2_especies->id_especie->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_id_especie");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($_2_especies->id_especie->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_NOMBRE_FAM");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($_2_especies->NOMBRE_FAM->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_NOMBRE_CIE");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($_2_especies->NOMBRE_CIE->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_NOMBRE_COM");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($_2_especies->NOMBRE_COM->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_TIPO_FOLLA");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($_2_especies->TIPO_FOLLA->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_ORIGEN");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($_2_especies->ORIGEN->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_perfume");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($_2_especies->perfume->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_avejas");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($_2_especies->avejas->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_mariposas");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($_2_especies->mariposas->FldErrMsg()) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
f_2_especiesedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
f_2_especiesedit.ValidateRequired = true;
<?php } else { ?>
f_2_especiesedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $p_2_especies_edit->ShowPageHeader(); ?>
<?php
$p_2_especies_edit->ShowMessage();
?>
<form name="f_2_especiesedit" id="f_2_especiesedit" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="_2_especies">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl__2_especiesedit" class="table table-bordered table-striped">
<?php if ($_2_especies->id_especie->Visible) { // id_especie ?>
	<tr id="r_id_especie">
		<td><span id="elh__2_especies_id_especie"><?php echo $_2_especies->id_especie->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $_2_especies->id_especie->CellAttributes() ?>>
<span id="el__2_especies_id_especie" class="control-group">
<span<?php echo $_2_especies->id_especie->ViewAttributes() ?>>
<?php echo $_2_especies->id_especie->EditValue ?></span>
</span>
<input type="hidden" data-field="x_id_especie" name="x_id_especie" id="x_id_especie" value="<?php echo ew_HtmlEncode($_2_especies->id_especie->CurrentValue) ?>">
<?php echo $_2_especies->id_especie->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->NOMBRE_FAM->Visible) { // NOMBRE_FAM ?>
	<tr id="r_NOMBRE_FAM">
		<td><span id="elh__2_especies_NOMBRE_FAM"><?php echo $_2_especies->NOMBRE_FAM->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $_2_especies->NOMBRE_FAM->CellAttributes() ?>>
<span id="el__2_especies_NOMBRE_FAM" class="control-group">
<input type="text" data-field="x_NOMBRE_FAM" name="x_NOMBRE_FAM" id="x_NOMBRE_FAM" size="30" maxlength="255" placeholder="<?php echo $_2_especies->NOMBRE_FAM->PlaceHolder ?>" value="<?php echo $_2_especies->NOMBRE_FAM->EditValue ?>"<?php echo $_2_especies->NOMBRE_FAM->EditAttributes() ?>>
</span>
<?php echo $_2_especies->NOMBRE_FAM->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->NOMBRE_CIE->Visible) { // NOMBRE_CIE ?>
	<tr id="r_NOMBRE_CIE">
		<td><span id="elh__2_especies_NOMBRE_CIE"><?php echo $_2_especies->NOMBRE_CIE->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $_2_especies->NOMBRE_CIE->CellAttributes() ?>>
<span id="el__2_especies_NOMBRE_CIE" class="control-group">
<input type="text" data-field="x_NOMBRE_CIE" name="x_NOMBRE_CIE" id="x_NOMBRE_CIE" size="30" maxlength="255" placeholder="<?php echo $_2_especies->NOMBRE_CIE->PlaceHolder ?>" value="<?php echo $_2_especies->NOMBRE_CIE->EditValue ?>"<?php echo $_2_especies->NOMBRE_CIE->EditAttributes() ?>>
</span>
<?php echo $_2_especies->NOMBRE_CIE->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->NOMBRE_COM->Visible) { // NOMBRE_COM ?>
	<tr id="r_NOMBRE_COM">
		<td><span id="elh__2_especies_NOMBRE_COM"><?php echo $_2_especies->NOMBRE_COM->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $_2_especies->NOMBRE_COM->CellAttributes() ?>>
<span id="el__2_especies_NOMBRE_COM" class="control-group">
<input type="text" data-field="x_NOMBRE_COM" name="x_NOMBRE_COM" id="x_NOMBRE_COM" size="30" maxlength="255" placeholder="<?php echo $_2_especies->NOMBRE_COM->PlaceHolder ?>" value="<?php echo $_2_especies->NOMBRE_COM->EditValue ?>"<?php echo $_2_especies->NOMBRE_COM->EditAttributes() ?>>
</span>
<?php echo $_2_especies->NOMBRE_COM->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->TIPO_FOLLA->Visible) { // TIPO_FOLLA ?>
	<tr id="r_TIPO_FOLLA">
		<td><span id="elh__2_especies_TIPO_FOLLA"><?php echo $_2_especies->TIPO_FOLLA->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $_2_especies->TIPO_FOLLA->CellAttributes() ?>>
<span id="el__2_especies_TIPO_FOLLA" class="control-group">
<input type="text" data-field="x_TIPO_FOLLA" name="x_TIPO_FOLLA" id="x_TIPO_FOLLA" size="30" maxlength="255" placeholder="<?php echo $_2_especies->TIPO_FOLLA->PlaceHolder ?>" value="<?php echo $_2_especies->TIPO_FOLLA->EditValue ?>"<?php echo $_2_especies->TIPO_FOLLA->EditAttributes() ?>>
</span>
<?php echo $_2_especies->TIPO_FOLLA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->ORIGEN->Visible) { // ORIGEN ?>
	<tr id="r_ORIGEN">
		<td><span id="elh__2_especies_ORIGEN"><?php echo $_2_especies->ORIGEN->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $_2_especies->ORIGEN->CellAttributes() ?>>
<span id="el__2_especies_ORIGEN" class="control-group">
<input type="text" data-field="x_ORIGEN" name="x_ORIGEN" id="x_ORIGEN" size="30" maxlength="255" placeholder="<?php echo $_2_especies->ORIGEN->PlaceHolder ?>" value="<?php echo $_2_especies->ORIGEN->EditValue ?>"<?php echo $_2_especies->ORIGEN->EditAttributes() ?>>
</span>
<?php echo $_2_especies->ORIGEN->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->ICONO->Visible) { // ICONO ?>
	<tr id="r_ICONO">
		<td><span id="elh__2_especies_ICONO"><?php echo $_2_especies->ICONO->FldCaption() ?></span></td>
		<td<?php echo $_2_especies->ICONO->CellAttributes() ?>>
<span id="el__2_especies_ICONO" class="control-group">
<span id="fd_x_ICONO">
<span class="btn btn-small fileinput-button"<?php if ($_2_especies->ICONO->ReadOnly || $_2_especies->ICONO->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_ICONO" name="x_ICONO" id="x_ICONO">
</span>
<input type="hidden" name="fn_x_ICONO" id= "fn_x_ICONO" value="<?php echo $_2_especies->ICONO->Upload->FileName ?>">
<?php if (@$_POST["fa_x_ICONO"] == "0") { ?>
<input type="hidden" name="fa_x_ICONO" id= "fa_x_ICONO" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_ICONO" id= "fa_x_ICONO" value="1">
<?php } ?>
<input type="hidden" name="fs_x_ICONO" id= "fs_x_ICONO" value="50">
</span>
<table id="ft_x_ICONO" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $_2_especies->ICONO->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->imagen_completo->Visible) { // imagen_completo ?>
	<tr id="r_imagen_completo">
		<td><span id="elh__2_especies_imagen_completo"><?php echo $_2_especies->imagen_completo->FldCaption() ?></span></td>
		<td<?php echo $_2_especies->imagen_completo->CellAttributes() ?>>
<span id="el__2_especies_imagen_completo" class="control-group">
<span id="fd_x_imagen_completo">
<span class="btn btn-small fileinput-button"<?php if ($_2_especies->imagen_completo->ReadOnly || $_2_especies->imagen_completo->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_imagen_completo" name="x_imagen_completo" id="x_imagen_completo">
</span>
<input type="hidden" name="fn_x_imagen_completo" id= "fn_x_imagen_completo" value="<?php echo $_2_especies->imagen_completo->Upload->FileName ?>">
<?php if (@$_POST["fa_x_imagen_completo"] == "0") { ?>
<input type="hidden" name="fa_x_imagen_completo" id= "fa_x_imagen_completo" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_imagen_completo" id= "fa_x_imagen_completo" value="1">
<?php } ?>
<input type="hidden" name="fs_x_imagen_completo" id= "fs_x_imagen_completo" value="50">
</span>
<table id="ft_x_imagen_completo" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $_2_especies->imagen_completo->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->imagen_hoja->Visible) { // imagen_hoja ?>
	<tr id="r_imagen_hoja">
		<td><span id="elh__2_especies_imagen_hoja"><?php echo $_2_especies->imagen_hoja->FldCaption() ?></span></td>
		<td<?php echo $_2_especies->imagen_hoja->CellAttributes() ?>>
<span id="el__2_especies_imagen_hoja" class="control-group">
<span id="fd_x_imagen_hoja">
<span class="btn btn-small fileinput-button"<?php if ($_2_especies->imagen_hoja->ReadOnly || $_2_especies->imagen_hoja->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_imagen_hoja" name="x_imagen_hoja" id="x_imagen_hoja">
</span>
<input type="hidden" name="fn_x_imagen_hoja" id= "fn_x_imagen_hoja" value="<?php echo $_2_especies->imagen_hoja->Upload->FileName ?>">
<?php if (@$_POST["fa_x_imagen_hoja"] == "0") { ?>
<input type="hidden" name="fa_x_imagen_hoja" id= "fa_x_imagen_hoja" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_imagen_hoja" id= "fa_x_imagen_hoja" value="1">
<?php } ?>
<input type="hidden" name="fs_x_imagen_hoja" id= "fs_x_imagen_hoja" value="50">
</span>
<table id="ft_x_imagen_hoja" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $_2_especies->imagen_hoja->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->imagen_flor->Visible) { // imagen_flor ?>
	<tr id="r_imagen_flor">
		<td><span id="elh__2_especies_imagen_flor"><?php echo $_2_especies->imagen_flor->FldCaption() ?></span></td>
		<td<?php echo $_2_especies->imagen_flor->CellAttributes() ?>>
<span id="el__2_especies_imagen_flor" class="control-group">
<span id="fd_x_imagen_flor">
<span class="btn btn-small fileinput-button"<?php if ($_2_especies->imagen_flor->ReadOnly || $_2_especies->imagen_flor->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_imagen_flor" name="x_imagen_flor" id="x_imagen_flor">
</span>
<input type="hidden" name="fn_x_imagen_flor" id= "fn_x_imagen_flor" value="<?php echo $_2_especies->imagen_flor->Upload->FileName ?>">
<?php if (@$_POST["fa_x_imagen_flor"] == "0") { ?>
<input type="hidden" name="fa_x_imagen_flor" id= "fa_x_imagen_flor" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_imagen_flor" id= "fa_x_imagen_flor" value="1">
<?php } ?>
<input type="hidden" name="fs_x_imagen_flor" id= "fs_x_imagen_flor" value="50">
</span>
<table id="ft_x_imagen_flor" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $_2_especies->imagen_flor->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->descripcion->Visible) { // descripcion ?>
	<tr id="r_descripcion">
		<td><span id="elh__2_especies_descripcion"><?php echo $_2_especies->descripcion->FldCaption() ?></span></td>
		<td<?php echo $_2_especies->descripcion->CellAttributes() ?>>
<span id="el__2_especies_descripcion" class="control-group">
<textarea data-field="x_descripcion" name="x_descripcion" id="x_descripcion" cols="35" rows="4" placeholder="<?php echo $_2_especies->descripcion->PlaceHolder ?>"<?php echo $_2_especies->descripcion->EditAttributes() ?>><?php echo $_2_especies->descripcion->EditValue ?></textarea>
</span>
<?php echo $_2_especies->descripcion->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->medicinal->Visible) { // medicinal ?>
	<tr id="r_medicinal">
		<td><span id="elh__2_especies_medicinal"><?php echo $_2_especies->medicinal->FldCaption() ?></span></td>
		<td<?php echo $_2_especies->medicinal->CellAttributes() ?>>
<span id="el__2_especies_medicinal" class="control-group">
<textarea data-field="x_medicinal" name="x_medicinal" id="x_medicinal" cols="35" rows="4" placeholder="<?php echo $_2_especies->medicinal->PlaceHolder ?>"<?php echo $_2_especies->medicinal->EditAttributes() ?>><?php echo $_2_especies->medicinal->EditValue ?></textarea>
</span>
<?php echo $_2_especies->medicinal->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->comestible->Visible) { // comestible ?>
	<tr id="r_comestible">
		<td><span id="elh__2_especies_comestible"><?php echo $_2_especies->comestible->FldCaption() ?></span></td>
		<td<?php echo $_2_especies->comestible->CellAttributes() ?>>
<span id="el__2_especies_comestible" class="control-group">
<textarea data-field="x_comestible" name="x_comestible" id="x_comestible" cols="35" rows="4" placeholder="<?php echo $_2_especies->comestible->PlaceHolder ?>"<?php echo $_2_especies->comestible->EditAttributes() ?>><?php echo $_2_especies->comestible->EditValue ?></textarea>
</span>
<?php echo $_2_especies->comestible->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->perfume->Visible) { // perfume ?>
	<tr id="r_perfume">
		<td><span id="elh__2_especies_perfume"><?php echo $_2_especies->perfume->FldCaption() ?></span></td>
		<td<?php echo $_2_especies->perfume->CellAttributes() ?>>
<span id="el__2_especies_perfume" class="control-group">
<input type="text" data-field="x_perfume" name="x_perfume" id="x_perfume" size="30" placeholder="<?php echo $_2_especies->perfume->PlaceHolder ?>" value="<?php echo $_2_especies->perfume->EditValue ?>"<?php echo $_2_especies->perfume->EditAttributes() ?>>
</span>
<?php echo $_2_especies->perfume->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->avejas->Visible) { // avejas ?>
	<tr id="r_avejas">
		<td><span id="elh__2_especies_avejas"><?php echo $_2_especies->avejas->FldCaption() ?></span></td>
		<td<?php echo $_2_especies->avejas->CellAttributes() ?>>
<span id="el__2_especies_avejas" class="control-group">
<input type="text" data-field="x_avejas" name="x_avejas" id="x_avejas" size="30" placeholder="<?php echo $_2_especies->avejas->PlaceHolder ?>" value="<?php echo $_2_especies->avejas->EditValue ?>"<?php echo $_2_especies->avejas->EditAttributes() ?>>
</span>
<?php echo $_2_especies->avejas->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($_2_especies->mariposas->Visible) { // mariposas ?>
	<tr id="r_mariposas">
		<td><span id="elh__2_especies_mariposas"><?php echo $_2_especies->mariposas->FldCaption() ?></span></td>
		<td<?php echo $_2_especies->mariposas->CellAttributes() ?>>
<span id="el__2_especies_mariposas" class="control-group">
<input type="text" data-field="x_mariposas" name="x_mariposas" id="x_mariposas" size="30" placeholder="<?php echo $_2_especies->mariposas->PlaceHolder ?>" value="<?php echo $_2_especies->mariposas->EditValue ?>"<?php echo $_2_especies->mariposas->EditAttributes() ?>>
</span>
<?php echo $_2_especies->mariposas->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
f_2_especiesedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$p_2_especies_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$p_2_especies_edit->Page_Terminate();
?>
