<?php
/**
 * Plugin Name: Password Protection
 * Plugin URI: http://x-team.com
 * Description: Adds HTTP Basic Authentication as a secondary defense layer for wp-admin and to prevent brute force attack attempts
 * Author: Chris Olbekson <chris@x-team.com>
 * Version: 1.0.1
 * Author URI: http://x-team.com
 * Contributor: Weston Rutor <weston@x-team.com>
 */

/*
 Copyright 2013 Chris Olbekson (email: chris@x-team.com) 
 HTTP Authentication methods adpated from WPized Password Protection
 by: Weston Rutor X-Team BOOM!
 
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 Please see license.txt for the full license.
 */

class WPized_Password_Protect {

	/**
	 * @var string $meta_cap Capability needed to access plugin settings
	 * @access private
	 */
	private static $meta_cap = 'manage_options';

	/**
	 * @var string $password_page_key Page slug used in add_options_page
	 * @access private
	 */
	private static $password_page_key = 'password_protection';

	/**
	 * @var string $password_page_name Admin Page title
	 * @access private
	 */
	private static $password_page_name = 'Password Protection';

	/**
	 * @var string $settings_key Option Name value for saving and retrieving plugin settings
	 * @access private
	 */
	private static $settings_key = 'wpized_password_protect_settings';

	/**
	 * @var string $settings_field Settings Field name used in add_settings_field
	 * @access private
	 */
	private static $settings_field = 'wpized_password_protect';

	/**
	 * @var string $settings_name Settings Name used as a label in add_settings_section
	 * @access public
	 */
	private static $settings_name = 'Enter a Username and Password to Enable Password Protection';

	/**
	 * @var string $password_page_hook hook slug assigned to plugin settings page
	 * @access public
	 */
	public static $password_page_hook;

	static $plugin_version = '1.0.1';

