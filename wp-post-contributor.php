<?php
/**
 * Plugin Name: WP Post Contributor
 * Description: WP Post Contributors plugin allows you to add more than one author to the post who have contributed.
 * Author: Sujit
 * version: 1.0
 * license: GPLv3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/* Plugin Root Directory Path */
define( 'WPC_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * Included Files
 */
include_once( WPC_PLUGIN_DIR . '/includes/class-post-contributors.php' );