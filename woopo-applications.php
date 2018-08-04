<?php
class Woopo_Application {

	/**
	 * @var Singleton The reference the *Singleton* instance of this class
	 */
	private static $instance;

	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @return Singleton The *Singleton* instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'init', array( $this, 'actions_in_init' ) );
	}

	public function actions_in_init() {
		if( current_user_can('administrator' ) ) return;
	}
}

Woopo_Application::get_instance();