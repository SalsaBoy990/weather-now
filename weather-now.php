<?php

namespace AG\WeatherNow;

defined('ABSPATH') or die();

/*
Plugin Name: Weather Now
Plugin URI: https://github.com/SalsaBoy990/weather-now
Description: Current Weather plugin
Version: 1.0
Author: András Gulácsi
Author URI: https://github.com/SalsaBoy990
License: GPLv2 or later
Text Domain: ag-weather-now
Domain Path: /languages
*/

// require all requires once
require_once 'requires.php';

use \AG\WeatherNow\WeatherNow as WeatherNow;

use \AG\WeatherNow\Log\KLogger as Klogger;


$ag_weather_now_log_file_path = plugin_dir_path(__FILE__) . '/log';

$ag_weather_now_log = new KLogger($ag_weather_now_log_file_path, KLogger::INFO);

// main class
WeatherNow::getInstance();

// we don't need to do anything when deactivation
// register_deactivation_hook(__FILE__, function () {});

register_activation_hook(__FILE__, '\AG\WeatherNow\WeatherNow::activatePlugin');

// delete options when uninstalling the plugin
register_uninstall_hook(__FILE__, '\AG\WeatherNow\WeatherNow::uninstallPlugin');