	/**
	 * Class constructor
	 *  Add our actions hooks here
	 */
	function __construct() {
		self::run_update();
		add_action( 'admin_menu', array( __CLASS__, 'admin_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'parse_request', array( __CLASS__, 'http_authenticate' ) );
		add_action( 'init', array( __CLASS__, 'http_authenticate' ), 100 );
	}

	/**
	 * Plugin update to delete current saved password hash to comply with new
	 *  password hashing added in 1.0.1
	 */
	static function run_update() {
		$settings = get_option( self::$settings_key );
		if ( ! empty( $settings ) && ! isset( $settings['plugin_version'] ) ) {
			$settings['plugin_version'] = self::$plugin_version;
			$settings['password'] = false;
			update_option( self::$settings_key, $settings );
		}
	}

	/**
	 * Adds the settings page to the admin menu
	 * @uses add_options_page()
	 * @hook add_action, hook name: admin_menu
	 */
	static function admin_settings_page() {
		self::$password_page_hook = add_options_page(
			self::$password_page_name,
			self::$password_page_name,
			self::$meta_cap,
			self::$password_page_key,
			array( __CLASS__, 'password_settings_page' )
		);
	}

	/**
	 * Registers our plugin settings
	 * @uses register_setting()
	 * @uses add_settings_section()
	 * @hook add_action, hook name: admin_init
	 */
	static function register_settings() {
		register_setting(
			self::$settings_field,
			self::$settings_key,
			array( __CLASS__, 'validate_settings' )
		);
		add_settings_section(
			self::$password_page_key,
			self::$settings_name,
			array( __CLASS__, 'settings_html' ),
			self::$password_page_key
		);
	}

	/**
	 * Output the settings page html, called by add_settings_section()
	 */
	static function settings_html() {
		$settings = get_option( self::$settings_key );
		if ( ! isset( $settings['referrer_block'] ) )
			$settings['referrer_block'] = 'off';
		?>
	    <p>
	        <label for="pp-username">Username</label>
			<input type="text" name="<?php echo esc_attr( self::$settings_key.'[username]' ) ?>" id="pp-username" value="<?php echo esc_attr( $settings['username'] ) ?>" />
		</p>
		<p>
	        <label for="pp-password">Password</label>
			<input type="password" name="<?php echo esc_attr( self::$settings_key.'[password]' ) ?>" id="pp-password" value="" autocomplete="off" />
			<span class="description"> If you would like to change the password type a new one. Otherwise leave this blank.</span>
		</p>
		<p>
			<label for="pp-referrer_block">Block No-Referrer Requests</label>
			<input name="<?php echo esc_attr( self::$settings_key.'[referrer_block]' ) ?>" type="checkbox" id="pp-referrer_block" value="on" <?php checked( 'on', $settings['referrer_block'] ); ?> />
			<span class="description">When this is checked direct requests to your login page will be blocked.  This will prevent most bot net brute force attacks from ever hitting your login page </span>
		</p>
		<?php
		submit_button( 'Save Changes', 'primary' );
	}

	/**
	 * Validates our settings fields before they are saved to the database
	 *  The password is encrypted using sha256 before being saved to the database
	 *
	 * @param array $settings The user entered form data
	 *
	 * @return mixed
	 */
	static function validate_settings( $settings ) {
		$saved_settings = get_option( self::$settings_key );
		if ( ! empty( $settings['password'] ) )
			$settings['password'] = wp_hash_password( $settings['password'] );
		else
			$settings['password'] = $saved_settings['password'];

		return $settings;
	}

	/**
	 * Builds our admin settings page
	 *
	 * @uses wp_nonce_field()
	 * @uses settings_fields()
	 * @uses do_settings_sections()
	 */
	static function password_settings_page() {
		?>
		<div class="wrap">
			<?php wp_nonce_field( self::$password_page_key );
				if ( ! current_user_can( self::$meta_cap ) )
					wp_die( 'You do not have authorization to access this page' );
			?>
			<div class="inner-wrap">
				<div id="icon-options-general" class="icon32"><br></div>
					<h2><?php echo esc_html( self::$password_page_name )  ?></h2>
					<form method="post" action="options.php">
						<h3>Password Protection adds an additional layer of security to your login page and WordPress Admin.</h3>
						<p> Once you set a Username and Password you will be required
		                    to enter it before you are allowed to access the login page or WordPress dashboard.  If you are logged in to WordPress you will bypass the additional authentication.
		                    This will prevent most types of brute force attacks from even making it your login page.
						</p>
						<p>
							<strong>Note: There is no way to retrieve your password if you misplace or forget it.</strong><br/>
		                    You can however disable this plugin by renaming or deleting the password-protection folder via FTP in wp-content/plugins.
	                    </p>
						<?php
						settings_fields( self::$settings_field );
						do_settings_sections( self::$password_page_key );
						?>
					</form>
			</div><!-- ./inner-wrap -->
			<div class="clear"></div>
		</div><!-- ./wrap -->
		<?php
	}

	/**
	 * Sends the not authorized response headers and prevents non authorized
	 *  users from attempting to login or access wp-admin
	 */
	static function not_authorized() {
		header( 'HTTP/1.1 403 Not Authorized' );
		header( 'WWW-Authenticate: Basic realm="Protected Admin"' );
		print "You do not have permission to access this site.";
		exit;
	}

	/**
	 * Our authentication function
	 *  When certain conditions are met auth checking is skipped (required for core WordPress functionality)
	 */
	static function http_authenticate() {
		// Skip checks if user is currently logged in
		if ( is_user_logged_in() )
			return;

		// Make sure we never cache protected pages
		header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );

		// Skip checks if we're logged into WordPress already or not in admin
		$login_path = parse_url( wp_login_url(), PHP_URL_PATH );
		$request_path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
		if ( ! is_admin() && $login_path != $request_path )
			return;

		// Only run this if we have a user and pass provided
		$settings = get_option( self::$settings_key, array() );
		if ( empty( $settings ) || empty( $settings['username'] )  || empty( $settings['password'] ) )
			return;

		if ( isset( $settings['referrer_block'] ) && 'off' != $settings['referrer_block'] ) {
			if ( $_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'] )
				self::not_authorized();
		}

		// Allows this to be overridden in other plugins or theme
		if ( apply_filters( 'wpized_password_protect_enabled', true ) === false )
			return;

		// Skip checks if this is an ajax request
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return;

		// Skip checks if this is a WP_Cron request
		if ( defined( 'DOING_CRON' ) && DOING_CRON )
			return;

		// Make a special accomodation for SWF Upload
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['REQUEST_URI'] == '/wp-admin/async-upload.php' )
			return;

		$is_not_authenticated = (
			empty( $_SERVER['PHP_AUTH_USER'] ) ||
			empty( $_SERVER['PHP_AUTH_PW'] ) ||
			$_SERVER['PHP_AUTH_USER'] != $settings['username'] ||
			!wp_check_password( $_SERVER['PHP_AUTH_PW'], $settings['password'] )
		);

		if ( $is_not_authenticated )
			self::not_authorized();
	}
}

new WPized_Password_Protect();
 
