<?php

namespace AG\WeatherNow\Widget;

defined('ABSPATH') or die();

use \AG\WeatherNow\API\GetData as GetData;

use \AG\WeatherNow\Output\OutputData as OutputData;

class WeatherNowWidget extends \WP_Widget
{
    private $getData;

    private $outputData;


    /**
     * Sets up a new WeatherNow Widget instance.
     *
     * @since 2.8.0
     */
    public function __construct()
    {
        $this->getData = new GetData();
        $this->outputData = new OutputData();

        $widget_ops = array(
            'classname'                   => 'weather_now_widget',
            'description'                 => __('Shows the current weather in your city using the Open Weather API.'),
            'customize_selective_refresh' => true,
        );

        parent::__construct('ag_weather_now_widget', __('Weather Now Widget'), $widget_ops);
        $this->alt_option_name = 'ag_weather_now_widget';
    }

    /**
     * Outputs the content for the current AG_YT_Video_Embed_Single widget instance.
     *
     * @since 2.8.0
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Recent Reviews widget instance.
     */
    public function widget($args, $instance)
    {
        extract($args);

        if (!isset($args['widget_id'])) {
            $args['widget_id'] = $this->id;
        }

        $heading = (!empty($instance['heading'])) ? $instance['heading'] : __('');
        
        // // json decoded data from API
        // $responseData = $this->getData->apiCall();

        // // converted data to the right formats
        // $generatedHtml = $this->outputData->convertFormat($responseData);

        // $generatedHtml = $this->outputData->injectDataToHtml($generatedHtml);

        // populate HTML view with data
        require_once AG_WEATHER_NOW_PLUGIN_DIR . '/pages/weatherNowWidgetTemplate.php';
    }



    /**
     * Handles updating the settings for the current AG_YT_Video_Embed_Single widget instance.
     *
     * @since 2.8.0
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update($new_instance, $old_instance)
    {
        $instance           = $old_instance;
        $instance['heading']  = sanitize_text_field($new_instance['heading']);

        return $instance;
    }

    /**
     * Outputs the settings form for the AG_YT_Video_Embed_Single widget.
     *
     * @since 2.8.0
     *
     * @param array $instance Current settings.
     */
    public function form($instance)
    {
        $heading = isset($instance['heading']) ? esc_attr($instance['heading']) : '';
?>
        <div>
            <p><?php _e('Adds your WeatherNow Widget.'); ?></p>
            <p>
                <label for="<?php echo $this->get_field_id('heading'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('heading'); ?>" name="<?php echo $this->get_field_name('heading'); ?>" type="text" value="<?php echo $heading; ?>" />
            </p>
        </div>
<?php
    }
}
