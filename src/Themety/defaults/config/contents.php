<?php

return array(
    /**
     * Post types
     *
     * http://codex.wordpress.org/Function_Reference/register_post_type
     */
    'post_types' => array(
        'test' => array(
            'label' => __('Test'),
            'labels' => array(
                'name' => __('Test'),
                'singular_name' => __('Test'),
            ),
            'public' => true,
            'taxonomies' => array( 'test_cat' ),
        ),

    ),


    /**
     * Taxonomies
     *
     * http://codex.wordpress.org/Function_Reference/register_taxonomy
     */
    'taxonomies' => array(
        'test_cat' => array(
            'hierarchical' => false,
            'labels' => array(
                'name'              => __('Categories'),
                'singular_name'     => __('Category'),
            ),
            'show_ui' => true,
            'show_admin_column' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'test_cat'),
            'single_value' => true,
            'public' => true,
            'show_in_nav_menus' => true,
        ),
    ),



    /**
     * Meta fields
     *
     *
     */

    'meta_boxes' => array(
        'test_group' => array(
            'title' => __('Test group'),
            'post_type' => 'test',
            'field_type' => 'group',
            'items' => array(
                'width' => array(
                    'title' => __('Width'),
                    'field_type' => 'numeric',
                ),
                'height' => array(
                    'title' => __('Height'),
                    'field_type' => 'numeric',
                ),
            ),
        ),

        'test_single_field' => array(
            'title' => __('Single Field'),
            'post_type' => 'test',
            'field_type' => 'text',
        ),
    )
);
