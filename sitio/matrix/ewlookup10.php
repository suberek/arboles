<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "userfn10.php" ?>
<?php
ew_Header(FALSE, 'utf-8');
$lookup = new clookup;
$lookup->Page_Main();

//
// Page class for lookup
//
class clookup {

	// Page ID
	var $PageID = "lookup";

	// Project ID
	var $ProjectID = "{E8FDA5CE-82C7-4B68-84A5-21FD1A691C43}";

	// Page object name
	var $PageObjName = "lookup";

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		return ew_CurrentPage() . "?";
	}

	// Main
	function Page_Main() {
		$GLOBALS["Page"] = &$this;
		$post = ew_StripSlashes($_POST);
		if (count($post) == 0)
			die("Missing post data.");

		//$sql = $qs->getValue("s");
		$sql = @$post["s"];
		$sql = ew_Decrypt($sql);
		if ($sql == "")
			die("Missing SQL.");
		if (strpos($sql, "{filter}") > 0) {
			$filters = "";
			for ($i = 0; $i < 5; $i++) {

				// Get the filter values (for "IN")
				$filter = ew_Decrypt(@$post["f" . $i]);
				if ($filter <> "") {
					$value = @$post["v" . $i];
					if ($value == "") {
						if ($i > 0) // Empty parent field

							//continue; // Allow
							ew_AddFilter($filters, "1=0"); // Disallow
						continue;
					}
					$arValue = explode(",", $value);
					$fldtype = intval(@$post["t" . $i]);
					for ($j = 0, $cnt = count($arValue); $j < $cnt; $j++)
						$arValue[$j] = ew_QuotedValue($arValue[$j], ew_FieldDataType($fldtype));
					$filter = str_replace("{filter_value}", implode(",", $arValue), $filter);
					ew_AddFilter($filters, $filter);
				}
			}
			$sql = str_replace("{filter}", ($filters <> "") ? $filters : "1=1", $sql);
		}

		// Get the query value (for "LIKE" or "=")
		$value = ew_AdjustSql(@$post["q"]);
		if ($value <> "") {
			$sql = preg_replace('/LIKE \'(%)?\{query_value\}%\'/', ew_Like('\'$1{query_value}%\''), $sql);
			$sql = str_replace("{query_value}", $value, $sql);
		}

		// Replace {query_value_n}
		preg_match_all('/\{query_value_(\d+)\}/', $sql, $out);
		$cnt = count($out[0]);
		for ($i = 0; $i < $cnt; $i++) {
			$j = $out[1][$i];
			$v = ew_AdjustSql(@$post["q" . $j]);
			$sql = str_replace("{query_value_" . $j . "}", $v, $sql);
		}
		$this->GetLookupValues($sql);
	}

	// Get lookup values
	function GetLookupValues($sql) {
		$rsarr = array();
		$rowcnt = 0;
		$conn = ew_Connect();
		if ($rs = $conn->Execute($sql)) {
			$rowcnt = $rs->RecordCount();
			$fldcnt = $rs->FieldCount();
			$rsarr = $rs->GetRows();
			$rs->Close();
		}
		$conn->Close();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Output
		if (is_array($rsarr) && $rowcnt > 0) {
			for ($i = 0; $i < $rowcnt; $i++) {
				for ($j = 0; $j < $fldcnt; $j++) {
					$str = strval($rsarr[$i][$j]);
					$str = ew_ConvertToUtf8($str);
					if (isset($post["keepCRLF"])) {
						$str = str_replace(array("\r", "\n"), array("\\r", "\\n"), $str);
					} else {
						$str = str_replace(array("\r", "\n"), array(" ", " "), $str);
					}
					$rsarr[$i][$j] = $str;
				}
			}
		}
		echo ew_ArrayToJson($rsarr);
	}
}
?>
