=== WooCommerce Plivo SMS notifications ===
Contributors: siteoptimo, vdwijngaert
Donate link: http://www.siteoptimo.com/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: woocommerce, plivo, sms, notifications, support, update, cart, status, shipping, notification
Requires at least: 3.6
Tested up to: 4.0
Stable tag: 1.3.1

Allow your WooCommerce website to send SMS notifications to your customers.

== Description ==

This Plivo plugin for WordPress WooCommerce makes sending SMS status notifications easy. Your customers will love it.

Current features:

* Auto send status updates
* Send a test SMS
* Add opt-in/opt-out on checkout
* Edit status SMS notifications
* Send an SMS from the order page
* WPML compatible

= Extra customer trust =
Your customers will love it. Sending SMS notifications is a great way to let customers know the current order status and will increase positive reviews.
Get what the big boys are using and add a more physical dimension to the shop.

= Powered by Plivo =
[Plivo](http://www.plivo.com) is an awesome cloud based API platform for building Voice and SMS enabled applications. We make use of the Plivo API to send all the messages.
Support for over 200 countries and competitive pricing makes Plivo a nobrainer. Prices starting at $0,0065 per text!
Get started now by creating your [free trial account](https://manage.plivo.com/accounts/register/?utm_source=plivo-plugin&utm_medium=wordpress&utm_campaign=siteoptimo).

= WPML compatible =
This plugin is WPML compatible. Multilingual shops can now auto send status notifications in the language of the customer.

= Requirements =
To make use of the Plivo API, this plugin requires php-openssl and php-curl to be installed. Obviously, you'll also need a Plivo account.

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

= 1.3.1 =
* Better version numbering.
* Translation fixes.

= 1.3 =
* Translation update
* Fixed compatibility with WooCommerce 2.2.

= 1.2 =
* Added full WPML support

= 1.1 =
* Added fallback for the HTTP_Request2 library.
* Added feature: opt-in/opt-out message to checkout.
* Fixed bug where you could only send a test SMS after the Plivo credentials where saved.

= 1.0 =
* First version of the plugin.