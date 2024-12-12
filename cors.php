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
 * Version:           2.0.1
 * Requires PHP:      7.4
 * Author:            Tobias Lorsbach
 * Author URI:        https://tobeworks.de
 * Text Domain:       wp-restapi-cors
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */


if (!defined('ABSPATH')) {
  exit;
}

define('_WPRAC', 'wp_restapi_cors');

function wp_restapi_cors_Init()
{
  add_option('wp_restapi_cors_options', true);
}
register_activation_hook(__FILE__, 'wp_restapi_cors_Init');

function wp_restapi_cors_Deactivate()
{
  delete_option('wp_restapi_cors_options');
}
register_deactivation_hook(__FILE__, 'wp_restapi_cors_Deactivate');

function wp_restapi_cors_addCors()
{
  wp_restapi_cors_generate('backend');
}

add_action('rest_api_init', function () {
  remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
  add_filter('rest_pre_serve_request', 'wp_restapi_cors_addCors');
}, 15);

function wp_restapi_cors_admin_init()
{
  wp_restapi_cors_admin_assets();
}
add_action('init', 'wp_restapi_cors_admin_init');

function wp_restapi_cors_save_data()
{
  if (!isset($_POST['wprac_submit'])) {
    return;
  }

  $res = $_POST;
  unset($res['wprac_submit']);

  if (isset($_POST['wprac_enabled'])) {
    update_site_option('wp_restapi_cors_options', $res);
  } else {
    update_site_option('wp_restapi_cors_options', []);
  }
}

function wp_restapi_cors_admin_assets()
{
  if (is_admin() === true) {
    wp_enqueue_script('wp_restapi_cors_admin_js', plugin_dir_url(__FILE__) . 'js/wp_restapi_cors_admin.js', ['jquery'], '1.2', true);
    wp_enqueue_style('wp_restapi_cors_admin_css', plugin_dir_url(__FILE__) . 'css/styles.css', [], '1.0', 'all');
  }
}

