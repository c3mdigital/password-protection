password-protection
===================
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


== Changelog ==

= 1.0 =
* Initial Version

== Upgrade Notice ==

= 1.0 =
* You just installed it and don't need to upgrade
