<?php

namespace WSUWP\Issue_Builder;

/**
 * Adds Issue Promo shortcode to the Plugin.
 *
 * @since 0.0.6
 */
class Issue_Promo_Shortcode {

	/**
	 * @since 0.0.6
	 * @var array $default_atts Array of default shortcode attributes
	 */
	public $default_atts = array(
		'id'                   => '',
		'display-image'        => '',
		'header'               => '',
		'subheader'            => '',
		'background-position'  => '',
		'classes'              => '',
		'display-excerpt'      => '',
	);


	/**
	 * Init shortcode
	 *
	 * @since 0.0.6
	 */
	public function __construct() {

		$this->register_shortcode();

	} // End __construct


	/**
	 * Register the shortcode with WordPress
	 *
	 * @since 0.0.6
	 */
	public function register_shortcode() {

		add_shortcode( 'issue_article', array( $this, 'render_shortcode' ) );

	} // End register_shortcode


	/**
	 * Generate the html output for the shortcode
	 *
	 * @since 0.0.6
	 *
	 * @param array $atts Shortcode attributes
	 * @param string|null $content Shortcode content
	 * @param string $tag Shortcode tag/slug
	 *
	 */
	public function render_shortcode( $atts, $content, $tag ) {

		$atts = shortcode_atts( $this->default_atts, $atts, $tag );

		$id              = ( ! empty( $atts['id'] ) ) ? $atts['id'] : '';
		$image_array     = $this->get_post_image_array( $id, $atts );
		$column_classes  = ( ! empty( $atts['classes'] ) ) ? $atts['classes'] : '';
		$column_styles   = ( ! empty( $atts['background-position'] ) ) ? 'background-postion:' . $atts['background-position'] . ';' : '';
		$header          = ( ! empty( $atts['header'] ) ) ? $atts['header'] : '';
		$subheader       = ( ! empty( $atts['subheader'] ) ) ? $atts['subheader'] : '';
		$display_image   = ( ! empty( $atts['display-image'] ) ) ? $atts['display-image'] : '';
		$img_src         = ( ! empty( $image_array['src'] ) ) ? $image_array['src'] : '';
		$img_alt         = ( ! empty( $image_array['alt'] ) ) ? $image_array['alt'] : '';
		$excerpt         = ( ! empty( $atts['display-excerpt'] ) ) ? wpautop( get_the_excerpt( $id ) ) : '';
		$link            = get_permalink( $id );

		ob_start();

		include dirname( dirname( __FILE__ ) ) . '/displays/issue-article.php';

		$html = ob_get_clean();

		return $html;

	} // End render_shortcode


	/**
	 * Generate image array setting for the shortcode
	 *
	 * @since 0.0.6
	 *
	 * @param string $id Post ID
	 * @param array $atts Shortcode attributes
	 *
	 * @return array Image array with src and alt set if they exist, empty array if no image.
	 */
	private function get_post_image_array( $id, $atts ) {

		$display_image = ( ! empty( $atts['display-image'] ) ) ? $atts['display-image'] : '';

		$image_array = array();

		if ( 'featured-image' === $display_image ) {

			$img_id = get_post_thumbnail_id( $id );

			if ( ! empty( $img_id ) ) {

				$image_array['src'] = get_the_post_thumbnail_url( $id, 'large' );

				$image_array['alt'] = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

			} // End if
		} elseif ( ( 'thumbnail-image' === $display_image ) && class_exists( 'MultiPostThumbnails' ) ) {

			$image_array['src'] = MultiPostThumbnails::get_post_thumbnail_url( 'post', 'thumbnail-image', $id, 'large' );

		} // End if

		return $image_array;

	} // End $image_array

} // End Issue_Promo_Shortcode

$issue_promo_shortcode = new Issue_Promo_Shortcode();
