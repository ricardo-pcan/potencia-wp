<?php
function level_meta_box_add()
{
  add_meta_box( 'level_metabox', 'Identificación', 'level_meta_box_cb', 'fichas', 'normal', 'high' );
}


function level_meta_box_cb( $post ) {
  global $post;
  $values = get_post_custom( $post->ID );
  $selected_level = isset( $values['level_select'] ) ? esc_attr( $values['level_select'][0] ) :  '”';
  $selected_grade = isset( $values['grade_select'] ) ? esc_attr( $values['grade_select'][0] ) :  '”';
  ?>

    <p>
      <label for="level_select">Nivel</label>
      <br/>
      <select name="level_select" id="level_select">
        <option value="" disabled>Selecciona un nivel</option>
        <option value="elementary" <?php selected( $selected_level, 'elementary' ); ?>>Primaria</option>
        <option value="highschool" <?php selected( $selected_level, 'highschool' ); ?>>Secundaria</option>
      </select>
    </p>

    <p>
      <label for="selected_grade">Grado</label>
      <br/>
      <div id="grade_select_field">

      </div>
    </p>
  <?php

  wp_nonce_field( 'level_meta_box_nonce', 'meta_box_nonce' );
}


function level_meta_box_save( $post_id ) {
  // Bail if we're doing an auto save
  if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

  // if our nonce isn't there, or we can't verify it, bail
  if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'level_meta_box_nonce' ) ) return;

  // if our current user can't edit this post, bail
  if( !current_user_can( 'edit_post' ) ) return;

  // now we can actually save the data
  $allowed = array(
      'a' => array( // on allow a tags
        'href' => array() // and those anchors can only have href attribute
      )
  );

  if( isset( $_POST['level_select'] ) )
    update_post_meta( $post_id, 'level_select', esc_attr( $_POST['level_select'] ) );

  if( isset( $_POST['level_select'] ) )
    update_post_meta( $post_id, 'grade_select', esc_attr( $_POST['grade_select'] ) );

  /*

  // This is purely my personal preference for saving check-boxes
  $chk = isset( $_POST['my_meta_box_check'] ) && $_POST['my_meta_box_select'] ? 'on' : 'off';
  update_post_meta( $post_id, 'my_meta_box_check', $chk );
  */
}

add_action( 'add_meta_boxes', 'level_meta_box_add' );
add_action( 'save_post', 'level_meta_box_save' );

?>
