=== WooCommerce Plivo SMS notifications ===
Contributors: siteoptimo
Donate link: http://www.siteoptimo.com/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: woocommerce, plivo, sms, notifications, support, update, cart, status, shipping, notification
Requires at least: 3.6
Tested up to: 4.0
Stable tag: 1.1

Allow your WooCommerce website to send SMS notifications to your customers.

== Description ==

This plugin for WordPress WooCommerce enables to automatically send SMS status notifications to your customers.

Current features:

* Auto send status updates
* Send a test SMS
* Add opt-in/opt-out on checkout
* Edit status SMS notifications
* Send an SMS from the order page

= Powered by Plivo =
[Plivo](http://www.plivo.com) is an awesome cloud based API platform for building Voice and SMS enabled applications. We make use of the Plivo API to send all the messages.
Support for over 200 countries and competitive pricing makes Plivo a nobrainer.
Get started now by creating your [free trial account](https://manage.plivo.com/accounts/register/?utm_source=plivo-plugin&utm_medium=wordpress&utm_campaign=siteoptimo).

= Requirements =
To make use of the Plivo API, this plugin requires php-openssl and php-curl to be installed. Obviously, you'll also need a Plivo account.

Version 1.1 note: we have written a fallback for the required [pear package HTTP_Request2](https://github.com/pear/HTTP_Request2). So from now on, it's no longer required, but still recommended.


= About the authors & support =
This plugin is written by the brave and handsome coders of [SiteOptimo](http://www.siteoptimo.com/?utm_source=plivo-plugin&utm_medium=wordpress&utm_campaign=wcp).
We made it freely available for the WordPress and WooCommerce community. We might build more custom work in the future.

Issues can be reported in our [GitHub Repository](https://github.com/siteoptimo/woocommerce-plivo/issues).

== Installation ==

1. Upload the `woocommerce-plivo` folder to the `/wp-content/plugins/` directory.
1. Activate the Woocommerce Plivo plugin through the 'Plugins' menu in WordPress.
1. Go to the SMS settings tab in your WooCommerce settings.
1. Fill in the number, Auth ID and Auth Token provided by Plivo.

== Frequently Asked Questions ==

*It's broken!

Have you checked the requirements? If yes, let us know and we'll be fixing it soon.

*I don't like your plugin.

You're free to use an other WooCommerce Plivo plugin. Oh right, there is no. Another SMS provider then.

*Why did you make this plugin?

We needed something like this for one of our clients. Now we're giving it back to the community.

*I want the plugin to do X. Can you change it?

You're free to make feature suggestions, but if it's custom work you want, contact us.

== Screenshots ==

1. Save your Plivo settings in the new WooCommerce SMS tab.

2. Define the messages you want to have sent automatically and give start customizing the messages.

3. Your order notification changes are now sent automatically. You can send additional messages too. The sky is the limit.

== Changelog ==

= 1.1 =
* Added fallback for the HTTP_Request2 library.
* Added feature: opt-in/opt-out message to checkout.
* Fixed bug where you could only send a test SMS after the Plivo credentials where saved.

= 1.0 =
* First version of the plugin.