<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "_1_individuosinfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$p_1_individuos_list = NULL; // Initialize page object first

class cp_1_individuos_list extends c_1_individuos {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Table name
	var $TableName = '1_individuos';

	// Page object name
	var $PageObjName = 'p_1_individuos_list';

	// Grid form hidden field names
	var $FormName = 'f_1_individuoslist';
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

		// Table object (_1_individuos)
		if (!isset($GLOBALS["_1_individuos"])) {
			$GLOBALS["_1_individuos"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["_1_individuos"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "_1_individuosadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "_1_individuosdelete.php";
		$this->MultiUpdateUrl = "_1_individuosupdate.php";

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", '1_individuos', TRUE);

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
		$this->id_individuo->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->id_individuo->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->id_individuo->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->calle, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->espacio_verde, $Keyword);
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
			$this->UpdateSort($this->id_individuo); // id_individuo
			$this->UpdateSort($this->id_especie); // id_especie
			$this->UpdateSort($this->calle); // calle
			$this->UpdateSort($this->alt_ini); // alt_ini
			$this->UpdateSort($this->espacio_verde); // espacio_verde
			$this->UpdateSort($this->id_fuente); // id_fuente
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
				$this->id_individuo->setSort("");
				$this->id_especie->setSort("");
				$this->calle->setSort("");
				$this->alt_ini->setSort("");
				$this->espacio_verde->setSort("");
				$this->id_fuente->setSort("");
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
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->id_individuo->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.f_1_individuoslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		$this->id_individuo->setDbValue($rs->fields('id_individuo'));
		$this->ALTURA_TOT->setDbValue($rs->fields('ALTURA_TOT'));
		$this->DIAMETRO->setDbValue($rs->fields('DIAMETRO'));
		$this->INCLINACIO->setDbValue($rs->fields('INCLINACIO'));
		$this->id_especie->setDbValue($rs->fields('id_especie'));
		if (array_key_exists('EV__id_especie', $rs->fields)) {
			$this->id_especie->VirtualValue = $rs->fields('EV__id_especie'); // Set up virtual field value
		} else {
			$this->id_especie->VirtualValue = ""; // Clear value
		}
		$this->calle->setDbValue($rs->fields('calle'));
		$this->alt_ini->setDbValue($rs->fields('alt_ini'));
		$this->espacio_verde->setDbValue($rs->fields('espacio_verde'));
		$this->lng->setDbValue($rs->fields('lng'));
		$this->lat->setDbValue($rs->fields('lat'));
		$this->id_fuente->setDbValue($rs->fields('id_fuente'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id_individuo->DbValue = $row['id_individuo'];
		$this->ALTURA_TOT->DbValue = $row['ALTURA_TOT'];
		$this->DIAMETRO->DbValue = $row['DIAMETRO'];
		$this->INCLINACIO->DbValue = $row['INCLINACIO'];
		$this->id_especie->DbValue = $row['id_especie'];
		$this->calle->DbValue = $row['calle'];
		$this->alt_ini->DbValue = $row['alt_ini'];
		$this->espacio_verde->DbValue = $row['espacio_verde'];
		$this->lng->DbValue = $row['lng'];
		$this->lat->DbValue = $row['lat'];
		$this->id_fuente->DbValue = $row['id_fuente'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id_individuo")) <> "")
			$this->id_individuo->CurrentValue = $this->getKey("id_individuo"); // id_individuo
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
		// id_individuo
		// ALTURA_TOT
		// DIAMETRO
		// INCLINACIO
		// id_especie
		// calle
		// alt_ini
		// espacio_verde
		// lng
		// lat
		// id_fuente

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id_individuo
			$this->id_individuo->ViewValue = $this->id_individuo->CurrentValue;
			$this->id_individuo->ViewCustomAttributes = "";

			// ALTURA_TOT
			$this->ALTURA_TOT->ViewValue = $this->ALTURA_TOT->CurrentValue;
			$this->ALTURA_TOT->ViewCustomAttributes = "";

			// DIAMETRO
			$this->DIAMETRO->ViewValue = $this->DIAMETRO->CurrentValue;
			$this->DIAMETRO->ViewCustomAttributes = "";

			// INCLINACIO
			$this->INCLINACIO->ViewValue = $this->INCLINACIO->CurrentValue;
			$this->INCLINACIO->ViewCustomAttributes = "";

			// id_especie
			if ($this->id_especie->VirtualValue <> "") {
				$this->id_especie->ViewValue = $this->id_especie->VirtualValue;
			} else {
				$this->id_especie->ViewValue = $this->id_especie->CurrentValue;
			if (strval($this->id_especie->CurrentValue) <> "") {
				$sFilterWrk = "`id_especie`" . ew_SearchString("=", $this->id_especie->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id_especie`, `NOMBRE_CIE` AS `DispFld`, `NOMBRE_COM` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `2_especies`";
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

			// espacio_verde
			$this->espacio_verde->ViewValue = $this->espacio_verde->CurrentValue;
			$this->espacio_verde->ViewCustomAttributes = "";

			// lng
			$this->lng->ViewValue = $this->lng->CurrentValue;
			$this->lng->ViewCustomAttributes = "";

			// lat
			$this->lat->ViewValue = $this->lat->CurrentValue;
			$this->lat->ViewCustomAttributes = "";

			// id_fuente
			$this->id_fuente->ViewValue = $this->id_fuente->CurrentValue;
			$this->id_fuente->ViewCustomAttributes = "";

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

			// id_fuente
			$this->id_fuente->LinkCustomAttributes = "";
			$this->id_fuente->HrefValue = "";
			$this->id_fuente->TooltipValue = "";
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
if (!isset($p_1_individuos_list)) $p_1_individuos_list = new cp_1_individuos_list();

// Page init
$p_1_individuos_list->Page_Init();

// Page main
$p_1_individuos_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$p_1_individuos_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var p_1_individuos_list = new ew_Page("p_1_individuos_list");
p_1_individuos_list.PageID = "list"; // Page ID
var EW_PAGE_ID = p_1_individuos_list.PageID; // For backward compatibility

// Form object
var f_1_individuoslist = new ew_Form("f_1_individuoslist");
f_1_individuoslist.FormKeyCountName = '<?php echo $p_1_individuos_list->FormKeyCountName ?>';

// Form_CustomValidate event
f_1_individuoslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
f_1_individuoslist.ValidateRequired = true;
<?php } else { ?>
f_1_individuoslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
f_1_individuoslist.Lists["x_id_especie"] = {"LinkField":"x_id_especie","Ajax":true,"AutoFill":false,"DisplayFields":["x_NOMBRE_CIE","x_NOMBRE_COM","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var f_1_individuoslistsrch = new ew_Form("f_1_individuoslistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php if ($p_1_individuos_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $p_1_individuos_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$p_1_individuos_list->TotalRecs = $_1_individuos->SelectRecordCount();
	} else {
		if ($p_1_individuos_list->Recordset = $p_1_individuos_list->LoadRecordset())
			$p_1_individuos_list->TotalRecs = $p_1_individuos_list->Recordset->RecordCount();
	}
	$p_1_individuos_list->StartRec = 1;
	if ($p_1_individuos_list->DisplayRecs <= 0 || ($_1_individuos->Export <> "" && $_1_individuos->ExportAll)) // Display all records
		$p_1_individuos_list->DisplayRecs = $p_1_individuos_list->TotalRecs;
	if (!($_1_individuos->Export <> "" && $_1_individuos->ExportAll))
		$p_1_individuos_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$p_1_individuos_list->Recordset = $p_1_individuos_list->LoadRecordset($p_1_individuos_list->StartRec-1, $p_1_individuos_list->DisplayRecs);
$p_1_individuos_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($_1_individuos->Export == "" && $_1_individuos->CurrentAction == "") { ?>
<form name="f_1_individuoslistsrch" id="f_1_individuoslistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewSearchTable"><tr><td>
<div class="accordion" id="f_1_individuoslistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#f_1_individuoslistsrch_SearchGroup" href="#f_1_individuoslistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="f_1_individuoslistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="f_1_individuoslistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="_1_individuos">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($p_1_individuos_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo $Language->Phrase("Search") ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $p_1_individuos_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($p_1_individuos_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($p_1_individuos_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($p_1_individuos_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
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
<?php $p_1_individuos_list->ShowPageHeader(); ?>
<?php
$p_1_individuos_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($_1_individuos->CurrentAction <> "gridadd" && $_1_individuos->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($p_1_individuos_list->Pager)) $p_1_individuos_list->Pager = new cPrevNextPager($p_1_individuos_list->StartRec, $p_1_individuos_list->DisplayRecs, $p_1_individuos_list->TotalRecs) ?>
<?php if ($p_1_individuos_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($p_1_individuos_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_1_individuos_list->PageUrl() ?>start=<?php echo $p_1_individuos_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($p_1_individuos_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_1_individuos_list->PageUrl() ?>start=<?php echo $p_1_individuos_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $p_1_individuos_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($p_1_individuos_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_1_individuos_list->PageUrl() ?>start=<?php echo $p_1_individuos_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($p_1_individuos_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_1_individuos_list->PageUrl() ?>start=<?php echo $p_1_individuos_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $p_1_individuos_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $p_1_individuos_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $p_1_individuos_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $p_1_individuos_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($p_1_individuos_list->SearchWhere == "0=101") { ?>
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
	foreach ($p_1_individuos_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="f_1_individuoslist" id="f_1_individuoslist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="_1_individuos">
<div id="gmp__1_individuos" class="ewGridMiddlePanel">
<?php if ($p_1_individuos_list->TotalRecs > 0) { ?>
<table id="tbl__1_individuoslist" class="ewTable ewTableSeparate">
<?php echo $_1_individuos->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$p_1_individuos_list->RenderListOptions();

// Render list options (header, left)
$p_1_individuos_list->ListOptions->Render("header", "left");
?>
<?php if ($_1_individuos->id_individuo->Visible) { // id_individuo ?>
	<?php if ($_1_individuos->SortUrl($_1_individuos->id_individuo) == "") { ?>
		<td><div id="elh__1_individuos_id_individuo" class="_1_individuos_id_individuo"><div class="ewTableHeaderCaption"><?php echo $_1_individuos->id_individuo->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_1_individuos->SortUrl($_1_individuos->id_individuo) ?>',1);"><div id="elh__1_individuos_id_individuo" class="_1_individuos_id_individuo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_1_individuos->id_individuo->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($_1_individuos->id_individuo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_1_individuos->id_individuo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_1_individuos->id_especie->Visible) { // id_especie ?>
	<?php if ($_1_individuos->SortUrl($_1_individuos->id_especie) == "") { ?>
		<td><div id="elh__1_individuos_id_especie" class="_1_individuos_id_especie"><div class="ewTableHeaderCaption"><?php echo $_1_individuos->id_especie->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_1_individuos->SortUrl($_1_individuos->id_especie) ?>',1);"><div id="elh__1_individuos_id_especie" class="_1_individuos_id_especie">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_1_individuos->id_especie->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($_1_individuos->id_especie->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_1_individuos->id_especie->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_1_individuos->calle->Visible) { // calle ?>
	<?php if ($_1_individuos->SortUrl($_1_individuos->calle) == "") { ?>
		<td><div id="elh__1_individuos_calle" class="_1_individuos_calle"><div class="ewTableHeaderCaption"><?php echo $_1_individuos->calle->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_1_individuos->SortUrl($_1_individuos->calle) ?>',1);"><div id="elh__1_individuos_calle" class="_1_individuos_calle">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_1_individuos->calle->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_1_individuos->calle->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_1_individuos->calle->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_1_individuos->alt_ini->Visible) { // alt_ini ?>
	<?php if ($_1_individuos->SortUrl($_1_individuos->alt_ini) == "") { ?>
		<td><div id="elh__1_individuos_alt_ini" class="_1_individuos_alt_ini"><div class="ewTableHeaderCaption"><?php echo $_1_individuos->alt_ini->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_1_individuos->SortUrl($_1_individuos->alt_ini) ?>',1);"><div id="elh__1_individuos_alt_ini" class="_1_individuos_alt_ini">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_1_individuos->alt_ini->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($_1_individuos->alt_ini->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_1_individuos->alt_ini->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_1_individuos->espacio_verde->Visible) { // espacio_verde ?>
	<?php if ($_1_individuos->SortUrl($_1_individuos->espacio_verde) == "") { ?>
		<td><div id="elh__1_individuos_espacio_verde" class="_1_individuos_espacio_verde"><div class="ewTableHeaderCaption"><?php echo $_1_individuos->espacio_verde->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_1_individuos->SortUrl($_1_individuos->espacio_verde) ?>',1);"><div id="elh__1_individuos_espacio_verde" class="_1_individuos_espacio_verde">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_1_individuos->espacio_verde->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($_1_individuos->espacio_verde->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_1_individuos->espacio_verde->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($_1_individuos->id_fuente->Visible) { // id_fuente ?>
	<?php if ($_1_individuos->SortUrl($_1_individuos->id_fuente) == "") { ?>
		<td><div id="elh__1_individuos_id_fuente" class="_1_individuos_id_fuente"><div class="ewTableHeaderCaption"><?php echo $_1_individuos->id_fuente->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $_1_individuos->SortUrl($_1_individuos->id_fuente) ?>',1);"><div id="elh__1_individuos_id_fuente" class="_1_individuos_id_fuente">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $_1_individuos->id_fuente->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($_1_individuos->id_fuente->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($_1_individuos->id_fuente->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$p_1_individuos_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($_1_individuos->ExportAll && $_1_individuos->Export <> "") {
	$p_1_individuos_list->StopRec = $p_1_individuos_list->TotalRecs;
} else {

	// Set the last record to display
	if ($p_1_individuos_list->TotalRecs > $p_1_individuos_list->StartRec + $p_1_individuos_list->DisplayRecs - 1)
		$p_1_individuos_list->StopRec = $p_1_individuos_list->StartRec + $p_1_individuos_list->DisplayRecs - 1;
	else
		$p_1_individuos_list->StopRec = $p_1_individuos_list->TotalRecs;
}
$p_1_individuos_list->RecCnt = $p_1_individuos_list->StartRec - 1;
if ($p_1_individuos_list->Recordset && !$p_1_individuos_list->Recordset->EOF) {
	$p_1_individuos_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $p_1_individuos_list->StartRec > 1)
		$p_1_individuos_list->Recordset->Move($p_1_individuos_list->StartRec - 1);
} elseif (!$_1_individuos->AllowAddDeleteRow && $p_1_individuos_list->StopRec == 0) {
	$p_1_individuos_list->StopRec = $_1_individuos->GridAddRowCount;
}

// Initialize aggregate
$_1_individuos->RowType = EW_ROWTYPE_AGGREGATEINIT;
$_1_individuos->ResetAttrs();
$p_1_individuos_list->RenderRow();
while ($p_1_individuos_list->RecCnt < $p_1_individuos_list->StopRec) {
	$p_1_individuos_list->RecCnt++;
	if (intval($p_1_individuos_list->RecCnt) >= intval($p_1_individuos_list->StartRec)) {
		$p_1_individuos_list->RowCnt++;

		// Set up key count
		$p_1_individuos_list->KeyCount = $p_1_individuos_list->RowIndex;

		// Init row class and style
		$_1_individuos->ResetAttrs();
		$_1_individuos->CssClass = "";
		if ($_1_individuos->CurrentAction == "gridadd") {
		} else {
			$p_1_individuos_list->LoadRowValues($p_1_individuos_list->Recordset); // Load row values
		}
		$_1_individuos->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$_1_individuos->RowAttrs = array_merge($_1_individuos->RowAttrs, array('data-rowindex'=>$p_1_individuos_list->RowCnt, 'id'=>'r' . $p_1_individuos_list->RowCnt . '__1_individuos', 'data-rowtype'=>$_1_individuos->RowType));

		// Render row
		$p_1_individuos_list->RenderRow();

		// Render list options
		$p_1_individuos_list->RenderListOptions();
?>
	<tr<?php echo $_1_individuos->RowAttributes() ?>>
<?php

// Render list options (body, left)
$p_1_individuos_list->ListOptions->Render("body", "left", $p_1_individuos_list->RowCnt);
?>
	<?php if ($_1_individuos->id_individuo->Visible) { // id_individuo ?>
		<td<?php echo $_1_individuos->id_individuo->CellAttributes() ?>>
<span<?php echo $_1_individuos->id_individuo->ViewAttributes() ?>>
<?php echo $_1_individuos->id_individuo->ListViewValue() ?></span>
<a id="<?php echo $p_1_individuos_list->PageObjName . "_row_" . $p_1_individuos_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_1_individuos->id_especie->Visible) { // id_especie ?>
		<td<?php echo $_1_individuos->id_especie->CellAttributes() ?>>
<span<?php echo $_1_individuos->id_especie->ViewAttributes() ?>>
<?php echo $_1_individuos->id_especie->ListViewValue() ?></span>
<a id="<?php echo $p_1_individuos_list->PageObjName . "_row_" . $p_1_individuos_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_1_individuos->calle->Visible) { // calle ?>
		<td<?php echo $_1_individuos->calle->CellAttributes() ?>>
<span<?php echo $_1_individuos->calle->ViewAttributes() ?>>
<?php echo $_1_individuos->calle->ListViewValue() ?></span>
<a id="<?php echo $p_1_individuos_list->PageObjName . "_row_" . $p_1_individuos_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_1_individuos->alt_ini->Visible) { // alt_ini ?>
		<td<?php echo $_1_individuos->alt_ini->CellAttributes() ?>>
<span<?php echo $_1_individuos->alt_ini->ViewAttributes() ?>>
<?php echo $_1_individuos->alt_ini->ListViewValue() ?></span>
<a id="<?php echo $p_1_individuos_list->PageObjName . "_row_" . $p_1_individuos_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_1_individuos->espacio_verde->Visible) { // espacio_verde ?>
		<td<?php echo $_1_individuos->espacio_verde->CellAttributes() ?>>
<span<?php echo $_1_individuos->espacio_verde->ViewAttributes() ?>>
<?php echo $_1_individuos->espacio_verde->ListViewValue() ?></span>
<a id="<?php echo $p_1_individuos_list->PageObjName . "_row_" . $p_1_individuos_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($_1_individuos->id_fuente->Visible) { // id_fuente ?>
		<td<?php echo $_1_individuos->id_fuente->CellAttributes() ?>>
<span<?php echo $_1_individuos->id_fuente->ViewAttributes() ?>>
<?php echo $_1_individuos->id_fuente->ListViewValue() ?></span>
<a id="<?php echo $p_1_individuos_list->PageObjName . "_row_" . $p_1_individuos_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$p_1_individuos_list->ListOptions->Render("body", "right", $p_1_individuos_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($_1_individuos->CurrentAction <> "gridadd")
		$p_1_individuos_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($_1_individuos->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($p_1_individuos_list->Recordset)
	$p_1_individuos_list->Recordset->Close();
?>
<?php if ($p_1_individuos_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($_1_individuos->CurrentAction <> "gridadd" && $_1_individuos->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($p_1_individuos_list->Pager)) $p_1_individuos_list->Pager = new cPrevNextPager($p_1_individuos_list->StartRec, $p_1_individuos_list->DisplayRecs, $p_1_individuos_list->TotalRecs) ?>
<?php if ($p_1_individuos_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($p_1_individuos_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_1_individuos_list->PageUrl() ?>start=<?php echo $p_1_individuos_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($p_1_individuos_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_1_individuos_list->PageUrl() ?>start=<?php echo $p_1_individuos_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $p_1_individuos_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($p_1_individuos_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_1_individuos_list->PageUrl() ?>start=<?php echo $p_1_individuos_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($p_1_individuos_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $p_1_individuos_list->PageUrl() ?>start=<?php echo $p_1_individuos_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" disabled="disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $p_1_individuos_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $p_1_individuos_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $p_1_individuos_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $p_1_individuos_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($p_1_individuos_list->SearchWhere == "0=101") { ?>
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
	foreach ($p_1_individuos_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
f_1_individuoslistsrch.Init();
f_1_individuoslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$p_1_individuos_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$p_1_individuos_list->Page_Terminate();
?>
