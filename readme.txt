=== Password Protection ===
Contributors: c3mdigital
Donate link: http://www.redcross.org/charitable-donations
Tags: security, password protection, brute force blocking
Requires at least: 3.5.1
Tested up to: 3.6 beta
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds HTTP Basic Authentication as a secondary defense layer for wp-admin and to prevent brute force attack attempts.  Also includes an option to block access to wp-admin from users (bots) with No-Referrer Headers.

== Description ==

This plugin helps prevent annoyance from multiple brute force login attempts to your site.  It does this by adding an additional authentication method.  Once you enable the plugin
and enter a username and password ( please use a different username and password than your WordPress admin account ). Any user or bot that attempts to access wp-admin or your login page
will be required to successfully enter the additional authorization details before allowed access to the WordPress login page.  You can also set your login page to not allow direct access
without a valid referrer header from your site. Please Note: No security plugin will provide 100% protection from hackers.  This plugin simply makes it harder for them to gain access using
automated techniques.  Please remember to ALWAYS KEEP UP TO DATE BACKUPS and use STRONG PASSWORDS!!

PLEASE NOTE: Very Limited support will be offered for this plugin but it will be kept up to date and any bugs can be reported on the github page.

== Installation ==

1. Upload the `password-protection` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the settings page and enter a username and password to be used as the secondary authorization

== Frequently Asked Questions ==

= Will this plugin keep my site from being hacked? =

NO! No plugin can keep your site from being hacked but this plugin will stop annoying brute force attempts to your login page.

= What is a No-Referrer Request? =

A No-Referrer Request is a direct request made to your wp-login.php file.  Normally when you go to wp-admin and you are not logged in WordPress will redirect you to wp-login.php.  When this happens the referrer is from your same domain.  Bots and automated scripts normally make direct post requests to wp-login.php without a referrer.  This plugin can block all requests without a referrer or requests from a referrer that is not from your domain.

= What if I forget my Password? =

If you forget your password there is no way to recover it because it is stored as an encrypted hash.  If you forget your password you will have to disable the plugin by changing the name of the password-protection using FTP.  Once disabled and you log in you can then re activate the plugin and enter a new password on the settings page.

== Screenshots ==

1. HTTP Authentication on Chrome, your browser my not look the same but it should be similar.
2. The admin interface.

== Changelog ==

= 1.0 =
* Initial Version

== Upgrade Notice ==

= 1.0 =
* You just installed it and don't need to upgrade
