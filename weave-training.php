<?php
/**
 * Plugin Name: Weave Website Training
 * Plugin URI: https://github.com/weavedigitalstudio/weave-training
 * Description: A simple WordPress plugin that adds a dedicated "Website Training" page to the WordPress admin area for our client specific training videos and notes. Bundled on every site by Weave Digital. Uses an iframe from our central training resources.
 * Version: 1.0.0
 * Author: Weave Digital, Gareth Bissland
 * Author URI: https://weave.co.nz
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: weave-training
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WEAVE_TRAINING_VERSION', '1.0.0');
define('WEAVE_TRAINING_PLUGIN_NAME', 'Weave Training');
define('WEAVE_TRAINING_PLUGIN_SLUG', 'weave-training');
define('WEAVE_TRAINING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WEAVE_TRAINING_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WEAVE_TRAINING_PLUGIN_FILE', __FILE__);
define('WEAVE_TRAINING_MENU_SLUG', 'weave-training');

// Plugin information functions
if (!function_exists('weave_training_get_version')) {
    /**
     * Get plugin version
     * 
     * @return string Plugin version
     */
    function weave_training_get_version() {
        return WEAVE_TRAINING_VERSION;
    }
}

if (!function_exists('weave_training_get_plugin_data')) {
    /**
     * Get plugin data
     * 
     * @return array Plugin data
     */
    function weave_training_get_plugin_data() {
        return array(
            'name' => WEAVE_TRAINING_PLUGIN_NAME,
            'version' => WEAVE_TRAINING_VERSION,
            'slug' => WEAVE_TRAINING_PLUGIN_SLUG,
            'file' => WEAVE_TRAINING_PLUGIN_FILE,
            'dir' => WEAVE_TRAINING_PLUGIN_DIR,
            'url' => WEAVE_TRAINING_PLUGIN_URL,
            'text_domain' => 'weave-training'
        );
    }
}

// Plugin activation hook
register_activation_hook(__FILE__, 'weave_training_activate');

// Plugin deactivation hook  
register_deactivation_hook(__FILE__, 'weave_training_deactivate');

/**
 * Plugin activation function
 * 
 * @return void
 */
function weave_training_activate() {
    // Check WordPress version compatibility
    if (version_compare(get_bloginfo('version'), '5.0', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('Weave Training requires WordPress 5.0 or higher.', 'weave-training'));
    }
    
    // Check PHP version compatibility
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('Weave Training requires PHP 7.4 or higher.', 'weave-training'));
    }
    
    // Check if current user has required capabilities
    if (!current_user_can('activate_plugins')) {
        return;
    }
    
    // Store activation timestamp
    update_option('weave_training_activated', time());
    update_option('weave_training_version', WEAVE_TRAINING_VERSION);
}

/**
 * Plugin deactivation function
 * 
 * @return void
 */
function weave_training_deactivate() {
    // Check if current user has required capabilities
    if (!current_user_can('activate_plugins')) {
        return;
    }
    
    // Clean up activation timestamp (optional - you might want to keep this for reactivation)
    delete_option('weave_training_activated');
    
    // Note: We don't delete the version option in case of reactivation
}

// Include GitHub updater
require_once WEAVE_TRAINING_PLUGIN_DIR . 'includes/github-updater.php';

// Initialize plugin
add_action('init', 'weave_training_init');

/**
 * Initialize the plugin
 * 
 * @return void
 */
