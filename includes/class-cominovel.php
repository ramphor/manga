<?php
/**
 * The main Cominovel plugin file
 *
 * @package Cominovel
 * @author  Puleeno Nguyen <puleeno@gmail.com>
 */

use Ramphor\User\Profile as UserProfile;
use Ramphor\User\LoginStyle\Enum as LoginStyle;

if ( ! class_exists( 'Cominovel' ) ) {
	/**
	 * Class Cominovel is the main class of Cominovel plugin
	 */
	class Cominovel {
		/**
		 * The instance of class Cominovel.
		 *
		 * @var Cominovel
		 */
		protected static $instance;

		/**
		 * Get Ramphor comic instance or create if not exists.
		 *
		 * @return  Cominovel  The instance of Cominovel class.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->define_constants();
			$this->includes();
			$this->hooks();
		}

		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		public function define_constants() {
			$this->define( 'COMINOVEL_ABSPATH', plugin_dir_path( COMINOVEL_PLUGIN_FILE ) );
			$this->define( 'COMINOVEL_TEMPLATES_DIR', sprintf( '%s/templates', COMINOVEL_ABSPATH ) );
		}

		public function includes() {
			/**
			 * Interfaces
			 */

			/**
			 * Abstract classes
			 */

			/**
			 * Core classses
			 */
			require_once COMINOVEL_ABSPATH . 'includes/cominovel-core-functions.php';
			require_once COMINOVEL_ABSPATH . 'includes/class-cominovel-post-types.php';
			require_once COMINOVEL_ABSPATH . 'includes/class-cominovel-install.php';
			require_once COMINOVEL_ABSPATH . 'includes/class-cominovel-query.php';

			/**
			 * Load libraries via composer
			 */
			$composer = COMINOVEL_ABSPATH . 'vendor/autoload.php';
			if ( file_exists( $composer ) ) {
				require_once $composer;
			}

			require_once COMINOVEL_ABSPATH . 'includes/abstracts/class-cominovel-data.php';
			require_once COMINOVEL_ABSPATH . 'includes/class-cominovel-comic.php';
			require_once COMINOVEL_ABSPATH . 'includes/class-cominovel-novel.php';

			if ( $this->is_request( 'admin' ) ) {
				require_once COMINOVEL_ABSPATH . 'includes/admin/class-cominovel-admin.php';
			}

			if ( $this->is_request( 'frontend' ) ) {
				$this->frontend_includes();
			}

			$this->theme_support_includes();
			if ( class_exists( 'Cominovel_Query' ) ) {
				$this->query = new Cominovel_Query();
			}
		}

		/**
		 * What type of request is this?
		 *
		 * @param  string $type admin, ajax, cron or frontend.
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
			}
		}

		public function is_rest_api_request() {
			if ( empty( $_SERVER['REQUEST_URI'] ) ) {
				return false;
			}
			$rest_prefix         = trailingslashit( rest_get_url_prefix() );
			$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return apply_filters( 'cominovel_is_rest_api_request', $is_rest_api_request );
		}

		public function hooks() {
			register_activation_hook( COMINOVEL_PLUGIN_FILE, array( Cominovel_Install::class, 'active' ) );
			add_action( 'init', array( $this, 'init' ) );
		}

		public function frontend_includes() {
			require_once COMINOVEL_ABSPATH . 'includes/class-cominovel-frontend.php';
			require_once COMINOVEL_ABSPATH . 'includes/class-cominovel-template.php';
			require_once COMINOVEL_ABSPATH . 'includes/class-cominovel-seo.php';
		}

		private function theme_support_includes() {
		}

		public function init() {
			do_action( 'before_cominovel_init' );

			$this->load_plugin_textdomain();

			if ( class_exists( UserProfile::class ) ) {
				UserProfile::init(
					array(
						'templates_location' => sprintf( '%s/templates/users', COMINOVEL_ABSPATH ),
						'login_styles'       => array(
							LoginStyle::LOGIN_STYLE_WORDPRESS_NATIVE,
							LoginStyle::LOGIN_STYLE_POPUP_MODAL,
						),
					)
				);
			}
		}

		public function load_plugin_textdomain() {
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'cominovel' );
			unload_textdomain( 'cominovel' );
			load_textdomain( 'cominovel', WP_LANG_DIR . '/cominovel/cominovel-' . $locale . '.mo' );
			load_plugin_textdomain( 'cominovel', false, plugin_basename( dirname( COMINOVEL_PLUGIN_FILE ) ) . '/languages' );
		}

		public function plugin_url( $path = '/' ) {
			return untrailingslashit( plugins_url( $path, COMINOVEL_PLUGIN_FILE ) );
		}
	}
}