function wp_restapi_cors_options_page_sub()
{
  if (!current_user_can('manage_options')) {
    return;
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

function wp_restapi_cors_generate(string $mode = 'frontend')
{
  $options = get_option('wp_restapi_cors_options');
  $res_methods_arr = [];

  if (isset($options['wprac_POST']) && $options['wprac_POST'] == 1) {
    $res_methods_arr['wprac_POST'] = 'POST';
  }

  if (isset($options['wprac_GET']) && $options['wprac_GET'] == 1) {
    $res_methods_arr['wprac_GET'] = 'GET';
  }

  if (isset($options['wprac_OPTIONS']) && $options['wprac_OPTIONS'] == 1) {
    $res_methods_arr['wprac_OPTIONS'] = 'OPTIONS';
  }

  if (isset($options['wprac_PUT']) && $options['wprac_PUT'] == 1) {
    $res_methods_arr['wprac_PUT'] = 'PUT';
  }

  if (isset($options['wprac_DELETE']) && $options['wprac_DELETE'] == 1) {
    $res_methods_arr['wprac_DELETE'] = 'DELETE';
  }

  $credent = false;
  if (isset($options['wprac_CREDENTIALS']) && $options['wprac_CREDENTIALS'] == 1) {
    $credent = 'Access-Control-Allow-Credentials: true';
  }

  $head = false;
  if (isset($options['wprac_HEAD']) && $options['wprac_HEAD'] == 1) {
    $head = 'Access-Control-Expose-Headers: Link';
    $res_methods_arr['wprac_HEAD'] = 'HEAD';
  }

  $wprac_ORIGIN = 'null';
  if (isset($options['wprac_ORIGIN_select']) && $options['wprac_ORIGIN_select'] == 1) {
    $wprac_ORIGIN = $options['wprac_ORIGIN'];
  }

  if (count($res_methods_arr) > 0 && $mode == 'frontend') {
?>
    <pre>
        Access-Control-Allow-Origin: <?php echo $wprac_ORIGIN . "\n"; ?>
        Access-Control-Allow-Methods: <?php echo implode(',', $res_methods_arr) . "\n"; ?>
        <?php echo $head . "\n"; ?>
        <?php echo $credent . "\n"; ?>
        </pre>
  <?php
  } elseif (count($res_methods_arr) > 0 && $mode == 'backend') {
    header('Access-Control-Allow-Origin: ' . $wprac_ORIGIN);
    header('Access-Control-Allow-Methods: ' . implode(',', $res_methods_arr));
    if ($head) {
      header('Access-Control-Expose-Headers: Link');
    }
    if ($credent) {
      header('Access-Control-Allow-Credentials: true');
    }
  }
}

function wp_restapi_cors_admin_page()
{
  if (!current_user_can('manage_options')) {
    return;
  }

  wp_restapi_cors_save_data();

  $options = get_option('wp_restapi_cors_options');
  $checked = 'checked="checked"';

  $wprac_enabled_checked = isset($options['wprac_enabled']) && $options['wprac_enabled'] == 1 ? $checked : '';
  $wprac_POST_checked = isset($options['wprac_POST']) && $options['wprac_POST'] == 1 ? $checked : '';
  $wprac_GET_checked = isset($options['wprac_GET']) && $options['wprac_GET'] == 1 ? $checked : '';
  $wprac_OPTIONS_checked = isset($options['wprac_OPTIONS']) && $options['wprac_OPTIONS'] == 1 ? $checked : '';
  $wprac_PUT_checked = isset($options['wprac_PUT']) && $options['wprac_PUT'] == 1 ? $checked : '';
  $wprac_DELETE_checked = isset($options['wprac_DELETE']) && $options['wprac_DELETE'] == 1 ? $checked : '';
  $wprac_CREDENTIALS_checked = isset($options['wprac_CREDENTIALS']) && $options['wprac_CREDENTIALS'] == 1 ? $checked : '';
  $wprac_HEAD_checked = isset($options['wprac_HEAD']) && $options['wprac_HEAD'] == 1 ? $checked : '';

  $wprac_ORIGIN_select = isset($options['wprac_ORIGIN_select']) && $options['wprac_ORIGIN_select'] == 1 ? $checked : '';
  $wprac_ORIGIN = isset($options['wprac_ORIGIN']) ? $options['wprac_ORIGIN'] : '';
  ?>

  <h1><?php _e('WP REST API - CORS Administration', _WPRAC); ?></h1>
  <form action="./options-general.php?page=wp-restapi-cors" method="post">
    <legend class="screen-reader-text"><span>Available CORS</span></legend>
    <fieldset id="WPRAC_enabler">
      <legend class="screen-reader-text"><span>Enable</span></legend>
      <label for="wprac_enabled">
        <input name="wprac_enabled" type="checkbox" id="wprac_enabled" value="1" <?php echo $wprac_enabled_checked; ?> />
        <span><?php esc_attr_e('Enable/Disable CORS for this REST API', _WPRAC); ?></span>
      </label>
    </fieldset>

    <fieldset id="WPRAC_formset">
      <div>
        <label for="wprac_POST">
          <input name="wprac_POST" type="checkbox" id="wprac_POST" value="1" <?php echo $wprac_POST_checked; ?> />
          <span><?php esc_attr_e('POST', _WPRAC); ?></span>
        </label>
      </div>

      <div>
        <label for="wprac_GET">
          <input name="wprac_GET" type="checkbox" id="wprac_GET" value="1" <?php echo $wprac_GET_checked; ?> />
          <span><?php esc_attr_e('GET', _WPRAC); ?></span>
        </label>
      </div>

      <div>
        <label for="wprac_OPTIONS">
          <input name="wprac_OPTIONS" type="checkbox" id="wprac_OPTIONS" value="1" <?php echo $wprac_OPTIONS_checked; ?> />
          <span><?php esc_attr_e('OPTIONS', _WPRAC); ?></span>
        </label>
      </div>

      <div>
        <label for="wprac_PUT">
          <input name="wprac_PUT" type="checkbox" id="wprac_PUT" value="1" <?php echo $wprac_PUT_checked; ?> />
          <span><?php esc_attr_e('PUT', _WPRAC); ?></span>
        </label>
      </div>

      <div>
        <label for="wprac_DELETE">
          <input name="wprac_DELETE" type="checkbox" id="wprac_DELETE" value="1" <?php echo $wprac_DELETE_checked; ?> />
          <span><?php esc_attr_e('DELETE', _WPRAC); ?></span>
        </label>
      </div>

      <div>
        <label for="wprac_ORIGIN">
          <input type="checkbox" name="wprac_ORIGIN_select" id="wprac_ORIGIN_select" value="1" <?php echo $wprac_ORIGIN_select; ?> />
          <input type="text" value="<?php echo esc_attr($wprac_ORIGIN); ?>" placeholder="* | origin | null" name="wprac_ORIGIN" class="regular-text" />
          <span><?php esc_attr_e('ORIGIN', _WPRAC); ?></span>
        </label>
      </div>

      <div>
        <label for="wprac_CREDENTIALS">
          <input type="checkbox" name="wprac_CREDENTIALS" id="wprac_CREDENTIALS" value="1" <?php echo $wprac_CREDENTIALS_checked; ?> />
          <span><?php esc_attr_e('Allow Credentials', _WPRAC); ?></span>
        </label>
      </div>

      <div>
        <label for="wprac_HEAD">
          <input type="checkbox" name="wprac_HEAD" id="wprac_HEAD" value="1" <?php echo $wprac_HEAD_checked; ?> />
          <span><?php esc_attr_e('HEAD', _WPRAC); ?></span>
        </label>
      </div>
    </fieldset>

    <div style="padding:10px 0">
      <input class="button-primary" type="submit" name="wprac_submit" value="<?php esc_attr_e('Save values'); ?>" />
    </div>
  </form>
<?php
  wp_restapi_cors_generate();
}
