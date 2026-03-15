<?php
/**
 * Plugin Name: Simple Theme Settings (STS)
 * Description: STS adds a new admin menu page, where you are able to add simple global settings to your theme.
 * Version: 1.1
 * Author: AENDERS.DK
 * Author URI: https://aenders.dk
 */


// @@ EXIT IF ACCESSED DIRECTLY
if ( ! defined( 'ABSPATH' ) ) exit;


// @@ THE PLUGIN PATH
define( 'STS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );


// @@ LOAD ALL FILES IN THE ORDER
require_once STS_PLUGIN_PATH . 'includes/schema-helpers.php';
require_once STS_PLUGIN_PATH . 'includes/fields-config.php';
require_once STS_PLUGIN_PATH . 'includes/helpers.php';
require_once STS_PLUGIN_PATH . 'includes/admin-page.php';
require_once STS_PLUGIN_PATH . 'includes/polylang-bridge.php';
require_once STS_PLUGIN_PATH . 'includes/polylang-strings.php';