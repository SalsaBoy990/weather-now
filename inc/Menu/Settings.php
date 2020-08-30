<?php

namespace AG\WeatherNow\Menu;

defined('ABSPATH') or die();

use \AG\WeatherNow\API\GetData as GetData;

use \AG\WeatherNow\Output\OutputData as OutputData;

class Settings
{
    use \AG\WeatherNow\Log\Logger;

    private const FIELD_PREFIX = 'ag_';
    private $getData;
    private $outputData;

    public function __construct()
    {
        $this->getData = new GetData();
        $this->outputData = new OutputData();
    }


    public function __destruct()
    {
    }

    public function create_settings()
    {
        $this->logger(AG_WEATHER_NOW_DEBUG, AG_WEATHER_NOW_LOGGING);

        register_setting(
            self::FIELD_PREFIX . 'current_weather',
            self::FIELD_PREFIX . 'weather_api_options',
            array($this, 'sanitize_options')
        );

        add_settings_section(
            self::FIELD_PREFIX . 'current_weather_options',
            '',
            function () {
                printf(
                    '<p>%s <a href="%s" target="_blank">%s</a> %s <a href="%s">%s</a></p>',
                    __('Paste in your free Open Weather API key'),
                    esc_url('https://openweathermap.org/api'),
                    __('(get it here)'),
                    __('and the settlement for which you want to get current weather. Also set the language, country code and'),
                    esc_url('https://openweathermap.org/weather-data'),
                    __('units.')
                );
            },
            'current_weather_opts'
        );

        // API key input
        add_settings_field(
            'api_key',
            'Open Weather API key:',
            array($this, 'setting_api_options'),
            'current_weather_opts',
            self::FIELD_PREFIX . 'current_weather_options',
            array('api_key')
        );

        // City input
        add_settings_field(
            'city',
            'City name:',
            array($this, 'setting_api_options'),
            'current_weather_opts',
            self::FIELD_PREFIX . 'current_weather_options',
            array('city')
        );

        // Language input
        add_settings_field(
            'language',
            'Language:',
            array($this, 'setting_api_options'),
            'current_weather_opts',
            self::FIELD_PREFIX . 'current_weather_options',
            array('language')
        );

        // Country input
        add_settings_field(
            'country_code',
            'Country Code (GB, JP etc.):',
            array($this, 'setting_api_options'),
            'current_weather_opts',
            self::FIELD_PREFIX . 'current_weather_options',
            array('country_code')
        );

        // Units input
        add_settings_field(
            'units',
            'Pick measurement units:',
            array($this, 'setting_api_options'),
            'current_weather_opts',
            self::FIELD_PREFIX . 'current_weather_options',
            array('units')
        );

        // Last updated input
        add_settings_field(
            'last_updated',
            '',
            array($this, 'setting_api_options'),
            'current_weather_opts',
            self::FIELD_PREFIX . 'current_weather_options',
            array('last_updated')
        );
    }


    public static function setting_api_options($args)
    {
        $this->logger(AG_WEATHER_NOW_DEBUG, AG_WEATHER_NOW_LOGGING);

        // see last arg of add_settings_field like
        // array('units') -> units will be the property name
        // stored in options record
        $property = (string) array_pop($args);

        $opt_name = self::FIELD_PREFIX . 'weather_api_options';

        $opts = get_option($opt_name);

        if (empty($opts[$property])) {
            $default_value = self::get_default_api_options()[$property];

            $val = $default_value;
        } else {
            $val = $opts[$property];
        }

        if ($property === 'units') {
?>
            <select name="<?php echo $opt_name . '[' . $property . ']'; ?>">
                <option value="metric" <?php echo selected($val, 'metric'); ?>>metric</option>
                <option value="imperial" <?php echo selected($val, 'imperial'); ?>>imperial</option>
                <option value="standard" <?php echo selected($val, 'standard'); ?>>standard</option>
            </select>
        <?php
        } elseif ($property === 'last_updated') {

            print '<input type="hidden" id="hide-ag_weather_last_updated" class="widefat" value="' . esc_attr($val) . '" />';
        ?>
             <script type="text/javascript">
               jQuery(document).ready(function($) {
                 var t = $('#hide-ag_weather_last_updated')
                     .parent().parent().hide();
                 console.log(t);

                 $('#hide-ag_weather_api_options[last_updated]')
                     .find('[scope=row]').hide();
               });
             </script>

<?php
        } else {
            print '<input type="text" name="' . $opt_name . '[' . $property . ']" class="widefat" value="' . esc_attr($val) . '" />';
        }
    }


    /**
     * Helper function that sets the default value for api options
     *
     * @return array
     */
    private static function get_default_api_options()
    {
        // $this->logger(AG_WEATHER_NOW_DEBUG, AG_WEATHER_NOW_LOGGING);

        return array(
            'api_key'  => __(''),
            'city'  => __('Szeged'),
            'language'  => __('hu'),
            'country_code'  => __('hu'),
            'units'  => __('metric')
        );
    }

    /* This is the settings page template to render for admin user */
    public function settingsForm()
    {
        $this->logger(AG_WEATHER_NOW_DEBUG, AG_WEATHER_NOW_LOGGING);

        // json decoded data from API
        $responseData = $this->getData->apiCall();

        // converted data to the right formats
        $generatedHtml = $this->outputData->convertFormat($responseData);

        $generatedHtml = $this->outputData->injectDataToHtml($generatedHtml);

        require_once AG_WEATHER_NOW_PLUGIN_DIR . '/pages/settingsFormTemplate.php';
    }
}
