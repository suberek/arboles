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

$p_2_especies_list = NULL; // Initialize page object first

class cp_2_especies_list extends c_2_especies {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Table name
	var $TableName = '2_especies';

	// Page object name
	var $PageObjName = 'p_2_especies_list';

	// Grid form hidden field names
	var $FormName = 'f_2_especieslist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "_2_especiesadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "_2_especiesdelete.php";
		$this->MultiUpdateUrl = "_2_especiesupdate.php";

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", '2_especies', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "span";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "span";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "span";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "span";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("login.php");
		}

		// Update last accessed time
		if ($UserProfile->IsValidUser(session_id())) {
			if (!$Security->IsSysAdmin())
				$UserProfile->SaveProfileToDatabase(CurrentUserName()); // Update last accessed time to user profile
		} else {
			echo $Language->Phrase("UserProfileCorrupted");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Setup other options
		$this->SetupOtherOptions();

		// Set "checkbox" visible
		if (count($this->CustomActions) > 0)
			$this->ListOptions->Items["checkbox"]->Visible = TRUE;
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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide export options
			if ($this->Export <> "" || $this->CurrentAction <> "")
				$this->ExportOptions->HideAllOptions();

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Restore search parms from Session if not searching / reset
			if ($this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall" && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue("k_key"));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue("k_key"));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->id_especie->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->id_especie->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->NOMBRE_FAM, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->NOMBRE_CIE, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->NOMBRE_COM, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->TIPO_FOLLA, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->ORIGEN, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->ICONO, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->imagen_completo, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->imagen_hoja, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->imagen_flor, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->descripcion, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->medicinal, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->comestible, $Keyword);
		if (is_numeric($Keyword)) $this->BuildBasicSearchSQL($sWhere, $this->perfume, $Keyword);
		if (is_numeric($Keyword)) $this->BuildBasicSearchSQL($sWhere, $this->avejas, $Keyword);
		if (is_numeric($Keyword)) $this->BuildBasicSearchSQL($sWhere, $this->mariposas, $Keyword);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $Keyword) {
		if ($Keyword == EW_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NULL";
		} elseif ($Keyword == EW_NOT_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NOT NULL";
		} else {
			$sFldExpression = ($Fld->FldVirtualExpression <> $Fld->FldExpression) ? $Fld->FldVirtualExpression : $Fld->FldBasicSearchExpression;
			$sWrk = $sFldExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING));
		}
		if ($Where <> "") $Where .= " OR ";
		$Where .= $sWrk;
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere() {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = $this->BasicSearch->Keyword;
		$sSearchType = $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				while (strpos($sSearch, "  ") !== FALSE)
					$sSearch = str_replace("  ", " ", $sSearch);
				$arKeyword = explode(" ", trim($sSearch));
				foreach ($arKeyword as $sKeyword) {
					if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
					$sSearchStr .= "(" . $this->BasicSearchSQL($sKeyword) . ")";
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL($sSearch);
			}
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->id_especie); // id_especie
			$this->UpdateSort($this->NOMBRE_FAM); // NOMBRE_FAM
			$this->UpdateSort($this->NOMBRE_CIE); // NOMBRE_CIE
			$this->UpdateSort($this->NOMBRE_COM); // NOMBRE_COM
			$this->UpdateSort($this->TIPO_FOLLA); // TIPO_FOLLA
			$this->UpdateSort($this->ORIGEN); // ORIGEN
			$this->UpdateSort($this->ICONO); // ICONO
			$this->UpdateSort($this->imagen_completo); // imagen_completo
			$this->UpdateSort($this->imagen_hoja); // imagen_hoja
			$this->UpdateSort($this->imagen_flor); // imagen_flor
			$this->UpdateSort($this->medicinal); // medicinal
			$this->UpdateSort($this->comestible); // comestible
			$this->UpdateSort($this->perfume); // perfume
			$this->UpdateSort($this->avejas); // avejas
			$this->UpdateSort($this->mariposas); // mariposas
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->SqlOrderBy() <> "") {
				$sOrderBy = $this->SqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->id_especie->setSort("");
				$this->NOMBRE_FAM->setSort("");
				$this->NOMBRE_CIE->setSort("");
				$this->NOMBRE_COM->setSort("");
				$this->TIPO_FOLLA->setSort("");
				$this->ORIGEN->setSort("");
				$this->ICONO->setSort("");
				$this->imagen_completo->setSort("");
				$this->imagen_hoja->setSort("");
				$this->imagen_flor->setSort("");
				$this->medicinal->setSort("");
				$this->comestible->setSort("");
				$this->perfume->setSort("");
				$this->avejas->setSort("");
				$this->mariposas->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanView();
		$item->OnLeft = FALSE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = FALSE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanAdd();
		$item->OnLeft = FALSE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = FALSE;
		$item->Header = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\"></label>";
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		$this->ListOptions->ButtonClass = "btn-small"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->CanView())
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->CanAdd()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewCopy\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CopyLink")) . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->CanDelete())
			$oListOpt->Body = "<a class=\"ewRowLink ewDelete\"" . "" . " data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->id_especie->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAddEdit ewAdd\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-small"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];
			foreach ($this->CustomActions as $action => $name) {

				// Add custom action
				$item = &$option->Add("custom_" . $action);
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.f_2_especieslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
			}

			// Hide grid edit, multi-delete and multi-update
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$item = &$option->GetItem("multidelete");
				if ($item) $item->Visible = FALSE;
				$item = &$option->GetItem("multiupdate");
				if ($item) $item->Visible = FALSE;
			}
	}

	// Process custom action
	function ProcessCustomAction() {
		global $conn, $Language, $Security;
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$rsuser = ($rs) ? $rs->GetRows() : array();
			if ($rs)
				$rs->Close();

			// Call row custom action event
			if (count($rsuser) > 0) {
				$conn->BeginTrans();
				foreach ($rsuser as $row) {
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCancelled")));
					}
				}
			}
		}
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", $url, $this->TableVar);
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

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($p_2_especies_list)) $p_2_especies_list = new cp_2_especies_list();

