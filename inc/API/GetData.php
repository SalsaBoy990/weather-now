<?php

namespace AG\WeatherNow\API;

class GetData
{
    use \AG\WeatherNow\Log\Logger;

    public function apiCall(): array
    {
        $apiOptions = get_option('ag_weather_api_options');
        if (empty($apiOptions)) {
            return false;
        }

        $extractedOptions = [];

        try {
            foreach ($apiOptions as $key => $val) {
                if (!empty($apiOptions[$key])) {
                    $extractedOptions[$key] = $val;
                } else {
                    throw new APIQueryException('Error: query parameter "' . $key . '" is missing.');
                }
            }
        } catch (APIQueryException $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_WEATHER_NOW_LOGGING, $ex);

        } catch (\Exception $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(\AG_WEATHER_NOW_LOGGING, $ex);
        }

        $url = "http://api.openweathermap.org/data/2.5/weather?q={$extractedOptions['city']}," .
            "{$extractedOptions['country_code']}&appid={$extractedOptions['api_key']}" .
            "&lang={$extractedOptions['language']}&units={$extractedOptions['units']}";

        // Report all errors except E_NOTICE
        error_reporting(E_ALL & ~E_WARNING);

        try {
            // change to the free URL if you're using the free version
            // WP HTTP API
            $res = wp_remote_get($url);
            $resCode = wp_remote_retrieve_response_code($res);
            $resBody = wp_remote_retrieve_body($res);

            if ($resCode !== 200 ||  $resCode['cod'] !== 200) {
            } else {
                throw new \Exception('API request failed. Check your API settings!');
            }
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
        // report all errors
        error_reporting(E_ALL);

        // store update time in the options field used for ajax
        $apiOptions['last_updated'] = time();
        update_option('ag_weather_api_options', $apiOptions);

        $data = json_decode($resBody, true);
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        return $data;
    }
}
