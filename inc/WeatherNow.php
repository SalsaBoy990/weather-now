<?php

namespace AG\WeatherNow;

use \AG\WeatherNow\Menu\Settings as Settings;

use \AG\WeatherNow\Widget\WeatherNowWidget as WeatherNowWidget;

defined('ABSPATH') or die();

/**
 * Class made to access the Open Weather API
 * @see https://openweathermap.org/current
 * validate user input, call the API,
 * Author: András Gulácsi 2020
 */
final class WeatherNow
{
    private const TEXT_DOMAIN = 'ag-weather-now';

    private const OPTION_NAME = 'ag_weather_now_version';

    private const OPTION_VERSION = '0.1';

    // class instance
    private static $instance;

    private static $settings;


    // For temperature in Fahrenheit use units=imperial
    // For temperature in Celsius use units=metric
    // Temperature in Kelvin is used by default, in this case no need to use units parameter in API call
    // public $errorMessage = '';
    // public $weatherData = null;

    /**
     * Get class instance, if not exists -> instantiate it
     * @return self $instance
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self(
                new Settings()
            );
        }
        return self::$instance;
    }


    // CONSTRUCTOR ------------------------------
    // initialize properties, some defaults added
    private function __construct(Settings $settings)
    {
        self::$settings = $settings;

        add_action('plugins_loaded', array($this, 'loadTextdomain'));

        add_filter('admin_init', array(self::$settings, 'create_settings'));

        // add admin menu and page
        add_action('admin_menu', array($this, 'addAdminMenu'));

        // put the css into head (only admin page)
        // add_action('admin_head', array($this, 'addCSS'));
          // add script on the backend
          add_action('admin_enqueue_scripts', array($this, 'adminLoadScripts'));

        // put the css before end of </body>
        add_action('wp_enqueue_scripts', array($this, 'addCSS'));

        // add ajax script
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('ag-weather-now', plugin_dir_url(dirname(__FILE__)) . 'js/agWeatherNow.js', array('jquery'));
            
            // enable ajax on frontend
            wp_localize_script('ag-weather-now', 'AGWeatherNowAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'security' => wp_create_nonce('agweathernowajax-vxe4bn6x68')
            ));
        });

        // connect AJAX request with PHP hooks
        add_action('wp_ajax_my_ajax_action', array($this, 'agWeatherNowAJAXHandler'));
        add_action('wp_ajax_nopriv_my_ajax_action', array($this, 'agWeatherNowAJAXHandler'));


        // hook for our widget implementation
        add_action('widgets_init', array($this, 'register_widgets'));
    }


    // DESCTRUCTOR -------------------------------
    public function __destruct()
    {
    }

    // getter
    public function __get($property)
    {
        // get private property
    }

    // setter
    public function __set($property, $value)
    {
        // set private property
    }


    // METHODS
    public static function loadTextdomain(): void
    {
        // modified slightly from https://gist.github.com/grappler/7060277#file-plugin-name-php

        $domain = self::TEXT_DOMAIN;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, trailingslashit(\WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, basename(dirname(__FILE__, 2)) . '/languages/');
    }

    /**
     * Register admin menu page and submenu page
     * @return void
     */
    public function addAdminMenu(): void
    {
        add_menu_page(
            __('Weather Now Admin'), // page title
            __('Current Weather in your City'), // menu title
            'manage_options', // capability
            'weather_now_settings', // menu slug
            array(self::$settings, 'settingsForm'), // callback
            'dashicons-cloud' // icon
        );
    }


    /**
     * Add some styling to the plugin's admin and shortcode UI
     * @return void
     */
    public function addCSS(): void
    {
        
    }

    public function adminLoadScripts($hook)
    {
        if ($hook != 'toplevel_page_weather_now_settings') {
            return;
        }

        wp_enqueue_style(
            'ag_weather_now_admin_css',
            plugins_url() . '/weather-now/css/weather-now.css'
        );
    }


    /**
     * Add add an option with the version when activated
     */
    public static function activatePlugin(): void
    {
        $option = self::OPTION_NAME;
        // check if option exists, then delete
        if (!get_option($option)) {
            add_option($option, self::OPTION_VERSION);
        }
    }


    // This code will only run when plugin is deleted
    // it will drop the custom database table, delete wp_option record (if exists)
    public static function uninstallPlugin()
    {
        // check if option exists, then delete
        if (get_option(self::OPTION_NAME)) {
            delete_option(self::OPTION_NAME);
        }

        // delete settings option created via Settings API
        if (!get_option('ag_weather_api_options')) {
            delete_option('ag_weather_api_options');
        }
    }


    /**
     * Register the new widget.
     *
     * @see 'widgets_init'
     */
    public function register_widgets()
    {
        register_widget('\AG\WeatherNow\Widget\WeatherNowWidget');
    }





    public function agWeatherNowAJAXHandler()
    {
        if (check_ajax_referer('agweathernowajax-vxe4bn6x68', 'security')) {

            $getData = new \AG\WeatherNow\API\GetData();
            $outputData = new \AG\WeatherNow\Output\OutputData();

            // json decoded data from API
            $responseData = $getData->apiCall();

            // converted data to the right formats
            $formattedData = $outputData->convertFormat($responseData);

            wp_send_json_success($formattedData, 200);
        } else {
            wp_send_json_error();
        }
        wp_die();
    }
}
