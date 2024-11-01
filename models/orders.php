<?php

	Class shop86Orders {

		var $personData = '';
		var $cartData = '';


		public function __construct(){

		}

		public function newOrder ($cartData, $formData) {


			$nonce = $_POST['orderNonce'];
			// check to see if the submitted nonce matches with the
			// generated nonce we created earlier
			if (!wp_verify_nonce( $nonce, 'shop86_nonce')) die();

			$cartData->personData = $formData;

			//insert post type
			$order = array();
			$order['post_title'] = '';
			$order['post_content'] = serialize($cartData);
			$order['post_status'] = 'order_received';
			$order['post_type'] = 'shop86orders';
			$newOrder = wp_insert_post($order, true);

			return $newOrder;
		}


	}
?>
