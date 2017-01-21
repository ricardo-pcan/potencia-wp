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

    $argsTotal = array(
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post_type'        => 'fichas',
        'post_status'      => 'publish',
        'suppress_filters' => true
    );

    $posts_array = get_posts($args);
    $postsAll    = get_posts($argsTotal);
    $countPosts  = count($posts_array);
    $totalPosts  = count($postsAll);

    $arrayResponse = array(
        'data' => array(),
        'meta' => array()
    );

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
        $bimester          = get_field('bimestre', $post->ID);
        $file_thumbnail    = get_field( 'file_small_thumbnail', $post->ID );

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
        $post->file_thumbnail    =  $file_thumbnail;
    }

    $postMeta = array(
        'page'  => $paged + 1,
        'limit' => $postPerPage,
        'posts' => $countPosts,
        'total' => $totalPosts
    );

    $arrayResponse['data'] = $posts_array;
    $arrayResponse['meta'] = $postMeta;

    return $arrayResponse;
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
    // Get curricula values
    $level             = get_field('level', $post->ID);
    $bimester          = get_field('bimestre', $post->ID);

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


    // Get Metadata values
    $ambit                   = get_field( 'ambit', $post->ID );
    $expected_learning       = get_field( 'expected_learning', $post->ID );
    $theme                   = get_field( 'theme', $post->ID );
    $contents                = get_field( 'contents', $post->ID );

    // Get Identification values
    $author                  = get_field( 'author', $post->ID );
    $file_thumbnail          = get_field( 'file_small_thumbnail', $post->ID );
    $file_main_attachemnt    = get_field( 'file_main_attachment', $post->ID );
    $file_key                = get_field( 'file_key', $post->ID );

    // Get teacher correspondance fields
    $teacher_correspondance_title = get_field( 'teacher_mail_input', $post->ID );
    $teacher_mail_title           = get_field( 'teacher_mail_url', $post->ID );

    // Get Discover values
    $discover_img            = get_field( 'discover_img', $post->ID );
    $discover_txt_student    = get_field( 'discover_txt_student', $post->ID );
    $discover_question       = get_field( 'discover_question', $post->ID );
    $discover_txt_teacher    = get_field( 'discover_txt_teacher', $post->ID );
    $discover_add_info       = get_field( 'discover_add_info', $post->ID );
    $discover_relevant       = get_field( 'discover_relevant', $post->ID );
    $discover_related        = get_field( 'discover_related', $post->ID );
    $discover_emotions       = get_field( 'discover_emotions', $post->ID );

    // Get Idea values
    $idea_txt_student       = get_field( 'idea_txt_student', $post->ID );
    $idea_txt_teacher       = get_field( 'idea_txt_teacher', $post->ID );
    $idea_content           = get_field( 'idea_additional', $post->ID );

    // Get Create values
    $create_txt_student     = get_field( 'create_txt_student', $post->ID );
    $create_txt_teacher     = get_field( 'create_txt_teacher', $post->ID );
    $create_content         = get_field( 'create_additional', $post->ID );

    // Get Emotions value
    $emotions = get_field('emotions_txt', $post->ID );
    // Get Evaluation values
    $evaluation_content      = get_field( 'evaluation_data', $post->ID );
    $score                   = get_field( 'score', $post->ID);
    $level                   = get_field('level', $post->ID);
    $bimester                = get_field('bimestre', $post->ID);
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

    // Get Enrich class values
    $enrich_med       = get_field( 'enrich_med', $post->ID );
    $enrich_plan      = get_field( 'enrich_plan', $post->ID );
    $enrich_reactive  = get_field( 'enrich_reactive', $post->ID );


    /*
     * Set post object response
     */

    // Set curricula response
    $curricula                 = new stdClass();
    $post->curricula           = $curricula;
    $curricula->level          = !empty($level) ? $level : '';
    $curricula->grade          = !empty($grade) ? $grade : '';
    $curricula->lesson         = !empty($lesson) ? $lesson : '';
    $curricula->bimester       = !empty($bimester) ? $bimester : '';

    // Set Index response
    $post->ambit               = !empty( $ambit ) ? $ambit : '';
    $post->expected_learning   = !empty( $expected_learning ) ? $expected_learning : '';
    $post->theme               = !empty( $theme ) ? $theme : '';
    $post->contents            = !empty( $contents ) ? $contents : '';

    // Set identification response
    $post->author               = !empty( $author ) ? $author : '';
    $post->file_thumbnail       = $file_thumbnail;
    $post->file_main_attachment = $file_main_attachment;
    $post->file_key             = !empty( $file_key ) ? $file_key : '';

    // Set Teacher Correspondance response
    $teacher_correspondance         = new stdClass();
    $post->teacher_correspondance   = $teacher_correspondance;
    $teacher_correspondance->title  = !empty( $teacher_correspondance_title ) ? $teacher_correspondance_title : '';
    $teacher_correspondance->url    = !empty( $teacher_correspondance_url ) ? $teacher_correspondance_url : '';

    // Set Discover response
    $discover                  = new stdClass();
    $post->discover            = $discover;
    $discover->image           = !empty( $discover_img ) ? $discover_img : '';
    $discover->text_student    = !empty( $discover_txt_student ) ? $discover_txt_student : '';
    $discover->question        = !empty( $discover_question ) ? $discover_question : '';
    $discover->text_teacher    = !empty( $discover_txt_teacher ) ? $discover_txt_teacher : '';
    $discover->additional_info = !empty( $discover_add_info ) ? $discover_add_info : '';
    $discover->relevant_themes = !empty( $discover_relevant ) ? $discover_relevant : '';
    $discover->related         = !empty( $discover_related ) ? $discover_related : '';
    $discover->emotions        = !empty( $discover_emotions ) ? $discover_emotions : '';

    // Set Idea response
    $idea                      = new stdClass();
    $post->idea                = $idea;
    $idea->text_student        = !empty( $idea_txt_student ) ? $idea_txt_student : '';
    $idea->text_teacher        = !empty( $idea_txt_teacher ) ? $idea_txt_teacher : '';
    $idea->content             = !empty( $idea_content ) ? $idea_content : '';

    // Set Create response
    $create                    = new stdClass();
    $post->create              = $idea;
    $create->text_student      = !empty( $create_txt_student ) ? $create_txt_student : '';
    $create->text_teacher      = !empty( $create_txt_teacher ) ? $create_txt_teacher : '';
    $create->content           = !empty( $create_content ) ? $create_content : '';

    $post->emotions            = !empty( $emotions ) ? $emotions : '';
    $post->evaluation_content  = !empty( $evaluation_content ) ? $evaluation_content : '';
    $post->score               = !empty( $score ) ? $score : '';
    $post->grade               = !empty($grade) ? $grade : '';
    $post->lesson              = !empty($lesson) ? $lesson : '';
    $post->bimester            = !empty($bimester) ? $bimester : '';

    // Set Enrich response
    $enrich                    = new stdClass();
    $post->enrich              = $enrich;
    $enrich->related_meds      = !empty( $enrich_med ) ? $enrich_med : '';
    $enrich->related_plannings = !empty( $enrich_plan ) ? $enrich_plan : '';
    $enrich->related_reactives = !empty( $enrich_reactive) ? $enrich_reactive : '';

    return $post;

    } else {
        $post->code = 404;
        $post->message = "Not Found";
        header("Status: 404 Not Found");
        return $post;
    }
}




add_action( 'rest_api_init', 'register_potencia_routes');