function weave_training_init() {
    // Load text domain for translations
    load_plugin_textdomain('weave-training', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Add admin menu hook
add_action('admin_menu', 'weave_training_add_admin_menu');

/**
 * Add admin menu callback function
 * 
 * @return void
 */
function weave_training_add_admin_menu() {
    // Check if user has required capability
    if (!current_user_can('edit_posts')) {
        return;
    }
    
    // Add menu page
    add_menu_page(
        __('Website Training', 'weave-training'),    // Page title
        __('Website Training', 'weave-training'),    // Menu title
        'edit_posts',                                // Capability
        WEAVE_TRAINING_MENU_SLUG,                   // Menu slug
        'weave_training_display_page',              // Callback function
        'dashicons-video-alt3',                     // Icon
        3                                           // Position (after Dashboard)
    );
}

/**
 * Display the training page
 * 
 * @return void
 */
function weave_training_display_page() {
    // Check if user has required capability
    if (!current_user_can('edit_posts')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'weave-training'));
    }
    
    // Get training URL
    $training_url = weave_training_get_url();
    
    // Enqueue admin assets
    weave_training_enqueue_admin_assets();
    
    // Output the iframe container (JavaScript will handle the full-screen setup)
    echo '<div id="weave-training-container">';
    echo '<div id="weave-training-loading">';
    echo '<p>' . esc_html__('Loading training content...', 'weave-training') . '</p>';
    echo '</div>';
    echo '</div>';
    
    // Pass the URL to JavaScript
    wp_localize_script(
        'weave-training-admin', 
        'weaveTraining', 
        array(
            'url' => esc_url($training_url),
            'title' => esc_attr__('Website Training', 'weave-training'),
            'loadingText' => esc_html__('Loading training content...', 'weave-training'),
            'errorText' => esc_html__('Failed to load training content. Please try refreshing the page.', 'weave-training'),
            'unsupportedText' => esc_html__('Your browser does not support iframes.', 'weave-training')
        )
    );
}

/**
 * Get the training URL from constant or default
 * 
 * @return string The training URL
 */
function weave_training_get_url() {
    $default_url = 'https://training.weave.digital';
    
    // Check for WEAVE_TRAINING_URL constant first
    if (defined('WEAVE_TRAINING_URL') && !empty(WEAVE_TRAINING_URL)) {
        $url = WEAVE_TRAINING_URL;
        
        // Log custom URL usage for debugging
        error_log('Weave Training: Using custom URL from WEAVE_TRAINING_URL constant');
    } else {
        // Fallback to default URL
        $url = $default_url;
        
        // Log default URL usage for debugging
        error_log('Weave Training: Using default URL (WEAVE_TRAINING_URL not defined)');
    }
    
    // Sanitize URL
    $url = esc_url_raw($url);
    
    // Validate URL format
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        // Log validation failure
        error_log('Weave Training: Invalid URL format, falling back to default: ' . $url);
        
        // If invalid, fallback to default
        $url = $default_url;
    }
    
    // Final validation check
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        // This should never happen, but just in case
        error_log('Weave Training: Critical error - default URL is invalid');
        wp_die(__('Weave Training: Unable to load training content due to invalid URL configuration.', 'weave-training'));
    }
    
    return $url;
}

/**
 * Enqueue admin assets for training page
 * 
 * @return void
 */
function weave_training_enqueue_admin_assets() {
    // Get current screen
    $screen = get_current_screen();
    
    // Only enqueue on training page
    if (!$screen || $screen->id !== 'toplevel_page_' . WEAVE_TRAINING_MENU_SLUG) {
        return;
    }
    
    // CSS file path
    $css_file = WEAVE_TRAINING_PLUGIN_DIR . 'assets/css/weave-training-admin.css';
    $css_url = WEAVE_TRAINING_PLUGIN_URL . 'assets/css/weave-training-admin.css';
    
    // Enqueue CSS if file exists
    if (file_exists($css_file)) {
        wp_enqueue_style(
            'weave-training-admin',
            $css_url,
            array(),
            WEAVE_TRAINING_VERSION
        );
    } else {
        // Log error for debugging
        error_log('Weave Training: CSS file not found at ' . $css_file);
    }
    
    // JavaScript file path
    $js_file = WEAVE_TRAINING_PLUGIN_DIR . 'assets/js/weave-training-admin.js';
    $js_url = WEAVE_TRAINING_PLUGIN_URL . 'assets/js/weave-training-admin.js';
    
    // Enqueue JavaScript if file exists
    if (file_exists($js_file)) {
        wp_enqueue_script(
            'weave-training-admin',
            $js_url,
            array('jquery'),
            WEAVE_TRAINING_VERSION,
            true
        );
    } else {
        // Log error for debugging
        error_log('Weave Training: JavaScript file not found at ' . $js_file);
    }
} 