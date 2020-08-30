;jQuery(document).ready(function ($) {
  
  // only send AJAX request when the weather now widget is added
  if ($("#ag-weather-now-widget").length > 0) {
    var data = {
      action: "my_ajax_action",
      security: AGWeatherNowAjax.security,
    };
  
    console.log(AGWeatherNowAjax.ajax_url);
  
    $.ajax({
      type: "POST",
      url: AGWeatherNowAjax.ajax_url,
      data: data,
      dataType: "json",
    })
      .done(function ($response) {
        console.log($response);
        console.log("AG Weather Now AJAX - OK response.");
  
        var $results = $response.data;
  
        // generate dynamic html
        var $weatherWidget =
          '<div style="background: #fefefe; padding: 5px;">' +
          '<h2 style="margin-bottom: 0;">' +
          $results.name +
          "</h2>" +
          "<ul>" +
          '<li style="color: #999 + margin-top: 0 !important +">' +
          $results.time +
          "</li>" +
          '<li style="display: inline-block;vertical-align: middle; font-weight: 500;">' +
          $results.weatherDescription +
          $results.weatherIcon +
          "</li>" +
          "<li>hőmérséklet: " +
          Math.round($results.temperature) +
          " °C</li>" +
          "<li>páratartalom: " +
          $results.humidity +
          "%</li>" +
          "<li>szél: " +
          Math.round($results.windSpeed) +
          " km/h " +
          $results.windDirection +
          "</li>" +
          '<li><a href="' +
          $results.mapUrl +
          '" target="_blank">Időjárástérkép</a></li>' +
          "</ul>" +
          "</div>";
  
          $("#ag-weather-now-widget").html($weatherWidget);
  
      })
      .fail(function () {
        console.log("AG Weather Now AJAX res error.");
      })
      .always(function () {
        console.log("AG Weather Now AJAX req run.");
      });
  }
  
  
});
