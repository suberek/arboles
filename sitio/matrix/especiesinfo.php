<?php

// Global variable for table object
$especies = NULL;

//
// Table class for especies
//
class cespecies extends cTable {
	var $id_especie;
	var $id_familia;
	var $NOMBRE_FAM;
	var $NOMBRE_CIE;
	var $NOMBRE_COM;
	var $TIPO_FOLLA;
	var $ORIGEN;
	var $ICONO;
	var $imagen_completo;
	var $imagen_hoja;
	var $imagen_flor;
	var $descripcion;
	var $medicinal;
	var $comestible;
	var $perfume;
	var $abejas;
	var $mariposas;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'especies';
		$this->TableName = 'especies';
		$this->TableType = 'TABLE';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// id_especie
		$this->id_especie = new cField('especies', 'especies', 'x_id_especie', 'id_especie', '`id_especie`', '`id_especie`', 3, -1, FALSE, '`id_especie`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->id_especie->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_especie'] = &$this->id_especie;

		// id_familia
		$this->id_familia = new cField('especies', 'especies', 'x_id_familia', 'id_familia', '`id_familia`', '`id_familia`', 3, -1, FALSE, '`EV__id_familia`', TRUE, FALSE, TRUE, 'FORMATTED TEXT');
		$this->id_familia->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_familia'] = &$this->id_familia;

