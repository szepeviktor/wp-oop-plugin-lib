<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Post_Repository
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity_Query;
use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity_Repository;
use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Trash_Aware;
use Felix_Arntz\WP_OOP_Plugin_Lib\Exception\Invalid_Entity_Data_Exception;
use WP_Post;

/**
 * Class for a repository of WordPress posts.
 *
 * @since n.e.x.t
 */
class Post_Repository implements Entity_Repository, Trash_Aware {

	/**
	 * Checks whether a post for the given ID exists in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Post ID.
	 * @return bool True if the post exists, false otherwise.
	 */
	public function exists( int $id ): bool {
		return get_post( $id ) !== null;
	}

	/**
	 * Gets the post for a given ID from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Post ID.
	 * @return Post|null The post, or `null` if no value exists.
	 */
	public function get( int $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			return null;
		}
		return new Post( $post );
	}

	/**
	 * Updates the post for a given ID in the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int                  $id   Post ID.
	 * @param array<string, mixed> $data New data to set for the post. See {@see wp_update_post()} for a list of
	 *                                   supported arguments.
	 * @return bool True on success, false on failure.
	 *
	 * @throws Invalid_Entity_Data_Exception Thrown when updating the post fails and `WP_DEBUG` is enabled.
	 */
	public function update( int $id, array $data ): bool {
		$data['ID'] = $id;

		$result = wp_update_post( $data, true );

		if ( is_wp_error( $result ) ) {
			if ( WP_DEBUG ) {
				throw new Invalid_Entity_Data_Exception( esc_html( $result->get_error_message() ) );
			}
			return false;
		}

		return true;
	}

	/**
	 * Adds a new post to the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $data Initial data to set for the post. See {@see wp_insert_post()} for a list of
	 *                                   supported arguments.
	 * @return int The post ID, or `0` on failure.
	 *
	 * @throws Invalid_Entity_Data_Exception Thrown when adding the post fails and `WP_DEBUG` is enabled.
	 */
	public function add( array $data ): int {
		$result = wp_insert_post( $data, true );

		if ( is_wp_error( $result ) ) {
			if ( WP_DEBUG ) {
				throw new Invalid_Entity_Data_Exception( esc_html( $result->get_error_message() ) );
			}
			return 0;
		}

		return (int) $result;
	}

	/**
	 * Deletes the post for a given ID from the repository.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Post ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete( int $id ): bool {
		return (bool) wp_delete_post( $id, true );
	}

	/**
	 * Returns a post query object for the given arguments.
	 *
	 * @since n.e.x.t
	 *
	 * @param array<string, mixed> $query_args Query arguments.
	 * @return Post_Query Query object.
	 */
	public function query( array $query_args ): Entity_Query {
		return new Post_Query( $query_args );
	}

	/**
	 * Moves the post for a given ID to the trash.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Post ID.
	 * @return bool True on success, false on failure.
	 */
	public function trash( int $id ): bool {
		return (bool) wp_trash_post( $id );
	}

	/**
	 * Moves the post for a given ID out of the trash.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Post ID.
	 * @return bool True on success, false on failure.
	 */
	public function untrash( int $id ): bool {
		return (bool) wp_untrash_post( $id );
	}

	/**
	 * Checks whether the post for a given ID is in the trash.
	 *
	 * @since n.e.x.t
	 *
	 * @param int $id Post ID.
	 * @return bool True if the post is in the trash, false otherwise.
	 */
	public function is_trashed( int $id ): bool {
		return get_post_status( $id ) === 'trash';
	}
}
