<?php 
/*
Plugin Name: Code Injection
Plugin URI: https://wordpress.org/plugins/code-injection/
Description: add extra code
Version: 0.1.0
Author: Sidraj
Author URI: http://Codeinject.com
Text Domain: Code Injection
Domain Path: /languages
*/

?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php
register_activation_hook( __FILE__, 'child_plugin_activate' );
add_action( 'admin_notices', 'child_plugin_activate');
function child_plugin_activate(){

    // Require parent plugin
    if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) and current_user_can( 'activate_plugins' ) ) {
        // Stop activation redirect and show error
       echo "<div id='message' class='error'><p>Sry we need woocommerce first.</p></div>";
     deactivate_plugins( 'codeinjection/main.php' );
    }


}
add_action('admin_menu', 'rudr_top_lvl_menu');
add_action('admin_init', 'codeplugin_init');

function rudr_top_lvl_menu() {
  add_menu_page(
    'plugin Settings', // page <title>Title</title>
    'codeinjection', // link text
    'manage_options', // user capabilities
    'code_inject', // page slug
    'check_box_callback', // this function prints the page content
    'dashicons-images-alt2', // icon (from Dashicons for example)
    4 // menu position
  );
}

function codeplugin_init() {
  register_setting('codepage', 'codeplugin_settings');
  add_settings_section(
    'codeplugin_pluginPage_section',
    __('', 'cdplugin'),
    'codeplugin_settings_section_callback',
    'codepage'
  );

  add_settings_field(
    'plugin_checkbox_field_0',
    __('Add Discount Field', 'cdplugin'),
    'plugin_checkbox_field_0_render',
    'codepage',
    'codeplugin_pluginPage_section'
  );
}

function plugin_checkbox_field_0_render() {
  $checkbox = get_option('codeplugin_settings');
  $is_checked = ($checkbox != '' && $checkbox == 1) ? 'checked' : '';

  printf(
    '<input type="checkbox" id="disabletitle_text" name="codeplugin_settings" value="1" %s/>',
    esc_attr($is_checked)
  );
}

function check_box_callback() {
  ?>
  <form action='options.php' method='post'>

    <h2>codeinjection</h2>

    <?php
    settings_fields('codepage');
    do_settings_sections('codepage');
    submit_button();
    ?>

  </form>
  <?php
}


add_action( 'woocommerce_after_add_to_cart_quantity', 'ts_quantity_plus_sign' );
function ts_quantity_plus_sign() {

echo '<button type="button" class="plus" >+</button>';
}

add_action( 'woocommerce_before_add_to_cart_quantity', 'ts_quantity_minus_sign' );

function ts_quantity_minus_sign() {
echo '<button type="button" class="minus" >-</button>';
}

add_action( 'wp_footer', 'ts_quantity_plus_minus' );

function ts_quantity_plus_minus() {
// To run this on the single product page
if ( ! is_product() ) return;
?>
<script type="text/javascript">

jQuery(document).ready(function($){

$('form.cart').on( 'click', 'button.plus, button.minus', function() {

// Get current quantity values
var qty = $( this ).closest( 'form.cart' ).find( '.qty' );
var val = parseFloat(qty.val());
var max = parseFloat(qty.attr( 'max' ));
var min = parseFloat(qty.attr( 'min' ));
var step = parseFloat(qty.attr( 'step' ));

// Change the value if plus or minus
if ( $( this ).is( '.plus' ) ) {
if ( max && ( max <= val ) ) {
qty.val( max );
}
else {
qty.val( val + step );
}
}
else {
if ( min && ( min >= val ) ) {
qty.val( min );
}
else if ( val > 1 ) {
qty.val( val - step );
}
}

});

});

</script>
<?php
}                           
add_action('admin_head', 'my_custom_fonts');
function my_custom_fonts() {
  echo '<style>
    button#add_field_button {
    position: absolute;
    right: 26%;
    top: 41%;
}
 button#remove_field_button {
    position: absolute;
    right: 26%;
    top: 41%;
}

