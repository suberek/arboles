<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "individuosinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$individuos_delete = NULL; // Initialize page object first

class cindividuos_delete extends cindividuos {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Table name
	var $TableName = 'individuos';

	// Page object name
	var $PageObjName = 'individuos_delete';

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

		// Table object (individuos)
		if (!isset($GLOBALS["individuos"])) {
			$GLOBALS["individuos"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["individuos"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'individuos', TRUE);

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
			$this->Page_Terminate("individuoslist.php");
		}

		// Update last accessed time
		if ($UserProfile->IsValidUser(session_id())) {
			if (!$Security->IsSysAdmin())
				$UserProfile->SaveProfileToDatabase(CurrentUserName()); // Update last accessed time to user profile
		} else {
			echo $Language->Phrase("UserProfileCorrupted");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action
		$this->id_individuo->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->Page_Terminate("individuoslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in individuos class, individuosinfo.php

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
		$this->id_individuo->setDbValue($rs->fields('id_individuo'));
		$this->id_especie->setDbValue($rs->fields('id_especie'));
		if (array_key_exists('EV__id_especie', $rs->fields)) {
			$this->id_especie->VirtualValue = $rs->fields('EV__id_especie'); // Set up virtual field value
		} else {
			$this->id_especie->VirtualValue = ""; // Clear value
		}
		$this->calle->setDbValue($rs->fields('calle'));
		$this->alt_ini->setDbValue($rs->fields('alt_ini'));
		$this->ALTURA_TOT->setDbValue($rs->fields('ALTURA_TOT'));
		$this->DIAMETRO->setDbValue($rs->fields('DIAMETRO'));
		$this->INCLINACIO->setDbValue($rs->fields('INCLINACIO'));
		$this->lat->setDbValue($rs->fields('lat'));
		$this->lng->setDbValue($rs->fields('lng'));
		$this->espacio_verde->setDbValue($rs->fields('espacio_verde'));
		$this->id_usuario->setDbValue($rs->fields('id_usuario'));
		if (array_key_exists('EV__id_usuario', $rs->fields)) {
			$this->id_usuario->VirtualValue = $rs->fields('EV__id_usuario'); // Set up virtual field value
		} else {
			$this->id_usuario->VirtualValue = ""; // Clear value
		}
		$this->fecha_creacion->setDbValue($rs->fields('fecha_creacion'));
		$this->fecha_modificacion->setDbValue($rs->fields('fecha_modificacion'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id_individuo->DbValue = $row['id_individuo'];
		$this->id_especie->DbValue = $row['id_especie'];
		$this->calle->DbValue = $row['calle'];
		$this->alt_ini->DbValue = $row['alt_ini'];
		$this->ALTURA_TOT->DbValue = $row['ALTURA_TOT'];
		$this->DIAMETRO->DbValue = $row['DIAMETRO'];
		$this->INCLINACIO->DbValue = $row['INCLINACIO'];
		$this->lat->DbValue = $row['lat'];
		$this->lng->DbValue = $row['lng'];
		$this->espacio_verde->DbValue = $row['espacio_verde'];
		$this->id_usuario->DbValue = $row['id_usuario'];
		$this->fecha_creacion->DbValue = $row['fecha_creacion'];
		$this->fecha_modificacion->DbValue = $row['fecha_modificacion'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// id_individuo
		// id_especie
		// calle
		// alt_ini
		// ALTURA_TOT
		// DIAMETRO
		// INCLINACIO
		// lat
		// lng
		// espacio_verde
		// id_usuario
		// fecha_creacion
		// fecha_modificacion

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id_individuo
			$this->id_individuo->ViewValue = $this->id_individuo->CurrentValue;
			$this->id_individuo->ViewCustomAttributes = "";

			// id_especie
			if ($this->id_especie->VirtualValue <> "") {
				$this->id_especie->ViewValue = $this->id_especie->VirtualValue;
			} else {
				$this->id_especie->ViewValue = $this->id_especie->CurrentValue;
			if (strval($this->id_especie->CurrentValue) <> "") {
				$sFilterWrk = "`id_especie`" . ew_SearchString("=", $this->id_especie->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id_especie`, `NOMBRE_CIE` AS `DispFld`, `NOMBRE_COM` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `especies`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->id_especie, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `NOMBRE_CIE`";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->id_especie->ViewValue = $rswrk->fields('DispFld');
					$this->id_especie->ViewValue .= ew_ValueSeparator(1,$this->id_especie) . $rswrk->fields('Disp2Fld');
					$rswrk->Close();
				} else {
					$this->id_especie->ViewValue = $this->id_especie->CurrentValue;
				}
			} else {
				$this->id_especie->ViewValue = NULL;
			}
			}
			$this->id_especie->ViewCustomAttributes = "";

			// calle
			$this->calle->ViewValue = $this->calle->CurrentValue;
			$this->calle->ViewCustomAttributes = "";

			// alt_ini
			$this->alt_ini->ViewValue = $this->alt_ini->CurrentValue;
			$this->alt_ini->ViewCustomAttributes = "";

			// ALTURA_TOT
			$this->ALTURA_TOT->ViewValue = $this->ALTURA_TOT->CurrentValue;
			$this->ALTURA_TOT->ViewCustomAttributes = "";

			// DIAMETRO
			$this->DIAMETRO->ViewValue = $this->DIAMETRO->CurrentValue;
			$this->DIAMETRO->ViewCustomAttributes = "";

			// INCLINACIO
			$this->INCLINACIO->ViewValue = $this->INCLINACIO->CurrentValue;
			$this->INCLINACIO->ViewCustomAttributes = "";

			// lat
			$this->lat->ViewValue = $this->lat->CurrentValue;
			$this->lat->ViewCustomAttributes = "";

			// lng
			$this->lng->ViewValue = $this->lng->CurrentValue;
			$this->lng->ViewCustomAttributes = "";

			// espacio_verde
			$this->espacio_verde->ViewValue = $this->espacio_verde->CurrentValue;
			$this->espacio_verde->ViewCustomAttributes = "";

			// id_usuario
			if ($this->id_usuario->VirtualValue <> "") {
				$this->id_usuario->ViewValue = $this->id_usuario->VirtualValue;
			} else {
			if (strval($this->id_usuario->CurrentValue) <> "") {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->id_usuario->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id`, `nombre_completo` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `usuarios`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->id_usuario, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `nombre_completo`";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->id_usuario->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->id_usuario->ViewValue = $this->id_usuario->CurrentValue;
				}
			} else {
				$this->id_usuario->ViewValue = NULL;
			}
			}
			$this->id_usuario->ViewCustomAttributes = "";

			// fecha_creacion
			$this->fecha_creacion->ViewValue = $this->fecha_creacion->CurrentValue;
			$this->fecha_creacion->ViewValue = ew_FormatDateTime($this->fecha_creacion->ViewValue, 7);
			$this->fecha_creacion->ViewCustomAttributes = "";

			// fecha_modificacion
			$this->fecha_modificacion->ViewValue = $this->fecha_modificacion->CurrentValue;
			$this->fecha_modificacion->ViewValue = ew_FormatDateTime($this->fecha_modificacion->ViewValue, 7);
			$this->fecha_modificacion->ViewCustomAttributes = "";

			// id_individuo
			$this->id_individuo->LinkCustomAttributes = "";
			$this->id_individuo->HrefValue = "";
			$this->id_individuo->TooltipValue = "";

			// id_especie
			$this->id_especie->LinkCustomAttributes = "";
			$this->id_especie->HrefValue = "";
			$this->id_especie->TooltipValue = "";

			// calle
			$this->calle->LinkCustomAttributes = "";
			$this->calle->HrefValue = "";
			$this->calle->TooltipValue = "";

			// alt_ini
			$this->alt_ini->LinkCustomAttributes = "";
			$this->alt_ini->HrefValue = "";
			$this->alt_ini->TooltipValue = "";

			// espacio_verde
			$this->espacio_verde->LinkCustomAttributes = "";
			$this->espacio_verde->HrefValue = "";
			$this->espacio_verde->TooltipValue = "";

			// id_usuario
			$this->id_usuario->LinkCustomAttributes = "";
			$this->id_usuario->HrefValue = "";
			$this->id_usuario->TooltipValue = "";

			// fecha_creacion
			$this->fecha_creacion->LinkCustomAttributes = "";
			$this->fecha_creacion->HrefValue = "";
			$this->fecha_creacion->TooltipValue = "";

			// fecha_modificacion
			$this->fecha_modificacion->LinkCustomAttributes = "";
			$this->fecha_modificacion->HrefValue = "";
			$this->fecha_modificacion->TooltipValue = "";
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
				$sThisKey .= $row['id_individuo'];
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "individuoslist.php", $this->TableVar);
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
if (!isset($individuos_delete)) $individuos_delete = new cindividuos_delete();

// Page init
$individuos_delete->Page_Init();

// Page main
$individuos_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$individuos_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var individuos_delete = new ew_Page("individuos_delete");
individuos_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = individuos_delete.PageID; // For backward compatibility

// Form object
var findividuosdelete = new ew_Form("findividuosdelete");

// Form_CustomValidate event
findividuosdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
findividuosdelete.ValidateRequired = true;
<?php } else { ?>
findividuosdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
findividuosdelete.Lists["x_id_especie"] = {"LinkField":"x_id_especie","Ajax":true,"AutoFill":false,"DisplayFields":["x_NOMBRE_CIE","x_NOMBRE_COM","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
findividuosdelete.Lists["x_id_usuario"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_completo","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($individuos_delete->Recordset = $individuos_delete->LoadRecordset())
	$individuos_deleteTotalRecs = $individuos_delete->Recordset->RecordCount(); // Get record count
if ($individuos_deleteTotalRecs <= 0) { // No record found, exit
	if ($individuos_delete->Recordset)
		$individuos_delete->Recordset->Close();
	$individuos_delete->Page_Terminate("individuoslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $individuos_delete->ShowPageHeader(); ?>
<?php
$individuos_delete->ShowMessage();
?>
<form name="findividuosdelete" id="findividuosdelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="individuos">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($individuos_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_individuosdelete" class="ewTable ewTableSeparate">
<?php echo $individuos->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($individuos->id_individuo->Visible) { // id_individuo ?>
		<td><span id="elh_individuos_id_individuo" class="individuos_id_individuo"><?php echo $individuos->id_individuo->FldCaption() ?></span></td>
<?php } ?>
<?php if ($individuos->id_especie->Visible) { // id_especie ?>
		<td><span id="elh_individuos_id_especie" class="individuos_id_especie"><?php echo $individuos->id_especie->FldCaption() ?></span></td>
<?php } ?>
<?php if ($individuos->calle->Visible) { // calle ?>
		<td><span id="elh_individuos_calle" class="individuos_calle"><?php echo $individuos->calle->FldCaption() ?></span></td>
<?php } ?>
<?php if ($individuos->alt_ini->Visible) { // alt_ini ?>
		<td><span id="elh_individuos_alt_ini" class="individuos_alt_ini"><?php echo $individuos->alt_ini->FldCaption() ?></span></td>
<?php } ?>
<?php if ($individuos->espacio_verde->Visible) { // espacio_verde ?>
		<td><span id="elh_individuos_espacio_verde" class="individuos_espacio_verde"><?php echo $individuos->espacio_verde->FldCaption() ?></span></td>
<?php } ?>
<?php if ($individuos->id_usuario->Visible) { // id_usuario ?>
		<td><span id="elh_individuos_id_usuario" class="individuos_id_usuario"><?php echo $individuos->id_usuario->FldCaption() ?></span></td>
<?php } ?>
<?php if ($individuos->fecha_creacion->Visible) { // fecha_creacion ?>
		<td><span id="elh_individuos_fecha_creacion" class="individuos_fecha_creacion"><?php echo $individuos->fecha_creacion->FldCaption() ?></span></td>
<?php } ?>
<?php if ($individuos->fecha_modificacion->Visible) { // fecha_modificacion ?>
		<td><span id="elh_individuos_fecha_modificacion" class="individuos_fecha_modificacion"><?php echo $individuos->fecha_modificacion->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$individuos_delete->RecCnt = 0;
$i = 0;
while (!$individuos_delete->Recordset->EOF) {
	$individuos_delete->RecCnt++;
	$individuos_delete->RowCnt++;

	// Set row properties
	$individuos->ResetAttrs();
	$individuos->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$individuos_delete->LoadRowValues($individuos_delete->Recordset);

	// Render row
	$individuos_delete->RenderRow();
?>
	<tr<?php echo $individuos->RowAttributes() ?>>
<?php if ($individuos->id_individuo->Visible) { // id_individuo ?>
		<td<?php echo $individuos->id_individuo->CellAttributes() ?>>
<span id="el<?php echo $individuos_delete->RowCnt ?>_individuos_id_individuo" class="control-group individuos_id_individuo">
<span<?php echo $individuos->id_individuo->ViewAttributes() ?>>
<?php echo $individuos->id_individuo->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($individuos->id_especie->Visible) { // id_especie ?>
		<td<?php echo $individuos->id_especie->CellAttributes() ?>>
<span id="el<?php echo $individuos_delete->RowCnt ?>_individuos_id_especie" class="control-group individuos_id_especie">
<span<?php echo $individuos->id_especie->ViewAttributes() ?>>
<?php echo $individuos->id_especie->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($individuos->calle->Visible) { // calle ?>
		<td<?php echo $individuos->calle->CellAttributes() ?>>
<span id="el<?php echo $individuos_delete->RowCnt ?>_individuos_calle" class="control-group individuos_calle">
<span<?php echo $individuos->calle->ViewAttributes() ?>>
<?php echo $individuos->calle->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($individuos->alt_ini->Visible) { // alt_ini ?>
		<td<?php echo $individuos->alt_ini->CellAttributes() ?>>
<span id="el<?php echo $individuos_delete->RowCnt ?>_individuos_alt_ini" class="control-group individuos_alt_ini">
<span<?php echo $individuos->alt_ini->ViewAttributes() ?>>
<?php echo $individuos->alt_ini->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($individuos->espacio_verde->Visible) { // espacio_verde ?>
		<td<?php echo $individuos->espacio_verde->CellAttributes() ?>>
<span id="el<?php echo $individuos_delete->RowCnt ?>_individuos_espacio_verde" class="control-group individuos_espacio_verde">
<span<?php echo $individuos->espacio_verde->ViewAttributes() ?>>
<?php echo $individuos->espacio_verde->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($individuos->id_usuario->Visible) { // id_usuario ?>
		<td<?php echo $individuos->id_usuario->CellAttributes() ?>>
<span id="el<?php echo $individuos_delete->RowCnt ?>_individuos_id_usuario" class="control-group individuos_id_usuario">
<span<?php echo $individuos->id_usuario->ViewAttributes() ?>>
<?php echo $individuos->id_usuario->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($individuos->fecha_creacion->Visible) { // fecha_creacion ?>
		<td<?php echo $individuos->fecha_creacion->CellAttributes() ?>>
<span id="el<?php echo $individuos_delete->RowCnt ?>_individuos_fecha_creacion" class="control-group individuos_fecha_creacion">
<span<?php echo $individuos->fecha_creacion->ViewAttributes() ?>>
<?php echo $individuos->fecha_creacion->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($individuos->fecha_modificacion->Visible) { // fecha_modificacion ?>
		<td<?php echo $individuos->fecha_modificacion->CellAttributes() ?>>
<span id="el<?php echo $individuos_delete->RowCnt ?>_individuos_fecha_modificacion" class="control-group individuos_fecha_modificacion">
<span<?php echo $individuos->fecha_modificacion->ViewAttributes() ?>>
<?php echo $individuos->fecha_modificacion->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$individuos_delete->Recordset->MoveNext();
}
$individuos_delete->Recordset->Close();
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
findividuosdelete.Init();
</script>
<?php
$individuos_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$individuos_delete->Page_Terminate();
?>
