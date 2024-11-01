<?php

	//TODO make refactoring of this shit Foreach
	class shop86Cart {
		var $cartName;       // The name of the cart/session variable
		var $items = array(); // The array for storing items in the cart
		var $personData = ''; // TODO remove this shit

		/**
		 * __construct() - Constructor. This assigns the name of the cart
		 *                 to an instance variable and loads the cart from
		 *                 session.
		 *
		 * @param string $name The name of the cart.
		 */
		 public function __construct($name) {
			$this->cartName = $name;
			$this->items = $_SESSION[$this->cartName];
		}

		/**
		 * setItemQuantity() - Set the quantity of an item.
		 *
		 * @param string $product_id The product id of the item.
		 * @param int $quantity The quantity.
		 */
		public function setItemQuantity($product_id, $quantity) {
			if (!empty($this->items)) {
				if(!$this->checkItemExist($product_id)){
					$this->items[] = array ('product_id' => $product_id, 'quantity' => $quantity);
				} else {
					foreach ($this->items as &$item) {
						if(isset($item['product_id']) && $item['product_id'] == $product_id){
							$item['quantity'] = $quantity;
						}
					}
				}
			} else {
				$this->items[] = array ('product_id' => $product_id, 'quantity' => $quantity);
			}
		}

		/**
		 * checkItemExist() - Check if user added item to basket
		 *
		 * @param string $product_id The product code of the item.
		 * @return bool True if exist.
		 */

		public function checkItemExist($product_id){
			foreach ($this->items as $item)
				if (isset($item['product_id']) && $item['product_id'] == $product_id)
					return true;
			return false;
		}

		/**
		 * getItemPrice() - Get the price of an item.
		 *
		 * @param string $order_code The order code of the item.
		 * @return int The price.
		 */
		function getItemPrice($product_id) {
			// This is where the code taht retrieves prices
			// goes. We'll just say everything costs $9.99 for this tutorial.
			return 9.99;
		}

		/**
		 * getItemName() - Get the name of an item.
		 *
		 * @param string $order_code The order code of the item.
		 */
		function getItemName($product_id) {
			// This is where the code that retrieves product names
			// goes. We'll just return something generic for this tutorial.
			return 'My Product (' . $product_id . ')';
		}

		/**
		 * getItems() - Get all items.
		 *
		 * @return array The items.
		 */
		function getItems() {
			return $this->items;
		}

		/**
		 * hasItems() - Checks to see if there are items in the cart.
		 *
		 * @return bool True if there are items.
		 */
		function hasItems() {
			return (bool) $this->items;
		}

		/**
		 * getItemQuantity() - Get the quantity of an item in the cart.
		 *
		 * @param string $order_code The order code.
		 * @return int The quantity.
		 */
		function getItemQuantity($product_id) {
			foreach ($this->items as $item){
				if($item['product_id'] == $product_id) return (int) $item['quantity'];
			}
		}

		/**
		 * clean() - Cleanup the cart contents. If any items have a
		 *           quantity less than one, remove them.
		 */
		function clean() {
			foreach ($this->items as $key => $item) {
				if ( $item['quantity'] < 1 ) {
					unset($this->items[$key]);
				}
			}
			unset($_SESSION[$this->cartName]);
		}

		/**
		 * save() - Saves the cart to a session variable.
		 */
		function save() {
			$this->clean();
			$_SESSION[$this->cartName] = $this->items;
		}
	}

?>