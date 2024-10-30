=== If-So Geolocation===
Contributors: ifso
Donate link: https://www.if-so.com/location-based-content-wordpress-plugin/?utm_source=WordPressGeo&utm_medium=Readme&utm_campaign=Donate%20link
Tags: Geolocation, location-based content, geoIP, geolocation redirect, Geotargeting
Requires at least: 4.0.1
Tested up to: 6.6
Stable tag: 1.4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

All-in-one geolocation. Personalized content, geolocation Dynamic Keyword Insertion shortcodes, Rediects, and more. No coding required!

== Description ==

Create location-based content in minutes. No coding required. Works with any page builder. No need to sync an IP-to-location database.

This plugin enhances the Geolocation capabilities of the core <a href="https://wordpress.org/plugins/if-so/" target="_blank">If-So Dynamic Content plugin</a>:

* HTML5 Geolocation API (browser location) - Can be used in addition to or instead of the IP-to-location service.
* Location override - Allow users to manually select their location and override the default IP-based location detection.
* Display the user's country flag (image or emoji).
* Log geolocation requests and analyze the log to find and exclude bot traffic.





== WHY IF-SO? ==

* Simple to use - No coding is required
* No need to sync with an IP-to-location database
* Show dynamic content based on City, State, Country, Continent, or Time zone
* Works with any page builder
* 100% compatible with all caching plugins (no need to disable the cache)
* A simple way to improve engagement and conversion rates
* Conditional Gutenberg Blocks and Elementor Widgets
* Create location-based pop-ups
* Built-in stats system

.....................................................................

== HOW IT WORKS ==

**Select a condition >> Set a version of content to be displayed if it is met.**


= All page builder users =

1. Create a trigger.
2. Select a condition and set the personalized content version.
3. Optional - create more dynamic versions and set the default content.
4. Paste the shortcode wherever you want to display the content.
Whenever a page with the shortcode is loaded, one of the content versions will be displayed accordingly.

<a href="https://www.if-so.com/help/documentation/how-to-create-dynamic-content-trigger/?utm_source=WordPressGeo&utm_medium=Readme&utm_campaign=v2&utm_term=dynamic-trigger" target="_blank">Learn more >></a>

= Gutenberg and Elementor users =

1. Select the block or element.
2. On the side menu, select the condition to display the block/element.

<a href="https://www.if-so.com/elementor-personalization/?utm_source=WordPressGeo&utm_medium=Readme&utm_campaign=v2&utm_term=elementor" target="_blank"> - More about conditional  Elementor Elements >></a>
<a href="https://www.if-so.com/conditional-gutenberg-blocks/?utm_source=WordPressGeo&utm_medium=Readme&utm_campaign=v2&utm_term=gutenberg" target="_blank"> - More about conditional  Gutenberg Blocks >></a>


.....................................................................


== HTML5 Geolocation API (browser location) ==

The Geolocation API-based content option is an alternative method for presenting location-specific content. This approach is significantly more accurate in determining the user’s location compared to the IP-to-location method (which is not crucial at the country and state level, but might be significant at the city level).

The drawback of this approach is that it necessitates the user’s consent to share their location.

The HTML5 Geolocation API method can be used in addition to or instead of the IP-to-location service.

<a href="https://www.if-so.com/the-html-geolocation-api/?utm_source=WordPress&utm_medium=Readme&utm_campaign=v2&utm_term=html5_api" target="_blank">- More about the HTML5 Geolocation API.</a>


== LOCATION OVERRIDE (MANUAL USER LOCATION SELECTION) ==

With the manual user location selection option, users can override the location detected by our IP-to-location service and manually select a different location. This allows for dynamic content to be displayed based on the user's chosen location.

<a href="https://www.if-so.com/dynamic-select-form/manual-user-location-selection/" target="_blank"> - More about the Location Override option >></a>


== LOG GEOLOCATION REQUESTS ==

Track bots that visit your site and exhaust your session quota. Log the geolocation requests to identify their IPs, analyze them,  and chose if you want to block them from the geolocation service. 
Although the site will remain open to visits from the blocked IPs, the geolocation service will be disabled for them and default content will be displayed instead of the location-based version.



<a href="https://www.if-so.com/faq-items/the-geolocation-session-count-doesnt-seem-to-behave-as-expected/" target="_blank"> - More about logging geolocation requests >></a>
.....................................................................

== Use cases and examples ==

* Display the user’s country flag
* Highlight different products in specific locations
* Show reviews in the visitor’s language
* Display the time of an event in the user’s time zone (auto-local time display)
* Set up a conditional redirect
* Redirect all .com pages to corresponding pages on the local version of the site
* Allow users to manually set their location (location override)
 

