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
				$.post(AJAXPAGE, {ajaxPageName : 'prodItemReqStat', itemId : itemId}, function(data)	{
					$('#prodItemReqStat').html(data);
				});
			}
		}
	});
	$('#produceSelectedItem').live(
		'click'	, function()	{
			var itemId = $('#prodItem').val();
			$.post(AJAXPAGE, {ajaxPageName : 'prodItemConfirm', itemId : itemId}, function(data)	{
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
	$('#itemToSell').on({
		'change'	: function()	{
			var itemId = $(this).val();
			if(itemId == '0')	{
				return;
			}
			$.post(AJAXPAGE, {ajaxPageName : 'itemToSellQty', itemId : itemId}, function(data)	{
				//This is used to add the available quantities that can be sold.
				if(data == '0')	{
					alert('You cannot sell that Item. You dont have any.');
				} else {
					$('#itemToSellQty').find('option').remove().end().append(data);
					$('#itemToSellQty option:first').attr('selected', 'selected');
				}
			});
		}
	});
	$('#itemToSellAuction').live('click', function()	{
		var itemId = $('#itemToSell').val();
		var itemQty = $('#itemToSellQty').val();
		var startBid = $('#itemToSellStartBid').text();
		var buyout = $('#itemToSellBuyout').text();
		if(itemId == 0)	{
			alert('Select an item to sell.');
			return;
		}
		if(parseInt(startBid, 10) < 0)	{
			alert('Starting Bid should be Positive.');
		}
		if(parseInt(buyout, 10) < 0)	{
			alert('Buyout Amount should be positive.');
		}
		$.post(AJAXPAGE, {ajaxPageName : 'itemToSellAuction', itemId : itemId, itemQty : itemQty, startBid : startBid, buyout : buyout}, function(data)	{
			if(data == '0')	{
				alert('Item succesfully Posted.');
				window.location = 'sellItem.php';
			} else if (data == '1')	{
				alert('You dont have enough Quantity to sell. Try changing the Quantity.');
			} else {
				alert('An error occoured. Try again.');
				window.location = 'sellItem.php';
			}
		});
	});
});