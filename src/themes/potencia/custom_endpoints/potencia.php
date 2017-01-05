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
    register_rest_route( get_potencia_namespace(), '/files/(?P<id>[0-9]+)', array(
        'methods' => 'GET',
        'callback' => 'get_file'
    ));

    register_rest_route( get_potencia_namespace(), '/files', array(
        'methods' => 'GET',
        'callback' => 'get_all_files'
    ));

}


/**
 * Get all custom post fichas
 *
 * @param array $params Options for the function.
 * @return Array Array articles category post.
 *
 * Request:
 *     Parameter: paged
 *         Description: This params is the number of page in the request
 *             pagination, the post per page is 10.
 *         Default: 1
 *         Required: False
 *
 *     Parameter: level
 *         Description: This param is the filter for level [Primaria |
 *             Secundaria]
 *         Required: False
 *
 *     Parameter: grade
 *         Description: This param is the filter for grade such [1, 2, 3]
 *         Required: False
 *
 *     Parameter: lesson
 *         Descirption: THis param is the filter for lesson such Español
 *         Required: False
 */
function get_all_files($params) {

    $paged = isset($_GET['paged']) ? $_GET['paged'] - 1 : 0;
    $postPerPage = 10;
    $postOffset = $paged * $postPerPage;

    $args = array(
        'posts_per_page'   => $postPerPage,
        'offset'           => $postOffset,
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post_type'        => 'fichas',
        'post_status'      => 'publish',
        'suppress_filters' => true
    );

    $posts_array = get_posts($args);

    foreach($posts_array as $post) {
        unset(
            $post->post_author,
            $post->post_date,
            $post->post_date_gmt,
            $post->post_content_filtered,
            $post->post_excerpt,
            $post->post_status,
            $post->comment_status,
            $post->ping_status,
            $post->post_password,
            $post->to_ping,
            $post->pinged,
            $post->post_modified,
            $post->post_modified_gmt,
            $post->content_filtered,
            $post->post_parent,
            $post->guid,
            $post->menu_order,
            $post->post_type,
            $post->post_mime_type,
            $post->comment_count,
            $post->filter,
            $post->level,
            $post->grade,
            $post->lesson
        );

        $ambit             = get_field('ambit', $post->ID);
        $expected_learning = get_field('expected_learning', $post->ID);
        $theme             = get_field('theme', $post->ID);
        $contents          = get_field('contents', $post->ID);
        $level             = get_field('level', $post->ID);
        $bimester          = get_field('bimester', $post->ID);

        $grade  = '';
        $lesson = '';
        if ($level == 'Primaria') { // Is Primaria level
            $grade = get_field('grados_primaria', $post->ID);
            $lesson = get_field('asignaturas_primaria_' . $grade, $post->ID);
        }
        if ($level == 'Secundaria') { // Is Secundaria level
            $grade = get_field('grados_secundaria', $post->ID);
            $lesson = get_field('asignaturas_secundaria_' . $grade, $post->ID);
        }
        $post->ambit             = !empty($ambit) ? $ambit : '';
        $post->expected_learning = !empty($expected_learning) ? $expected_learning : '';
        $post->theme             = !empty($theme) ? $theme : '';
        $post->contents          = !empty($contents) ? $contents : '';
        $post->level             = !empty($level) ? $level : '';
        $post->grade             = !empty($grade) ? $grade : '';
        $post->lesson            = !empty($lesson) ? $lesson : '';
        $post->bimester          = !empty($bimester) ? $bimester : '';
    }
    return $posts_array;
}


/**
 * Get fichas custom post type by id
 *
 * @param array $data Options for the function.
 * @return Array Array articles category post.
 */
