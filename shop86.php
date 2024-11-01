<?php
/*
Plugin Name: Shop86 - shopping cart
Plugin URI: http://www.nulllogic.net/shop/wordpress/shop86
Description: <strong>Simple</strong> shopping cart for Wordpress by <a href="http://www.nulllogic.net/">NullLogic syndicate</a>
Version: 0.0.1
Author: Vladimir Lukyanov
Author URI: http://www.nulllogic.net
Text Domain: shop86
*/
?>
<?php

if (!function_exists('add_action')) {
	die('Please don\'t open this file directly!');
}


if(!class_exists('shop86')) {

	ob_start();

	define('SHOP86PATH', WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)));
	define('SHOP86URL', plugin_dir_url(SHOP86PATH) . basename(dirname(__FILE__)));

	// require_once(SHOP86PATH. "/models/shop86CartWidget.php");
	require_once(SHOP86PATH. "/models/core.php");
	require_once(SHOP86PATH. "/models/functions.php");
	require_once(SHOP86PATH. "/models/cart.php");
	require_once(SHOP86PATH. "/models/orders.php");

	define('shop86_VERSION_NUMBER', '0.0.1');


	$shop86 = new shop86();



	load_plugin_textdomain( 'shop86', false, '/' . basename(dirname(__FILE__)) . '/languages/' );

	// Register activation hook to install database and plugin
	register_activation_hook(SHOP86PATH . '/shop86.php', array(&$shop86, 'install'));

	// Register deactivation hook to uninstall database and plugin
	register_deactivation_hook(SHOP86PATH . '/shop86.php', array(&$shop86, 'uninstall'));


	add_action( 'init', array(&$shop86, 'register_custom_posts'), 0 );
	add_action( 'admin_menu', array(&$shop86, 'group_menu_items'), 0 );

	//Products

	add_filter( 'manage_shop86products_posts_columns', array(&$shop86, 'add_shop86_product_columns') );
	add_action( 'manage_posts_custom_column', array(&$shop86, 'customize_shop86_product_columns') );

	add_action( 'admin_head', array(&$shop86,'shop86_admin_styles'));

	// Metabox products
	add_action( 'add_meta_boxes', array(&$shop86,'add_main_metabox') );
	add_action('save_post', array(&$shop86,'save_main_metabox') );

	// Orders

	add_filter( 'manage_shop86products_page_shop86orders_columns', array(&$shop86, 'add_shop86_order_columns') );
	add_action( 'manage_posts_custom_column', array(&$shop86, 'customize_shop86_order_columns') );

//add a button to the content editor, next to the media button
//this button will show a popup that contains inline content
	add_action('media_buttons_context', 'add_my_custom_button');

	// Update after adding Thickbox ajax handling requests;;
	// add_action( 'wp_footer' , array(&$shop86, 'add_cart_html'));

	add_action( 'init','fsm_thickbox' );

	//Adding Ajax handler requests
	add_action( 'wp_ajax_nopriv_update_shop86_cart', array(&$shop86,'update_shop86_cart'));
	add_action( 'wp_ajax_update_shop86_cart', array(&$shop86,'update_shop86_cart'));

	add_action( 'wp_ajax_nopriv_shop86_cart_showForm', array(&$shop86,'shop86_cart_showForm'));
	add_action( 'wp_ajax_shop86_cart_showForm', array(&$shop86,'shop86_cart_showForm'));


	add_action( 'wp_ajax_nopriv_shop86_cart_addOrder', array(&$shop86,'shop86_cart_addOrder'));
	add_action( 'wp_ajax_shop86_cart_addOrder', array(&$shop86,'shop86_cart_addOrder'));

	/**
	 * fsm_thickbox function.
	 *
	 * @access public
	 * @param mixed $text
	 * @return void
	 */
	function fsm_thickbox(){
		if( !is_admin() ){
			// wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'thickbox',null,array( 'jquery' ) );
			wp_enqueue_script('shop86',plugins_url('/shop86.js', __FILE__), array('jquery'), '');
			// declare the variables we need to access in js
			wp_localize_script( 'shop86',
									'Shop86Ajax', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ),
																'ajaxNonce' => wp_create_nonce( 'shop86_nonce' ),
				));
			wp_enqueue_style( 'thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0' );
			// Add custom css to frond-end;
			wp_register_style( 'shop86user', plugins_url('shop86.css', __FILE__) );
			wp_enqueue_style( 'shop86user' );
		}

	}



	add_shortcode('shop86',array(&$shop86, 'add_shop86_cart_button'));

