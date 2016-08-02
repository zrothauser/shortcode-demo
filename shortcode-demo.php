<?php
/*
Plugin Name: Shortcode Demo
Plugin URI: https://zackrothauser.com
Description: Adds a Pullquote shortcode that can be inserted with a TinyMCE button.
Version: 1.0
Author: Zack Rothauser
Author URI: https://zackrothauser.com
License: GPL2
*/

// Useful global constants
define( 'SHORTCODE_DEMO_PATH',    dirname( __FILE__ ) . '/' );
define( 'SHORTCODE_DEMO_INC',     SHORTCODE_DEMO_PATH . 'includes/' );
define( 'SHORTCODE_DEMO_URL',     plugin_dir_url( __FILE__ ) );
define( 'SHORTCODE_DEMO_VERSION', '1.0' );

// Include files
require_once SHORTCODE_DEMO_INC . 'class-shortcode-demo.php';

// Start main class
$shortcode_demo_plugin = new Shortcode_Demo;

// Connect actions
add_action( 'init', array( $shortcode_demo_plugin, 'load' ) );
