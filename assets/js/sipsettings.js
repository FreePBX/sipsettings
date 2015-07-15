var changed = false;

$(document).ready(function() {
	$('.sortable').sortable(	{
	   update: function(event, ui) {
			//console.log(ui.item.find('input').val(), ui.item.index())
			ui.item.find('input').val(ui.item.index())
		}
	});
	$("form").submit(function() {
		if(changed) {
			alert(_("Port/Bind Address has changed. This requires an Asterisk restart after Apply Config"));
		}
		return checkBindConflicts();
	});
	$("#editSip #bindaddr").bind('input propertychange', function() {
		changed = true;
		console.log(changed);
	});
	$("#editSip #bindport").bind('input propertychange', function() {
		changed = true;
	})
	$(".port").bind('input propertychange', function() {
		changed = true;
	})
});

/**
 * Check for port conflicts
 * @return {bool} true if we can proceed, false otherwise
 */
function checkBindConflicts() {
	if($("#editSip").length > 0 && $("#pjsipform").length > 0) {
		var sipaddr = $("#editSip #bindaddr").val(),
				sipport = $("#editSip #bindport").val(),
				submit = true;

		sipaddr = (sipaddr.trim() != "") ? sipaddr : '0.0.0.0';
		sipport = (sipport.trim() != "") ? sipport : '5060';

		$(".port").each(function() {
			var ip = $(this).data("ip");
			if(sipaddr == ip && sipport == $(this).val()) {
				submit = false;
				warnInvalid($(this),_("PJSIP transport port conflicts with SIP port"));
				return false;
			}
		})
	}
	return submit;
}
