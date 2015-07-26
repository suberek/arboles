// create editor
function ew_CreateEditor(formid, name, cols, rows, readonly) {
	if (typeof CKEDITOR == "undefined" || name.indexOf("$rowindex$") > -1)
		return;
	var $ = jQuery, form = $("#" + formid)[0], el = ew_GetElement(name, form);
	if (!el)
		return;
	var args = {"id": name, "form": form, "enabled": true};
	$(el).trigger("create", [args]);
	if (!args.enabled)
		return;
	if (cols <= 0)
		cols = 35;
	if (rows <= 0)
		rows = 4;
	var w = cols * 20; // width multiplier
	var h = rows * 60; // height multiplier
	if (readonly) {
		new ew_ReadOnlyTextArea(el, w, h);
	} else {
		var longname = formid + "$" + name + "$";
		var path = window.location.href.substring(0, window.location.href.lastIndexOf("/") + 1);
		var editor = {
			name: name,
			active: false,
			instance: null,
			create: function() {
				this.instance = CKEDITOR.replace(el, { width: w, height: h,
					autoUpdateElement: false,
					baseHref: 'ckeditor/'
				});				
				CKEDITOR.instances[longname] = this.instance;
				delete CKEDITOR.instances[name];
				this.active = true;
			},			
			set: function() { // update value from textarea to editor
				if (this.instance) this.instance.setData(this.instance.element.value);
			},
			save: function() { // update value from editor to textarea
				if (this.instance) this.instance.updateElement();
				var args = {"id": name, "form": form, "value": ew_RemoveSpaces(el.value)};
				$(el).trigger("save", [args]).val(args.value);
			},
			focus: function() { // focus editor
				if (this.instance) this.instance.focus();
			},
			destroy: function() { // destroy
				if (this.instance) this.instance.destroy();
			}			
		};
		$(el).data("editor", editor).addClass("editor");
	}
}
