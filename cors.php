<?php
/**
 * @package REST API CORS
 * @version 1.0.2
 */
/*
Plugin Name: Additional Cors for WP REST API

Description: Additional HTTP CORS Header for the WP REST API
Author: Tobias Lorsbach
Version: 1.0.2
Author URI: https://tobeworks.de
*/

if (!defined('ABSPATH')) {
  exit;
}

function addCors() {
  $origin_url = '*';
  header( 'Access-Control-Allow-Origin: ' . $origin_url );
  header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
  header( 'Access-Control-Allow-Credentials: true');
}
add_action( 'rest_api_init', function() {
	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	add_filter( 'rest_pre_serve_request', addCors);
}, 15 );
?>