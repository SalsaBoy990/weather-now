<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h1><?php esc_attr_e('Current Weather in your City'); ?></h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">

                        <h2><span><?php esc_attr_e('Set Up Current Weather Widget'); ?></span></h2>

                        <div class="inside">
                            <form action="options.php" method="post">
                                <?php
                                settings_fields(self::FIELD_PREFIX . 'current_weather');

                                // page slug needed as arg!
                                do_settings_sections('current_weather_opts');

                                submit_button();

                                ?>
                            </form>
                        </div>
                        <!-- .inside -->
                    </div>
                    <!-- .postbox -->
                </div>
                <!-- .meta-box-sortables .ui-sortable -->
            </div>
            <!-- post-body-content -->

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <h2><span><?php esc_attr_e(
                                        'Current Weather Widget Preview',
                                        'wp_admin_style'
                                    ); ?></span></h2>

                        <div class="inside">
                            <?php
                            echo $generatedHtml;
                            ?>
                        </div>
                        <!-- .inside -->
                    </div>
                    <!-- .postbox -->
                </div>
                <!-- .meta-box-sortables -->
            </div>
            <!-- #postbox-container-1 .postbox-container -->
        </div>
        <!-- #post-body .metabox-holder .columns-2 -->
        <br class="clear">
    </div>
    <!-- #poststuff -->
</div> <!-- .wrap -->

<div class="wrap">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">

                        <h2><span><?php esc_attr_e('Response Viewer'); ?></span></h2>

                        <div class="inside">
                            <?php
                            echo '<pre>';
                            print_r($responseData);
                            echo '</pre>';
                            ?>
                        </div>
                        <!-- .inside -->
                    </div>
                    <!-- .postbox -->
                </div>
                <!-- .meta-box-sortables .ui-sortable -->
            </div>
            <!-- post-body-content -->
            <!-- #postbox-container-1 .postbox-container -->
        </div>
        <!-- #post-body .metabox-holder .columns-2 -->
        <br class="clear">
    </div>
    <!-- #poststuff -->
</div> <!-- .wrap -->