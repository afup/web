<?php
/**
 * Abstract class that all modules should implement.
 *
 * @package Inc/Core/Abstract
 */

/**
 * Class Hestia_Abstract_Module
 */
abstract class Hestia_Abstract_Module {

	/**
	 * Classes to load.
	 *
	 * @var array
	 */
	protected $classes_to_load = array();

	/**
	 * Check if module should load.
	 *
	 * @return bool
	 */
	abstract protected function should_load();

	/**
	 * Run module's functions.
	 *
	 * @return void
	 */
	abstract function run_module();

	/**
	 * Register customizer classes.
	 */
	private function register_customizer_classes() {
		if ( empty( $this->classes_to_load ) ) {
			return false;
		}

		foreach ( $this->classes_to_load as $class_name => $class_path ) {
			$filename  = 'class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';
			$full_path = trailingslashit( $class_path ) . $filename;
			if ( is_file( $full_path ) ) {
				require $full_path;
				$instance = new $class_name();
				if ( method_exists( $instance, 'init' ) ) {
					$instance->init();
				}
			}
		}

		return true;
	}

	/**
	 * Init module function.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! $this->should_load() ) {
			return;
		}
		$this->register_customizer_classes();
		$this->run_module();
	}
}
