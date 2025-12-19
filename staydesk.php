<?php
/**
 * Plugin Name: StayDesk by BendlessTech
 * Plugin URI: https://bendlesstech.com/staydesk
 * Description: A comprehensive hotel assistant platform designed for hotels in Nigeria. Manage bookings, rooms, payments, and guest enquiries with an integrated AI chatbot.
 * Version: 1.0.0
 * Author: BendlessTech
 * Author URI: https://bendlesstech.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: staydesk
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('STAYDESK_VERSION', '1.0.0');
define('STAYDESK_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('STAYDESK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('STAYDESK_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_staydesk() {
    require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-activator.php';
    Staydesk_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_staydesk() {
    require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-deactivator.php';
    Staydesk_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_staydesk');
register_deactivation_hook(__FILE__, 'deactivate_staydesk');

/**
 * The core plugin class.
 */
require STAYDESK_PLUGIN_DIR . 'includes/class-staydesk.php';

/**
 * Begins execution of the plugin.
 */
function run_staydesk() {
    $plugin = new Staydesk();
    $plugin->run();
}
run_staydesk();
