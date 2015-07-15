$(document).ready(function() {
	$('.sortable').sortable(	{
	   update: function(event, ui) {
			//console.log(ui.item.find('input').val(), ui.item.index())
			ui.item.find('input').val(ui.item.index())
		}
	});
	$("form").submit(function() {
		return checkBindConflicts();
	});
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
	/*
	var port = field.val(),
			ip = typeof ip !== "undefined" ? ip : field.data("ip"),
			orig = field.data("orig"),
			status = true;

	$.each(binds, function(k, v) {
		if(k == ip) {
			$.each(v, function(x, z) {
				if(z.port == port && port != orig) {
					status = false;
					warnInvalid(field, sprintf(_("Port Conflict with Chan %s"),z.type.toUpperCase()));
					return false;
				}
			});
		}
	})

	return status;
	*/
}
