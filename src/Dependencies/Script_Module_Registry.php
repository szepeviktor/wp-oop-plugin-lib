<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies\Script_Module_Registry
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Dependencies;

/**
 * Class for a registry of script modules.
 *
 * @since n.e.x.t
 */
class Script_Module_Registry extends Abstract_Dependency_Registry {

	/**
	 * Registers a script module with the given handle and arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @param string               $key  Script module handle.
	 * @param array<string, mixed> $args {
	 *     Script module registration arguments.
	 *
	 *     @type string|false      $src       Full URL of the script module. Providing one is required. Default false.
	 *     @type array             $deps      An array of registered script module handles this module depends on.
	 *                                        Default empty array.
	 *     @type string|false|null $ver       String specifying module version number, if it has one, which is added
	 *                                        to the URL as a query string for cache busting purposes. If set to false,
	 *                                        the current WordPress version number is automatically added. If set to
	 *                                        null, no version is added. Default false.
	 *     @type string            $manifest  Full path of a PHP file which returns arguments for the script module,
	 *                                        such as the '*.asset.php' files generated by the '@wordpress/scripts'
	 *                                        package. If provided, the returned arguments will be used to register the
	 *                                        script module. Default empty string (none).
	 * }
	 * @return bool True on success, false on failure.
	 */
	public function register( string $key, array $args ): bool {
		if ( ! $this->support_check( __METHOD__, 'wp_register_script_module' ) ) {
			return false;
		}

		$args = $this->parse_args( $args );

		wp_register_script_module(
			$key,
			$args['src'],
			$args['deps'],
			$args['ver']
		);
		return true;
	}

	/**
	 * Checks whether a script module with the given handle is registered.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Script module handle.
	 * @return bool True if the script module is registered, false otherwise.
	 */
	public function is_registered( string $key ): bool {
		$this->support_check( __METHOD__ );
		return false;
	}

	/**
	 * Gets the registered script module for the given handle from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Script module handle.
	 * @return object|null The registered module definition, or `null` if not registered.
	 */
	public function get_registered( string $key ) {
		$this->support_check( __METHOD__ );
		return null;
	}

	/**
	 * Gets all script modules from the registry.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, object> Associative array of handles and their module definitions, or empty array if
	 *                               nothing is registered.
	 */
	public function get_all_registered(): array {
		$this->support_check( __METHOD__ );
		return array();
	}

	/**
	 * Enqueues the script module with the given handle.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Script module handle.
	 */
	public function enqueue( string $key ): void {
		if ( ! $this->support_check( __METHOD__, 'wp_enqueue_script_module' ) ) {
			return;
		}

		wp_enqueue_script_module( $key );
	}

	/**
	 * Dequeues the script module with the given handle.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Script module handle.
	 */
	public function dequeue( string $key ): void {
		if ( ! $this->support_check( __METHOD__, 'wp_dequeue_script_module' ) ) {
			return;
		}

		wp_dequeue_script_module( $key );
	}

	/**
	 * Checks whether the script module with the given handle is enqueued.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $key Script module handle.
	 * @return bool True if the script module is enqueued, false otherwise.
	 */
	public function is_enqueued( string $key ): bool {
		$this->support_check( __METHOD__ );
		return false;
	}

	/**
	 * Returns defaults to parse script arguments with.
	 *
	 * The keys 'src' and 'deps' do not need to be included as they are universal defaults for any dependency type.
	 *
	 * @since n.e.x.t
	 *
	 * @return array<string, mixed> Script module registration defaults.
	 */
	protected function get_additional_args_defaults(): array {
		return array(
			'ver' => false,
		);
	}

	/**
	 * Utility to check whether a given script module functionality is supported by the current WordPress version.
	 *
	 * It also triggers a PHP notice if the functionality is not supported.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $method         The method that was called. Used to trigger a PHP notice as applicable.
	 * @param string $check_function Optional. The WordPress core function to check for. If none is provided, the
	 *                               functionality is assumed to be not supported. Default empty string (none).
	 * @return bool True whether the functionality is supported, false otherwise.
	 */
	private function support_check( string $method, string $check_function = '' ): bool {
		if ( ! $check_function || ! function_exists( $check_function ) ) {
			_doing_it_wrong(
				// The $method parameter is safe to use as it is always __METHOD__, called internally by this class.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$method,
				esc_html__( 'This script module functionality is not supported by the current WordPress version. Basic script module functionality was added in WordPress 6.5.', 'wp-oop-plugin-lib' ), // phpcs:ignore Generic.Files.LineLength.TooLong
				''
			);
			return false;
		}
		return true;
	}
}
