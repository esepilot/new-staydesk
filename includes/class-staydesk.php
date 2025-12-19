<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        if (defined('STAYDESK_VERSION')) {
            $this->version = STAYDESK_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'staydesk';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-loader.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-settings.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-auth.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-dashboard.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-admin.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-bookings.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-rooms.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-payments.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-subscriptions.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-chatbot.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-notifications.php';
        require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-api.php';

        $this->loader = new Staydesk_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     */
    private function define_admin_hooks() {
        $admin = new Staydesk_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     */
    private function define_public_hooks() {
        $auth = new Staydesk_Auth();
        $dashboard = new Staydesk_Dashboard();
        $api = new Staydesk_API();

        // Enqueue public styles and scripts
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_public_styles');
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_public_scripts');

        // Auth hooks
        $this->loader->add_action('init', $auth, 'init');
        
        // Dashboard hooks
        $this->loader->add_action('init', $dashboard, 'init');

        // API hooks
        $this->loader->add_action('rest_api_init', $api, 'register_routes');

        // Add chatbot widget to footer
        $this->loader->add_action('wp_footer', $this, 'add_whatsapp_widget');
    }

    /**
     * Enqueue public styles.
     */
    public function enqueue_public_styles() {
        wp_enqueue_style($this->plugin_name, STAYDESK_PLUGIN_URL . 'public/css/staydesk-public.css', array(), $this->version, 'all');
    }

    /**
     * Enqueue public scripts.
     */
    public function enqueue_public_scripts() {
        wp_enqueue_script($this->plugin_name, STAYDESK_PLUGIN_URL . 'public/js/staydesk-public.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'staydesk_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('staydesk_nonce')
        ));
    }

    /**
     * Add WhatsApp chatbot widget.
     */
    public function add_whatsapp_widget() {
        include STAYDESK_PLUGIN_DIR . 'templates/chatbot-widget.php';
    }

    /**
     * Run the loader to execute all of the hooks.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}
