/*
 * This is all the jquery that is used in the regular user's website.
 */

$(document).ready(function(){
	var AJAXPAGE = 'AJAX/userAjax.php';
    $('#prodItem').on({
		'change'	: function()	{
			//This is used to check if the Selected item can be produced or Not.
			var itemId = $(this).val();
			if(itemId == '0')	{
				$('#prodItemReqStat').html('');
			} else {
				$.post(AJAXPAGE, {'ajaxPageName' : 'prodItemReqStat', 'itemId' : itemId}, function(data)	{
					$('#prodItemReqStat').html(data);
				});
			}
		}
	});
	$('#produceSelectedItem').live(
		'click'	, function()	{
			var itemId = $('#prodItem').val();
			$.post(AJAXPAGE, {'ajaxPageName' : 'prodItemConfirm', 'itemId' : itemId}, function(data)	{
				if(data == '0')	{
					//The item has been added to the production queue.
					alert('Item Successfully added to the Production Queue.');
				} else if(data == '1')	{
					alert('You dont have enough of the required items to Produce the new Item.');
				} else {
					alert('Unable to produce item. Try again.');
				}
				window.location = 'production.php';
			});
		}
	);
});