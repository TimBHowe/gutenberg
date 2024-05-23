<?php
/**
 * Server-side rendering of the `core/post-date` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-date` block on the server.
 *
 * @since 5.8.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the filtered post date for the current post wrapped inside "time" tags.
 */
function render_block_core_post_date( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$post_ID          = $block->context['postId'];
	$formatted_date   = get_the_date( empty( $attributes['format'] ) ? '' : $attributes['format'], $post_ID );
	$unformatted_date = esc_attr( get_the_date( 'c', $post_ID ) );
	$classes          = array();

	if ( isset( $attributes['textAlign'] ) ) {
		$classes[] = 'has-text-align-' . $attributes['textAlign'];
	}
	if ( isset( $attributes['style']['elements']['link']['color']['text'] ) ) {
		$classes[] = 'has-link-color';
	}

	/*
	 * If the "Display last modified date" setting is enabled,
	 * only display the modified date if it is later than the publishing date.
	 */
	if ( isset( $attributes['displayType'] ) && 'modified' === $attributes['displayType'] ) {
		if ( get_the_modified_date( 'Ymdhi', $post_ID ) > get_the_date( 'Ymdhi', $post_ID ) ) {
			$formatted_date   = get_the_modified_date( empty( $attributes['format'] ) ? '' : $attributes['format'], $post_ID );
			$unformatted_date = esc_attr( get_the_modified_date( 'c', $post_ID ) );
			$classes[]        = 'wp-block-post-date__modified-date';
		} else {
			return '';
		}
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

	$prefix = '';
	if ( isset( $attributes['prefix'] ) && $attributes['prefix'] ) {
		$prefix = '<span class="wp-block-post-date__prefix">' . $attributes['prefix'] . '</span>';
	}

	if ( isset( $attributes['isLink'] ) && $attributes['isLink'] ) {
		$formatted_date = sprintf( '<a href="%1s">%2s</a>', get_the_permalink( $post_ID ), $formatted_date );
	}

	$suffix = '';
	if ( isset( $attributes['suffix'] ) && $attributes['suffix'] ) {
		$suffix = '<span class="wp-block-post-date__suffix">' . $attributes['suffix'] . '</span>';
	}

	return sprintf(
		'<div %1$s>%2$s<time datetime="%3$s">%4$s</time>%5$s</div>',
		$wrapper_attributes,
		$prefix,
		$unformatted_date,
		$formatted_date,
		$suffix
	);
}

/**
 * Registers the `core/post-date` block on the server.
 *
 * @since 5.8.0
 */
function register_block_core_post_date() {
	register_block_type_from_metadata(
		__DIR__ . '/post-date',
		array(
			'render_callback' => 'render_block_core_post_date',
		)
	);
}
add_action( 'init', 'register_block_core_post_date' );
