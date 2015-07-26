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

$especies_delete = NULL; // Initialize page object first

class cespecies_delete extends cespecies {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Table name
	var $TableName = 'especies';

	// Page object name
	var $PageObjName = 'especies_delete';

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
			define("EW_PAGE_ID", 'delete', TRUE);

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
		if (!$Security->CanDelete()) {
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
			$this->Page_Terminate("especieslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in especies class, especiesinfo.php

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

		$this->NOMBRE_FAM->CellCssStyle = "white-space: nowrap;";

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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "especieslist.php", $this->TableVar);
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
if (!isset($especies_delete)) $especies_delete = new cespecies_delete();

// Page init
$especies_delete->Page_Init();

// Page main
$especies_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$especies_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var especies_delete = new ew_Page("especies_delete");
especies_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = especies_delete.PageID; // For backward compatibility

// Form object
var fespeciesdelete = new ew_Form("fespeciesdelete");

// Form_CustomValidate event
fespeciesdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fespeciesdelete.ValidateRequired = true;
<?php } else { ?>
fespeciesdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fespeciesdelete.Lists["x_id_familia"] = {"LinkField":"x_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_familia","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($especies_delete->Recordset = $especies_delete->LoadRecordset())
	$especies_deleteTotalRecs = $especies_delete->Recordset->RecordCount(); // Get record count
if ($especies_deleteTotalRecs <= 0) { // No record found, exit
	if ($especies_delete->Recordset)
		$especies_delete->Recordset->Close();
	$especies_delete->Page_Terminate("especieslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $especies_delete->ShowPageHeader(); ?>
<?php
$especies_delete->ShowMessage();
?>
<form name="fespeciesdelete" id="fespeciesdelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="especies">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($especies_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_especiesdelete" class="ewTable ewTableSeparate">
<?php echo $especies->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($especies->id_especie->Visible) { // id_especie ?>
		<td><span id="elh_especies_id_especie" class="especies_id_especie"><?php echo $especies->id_especie->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->id_familia->Visible) { // id_familia ?>
		<td><span id="elh_especies_id_familia" class="especies_id_familia"><?php echo $especies->id_familia->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->NOMBRE_CIE->Visible) { // NOMBRE_CIE ?>
		<td><span id="elh_especies_NOMBRE_CIE" class="especies_NOMBRE_CIE"><?php echo $especies->NOMBRE_CIE->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->NOMBRE_COM->Visible) { // NOMBRE_COM ?>
		<td><span id="elh_especies_NOMBRE_COM" class="especies_NOMBRE_COM"><?php echo $especies->NOMBRE_COM->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->TIPO_FOLLA->Visible) { // TIPO_FOLLA ?>
		<td><span id="elh_especies_TIPO_FOLLA" class="especies_TIPO_FOLLA"><?php echo $especies->TIPO_FOLLA->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->ORIGEN->Visible) { // ORIGEN ?>
		<td><span id="elh_especies_ORIGEN" class="especies_ORIGEN"><?php echo $especies->ORIGEN->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->ICONO->Visible) { // ICONO ?>
		<td><span id="elh_especies_ICONO" class="especies_ICONO"><?php echo $especies->ICONO->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->imagen_completo->Visible) { // imagen_completo ?>
		<td><span id="elh_especies_imagen_completo" class="especies_imagen_completo"><?php echo $especies->imagen_completo->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->imagen_hoja->Visible) { // imagen_hoja ?>
		<td><span id="elh_especies_imagen_hoja" class="especies_imagen_hoja"><?php echo $especies->imagen_hoja->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->imagen_flor->Visible) { // imagen_flor ?>
		<td><span id="elh_especies_imagen_flor" class="especies_imagen_flor"><?php echo $especies->imagen_flor->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->medicinal->Visible) { // medicinal ?>
		<td><span id="elh_especies_medicinal" class="especies_medicinal"><?php echo $especies->medicinal->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->comestible->Visible) { // comestible ?>
		<td><span id="elh_especies_comestible" class="especies_comestible"><?php echo $especies->comestible->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->perfume->Visible) { // perfume ?>
		<td><span id="elh_especies_perfume" class="especies_perfume"><?php echo $especies->perfume->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->avejas->Visible) { // avejas ?>
		<td><span id="elh_especies_avejas" class="especies_avejas"><?php echo $especies->avejas->FldCaption() ?></span></td>
<?php } ?>
<?php if ($especies->mariposas->Visible) { // mariposas ?>
		<td><span id="elh_especies_mariposas" class="especies_mariposas"><?php echo $especies->mariposas->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$especies_delete->RecCnt = 0;
$i = 0;
while (!$especies_delete->Recordset->EOF) {
	$especies_delete->RecCnt++;
	$especies_delete->RowCnt++;

	// Set row properties
	$especies->ResetAttrs();
	$especies->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$especies_delete->LoadRowValues($especies_delete->Recordset);

	// Render row
	$especies_delete->RenderRow();
?>
	<tr<?php echo $especies->RowAttributes() ?>>
<?php if ($especies->id_especie->Visible) { // id_especie ?>
		<td<?php echo $especies->id_especie->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_id_especie" class="control-group especies_id_especie">
<span<?php echo $especies->id_especie->ViewAttributes() ?>>
<?php echo $especies->id_especie->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($especies->id_familia->Visible) { // id_familia ?>
		<td<?php echo $especies->id_familia->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_id_familia" class="control-group especies_id_familia">
<span<?php echo $especies->id_familia->ViewAttributes() ?>>
<?php echo $especies->id_familia->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($especies->NOMBRE_CIE->Visible) { // NOMBRE_CIE ?>
		<td<?php echo $especies->NOMBRE_CIE->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_NOMBRE_CIE" class="control-group especies_NOMBRE_CIE">
<span<?php echo $especies->NOMBRE_CIE->ViewAttributes() ?>>
<?php echo $especies->NOMBRE_CIE->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($especies->NOMBRE_COM->Visible) { // NOMBRE_COM ?>
		<td<?php echo $especies->NOMBRE_COM->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_NOMBRE_COM" class="control-group especies_NOMBRE_COM">
<span<?php echo $especies->NOMBRE_COM->ViewAttributes() ?>>
<?php echo $especies->NOMBRE_COM->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($especies->TIPO_FOLLA->Visible) { // TIPO_FOLLA ?>
		<td<?php echo $especies->TIPO_FOLLA->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_TIPO_FOLLA" class="control-group especies_TIPO_FOLLA">
<span<?php echo $especies->TIPO_FOLLA->ViewAttributes() ?>>
<?php echo $especies->TIPO_FOLLA->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($especies->ORIGEN->Visible) { // ORIGEN ?>
		<td<?php echo $especies->ORIGEN->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_ORIGEN" class="control-group especies_ORIGEN">
<span<?php echo $especies->ORIGEN->ViewAttributes() ?>>
<?php echo $especies->ORIGEN->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($especies->ICONO->Visible) { // ICONO ?>
		<td<?php echo $especies->ICONO->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_ICONO" class="control-group especies_ICONO">
<span>
<?php if ($especies->ICONO->LinkAttributes() <> "") { ?>
<?php if (!empty($especies->ICONO->Upload->DbValue)) { ?>
<img src="<?php echo $especies->ICONO->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $especies->ICONO->ViewAttributes() ?>>
<?php } elseif (!in_array($especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($especies->ICONO->Upload->DbValue)) { ?>
<img src="<?php echo $especies->ICONO->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $especies->ICONO->ViewAttributes() ?>>
<?php } elseif (!in_array($especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($especies->imagen_completo->Visible) { // imagen_completo ?>
		<td<?php echo $especies->imagen_completo->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_imagen_completo" class="control-group especies_imagen_completo">
<span>
<?php if ($especies->imagen_completo->LinkAttributes() <> "") { ?>
<?php if (!empty($especies->imagen_completo->Upload->DbValue)) { ?>
<img src="<?php echo $especies->imagen_completo->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $especies->imagen_completo->ViewAttributes() ?>>
<?php } elseif (!in_array($especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($especies->imagen_completo->Upload->DbValue)) { ?>
<img src="<?php echo $especies->imagen_completo->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $especies->imagen_completo->ViewAttributes() ?>>
<?php } elseif (!in_array($especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($especies->imagen_hoja->Visible) { // imagen_hoja ?>
		<td<?php echo $especies->imagen_hoja->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_imagen_hoja" class="control-group especies_imagen_hoja">
<span>
<?php if ($especies->imagen_hoja->LinkAttributes() <> "") { ?>
<?php if (!empty($especies->imagen_hoja->Upload->DbValue)) { ?>
<img src="<?php echo $especies->imagen_hoja->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $especies->imagen_hoja->ViewAttributes() ?>>
<?php } elseif (!in_array($especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($especies->imagen_hoja->Upload->DbValue)) { ?>
<img src="<?php echo $especies->imagen_hoja->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $especies->imagen_hoja->ViewAttributes() ?>>
<?php } elseif (!in_array($especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($especies->imagen_flor->Visible) { // imagen_flor ?>
		<td<?php echo $especies->imagen_flor->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_imagen_flor" class="control-group especies_imagen_flor">
<span>
<?php if ($especies->imagen_flor->LinkAttributes() <> "") { ?>
<?php if (!empty($especies->imagen_flor->Upload->DbValue)) { ?>
<img src="<?php echo $especies->imagen_flor->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $especies->imagen_flor->ViewAttributes() ?>>
<?php } elseif (!in_array($especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($especies->imagen_flor->Upload->DbValue)) { ?>
<img src="<?php echo $especies->imagen_flor->ListViewValue() ?>" alt="" style="border: 0;"<?php echo $especies->imagen_flor->ViewAttributes() ?>>
<?php } elseif (!in_array($especies->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span>
</td>
<?php } ?>
<?php if ($especies->medicinal->Visible) { // medicinal ?>
		<td<?php echo $especies->medicinal->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_medicinal" class="control-group especies_medicinal">
<span<?php echo $especies->medicinal->ViewAttributes() ?>>
<?php echo $especies->medicinal->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($especies->comestible->Visible) { // comestible ?>
		<td<?php echo $especies->comestible->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_comestible" class="control-group especies_comestible">
<span<?php echo $especies->comestible->ViewAttributes() ?>>
<?php echo $especies->comestible->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($especies->perfume->Visible) { // perfume ?>
		<td<?php echo $especies->perfume->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_perfume" class="control-group especies_perfume">
<span<?php echo $especies->perfume->ViewAttributes() ?>>
<?php echo $especies->perfume->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($especies->avejas->Visible) { // avejas ?>
		<td<?php echo $especies->avejas->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_avejas" class="control-group especies_avejas">
<span<?php echo $especies->avejas->ViewAttributes() ?>>
<?php echo $especies->avejas->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($especies->mariposas->Visible) { // mariposas ?>
		<td<?php echo $especies->mariposas->CellAttributes() ?>>
<span id="el<?php echo $especies_delete->RowCnt ?>_especies_mariposas" class="control-group especies_mariposas">
<span<?php echo $especies->mariposas->ViewAttributes() ?>>
<?php echo $especies->mariposas->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$especies_delete->Recordset->MoveNext();
}
$especies_delete->Recordset->Close();
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
fespeciesdelete.Init();
</script>
<?php
$especies_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$especies_delete->Page_Terminate();
?>
