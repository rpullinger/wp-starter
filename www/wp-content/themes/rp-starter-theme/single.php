<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 */
$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;
// $context['wp_title'] .= ' - ' . $post->title();

$id = $post->get_terms('country')[0]->description;
$image = new TimberImage($id);
$context['country_outline'] = file_get_contents($image->file_loc);

Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );