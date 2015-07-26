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

$individuos_search = NULL; // Initialize page object first

class cindividuos_search extends cindividuos {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Table name
	var $TableName = 'individuos';

	// Page object name
	var $PageObjName = 'individuos_search';

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
			define("EW_PAGE_ID", 'search', TRUE);

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
		if (!$Security->CanSearch()) {
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

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsSearchError;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request

			// Get action
			$this->CurrentAction = $objForm->GetValue("a_search");
			switch ($this->CurrentAction) {
				case "S": // Get search criteria

					// Build search string for advanced search, remove blank field
					$this->LoadSearchValues(); // Get search values
					if ($this->ValidateSearch()) {
						$sSrchStr = $this->BuildAdvancedSearch();
					} else {
						$sSrchStr = "";
						$this->setFailureMessage($gsSearchError);
					}
					if ($sSrchStr <> "") {
						$sSrchStr = $this->UrlParm($sSrchStr);
						$this->Page_Terminate("individuoslist.php" . "?" . $sSrchStr); // Go to list page
					}
			}
		}

		// Restore search settings from Session
		if ($gsSearchError == "")
			$this->LoadAdvancedSearch();

		// Render row for search
		$this->RowType = EW_ROWTYPE_SEARCH;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Build advanced search
	function BuildAdvancedSearch() {
		$sSrchUrl = "";
		$this->BuildSearchUrl($sSrchUrl, $this->id_especie); // id_especie
		$this->BuildSearchUrl($sSrchUrl, $this->calle); // calle
		$this->BuildSearchUrl($sSrchUrl, $this->alt_ini); // alt_ini
		$this->BuildSearchUrl($sSrchUrl, $this->ALTURA_TOT); // ALTURA_TOT
		$this->BuildSearchUrl($sSrchUrl, $this->DIAMETRO); // DIAMETRO
		$this->BuildSearchUrl($sSrchUrl, $this->INCLINACIO); // INCLINACIO
		$this->BuildSearchUrl($sSrchUrl, $this->lat); // lat
		$this->BuildSearchUrl($sSrchUrl, $this->lng); // lng
		$this->BuildSearchUrl($sSrchUrl, $this->espacio_verde); // espacio_verde
		$this->BuildSearchUrl($sSrchUrl, $this->id_usuario); // id_usuario
		$this->BuildSearchUrl($sSrchUrl, $this->fecha_creacion); // fecha_creacion
		$this->BuildSearchUrl($sSrchUrl, $this->fecha_modificacion); // fecha_modificacion
		if ($sSrchUrl <> "") $sSrchUrl .= "&";
		$sSrchUrl .= "cmd=search";
		return $sSrchUrl;
	}