<a href="https://www.if-so.com/dynamic-content/examples/?category_filter%5B%5D=%7B%22cat%22%3A564%2C%22subcat%22%3A565%7D&category_filter%5B%5D=%7B%22cat%22%3A517%2C%22subcat%22%3A518%7D&apply_filter=" target="_blank">More usage examples >></a>



.....................................................................

== Built-in stats ==

Get clear insights into your content performance! See real-time results with a built-in analytics system, like how many times each version was displayed and how it affected the conversion rate.

.....................................................................


== DYNAMIC KEYWORD INSERTION (DKI) ==

The Dynamic Keyword Insertion (DKI) option allows you to display values using shortcodes:
Displaying the user's country:



**Insert the user’s country:**
`
[ifsoDKI type='geo' show='country' fallback='' ajax='yes']
`


**Insert the user’s state:**
`
[ifsoDKI type='geo' show='state' fallback='' ajax='yes']
`

**Insert the user’s city:** 
`
[ifsoDKI type='geo' show='city' fallback='' ajax='yes']
`

**Insert the user’s continent:**
`
[ifsoDKI type='geo' show='continent' fallback='' ajax='yes']
`

**Insert the user’s time zone:**
`
[ifsoDKI type='geo' show='timezone' fallback='' ajax='yes']
`

**Insert the user’s country flag (image):**
`
[ifsoDKI ajax='yes' type='geo' show='flag' width='50px']
`

**Insert the user’s country flag (emoji):**
`
[ifsoDKI type='geo' show='emoji-flag']
`




<a href="https://www.if-so.com/geolocation-dki/?utm_source=WordPressGeo&utm_medium=Readme&utm_campaign=v2&utm_term=geo-dki" target="_blank">More Geolocation DKI shortcode options >></a>



**The Auto-Local Time Display shortcode**
Display the event time auto-adjusted to the user's time zone.
`
[ifsoDKI type='time' show='user-geo-timezone-sensitive' time='04/25/2024 08:00' format='n/j/o, G:i']
`

<a href="https://www.if-so.com/auto-local-time-display/?utm_source=WordPressGeo&utm_medium=Readme&utm_campaign=v2&utm_term=local-time-display-dki" target="_blank">More about the Auto-Local Time Display Shortcode >></a>





== Installation ==

1. Make sure you have the <a href="https://wordpress.org/plugins/if-so/">core If-So plugin </a>installed on your site
1. Go to your WordPress Control Panel
1. Click "Plugins", then "Add New"
1. Enter "If-So Geolocation" as a search term and click "Search Plugins"
1. Download and install the plugin
1. Click the "Activate Plugin" link



== Frequently Asked Questions ==

= Does If-So work with any page builder? =
Yes, If-So works on every website, regardless of the page builder you are using.


= What content can be customized with If-So? =
If-So allows you to customize any element on the website, including titles, texts, images, videos, menu items, and design.


== Screenshots ==
1. Location-based content using a trigger - works with all page builder users
2. Location-based Elementor Elements
3. Location-based Gutenberg Blocks
4. Built-in stats
5. Geolocation Dynamic Keyword Insertion (DKI) shortcodes
6. User location selection (location override)
7. Compatible with caching plugins
8. Dynamic trigger 



== Changelog ==

= 1.4 =
* WordPress 6.5 compatibility
* Bug fixes

= 1.3 =
* WordPress 6.4 compatibility

= 1.2=
* Added the option to "block bots" (can be activated in the settings) (learn more).
* The geolocation request log and analyzer tool now include user-agent data, making it easier to detect and block bots. User agents can be blocked by passing an array of user-agent values to the filter "ifso_block_bots_extra_blocked_user_agents" (User-agent will be blocked if its text contains the value)
* Flag DKI Shortcode: Added a "classname" attribute, allowing users to add classes to the rendered element.
Ex. [ifsoDKI type='geo' show='flag' width='50px' classname='class-you-choose']
* Location override improvements: Added the option to display flags near the country labels in a selection form.
* Location override improvements: Country DKI shortcode is now affected by the override functionality.
* Location override page caching compatibility: The form can now be loaded using Ajax.
* Added the ability to whitelist users from the geolocation “Block bots” mode based on IP, cookie, or by using the geo service in the same way blacklisting is done, but with the "ifso_geo_whitelist" filter instead.
* Allow blocking users from the geolocation service based on the results of custom functions (user role, login status).
* UI improvements.
* Bug fixes.



= 1.1=
* Added a location override finctionlaity 
* Added a location override selection form generator



= 1.0=
* User country flag image DKI  - use the shortcode Display the user's country flag using 
* Log all geolocation requests.
* Auto-analyze the geolocation log to find bots (IPs that belong to data centers that used the service more than 15 times)



== Upgrade Notice == 
The extensions requires the core If-So plugin in order to work