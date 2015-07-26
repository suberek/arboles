<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "especiesinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$especies_add = NULL; // Initialize page object first

class cespecies_add extends cespecies {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Table name
	var $TableName = 'especies';

	// Page object name
	var $PageObjName = 'especies_add';

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

		// Table object (especies)
		if (!isset($GLOBALS["especies"])) {
			$GLOBALS["especies"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["especies"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'especies', TRUE);

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
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("especieslist.php");
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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["id_especie"] != "") {
				$this->id_especie->setQueryStringValue($_GET["id_especie"]);
				$this->setKey("id_especie", $this->id_especie->CurrentValue); // Set up key
			} else {
				$this->setKey("id_especie", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("especieslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "especiesview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
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

	// Load default values
	function LoadDefaultValues() {
		$this->id_especie->CurrentValue = 0;
		$this->id_familia->CurrentValue = NULL;
		$this->id_familia->OldValue = $this->id_familia->CurrentValue;
		$this->NOMBRE_CIE->CurrentValue = NULL;
		$this->NOMBRE_CIE->OldValue = $this->NOMBRE_CIE->CurrentValue;
		$this->NOMBRE_COM->CurrentValue = NULL;
		$this->NOMBRE_COM->OldValue = $this->NOMBRE_COM->CurrentValue;
		$this->TIPO_FOLLA->CurrentValue = NULL;
		$this->TIPO_FOLLA->OldValue = $this->TIPO_FOLLA->CurrentValue;
		$this->ORIGEN->CurrentValue = NULL;
		$this->ORIGEN->OldValue = $this->ORIGEN->CurrentValue;
		$this->ICONO->Upload->DbValue = NULL;
		$this->ICONO->OldValue = $this->ICONO->Upload->DbValue;
		$this->ICONO->CurrentValue = NULL; // Clear file related field
		$this->imagen_completo->Upload->DbValue = NULL;
		$this->imagen_completo->OldValue = $this->imagen_completo->Upload->DbValue;
		$this->imagen_completo->CurrentValue = NULL; // Clear file related field
		$this->imagen_hoja->Upload->DbValue = NULL;
		$this->imagen_hoja->OldValue = $this->imagen_hoja->Upload->DbValue;
		$this->imagen_hoja->CurrentValue = NULL; // Clear file related field
		$this->imagen_flor->Upload->DbValue = NULL;
		$this->imagen_flor->OldValue = $this->imagen_flor->Upload->DbValue;
		$this->imagen_flor->CurrentValue = NULL; // Clear file related field
		$this->descripcion->CurrentValue = NULL;
		$this->descripcion->OldValue = $this->descripcion->CurrentValue;
		$this->medicinal->CurrentValue = NULL;
		$this->medicinal->OldValue = $this->medicinal->CurrentValue;
		$this->comestible->CurrentValue = NULL;
		$this->comestible->OldValue = $this->comestible->CurrentValue;
		$this->perfume->CurrentValue = NULL;
		$this->perfume->OldValue = $this->perfume->CurrentValue;
		$this->avejas->CurrentValue = NULL;
		$this->avejas->OldValue = $this->avejas->CurrentValue;
		$this->mariposas->CurrentValue = NULL;
		$this->mariposas->OldValue = $this->mariposas->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->id_especie->FldIsDetailKey) {
			$this->id_especie->setFormValue($objForm->GetValue("x_id_especie"));
		}
		if (!$this->id_familia->FldIsDetailKey) {
			$this->id_familia->setFormValue($objForm->GetValue("x_id_familia"));
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
		$this->LoadOldRecord();
		$this->id_especie->CurrentValue = $this->id_especie->FormValue;
		$this->id_familia->CurrentValue = $this->id_familia->FormValue;
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
		if (array_key_exists('EV__id_familia', $rs->fields)) {
			$this->id_familia->VirtualValue = $rs->fields('EV__id_familia'); // Set up virtual field value
		} else {
			$this->id_familia->VirtualValue = ""; // Clear value
		}
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id_especie")) <> "")
			$this->id_especie->CurrentValue = $this->getKey("id_especie"); // id_especie
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
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

			// id_familia
			if ($this->id_familia->VirtualValue <> "") {
				$this->id_familia->ViewValue = $this->id_familia->VirtualValue;
			} else {
				$this->id_familia->ViewValue = $this->id_familia->CurrentValue;
			if (strval($this->id_familia->CurrentValue) <> "") {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->id_familia->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id`, `familia` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `familias`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->id_familia, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `familia`";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->id_familia->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->id_familia->ViewValue = $this->id_familia->CurrentValue;
				}
			} else {
				$this->id_familia->ViewValue = NULL;
			}
			}
			$this->id_familia->ViewCustomAttributes = "";

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

			// id_familia
			$this->id_familia->LinkCustomAttributes = "";
			$this->id_familia->HrefValue = "";
			$this->id_familia->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// id_especie
			$this->id_especie->EditCustomAttributes = "";
			$this->id_especie->EditValue = ew_HtmlEncode($this->id_especie->CurrentValue);
			$this->id_especie->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->id_especie->FldCaption()));

			// id_familia
			$this->id_familia->EditCustomAttributes = "";
			$this->id_familia->EditValue = ew_HtmlEncode($this->id_familia->CurrentValue);
			$this->id_familia->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->id_familia->FldCaption()));

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
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->ICONO);

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
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->imagen_completo);

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
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->imagen_hoja);

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
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->imagen_flor);

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

			// id_familia
			$this->id_familia->HrefValue = "";

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
		if (!$this->id_familia->FldIsDetailKey && !is_null($this->id_familia->FormValue) && $this->id_familia->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->id_familia->FldCaption());
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

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// id_especie
		$this->id_especie->SetDbValueDef($rsnew, $this->id_especie->CurrentValue, 0, strval($this->id_especie->CurrentValue) == "");

		// id_familia
		$this->id_familia->SetDbValueDef($rsnew, $this->id_familia->CurrentValue, 0, FALSE);

		// NOMBRE_CIE
		$this->NOMBRE_CIE->SetDbValueDef($rsnew, $this->NOMBRE_CIE->CurrentValue, "", FALSE);

		// NOMBRE_COM
		$this->NOMBRE_COM->SetDbValueDef($rsnew, $this->NOMBRE_COM->CurrentValue, "", FALSE);

		// TIPO_FOLLA
		$this->TIPO_FOLLA->SetDbValueDef($rsnew, $this->TIPO_FOLLA->CurrentValue, "", FALSE);

		// ORIGEN
		$this->ORIGEN->SetDbValueDef($rsnew, $this->ORIGEN->CurrentValue, "", FALSE);

		// ICONO
		if (!$this->ICONO->Upload->KeepFile) {
			if ($this->ICONO->Upload->FileName == "") {
				$rsnew['ICONO'] = NULL;
			} else {
				$rsnew['ICONO'] = $this->ICONO->Upload->FileName;
			}
		}

		// imagen_completo
		if (!$this->imagen_completo->Upload->KeepFile) {
			if ($this->imagen_completo->Upload->FileName == "") {
				$rsnew['imagen_completo'] = NULL;
			} else {
				$rsnew['imagen_completo'] = $this->imagen_completo->Upload->FileName;
			}
		}

		// imagen_hoja
		if (!$this->imagen_hoja->Upload->KeepFile) {
			if ($this->imagen_hoja->Upload->FileName == "") {
				$rsnew['imagen_hoja'] = NULL;
			} else {
				$rsnew['imagen_hoja'] = $this->imagen_hoja->Upload->FileName;
			}
		}

		// imagen_flor
		if (!$this->imagen_flor->Upload->KeepFile) {
			if ($this->imagen_flor->Upload->FileName == "") {
				$rsnew['imagen_flor'] = NULL;
			} else {
				$rsnew['imagen_flor'] = $this->imagen_flor->Upload->FileName;
			}
		}

		// descripcion
		$this->descripcion->SetDbValueDef($rsnew, $this->descripcion->CurrentValue, NULL, FALSE);

		// medicinal
		$this->medicinal->SetDbValueDef($rsnew, $this->medicinal->CurrentValue, NULL, FALSE);

		// comestible
		$this->comestible->SetDbValueDef($rsnew, $this->comestible->CurrentValue, NULL, FALSE);

		// perfume
		$this->perfume->SetDbValueDef($rsnew, $this->perfume->CurrentValue, NULL, FALSE);

		// avejas
		$this->avejas->SetDbValueDef($rsnew, $this->avejas->CurrentValue, NULL, FALSE);

		// mariposas
		$this->mariposas->SetDbValueDef($rsnew, $this->mariposas->CurrentValue, NULL, FALSE);
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

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && $this->id_especie->CurrentValue == "" && $this->id_especie->getSessionValue() == "") {
			$this->setFailureMessage($Language->Phrase("InvalidKeyValue"));
			$bInsertRow = FALSE;
		}

		// Check for duplicate key
		if ($bInsertRow && $this->ValidateKey) {
			$sFilter = $this->KeyFilter();
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sKeyErrMsg = str_replace("%f", $sFilter, $Language->Phrase("DupKey"));
				$this->setFailureMessage($sKeyErrMsg);
				$rsChk->Close();
				$bInsertRow = FALSE;
			}
		}
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
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
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}

		// ICONO
		ew_CleanUploadTempPath($this->ICONO, $this->ICONO->Upload->Index);

		// imagen_completo
		ew_CleanUploadTempPath($this->imagen_completo, $this->imagen_completo->Upload->Index);

		// imagen_hoja
		ew_CleanUploadTempPath($this->imagen_hoja, $this->imagen_hoja->Upload->Index);

		// imagen_flor
		ew_CleanUploadTempPath($this->imagen_flor, $this->imagen_flor->Upload->Index);
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "especieslist.php", $this->TableVar);
		$PageCaption = ($this->CurrentAction == "C") ? $Language->Phrase("Copy") : $Language->Phrase("Add");
		$Breadcrumb->Add("add", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
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
if (!isset($especies_add)) $especies_add = new cespecies_add();

// Page init
$especies_add->Page_Init();

// Page main
$especies_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$especies_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var especies_add = new ew_Page("especies_add");
especies_add.PageID = "add"; // Page ID
var EW_PAGE_ID = especies_add.PageID; // For backward compatibility

// Form object
var fespeciesadd = new ew_Form("fespeciesadd");

// Validate form
fespeciesadd.Validate = function() {
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
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($especies->id_especie->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_id_especie");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($especies->id_especie->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_id_familia");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($especies->id_familia->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_NOMBRE_CIE");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($especies->NOMBRE_CIE->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_NOMBRE_COM");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($especies->NOMBRE_COM->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_TIPO_FOLLA");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($especies->TIPO_FOLLA->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_ORIGEN");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($especies->ORIGEN->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_perfume");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($especies->perfume->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_avejas");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($especies->avejas->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_mariposas");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($especies->mariposas->FldErrMsg()) ?>");

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
fespeciesadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fespeciesadd.ValidateRequired = true;
<?php } else { ?>
fespeciesadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fespeciesadd.Lists["x_id_familia"] = {"LinkField":"x_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_familia","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $especies_add->ShowPageHeader(); ?>
<?php
$especies_add->ShowMessage();
?>
<form name="fespeciesadd" id="fespeciesadd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="especies">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_especiesadd" class="table table-bordered table-striped">
<?php if ($especies->id_especie->Visible) { // id_especie ?>
	<tr id="r_id_especie">
		<td><span id="elh_especies_id_especie"><?php echo $especies->id_especie->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $especies->id_especie->CellAttributes() ?>>
<span id="el_especies_id_especie" class="control-group">
<input type="text" data-field="x_id_especie" name="x_id_especie" id="x_id_especie" size="30" placeholder="<?php echo $especies->id_especie->PlaceHolder ?>" value="<?php echo $especies->id_especie->EditValue ?>"<?php echo $especies->id_especie->EditAttributes() ?>>
</span>
<?php echo $especies->id_especie->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->id_familia->Visible) { // id_familia ?>
	<tr id="r_id_familia">
		<td><span id="elh_especies_id_familia"><?php echo $especies->id_familia->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $especies->id_familia->CellAttributes() ?>>
<span id="el_especies_id_familia" class="control-group">
<?php
	$wrkonchange = trim(" " . @$especies->id_familia->EditAttrs["onchange"]);
	if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
	$especies->id_familia->EditAttrs["onchange"] = "";
?>
<span id="as_x_id_familia" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_id_familia" id="sv_x_id_familia" value="<?php echo $especies->id_familia->EditValue ?>" size="30" placeholder="<?php echo $especies->id_familia->PlaceHolder ?>"<?php echo $especies->id_familia->EditAttributes() ?>>&nbsp;<span id="em_x_id_familia" class="ewMessage" style="display: none"><?php echo str_replace("%f", "phpimages/", $Language->Phrase("UnmatchedValue")) ?></span>
	<div id="sc_x_id_familia" style="display: inline; z-index: 8980"></div>
</span>
<input type="hidden" data-field="x_id_familia" name="x_id_familia" id="x_id_familia" value="<?php echo $especies->id_familia->CurrentValue ?>"<?php echo $wrkonchange ?>>
<?php
$sSqlWrk = "SELECT `id`, `familia` AS `DispFld` FROM `familias`";
$sWhereWrk = "`familia` LIKE '{query_value}%'";

// Call Lookup selecting
$especies->Lookup_Selecting($especies->id_familia, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `familia`";
$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
?>
<input type="hidden" name="q_x_id_familia" id="q_x_id_familia" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>">
<script type="text/javascript">
var oas = new ew_AutoSuggest("x_id_familia", fespeciesadd, false, EW_AUTO_SUGGEST_MAX_ENTRIES);
oas.formatResult = function(ar) {
	var dv = ar[1];
	for (var i = 2; i <= 4; i++)
		dv += (ar[i]) ? ew_ValueSeparator(i - 1, "x_id_familia") + ar[i] : "";
	return dv;
}
fespeciesadd.AutoSuggests["x_id_familia"] = oas;
</script>
</span>
<?php echo $especies->id_familia->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->NOMBRE_CIE->Visible) { // NOMBRE_CIE ?>
	<tr id="r_NOMBRE_CIE">
		<td><span id="elh_especies_NOMBRE_CIE"><?php echo $especies->NOMBRE_CIE->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $especies->NOMBRE_CIE->CellAttributes() ?>>
<span id="el_especies_NOMBRE_CIE" class="control-group">
<input type="text" data-field="x_NOMBRE_CIE" name="x_NOMBRE_CIE" id="x_NOMBRE_CIE" size="30" maxlength="255" placeholder="<?php echo $especies->NOMBRE_CIE->PlaceHolder ?>" value="<?php echo $especies->NOMBRE_CIE->EditValue ?>"<?php echo $especies->NOMBRE_CIE->EditAttributes() ?>>
</span>
<?php echo $especies->NOMBRE_CIE->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->NOMBRE_COM->Visible) { // NOMBRE_COM ?>
	<tr id="r_NOMBRE_COM">
		<td><span id="elh_especies_NOMBRE_COM"><?php echo $especies->NOMBRE_COM->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $especies->NOMBRE_COM->CellAttributes() ?>>
<span id="el_especies_NOMBRE_COM" class="control-group">
<input type="text" data-field="x_NOMBRE_COM" name="x_NOMBRE_COM" id="x_NOMBRE_COM" size="30" maxlength="255" placeholder="<?php echo $especies->NOMBRE_COM->PlaceHolder ?>" value="<?php echo $especies->NOMBRE_COM->EditValue ?>"<?php echo $especies->NOMBRE_COM->EditAttributes() ?>>
</span>
<?php echo $especies->NOMBRE_COM->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->TIPO_FOLLA->Visible) { // TIPO_FOLLA ?>
	<tr id="r_TIPO_FOLLA">
		<td><span id="elh_especies_TIPO_FOLLA"><?php echo $especies->TIPO_FOLLA->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $especies->TIPO_FOLLA->CellAttributes() ?>>
<span id="el_especies_TIPO_FOLLA" class="control-group">
<input type="text" data-field="x_TIPO_FOLLA" name="x_TIPO_FOLLA" id="x_TIPO_FOLLA" size="30" maxlength="255" placeholder="<?php echo $especies->TIPO_FOLLA->PlaceHolder ?>" value="<?php echo $especies->TIPO_FOLLA->EditValue ?>"<?php echo $especies->TIPO_FOLLA->EditAttributes() ?>>
</span>
<?php echo $especies->TIPO_FOLLA->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->ORIGEN->Visible) { // ORIGEN ?>
	<tr id="r_ORIGEN">
		<td><span id="elh_especies_ORIGEN"><?php echo $especies->ORIGEN->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $especies->ORIGEN->CellAttributes() ?>>
<span id="el_especies_ORIGEN" class="control-group">
<input type="text" data-field="x_ORIGEN" name="x_ORIGEN" id="x_ORIGEN" size="30" maxlength="255" placeholder="<?php echo $especies->ORIGEN->PlaceHolder ?>" value="<?php echo $especies->ORIGEN->EditValue ?>"<?php echo $especies->ORIGEN->EditAttributes() ?>>
</span>
<?php echo $especies->ORIGEN->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->ICONO->Visible) { // ICONO ?>
	<tr id="r_ICONO">
		<td><span id="elh_especies_ICONO"><?php echo $especies->ICONO->FldCaption() ?></span></td>
		<td<?php echo $especies->ICONO->CellAttributes() ?>>
<span id="el_especies_ICONO" class="control-group">
<span id="fd_x_ICONO">
<span class="btn btn-small fileinput-button"<?php if ($especies->ICONO->ReadOnly || $especies->ICONO->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_ICONO" name="x_ICONO" id="x_ICONO">
</span>
<input type="hidden" name="fn_x_ICONO" id= "fn_x_ICONO" value="<?php echo $especies->ICONO->Upload->FileName ?>">
<input type="hidden" name="fa_x_ICONO" id= "fa_x_ICONO" value="0">
<input type="hidden" name="fs_x_ICONO" id= "fs_x_ICONO" value="50">
</span>
<table id="ft_x_ICONO" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $especies->ICONO->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->imagen_completo->Visible) { // imagen_completo ?>
	<tr id="r_imagen_completo">
		<td><span id="elh_especies_imagen_completo"><?php echo $especies->imagen_completo->FldCaption() ?></span></td>
		<td<?php echo $especies->imagen_completo->CellAttributes() ?>>
<span id="el_especies_imagen_completo" class="control-group">
<span id="fd_x_imagen_completo">
<span class="btn btn-small fileinput-button"<?php if ($especies->imagen_completo->ReadOnly || $especies->imagen_completo->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_imagen_completo" name="x_imagen_completo" id="x_imagen_completo">
</span>
<input type="hidden" name="fn_x_imagen_completo" id= "fn_x_imagen_completo" value="<?php echo $especies->imagen_completo->Upload->FileName ?>">
<input type="hidden" name="fa_x_imagen_completo" id= "fa_x_imagen_completo" value="0">
<input type="hidden" name="fs_x_imagen_completo" id= "fs_x_imagen_completo" value="50">
</span>
<table id="ft_x_imagen_completo" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $especies->imagen_completo->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->imagen_hoja->Visible) { // imagen_hoja ?>
	<tr id="r_imagen_hoja">
		<td><span id="elh_especies_imagen_hoja"><?php echo $especies->imagen_hoja->FldCaption() ?></span></td>
		<td<?php echo $especies->imagen_hoja->CellAttributes() ?>>
<span id="el_especies_imagen_hoja" class="control-group">
<span id="fd_x_imagen_hoja">
<span class="btn btn-small fileinput-button"<?php if ($especies->imagen_hoja->ReadOnly || $especies->imagen_hoja->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_imagen_hoja" name="x_imagen_hoja" id="x_imagen_hoja">
</span>
<input type="hidden" name="fn_x_imagen_hoja" id= "fn_x_imagen_hoja" value="<?php echo $especies->imagen_hoja->Upload->FileName ?>">
<input type="hidden" name="fa_x_imagen_hoja" id= "fa_x_imagen_hoja" value="0">
<input type="hidden" name="fs_x_imagen_hoja" id= "fs_x_imagen_hoja" value="50">
</span>
<table id="ft_x_imagen_hoja" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $especies->imagen_hoja->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->imagen_flor->Visible) { // imagen_flor ?>
	<tr id="r_imagen_flor">
		<td><span id="elh_especies_imagen_flor"><?php echo $especies->imagen_flor->FldCaption() ?></span></td>
		<td<?php echo $especies->imagen_flor->CellAttributes() ?>>
<span id="el_especies_imagen_flor" class="control-group">
<span id="fd_x_imagen_flor">
<span class="btn btn-small fileinput-button"<?php if ($especies->imagen_flor->ReadOnly || $especies->imagen_flor->Disabled) echo " style=\"display: none;\""; ?>>
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_imagen_flor" name="x_imagen_flor" id="x_imagen_flor">
</span>
<input type="hidden" name="fn_x_imagen_flor" id= "fn_x_imagen_flor" value="<?php echo $especies->imagen_flor->Upload->FileName ?>">
<input type="hidden" name="fa_x_imagen_flor" id= "fa_x_imagen_flor" value="0">
<input type="hidden" name="fs_x_imagen_flor" id= "fs_x_imagen_flor" value="50">
</span>
<table id="ft_x_imagen_flor" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $especies->imagen_flor->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->descripcion->Visible) { // descripcion ?>
	<tr id="r_descripcion">
		<td><span id="elh_especies_descripcion"><?php echo $especies->descripcion->FldCaption() ?></span></td>
		<td<?php echo $especies->descripcion->CellAttributes() ?>>
<span id="el_especies_descripcion" class="control-group">
<textarea data-field="x_descripcion" name="x_descripcion" id="x_descripcion" cols="35" rows="4" placeholder="<?php echo $especies->descripcion->PlaceHolder ?>"<?php echo $especies->descripcion->EditAttributes() ?>><?php echo $especies->descripcion->EditValue ?></textarea>
</span>
<?php echo $especies->descripcion->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->medicinal->Visible) { // medicinal ?>
	<tr id="r_medicinal">
		<td><span id="elh_especies_medicinal"><?php echo $especies->medicinal->FldCaption() ?></span></td>
		<td<?php echo $especies->medicinal->CellAttributes() ?>>
<span id="el_especies_medicinal" class="control-group">
<textarea data-field="x_medicinal" name="x_medicinal" id="x_medicinal" cols="35" rows="4" placeholder="<?php echo $especies->medicinal->PlaceHolder ?>"<?php echo $especies->medicinal->EditAttributes() ?>><?php echo $especies->medicinal->EditValue ?></textarea>
</span>
<?php echo $especies->medicinal->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->comestible->Visible) { // comestible ?>
	<tr id="r_comestible">
		<td><span id="elh_especies_comestible"><?php echo $especies->comestible->FldCaption() ?></span></td>
		<td<?php echo $especies->comestible->CellAttributes() ?>>
<span id="el_especies_comestible" class="control-group">
<textarea data-field="x_comestible" name="x_comestible" id="x_comestible" cols="35" rows="4" placeholder="<?php echo $especies->comestible->PlaceHolder ?>"<?php echo $especies->comestible->EditAttributes() ?>><?php echo $especies->comestible->EditValue ?></textarea>
</span>
<?php echo $especies->comestible->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->perfume->Visible) { // perfume ?>
	<tr id="r_perfume">
		<td><span id="elh_especies_perfume"><?php echo $especies->perfume->FldCaption() ?></span></td>
		<td<?php echo $especies->perfume->CellAttributes() ?>>
<span id="el_especies_perfume" class="control-group">
<input type="text" data-field="x_perfume" name="x_perfume" id="x_perfume" size="30" placeholder="<?php echo $especies->perfume->PlaceHolder ?>" value="<?php echo $especies->perfume->EditValue ?>"<?php echo $especies->perfume->EditAttributes() ?>>
</span>
<?php echo $especies->perfume->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->avejas->Visible) { // avejas ?>
	<tr id="r_avejas">
		<td><span id="elh_especies_avejas"><?php echo $especies->avejas->FldCaption() ?></span></td>
		<td<?php echo $especies->avejas->CellAttributes() ?>>
<span id="el_especies_avejas" class="control-group">
<input type="text" data-field="x_avejas" name="x_avejas" id="x_avejas" size="30" placeholder="<?php echo $especies->avejas->PlaceHolder ?>" value="<?php echo $especies->avejas->EditValue ?>"<?php echo $especies->avejas->EditAttributes() ?>>
</span>
<?php echo $especies->avejas->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($especies->mariposas->Visible) { // mariposas ?>
	<tr id="r_mariposas">
		<td><span id="elh_especies_mariposas"><?php echo $especies->mariposas->FldCaption() ?></span></td>
		<td<?php echo $especies->mariposas->CellAttributes() ?>>
<span id="el_especies_mariposas" class="control-group">
<input type="text" data-field="x_mariposas" name="x_mariposas" id="x_mariposas" size="30" placeholder="<?php echo $especies->mariposas->PlaceHolder ?>" value="<?php echo $especies->mariposas->EditValue ?>"<?php echo $especies->mariposas->EditAttributes() ?>>
</span>
<?php echo $especies->mariposas->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fespeciesadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$especies_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$especies_add->Page_Terminate();
?>
