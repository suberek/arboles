<form id="ewEmailForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<input type="hidden" name="export" id="export" value="email">
<div class="ewEmailContent">
	<div class="control-group">
		<label class="control-label" for="sender"><?php echo $Language->Phrase("EmailFormSender") ?></label>
		<div class="controls"><input type="text" name="sender" id="sender" size="30"></div>
	</div>
	<div class="control-group">
		<label class="control-label" for="recipient"><?php echo $Language->Phrase("EmailFormRecipient") ?></label>
		<div class="controls"><input type="text" name="recipient" id="recipient" size="30"></div>
	</div>
	<div class="control-group">
		<label class="control-label" for="cc"><?php echo $Language->Phrase("EmailFormCc") ?></label>
		<div class="controls"><input type="text" name="cc" id="cc" size="30"></div>
	</div>
	<div class="control-group">
		<label class="control-label" for="bcc"><?php echo $Language->Phrase("EmailFormBcc") ?></label>
		<div class="controls"><input type="text" name="bcc" id="bcc" size="30"></div>
	</div>
	<div class="control-group">
		<label class="control-label" for="subject"><?php echo $Language->Phrase("EmailFormSubject") ?></label>
		<div class="controls"><input type="text" name="subject" id="subject" size="50"></div>
	</div>
	<div class="control-group">
		<label class="control-label" for="message"><?php echo $Language->Phrase("EmailFormMessage") ?></label>
		<div class="controls"><textarea cols="50" rows="6" name="message" id="message"></textarea></div>
	</div>
	<div class="control-group">
		<label class="control-label" for="contenttype"><?php echo $Language->Phrase("EmailFormContentType") ?></label>
		<div class="controls">
		<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="contenttype" value="html" checked="checked"><?php echo $Language->Phrase("EmailFormContentTypeHtml") ?></label>
		<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="contenttype" value="url"><?php echo $Language->Phrase("EmailFormContentTypeUrl") ?></label>
		</div>
	</div>
</div>
</form>