span#remove {
    position: absolute;
    /* left: 9px; */
    right: 32%;
    padding: 6px;
    background: #f6f7f7;
    border-radius: 50%;
    /* height: 5px; */
    /* width: 5px; */
    line-height: 10px;
    font-size: 37px;
    border: 1px solid #0071ae;
    cursor: pointer;
}
</style>';
}
// add custom fields in the backend 
function create_custom_field_in_backend() {
    $checkboxx = get_option('codeplugin_settings');
    $is_checkedd = ($checkboxx != '' && $checkboxx == 1) ? 'checked' : '';

    if ($is_checkedd) {
        $args = array(
            'id' => 'Extra_bonus',
            'label' => __('Extra Bonus', 'cfwc'),
            'class' => 'cfwc-custom-field',
            'desc_tip' => true,
            'description' => __('Enter the title of your custom text field.', 'ctwc'),
        );

        woocommerce_wp_text_input($args);


   // retive post meta fields
 $post_id = get_the_ID();
        $appendedFieldValues = get_post_meta($post_id, 'input_field', true);
//print_r($appendedFieldValues);
//print_r(unserialize('a:2:{i:0;s:4:"5656";i:1;s:6:"205656";}'));
 if (!empty($appendedFieldValues) && is_array($appendedFieldValues)) {
foreach($appendedFieldValues as $val)
{
 echo ' <div class="new_append" ><p class="form-field Extra_bonus_field"><label for="Extra_bonus_'.$val.'">Extra Bonus ' .$val. '</label></span><input type="text" class="cfwc-custom-field" style="" name="input_field[]" id="Extra_bonus_' .$val. '" value="' .$val. '" placeholder=""><span id="remove">-</span></p></div>';
}
  }      // Add a button for dynamically adding more fields
        echo '<button type="button" id="add_field_button" class="button">Add Field</button>';
      
        // JavaScript for adding more fields dynamically
        ?>
        <script>  $(this).closest('.list_product').remove();
            jQuery(document).ready(function($) {
              $('#remove_field_button').hide();
                var i = 0;
                $('#add_field_button').click(function() {
                    i++;
                     if($(".new_append").length<4)  
      
                    {
                    var field = '<div class="new_append" ><p class="form-field Extra_bonus_field"><label for="Extra_bonus_' + i + '">Extra Bonus ' + i + '</label></span><input type="text" class="cfwc-custom-field addfield" style="" name="input_field[]" id="Extra_bonus_' + i + '" value=" " placeholder=""><span id="remove">-</span></p></div>';
                    $('#general_product_data').append(field);
                      
                  }
                                    




                   })
                });
              jQuery(document).on('click', '#remove', function(e) {
   e.preventDefault();
   jQuery(this).closest('.new_append').remove();
  
});
   
           

             
        </script>
        <?php
    }
}
add_action('woocommerce_product_options_general_product_data', 'create_custom_field_in_backend');

// Save the appended field values
function save_custom_field_values($post_id) {
    if (isset($_POST['input_field'])  &&  $_POST['input_field']!='null') {
        $appendedFieldValues = array_map('sanitize_text_field', $_POST['input_field']);

        // Update the product meta with the appended field values
        update_post_meta($post_id, 'input_field', $appendedFieldValues);
    }
}
add_action('woocommerce_process_product_meta', 'save_custom_field_values');

//Update custom field

// function save_custom_field_in_backend( $post_id ) {
//  $product = wc_get_product( $post_id );
//  $title = isset( $_POST['Extra_bonus'] ) ? $_POST['Extra_bonus'] : '';
//  $product->update_meta_data( 'Extra_bonus', sanitize_text_field( $title ) );
//  $product->save();
// }
// add_action( 'woocommerce_process_product_meta', 'save_custom_field_in_backend' );


// display custom field in front-end


// function cfwc_add_custom_field_item_data( $cart_item_datassd) {
//  if( ! empty( $_POST['Extra_bonus'] ) ) {
//  // Add the item data
//  $cart_item_datassd['Extra_bonus'] = $_POST['Extra_bonus'];
//  }
//  return $cart_item_data;
// }
// add_filter( 'woocommerce_add_cart_item_data', 'cfwc_add_custom_field_item_data', 10, 4 );
// global $product_name;

// Display in cart and checkout pages
// add_filter( 'woocommerce_cart_item_name', 'customizing_cart_item_name', 10, 3 );
// function customizing_cart_item_name( $product_name, $cart_item ) {

//     $product = $cart_item['data']; // Get the WC_Product Object

//     if ( $value = $product->get_meta('Extra_bonus') ) {
//         $product_name .= '<td>'.$value.'%</td>';
//     }
//     return $product_name;
// }


// add_filter( 'woocommerce_calculated_total', 'custom_calculated_total', 10, 2 );

// function custom_calculated_total( $total, $cart ) {
// 	global $woocommerce;
//     foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
//         $original_price   = $cart_item['data']->get_price(); // Get original product price
       
//         // Get meta data for the cart item
//         $meta_data = $cart_item['data']->get_meta_data();

//         $my_meta_value = $cart_item['data']->get_meta( 'Extra_bonus', true );

//         $quantity = $cart_item['quantity'];
//         //print_r($my_meta_value);
//         $data = $total * $my_meta_value / 100;
//         // $totall = $total-$data;
  

//     }

//     return $total-$data;
// }
add_action( 'woocommerce_cart_calculate_fees', 'progressive_discount_based_on_cart_total', 10, 1 );
function progressive_discount_based_on_cart_total( $cart_object ) {
 global $woocommerce;
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;
       
        

 foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
 	$pushh = array();
   	   // $meta_data = $cart_item['data']->get_meta_data();
 	 $per_meter_cost = 5;

        $my_meta_value = $cart_item['data']->get_meta( 'Extra_bonus', true );
         $P_name = $cart_item['data']->get_name();
//print_r($P_name);
 $percent = $my_meta_value; 
//print_r($cart_object);
    
        $cart_total = $cart_object->line_subtotal; 
         $line_subtotal = $cart_item['line_subtotal'];// Cart total
// print_r($line_subtotal);
     
    if ( $percent != 0 ) {
        $discount = $percent / 100*$line_subtotal;
     print_r($cart_total);
        $cart_object->add_fee( "Discount on $P_name ($percent%)", -$discount, true );
       
    }

}
// foreach ( $cart_object->get_cart() as $cart_item_keys => $cart_item_data ) {
//    $quantity = $cart_item['quantity'];
//         $Heightt_of = $cart_item_data['Height_of'];
// print_r($Heightt_of);
//  $cart_object->add_fee( "Cost of One bullet($per_meter_cost/Bullet)", $Heightt_of*$quantity*$per_meter_cost, true );
// 	}
}

add_filter('woocommerce_cart_item_subtotal','new_cart_item_subtotal_filter', 10, 3);
function new_cart_item_subtotal_filter( $oldTotal, $cart_item){
    //filter...
     $oldTotal = $cart_item['data']->get_price();
     //print_r($oldTotal);
  //print_r($cart_item);
    $bonus = $cart_item['data']->get_meta('Extra_bonus');
    if($bonus)
    {
    	$quantity =  $cart_item['quantity'];
    	 $Height_of =  $cart_item['Height_of'];
    	// $width_of =  $cart_item['width_of'];
    $multi = $Height_of*5;
    	//print_r($quantity);
    	$data = $oldTotal*$bonus/100;
    	$remain = $oldTotal-$data;
    	print_r($remain*$quantity+$multi);
    }
    else
    {
     echo $oldTotal;	
    }
   // return $cart_item->price*400;
}



// add_action( 'wp_footer', function() {
	
// 	?><script>
// 	jQuery( function( $ ) {
// 		let timeout;
// 		$('.woocommerce').on('click', '[name="update_cart"]', function(){
		
// 			timeout = setTimeout(function() {
// 				alert(parseFloat($("input#quantity_646cadae57cd1").val())); // trigger cart update
// 			}, 1000 ); // 1 second delay, half a second (500) seems comfortable too
// 		});
// 	} );
// 	</script><?php
	
