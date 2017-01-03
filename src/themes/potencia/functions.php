<?php
// Custom Post Type Files

/**
 * Register a fichas post type, with REST API support
 *
 * Based on example at: http://codex.wordpress.org/Function_Reference/register_post_type
 */
add_action( 'init', 'fichas_cpt' );
function fichas_cpt() {
    $labels = array(
        'name'               => _x( 'Fichas', 'post type general name', 'potencia' ),
        'singular_name'      => _x( 'Ficha', 'post type singular name', 'potencia' ),
        'menu_name'          => _x( 'Fichas', 'admin menu', 'potencia' ),
        'name_admin_bar'     => _x( 'Fichas', 'add new on admin bar', 'potencia' ),
        'add_new'            => _x( 'Añadir Nuevo', 'ficha', 'potencia' ),
        'add_new_item'       => __( 'Añadir Nueva Ficha', 'potencia' ),
        'new_item'           => __( 'Nueva Ficha', 'potencia' ),
        'edit_item'          => __( 'Editar Ficha', 'potencia' ),
        'view_item'          => __( 'Ver Ficha', 'potencia' ),
        'all_items'          => __( 'Todas las Fichas', 'potencia' ),
        'search_items'       => __( 'Buscar Fichas', 'potencia' ),
        'parent_item_colon'  => __( 'Parent Fichas:', 'potencia' ),
        'not_found'          => __( 'No se encontraron fichas.', 'potencia' ),
        'not_found_in_trash' => __( 'Ningún elemento encontrado en la papelera.', 'potencia' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'potencia' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'fichas' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'show_in_rest'       => true,
        'rest_base'          => 'fichas',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );

    register_post_type( 'fichas', $args );
}

// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_fichas_taxonomies', 0 );

// create two taxonomies, genres and writers for the post type "book"
function create_fichas_taxonomies() {
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'              => _x( 'Niveles', 'taxonomy general name', 'potencia' ),
    'singular_name'     => _x( 'Nivel', 'taxonomy singular name', 'potencia' ),
    'search_items'      => __( 'Buscar Niveles', 'potencia' ),
    'all_items'         => __( 'Todos los niveles', 'potencia' ),
    'parent_item'       => __( 'Nivel Padre', 'potencia' ),
    'parent_item_colon' => __( 'Nivel Padre:', 'potencia' ),
    'edit_item'         => __( 'Editar Nivel', 'potencia' ),
    'update_item'       => __( 'Actualizar Nivel', 'potencia' ),
    'add_new_item'      => __( 'Añadir nuevo nivel', 'potencia' ),
    'new_item_name'     => __( 'Nuevo Nivel', 'potencia' ),
    'menu_name'         => __( 'Nivel', 'potencia' ),
  );

	$args = array(
		'hierarchical'      => true,
    'post_type'         => 'fichas',
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'level' ),
	);

	register_taxonomy( 'nivel', array( 'fichas' ), $args );
}


/**
 * Add REST API support to an already registered post type.
 */
add_action( 'init', 'custom_post_type_rest_support', 25 );
function custom_post_type_rest_support() {
    global $wp_post_types;

    //be sure to set this to the name of your post type!
    $post_type_name = 'header';
    if( isset( $wp_post_types[ $post_type_name ] ) ) {
        $wp_post_types[$post_type_name]->show_in_rest = true;
        $wp_post_types[$post_type_name]->rest_base = $post_type_name;
        $wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
    }

}

/**
 * Add REST API support to an already registered taxonomy.
 */
add_action( 'init', 'custom_taxonomy_rest_support', 25 );
function custom_taxonomy_rest_support() {
    global $wp_taxonomies;

    //be sure to set this to the name of your taxonomy!
    $taxonomy_name = 'header_class';

    if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
        $wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
        $wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
        $wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
    }


}

/*
 *  Add fields to REST API response for carousels read
 */
function slug_register_fichas_custom_post_type() {
    // Register elements advance custom fields
    $acf_elements = array('button_text', 'button_link');
    foreach( $acf_elements as $acf_element ) {
        register_rest_field( 'fichas',
            $acf_element,
            array(
                'get_callback' => 'get_fichas_post_type',
                'update_callback' => null,
                'schema' => null
            )
        );
    }
    // Register featured images url
    register_rest_field( 'carousel',
        'featured_img_url',
        array(
            'get_callback' => 'get_carousels_post_type',
            'update_callback' => null,
            'schema' => null
        )
    );
}

/*
 *  Gets the data from fichas custom post type
 *
 *  @param (array)($object) Details of current post
 *  @param (string)($field_name) The name of the field.
 *  @param (array)($request) Current request
 *
 *  @return (mixed) The fields requested
 *
 */
function get_fichas_post_type( $object, $field_name, $request ) {
    $acf_elements = array('button_text', 'button_link');
    if( in_array($field_name, $acf_elements) ) {
        $field_name = $field_name; // Add field prefix
        $field_value = get_field($field_name, $object['id']);
        return $field_value;
    }
    if( $field_name == 'featured_img_url' ) {
        $imgSrc = wp_get_attachment_image_src($object['featured_media'], 'full');
        return $imgSrc = ($imgSrc[0] != null) ? $imgSrc[0] : '';
    }
    return get_post_meta( $object[ 'id' ], $field_name, true );
}
add_action( 'rest_api_init', 'slug_register_fichas_custom_post_type' );

/*
 * Include custom endpoints
 */
include "custom_endpoints/potencia.php";

/*
 * Include custom fields
 */
include "custom-fields.php";



function load_custom_wp_admin_js() {
  wp_register_script( 'admin', get_template_directory_uri() . '/admin.js', array(), '', true ); // Vendor
  wp_enqueue_script( 'admin' ); // Enqueue it!
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_js' );




?>
