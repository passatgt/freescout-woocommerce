function loadWooCommerceOrders(customer_email, domain, locale) {
	$(document).ready(function(){

		//Check if already stored
		var email_address_id = customer_email.replace(/[^A-Za-z0-9\-]/, '');
		var order_data = getWithExpiry('wc_customer_'+email_address_id);
		var $order_list = $('.woocommerce-order-list');
		var $order_list_reload = $('.woocommerce-order-list-reload');
		var $not_found = '<li class="not-found">'+$order_list.data('not-found')+'</li>'
		var $orders_box = $('.woocommerce-orders');

		//If expired or doesn't exists, fetch new orders
		if(!order_data) {
			get_orders();
		} else {
			display_orders(order_data);
		}

		//Reload orders manually
		$order_list_reload.click(function(){
			$orders_box.addClass('loading');
			get_orders();
			return false;
		});

		function get_orders() {
			fsAjax({
				action: 'wc_check_orders',
				customer_email: customer_email,
			},
			laroute.route('woocommerce.ajax'),
			function(response) {

				//Cache for 1 hour
				setWithExpiry('wc_customer_'+email_address_id, response.data, 60*60);

				//Dispaly orders
				display_orders(response.data);

			},
			true);
		}

		function display_orders(order_data) {
			$order_list.html('');
			$orders_box.removeClass('loading');

			if(!Array.isArray(order_data) || (Array.isArray(order_data) && order_data.length < 1)) {
				$order_list.append($not_found);
				return;
			}

			order_data.forEach(function(order){
				var template = $('#wc-order-item-template').html();
				var total = new Intl.NumberFormat(locale, { style: 'currency', currency: order.currency }).format(order.total);
				var edit_link = domain+'wp-admin/post.php?post='+order.id+'&action=edit';
				var date = new Date(order.date_created);
				var datetime = Intl.DateTimeFormat(locale).format(date)

				var data = {
					'number': order.number,
					'total': total,
					'date': datetime,
					'status': order.status
				};

				Object.keys(data).forEach(function(key, index) {
					var value = this[key];
					template = template.replace('{'+key+'}', value);
				}, data);

				$order_list.append(template);
			});

		}

		function setWithExpiry(key, value, ttl) {
			const now = new Date()
			const item = {
				value: value,
				expiry: now.getTime() + ttl,
			}
			localStorage.setItem(key, JSON.stringify(item))
		}

		function getWithExpiry(key) {
			const itemStr = localStorage.getItem(key)
			// if the item doesn't exist, return null
			if (!itemStr) {
				return null
			}
			const item = JSON.parse(itemStr)
			const now = new Date()
			// compare the expiry time of the item with the current time
			if (now.getTime() > item.expiry) {
				// If the item is expired, delete the item from storage
				// and return null
				localStorage.removeItem(key)
				return null
			}
			return item.value
		}

	});
}