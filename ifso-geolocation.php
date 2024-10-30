<?php
/*
Plugin Name: If-So Geolocation
Description: Set up location-based content in minutes. No coding required. Works with any page builder. No need to sync an IP-to-location database.
Version: 1.4.1
Author: If So Plugin
Author URI: http://www.if-so.com/
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

@author: Nick Martianov
*/

if(!defined('WPINC')){
    die();
}

if(!defined('IFSO_GEOLOCATION_ON')){
    define('IFSO_GEOLOCATION_ON',true);
    define('IFSO_GEOLOCATION_FILE',__FILE__);
    define('IFSO_GEOLOCATION_ADDON_DIR',__DIR__);

    add_action( 'plugins_loaded', function(){
        if(defined('IFSO_PLUGIN_BASE_DIR') && IFSO_GEOLOCATION_ON){
            require_once(__DIR__ . '/ifso-geolocation.class.php');
            if(class_exists('\IfSo\Addons\Geolocation\GeolocationExtension'))
                $ext = new IfSo\Addons\Base\Extension('\IfSo\Addons\Geolocation\GeolocationExtension');
            return;
        }
        add_action( 'admin_notices', function(){
            ?>
            <div class="notice notice-error ifso-special-error">
                <p>The If-So Geolocation Extension requires the core If-So (free version) to be installed on your site. <a href="https://wordpress.org/plugins/if-so/" target="_blank">Free Download</a></p>
            </div>
            <?php
        });
    });
}


