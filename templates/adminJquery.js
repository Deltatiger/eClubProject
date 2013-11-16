/*
 * This is the Ajax used in the Admin Part of the Website.
 */
 
$(document).ready(function()	{
	$('#addMoreDepends').on({
		'click'  : function()	{
			//This is used to add more items which are required to produce an item.
			var itemCount = $('#addItemDependCount').val();
			var newItemId = parseInt(itemCount) + 1;
			
			//We add the input boxes
			var newItemLinks = '<div class="itemDependHolder"> Requires <select name="itemReq' + newItemId + '" id="itemReq' + newItemId + '"> </select> Qty : <input type="text" name="itemQty' + newItemId + '" /></div>';
			$('#adminItemDepends').append(newItemLinks);
			$('#addItemDependCount').val(newItemId);
			
			//We use AJAX to get the list of all items in the DB.
			$.post('../AJAX/adminAjax.php', {'ajaxPageName' : 'reqItemsList'}, function(data)	{
				$('#itemReq' + newItemId).append(data);
			});
		}
	});

	$('#dayChangeForm').submit(function()	{
		var conf = confirm("Confirm day change.");
		return conf;
	});
});