<?php

do_meta_boxes(null, 'normal', $post);

if ( 'page' == $post_type ) {
	do_action( 'edit_page_form', $post );
}
else {
	do_action( 'edit_form_advanced', $post );
}

do_meta_boxes(null, 'advanced', $post);