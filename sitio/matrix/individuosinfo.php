<?php

// Global variable for table object
$individuos = NULL;

//
// Table class for individuos
//
class cindividuos extends cTable {
	var $id_individuo;
	var $id_especie;
	var $calle;
	var $alt_ini;
	var $ALTURA_TOT;
	var $DIAMETRO;
	var $INCLINACIO;
	var $lat;
	var $lng;
	var $espacio_verde;
	var $id_usuario;
	var $fecha_creacion;
	var $fecha_modificacion;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'individuos';
		$this->TableName = 'individuos';
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

		// id_individuo
		$this->id_individuo = new cField('individuos', 'individuos', 'x_id_individuo', 'id_individuo', '`id_individuo`', '`id_individuo`', 3, -1, FALSE, '`id_individuo`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->id_individuo->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_individuo'] = &$this->id_individuo;

		// id_especie
		$this->id_especie = new cField('individuos', 'individuos', 'x_id_especie', 'id_especie', '`id_especie`', '`id_especie`', 3, -1, FALSE, '`EV__id_especie`', TRUE, TRUE, TRUE, 'FORMATTED TEXT');
		$this->id_especie->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_especie'] = &$this->id_especie;

		// calle
		$this->calle = new cField('individuos', 'individuos', 'x_calle', 'calle', '`calle`', '`calle`', 200, -1, FALSE, '`calle`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['calle'] = &$this->calle;

		// alt_ini
		$this->alt_ini = new cField('individuos', 'individuos', 'x_alt_ini', 'alt_ini', '`alt_ini`', '`alt_ini`', 3, -1, FALSE, '`alt_ini`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->alt_ini->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['alt_ini'] = &$this->alt_ini;

		// ALTURA_TOT
		$this->ALTURA_TOT = new cField('individuos', 'individuos', 'x_ALTURA_TOT', 'ALTURA_TOT', '`ALTURA_TOT`', '`ALTURA_TOT`', 3, -1, FALSE, '`ALTURA_TOT`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->ALTURA_TOT->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['ALTURA_TOT'] = &$this->ALTURA_TOT;

		// DIAMETRO
		$this->DIAMETRO = new cField('individuos', 'individuos', 'x_DIAMETRO', 'DIAMETRO', '`DIAMETRO`', '`DIAMETRO`', 3, -1, FALSE, '`DIAMETRO`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->DIAMETRO->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['DIAMETRO'] = &$this->DIAMETRO;

		// INCLINACIO
		$this->INCLINACIO = new cField('individuos', 'individuos', 'x_INCLINACIO', 'INCLINACIO', '`INCLINACIO`', '`INCLINACIO`', 3, -1, FALSE, '`INCLINACIO`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->INCLINACIO->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['INCLINACIO'] = &$this->INCLINACIO;

		// lat
		$this->lat = new cField('individuos', 'individuos', 'x_lat', 'lat', '`lat`', '`lat`', 4, -1, FALSE, '`lat`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->lat->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['lat'] = &$this->lat;

		// lng
		$this->lng = new cField('individuos', 'individuos', 'x_lng', 'lng', '`lng`', '`lng`', 4, -1, FALSE, '`lng`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->lng->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['lng'] = &$this->lng;

		// espacio_verde
		$this->espacio_verde = new cField('individuos', 'individuos', 'x_espacio_verde', 'espacio_verde', '`espacio_verde`', '`espacio_verde`', 200, -1, FALSE, '`espacio_verde`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['espacio_verde'] = &$this->espacio_verde;

		// id_usuario
		$this->id_usuario = new cField('individuos', 'individuos', 'x_id_usuario', 'id_usuario', '`id_usuario`', '`id_usuario`', 3, -1, FALSE, '`EV__id_usuario`', TRUE, TRUE, TRUE, 'FORMATTED TEXT');
		$this->id_usuario->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['id_usuario'] = &$this->id_usuario;

		// fecha_creacion
		$this->fecha_creacion = new cField('individuos', 'individuos', 'x_fecha_creacion', 'fecha_creacion', '`fecha_creacion`', 'DATE_FORMAT(`fecha_creacion`, \'%d/%m/%Y\')', 135, 7, FALSE, '`fecha_creacion`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fecha_creacion->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['fecha_creacion'] = &$this->fecha_creacion;

		// fecha_modificacion
		$this->fecha_modificacion = new cField('individuos', 'individuos', 'x_fecha_modificacion', 'fecha_modificacion', '`fecha_modificacion`', 'DATE_FORMAT(`fecha_modificacion`, \'%d/%m/%Y\')', 135, 7, FALSE, '`fecha_modificacion`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fecha_modificacion->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['fecha_modificacion'] = &$this->fecha_modificacion;
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
		return "`individuos`";
	}

	function SqlSelect() { // Select
		return "SELECT * FROM " . $this->SqlFrom();
	}

	function SqlSelectList() { // Select for List page
		return "SELECT * FROM (" .
			"SELECT *, (SELECT CONCAT(`NOMBRE_CIE`,'" . ew_ValueSeparator(1, $this->id_especie) . "',`NOMBRE_COM`) FROM `especies` `EW_TMP_LOOKUPTABLE` WHERE `EW_TMP_LOOKUPTABLE`.`id_especie` = `individuos`.`id_especie` LIMIT 1) AS `EV__id_especie`, (SELECT `nombre_completo` FROM `usuarios` `EW_TMP_LOOKUPTABLE` WHERE `EW_TMP_LOOKUPTABLE`.`id` = `individuos`.`id_usuario` LIMIT 1) AS `EV__id_usuario` FROM `individuos`" .
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
		if ($this->BasicSearch->getKeyword() <> "")
			return TRUE;
		if ($this->id_especie->AdvancedSearch->SearchValue <> "" ||
			$this->id_especie->AdvancedSearch->SearchValue2 <> "" ||
			strpos($sWhere, " " . $this->id_especie->FldVirtualExpression . " ") !== FALSE)
			return TRUE;
		if (strpos($sOrderBy, " " . $this->id_especie->FldVirtualExpression . " ") !== FALSE)
			return TRUE;
		if ($this->id_usuario->AdvancedSearch->SearchValue <> "" ||
			$this->id_usuario->AdvancedSearch->SearchValue2 <> "" ||
			strpos($sWhere, " " . $this->id_usuario->FldVirtualExpression . " ") !== FALSE)
			return TRUE;
		if (strpos($sOrderBy, " " . $this->id_usuario->FldVirtualExpression . " ") !== FALSE)
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
	var $UpdateTable = "`individuos`";

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
			if (array_key_exists('id_individuo', $rs))
				ew_AddFilter($where, ew_QuotedName('id_individuo') . '=' . ew_QuotedValue($rs['id_individuo'], $this->id_individuo->FldDataType));
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
		return "`id_individuo` = @id_individuo@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->id_individuo->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@id_individuo@", ew_AdjustSql($this->id_individuo->CurrentValue), $sKeyFilter); // Replace key value
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
			return "individuoslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "individuoslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("individuosview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("individuosview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "individuosadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("individuosedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("individuosadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("individuosdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->id_individuo->CurrentValue)) {
			$sUrl .= "id_individuo=" . urlencode($this->id_individuo->CurrentValue);
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
			$arKeys[] = @$_GET["id_individuo"]; // id_individuo

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
			$this->id_individuo->CurrentValue = $key;
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
		$this->id_individuo->setDbValue($rs->fields('id_individuo'));
		$this->id_especie->setDbValue($rs->fields('id_especie'));
		$this->calle->setDbValue($rs->fields('calle'));
		$this->alt_ini->setDbValue($rs->fields('alt_ini'));
		$this->ALTURA_TOT->setDbValue($rs->fields('ALTURA_TOT'));
		$this->DIAMETRO->setDbValue($rs->fields('DIAMETRO'));
		$this->INCLINACIO->setDbValue($rs->fields('INCLINACIO'));
		$this->lat->setDbValue($rs->fields('lat'));
		$this->lng->setDbValue($rs->fields('lng'));
		$this->espacio_verde->setDbValue($rs->fields('espacio_verde'));
		$this->id_usuario->setDbValue($rs->fields('id_usuario'));
		$this->fecha_creacion->setDbValue($rs->fields('fecha_creacion'));
		$this->fecha_modificacion->setDbValue($rs->fields('fecha_modificacion'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
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
				if ($this->id_individuo->Exportable) $Doc->ExportCaption($this->id_individuo);
				if ($this->id_especie->Exportable) $Doc->ExportCaption($this->id_especie);
				if ($this->calle->Exportable) $Doc->ExportCaption($this->calle);
				if ($this->alt_ini->Exportable) $Doc->ExportCaption($this->alt_ini);
				if ($this->ALTURA_TOT->Exportable) $Doc->ExportCaption($this->ALTURA_TOT);
				if ($this->DIAMETRO->Exportable) $Doc->ExportCaption($this->DIAMETRO);
				if ($this->INCLINACIO->Exportable) $Doc->ExportCaption($this->INCLINACIO);
				if ($this->lat->Exportable) $Doc->ExportCaption($this->lat);
				if ($this->lng->Exportable) $Doc->ExportCaption($this->lng);
				if ($this->espacio_verde->Exportable) $Doc->ExportCaption($this->espacio_verde);
				if ($this->id_usuario->Exportable) $Doc->ExportCaption($this->id_usuario);
				if ($this->fecha_creacion->Exportable) $Doc->ExportCaption($this->fecha_creacion);
				if ($this->fecha_modificacion->Exportable) $Doc->ExportCaption($this->fecha_modificacion);
			} else {
				if ($this->id_individuo->Exportable) $Doc->ExportCaption($this->id_individuo);
				if ($this->id_especie->Exportable) $Doc->ExportCaption($this->id_especie);
				if ($this->calle->Exportable) $Doc->ExportCaption($this->calle);
				if ($this->alt_ini->Exportable) $Doc->ExportCaption($this->alt_ini);
				if ($this->ALTURA_TOT->Exportable) $Doc->ExportCaption($this->ALTURA_TOT);
				if ($this->DIAMETRO->Exportable) $Doc->ExportCaption($this->DIAMETRO);
				if ($this->INCLINACIO->Exportable) $Doc->ExportCaption($this->INCLINACIO);
				if ($this->lat->Exportable) $Doc->ExportCaption($this->lat);
				if ($this->lng->Exportable) $Doc->ExportCaption($this->lng);
				if ($this->espacio_verde->Exportable) $Doc->ExportCaption($this->espacio_verde);
				if ($this->id_usuario->Exportable) $Doc->ExportCaption($this->id_usuario);
				if ($this->fecha_creacion->Exportable) $Doc->ExportCaption($this->fecha_creacion);
				if ($this->fecha_modificacion->Exportable) $Doc->ExportCaption($this->fecha_modificacion);
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
					if ($this->id_individuo->Exportable) $Doc->ExportField($this->id_individuo);
					if ($this->id_especie->Exportable) $Doc->ExportField($this->id_especie);
					if ($this->calle->Exportable) $Doc->ExportField($this->calle);
					if ($this->alt_ini->Exportable) $Doc->ExportField($this->alt_ini);
					if ($this->ALTURA_TOT->Exportable) $Doc->ExportField($this->ALTURA_TOT);
					if ($this->DIAMETRO->Exportable) $Doc->ExportField($this->DIAMETRO);
					if ($this->INCLINACIO->Exportable) $Doc->ExportField($this->INCLINACIO);
					if ($this->lat->Exportable) $Doc->ExportField($this->lat);
					if ($this->lng->Exportable) $Doc->ExportField($this->lng);
					if ($this->espacio_verde->Exportable) $Doc->ExportField($this->espacio_verde);
					if ($this->id_usuario->Exportable) $Doc->ExportField($this->id_usuario);
					if ($this->fecha_creacion->Exportable) $Doc->ExportField($this->fecha_creacion);
					if ($this->fecha_modificacion->Exportable) $Doc->ExportField($this->fecha_modificacion);
				} else {
					if ($this->id_individuo->Exportable) $Doc->ExportField($this->id_individuo);
					if ($this->id_especie->Exportable) $Doc->ExportField($this->id_especie);
					if ($this->calle->Exportable) $Doc->ExportField($this->calle);
					if ($this->alt_ini->Exportable) $Doc->ExportField($this->alt_ini);
					if ($this->ALTURA_TOT->Exportable) $Doc->ExportField($this->ALTURA_TOT);
					if ($this->DIAMETRO->Exportable) $Doc->ExportField($this->DIAMETRO);
					if ($this->INCLINACIO->Exportable) $Doc->ExportField($this->INCLINACIO);
					if ($this->lat->Exportable) $Doc->ExportField($this->lat);
					if ($this->lng->Exportable) $Doc->ExportField($this->lng);
					if ($this->espacio_verde->Exportable) $Doc->ExportField($this->espacio_verde);
					if ($this->id_usuario->Exportable) $Doc->ExportField($this->id_usuario);
					if ($this->fecha_creacion->Exportable) $Doc->ExportField($this->fecha_creacion);
					if ($this->fecha_modificacion->Exportable) $Doc->ExportField($this->fecha_modificacion);
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