//add some content to the bottom of the page
//This will be shown in the inline modal
	add_action('admin_footer', 'add_inline_popup_content');

//action to add a custom button to the content editor
	function add_my_custom_button($context) {

		//the id of the container I want to show in the popup
		$container_id = 'popup_container';

		//our popup's title
		$title = 'An Inline Popup!';

		//append the icon
		$context .= "<a class='thickbox button add_product' title='{$title}'
    href='#TB_inline&inlineId={$container_id}'>
    <span class='add_product_btn_img' /></span>
    ".__('Add product', 'shop86')."
    </a>";

		return $context;
	}

	function add_inline_popup_content() {
		?>
		<div id="popup_container" style="display:none;padding:0;margin:0;">
		<style type="text/css">
			body {
				min-width: 0;
			}
			#wphead {
				font-size: 80%;
				border-top: 0;
				color: #555;
				background-color: #f1f1f1;
			}
			#wphead h1 {
				font-size: 24px;
				color: #555;
				margin: 0;
				padding: 10px;
			}
			#tabs {
				padding: 15px 15px 3px;
				background-color: #f1f1f1;
				border-bottom: 1px solid #dfdfdf;
				margin: 0;
			}
			#tabs li {
				display: inline;
			}
			#tabs a.current {
				background-color: #fff;
				border-color: #dfdfdf;
				border-bottom-color: #fff;
				color: #d54e21;
			}
			#tabs a {
				color: #2583AD;
				padding: 6px;
				border-width: 1px 1px 0;
				border-style: solid solid none;
				border-color: #f1f1f1;
				text-decoration: none;
			}
			#tabs a:hover {
				color: #d54e21;
			}
			.wrap h2 {
				border-bottom-color: #dfdfdf;
				color: #555;
				margin: 5px 0;
				padding: 0;
				font-size: 18px;
			}
			#user_info {
				right: 5%;
				top: 5px;
			}
			h3 {
				font-size: 1.1em;
				margin-top: 10px;
				margin-bottom: 0px;
			}
			#flipper {
				margin: 0;
				padding: 5px 20px 10px;
				background-color: #fff;
				border-left: 1px solid #dfdfdf;
				border-bottom: 1px solid #dfdfdf;
			}
			* html {
				overflow-x: hidden;
				overflow-y: scroll;
			}
			#flipper div p {
				margin-top: 0.4em;
				margin-bottom: 0.8em;
				text-align: justify;
			}
			th {
				text-align: center;
			}
			.top th {
				text-decoration: underline;
			}
			.top .key {
				text-align: center;
				width: 5em;
			}
			.top .action {
				text-align: left;
			}
			.align {
				border-left: 3px double #333;
				border-right: 3px double #333;
			}
			.keys {
				margin-bottom: 15px;
				width: 100%;
				border: 0 none;
			}
			.keys p {
				display: inline-block;
				margin: 0px;
				padding: 0px;
			}
			.keys .left { text-align: left; }
			.keys .center { text-align: center; }
			.keys .right { text-align: right; }
			td b {
				font-family: "Times New Roman" Times serif;
			}
			#buttoncontainer {
				text-align: center;
				margin-bottom: 20px;
			}
			#buttoncontainer a, #buttoncontainer a:hover {
				border-bottom: 0px;
			}
			.macos .win,
			.windows .mac {
				display: none;
			}
			#TB_ajaxContent{
				margin:0;
				padding:0;
			}
		</style>
			<ul id="tabs">
				<li><a id="tab1" href="javascript:flipTab(1)" title="Basics of Rich Editing" accesskey="1" class="current">Basics</a></li>
				<li><a id="tab2" href="javascript:flipTab(2)" title="Advanced use of the Rich Editor" accesskey="2" class="">Advanced</a></li>
				<li><a id="tab3" href="javascript:flipTab(3)" title="Hotkeys" accesskey="3" class="">Hotkeys</a></li>
				<li><a id="tab4" href="javascript:flipTab(4)" title="About the software" accesskey="4" class="">About</a></li>
			</ul>
			<div id="flipper" class="wrap">
				<div id="content1" class="">
					<h2><?php _e('Adding shop item','shop86'); ?></h2>
					<p>
						<?php
						$products = get_posts(array('post_type' => 'shop86products'));
						print '<span class="shop86lightbox_productName">';
						echo '<select class="shop86lightbox_productID">';
						foreach ($products as $product) {
							echo '<option value="', $product->ID, '"', /*,  $selected == $product->ID ? ' selected="selected"' : '' ,*/ '>', $product->post_title, '</option>';
						}
						echo '</select>';
						print '</span>';
						print '<input type="button" class="media-button button-primary button-large media-button-insert"
										  id="cancel" name="cancel" value="'. __('Add product','shop86') .'"
										  title="'. __('Add product','shop86') .'" onclick="insertthkBCLink()">';
						?>

					</p>
				</div>

				<div id="content2" class="hidden">
					<h2>Advanced Rich Editing</h2>
					<h3>Images and Attachments</h3>
					<p>There is a button in the editor toolbar for inserting images that are already hosted somewhere on the internet. If you have a URL for an image, click this button and enter the URL in the box which appears.</p>
					<p>If you need to upload an image or another media file from your computer, you can use the Media Library button above the editor. The media library will attempt to create a thumbnail-sized copy from each uploaded image. To insert your image into the post, first click on the thumbnail to reveal a menu of options. When you have selected the options you like, click "Insert into Post" and your image or file will appear in the post you are editing.</p>
					<h3>HTML in the Rich Editor</h3>
					<p>Any HTML entered directly into the rich editor will show up as text when the post is viewed. What you see is what you get. When you want to include HTML elements that cannot be generated with the toolbar buttons, you must enter it by hand in the Text editor. Examples are tables and &lt;code&gt;. To do this, click the Text tab and edit the code, then switch back to Visual mode. If the code is valid and understood by the editor, you should see it rendered immediately.</p>
					<h3>Pasting in the Rich Editor</h3>
					<p>When pasting content from another web page the results can be inconsistent and depend on your browser and on the web page you are pasting from. The editor tries to correct any invalid HTML code that was pasted, but for best results try using the Text tab or one of the paste buttons that are on the second row. Alternatively try pasting paragraph by paragraph. In most browsers to select one paragraph at a time, triple-click on it.</p>
					<p>Pasting content from another application, like Word or Excel, is best done with the Paste from Word button on the second row, or in Text mode.</p>
				</div>

				<div id="content3" class="hidden">
					<h2>Writing at Full Speed</h2>
					<p>Rather than reaching for your mouse to click on the toolbar, use these access keys. Windows and Linux use Ctrl + letter. Macintosh uses Command + letter.</p>

					<table class="keys">
						<tbody><tr class="top"><th class="key center">Letter</th><th class="left">Action</th><th class="key center">Letter</th><th class="left">Action</th></tr>
						<tr><th>c</th><td>Copy</td><th>v</th><td>Paste</td></tr>
						<tr><th>a</th><td>Select all</td><th>x</th><td>Cut</td></tr>
						<tr><th>z</th><td>Undo</td><th>y</th><td>Redo</td></tr>

						<tr><th>b</th><td>Bold</td><th>i</th><td>Italic</td></tr>
						<tr><th>u</th><td>Underline</td><th>1</th><td>Heading 1</td></tr>
						<tr><th>2</th><td>Heading 2</td><th>3</th><td>Heading 3</td></tr>
						<tr><th>4</th><td>Heading 4</td><th>5</th><td>Heading 5</td></tr>
						<tr><th>6</th><td>Heading 6</td><th>9</th><td>Address</td></tr>
						</tbody></table>

					<p>The following shortcuts use different access keys: Alt + Shift + letter.</p>
					<table class="keys">
						<tbody><tr class="top"><th class="key center">Letter</th><th class="left">Action</th><th class="key center">Letter</th><th class="left">Action</th></tr>
						<tr><th>n</th><td>Check Spelling</td><th>l</th><td>Align Left</td></tr>
						<tr><th>j</th><td>Justify Text</td><th>c</th><td>Align Center</td></tr>
						<tr><th>d</th><td><span style="text-decoration: line-through;">Strikethrough</span></td><th>r</th><td>Align Right</td></tr>
						<tr><th>u</th><td><strong>•</strong> List</td><th>a</th><td>Insert link</td></tr>
						<tr><th>o</th><td>1. List</td><th>s</th><td>Remove link</td></tr>
						<tr><th>q</th><td>Quote</td><th>m</th><td>Insert Image</td></tr>
						<tr><th>w</th><td>Distraction Free Writing mode</td><th>t</th><td>Insert More Tag</td></tr>
						<tr><th>p</th><td>Insert Page Break tag</td><th>h</th><td>Help</td></tr>
						</tbody></table>

					<p style="padding: 15px 10px 10px;">Editor width in Distraction Free Writing mode:</p>
					<table class="keys">
						<tbody><tr><th><span class="win">Alt +</span><span class="mac">Ctrl +</span></th><td>Wider</td>
							<th><span class="win">Alt -</span><span class="mac">Ctrl -</span></th><td>Narrower</td></tr>
						<tr><th><span class="win">Alt 0</span><span class="mac">Ctrl 0</span></th><td>Default width</td><th></th><td></td></tr>
						</tbody></table>
				</div>

				<div id="content4" class="hidden">
					<h2>About TinyMCE</h2>

					<p>Version: <span id="version">3.5.8-wp</span> (<span id="date">2012-12-19</span>)</p>
					<p>TinyMCE is a platform independent web based Javascript HTML WYSIWYG editor released as Open Source under <a href="http://wp/wp-includes/js/tinymce/license.txt" target="_blank" title="GNU Library General Public License">LGPL</a>	by Moxiecode Systems AB. It has the ability to convert HTML TEXTAREA fields or other HTML elements to editor instances.</p>
					<p>Copyright © 2003-2011, <a href="http://www.moxiecode.com" target="_blank">Moxiecode Systems AB</a>, All rights reserved.</p>
					<p>For more information about this software visit the <a href="http://tinymce.com" target="_blank">TinyMCE website</a>.</p>

				</div>
			</div>
			<script type="text/javascript">
				function d(id) { return document.getElementById(id); }

				function flipTab(n) {
					var i, c, t;

					for ( i = 1; i <= 4; i++ ) {
						c = d('content'+i.toString());
						t = d('tab'+i.toString());
						if ( n == i ) {
							c.className = '';
							t.className = 'current';
						} else {
							c.className = 'hidden';
							t.className = '';
						}
					}
				}


				/* Small hack to remove padding and margin for inline content */
				document.getElementById('TB_ajaxContent').style.width = '';
				document.getElementById('TB_ajaxContent').style.height = '';

				// Add product button js function
				function insertthkBCLink() {

					var tagtext;

					var thkBC = document.getElementById('thkBC_options');

					var productID = jQuery(".shop86lightbox_productID option:selected").val();


					var x;
					var var_arr = new Array();
					var_arr['id'] = productID;

					if ( productID != '' ){

						tagtext = "";
						for (x in var_arr)
						{
							if(var_arr[x] != ''){
								tagtext += " " + x + "=\"" + var_arr[x] + "\"";
							}
						}
						tagtext += "";

						// TODO remove this shit
					} else {}

					window.send_to_editor('[shop86 '+tagtext+']');

					return;
				}

			</script>
		</div>
	<?php
	}
}