//TODO make some refactoring of JS in good way;;

$(function(){



	$(".add_to_cart86").click(function (e) {
		// TODO make modifaction of ThickBox in order to get ajax requests;;
		e.preventDefault();

		$('#shop86cart').remove();

		$.ajaxSetup({cache:false});
		$.ajax({
			url : Shop86Ajax.ajaxUrl,
			data : {
				action: 'update_shop86_cart',
				shop86_action: 'add',
				item:   { product_id : $(this).attr('data-id'),
						  quantity: '1'
						}
			},
			type: 'POST'
		})
		.success(function (data) {
			$('body').append(data);
			tb_show('Add to shopping cart','#TB_inline?inlineId=shop86cart&width=800&height=600','');
			calcPrice();
			$('#shop86cart').remove();

		})
		.fail(function (err){

		});
	});

	$('.shop86_item .itemAmount').live("change", function (e) {

		// TODO make modifaction of ThickBox in order to get ajax requests;;

		$.ajax({
			url : Shop86Ajax.ajaxUrl,
			data : {
				action: 'update_shop86_cart',
				shop86_action: 'update',
				item:   { product_id : $(this).closest('.shop86_item').attr('data-id'),
					quantity: $(this).val()
				}
			},
			type: 'POST'
		})
			.success(function (data) {
				calcPrice();
			})
			.fail(function (err){

			});

	});

	$('.shop86_item_remove_btn').live('click', function() {
		var itemContainerID = $(this).closest('.shop86_item').attr('data-id');

		$.ajax({
			url : Shop86Ajax.ajaxUrl,
			data : {
				action: 'update_shop86_cart',
				shop86_action: 'remove',
				item:   {
					product_id : itemContainerID
				}
			},
			type: 'POST'
		})
			.success(function (data) {
				$('.shop86_item[data-id='+ itemContainerID + ']').fadeOut(500, function() {
					$(this).remove();
					calcPrice();
				});
			})
			.fail(function (err){

			});
	});

	$('.shop86_submit').live('click', function (e) {
		e.preventDefault();
		$.ajax({
			url : Shop86Ajax.ajaxUrl,
			data : {
				action: 'shop86_cart_addOrder',
				form: $('.shop86_form').serialize(),
				orderNonce : Shop86Ajax.ajaxNonce
			},
			type: 'POST',
		})
			.success(function (data) {
				$("#TB_ajaxContent").html(data).height(75);
			})
			.fail(function (err){

			});

	});

});

function calcPrice() {
	var totalPrice = 0;
	$(".shop86_first .shop86_item").each(function() {
		var quantity = $(".itemAmount", this).val();
		var price = $(".itemPrice", this).text();
		var extendedPrice = price * quantity;
//		var extendedPrice = quantity * totalPrice;
		totalPrice += extendedPrice;

		$(".itemPriceTotal", this).html(extendedPrice);
		$(".shop86_totalSum").html(totalPrice);
	});


	// TODO remove this shame shit

	$('.shop86_orderColumn .shop86_item').each(function() {
		var quantity = $('span.amount',this).text();
		var price = $('span.price',this).text();
		var extendedPrice = price * quantity;
//		var extendedPrice = quantity * totalPrice;
		totalPrice += extendedPrice;

		$(".priceTotal", this).html(extendedPrice);
		$(".totalSum").html(totalPrice);
	});

	if ( totalPrice == 0 ) {
		$(".shop86_total").replaceWith("<p class='center'>Корзина пуста</p>");
	}
}