// Page init
$p_2_especies_list->Page_Init();

// Page main
$p_2_especies_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$p_2_especies_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var p_2_especies_list = new ew_Page("p_2_especies_list");
p_2_especies_list.PageID = "list"; // Page ID
var EW_PAGE_ID = p_2_especies_list.PageID; // For backward compatibility

// Form object
var f_2_especieslist = new ew_Form("f_2_especieslist");
f_2_especieslist.FormKeyCountName = '<?php echo $p_2_especies_list->FormKeyCountName ?>';

// Form_CustomValidate event
f_2_especieslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
f_2_especieslist.ValidateRequired = true;
<?php } else { ?>
f_2_especieslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

var f_2_especieslistsrch = new ew_Form("f_2_especieslistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php if ($p_2_especies_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $p_2_especies_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$p_2_especies_list->TotalRecs = $_2_especies->SelectRecordCount();
	} else {
		if ($p_2_especies_list->Recordset = $p_2_especies_list->LoadRecordset())
			$p_2_especies_list->TotalRecs = $p_2_especies_list->Recordset->RecordCount();
	}
	$p_2_especies_list->StartRec = 1;
	if ($p_2_especies_list->DisplayRecs <= 0 || ($_2_especies->Export <> "" && $_2_especies->ExportAll)) // Display all records
		$p_2_especies_list->DisplayRecs = $p_2_especies_list->TotalRecs;
	if (!($_2_especies->Export <> "" && $_2_especies->ExportAll))
		$p_2_especies_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$p_2_especies_list->Recordset = $p_2_especies_list->LoadRecordset($p_2_especies_list->StartRec-1, $p_2_especies_list->DisplayRecs);
$p_2_especies_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($_2_especies->Export == "" && $_2_especies->CurrentAction == "") { ?>
<form name="f_2_especieslistsrch" id="f_2_especieslistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewSearchTable"><tr><td>
<div class="accordion" id="f_2_especieslistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#f_2_especieslistsrch_SearchGroup" href="#f_2_especieslistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="f_2_especieslistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="f_2_especieslistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="_2_especies">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($p_2_especies_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo $Language->Phrase("Search") ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $p_2_especies_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($p_2_especies_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($p_2_especies_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($p_2_especies_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
</div>
</div>
</div>
			</div>
		</div>
	</div>
</div>
</td></tr></table>
</form>
<?php } ?>
<?php } ?>
<?php $p_2_especies_list->ShowPageHeader(); ?>
<?php
$p_2_especies_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($_2_especies->CurrentAction <> "gridadd" && $_2_especies->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($p_2_especies_list->Pager)) $p_2_especies_list->Pager = new cPrevNextPager($p_2_especies_list->StartRec, $p_2_especies_list->DisplayRecs, $p_2_especies_list->TotalRecs) ?>
<?php if ($p_2_especies_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($p_2_especies_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_2_especies_list->PageUrl() ?>start=<?php echo $p_2_especies_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($p_2_especies_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_2_especies_list->PageUrl() ?>start=<?php echo $p_2_especies_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $p_2_especies_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($p_2_especies_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_2_especies_list->PageUrl() ?>start=<?php echo $p_2_especies_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($p_2_especies_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_2_especies_list->PageUrl() ?>start=<?php echo $p_2_especies_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $p_2_especies_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $p_2_especies_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $p_2_especies_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $p_2_especies_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($p_2_especies_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($p_2_especies_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="f_2_especieslist" id="f_2_especieslist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="_2_especies">
<div id="gmp__2_especies" class="ewGridMiddlePanel">
<?php if ($p_2_especies_list->TotalRecs > 0) { ?>
<table id="tbl__2_especieslist" class="ewTable ewTableSeparate">
<?php echo $_2_especies->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$p_2_especies_list->RenderListOptions();

// Render list options (header, left)
$p_2_especies_list->ListOptions->Render("header", "left");
?>
<?php if ($_2_especies->id_especie->Visible) { // id_especie ?>
	<?php if ($_2_especies->SortUrl($_2_especies->id_especie) == "") { ?>
		<td><div id="elh__2_especies_id_especie" class="_2_especies_id_especie"><div class="ewTableHeaderCaption"><?php echo $_2_especies->id_especie->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->id_especie) ?>',1);"><div id="elh__2_especies_id_especie" class="_2_especies_id_especie">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->id_especie->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->id_especie->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->id_especie->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->NOMBRE_FAM->Visible) { // NOMBRE_FAM ?>
	<?php if ($_2_especies->SortUrl($_2_especies->NOMBRE_FAM) == "") { ?>
		<td><div id="elh__2_especies_NOMBRE_FAM" class="_2_especies_NOMBRE_FAM"><div class="ewTableHeaderCaption"><?php echo $_2_especies->NOMBRE_FAM->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->NOMBRE_FAM) ?>',1);"><div id="elh__2_especies_NOMBRE_FAM" class="_2_especies_NOMBRE_FAM">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->NOMBRE_FAM->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->NOMBRE_FAM->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->NOMBRE_FAM->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->NOMBRE_CIE->Visible) { // NOMBRE_CIE ?>
	<?php if ($_2_especies->SortUrl($_2_especies->NOMBRE_CIE) == "") { ?>
		<td><div id="elh__2_especies_NOMBRE_CIE" class="_2_especies_NOMBRE_CIE"><div class="ewTableHeaderCaption"><?php echo $_2_especies->NOMBRE_CIE->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->NOMBRE_CIE) ?>',1);"><div id="elh__2_especies_NOMBRE_CIE" class="_2_especies_NOMBRE_CIE">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->NOMBRE_CIE->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->NOMBRE_CIE->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->NOMBRE_CIE->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->NOMBRE_COM->Visible) { // NOMBRE_COM ?>
	<?php if ($_2_especies->SortUrl($_2_especies->NOMBRE_COM) == "") { ?>
		<td><div id="elh__2_especies_NOMBRE_COM" class="_2_especies_NOMBRE_COM"><div class="ewTableHeaderCaption"><?php echo $_2_especies->NOMBRE_COM->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->NOMBRE_COM) ?>',1);"><div id="elh__2_especies_NOMBRE_COM" class="_2_especies_NOMBRE_COM">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->NOMBRE_COM->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->NOMBRE_COM->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->NOMBRE_COM->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->TIPO_FOLLA->Visible) { // TIPO_FOLLA ?>
	<?php if ($_2_especies->SortUrl($_2_especies->TIPO_FOLLA) == "") { ?>
		<td><div id="elh__2_especies_TIPO_FOLLA" class="_2_especies_TIPO_FOLLA"><div class="ewTableHeaderCaption"><?php echo $_2_especies->TIPO_FOLLA->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->TIPO_FOLLA) ?>',1);"><div id="elh__2_especies_TIPO_FOLLA" class="_2_especies_TIPO_FOLLA">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->TIPO_FOLLA->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->TIPO_FOLLA->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->TIPO_FOLLA->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->ORIGEN->Visible) { // ORIGEN ?>
	<?php if ($_2_especies->SortUrl($_2_especies->ORIGEN) == "") { ?>
		<td><div id="elh__2_especies_ORIGEN" class="_2_especies_ORIGEN"><div class="ewTableHeaderCaption"><?php echo $_2_especies->ORIGEN->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->ORIGEN) ?>',1);"><div id="elh__2_especies_ORIGEN" class="_2_especies_ORIGEN">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->ORIGEN->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->ORIGEN->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->ORIGEN->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->ICONO->Visible) { // ICONO ?>
	<?php if ($_2_especies->SortUrl($_2_especies->ICONO) == "") { ?>
		<td><div id="elh__2_especies_ICONO" class="_2_especies_ICONO"><div class="ewTableHeaderCaption"><?php echo $_2_especies->ICONO->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->ICONO) ?>',1);"><div id="elh__2_especies_ICONO" class="_2_especies_ICONO">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->ICONO->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->ICONO->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->ICONO->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->imagen_completo->Visible) { // imagen_completo ?>
	<?php if ($_2_especies->SortUrl($_2_especies->imagen_completo) == "") { ?>
		<td><div id="elh__2_especies_imagen_completo" class="_2_especies_imagen_completo"><div class="ewTableHeaderCaption"><?php echo $_2_especies->imagen_completo->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->imagen_completo) ?>',1);"><div id="elh__2_especies_imagen_completo" class="_2_especies_imagen_completo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->imagen_completo->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->imagen_completo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->imagen_completo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->imagen_hoja->Visible) { // imagen_hoja ?>
	<?php if ($_2_especies->SortUrl($_2_especies->imagen_hoja) == "") { ?>
		<td><div id="elh__2_especies_imagen_hoja" class="_2_especies_imagen_hoja"><div class="ewTableHeaderCaption"><?php echo $_2_especies->imagen_hoja->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->imagen_hoja) ?>',1);"><div id="elh__2_especies_imagen_hoja" class="_2_especies_imagen_hoja">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->imagen_hoja->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->imagen_hoja->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->imagen_hoja->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->imagen_flor->Visible) { // imagen_flor ?>
	<?php if ($_2_especies->SortUrl($_2_especies->imagen_flor) == "") { ?>
		<td><div id="elh__2_especies_imagen_flor" class="_2_especies_imagen_flor"><div class="ewTableHeaderCaption"><?php echo $_2_especies->imagen_flor->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->imagen_flor) ?>',1);"><div id="elh__2_especies_imagen_flor" class="_2_especies_imagen_flor">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->imagen_flor->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->imagen_flor->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->imagen_flor->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->medicinal->Visible) { // medicinal ?>
	<?php if ($_2_especies->SortUrl($_2_especies->medicinal) == "") { ?>
		<td><div id="elh__2_especies_medicinal" class="_2_especies_medicinal"><div class="ewTableHeaderCaption"><?php echo $_2_especies->medicinal->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->medicinal) ?>',1);"><div id="elh__2_especies_medicinal" class="_2_especies_medicinal">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->medicinal->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->medicinal->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->medicinal->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->comestible->Visible) { // comestible ?>
	<?php if ($_2_especies->SortUrl($_2_especies->comestible) == "") { ?>
		<td><div id="elh__2_especies_comestible" class="_2_especies_comestible"><div class="ewTableHeaderCaption"><?php echo $_2_especies->comestible->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->comestible) ?>',1);"><div id="elh__2_especies_comestible" class="_2_especies_comestible">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->comestible->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->comestible->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->comestible->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->perfume->Visible) { // perfume ?>
	<?php if ($_2_especies->SortUrl($_2_especies->perfume) == "") { ?>
		<td><div id="elh__2_especies_perfume" class="_2_especies_perfume"><div class="ewTableHeaderCaption"><?php echo $_2_especies->perfume->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->perfume) ?>',1);"><div id="elh__2_especies_perfume" class="_2_especies_perfume">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->perfume->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->perfume->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->perfume->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->avejas->Visible) { // avejas ?>
	<?php if ($_2_especies->SortUrl($_2_especies->avejas) == "") { ?>
		<td><div id="elh__2_especies_avejas" class="_2_especies_avejas"><div class="ewTableHeaderCaption"><?php echo $_2_especies->avejas->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->avejas) ?>',1);"><div id="elh__2_especies_avejas" class="_2_especies_avejas">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->avejas->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->avejas->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->avejas->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_2_especies->mariposas->Visible) { // mariposas ?>
	<?php if ($_2_especies->SortUrl($_2_especies->mariposas) == "") { ?>
		<td><div id="elh__2_especies_mariposas" class="_2_especies_mariposas"><div class="ewTableHeaderCaption"><?php echo $_2_especies->mariposas->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_2_especies->SortUrl($_2_especies->mariposas) ?>',1);"><div id="elh__2_especies_mariposas" class="_2_especies_mariposas">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_2_especies->mariposas->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_2_especies->mariposas->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_2_especies->mariposas->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$p_2_especies_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($_2_especies->ExportAll && $_2_especies->Export <> "") {
	$p_2_especies_list->StopRec = $p_2_especies_list->TotalRecs;
} else {

	// Set the last record to display
	if ($p_2_especies_list->TotalRecs > $p_2_especies_list->StartRec + $p_2_especies_list->DisplayRecs - 1)
		$p_2_especies_list->StopRec = $p_2_especies_list->StartRec + $p_2_especies_list->DisplayRecs - 1;
	else
		$p_2_especies_list->StopRec = $p_2_especies_list->TotalRecs;
}
$p_2_especies_list->RecCnt = $p_2_especies_list->StartRec - 1;
if ($p_2_especies_list->Recordset && !$p_2_especies_list->Recordset->EOF) {
	$p_2_especies_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $p_2_especies_list->StartRec > 1)
		$p_2_especies_list->Recordset->Move($p_2_especies_list->StartRec - 1);
} elseif (!$_2_especies->AllowAddDeleteRow && $p_2_especies_list->StopRec == 0) {
	$p_2_especies_list->StopRec = $_2_especies->GridAddRowCount;
}

// Initialize aggregate
$_2_especies->RowType = EW_ROWTYPE_AGGREGATEINIT;
$_2_especies->ResetAttrs();
$p_2_especies_list->RenderRow();
while ($p_2_especies_list->RecCnt < $p_2_especies_list->StopRec) {
	$p_2_especies_list->RecCnt++;
	if (intval($p_2_especies_list->RecCnt) >= intval($p_2_especies_list->StartRec)) {
		$p_2_especies_list->RowCnt++;

		// Set up key count
		$p_2_especies_list->KeyCount = $p_2_especies_list->RowIndex;

		// Init row class and style
		$_2_especies->ResetAttrs();
		$_2_especies->CssClass = "";
		if ($_2_especies->CurrentAction == "gridadd") {
		} else {
			$p_2_especies_list->LoadRowValues($p_2_especies_list->Recordset); // Load row values
		}
		$_2_especies->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$_2_especies->RowAttrs = array_merge($_2_especies->RowAttrs, array('data-rowindex'=>$p_2_especies_list->RowCnt, 'id'=>'r' . $p_2_especies_list->RowCnt . '__2_especies', 'data-rowtype'=>$_2_especies->RowType));

		// Render row
		$p_2_especies_list->RenderRow();

		// Render list options
		$p_2_especies_list->RenderListOptions();
?>
	<tr<?php echo $_2_especies->RowAttributes() ?>>
<?php

// Render list options (body, left)
$p_2_especies_list->ListOptions->Render("body", "left", $p_2_especies_list->RowCnt);
?>
	<?php if ($_2_especies->id_especie->Visible) { // id_especie ?>
		<td<?php echo $_2_especies->id_especie->CellAttributes() ?>>
<span<?php echo $_2_especies->id_especie->ViewAttributes() ?>>
<?php echo $_2_especies->id_especie->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->NOMBRE_FAM->Visible) { // NOMBRE_FAM ?>
		<td<?php echo $_2_especies->NOMBRE_FAM->CellAttributes() ?>>
<span<?php echo $_2_especies->NOMBRE_FAM->ViewAttributes() ?>>
<?php echo $_2_especies->NOMBRE_FAM->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->NOMBRE_CIE->Visible) { // NOMBRE_CIE ?>
		<td<?php echo $_2_especies->NOMBRE_CIE->CellAttributes() ?>>
<span<?php echo $_2_especies->NOMBRE_CIE->ViewAttributes() ?>>
<?php echo $_2_especies->NOMBRE_CIE->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->NOMBRE_COM->Visible) { // NOMBRE_COM ?>
		<td<?php echo $_2_especies->NOMBRE_COM->CellAttributes() ?>>
<span<?php echo $_2_especies->NOMBRE_COM->ViewAttributes() ?>>
<?php echo $_2_especies->NOMBRE_COM->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->TIPO_FOLLA->Visible) { // TIPO_FOLLA ?>
		<td<?php echo $_2_especies->TIPO_FOLLA->CellAttributes() ?>>
<span<?php echo $_2_especies->TIPO_FOLLA->ViewAttributes() ?>>
<?php echo $_2_especies->TIPO_FOLLA->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->ORIGEN->Visible) { // ORIGEN ?>
		<td<?php echo $_2_especies->ORIGEN->CellAttributes() ?>>
<span<?php echo $_2_especies->ORIGEN->ViewAttributes() ?>>
<?php echo $_2_especies->ORIGEN->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->ICONO->Visible) { // ICONO ?>
		<td<?php echo $_2_especies->ICONO->CellAttributes() ?>>
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
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->imagen_completo->Visible) { // imagen_completo ?>
		<td<?php echo $_2_especies->imagen_completo->CellAttributes() ?>>
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
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->imagen_hoja->Visible) { // imagen_hoja ?>
		<td<?php echo $_2_especies->imagen_hoja->CellAttributes() ?>>
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
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->imagen_flor->Visible) { // imagen_flor ?>
		<td<?php echo $_2_especies->imagen_flor->CellAttributes() ?>>
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
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->medicinal->Visible) { // medicinal ?>
		<td<?php echo $_2_especies->medicinal->CellAttributes() ?>>
<span<?php echo $_2_especies->medicinal->ViewAttributes() ?>>
<?php echo $_2_especies->medicinal->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->comestible->Visible) { // comestible ?>
		<td<?php echo $_2_especies->comestible->CellAttributes() ?>>
<span<?php echo $_2_especies->comestible->ViewAttributes() ?>>
<?php echo $_2_especies->comestible->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->perfume->Visible) { // perfume ?>
		<td<?php echo $_2_especies->perfume->CellAttributes() ?>>
<span<?php echo $_2_especies->perfume->ViewAttributes() ?>>
<?php echo $_2_especies->perfume->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->avejas->Visible) { // avejas ?>
		<td<?php echo $_2_especies->avejas->CellAttributes() ?>>
<span<?php echo $_2_especies->avejas->ViewAttributes() ?>>
<?php echo $_2_especies->avejas->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_2_especies->mariposas->Visible) { // mariposas ?>
		<td<?php echo $_2_especies->mariposas->CellAttributes() ?>>
<span<?php echo $_2_especies->mariposas->ViewAttributes() ?>>
<?php echo $_2_especies->mariposas->ListViewValue() ?></span>
<a id="<?php echo $p_2_especies_list->PageObjName . "_row_" . $p_2_especies_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$p_2_especies_list->ListOptions->Render("body", "right", $p_2_especies_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($_2_especies->CurrentAction <> "gridadd")
		$p_2_especies_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($_2_especies->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($p_2_especies_list->Recordset)
	$p_2_especies_list->Recordset->Close();
?>
<?php if ($p_2_especies_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($_2_especies->CurrentAction <> "gridadd" && $_2_especies->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($p_2_especies_list->Pager)) $p_2_especies_list->Pager = new cPrevNextPager($p_2_especies_list->StartRec, $p_2_especies_list->DisplayRecs, $p_2_especies_list->TotalRecs) ?>
<?php if ($p_2_especies_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($p_2_especies_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_2_especies_list->PageUrl() ?>start=<?php echo $p_2_especies_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($p_2_especies_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_2_especies_list->PageUrl() ?>start=<?php echo $p_2_especies_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $p_2_especies_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($p_2_especies_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_2_especies_list->PageUrl() ?>start=<?php echo $p_2_especies_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($p_2_especies_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_2_especies_list->PageUrl() ?>start=<?php echo $p_2_especies_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $p_2_especies_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $p_2_especies_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $p_2_especies_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $p_2_especies_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($p_2_especies_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($p_2_especies_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
f_2_especieslistsrch.Init();
f_2_especieslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$p_2_especies_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$p_2_especies_list->Page_Terminate();
?>
