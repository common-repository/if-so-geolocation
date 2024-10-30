<?php
namespace IfSo\Addons\Geolocation;

use IfSo\Services\PluginSettingsService;

if(class_exists('\IfSo\Addons\Base\Settings')){
    class Settings extends \IfSo\Addons\Base\Settings{
        public function print_extra_settings_ui_geolocation(){
            $settingsService = PluginSettingsService\PluginSettingsService::get_instance();
            $log_analyzer_url = admin_url("?page=wpcdd_ifso_geo_log_analyzer");
            ?>
            <tr valign="top">
                <td class="ifso-settings-td" scope="row" valign="baseline">
                    <b><?php _e('Log geolocation requests', 'if-so'); ?></b>
                </td>
                <td valign="baseline">
                    <input
                            type="checkbox"
                        <?php echo ($settingsService->extraOptions->geolocation['logGeoRequests']->get() ? "CHECKED" : ""); ?>
                            name="ifso_geolocation_logGeoRequests"
                            type="text"
                            class="ifso_settings_page_option"
                            value="log_geo_requests" />
                    <i><?php _e("Enable this option if the geolocation count doesn’t seem to match the number visits on your 3rd-party analytics reports. The log will help you identify IPs of bots visiting your site and block them from the geolocation service: ", 'if-so'); ?><a href="https://www.if-so.com/faq-items/the-geolocation-session-count-doesnt-seem-to-behave-as-expected/?utm_source=Plugin&utm_medium=settings&utm_campaign=geolocation_log" target="_blank">Learn More</a> </i>
                </td>
            </tr>
            <tr valign="top">
                <td class="ifso-settings-td" scope="row" valign="baseline"></td>
                <td valign="baseline">
                    <a onclick="window.open('<?php echo esc_url($log_analyzer_url); ?>', 'newwindow', 'width=800,height=900'); return false;" href="<?php echo esc_url($log_analyzer_url); ?>" target="">Analyze Geolocation Request Log</a>
                </td>
            </tr>
            <tr valign="top">
                <td class="ifso-settings-td" scope="row" valign="baseline">
                    <b><?php _e('Block Bots (Beta)', 'if-so'); ?></b>
                </td>
                <td valign="baseline">
                    <input
                            type="checkbox"
                        <?php echo ($settingsService->extraOptions->geolocation['blockGeoBots']->get() ? "CHECKED" : ""); ?>
                            name="ifso_geolocation_blockGeoBots"
                            type="text"
                            class="ifso_settings_page_option"
                            value="log_geo_requests" />
                    <i><?php _e('Prevent Search Engine Crawlers, CURL Requests, and other bots from accessing the geolocation service. Default content will be displayed regardless of the bot\'s location. <a href="https://www.if-so.com/faq-items/the-geolocation-session-count-doesnt-seem-to-behave-as-expected/#searchEngines">Learn more</a>.'); ?> </i>
                </td>
            </tr>
            <tr valign="top">
                <td class="ifso-settings-td" scope="row" valign="baseline">
                    <b><?php _e('Browser-Based Location', 'if-so'); ?></b>
                </td>
                <td valign="baseline">
                    <i><?php _e("Serve location-based content by utilizing the browser's Geolocation API (HTML5). This method is more accurate than the default IP-to-location approach, but it does require obtaining users' consent to access their location.", 'if-so'); ?> <a href="https://www.if-so.com/the-html-geolocation-api/?utm_source=Plugin&utm_medium=settings&utm_campaign=geolocation&utm_term=learnMore" target="_blank">Learn More</a></i> <br><br> Choose when to request access to the user's location: <br><br>
                    <input style="margin-top:0;margin-right:6px;" type="radio" name="ifso_geolocation_browserLocationMode" value="0" <?php echo (int) $settingsService->extraOptions->geolocation['browserLocationMode']->get() === 0 ? "CHECKED" : ''; ?>><label style="line-height: 1.7;">Never</label><br>
                    <input style="margin-top:0;margin-right:6px;" type="radio" name="ifso_geolocation_browserLocationMode" value="1" <?php echo (int) $settingsService->extraOptions->geolocation['browserLocationMode']->get() === 1 ? "CHECKED" : ''; ?>><label style="line-height: 1.7;">When the user encounters the shortcode [ifso_get_browser_location]</label><br>
                    <input style="margin-top:0;margin-right:6px;" type="radio" name="ifso_geolocation_browserLocationMode" value="2" <?php echo (int) $settingsService->extraOptions->geolocation['browserLocationMode']->get() === 2 ? "CHECKED" : ''; ?>><label style="line-height: 1.7;">Whenever the user encounters geo-targeted content</label><br>
                    <input style="margin-top:0;margin-right:6px;" type="radio" name="ifso_geolocation_browserLocationMode" value="3" <?php echo (int) $settingsService->extraOptions->geolocation['browserLocationMode']->get() === 3 ? "CHECKED" : ''; ?>><label style="line-height: 1.7;">Every time the user visits the site</label<br>
                </td>
            </tr>
            <?php
        }

        public function register_extra_settings($extra_settings){
            $load_modal_option = function(){
                $default = false;
                $postName = 'ifso_geolocation_logGeoRequests';
                return  new PluginSettingsService\IfSoSettingsYesNoOption($postName  . '_option',$default,$postName);
            };

            $load_block_geo_bots = function(){
                $default = false;
                $postName = 'ifso_geolocation_blockGeoBots';
                return  new PluginSettingsService\IfSoSettingsYesNoOption($postName  . '_option',$default,$postName);
            };

            $browser_geolocation_mode = function(){
                $default = 0;
                $postName = 'ifso_geolocation_browserLocationMode';
                return  new PluginSettingsService\IfSoSettingsNumberOption($postName  . '_option',$default,$postName);
            };

            $extra_settings->geolocation = ['logGeoRequests' => $load_modal_option(),'blockGeoBots'=>$load_block_geo_bots(),'browserLocationMode'=>$browser_geolocation_mode()];

            return $extra_settings;
        }

    }
}