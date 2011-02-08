$(document).ready(function() {
 $('#codec_list').sortable(	{
	   update: function(event, ui) {
			//console.log(ui.item.find('input').val(), ui.item.index())
			ui.item.find('input').val(ui.item.index())
		}
	})
});