		// NOMBRE_FAM
		$this->NOMBRE_FAM = new cField('especies', 'especies', 'x_NOMBRE_FAM', 'NOMBRE_FAM', '`NOMBRE_FAM`', '`NOMBRE_FAM`', 200, -1, FALSE, '`NOMBRE_FAM`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['NOMBRE_FAM'] = &$this->NOMBRE_FAM;

		// NOMBRE_CIE
		$this->NOMBRE_CIE = new cField('especies', 'especies', 'x_NOMBRE_CIE', 'NOMBRE_CIE', '`NOMBRE_CIE`', '`NOMBRE_CIE`', 200, -1, FALSE, '`NOMBRE_CIE`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['NOMBRE_CIE'] = &$this->NOMBRE_CIE;

		// NOMBRE_COM
		$this->NOMBRE_COM = new cField('especies', 'especies', 'x_NOMBRE_COM', 'NOMBRE_COM', '`NOMBRE_COM`', '`NOMBRE_COM`', 200, -1, FALSE, '`NOMBRE_COM`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['NOMBRE_COM'] = &$this->NOMBRE_COM;

		// TIPO_FOLLA
		$this->TIPO_FOLLA = new cField('especies', 'especies', 'x_TIPO_FOLLA', 'TIPO_FOLLA', '`TIPO_FOLLA`', '`TIPO_FOLLA`', 200, -1, FALSE, '`TIPO_FOLLA`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['TIPO_FOLLA'] = &$this->TIPO_FOLLA;

		// ORIGEN
		$this->ORIGEN = new cField('especies', 'especies', 'x_ORIGEN', 'ORIGEN', '`ORIGEN`', '`ORIGEN`', 200, -1, FALSE, '`ORIGEN`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['ORIGEN'] = &$this->ORIGEN;

		// ICONO
		$this->ICONO = new cField('especies', 'especies', 'x_ICONO', 'ICONO', '`ICONO`', '`ICONO`', 200, -1, TRUE, '`ICONO`', FALSE, FALSE, FALSE, 'IMAGE');
		$this->fields['ICONO'] = &$this->ICONO;

		// imagen_completo
		$this->imagen_completo = new cField('especies', 'especies', 'x_imagen_completo', 'imagen_completo', '`imagen_completo`', '`imagen_completo`', 200, -1, TRUE, '`imagen_completo`', FALSE, FALSE, FALSE, 'IMAGE');
		$this->fields['imagen_completo'] = &$this->imagen_completo;

		// imagen_hoja
		$this->imagen_hoja = new cField('especies', 'especies', 'x_imagen_hoja', 'imagen_hoja', '`imagen_hoja`', '`imagen_hoja`', 200, -1, TRUE, '`imagen_hoja`', FALSE, FALSE, FALSE, 'IMAGE');
		$this->fields['imagen_hoja'] = &$this->imagen_hoja;

		// imagen_flor
		$this->imagen_flor = new cField('especies', 'especies', 'x_imagen_flor', 'imagen_flor', '`imagen_flor`', '`imagen_flor`', 200, -1, TRUE, '`imagen_flor`', FALSE, FALSE, FALSE, 'IMAGE');
		$this->fields['imagen_flor'] = &$this->imagen_flor;

		// descripcion
		$this->descripcion = new cField('especies', 'especies', 'x_descripcion', 'descripcion', '`descripcion`', '`descripcion`', 201, -1, FALSE, '`descripcion`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['descripcion'] = &$this->descripcion;

		// medicinal
		$this->medicinal = new cField('especies', 'especies', 'x_medicinal', 'medicinal', '`medicinal`', '`medicinal`', 201, -1, FALSE, '`medicinal`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['medicinal'] = &$this->medicinal;

		// comestible
		$this->comestible = new cField('especies', 'especies', 'x_comestible', 'comestible', '`comestible`', '`comestible`', 201, -1, FALSE, '`comestible`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['comestible'] = &$this->comestible;

		// perfume
		$this->perfume = new cField('especies', 'especies', 'x_perfume', 'perfume', '`perfume`', '`perfume`', 16, -1, FALSE, '`perfume`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->perfume->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['perfume'] = &$this->perfume;

		// abejas
		$this->abejas = new cField('especies', 'especies', 'x_abejas', 'abejas', '`abejas`', '`abejas`', 16, -1, FALSE, '`abejas`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->abejas->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['abejas'] = &$this->abejas;

		// mariposas
		$this->mariposas = new cField('especies', 'especies', 'x_mariposas', 'mariposas', '`mariposas`', '`mariposas`', 16, -1, FALSE, '`mariposas`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->mariposas->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['mariposas'] = &$this->mariposas;
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
			$sSortFieldList = ($ofld->FldVirtualExpression <> "") ? $ofld->FldVirtualExpression : $sSortField;
			$this->setSessionOrderByList($sSortFieldList . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Session ORDER BY for List page
	function getSessionOrderByList() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_ORDER_BY_LIST];
	}

	function setSessionOrderByList($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_ORDER_BY_LIST] = $v;
	}

	// Table level SQL
	function SqlFrom() { // From
		return "`especies`";
	}

	function SqlSelect() { // Select
		return "SELECT * FROM " . $this->SqlFrom();
	}

	function SqlSelectList() { // Select for List page
		return "SELECT * FROM (" .
			"SELECT *, (SELECT `familia` FROM `familias` `EW_TMP_LOOKUPTABLE` WHERE `EW_TMP_LOOKUPTABLE`.`id` = `especies`.`id_familia` LIMIT 1) AS `EV__id_familia` FROM `especies`" .
			") `EW_TMP_TABLE`";
	}

	function SqlWhere() { // Where
		$sWhere = "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlGroupBy() { // Group By
		return "";
	}

	function SqlHaving() { // Having
		return "";
	}

	function SqlOrderBy() { // Order By
		return "";
	}

	// Check if Anonymous User is allowed
	function AllowAnonymousUser() {
		switch (@$this->PageID) {
			case "add":
			case "register":
			case "addopt":
				return FALSE;
			case "edit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return FALSE;
			case "delete":
				return FALSE;
			case "view":
				return FALSE;
			case "search":
				return FALSE;
			default:
				return FALSE;
		}
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		if ($this->UseVirtualFields()) {
			$sSort = $this->getSessionOrderByList();
			return ew_BuildSelectSql($this->SqlSelectList(), $this->SqlWhere(), $this->SqlGroupBy(), 
				$this->SqlHaving(), $this->SqlOrderBy(), $sFilter, $sSort);
		} else {
			$sSort = $this->getSessionOrderBy();
			return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(), $this->SqlGroupBy(),
				$this->SqlHaving(), $this->SqlOrderBy(), $sFilter, $sSort);
		}
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = ($this->UseVirtualFields()) ? $this->getSessionOrderByList() : $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->SqlOrderBy(), "", $sSort);
	}

	// Check if virtual fields is used in SQL
	function UseVirtualFields() {
		$sWhere = $this->getSessionWhere();
		$sOrderBy = $this->getSessionOrderByList();
		if ($sWhere <> "")
			$sWhere = " " . str_replace(array("(",")"), array("",""), $sWhere) . " ";
		if ($sOrderBy <> "")
			$sOrderBy = " " . str_replace(array("(",")"), array("",""), $sOrderBy) . " ";
		if ($this->id_familia->AdvancedSearch->SearchValue <> "" ||
			$this->id_familia->AdvancedSearch->SearchValue2 <> "" ||
			strpos($sWhere, " " . $this->id_familia->FldVirtualExpression . " ") !== FALSE)
			return TRUE;
		if (strpos($sOrderBy, " " . $this->id_familia->FldVirtualExpression . " ") !== FALSE)
			return TRUE;
		return FALSE;
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		global $conn;
		$cnt = -1;
		if ($this->TableType == 'TABLE' || $this->TableType == 'VIEW') {
			$sSql = "SELECT COUNT(*) FROM" . substr($sSql, 13);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		global $conn;
		$origFilter = $this->CurrentFilter;
		$this->Recordset_Selecting($this->CurrentFilter);
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Update Table
	var $UpdateTable = "`especies`";

	// INSERT statement
	function InsertSQL(&$rs) {
		global $conn;
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		global $conn;
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL) {
		global $conn;
		return $conn->Execute($this->UpdateSQL($rs, $where));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "") {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if ($rs) {
			if (array_key_exists('id_especie', $rs))
				ew_AddFilter($where, ew_QuotedName('id_especie') . '=' . ew_QuotedValue($rs['id_especie'], $this->id_especie->FldDataType));
		}
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->DeleteSQL($rs, $where));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`id_especie` = @id_especie@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->id_especie->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@id_especie@", ew_AdjustSql($this->id_especie->CurrentValue), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "especieslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "especieslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("especiesview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("especiesview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "especiesadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("especiesedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("especiesadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("especiesdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->id_especie->CurrentValue)) {
			$sUrl .= "id_especie=" . urlencode($this->id_especie->CurrentValue);
		} else {
			return "javascript:alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET)) {
			$arKeys[] = @$_GET["id_especie"]; // id_especie

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		foreach ($arKeys as $key) {
			if (!is_numeric($key))
				continue;
			$ar[] = $key;
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->id_especie->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {
		global $conn;

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
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
		$this->abejas->setDbValue($rs->fields('abejas'));
		$this->mariposas->setDbValue($rs->fields('mariposas'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
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

		// abejas
		$this->abejas->LinkCustomAttributes = "";
		$this->abejas->HrefValue = "";
		$this->abejas->TooltipValue = "";

		// mariposas
		$this->mariposas->LinkCustomAttributes = "";
		$this->mariposas->HrefValue = "";
		$this->mariposas->TooltipValue = "";

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
	}

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;

		// Write header
		$Doc->ExportTableHeader();
		if ($Doc->Horizontal) { // Horizontal format, write header
			$Doc->BeginExportRow();
			if ($ExportPageType == "view") {
				if ($this->id_especie->Exportable) $Doc->ExportCaption($this->id_especie);
				if ($this->id_familia->Exportable) $Doc->ExportCaption($this->id_familia);
				if ($this->NOMBRE_CIE->Exportable) $Doc->ExportCaption($this->NOMBRE_CIE);
				if ($this->NOMBRE_COM->Exportable) $Doc->ExportCaption($this->NOMBRE_COM);
				if ($this->TIPO_FOLLA->Exportable) $Doc->ExportCaption($this->TIPO_FOLLA);
				if ($this->ORIGEN->Exportable) $Doc->ExportCaption($this->ORIGEN);
				if ($this->ICONO->Exportable) $Doc->ExportCaption($this->ICONO);
				if ($this->imagen_completo->Exportable) $Doc->ExportCaption($this->imagen_completo);
				if ($this->imagen_hoja->Exportable) $Doc->ExportCaption($this->imagen_hoja);
				if ($this->imagen_flor->Exportable) $Doc->ExportCaption($this->imagen_flor);
				if ($this->descripcion->Exportable) $Doc->ExportCaption($this->descripcion);
				if ($this->medicinal->Exportable) $Doc->ExportCaption($this->medicinal);
				if ($this->comestible->Exportable) $Doc->ExportCaption($this->comestible);
				if ($this->perfume->Exportable) $Doc->ExportCaption($this->perfume);
				if ($this->abejas->Exportable) $Doc->ExportCaption($this->abejas);
				if ($this->mariposas->Exportable) $Doc->ExportCaption($this->mariposas);
			} else {
				if ($this->id_especie->Exportable) $Doc->ExportCaption($this->id_especie);
				if ($this->id_familia->Exportable) $Doc->ExportCaption($this->id_familia);
				if ($this->NOMBRE_CIE->Exportable) $Doc->ExportCaption($this->NOMBRE_CIE);
				if ($this->NOMBRE_COM->Exportable) $Doc->ExportCaption($this->NOMBRE_COM);
				if ($this->TIPO_FOLLA->Exportable) $Doc->ExportCaption($this->TIPO_FOLLA);
				if ($this->ORIGEN->Exportable) $Doc->ExportCaption($this->ORIGEN);
				if ($this->ICONO->Exportable) $Doc->ExportCaption($this->ICONO);
				if ($this->imagen_completo->Exportable) $Doc->ExportCaption($this->imagen_completo);
				if ($this->imagen_hoja->Exportable) $Doc->ExportCaption($this->imagen_hoja);
				if ($this->imagen_flor->Exportable) $Doc->ExportCaption($this->imagen_flor);
				if ($this->medicinal->Exportable) $Doc->ExportCaption($this->medicinal);
				if ($this->comestible->Exportable) $Doc->ExportCaption($this->comestible);
				if ($this->perfume->Exportable) $Doc->ExportCaption($this->perfume);
				if ($this->abejas->Exportable) $Doc->ExportCaption($this->abejas);
				if ($this->mariposas->Exportable) $Doc->ExportCaption($this->mariposas);
			}
			$Doc->EndExportRow();
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
				if ($ExportPageType == "view") {
					if ($this->id_especie->Exportable) $Doc->ExportField($this->id_especie);
					if ($this->id_familia->Exportable) $Doc->ExportField($this->id_familia);
					if ($this->NOMBRE_CIE->Exportable) $Doc->ExportField($this->NOMBRE_CIE);
					if ($this->NOMBRE_COM->Exportable) $Doc->ExportField($this->NOMBRE_COM);
					if ($this->TIPO_FOLLA->Exportable) $Doc->ExportField($this->TIPO_FOLLA);
					if ($this->ORIGEN->Exportable) $Doc->ExportField($this->ORIGEN);
					if ($this->ICONO->Exportable) $Doc->ExportField($this->ICONO);
					if ($this->imagen_completo->Exportable) $Doc->ExportField($this->imagen_completo);
					if ($this->imagen_hoja->Exportable) $Doc->ExportField($this->imagen_hoja);
					if ($this->imagen_flor->Exportable) $Doc->ExportField($this->imagen_flor);
					if ($this->descripcion->Exportable) $Doc->ExportField($this->descripcion);
					if ($this->medicinal->Exportable) $Doc->ExportField($this->medicinal);
					if ($this->comestible->Exportable) $Doc->ExportField($this->comestible);
					if ($this->perfume->Exportable) $Doc->ExportField($this->perfume);
					if ($this->abejas->Exportable) $Doc->ExportField($this->abejas);
					if ($this->mariposas->Exportable) $Doc->ExportField($this->mariposas);
				} else {
					if ($this->id_especie->Exportable) $Doc->ExportField($this->id_especie);
					if ($this->id_familia->Exportable) $Doc->ExportField($this->id_familia);
					if ($this->NOMBRE_CIE->Exportable) $Doc->ExportField($this->NOMBRE_CIE);
					if ($this->NOMBRE_COM->Exportable) $Doc->ExportField($this->NOMBRE_COM);
					if ($this->TIPO_FOLLA->Exportable) $Doc->ExportField($this->TIPO_FOLLA);
					if ($this->ORIGEN->Exportable) $Doc->ExportField($this->ORIGEN);
					if ($this->ICONO->Exportable) $Doc->ExportField($this->ICONO);
					if ($this->imagen_completo->Exportable) $Doc->ExportField($this->imagen_completo);
					if ($this->imagen_hoja->Exportable) $Doc->ExportField($this->imagen_hoja);
					if ($this->imagen_flor->Exportable) $Doc->ExportField($this->imagen_flor);
					if ($this->medicinal->Exportable) $Doc->ExportField($this->medicinal);
					if ($this->comestible->Exportable) $Doc->ExportField($this->comestible);
					if ($this->perfume->Exportable) $Doc->ExportField($this->perfume);
					if ($this->abejas->Exportable) $Doc->ExportField($this->abejas);
					if ($this->mariposas->Exportable) $Doc->ExportField($this->mariposas);
				}
				$Doc->EndExportRow();
			}
			$Recordset->MoveNext();
		}
		$Doc->ExportTableFooter();
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