	// Build search URL
	function BuildSearchUrl(&$Url, &$Fld, $OprOnly=FALSE) {
		global $objForm;
		$sWrk = "";
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $objForm->GetValue("x_$FldParm");
		$FldOpr = $objForm->GetValue("z_$FldParm");
		$FldCond = $objForm->GetValue("v_$FldParm");
		$FldVal2 = $objForm->GetValue("y_$FldParm");
		$FldOpr2 = $objForm->GetValue("w_$FldParm");
		$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
		$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		$lFldDataType = ($Fld->FldIsVirtual) ? EW_DATATYPE_STRING : $Fld->FldDataType;
		if ($FldOpr == "BETWEEN") {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal) && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			}
		} else {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal));
			if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $lFldDataType)) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL" || ($FldOpr <> "" && $OprOnly && ew_IsValidOpr($FldOpr, $lFldDataType))) {
				$sWrk = "z_" . $FldParm . "=" . urlencode($FldOpr);
			}
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $lFldDataType)) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&w_" . $FldParm . "=" . urlencode($FldOpr2);
			} elseif ($FldOpr2 == "IS NULL" || $FldOpr2 == "IS NOT NULL" || ($FldOpr2 <> "" && $OprOnly && ew_IsValidOpr($FldOpr2, $lFldDataType))) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "w_" . $FldParm . "=" . urlencode($FldOpr2);
			}
		}
		if ($sWrk <> "") {
			if ($Url <> "") $Url .= "&";
			$Url .= $sWrk;
		}
	}

	function SearchValueIsNumeric($Fld, $Value) {
		if (ew_IsFloatFormat($Fld->FldType)) $Value = ew_StrToFloat($Value);
		return is_numeric($Value);
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// id_especie

		$this->id_especie->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_id_especie"));
		$this->id_especie->AdvancedSearch->SearchOperator = $objForm->GetValue("z_id_especie");

		// calle
		$this->calle->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_calle"));
		$this->calle->AdvancedSearch->SearchOperator = $objForm->GetValue("z_calle");

		// alt_ini
		$this->alt_ini->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_alt_ini"));
		$this->alt_ini->AdvancedSearch->SearchOperator = $objForm->GetValue("z_alt_ini");

		// ALTURA_TOT
		$this->ALTURA_TOT->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ALTURA_TOT"));
		$this->ALTURA_TOT->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ALTURA_TOT");

		// DIAMETRO
		$this->DIAMETRO->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_DIAMETRO"));
		$this->DIAMETRO->AdvancedSearch->SearchOperator = $objForm->GetValue("z_DIAMETRO");

		// INCLINACIO
		$this->INCLINACIO->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_INCLINACIO"));
		$this->INCLINACIO->AdvancedSearch->SearchOperator = $objForm->GetValue("z_INCLINACIO");

		// lat
		$this->lat->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_lat"));
		$this->lat->AdvancedSearch->SearchOperator = $objForm->GetValue("z_lat");

		// lng
		$this->lng->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_lng"));
		$this->lng->AdvancedSearch->SearchOperator = $objForm->GetValue("z_lng");

		// espacio_verde
		$this->espacio_verde->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_espacio_verde"));
		$this->espacio_verde->AdvancedSearch->SearchOperator = $objForm->GetValue("z_espacio_verde");

		// id_usuario
		$this->id_usuario->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_id_usuario"));
		$this->id_usuario->AdvancedSearch->SearchOperator = $objForm->GetValue("z_id_usuario");

		// fecha_creacion
		$this->fecha_creacion->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_fecha_creacion"));
		$this->fecha_creacion->AdvancedSearch->SearchOperator = $objForm->GetValue("z_fecha_creacion");

		// fecha_modificacion
		$this->fecha_modificacion->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_fecha_modificacion"));
		$this->fecha_modificacion->AdvancedSearch->SearchOperator = $objForm->GetValue("z_fecha_modificacion");
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Convert decimal values if posted back

		if ($this->lat->FormValue == $this->lat->CurrentValue && is_numeric(ew_StrToFloat($this->lat->CurrentValue)))
			$this->lat->CurrentValue = ew_StrToFloat($this->lat->CurrentValue);

		// Convert decimal values if posted back
		if ($this->lng->FormValue == $this->lng->CurrentValue && is_numeric(ew_StrToFloat($this->lng->CurrentValue)))
			$this->lng->CurrentValue = ew_StrToFloat($this->lng->CurrentValue);

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

			// ALTURA_TOT
			$this->ALTURA_TOT->LinkCustomAttributes = "";
			$this->ALTURA_TOT->HrefValue = "";
			$this->ALTURA_TOT->TooltipValue = "";

			// DIAMETRO
			$this->DIAMETRO->LinkCustomAttributes = "";
			$this->DIAMETRO->HrefValue = "";
			$this->DIAMETRO->TooltipValue = "";

			// INCLINACIO
			$this->INCLINACIO->LinkCustomAttributes = "";
			$this->INCLINACIO->HrefValue = "";
			$this->INCLINACIO->TooltipValue = "";

			// lat
			$this->lat->LinkCustomAttributes = "";
			$this->lat->HrefValue = "";
			$this->lat->TooltipValue = "";

			// lng
			$this->lng->LinkCustomAttributes = "";
			$this->lng->HrefValue = "";
			$this->lng->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// id_especie
			$this->id_especie->EditCustomAttributes = "";
			$this->id_especie->EditValue = ew_HtmlEncode($this->id_especie->AdvancedSearch->SearchValue);
			if (strval($this->id_especie->AdvancedSearch->SearchValue) <> "") {
				$sFilterWrk = "`id_especie`" . ew_SearchString("=", $this->id_especie->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
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
					$this->id_especie->EditValue = $rswrk->fields('DispFld');
					$this->id_especie->EditValue .= ew_ValueSeparator(1,$this->id_especie) . $rswrk->fields('Disp2Fld');
					$rswrk->Close();
				} else {
					$this->id_especie->EditValue = $this->id_especie->AdvancedSearch->SearchValue;
				}
			} else {
				$this->id_especie->EditValue = NULL;
			}
			$this->id_especie->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->id_especie->FldCaption()));

			// calle
			$this->calle->EditCustomAttributes = "";
			$this->calle->EditValue = ew_HtmlEncode($this->calle->AdvancedSearch->SearchValue);
			$this->calle->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->calle->FldCaption()));

			// alt_ini
			$this->alt_ini->EditCustomAttributes = "";
			$this->alt_ini->EditValue = ew_HtmlEncode($this->alt_ini->AdvancedSearch->SearchValue);
			$this->alt_ini->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->alt_ini->FldCaption()));

			// ALTURA_TOT
			$this->ALTURA_TOT->EditCustomAttributes = "";
			$this->ALTURA_TOT->EditValue = ew_HtmlEncode($this->ALTURA_TOT->AdvancedSearch->SearchValue);
			$this->ALTURA_TOT->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->ALTURA_TOT->FldCaption()));

			// DIAMETRO
			$this->DIAMETRO->EditCustomAttributes = "";
			$this->DIAMETRO->EditValue = ew_HtmlEncode($this->DIAMETRO->AdvancedSearch->SearchValue);
			$this->DIAMETRO->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->DIAMETRO->FldCaption()));

			// INCLINACIO
			$this->INCLINACIO->EditCustomAttributes = "";
			$this->INCLINACIO->EditValue = ew_HtmlEncode($this->INCLINACIO->AdvancedSearch->SearchValue);
			$this->INCLINACIO->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->INCLINACIO->FldCaption()));

			// lat
			$this->lat->EditCustomAttributes = "";
			$this->lat->EditValue = ew_HtmlEncode($this->lat->AdvancedSearch->SearchValue);
			$this->lat->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->lat->FldCaption()));

			// lng
			$this->lng->EditCustomAttributes = "";
			$this->lng->EditValue = ew_HtmlEncode($this->lng->AdvancedSearch->SearchValue);
			$this->lng->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->lng->FldCaption()));

			// espacio_verde
			$this->espacio_verde->EditCustomAttributes = "";
			$this->espacio_verde->EditValue = ew_HtmlEncode($this->espacio_verde->AdvancedSearch->SearchValue);
			$this->espacio_verde->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->espacio_verde->FldCaption()));

			// id_usuario
			$this->id_usuario->EditCustomAttributes = "";
			$this->id_usuario->EditValue = ew_HtmlEncode($this->id_usuario->AdvancedSearch->SearchValue);
			if (strval($this->id_usuario->AdvancedSearch->SearchValue) <> "") {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->id_usuario->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
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
					$this->id_usuario->EditValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->id_usuario->EditValue = $this->id_usuario->AdvancedSearch->SearchValue;
				}
			} else {
				$this->id_usuario->EditValue = NULL;
			}
			$this->id_usuario->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->id_usuario->FldCaption()));

			// fecha_creacion
			$this->fecha_creacion->EditCustomAttributes = "";
			$this->fecha_creacion->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->fecha_creacion->AdvancedSearch->SearchValue, 7), 7));
			$this->fecha_creacion->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->fecha_creacion->FldCaption()));

			// fecha_modificacion
			$this->fecha_modificacion->EditCustomAttributes = "";
			$this->fecha_modificacion->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->fecha_modificacion->AdvancedSearch->SearchValue, 7), 7));
			$this->fecha_modificacion->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->fecha_modificacion->FldCaption()));
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

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;
		if (!ew_CheckInteger($this->alt_ini->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->alt_ini->FldErrMsg());
		}
		if (!ew_CheckInteger($this->ALTURA_TOT->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->ALTURA_TOT->FldErrMsg());
		}
		if (!ew_CheckInteger($this->DIAMETRO->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->DIAMETRO->FldErrMsg());
		}
		if (!ew_CheckInteger($this->INCLINACIO->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->INCLINACIO->FldErrMsg());
		}
		if (!ew_CheckNumber($this->lat->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->lat->FldErrMsg());
		}
		if (!ew_CheckNumber($this->lng->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->lng->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->fecha_creacion->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->fecha_creacion->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->fecha_modificacion->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->fecha_modificacion->FldErrMsg());
		}

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->id_especie->AdvancedSearch->Load();
		$this->calle->AdvancedSearch->Load();
		$this->alt_ini->AdvancedSearch->Load();
		$this->ALTURA_TOT->AdvancedSearch->Load();
		$this->DIAMETRO->AdvancedSearch->Load();
		$this->INCLINACIO->AdvancedSearch->Load();
		$this->lat->AdvancedSearch->Load();
		$this->lng->AdvancedSearch->Load();
		$this->espacio_verde->AdvancedSearch->Load();
		$this->id_usuario->AdvancedSearch->Load();
		$this->fecha_creacion->AdvancedSearch->Load();
		$this->fecha_modificacion->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "individuoslist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("search");
		$Breadcrumb->Add("search", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
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
if (!isset($individuos_search)) $individuos_search = new cindividuos_search();

// Page init
$individuos_search->Page_Init();

// Page main
$individuos_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$individuos_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var individuos_search = new ew_Page("individuos_search");
individuos_search.PageID = "search"; // Page ID
var EW_PAGE_ID = individuos_search.PageID; // For backward compatibility

// Form object
var findividuossearch = new ew_Form("findividuossearch");

// Form_CustomValidate event
findividuossearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
findividuossearch.ValidateRequired = true;
<?php } else { ?>
findividuossearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
findividuossearch.Lists["x_id_especie"] = {"LinkField":"x_id_especie","Ajax":true,"AutoFill":false,"DisplayFields":["x_NOMBRE_CIE","x_NOMBRE_COM","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
findividuossearch.Lists["x_id_usuario"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_completo","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
// Validate function for search

findividuossearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = this.GetElements("x" + infix + "_alt_ini");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($individuos->alt_ini->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_ALTURA_TOT");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($individuos->ALTURA_TOT->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_DIAMETRO");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($individuos->DIAMETRO->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_INCLINACIO");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($individuos->INCLINACIO->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_lat");
	if (elm && !ew_CheckNumber(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($individuos->lat->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_lng");
	if (elm && !ew_CheckNumber(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($individuos->lng->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_fecha_creacion");
	if (elm && !ew_CheckEuroDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($individuos->fecha_creacion->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_fecha_modificacion");
	if (elm && !ew_CheckEuroDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($individuos->fecha_modificacion->FldErrMsg()) ?>");

	// Set up row object
	ew_ElementsToRow(fobj);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
findividuossearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
findividuossearch.ValidateRequired = true; // Use JavaScript validation
<?php } else { ?>
findividuossearch.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Dynamic selection lists
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $individuos_search->ShowPageHeader(); ?>
<?php
$individuos_search->ShowMessage();
?>
<form name="findividuossearch" id="findividuossearch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="individuos">
<input type="hidden" name="a_search" id="a_search" value="S">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_individuossearch" class="table table-bordered table-striped">
<?php if ($individuos->id_especie->Visible) { // id_especie ?>
	<tr id="r_id_especie">
		<td><span id="elh_individuos_id_especie"><?php echo $individuos->id_especie->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_id_especie" id="z_id_especie" value="="></span></td>
		<td<?php echo $individuos->id_especie->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_id_especie" class="control-group">
<?php
	$wrkonchange = trim(" " . @$individuos->id_especie->EditAttrs["onchange"]);
	if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
	$individuos->id_especie->EditAttrs["onchange"] = "";
?>
<span id="as_x_id_especie" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_id_especie" id="sv_x_id_especie" value="<?php echo $individuos->id_especie->EditValue ?>" size="30" placeholder="<?php echo $individuos->id_especie->PlaceHolder ?>"<?php echo $individuos->id_especie->EditAttributes() ?>>&nbsp;<span id="em_x_id_especie" class="ewMessage" style="display: none"><?php echo str_replace("%f", "phpimages/", $Language->Phrase("UnmatchedValue")) ?></span>
	<div id="sc_x_id_especie" style="display: inline; z-index: 8980"></div>
</span>
<input type="hidden" data-field="x_id_especie" name="x_id_especie" id="x_id_especie" value="<?php echo $individuos->id_especie->AdvancedSearch->SearchValue ?>"<?php echo $wrkonchange ?>>
<?php
$sSqlWrk = "SELECT `id_especie`, `NOMBRE_CIE` AS `DispFld`, `NOMBRE_COM` AS `Disp2Fld` FROM `especies`";
$sWhereWrk = "`NOMBRE_CIE` LIKE '{query_value}%' OR CONCAT(`NOMBRE_CIE`,'" . ew_ValueSeparator(1, $Page->id_especie) . "',`NOMBRE_COM`) LIKE '{query_value}%'";

// Call Lookup selecting
$individuos->Lookup_Selecting($individuos->id_especie, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `NOMBRE_CIE`";
$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
?>
<input type="hidden" name="q_x_id_especie" id="q_x_id_especie" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>">
<script type="text/javascript">
var oas = new ew_AutoSuggest("x_id_especie", findividuossearch, false, EW_AUTO_SUGGEST_MAX_ENTRIES);
oas.formatResult = function(ar) {
	var dv = ar[1];
	for (var i = 2; i <= 4; i++)
		dv += (ar[i]) ? ew_ValueSeparator(i - 1, "x_id_especie") + ar[i] : "";
	return dv;
}
findividuossearch.AutoSuggests["x_id_especie"] = oas;
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->calle->Visible) { // calle ?>
	<tr id="r_calle">
		<td><span id="elh_individuos_calle"><?php echo $individuos->calle->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_calle" id="z_calle" value="LIKE"></span></td>
		<td<?php echo $individuos->calle->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_calle" class="control-group">
<input type="text" data-field="x_calle" name="x_calle" id="x_calle" size="30" maxlength="255" placeholder="<?php echo $individuos->calle->PlaceHolder ?>" value="<?php echo $individuos->calle->EditValue ?>"<?php echo $individuos->calle->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->alt_ini->Visible) { // alt_ini ?>
	<tr id="r_alt_ini">
		<td><span id="elh_individuos_alt_ini"><?php echo $individuos->alt_ini->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_alt_ini" id="z_alt_ini" value="="></span></td>
		<td<?php echo $individuos->alt_ini->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_alt_ini" class="control-group">
<input type="text" data-field="x_alt_ini" name="x_alt_ini" id="x_alt_ini" size="30" placeholder="<?php echo $individuos->alt_ini->PlaceHolder ?>" value="<?php echo $individuos->alt_ini->EditValue ?>"<?php echo $individuos->alt_ini->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->ALTURA_TOT->Visible) { // ALTURA_TOT ?>
	<tr id="r_ALTURA_TOT">
		<td><span id="elh_individuos_ALTURA_TOT"><?php echo $individuos->ALTURA_TOT->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_ALTURA_TOT" id="z_ALTURA_TOT" value="="></span></td>
		<td<?php echo $individuos->ALTURA_TOT->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_ALTURA_TOT" class="control-group">
<input type="text" data-field="x_ALTURA_TOT" name="x_ALTURA_TOT" id="x_ALTURA_TOT" size="30" placeholder="<?php echo $individuos->ALTURA_TOT->PlaceHolder ?>" value="<?php echo $individuos->ALTURA_TOT->EditValue ?>"<?php echo $individuos->ALTURA_TOT->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->DIAMETRO->Visible) { // DIAMETRO ?>
	<tr id="r_DIAMETRO">
		<td><span id="elh_individuos_DIAMETRO"><?php echo $individuos->DIAMETRO->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_DIAMETRO" id="z_DIAMETRO" value="="></span></td>
		<td<?php echo $individuos->DIAMETRO->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_DIAMETRO" class="control-group">
<input type="text" data-field="x_DIAMETRO" name="x_DIAMETRO" id="x_DIAMETRO" size="30" placeholder="<?php echo $individuos->DIAMETRO->PlaceHolder ?>" value="<?php echo $individuos->DIAMETRO->EditValue ?>"<?php echo $individuos->DIAMETRO->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->INCLINACIO->Visible) { // INCLINACIO ?>
	<tr id="r_INCLINACIO">
		<td><span id="elh_individuos_INCLINACIO"><?php echo $individuos->INCLINACIO->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_INCLINACIO" id="z_INCLINACIO" value="="></span></td>
		<td<?php echo $individuos->INCLINACIO->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_INCLINACIO" class="control-group">
<input type="text" data-field="x_INCLINACIO" name="x_INCLINACIO" id="x_INCLINACIO" size="30" placeholder="<?php echo $individuos->INCLINACIO->PlaceHolder ?>" value="<?php echo $individuos->INCLINACIO->EditValue ?>"<?php echo $individuos->INCLINACIO->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->lat->Visible) { // lat ?>
	<tr id="r_lat">
		<td><span id="elh_individuos_lat"><?php echo $individuos->lat->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_lat" id="z_lat" value="="></span></td>
		<td<?php echo $individuos->lat->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_lat" class="control-group">
<input type="text" data-field="x_lat" name="x_lat" id="x_lat" size="30" placeholder="<?php echo $individuos->lat->PlaceHolder ?>" value="<?php echo $individuos->lat->EditValue ?>"<?php echo $individuos->lat->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->lng->Visible) { // lng ?>
	<tr id="r_lng">
		<td><span id="elh_individuos_lng"><?php echo $individuos->lng->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_lng" id="z_lng" value="="></span></td>
		<td<?php echo $individuos->lng->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_lng" class="control-group">
<input type="text" data-field="x_lng" name="x_lng" id="x_lng" size="30" placeholder="<?php echo $individuos->lng->PlaceHolder ?>" value="<?php echo $individuos->lng->EditValue ?>"<?php echo $individuos->lng->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->espacio_verde->Visible) { // espacio_verde ?>
	<tr id="r_espacio_verde">
		<td><span id="elh_individuos_espacio_verde"><?php echo $individuos->espacio_verde->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_espacio_verde" id="z_espacio_verde" value="LIKE"></span></td>
		<td<?php echo $individuos->espacio_verde->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_espacio_verde" class="control-group">
<input type="text" data-field="x_espacio_verde" name="x_espacio_verde" id="x_espacio_verde" size="30" maxlength="255" placeholder="<?php echo $individuos->espacio_verde->PlaceHolder ?>" value="<?php echo $individuos->espacio_verde->EditValue ?>"<?php echo $individuos->espacio_verde->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->id_usuario->Visible) { // id_usuario ?>
	<tr id="r_id_usuario">
		<td><span id="elh_individuos_id_usuario"><?php echo $individuos->id_usuario->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_id_usuario" id="z_id_usuario" value="="></span></td>
		<td<?php echo $individuos->id_usuario->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_id_usuario" class="control-group">
<input type="text" data-field="x_id_usuario" name="x_id_usuario" id="x_id_usuario" size="30" placeholder="<?php echo $individuos->id_usuario->PlaceHolder ?>" value="<?php echo $individuos->id_usuario->EditValue ?>"<?php echo $individuos->id_usuario->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->fecha_creacion->Visible) { // fecha_creacion ?>
	<tr id="r_fecha_creacion">
		<td><span id="elh_individuos_fecha_creacion"><?php echo $individuos->fecha_creacion->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_fecha_creacion" id="z_fecha_creacion" value="="></span></td>
		<td<?php echo $individuos->fecha_creacion->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_fecha_creacion" class="control-group">
<input type="text" data-field="x_fecha_creacion" name="x_fecha_creacion" id="x_fecha_creacion" placeholder="<?php echo $individuos->fecha_creacion->PlaceHolder ?>" value="<?php echo $individuos->fecha_creacion->EditValue ?>"<?php echo $individuos->fecha_creacion->EditAttributes() ?>>
<?php if (!$individuos->fecha_creacion->ReadOnly && !$individuos->fecha_creacion->Disabled && @$individuos->fecha_creacion->EditAttrs["readonly"] == "" && @$individuos->fecha_creacion->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_fecha_creacion" name="cal_x_fecha_creacion" class="btn" type="button"><img src="phpimages/calendar.png" id="cal_x_fecha_creacion" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("findividuossearch", "x_fecha_creacion", "%d/%m/%Y");
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($individuos->fecha_modificacion->Visible) { // fecha_modificacion ?>
	<tr id="r_fecha_modificacion">
		<td><span id="elh_individuos_fecha_modificacion"><?php echo $individuos->fecha_modificacion->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_fecha_modificacion" id="z_fecha_modificacion" value="="></span></td>
		<td<?php echo $individuos->fecha_modificacion->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_individuos_fecha_modificacion" class="control-group">
<input type="text" data-field="x_fecha_modificacion" name="x_fecha_modificacion" id="x_fecha_modificacion" placeholder="<?php echo $individuos->fecha_modificacion->PlaceHolder ?>" value="<?php echo $individuos->fecha_modificacion->EditValue ?>"<?php echo $individuos->fecha_modificacion->EditAttributes() ?>>
<?php if (!$individuos->fecha_modificacion->ReadOnly && !$individuos->fecha_modificacion->Disabled && @$individuos->fecha_modificacion->EditAttrs["readonly"] == "" && @$individuos->fecha_modificacion->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_fecha_modificacion" name="cal_x_fecha_modificacion" class="btn" type="button"><img src="phpimages/calendar.png" id="cal_x_fecha_modificacion" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("findividuossearch", "x_fecha_modificacion", "%d/%m/%Y");
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
</form>
<script type="text/javascript">
findividuossearch.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$individuos_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$individuos_search->Page_Terminate();
?>
