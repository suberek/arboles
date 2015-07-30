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

$especies_list = NULL; // Initialize page object first

class cespecies_list extends cespecies {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Table name
	var $TableName = 'especies';

	// Page object name
	var $PageObjName = 'especies_list';

	// Grid form hidden field names
	var $FormName = 'fespecieslist';
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

		// Table object (especies)
		if (!isset($GLOBALS["especies"])) {
			$GLOBALS["especies"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["especies"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "especiesadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "especiesdelete.php";
		$this->MultiUpdateUrl = "especiesupdate.php";

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'especies', TRUE);

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
		if (is_numeric($Keyword)) $this->BuildBasicSearchSQL($sWhere, $this->abejas, $Keyword);
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
			$this->UpdateSort($this->id_familia); // id_familia
			$this->UpdateSort($this->NOMBRE_CIE); // NOMBRE_CIE
			$this->UpdateSort($this->NOMBRE_COM); // NOMBRE_COM
			$this->UpdateSort($this->ORIGEN); // ORIGEN
			$this->UpdateSort($this->ICONO); // ICONO
			$this->UpdateSort($this->imagen_completo); // imagen_completo
			$this->UpdateSort($this->medicinal); // medicinal
			$this->UpdateSort($this->comestible); // comestible
			$this->UpdateSort($this->perfume); // perfume
			$this->UpdateSort($this->abejas); // abejas
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
				$this->setSessionOrderByList($sOrderBy);
				$this->id_especie->setSort("");
				$this->id_familia->setSort("");
				$this->NOMBRE_CIE->setSort("");
				$this->NOMBRE_COM->setSort("");
				$this->ORIGEN->setSort("");
				$this->ICONO->setSort("");
				$this->imagen_completo->setSort("");
				$this->medicinal->setSort("");
				$this->comestible->setSort("");
				$this->perfume->setSort("");
				$this->abejas->setSort("");
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
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fespecieslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		$this->abejas->setDbValue($rs->fields('abejas'));
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
		$this->abejas->DbValue = $row['abejas'];
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
		// abejas
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
			if (strval($this->perfume->CurrentValue) <> "") {
				switch ($this->perfume->CurrentValue) {
					case $this->perfume->FldTagValue(1):
						$this->perfume->ViewValue = $this->perfume->FldTagCaption(1) <> "" ? $this->perfume->FldTagCaption(1) : $this->perfume->CurrentValue;
						break;
					case $this->perfume->FldTagValue(2):
						$this->perfume->ViewValue = $this->perfume->FldTagCaption(2) <> "" ? $this->perfume->FldTagCaption(2) : $this->perfume->CurrentValue;
						break;
					default:
						$this->perfume->ViewValue = $this->perfume->CurrentValue;
				}
			} else {
				$this->perfume->ViewValue = NULL;
			}
			$this->perfume->ViewCustomAttributes = "";

			// abejas
			if (strval($this->abejas->CurrentValue) <> "") {
				switch ($this->abejas->CurrentValue) {
					case $this->abejas->FldTagValue(1):
						$this->abejas->ViewValue = $this->abejas->FldTagCaption(1) <> "" ? $this->abejas->FldTagCaption(1) : $this->abejas->CurrentValue;
						break;
					case $this->abejas->FldTagValue(2):
						$this->abejas->ViewValue = $this->abejas->FldTagCaption(2) <> "" ? $this->abejas->FldTagCaption(2) : $this->abejas->CurrentValue;
						break;
					default:
						$this->abejas->ViewValue = $this->abejas->CurrentValue;
				}
			} else {
				$this->abejas->ViewValue = NULL;
			}
			$this->abejas->ViewCustomAttributes = "";

			// mariposas
			if (strval($this->mariposas->CurrentValue) <> "") {
				switch ($this->mariposas->CurrentValue) {
					case $this->mariposas->FldTagValue(1):
						$this->mariposas->ViewValue = $this->mariposas->FldTagCaption(1) <> "" ? $this->mariposas->FldTagCaption(1) : $this->mariposas->CurrentValue;
						break;
					case $this->mariposas->FldTagValue(2):
						$this->mariposas->ViewValue = $this->mariposas->FldTagCaption(2) <> "" ? $this->mariposas->FldTagCaption(2) : $this->mariposas->CurrentValue;
						break;
					default:
						$this->mariposas->ViewValue = $this->mariposas->CurrentValue;
				}
			} else {
				$this->mariposas->ViewValue = NULL;
			}
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

			// abejas
			$this->abejas->LinkCustomAttributes = "";
			$this->abejas->HrefValue = "";
			$this->abejas->TooltipValue = "";

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
if (!isset($especies_list)) $especies_list = new cespecies_list();

// Page init
$especies_list->Page_Init();

// Page main
$especies_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$especies_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var especies_list = new ew_Page("especies_list");
especies_list.PageID = "list"; // Page ID
var EW_PAGE_ID = especies_list.PageID; // For backward compatibility

// Form object
var fespecieslist = new ew_Form("fespecieslist");
fespecieslist.FormKeyCountName = '<?php echo $especies_list->FormKeyCountName ?>';

// Form_CustomValidate event
fespecieslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fespecieslist.ValidateRequired = true;
<?php } else { ?>
fespecieslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fespecieslist.Lists["x_id_familia"] = {"LinkField":"x_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_familia","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fespecieslistsrch = new ew_Form("fespecieslistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php if ($especies_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $especies_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$especies_list->TotalRecs = $especies->SelectRecordCount();
	} else {
		if ($especies_list->Recordset = $especies_list->LoadRecordset())
			$especies_list->TotalRecs = $especies_list->Recordset->RecordCount();
	}
	$especies_list->StartRec = 1;
	if ($especies_list->DisplayRecs <= 0 || ($especies->Export <> "" && $especies->ExportAll)) // Display all records
		$especies_list->DisplayRecs = $especies_list->TotalRecs;
	if (!($especies->Export <> "" && $especies->ExportAll))
		$especies_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$especies_list->Recordset = $especies_list->LoadRecordset($especies_list->StartRec-1, $especies_list->DisplayRecs);
$especies_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($especies->Export == "" && $especies->CurrentAction == "") { ?>
<form name="fespecieslistsrch" id="fespecieslistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewSearchTable"><tr><td>
<div class="accordion" id="fespecieslistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#fespecieslistsrch_SearchGroup" href="#fespecieslistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="fespecieslistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="fespecieslistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="especies">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($especies_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo $Language->Phrase("Search") ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $especies_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($especies_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($especies_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($especies_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
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
<?php $especies_list->ShowPageHeader(); ?>
<?php
$especies_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($especies->CurrentAction <> "gridadd" && $especies->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($especies_list->Pager)) $especies_list->Pager = new cPrevNextPager($especies_list->StartRec, $especies_list->DisplayRecs, $especies_list->TotalRecs) ?>
<?php if ($especies_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($especies_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $especies_list->PageUrl() ?>start=<?php echo $especies_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($especies_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $especies_list->PageUrl() ?>start=<?php echo $especies_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $especies_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($especies_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $especies_list->PageUrl() ?>start=<?php echo $especies_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($especies_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $especies_list->PageUrl() ?>start=<?php echo $especies_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $especies_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $especies_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $especies_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $especies_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($especies_list->SearchWhere == "0=101") { ?>
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
	foreach ($especies_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="fespecieslist" id="fespecieslist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="especies">
<div id="gmp_especies" class="ewGridMiddlePanel">
<?php if ($especies_list->TotalRecs > 0) { ?>
<table id="tbl_especieslist" class="ewTable ewTableSeparate">
<?php echo $especies->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$especies_list->RenderListOptions();

// Render list options (header, left)
$especies_list->ListOptions->Render("header", "left");
?>
<?php if ($especies->id_especie->Visible) { // id_especie ?>
	<?php if ($especies->SortUrl($especies->id_especie) == "") { ?>
		<td><div id="elh_especies_id_especie" class="especies_id_especie"><div class="ewTableHeaderCaption"><?php echo $especies->id_especie->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->id_especie) ?>',1);"><div id="elh_especies_id_especie" class="especies_id_especie">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->id_especie->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($especies->id_especie->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->id_especie->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->id_familia->Visible) { // id_familia ?>
	<?php if ($especies->SortUrl($especies->id_familia) == "") { ?>
		<td><div id="elh_especies_id_familia" class="especies_id_familia"><div class="ewTableHeaderCaption"><?php echo $especies->id_familia->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->id_familia) ?>',1);"><div id="elh_especies_id_familia" class="especies_id_familia">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->id_familia->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($especies->id_familia->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->id_familia->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->NOMBRE_CIE->Visible) { // NOMBRE_CIE ?>
	<?php if ($especies->SortUrl($especies->NOMBRE_CIE) == "") { ?>
		<td><div id="elh_especies_NOMBRE_CIE" class="especies_NOMBRE_CIE"><div class="ewTableHeaderCaption"><?php echo $especies->NOMBRE_CIE->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->NOMBRE_CIE) ?>',1);"><div id="elh_especies_NOMBRE_CIE" class="especies_NOMBRE_CIE">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->NOMBRE_CIE->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($especies->NOMBRE_CIE->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->NOMBRE_CIE->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->NOMBRE_COM->Visible) { // NOMBRE_COM ?>
	<?php if ($especies->SortUrl($especies->NOMBRE_COM) == "") { ?>
		<td><div id="elh_especies_NOMBRE_COM" class="especies_NOMBRE_COM"><div class="ewTableHeaderCaption"><?php echo $especies->NOMBRE_COM->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->NOMBRE_COM) ?>',1);"><div id="elh_especies_NOMBRE_COM" class="especies_NOMBRE_COM">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->NOMBRE_COM->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($especies->NOMBRE_COM->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->NOMBRE_COM->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->ORIGEN->Visible) { // ORIGEN ?>
	<?php if ($especies->SortUrl($especies->ORIGEN) == "") { ?>
		<td><div id="elh_especies_ORIGEN" class="especies_ORIGEN"><div class="ewTableHeaderCaption"><?php echo $especies->ORIGEN->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->ORIGEN) ?>',1);"><div id="elh_especies_ORIGEN" class="especies_ORIGEN">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->ORIGEN->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($especies->ORIGEN->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->ORIGEN->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->ICONO->Visible) { // ICONO ?>
	<?php if ($especies->SortUrl($especies->ICONO) == "") { ?>
		<td><div id="elh_especies_ICONO" class="especies_ICONO"><div class="ewTableHeaderCaption"><?php echo $especies->ICONO->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->ICONO) ?>',1);"><div id="elh_especies_ICONO" class="especies_ICONO">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->ICONO->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($especies->ICONO->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->ICONO->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->imagen_completo->Visible) { // imagen_completo ?>
	<?php if ($especies->SortUrl($especies->imagen_completo) == "") { ?>
		<td><div id="elh_especies_imagen_completo" class="especies_imagen_completo"><div class="ewTableHeaderCaption"><?php echo $especies->imagen_completo->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->imagen_completo) ?>',1);"><div id="elh_especies_imagen_completo" class="especies_imagen_completo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->imagen_completo->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($especies->imagen_completo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->imagen_completo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->medicinal->Visible) { // medicinal ?>
	<?php if ($especies->SortUrl($especies->medicinal) == "") { ?>
		<td><div id="elh_especies_medicinal" class="especies_medicinal"><div class="ewTableHeaderCaption"><?php echo $especies->medicinal->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->medicinal) ?>',1);"><div id="elh_especies_medicinal" class="especies_medicinal">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->medicinal->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($especies->medicinal->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->medicinal->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->comestible->Visible) { // comestible ?>
	<?php if ($especies->SortUrl($especies->comestible) == "") { ?>
		<td><div id="elh_especies_comestible" class="especies_comestible"><div class="ewTableHeaderCaption"><?php echo $especies->comestible->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->comestible) ?>',1);"><div id="elh_especies_comestible" class="especies_comestible">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->comestible->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($especies->comestible->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->comestible->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->perfume->Visible) { // perfume ?>
	<?php if ($especies->SortUrl($especies->perfume) == "") { ?>
		<td><div id="elh_especies_perfume" class="especies_perfume"><div class="ewTableHeaderCaption"><?php echo $especies->perfume->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->perfume) ?>',1);"><div id="elh_especies_perfume" class="especies_perfume">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->perfume->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($especies->perfume->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->perfume->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->abejas->Visible) { // abejas ?>
	<?php if ($especies->SortUrl($especies->abejas) == "") { ?>
		<td><div id="elh_especies_abejas" class="especies_abejas"><div class="ewTableHeaderCaption"><?php echo $especies->abejas->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->abejas) ?>',1);"><div id="elh_especies_abejas" class="especies_abejas">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->abejas->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($especies->abejas->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->abejas->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($especies->mariposas->Visible) { // mariposas ?>
	<?php if ($especies->SortUrl($especies->mariposas) == "") { ?>
		<td><div id="elh_especies_mariposas" class="especies_mariposas"><div class="ewTableHeaderCaption"><?php echo $especies->mariposas->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $especies->SortUrl($especies->mariposas) ?>',1);"><div id="elh_especies_mariposas" class="especies_mariposas">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $especies->mariposas->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($especies->mariposas->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($especies->mariposas->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$especies_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($especies->ExportAll && $especies->Export <> "") {
	$especies_list->StopRec = $especies_list->TotalRecs;
} else {

	// Set the last record to display
	if ($especies_list->TotalRecs > $especies_list->StartRec + $especies_list->DisplayRecs - 1)
		$especies_list->StopRec = $especies_list->StartRec + $especies_list->DisplayRecs - 1;
	else
		$especies_list->StopRec = $especies_list->TotalRecs;
}
$especies_list->RecCnt = $especies_list->StartRec - 1;
if ($especies_list->Recordset && !$especies_list->Recordset->EOF) {
	$especies_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $especies_list->StartRec > 1)
		$especies_list->Recordset->Move($especies_list->StartRec - 1);
} elseif (!$especies->AllowAddDeleteRow && $especies_list->StopRec == 0) {
	$especies_list->StopRec = $especies->GridAddRowCount;
}

// Initialize aggregate
$especies->RowType = EW_ROWTYPE_AGGREGATEINIT;
$especies->ResetAttrs();
$especies_list->RenderRow();
while ($especies_list->RecCnt < $especies_list->StopRec) {
	$especies_list->RecCnt++;
	if (intval($especies_list->RecCnt) >= intval($especies_list->StartRec)) {
		$especies_list->RowCnt++;

		// Set up key count
		$especies_list->KeyCount = $especies_list->RowIndex;

		// Init row class and style
		$especies->ResetAttrs();
		$especies->CssClass = "";
		if ($especies->CurrentAction == "gridadd") {
		} else {
			$especies_list->LoadRowValues($especies_list->Recordset); // Load row values
		}
		$especies->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$especies->RowAttrs = array_merge($especies->RowAttrs, array('data-rowindex'=>$especies_list->RowCnt, 'id'=>'r' . $especies_list->RowCnt . '_especies', 'data-rowtype'=>$especies->RowType));

		// Render row
		$especies_list->RenderRow();

		// Render list options
		$especies_list->RenderListOptions();
?>
	<tr<?php echo $especies->RowAttributes() ?>>
<?php

// Render list options (body, left)
$especies_list->ListOptions->Render("body", "left", $especies_list->RowCnt);
?>
	<?php if ($especies->id_especie->Visible) { // id_especie ?>
		<td<?php echo $especies->id_especie->CellAttributes() ?>>
<span<?php echo $especies->id_especie->ViewAttributes() ?>>
<?php echo $especies->id_especie->ListViewValue() ?></span>
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->id_familia->Visible) { // id_familia ?>
		<td<?php echo $especies->id_familia->CellAttributes() ?>>
<span<?php echo $especies->id_familia->ViewAttributes() ?>>
<?php echo $especies->id_familia->ListViewValue() ?></span>
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->NOMBRE_CIE->Visible) { // NOMBRE_CIE ?>
		<td<?php echo $especies->NOMBRE_CIE->CellAttributes() ?>>
<span<?php echo $especies->NOMBRE_CIE->ViewAttributes() ?>>
<?php echo $especies->NOMBRE_CIE->ListViewValue() ?></span>
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->NOMBRE_COM->Visible) { // NOMBRE_COM ?>
		<td<?php echo $especies->NOMBRE_COM->CellAttributes() ?>>
<span<?php echo $especies->NOMBRE_COM->ViewAttributes() ?>>
<?php echo $especies->NOMBRE_COM->ListViewValue() ?></span>
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->ORIGEN->Visible) { // ORIGEN ?>
		<td<?php echo $especies->ORIGEN->CellAttributes() ?>>
<span<?php echo $especies->ORIGEN->ViewAttributes() ?>>
<?php echo $especies->ORIGEN->ListViewValue() ?></span>
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->ICONO->Visible) { // ICONO ?>
		<td<?php echo $especies->ICONO->CellAttributes() ?>>
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
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->imagen_completo->Visible) { // imagen_completo ?>
		<td<?php echo $especies->imagen_completo->CellAttributes() ?>>
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
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->medicinal->Visible) { // medicinal ?>
		<td<?php echo $especies->medicinal->CellAttributes() ?>>
<span<?php echo $especies->medicinal->ViewAttributes() ?>>
<?php echo $especies->medicinal->ListViewValue() ?></span>
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->comestible->Visible) { // comestible ?>
		<td<?php echo $especies->comestible->CellAttributes() ?>>
<span<?php echo $especies->comestible->ViewAttributes() ?>>
<?php echo $especies->comestible->ListViewValue() ?></span>
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->perfume->Visible) { // perfume ?>
		<td<?php echo $especies->perfume->CellAttributes() ?>>
<span<?php echo $especies->perfume->ViewAttributes() ?>>
<?php echo $especies->perfume->ListViewValue() ?></span>
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->abejas->Visible) { // abejas ?>
		<td<?php echo $especies->abejas->CellAttributes() ?>>
<span<?php echo $especies->abejas->ViewAttributes() ?>>
<?php echo $especies->abejas->ListViewValue() ?></span>
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($especies->mariposas->Visible) { // mariposas ?>
		<td<?php echo $especies->mariposas->CellAttributes() ?>>
<span<?php echo $especies->mariposas->ViewAttributes() ?>>
<?php echo $especies->mariposas->ListViewValue() ?></span>
<a id="<?php echo $especies_list->PageObjName . "_row_" . $especies_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$especies_list->ListOptions->Render("body", "right", $especies_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($especies->CurrentAction <> "gridadd")
		$especies_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($especies->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($especies_list->Recordset)
	$especies_list->Recordset->Close();
?>
<?php if ($especies_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($especies->CurrentAction <> "gridadd" && $especies->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($especies_list->Pager)) $especies_list->Pager = new cPrevNextPager($especies_list->StartRec, $especies_list->DisplayRecs, $especies_list->TotalRecs) ?>
<?php if ($especies_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($especies_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $especies_list->PageUrl() ?>start=<?php echo $especies_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($especies_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $especies_list->PageUrl() ?>start=<?php echo $especies_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $especies_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($especies_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $especies_list->PageUrl() ?>start=<?php echo $especies_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($especies_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $especies_list->PageUrl() ?>start=<?php echo $especies_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $especies_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $especies_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $especies_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $especies_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($especies_list->SearchWhere == "0=101") { ?>
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
	foreach ($especies_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
fespecieslistsrch.Init();
fespecieslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$especies_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$especies_list->Page_Terminate();
?>
