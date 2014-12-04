<?php
/**
 * 
 * @package   Presslaff Inteface
 * @author    The Real Dizzle <mrealmuto@hbi.com>
 * @license   GPL-2.0+
 * @link      http://www.hubbardchicagoradio.com
 * @copyright 2013 Hubbard Radio Chicago
 *
 * @wordpress-plugin
 * Plugin Name: Presslaff Interface
 * Plugin URI:  n/a
 * Description: The Presslaff Interace will interact with the Presslaff Contest and Registration APIs
 * Version:     1.0.0
 * Author:      Michael Realmuto
 * Author URI:  http://www.hubbardradiochicago.com
 * Text Domain: plugin-name-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// TODO: replace `class-plugin-name.php` with the name of the actual plugin's class file
require_once( plugin_dir_path( __FILE__ ) . "presslaff.class.php");
require_once( plugin_dir_path( __FILE__ ) . 'class-presslaff-interface.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
// TODO: replace Plugin_Name with the name of the plugin defined in `class-plugin-name.php`
register_activation_hook( __FILE__, array( 'Presslaff_Interface', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Presslaff_Interface', 'deactivate' ) );

// TODO: replace Plugin_Name with the name of the plugin defined in `class-plugin-name.php`
Presslaff_Interface::get_instance();

