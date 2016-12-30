<?php
/**
 * Get API Rangel namespace.
 *
 * @since 1.0
 * @return string
 */
function get_potencia_namespace() {
    return 'potencia/v1';
}
/**
 * Get name article category.
 *
 * @since 1.0
 * @return string
 */
function get_name_article_category() {
    return 'Articulo';
}


/**
 * Register rangel routes for WP API v2.
 */
function register_potencia_routes() {
  register_rest_route( get_potencia_namespace(), '/file/(?P<id>[0-9]+)'), array(
    'methods' => 'GET',
    'callback' => 'get_file'
  ));
}

/**
 * Get fichas custom pos type by id
 *
 * @param array $data Options for the function.
 * @return Array Array articles category post.
 */
function get_article( $data ) {
    $params = $data->get_params();
    $post = get_post( $params['id'] );
    return $post;

}




add_action( 'rest_api_init', 'register_potencia_routes');
