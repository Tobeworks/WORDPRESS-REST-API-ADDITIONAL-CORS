<?php
/**
 * WP REST API CORS
 *
 * @package           wp-restapi-cors
 * @author            Tobias Lorsbach
 * @copyright         2021 Tobeworks
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WP REST API CORS
 * Description:       Add cross origin requests POST, GET, OPTIONS, PUT, DELETE for your Wordpress REST API. (https://developer.mozilla.org/de/docs/Web/HTTP/CORS)
 * Version:           2.0.0
 * Requires PHP:      7.2
 * Author:            Tobias Lorsbach
 * Author URI:        https://tobeworks.de
 * Text Domain:       wp-restapi-cors
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) {
  exit;
}

define('_WPRAC','wp_restapi_cors');


/**
 * Activate the plugin.
 */
function wp_restapi_cors_Init(){ 
    add_option('wp_restapi_cors_options',true);	
  }
  register_activation_hook( __FILE__, 'wp_restapi_cors_Init' );
  
  
  /**
   * Deactivation hook.
   */
  function wp_restapi_cors_Deactivate() {
    delete_option('wp_restapi_cors_options');
  }
  register_deactivation_hook( __FILE__, 'wp_restapi_cors_Deactivate' );
  


function wp_restapi_cors_addCors() {

  $options = get_option('wp_restapi_cors_options');

  $origin_url = '*';
  header( 'Access-Control-Allow-Origin: ' . $origin_url );
  header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, HEAD' );
  header( 'Access-Control-Expose-Headers: Link' );
  header( 'Access-Control-Allow-Credentials: true');
}
add_action( 'rest_api_init', function() {
	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	add_filter( 'rest_pre_serve_request', wp_restapi_cors_addCors);
}, 15 );


function wp_restapi_cors_admin_init(){
    wp_restapi_cors_admin_assets();
    
}
add_filter('init','wp_restapi_cors_admin_init');


function wp_restapi_cors_save_data(){
      if(isset($_POST['wprac_submit'])){

        $res = $_POST;
        unset($res['wprac_submit']);
        if(isset($_POST['wprac_enabled'])){
           update_site_option('wp_restapi_cors_options',$res);
        }else{
            update_site_option('wp_restapi_cors_options',[]);
        }  
        //print_r(get_option('wp_restapi_cors_options'));
        //print_r(update_site_option('wp_restapi_cors_options',$_POST));
      }
}

function wp_restapi_cors_admin_assets(){

  if(is_admin() == true){
    wp_enqueue_script('wp_restapi_cors_admin_js' , plugin_dir_url(__FILE__) . 'js/wp_restapi_cors_admin.js', array('jquery'), 1.2, true);
    wp_enqueue_style( 'wp_restapi_cors_admin_css', plugin_dir_url(__FILE__). 'css/styles.css', array(), '1.0', 'all');
  }

}


function wp_restapi_cors_options_page_sub(){
  if(current_user_can('manage_options') == false) {
    return false;
  }
    add_submenu_page(
        'options-general.php',
        'WP-REST-API Options',
        'WP-REST-API Options',
        'manage_options',
        'wp-restapi-cors',
        'wp_restapi_cors_admin_page'
    );
}
add_action('admin_menu', 'wp_restapi_cors_options_page_sub');


function wp_restapi_cors_generate(){

  $options = get_option('wp_restapi_cors_options');

  $res_methods_arr= array();

  if(isset($options['wprac_enabled']) && $options['wprac_enabled'] == 1){
      $wprac_enabled_checked = $checked;
  }

  $wprac_POST_checked = false;
  if(isset($options['wprac_POST']) && $options['wprac_POST'] == 1){
    $res_methods_arr['wprac_POST'] = 'POST';
  }

  if(isset($options['wprac_GET']) && $options['wprac_GET'] == 1){
    $res_methods_arr['wprac_GET'] = 'GET';
  }

  if(isset($options['wprac_OPTIONS']) && $options['wprac_OPTIONS'] == 1){
    $res_methods_arr['wprac_OPTIONS'] = 'OPTIONS';
  }
 
  if(isset($options['wprac_PUT']) && $options['wprac_PUT'] == 1){
    $res_methods_arr['wprac_PUT'] = 'PUT';
  }

  if(isset($options['wprac_DELETE']) && $options['wprac_DELETE'] == 1){
    $res_methods_arr['wprac_DELETE'] = 'DELETE';
  }
  $credent  = false;
  if(isset($options['wprac_CREDENTIALS']) && $options['wprac_CREDENTIALS'] == 1){
      $credent = 'Access-Control-Allow-Credentials: true';
  }

  $head= false;
  if(isset($options['wprac_HEAD']) && $options['wprac_HEAD'] == 1){
    $head ='Access-Control-Expose-Headers: Link';
    $res_methods_arr['wprac_HEAD'] = 'HEAD';
  }



  if(isset($options['wprac_ORIGIN_select']) && $options['wprac_ORIGIN_select'] == 1 ){
    $wprac_ORIGIN_select = $checked;
    $wprac_ORIGIN = $options['wprac_ORIGIN'];
 }
 //print_r($res_methods); 
 //var_dump(implode(',',$res_methods_arr));
  ?>
    <pre>
    Access-Control-Allow-Origin: <?= $wprac_ORIGIN ?>
    Access-Control-Allow-Methods: <?= implode(',',$res_methods_arr) ?> 
    <?= $head ?>
    <?= $credent  ?>
    </pre>
  <?php

    // header( 'Access-Control-Allow-Origin: ' . $options);
    // header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, HEAD' );
    // header( 'Access-Control-Expose-Headers: Link' );
    // header( 'Access-Control-Allow-Credentials: true');
}




function wp_restapi_cors_admin_page(){
  if(current_user_can( 'manage_options') == false) {
	  return false;
	}

  wp_restapi_cors_save_data();

  $options = get_option('wp_restapi_cors_options');

  $checked = 'checked="checked"';

  $wprac_enabled_checked = false;
  if(isset($options['wprac_enabled']) && $options['wprac_enabled'] == 1){
      $wprac_enabled_checked = $checked;
  }

  $wprac_POST_checked = false;
  if(isset($options['wprac_POST']) && $options['wprac_POST'] == 1){
      $wprac_POST_checked = $checked;
  }
  $wprac_GET_checked = false;
  if(isset($options['wprac_GET']) && $options['wprac_GET'] == 1){
      $wprac_GET_checked = $checked;
  }
  $wprac_OPTIONS_checked = false;
  if(isset($options['wprac_OPTIONS']) && $options['wprac_OPTIONS'] == 1){
      $wprac_OPTIONS_checked = $checked;
  }
  $wprac_PUT_checked = false;
  if(isset($options['wprac_PUT']) && $options['wprac_PUT'] == 1){
      $wprac_PUT_checked = $checked;
  }
  $wprac_DELETE_checked = false;
  if(isset($options['wprac_DELETE']) && $options['wprac_DELETE'] == 1){
      $wprac_DELETE_checked = $checked;
  }
  $wprac_CREDENTIALS_checked = false;
  if(isset($options['wprac_CREDENTIALS']) && $options['wprac_CREDENTIALS'] == 1){
      $wprac_CREDENTIALS_checked = $checked;
  }
  $wprac_HEAD_checked = false;
  if(isset($options['wprac_HEAD']) && $options['wprac_HEAD'] == 1){
      $wprac_HEAD_checked = $checked;
  }
  $wprac_ORIGIN_select = false;
  $wprac_ORIGIN = false;
  if(isset($options['wprac_ORIGIN_select']) && $options['wprac_ORIGIN_select'] == 1 ){
    $wprac_ORIGIN_select = $checked;
    $wprac_ORIGIN = $options['wprac_ORIGIN'];
  }



  ?>
    <h1><?php _e( 'WP REST API - CORS Administration', _WPRAC ); ?></h1>
      <form action="./options-general.php?page=wp-restapi-cors" method="post">
      <legend class="screen-reader-text"><span>Available CORS</span></legend>
      <fieldset id="WPRAC_enabler">
            <legend class="screen-reader-text"><span>Enable</span></legend>
        <label for="wprac_enabled">
          <input name="wprac_enabled" type="checkbox" id="wprac_enabled" value="1" <?= $wprac_enabled_checked  ?> />
          <span><?php esc_attr_e( 'Enable/Disable CORS for this REST API', _WPRAC ); ?></span>
        </label>
      </fieldset>

      <fieldset id="WPRAC_formset">
      <div>
          <label for="wprac_POST">
            <input name="wprac_POST" type="checkbox" id="wprac_POST" value="1" <?= $wprac_POST_checked  ?> />
            <span><?php esc_attr_e( 'POST', _WPRAC ); ?></span>
          </label>
      </div>

      <div>
          <label for="wprac_GET">
            <input name="wprac_GET" type="checkbox" id="wprac_GET" value="1" <?= $wprac_GET_checked  ?>  />
            <span><?php esc_attr_e( 'GET', _WPRAC ); ?></span>
          </label>
      </div>

      <div>
          <label for="wprac_OPTIONS">
            <input name="wprac_OPTIONS" type="checkbox" id="wprac_OPTIONS" value="1" <?= $wprac_OPTIONS_checked  ?>  />
            <span><?php esc_attr_e( 'OPTIONS', _WPRAC ); ?></span>
          </label>
      </div>
      
      <div>
          <label for="wprac_PUT">
            <input name="wprac_PUT" type="checkbox" id="wprac_PUT" value="1" <?= $wprac_PUT_checked  ?>  />
            <span><?php esc_attr_e( 'PUT', _WPRAC ); ?></span>
          </label>
      </div>
      <div>
          <label for="wprac_DELETE">
            <input name="wprac_DELETE" type="checkbox" id="wprac_DELETE" value="1" <?= $wprac_DELETE_checked  ?>  />
            <span><?php esc_attr_e('DELETE', _WPRAC ); ?></span>
          </label>
      </div>
      <div>
          <label for="wprac_ORIGIN">
            <input type="checkbox" name="wprac_ORIGIN_select" id="wprac_ORIGIN_select" value="1" <?= $wprac_ORIGIN_select  ?>  />
            <input type="text" value="<?= $wprac_ORIGIN  ?>" placeholder="* | origin | null" name="wprac_ORIGIN" class="regular-text" />  
            <span><?php esc_attr_e('ORIGIN', _WPRAC ); ?></span>
          </label>
      </div>
      <div>
          <label for="wprac_CREDENTIALS">
            <input type="checkbox" name="wprac_CREDENTIALS" id="wprac_CREDENTIALS" value="1" <?= $wprac_CREDENTIALS_checked  ?>  />
            <span><?php esc_attr_e('Allow Credentials', _WPRAC ); ?></span>
          </label>
      </div>
      <div>
          <label for="wprac_HEAD">
            <input type="checkbox" name="wprac_HEAD" id="wprac_HEAD" value="1" <?= $wprac_HEAD_checked  ?>  />
            <span><?php esc_attr_e('HEAD', _WPRAC ); ?></span>
          </label>
      </div>
    </fieldset>
    <div style="padding:10px 0">
      <input class="button-primary" type="submit" name="wprac_submit" value="<?php esc_attr_e( 'Save values' ); ?>" /> 
    </div>
    </form>
  <?php
  if(isset($_POST['wprac_submit'])){
    wp_restapi_cors_generate();
  }
}

?>