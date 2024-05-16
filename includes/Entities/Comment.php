<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\Entities\Comment
 *
 * @since n.e.x.t
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\Entities;

use Felix_Arntz\WP_OOP_Plugin_Lib\Contracts\Entity;
use WP_Comment;

/**
 * Class representing a WordPress comment.
 *
 * @since n.e.x.t
 */
class Comment implements Entity {

	/**
	 * The underlying WordPress comment object.
	 *
	 * @since n.e.x.t
	 * @var WP_Comment
	 */
	private $wp_obj;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param WP_Comment $comment The underlying WordPress comment object.
	 */
	public function __construct( WP_Comment $comment ) {
		$this->wp_obj = $comment;
	}

	/**
	 * Gets the comment ID.
	 *
	 * @since n.e.x.t
	 *
	 * @return int The comment ID.
	 */
	public function get_id(): int {
		return (int) $this->wp_obj->comment_ID;
	}

	/**
	 * Checks whether the comment is publicly accessible.
	 *
	 * @since n.e.x.t
	 *
	 * @return bool True if the comment is public, false otherwise.
	 */
	public function is_public(): bool {
		return is_post_publicly_viewable( (int) $this->wp_obj->comment_post_ID ) && wp_get_comment_status( $this->wp_obj ) === 'approved';
	}

	/**
	 * Gets the comment's primary URL.
	 *
	 * @since n.e.x.t
	 *
	 * @return string Comment link, or empty string if none.
	 */
	public function get_url(): string {
		return (string) get_comment_link( $this->wp_obj );
	}

	/**
	 * Gets the comment's edit URL.
	 *
	 * @since n.e.x.t
	 *
	 * @return string URL to edit the comment, or empty string if none.
	 */
	public function get_edit_url(): string {
		return str_replace( '&amp;', '&', (string) get_edit_comment_link( $this->wp_obj ) );
	}

	/**
	 * Gets the value for the given field of the comment.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $field The field identifier.
	 * @return mixed Value for the field, `null` if not set.
	 */
	public function get_field_value( string $field ) {
		switch ( $field ) {
			// Comment status has special handling.
			case 'comment_status':
				return wp_get_comment_status( $this->wp_obj );
			default:
				if ( isset( $this->wp_obj->$field ) ) {
					return $this->wp_obj->$field;
				}
		}

		return null;
	}
}