// } );



// add text field on single page
// 1. Show custom input field above Add to Cart

add_action( 'woocommerce_before_add_to_cart_button', 'add_custom_height_width', 9 );

function add_custom_height_width() {

    $height_f = isset( $_POST['height_f'] ) ? sanitize_text_field( $_POST['height_f'] ) : '';
    
    echo '<div class="heihgtwidth"><label>Add bullets Quantity <abbr class="required" title="required">*</abbr></label><p><input name="height_f" value="' . $height_f . '"><button id="add_field">addfield</button></p>
 </div>';
 // $post_idd = get_the_ID();
 //        $appended = get_post_meta($post_idd, 'height_', true);
 //        print_r($appended);

 ?>
<script>  $(this).closest('.list_product').remove();
            jQuery(document).ready(function($) {
              $('#remove_field_button').hide();
                var i = 0;
                var ram = 0;
                $('#add_field').click(function(event) {
                  console.log('sasaaa')
                   event.preventDefault()
                    i++;

                     if($(".addvalues").length<4)  
      ram++;
                    {
                    var fieldd = '<div class="addvalues"><p><input name="height_[name_'+ram+']" value=" "></p></div>';
                    $('.heihgtwidth').append(fieldd);
                      
                  }
                                    




                   })
                });
              jQuery(document).on('click', '#remove', function(e) {
   e.preventDefault();
   jQuery(this).closest('.new_append').remove();
  
});
   
           

             
        </script>
        <?php 
}

function add_custom_height_width_validation() { 
    if ( empty( $_REQUEST['height_f'] ) ) {
        wc_add_notice( __( 'Please enter a Height', 'woocommerce' ), 'error' );
        return false;
    }
    
    return true;
}
add_action( 'woocommerce_add_to_cart_validation', 'add_custom_height_width_validation', 10, 3 );
function save_name_on_tshirt_fields( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['height_'] ) ) {
        $cart_item_data[ 'Height_o' ] = $_REQUEST['height_'];
        /* below statement make sure every add to cart action as unique line item */
         $cart_item_data['unique_keys'] = md5( microtime().rand() );
    }
   
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_name_on_tshirt_fields', 10, 2 );
function save_name_on_tshirt_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['height_f'] ) ) {
        $cart_item_data[ 'Height_of' ] = $_REQUEST['height_f'];
        /* below statement make sure every add to cart action as unique line item */
         $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
   
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_name_on_tshirt_field', 10, 2 );

function render_meta_on_cart_and_checkout( $cart_data, $cart_item = null ) {
    $custom_items = array();
    $new_items = [];

    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
        $new_items = $cart_data;

    }
    if( isset( $cart_item['Height_of'] ) ) {
        $custom_items[] = array( "name" => 'Amount of Bullet', "value" => $cart_item['Height_of'] );
    }
    print_r($cart_item['Height_o']);
       if( isset( $cart_item['Height_o'] ) ) {
            foreach ($cart_item['Height_o'] as $key => $value) {
               echo "</br>";
    echo "$key of: $value";
    echo "</br>";
}}


    return $custom_items;
}
add_filter( 'woocommerce_get_item_data', 'render_meta_on_cart_and_checkout', 10, 2 );




 add_action( 'woocommerce_before_calculate_totals', 'mps_registration_price', 20, 1 );
function mps_registration_price( $cart ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    // Second loop change prices
    foreach ( $cart->get_cart() as $cart_item ) {

        // Get an instance of the WC_Product object (or the variation product object)
        $product = $cart_item['data'];

$quantity = $cart_item['quantity'];
//print_r($quantity);
   $Heightt_of = $cart_item['Height_of'];
  // print_r($Heightt_of);
   $multi = $quantity*$Heightt_of*5;
            // When product 11 is not cart
            if($product ){
                $product->set_price( $product->get_sale_price() + $multi);
            
    }}
}
 if(function_exists('related_posts'))
{
related_posts();
}
?>
