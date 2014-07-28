WooCommerce Plivo Plugin
========================
This plugin for WordPress WooCommerce enables the use of the popular platform for building voice and SMS enabled applications, [Plivo](http://www.plivo.com/?utm_source=plivo-plugin&utm_medium=github&utm_campaign=siteoptimo). We aim to integrate most of the features the Plivo API has to offer, but for now, we'll stick to implementing SMS order notifications.

Current features:
* Auto send status updates
* Send a test SMS
* Add opt-in/opt-out on checkout
* Edit status SMS notifications
* Send an SMS from the order page

Powered by Plivo
----------------
Plivo is an awesome cloud based API platform for building Voice and SMS enabled applications. We make use of the Plivo API to send all the messages.
Support for over 200 countries and competitive pricing makes Plivo a nobrainer.
Get started now by creating your [free trial account](https://manage.plivo.com/accounts/register/?utm_source=plivo-plugin&utm_medium=wordpress&utm_campaign=siteoptimo).


Requirements
------------
To make use of the Plivo API, this plugin requires php-openssl, php-curl to be installed. Obviously, you'll need a Plivo account.

Version 1.1 note: we have written a fallback for the required [pear package HTTP_Request2](http://pear.php.net/package/HTTP_Request2). So from now on, it's no longer required, but still recommended.

Installation
------------
Simply download the plugin from the WordPress plugin repository, or download the current working version from Github.

About the authors & support
---------------------------
This plugin is written by the brave and handsome coders of [SiteOptimo](http://www.siteoptimo.com/?utm_source=plivo-plugin&utm_medium=github&utm_campaign=wcp).
We made it freely available for the WordPress and WooCommerce community. We might build more custom work in the future.

Issues
------
If you find an issue or if you have an suggestion for an improvement, [please let us know](https://github.com/siteoptimo/woocommerce-plivo/issues/new)!
