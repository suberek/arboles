<?php if (@!$gbSkipHeaderFooter) { ?>
			<!-- right column (end) -->
			<?php if (isset($gTimer)) $gTimer->Stop() ?>
		</td></tr>
	</table>
	<!-- content (end) -->
<?php if (!ew_IsMobile()) { ?>
	<!-- footer (begin) --><!-- *** Note: Only licensed users are allowed to remove or change the following copyright statement. *** -->
	<div id="ewFooterRow" class="ewFooterRow">	
		<div class="ewFooterText"><?php echo $Language->ProjectPhrase("FooterText") ?></div>
		<!-- Place other links, for example, disclaimer, here -->		
	</div>
	<!-- footer (end) -->	
<?php } ?>
</div>
<?php } ?>
<?php if (ew_IsMobile()) { ?>
	</div>
	<!-- footer (begin) --><!-- *** Note: Only licensed users are allowed to remove or change the following copyright statement. *** -->
<!-- *** Remove comment lines to show footer for mobile
	<div data-role="footer">
		<h4><?php echo $Language->ProjectPhrase("FooterText") ?></h4>
	</div>
*** -->
	<!-- footer (end) -->	
</div>
<script type="text/javascript">
$("#ewPageTitle").html($("#ewPageCaption").text());
</script>
<?php } ?>
<?php if (@$_GET["_row"] <> "") { ?>
<script type="text/javascript">
jQuery.later(1000, null, function() {
	jQuery("#<?php echo $_GET["_row"] ?>").each(function() { this.scrollIntoView(); }
});
</script>
<?php } ?>
<!-- message box -->
<div id="ewMsgBox" class="modal hide" data-backdrop="false"><div class="modal-body"></div><div class="modal-footer"><a href="#" class="btn btn-primary ewButton" data-dismiss="modal" aria-hidden="true"><?php echo $Language->Phrase("MessageOK") ?></a></div></div>
<!-- tooltip -->
<div id="ewTooltip"></div>
<script type="text/javascript">

// Write your global startup script here
// document.write("page loaded");

</script>
</body>
</html>