function get_file($data) {
    $params = $data->get_params();
    $post = get_post( $params['id'] );

    if ($post) {

    unset(
        $post->post_author,
        $post->post_date,
        $post->post_date_gmt,
        $post->post_content_filtered,
        $post->post_excerpt,
        $post->post_status,
        $post->comment_status,
        $post->ping_status,
        $post->post_password,
        $post->to_ping,
        $post->pinged,
        $post->post_modified,
        $post->post_modified_gmt,
        $post->content_filtered,
        $post->post_parent,
        $post->guid,
        $post->menu_order,
        $post->post_type,
        $post->post_mime_type,
        $post->comment_count,
        $post->filter,
        $post->level
    );
    // Get Metadata values
    $author                  = get_field( 'author', $post->ID );
    $file_number             = get_field( 'file_number', $post->ID );
    $file_title              = get_field( 'file_title', $post->ID);
    // Get Discover values
    $discover_img            = get_field( 'discover_img', $post->ID );
    $discover_txt            = get_field( 'discover_txt', $post->ID );
    $discover_question       = get_field( 'discover_question', $post->ID );
    $discover_add_info       = get_field( 'discover_add_info', $post->ID );
    $discover_relevant       = get_field( 'discover_relevant', $post->ID );
    $discover_related        = get_field( 'discover_related', $post->ID );
    $discover_emotions       = get_field( 'discover_emotions', $post->ID );
    // Get Idea values
    $idea_txt                = get_field( 'idea_text', $post->ID );
    $idea_content            = get_field( 'idea_additional', $post->ID );
    $idea_suggest            = get_field( 'idea_suggest', $post->ID );
    // Get Create values
    $create_txt              = get_field( 'create_text', $post->ID );
    $create_content          = get_field( 'create_additional', $post->ID );
    $create_suggest          = get_field( 'create_suggest', $post->ID );

    // Get Evaluation values
    $evaluation_content      = get_field( 'evaluation_data', $post->ID );
    $score                   = get_field( 'score', $post->ID);
    $level                   = get_field('level', $post->ID);
    $bimester                = get_field('bimester', $post->ID);
    $grade  = '';
    $lesson = '';
    if ($level == 'Primaria') { // Is Primaria level
        $grade = get_field('grados_primaria', $post->ID);
        $lesson = get_field('asignaturas_primaria_' . $grade, $post->ID);
    }
    if ($level == 'Secundaria') { // Is Secundaria level
        $grade = get_field('grados_secundaria', $post->ID);
        $lesson = get_field('asignaturas_secundaria_' . $grade, $post->ID);
    }
    /*
     * Set post object response
     */

    // Set Index response
    $post->ambit               = !empty( $ambit ) ? $ambit : '';
    $post->expected_learning   = !empty( $expected_learning ) ? $expected_learning : '';
    $post->theme               = !empty( $theme ) ? $theme : '';
    $post->contents            = !empty( $contents ) ? $contents : '';

    // Set Metadata response
    $post->author              = !empty( $author ) ? $author : '';
    $post->file_number         = !empty( $file_number ) ? $file_number : '';
    $post->file_title          = !empty( $file_title ) ? $file_title : '';
    $post->level               = !empty( $level ) ? $level : '';
    // Set Discover response
    $discover                  = new stdClass();
    $post->discover            = $discover;
    $discover->image           = !empty( $discover_img ) ? $discover_img : '';
    $discover->text            = !empty( $discover_txt ) ? $discover_txt : '';
    $discover->question        = !empty( $discover_question ) ? $discover_question : '';
    $discover->additional_info = !empty( $discover_add_info ) ? $discover_add_info : '';
    $discover->relevant        = !empty( $discover_relevant ) ? $discover_relevant : '';
    $discover->related         = !empty( $discover_related ) ? $discover_related : '';
    $discover->emotions        = !empty( $discover_emotions ) ? $discover_emotions : '';
    // Set Idea response
    $idea                      = new stdClass();
    $post->idea                = $idea;
    $idea->text                = !empty( $idea_txt ) ? $idea_txt : '';
    $idea->content             = !empty( $idea_content ) ? $idea_content : '';
    $idea->suggest             = !empty( $idea_suggest ) ? $idea_suggest : '';
    // Set Create response
    $create                    = new stdClass();
    $post->create              = $idea;
    $create->text              = !empty( $create_txt ) ? $create_txt : '';
    $create->content           = !empty( $create_content ) ? $create_content : '';
    $create->suggest           = !empty( $create_suggest ) ? $create_suggest : '';
    $post->evaluation_content  = !empty( $evaluation_content ) ? $evaluation_content : '';
    $post->score               = !empty( $score ) ? $score : '';
    $post->grade             = !empty($grade) ? $grade : '';
    $post->lesson            = !empty($lesson) ? $lesson : '';
    $post->bimester          = !empty($bimester) ? $bimester : '';
    return $post;
    }
    else {
        $post->code = 404;
        $post->message = "Not Found";
        header("Status: 404 Not Found");
        return $post;
    }
}




add_action( 'rest_api_init', 'register_potencia_routes');
