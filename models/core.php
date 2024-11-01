<?php

	class shop86 {

		var $version = '0.0.1';

		var $cart = '';
		var $orders = '';

		public function install () {

		}


		public function uninstall() {

		}


		public function register_custom_posts() {


			// Register custom product post type
			$supports = array( 'title', 'revisions', 'thumbnail' );
			$args = array (
				'labels' => array('name' => __('Products', 'shop86'),
					'singular_name' => __('Product', 'shop86'),
					'add_new' => __('Add new', 'shop86'),
					'add_new_item' => __('Add new product', 'shop86'),
					'edit_item' => __('Edit Products', 'shop86'),
					'edit' => __('Edit', 'shop86'),
					'new_item' => __('New Product', 'shop86'),
					'view_item' => __('View Product', 'shop86'),
					'search_items' => __('Search Products', 'shop86'),
					'not_found' => __('No Products Found', 'shop86'),
					'not_found_in_trash' => __('No Products found in Trash', 'shop86'),
					'view' => __('View Product', 'shop86')
				),
				'description' => __('Wordpress simple shop86 store.', 'shop86'),
				'menu_icon' => SHOP86URL . '/admin/images/shop86_logo_small_sprite.png',
				'public' => true,
				'show_ui' => true,
				'publicly_queryable' => false,
				'capability_type' => 'page',
				'hierarchical' => false,
				'rewrite' => false,
				'query_var' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => false,
				'supports' => $supports
			);
			register_post_type( 'shop86products',$args);

			unset ($args);

			$args =  array(
				'labels' => array('name' => __('Orders', 'shop86'),
				'singular_name' => __('Order', 'shop86'),
				'edit' => __('Edit', 'shop86'),
				'view_item' => __('View Order', 'shop86'),
				'search_items' => __('Search Orders', 'shop86'),
				'not_found' => __('No Orders Found', 'shop86')
				),
				'description' => __('Orders from your Shop86 store.', 'shop86'),
				'public' => false,
				'show_ui' => false,
				'capability_type' => apply_filters( 'shop86orders_capability', 'page' ),
				'hierarchical' => false,
				'rewrite' => false,
				'query_var' => false,
				'supports' => array()
				);

			//register the orders post type
			register_post_type( 'shop86orders', $args );

				//register custom post statuses for our orders
			register_post_status( 'order_received', array(
			'label'       => __('Received', 'shop86'),
			'label_count' => array( __('Received <span class="count">(%s)</span>', 'shop86'), __('Received <span class="count">(%s)</span>', 'shop86') ),
			'post_type'   => 'shop86orders',
			'public'      => false
			) );
			register_post_status( 'order_paid', array(
			'label'       => __('Paid', 'shop86'),
			'label_count' => array( __('Paid <span class="count">(%s)</span>', 'shop86'), __('Paid <span class="count">(%s)</span>', 'shop86') ),
			'post_type'   => 'shop86orders',
			'public'      => false
			) );
			register_post_status( 'order_shipped', array(
			'label'       => __('Shipped', 'shop86'),
			'label_count' => array( __('Shipped <span class="count">(%s)</span>', 'shop86'), __('Shipped <span class="count">(%s)</span>', 'shop86') ),
			'post_type'   => 'shop86orders',
			'public'      => false
			) );
			register_post_status( 'order_closed', array(
			'label'       => __('Closed', 'shop86'),
			'label_count' => array( __('Closed <span class="count">(%s)</span>', 'shop86'), __('Closed <span class="count">(%s)</span>', 'shop86') ),
			'post_type'   => 'shop86orders',
			'public'      => false
			) );
	}

	public function group_menu_items() {

		//only process the manage orders page for editors and above and if orders hasn't been disabled
		if (current_user_can('edit_others_posts')) {
			$num_posts = wp_count_posts('shop86orders'); //get pending order count
			$count = $num_posts->order_received + $num_posts->order_paid;
			if ( $count > 0 )
				$count_output = '&nbsp;<span class="update-plugins"><span class="updates-count count-' . $count . '">' . $count . '</span></span>';
			else
				$count_output = '';
			$orders_page = add_submenu_page('edit.php?post_type=shop86products', __('Orders', 'shop86'), __('Orders', 'shop86') . $count_output, 'edit_others_posts', 'shop86orders', array(&$this, 'orders_page'));
		}

		$page = add_submenu_page('edit.php?post_type=shop86products', __('Store Settings', 'shop86'), __('Store Settings', 'shop86'), 'manage_options', 'shop86settings', array(&$this, 'admin_page'));
		// add_contextual_help($page, '<iframe src="http://premium.wpmudev.org/wdp-un.php?action=help&id=144" width="100%" height="600px"></iframe>');


	}

	//adds our custom column headers to edit products screen
	public function add_shop86_product_columns($old_columns)	{
		global $post_status;

		$columns['cb'] = '<input type="checkbox" />';
		$columns['thumbnail86'] = __('Thumbnail', 'shop86');
		$columns['title'] = __('Product Name', 'shop86');
		$columns['page86'] = __('Product Page', 'shop86');
		$columns['pricing86'] = __('Price', 'shop86');
		$columns['sales86'] = __('Sales', 'shop86');

		return $columns;
	}


	public function customize_shop86_product_columns($column) {
		global $post;
		$meta = get_post_meta($post->ID,'_shop86product_meta',TRUE);
		//unserialize
		switch ($column) {
			case "thumbnail86":
				if (has_post_thumbnail()) {
					echo '<a href="' . get_edit_post_link() . '" title="' . __('Edit &raquo;') . '">';
					the_post_thumbnail(array(50,50), array('title' => ''));
					echo '</a>';
				}
				break;

			case "page86":
				if(isset($meta['page'])) {
					print '<a href="'.get_page_link($meta['page']).'">'.get_the_title($meta['page']).'</a>';

				} else {

				}
				break;

			case "pricing86":
				if(isset($meta['price'])) {
					print $meta['price'];
				}
				break;

			case "sales86":
//				print number_format_i18n(($meta["shop86_product_sales"][0]) ? $meta["shop86_product_sales"][0] : 0);
				break;
		}
	}

		//adds our custom column headers to edit products screen
		public function add_shop86_order_columns($old_columns)	{
			global $post_status;

			$columns['cb'] = '<input type="checkbox" />';
			$columns['order_id'] = __('Order ID', 'shop86');
			$columns['order_type'] = __('Type','shop86');
			$columns['order_from'] = __('From', 'shop86');
			$columns['order_items'] = __('Items', 'shop86');
			$columns['order_date'] = __('Order Date', 'shop86');
			$columns['order_status'] = __('Status', 'shop86');

			return $columns;
		}

		//adds our custom column headers to edit products screen
		public function customize_shop86_order_columns($column)  {

			global $post;


			//unserialize

			switch ($column) {
				case "order_id":
					?>
					<strong><a class="row-title" <?php // href="edit.php?post_type=shop86products&page=shop86orders&order_id=<?php echo $post->ID;// ?>"><?php print 'GOS-'. $post->ID; ?></a></strong>
					<?php
					$actions = array();
					if ($post->post_status == 'order_received') {
						$actions['paid'] = "<a title='" . esc_attr(__('Mark as Paid', 'shop86')) . "' href='" . wp_nonce_url( admin_url( 'edit.php?post_type=product&amp;page=marketpress-orders&amp;action=paid&amp;post=' . $post->ID), 'update-order-status' ) . "'>" . __('Paid', 'shop86') . "</a>";
						$actions['shipped'] = "<a title='" . esc_attr(__('Mark as Shipped', 'shop86')) . "' href='" . wp_nonce_url( admin_url( 'edit.php?post_type=product&amp;page=marketpress-orders&amp;action=shipped&amp;post=' . $post->ID), 'update-order-status' ) . "'>" . __('Shipped', 'shop86') . "</a>";
						$actions['closed'] = "<a title='" . esc_attr(__('Mark as Closed', 'shop86')) . "' href='" . wp_nonce_url( admin_url( 'edit.php?post_type=product&amp;page=marketpress-orders&amp;action=closed&amp;post=' . $post->ID), 'update-order-status' ) . "'>" . __('Closed', 'shop86') . "</a>";
					} else if ($post->post_status == 'order_paid') {
						$actions['shipped'] = "<a title='" . esc_attr(__('Mark as Shipped', 'shop86')) . "' href='" . wp_nonce_url( admin_url( 'edit.php?post_type=product&amp;page=marketpress-orders&amp;action=shipped&amp;post=' . $post->ID), 'update-order-status' ) . "'>" . __('Shipped', 'shop86') . "</a>";
						$actions['closed'] = "<a title='" . esc_attr(__('Mark as Closed', 'shop86')) . "' href='" . wp_nonce_url( admin_url( 'edit.php?post_type=product&amp;page=marketpress-orders&amp;action=closed&amp;post=' . $post->ID), 'update-order-status' ) . "'>" . __('Closed', 'shop86') . "</a>";
					} else if ($post->post_status == 'order_shipped') {
						$actions['closed'] = "<a title='" . esc_attr(__('Mark as Closed', 'shop86')) . "' href='" . wp_nonce_url( admin_url( 'edit.php?post_type=product&amp;page=marketpress-orders&amp;action=closed&amp;post=' . $post->ID), 'update-order-status' ) . "'>" . __('Closed', 'shop86') . "</a>";
					} else if ($post->post_status == 'order_closed') {
						$actions['received'] = "<a title='" . esc_attr(__('Mark as Received', 'shop86')) . "' href='" . wp_nonce_url( admin_url( 'edit.php?post_type=product&amp;page=marketpress-orders&amp;action=received&amp;post=' . $post->ID), 'update-order-status' ) . "'>" . __('Received', 'shop86') . "</a>";
						$actions['paid'] = "<a title='" . esc_attr(__('Mark as Paid', 'shop86')) . "' href='" . wp_nonce_url( admin_url( 'edit.php?post_type=product&amp;page=marketpress-orders&amp;action=paid&amp;post=' . $post->ID), 'update-order-status' ) . "'>" . __('Paid', 'shop86') . "</a>";
						$actions['shipped'] = "<a title='" . esc_attr(__('Mark as Shipped', 'shop86')) . "' href='" . wp_nonce_url( admin_url( 'edit.php?post_type=product&amp;page=marketpress-orders&amp;action=shipped&amp;post=' . $post->ID), 'update-order-status' ) . "'>" . __('Shipped', 'shop86') . "</a>";
					}

					$action_count = count($actions);
					$i = 0;
					echo '<div class="row-actions">';
					foreach ( $actions as $action => $link ) {
						++$i;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo "<span class='$action'>$link$sep</span>";
					}
					echo '</div>';

					break;

				case "order_type":

					break;

				case "order_from":
					$cart = maybe_unserialize($post->post_content);
					if(!empty($cart->personData)) {
						parse_str($cart->personData, $vars);

						if (isset($vars['payment'])) print 'Способ оплаты –'.$vars['payment'].'<br />';
						if (isset($vars['delivery'])) print 'Доставка –'.$vars['delivery'].'<br />';
						if (isset($vars['title'])) print 'Имя –'.$vars['title'].'<br />';
						if (isset($vars['telephone'])) print 'Телефон –'.$vars['telephone'].'<br />';
						if (isset($vars['gorod'])) print 'Город –'.$vars['gorod'].'<br />';
						if (isset($vars['email'])) print 'E-mail –'.$vars['email'].'<br />';
						if (isset($vars['comments'])) print 'Комментарии –'.$vars['comments'].'<br />';
					}
					break;

				case "order_items":
					$cart = maybe_unserialize($post->post_content);
					if(isset($cart->items)) {
						print "<p>Было заказано</p>";
						$cart = maybe_unserialize($post->post_content);
						foreach($cart->items as $item){
							print '<a href="'. esc_url( get_permalink($item['product_id'])).'">'.get_the_title($item['product_id']).'</a> x '.$item['quantity'].'<br />';
						}
					}
					break;

				case "order_date":
					$t_time = get_the_time(__('Y/m/d g:i:s A'));
					$m_time = $post->post_date;
					$time = get_post_time('G', true, $post);

					$time_diff = time() - $time;

					if ( $time_diff > 0 && $time_diff < 24*60*60 )
						$h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
					else
						$h_time = mysql2date(__('Y/m/d'), $m_time);
					echo '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
					break;


				case "order_status":
					if ($post->post_status == 'order_received')
						$text = __('Received', 'shop86');
					else if ($post->post_status == 'order_paid')
						$text = __('Paid', 'shop86');
					else if ($post->post_status == 'order_shipped')
						$text = __('Shipped', 'shop86');
					else if ($post->post_status == 'order_closed')
						$text = __('Closed', 'shop86');

					?>
					<?php
					break;
			}
		}

	public function shop86_admin_styles () {
		wp_enqueue_style( 'shop86admin_style', SHOP86URL . '/admin/_css/shop86admin.css');
	}

	/* Adds a box to the main column on the Post edit screens */
	public function add_main_metabox() {

		add_meta_box(
			'shop86products_main_metabox',
			__( 'Main product details', 'shop86' ),
			array($this,'myplugin_inner_custom_box'),
			'shop86products',
			'normal',
			'high'
		);

	}

	/* Prints the box content */
	public function myplugin_inner_custom_box() {
		global $post;

		$meta = get_post_meta($post->ID,'_shop86product_meta',TRUE);

		?>
		<div class="shop86product_main_meta">
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras orci lorem, bibendum in pharetra ac, luctus ut mauris. Phasellus dapibus elit et justo malesuada eget <code>functions.php</code>.</p>

			<label for="shop86product_meta_price"><?php _e('Price','shop86'); ?></label>

			<p>
				<input type="number" id="shop86product_meta_price" name="shop86product_meta[price]" value="<?php if(!empty($meta['price'])) echo $meta['price']; ?>"/>
				<span><?php _e('Please enter <code>numbers</code> only','shop86'); ?></span>
			</p>

			<label for="shop86product_meta_desc">Description <span>(optional)</span></label>

			<p>
				<textarea id="shop86product_meta_desc" name="shop86product_meta[desc]" rows="3"><?php if(!empty($meta['desc'])) echo $meta['desc']; ?></textarea>
				<span>Enter in a description</span>
			</p>

			<label for="shop86product_meta_page"><?php _e('Product page link','shop86'); ?></label>
			<p>


			<?php
				$dropdown_args = array(
					'name'             => 'shop86product_meta[page]' ,
					'post_type'        => 'page',
					'sort_column'      => 'menu_order, post_title',
					'echo'             => 1,
					'selected'         => isset($meta['page']) ? $meta['page'] : ''
				);

				// Use nonce for verification
				wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename');

				//Dropdown of pages
				wp_dropdown_pages( $dropdown_args );
			?>
			</p>

			<?php
				// Make some security
				print '<input type="hidden" name="shop86product_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
			?>

		</div>
		<?php
	}

		public function save_main_metabox ($post_id) {

			//skip quick edit
			if ( defined('DOING_AJAX') )
				return;

			// authentication checks
			// make sure data came from our meta box
			if (!wp_verify_nonce($_POST['shop86product_meta_noncename'],__FILE__)) return $post_id;

			// check user permissions
			if ($_POST['post_type'] == 'shop86products')
			{
				if (!current_user_can('edit_page', $post_id)) return $post_id;
			}
			else
			{
				if (!current_user_can('edit_post', $post_id)) return $post_id;
			}

			// authentication passed, save data

			// var types
			// single: _my_meta[var]
			// array: _my_meta[var][]
			// grouped array: _my_meta[var_group][0][var_1], _my_meta[var_group][0][var_2]

			$current_data = get_post_meta($post_id, '_shop86product_meta', TRUE);

			$new_data = $_POST['shop86product_meta'];

			$this->my_meta_clean($new_data);

			if ($current_data)
			{
				if (is_null($new_data)) delete_post_meta($post_id,'_shop86product_meta');
				else update_post_meta($post_id,'_shop86product_meta',$new_data);
			}
			elseif (!is_null($new_data))
			{
				add_post_meta($post_id,'_shop86product_meta',$new_data,TRUE);
			}

			return $post_id;
		}


		public function my_meta_clean(&$arr)
		{
			if (is_array($arr))
			{
				foreach ($arr as $i => $v)
				{
					if (is_array($arr[$i]))
					{
						my_meta_clean($arr[$i]);

						if (!count($arr[$i]))
						{
							unset($arr[$i]);
						}
					}
					else
					{
						if (trim($arr[$i]) == '')
						{
							unset($arr[$i]);
						}
					}
				}

				if (!count($arr))
				{
					$arr = NULL;
				}
			}
		}

	public function add_cart_html(){
//		print_r($_POST);
//		'<div id="shop86cart">
//					<div class="inner">
//						<h4 class="shop86_title">'.__('Shop orders','shop86').'</h4>
//							<div class="shop86_items">
//								<div class="shop86_item">
//									<div class="shop86_item_img">
//										<img />
//									</div>
//									<div class="shop86_item_desc">
//										fsdfsdfssfd
//									</div>
//									<span class="shop86_item_remove_btn">x</span>
//								</div>
//							</div>
//							<div class="shop86_total">
//
//							</div>
//					</div>
//				</div>';
	}


	public function add_shop86_ajax_requests() {

		//setup shopping cart javascript
		wp_enqueue_script( 'shop86', SHOP86PATH . 'js/shop86.js', array('jquery'), $this->version );

		// declare the variables we need to access in js
		wp_localize_script( 'shop86', 'Shop86Ajax', array(
																	'ajaxUrl' => admin_url( 'admin-ajax.php' ),
																	'emptyCartMsg' => __('Are you sure you want to remove all items from your cart?', 'shop86'),
																	'successMsg' => __('Item(s) Added!', 'shop86'), 'imgUrl' => $this->plugin_url.'images/loading.gif',
																	'addingMsg' => __('Adding to your cart...', 'shop86'),
																	'outMsg' => __('In Your Cart', 'shop86') ) );
	}


	public function add_shop86_cart_button($attr) {
		$button = '<a href="#"
							class="btn btn-danger add_to_cart86"
							data-id="'.$attr['id'].'">
								<i class="icon-shopping-cart icon-white"></i>
								'.__('Buy this','shop86').'
						</a>';
		return $button;
	}

	public function update_shop86_cart() {

		//TODO remove from update_shop86_cart();

		if(session_id() == ''){
			session_start();
			$this->cart = &new shop86Cart('shoppingCart86');
		}

		switch ($_POST['shop86_action']) {

			case 'add' :
				if (!empty($_POST['item']['product_id']) && !empty($_POST['item']['quantity']) ) {

					// Add +1 to clicked item
					$quantity = $this->cart->getItemQuantity($_POST['item']['product_id']) + $_POST['item']['quantity'];
					$this->cart->setItemQuantity($_POST['item']['product_id'], $quantity);

				}
				break;

			case 'update' :
				if (!empty($_POST['item']['product_id']) && !empty($_POST['item']['quantity']) ) {
					$this->cart->setItemQuantity($_POST['item']['product_id'], $_POST['item']['quantity']);
				}
				break;

			case 'remove' :
				$this->cart->setItemQuantity($_POST['item']['product_id'], 0);
				break;

			default:
				break;
		}

		$this->cart->save();

		?>

		<div id="shop86cart">

			<?php //TODO remove this first shit ?>
			<div class="inner shop86_first">
				<h4 class="shop86_title"><?php print __('Shop orders','shop86'); ?></h4>
				<div class="shop86_items">
					<?php

					if ($this->cart->hasItems()) :
						$total_price = $i = 0;
						foreach ( $this->cart->getItems() as $item) :
							// $total_price += $product_id * $this->cart->getItemPrice($product_id);

							$args = array(

								//TODO add security enchance

								'p' => $item['product_id'], // id of a page, post, or custom type
								'post_type' => 'shop86products');
							$productQuery = new WP_Query($args);

							if ($productQuery->have_posts()) : while ($productQuery->have_posts()) : $productQuery->the_post();
								$meta = get_post_meta(get_the_ID(),'_shop86product_meta',TRUE);
								$this->my_meta_clean($meta);
								?>
								<div class="shop86_item" data-id="<?php print get_the_ID(); ?>">
									<div class="shop86_item_img">
										<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('thumbnail');
										}
										?>
									</div>
									<div class="shop86_item_desc">
										<div class="title">
											<a href="<?php print get_page_link($meta['page']); ?>" class="text"><?php the_title(); ?></a>
										</div>
										<div class="desc">
											<p class="text"><?php print $meta['desc']; ?></p>
										</div>
										<div class="price">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td width="35%" align="left">
														 <span class="text">Цена</span>
													</td>
													<td width="30%">
														<span class="text">Количество</span>
													</td>
													<td width="35%" align="left">
														<span class="text">К оплате</span>
													</td>
												</tr>
												<tr>
													<td>
														<span class="itemPrice"><?php print $meta['price'] ?></span>
														<span class="shop86_price_prefix"> руб.</span>
													</td>
													<td>
														<input class="itemAmount" size="4" type="text" width="50" style="width:50px;" value="<?php print $item['quantity']; ?>">
													</td>
													<td>
														<span class="itemPriceTotal"><?php print $meta['price'] ?></span>
														<span class="shop86_price_prefix"> руб.</span>
													</td>
												</tr>
											</table>
										</div>
									</div>
									<a class="shop86_item_remove_btn">
										<button type="button" class="close">&times;</button>
									</a>
								</div>
								<div style="clear:both;"></div>
							<?php
							endwhile;
						endif;
					endforeach;
					else:

						print '<p>no items</p>';

					endif;
					?>
				</div>
				<div class="shop86_total">
					<div class="shop86_totalContactInfo"></div>
					<div class="shop86_totalPriceInfo">
						<div class="name">
							<span class="text">Итого: </span>
						</div>
						<div class="price">
							<span class="shop86_totalSum">__</span>
							<span class="shop86_price_prefix"> руб.</span>
						</div>
						<div style="clear:both;"></div>
					</div>
					<div class="shop86_control_btns">
						<a href="<?php print admin_url( 'admin-ajax.php' ).'?action=shop86_cart_showForm&width=800&height=600'; ?>" class="btn btn-success thickbox">Купить</a>
						<a href="#" class="shop86_continue_shopping" onclick="self.parent.tb_remove();">Продолжить покупки</a>
					</div>
				</div>
			</div>
		</div>
	<?php
		die();
	}

	public function shop86_cart_showForm () {

		if(session_id() == ''){
			session_start();
			$this->cart = &new shop86Cart('shoppingCart86');
		}


		?>
		<div class="shop86_container">
			<div class="shop86_formColumn">
				<div class="inner">
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane fade active in" id="home">
							<form class="form-horizontal shop86_form">
								<legend>Оформление заказа</legend>
								<fieldset>
									<div class="control-group">
										<label class="control-label" for="payment">Оплата:</label>
										<div class="controls">
											<select id="payment"  name="payment">
												<option>Наличными</option>
												<option>Безналичный расчет</option>
												<option>WebMoney</option>
												<option>Yandex деньги</option>
											</select>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="delivery">Способ доставки:</label>
										<div class="controls">
											<select id="delivery"  name="delivery">
												<option>Самовывоз</option>
												<option>Доставка курьером</option>
												<option>Транспортная компания</option>
											</select>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="name">ФИО:</label>
										<div class="controls">
											<input type="text" class="input" id="name"  name="title">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="telephone">Телефон:</label>
										<div class="controls">
											<input type="text" class="input" id="telephone" name="telephone">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="gorod">Город:</label>
										<div class="controls">
											<input type="text" class="input" id="gorod" name="gorod">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="email">Email:</label>
										<div class="controls">
											<input type="text" class="input" id="email" name="email">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="comments">Комментарии к заказу:</label>
										<div class="controls">
											<textarea class="input" id="comments" rows="3" name="comments"></textarea>
										</div>
									</div>
									<div class="form-actions">
										<button type="submit" class="btn btn-success btn-large shop86_submit">Заказать</button>
									</div>
								</fieldset>
							</form>
						</div>
						<?php /*
                  <div class="tab-pane fade" id="profile">
							<form class="form-horizontal" data-id="2">
								<fieldset>
									<div class="control-group">
										<label class="control-label" for="oooname">Название организации:</label>
										<div class="controls">
											<input type="text" class="input" id="oooname">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="oooaddress">Юр. адрес:</label>
										<div class="controls">
											<input type="text" class="input" id="oooaddress">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="inn">ИНН:</label>
										<div class="controls">
											<input type="text" class="input" id="inn">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="kpp">КПП:</label>
										<div class="controls">
											<input type="text" class="input-small" id="kpp">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="bik">Расчетный счет:</label>
										<div class="controls">
											<input type="text" class="input" id="bik">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="bank">Банк:</label>
										<div class="controls">
											<input type="text" class="input" id="bank">
										</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="corschet">к/счет:</label>
										<div class="controls">
											<input type="text" class="input" id="corschet">
										</div>
									</div>
								</fieldset>
							</form>
						</div>
                  */ ?>
					</div>
				</div>
			</div>
			<div class="shop86_orderColumn">
				<div class="inner">
						<div class="shop86_items">
						<?php

						if ($this->cart->hasItems()) :
							$total_price = $i = 0;
							foreach ( $this->cart->getItems() as $item) :
								// $total_price += $product_id * $this->cart->getItemPrice($product_id);

								$args = array(

									//TODO add security enchance

									'p' => $item['product_id'], // id of a page, post, or custom type
									'post_type' => 'shop86products');
								$productQuery = new WP_Query($args);

								if ($productQuery->have_posts()) : while ($productQuery->have_posts()) : $productQuery->the_post();
									$meta = get_post_meta(get_the_ID(),'_shop86product_meta',TRUE);
									$this->my_meta_clean($meta);
									?>
									<div class="shop86_item" data-id="<?php print get_the_ID(); ?>">
										<div class="shop86_item_img">
											<?php
											if ( has_post_thumbnail() ) {
												the_post_thumbnail( 'thumbnail');
											}
											?>
										</div>
										<div class="shop86_item_desc">
											<div class="title">
												<a href="<?php print get_page_link($meta['page']); ?>" class="text"><?php the_title(); ?></a>
											</div>
											<div class="price">
												<span class="itemPrice price"><?php print $meta['price'] ?></span><span> руб</span><span> x </span><span class="itemAmount amount"><?php print $item['quantity']; ?></span><span> комплектов</span>
											</div>
										</div>
										<?php /*
										<a class="shop86_item_remove_btn">
											<button type="button" class="close">&times;</button>
										</a> */
										?>
										<div style="clear:both;"></div>
									</div>
								<?php
								endwhile;
								endif;
							endforeach;
						else:

							print '<p>no items</p>';

						endif;
						?>
					</div>
					<div class="shop86_total">
						<div class="shop86_totalPriceInfo">
							<div class="name">
								<span class="text">Итого: </span>
							</div>
							<div class="price">
								<span class="totalSum">4801801797</span>
								<span class="shop86_price_prefix"> руб.</span>
							</div>
							<div style="clear:both;"></div>
						</div>
					</div>
					<div class="shop86_shopinfo">
						<div class="inner">
							<div class="name">
								<span class="text">Наш сайт</span>
							</div>
							<div class="info">
								<span class="text"><?php print '<a href="'.get_bloginfo('url').'" title='.get_bloginfo('name').'>'.get_bloginfo('name').'</a>'; ?></span>
							</div>
							<div style="clear:both;"></div>
						</div>
						<div class="inner">
							<div class="name">
								<span class="text">Наш телефон</span>
							</div>
							<div class="info">
								<span class="text">+7 (495) 725-87-39</span>
							</div>
							<div style="clear:both;"></div>
						</div>
						<div class="inner">
							<div class="name">
								<span class="text">Наш телефон</span>
							</div>
							<div class="info">
								<span class="text">+7 (495) 725-87-36</span>
							</div>
							<div style="clear:both;"></div>
						</div>
					</div>
					<script type="text/javascript" >
						calcPrice();
					</script>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>

		<?php

		die();
	}

	public function shop86_cart_addOrder() {

		//TODO remove from update_shop86_cart();

		if(session_id() == ''){
			session_start();
			$this->cart = &new shop86Cart('shoppingCart86');
		}

		$this->orders = &new shop86Orders();
		$formData = isset($_REQUEST['form']) ? $_REQUEST['form'] : '';
		$order = $this->orders->newOrder($this->cart, $formData);

		print "<h1 style='text-align:center;'> Ваш заказ № GOS-".$order."</h1><script type='text/javascript'>$(function(){ $('.#TB_Ajaxcontent').height(100); });</script>";

		$this->cart->clean();

		die();
	}

	public function orders_page () {

//		//load single order view if id is set
//		if (isset($_GET['order_id'])) {
//			$this->single_order_page();
//			return;
//		}

		//force post type
		global $wpdb, $post_type, $wp_query, $wp_locale, $current_screen;
		$post_type = 'shop86orders';
		$_GET['post_type'] = $post_type;

		$post_type_object = get_post_type_object($post_type);

		if ( !current_user_can($post_type_object->cap->edit_posts) ) wp_die('-1');

		$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
		if ( empty($pagenum) )
			$pagenum = 1;
		$per_page = 'edit_' . $post_type . '_per_page';
		$per_page = (int) get_user_option( $per_page );
		if ( empty( $per_page ) || $per_page < 1 )
			$per_page = 15;
		// @todo filter based on type
		$per_page = apply_filters( 'edit_posts_per_page', $per_page );

		// Handle bulk actions
		if ( isset($_GET['doaction']) || isset($_GET['doaction2']) || isset($_GET['bulk_edit']) || isset($_GET['action']) ) {
			check_admin_referer('update-order-status');
			$sendback = remove_query_arg( array('received', 'paid', 'shipped', 'closed', 'ids'), wp_get_referer() );

			if ( ( $_GET['action'] != -1 || $_GET['action2'] != -1 ) && ( isset($_GET['post']) || isset($_GET['ids']) ) ) {
				$post_ids = isset($_GET['post']) ? array_map( 'intval', (array) $_GET['post'] ) : explode(',', $_GET['ids']);
				$doaction = ($_GET['action'] != -1) ? $_GET['action'] : $_GET['action2'];
			}

			switch ( $doaction ) {
				case 'received':
					$received = 0;
					foreach( (array) $post_ids as $post_id ) {
						$this->update_order_status($post_id, 'received');
						$received++;
					}
					$msg = sprintf( _n( '%s order marked as Received.', '%s orders marked as Received.', $received, 'shop86' ), number_format_i18n( $received ) );
					break;
				case 'paid':
					$paid = 0;
					foreach( (array) $post_ids as $post_id ) {
						$this->update_order_status($post_id, 'paid');
						$paid++;
					}
					$msg = sprintf( _n( '%s order marked as Paid.', '%s orders marked as Paid.', $paid, 'shop86' ), number_format_i18n( $paid ) );
					break;
				case 'shipped':
					$shipped = 0;
					foreach( (array) $post_ids as $post_id ) {
						$this->update_order_status($post_id, 'shipped');
						$shipped++;
					}
					$msg = sprintf( _n( '%s order marked as Shipped.', '%s orders marked as Shipped.', $shipped, 'shop86' ), number_format_i18n( $shipped ) );
					break;
				case 'closed':
					$closed = 0;
					foreach( (array) $post_ids as $post_id ) {
						$this->update_order_status($post_id, 'closed');
						$closed++;
					}
					$msg = sprintf( _n( '%s order Closed.', '%s orders Closed.', $closed, 'shop86' ), number_format_i18n( $closed ) );
					break;

			}

		}

		$avail_post_stati = wp_edit_posts_query();

		$num_pages = $wp_query->max_num_pages;

		$mode = 'list';
		?>

		<div class="wrap">
		<div class="icon32"><img src="<?php echo SHOP86URL . '/admin/_tmp/shopping-cart.png'; ?>" /></div>
		<h2><?php _e('Manage Orders', 'shop86');
			if ( isset($_GET['s']) && $_GET['s'] )
				printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', get_search_query() ); ?>
		</h2>

		<?php if ( isset($msg) ) { ?>
			<div class="updated fade"><p>
					<?php echo $msg; ?>
				</p></div>
		<?php } ?>

		<form id="posts-filter" action="<?php echo admin_url('edit.php'); ?>" method="get">

			<ul class="subsubsub">
				<?php
				if ( empty($locked_post_status) ) :
					$status_links = array();
					$num_posts = wp_count_posts( $post_type, 'readable' );
					$class = '';
					$allposts = '';

					$total_posts = array_sum( (array) $num_posts );

					// Subtract post types that are not included in the admin all list.
					foreach ( get_post_stati( array('show_in_admin_all_list' => false) ) as $state )
						$total_posts -= $num_posts->$state;

					$class = empty($class) && empty($_GET['post_status']) ? ' class="current"' : '';
					$status_links[] = "<li><a href='edit.php?page=shop86orders&post_type=shop86products{$allposts}'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

					foreach ( get_post_stati(array('post_type' => 'shop86orders'), 'objects') as $status ) {
						$class = '';

						$status_name = $status->name;

						if ( !in_array( $status_name, $avail_post_stati ) )
							continue;

						if ( empty( $num_posts->$status_name ) )
							continue;

						if ( isset($_GET['post_status']) && $status_name == $_GET['post_status'] )
							$class = ' class="current"';

						$status_links[] = "<li><a href='edit.php?page=shop86orders&amp;post_status=$status_name&amp;post_type=shop86products'$class>" . sprintf( _n( $status->label_count[0], $status->label_count[1], $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
					}
					echo implode( " |</li>\n", $status_links ) . '</li>';
					unset( $status_links );
				endif;
				?>
			</ul>

			<p class="search-box">
				<label class="screen-reader-text" for="post-search-input"><?php _e('Search Orders', 'shop86'); ?>:</label>
				<input type="text" id="post-search-input" name="s" value="<?php the_search_query(); ?>" />
				<input type="submit" value="<?php _e('Search Orders', 'shop86'); ?>" class="button" />
			</p>

			<input type="hidden" name="post_type" class="post_status_page" value="product" />
			<input type="hidden" name="page" class="post_status_page" value="marketpress-orders" />
			<?php if (!empty($_GET['post_status'])) { ?>
				<input type="hidden" name="post_status" class="post_status_page" value="<?php echo esc_attr($_GET['post_status']); ?>" />
			<?php } ?>

			<?php if ( have_posts() ) { ?>

				<div class="tablenav">
					<?php
					$page_links = paginate_links( array(
						'base' => add_query_arg( 'paged', '%#%' ),
						'format' => '',
						'prev_text' => __('&laquo;'),
						'next_text' => __('&raquo;'),
						'total' => $num_pages,
						'current' => $pagenum
					));

					?>

					<div class="alignleft actions">
						<select name="action">
							<option value="-1" selected="selected"><?php _e('Change Status', 'shop86'); ?></option>
							<option value="received"><?php _e('Received', 'shop86'); ?></option>
							<option value="paid"><?php _e('Paid', 'shop86'); ?></option>
							<option value="shipped"><?php _e('Shipped', 'shop86'); ?></option>
							<option value="closed"><?php _e('Closed', 'shop86'); ?></option>
						</select>
						<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
						<?php wp_nonce_field('update-order-status'); ?>

						<?php // view filters
						if ( !is_singular() ) {
							$arc_query = $wpdb->prepare("SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = %s ORDER BY post_date DESC", $post_type);

							$arc_result = $wpdb->get_results( $arc_query );

							$month_count = count($arc_result);

							if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) {
								$m = isset($_GET['m']) ? (int)$_GET['m'] : 0;
								?>
								<select name='m'>
									<option<?php selected( $m, 0 ); ?> value='0'><?php _e('Show all dates'); ?></option>
									<?php
									foreach ($arc_result as $arc_row) {
										if ( $arc_row->yyear == 0 )
											continue;
										$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

										if ( $arc_row->yyear . $arc_row->mmonth == $m )
											$default = ' selected="selected"';
										else
											$default = '';

										echo "<option$default value='" . esc_attr("$arc_row->yyear$arc_row->mmonth") . "'>";
										echo $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear";
										echo "</option>\n";
									}
									?>
								</select>
							<?php } ?>

							<input type="submit" id="post-query-submit" value="<?php esc_attr_e('Filter'); ?>" class="button-secondary" />
						<?php } ?>
					</div>

					<?php if ( $page_links ) { ?>
						<div class="tablenav-pages"><?php
							$count_posts = $post_type_object->hierarchical ? $wp_query->post_count : $wp_query->found_posts;
							$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
								number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
								number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
								number_format_i18n( $count_posts ),
								$page_links
							);
							echo $page_links_text;
							?></div>
					<?php } ?>

					<div class="clear"></div>
				</div>

				<div class="clear"></div>

				<table class="widefat <?php echo $post_type_object->hierarchical ? 'page' : 'post'; ?> fixed" cellspacing="0">
					<thead>
					<tr>
						<?php print_column_headers( $current_screen ); ?>
					</tr>
					</thead>

					<tfoot>
					<tr>
						<?php print_column_headers($current_screen, false); ?>
					</tr>
					</tfoot>

					<tbody>
					<?php
					if ( function_exists('post_rows') ) {
						post_rows();
					} else {
						$wp_list_table = _get_list_table('WP_Posts_List_Table');
						$wp_list_table->display_rows();
					}
					?>
					</tbody>
				</table>

				<div class="tablenav">

					<?php
					if ( $page_links )
						echo "<div class='tablenav-pages'>$page_links_text</div>";
					?>

					<div class="alignleft actions">
						<select name="action2">
							<option value="-1" selected="selected"><?php _e('Change Status', 'shop86'); ?></option>
							<option value="received"><?php _e('Received', 'shop86'); ?></option>
							<option value="paid"><?php _e('Paid', 'shop86'); ?></option>
							<option value="shipped"><?php _e('Shipped', 'shop86'); ?></option>
							<option value="closed"><?php _e('Closed', 'shop86'); ?></option>
						</select>
						<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
						<br class="clear" />
					</div>
					<br class="clear" />
				</div>

			<?php } else { // have_posts() ?>
				<div class="clear"></div>
				<p><?php _e('No Orders Yet', 'shop86'); ?></p>
			<?php } ?>

		</form>

		<br class="clear">
		</div>
	<?php
	}

}
