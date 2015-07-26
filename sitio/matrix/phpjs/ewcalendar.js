// Create calendar
function ew_CreateCalendar(formid, id, format) {
	if (id.indexOf("$rowindex$") > -1)
		return;
	var $ = jQuery, el = ew_GetElement(id, formid), $el = $(el),
		$btn = $el.next("button[id^=cal_]").css("height", $el.outerHeight());
	$el.data("calendar", Calendar.setup({
		inputField: el, // input field
		showsTime: / %H:%M:%S$/.test(format), // shows time
		ifFormat: format, // date format
		button: $btn[0], // button
		cache: true // reuse the same calendar object, where possible
	})).wrap("<span class=\"input-append\"></span>").after($btn);
}
