<?php
namespace IfSo\Addons\Geolocation;

include_once IFSO_PLUGIN_BASE_DIR . 'extensions/extension-base/ifso-extension-include.php';
require_once (__DIR__ . '/ifso-geolocation-addon-settings.class.php');
require_once (__DIR__ . '/services/geo-request-log-service.class.php');
require_once(IFSO_PLUGIN_BASE_DIR . 'services/geolocation-service/geolocation-service.class.php');

use  IfSo\Services\GeolocationService;

if(class_exists('\IfSo\Addons\Base\ExtensionMain')){
    class GeolocationExtension extends \IfSo\Addons\Base\ExtensionMain{
        public $geo_override_cookie_name = 'ifso-geo-override-data';
        public $browser_location_cookie_name = 'ifso-browser-location-data';
        public $req_browser_loc_cookie_name = 'ifso-request-browser-location';
        private $bad_geo_request = false;
        protected $addon_settings;
        protected function __construct(){
            if(class_exists('IfSo\Addons\Geolocation\Settings')){
                $this->addon_settings = new Settings();
            }

            add_action('ifso_extra_sumbenu_items',function (){
                add_submenu_page('',null,null,'manage_options','wpcdd_ifso_geo_log_analyzer',array($this,'display_geo_analysis_page'));
            });

            add_filter('ifso_dki_types_extension',[$this,'extend_dki'],10,4);

            add_filter('ifso_geo_page_display_extra_tabs',[$this,'extend_geo_page_tabs']);
            add_action( 'admin_enqueue_scripts', [$this,'admin_enqueue_scripts']);

            add_filter('ifso_location_data_override',[$this,'apply_geo_override'],11);
            add_filter('ifso_location_data_override',[$this,'geo_override_with_browser_geolocation'],10);
            add_filter('ifso_exclude_from_geo',[$this,'block_geolocation_for_bots']);
            add_action('wp_enqueue_scripts',[$this,'public_enqueue_scripts']);
            add_shortcode('ifso_get_browser_location',[$this, 'request_browser_location']);
            add_action('wp_footer',[$this,'request_user_location_on_every_page']);
        }

        public function display_geo_analysis_page(){
            require_once IFSO_GEOLOCATION_ADDON_DIR . '/admin/markup/geo_analysis_tool_page_display.php';
        }

        private function output_log_file(){
            $logloc = Services\GeoRequestLogService::get_instance()->get_log_location();
            if (file_exists($logloc)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($logloc));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($logloc));
                ob_clean();
                flush();
                readfile($logloc);
                exit;
            }
        }

        public function extend_dki($ret,$type,$show,$atts){
            if($type==='geo'){
                if($show==='flag' || $show==='emoji-flag'){
                    if(method_exists(GeolocationService\GeolocationService::get_instance(),'get_user_location')){
                        $geo_data = GeolocationService\GeolocationService::get_instance()->get_user_location();
                        $width = !empty($atts['width']) ? esc_attr($atts['width']) : null;
                        $style_att = $width===null ? '' : "style='width:{$width};height:auto;'";
                        $extra_classnames = !empty($atts['classname']) ? $atts['classname'] : '';
                        if(!empty($geo_data->get('countryCode'))){
                            if($show==='flag')
                                return "<img class='ifso-country-flag {$extra_classnames}' {$style_att} src='{$this->get_ifso_country_flag_url($geo_data->get('countryCode'))}'>";
                            if($show==='emoji-flag'){
                                return $this->get_ifso_country_flag_emoji($geo_data->get('countryCode'));
                            }
                        }
                    }
                }
            }
        }

        public function get_ifso_country_flag_url($countyCode){
            return plugin_dir_url(__FILE__) . "assets/img/flags/{$countyCode}-flag.png";
        }

        public function get_ifso_country_flag_emoji($countryCode,$once=true){
            if($once)
                require_once IFSO_GEOLOCATION_ADDON_DIR . '/includes/coutries_flag_emojis.php';
            else
                require IFSO_GEOLOCATION_ADDON_DIR . '/includes/coutries_flag_emojis.php';
            if(!empty($flagEmojis[$countryCode]))
                return $flagEmojis[$countryCode];
        }

        public function extend_geo_page_tabs($tabs){
            $geoOverrideTab = ['name'=>"geoOverride",'prettyname'=>'Geolocation Override'];
            ob_start();
            include_once  IFSO_GEOLOCATION_ADDON_DIR . '/admin/markup/location-override/location_override_generator_page_display.php';
            $geoOverrideTab['content'] = ob_get_clean();
            $tabs[] = $geoOverrideTab;

            return $tabs;
        }

        public function admin_enqueue_scripts(){
            if(!empty($_GET['page']) && $_GET['page']==='wpcdd_admin_geo_license'){        //geo page
                wp_enqueue_style( 'IfsoShortcodeGeneratorCss', plugin_dir_url( __FILE__ ) . 'admin/assets/css/location-override-generator.css');
                wp_enqueue_script( 'IfsoGeoSortable', plugin_dir_url( __FILE__ ) . 'admin/assets/js/sortable.min.js' );
                wp_enqueue_script( 'IfsoGeoDragTable', plugin_dir_url( __FILE__ ) . 'admin/assets/js/drag-table.js', array('IfsoGeoSortable') );
                wp_enqueue_script('ifsoTemplateEditor',plugin_dir_url(__FILE__) . 'admin/assets/js/template-editor.js');   
                wp_enqueue_script( 'IfsoShortcodeGenerator', plugin_dir_url(IFSO_PLUGIN_MAIN_FILE_NAME ) . 'admin/js/shortcode-generator.js');
                wp_enqueue_script( 'IfsoGeoOverrideGen', plugin_dir_url( __FILE__ ) . 'admin/assets/js/location-override-generator.js', array('IfsoShortcodeGenerator', 'ifsoTemplateEditor', 'IfsoGeoDragTable'));
            }
        }

        public function public_enqueue_scripts(){
            $old_inline_eneueue = !function_exists('wp_add_inline_script');
            $vars_script = <<<SCR
            var geoOverrideCookieName = '{$this->geo_override_cookie_name}';
            var browserLocationCookieName = '{$this->browser_location_cookie_name}';
            var requestBrowserLocCookieName = '{$this->req_browser_loc_cookie_name}';
SCR;
            if($old_inline_eneueue) echo "<script>{$vars_script}</script>";

            wp_enqueue_script('if-so-geolocation',plugin_dir_url(IFSO_GEOLOCATION_FILE) .'assets/js/if-so-geolocation-public.js',array('jquery','if-so'));

            if(!$old_inline_eneueue) wp_add_inline_script('if-so-geolocation',$vars_script,'before');
        }

        private function get_cached_override_data($cname){
            return !empty($_COOKIE[$cname]) && json_decode(stripslashes($_COOKIE[$cname])) ? json_decode(stripslashes($_COOKIE[$cname]),true) : null;
        }

        public function apply_geo_override($override){
            $override_data = $this->get_cached_override_data($this->geo_override_cookie_name);
            if(is_array($override_data)) {
                return array_map('sanitize_text_field',$override_data);
            }
            return $override;
        }

        public function block_geolocation_for_bots($exclude){
            if(\IfSo\Services\PluginSettingsService\PluginSettingsService::get_instance()->extraOptions->geolocation['blockGeoBots']->get()){
                if(isset($_SERVER['HTTP_USER_AGENT'])){
                    $extra_blocked_bots = apply_filters('ifso_block_bots_extra_blocked_user_agents',[]);
                    $extra_blocked_bots_regex = !empty($extra_blocked_bots) ? '|' . implode('|',$extra_blocked_bots) : '';
                    if ( preg_match("/bot|crawl|slurp|spider|mediapartners|curl|wget{$extra_blocked_bots_regex}/i", $_SERVER['HTTP_USER_AGENT'])) {
                        return ['blockme'=>true];
                    }
                }
            }
            return $exclude;
        }

        public function request_browser_location($atts=[]){
            if((int)\IfSo\Services\PluginSettingsService\PluginSettingsService::get_instance()->extraOptions->geolocation['browserLocationMode']->get()===0) return;
            if(is_callable([\IfSo\Extensions\IFSOExtendedShortcodes\ExtendedShortcodes\ExtendedShortcodes::get_instance(),'is_edit_page_or_publish_action']) && \IfSo\Extensions\IFSOExtendedShortcodes\ExtendedShortcodes\ExtendedShortcodes::get_instance()->is_edit_page_or_publish_action()) return;
            $ignore_cache = !empty($atts['ignore_cache']) && strtolower(trim($atts['ignore_cache']))==='yes';
            $ignore_cache_att = $ignore_cache ? "ignore_cache='true'" : "";
            if(!empty($atts['request_via_coookie']) && $atts['request_via_coookie']!=='no' && class_exists('\IfSo\PublicFace\Helpers\CookieConsent'))
                return \IfSo\PublicFace\Helpers\CookieConsent::get_instance()->set_cookie($this->req_browser_loc_cookie_name,1,0,'/','preferences');
            return "<IfsoGetBrowserLocation {$ignore_cache_att} style='display:none;'></IfsoGetBrowserLocation>";
        }

        public function geo_override_with_browser_geolocation(){
            $cname = $this->browser_location_cookie_name;
            $cached_data = $this->get_cached_override_data($cname);
            if($cached_data!==null && isset($cached_data['is_browser_location']) && $cached_data['is_browser_location'])
                return $cached_data;
            if($cached_data!==null && isset($cached_data['lat']) && isset($cached_data['long'])){
                if($this->bad_geo_request) return;
                $res = wp_remote_get(GeolocationService\GeolocationService::get_instance()->web_service_url . "?action=get_coords_location&lat={$cached_data['lat']}&lng={$cached_data['long']}");
                if(is_array($res) && !empty($res['body']) && json_decode($res['body'])!==null){
                    $data = array_merge($cached_data,json_decode($res['body'],true));
                    if($data && $data['success']===true){
                        $data = array_map('sanitize_text_field',$data);
                        $data['is_browser_location'] = true;
                        if(class_exists('\IfSo\PublicFace\Helpers\CookieConsent'))
                            \IfSo\PublicFace\Helpers\CookieConsent::get_instance()->set_cookie($cname,json_encode($data),0,'/','preferences');
                        $_COOKIE[$cname] = json_encode($data);
                        $geoService = GeolocationService\GeolocationService::get_instance();
                        if(is_callable([$geoService,'send_session_to_localdb']) && is_callable([$geoService,'notifications_email'])) {
                            $geoService->send_session_to_localdb('lic'); //Locally tracking geo sessions
                            $geoService->notifications_email();//Check whether a quota notification needs to be sent and send one if yes
                        }
                        return  $data;
                    }
                }
                $this->bad_geo_request = true;
            }
            elseif((int) \IfSo\Services\PluginSettingsService\PluginSettingsService::get_instance()->extraOptions->geolocation['browserLocationMode']->get()>1)
                echo $this->request_browser_location(['request_via_coookie'=>true]);
        }

        public function request_user_location_on_every_page(){
            if((int) \IfSo\Services\PluginSettingsService\PluginSettingsService::get_instance()->extraOptions->geolocation['browserLocationMode']->get()>2)
                    echo $this->request_browser_location();
        }
    }
}
