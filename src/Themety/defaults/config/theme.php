<?php

return array(

    /**
     * Theme features
     *
     * 'post-thumbnails'
     * or
     * 'post-thumbnails' => array( 'post' )
     *
     * See more options here http://codex.wordpress.org/add_theme_support
     */
    'theme_support' => array(
        'post-thumbnails',
    ),



    /**
    * Include stylesheets
    *
    * Params
    *   src       : theme-relative or full url
    *   deps
    *   ver
    *   media
    *   zone      : backend | frontend | both.  Default "frontend"
    *
    *   See more: http://codex.wordpress.org/Function_Reference/wp_enqueue_style
    */
    'styles' => array(
        'opensans' => array(
            'src' => 'http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300&subset=latin,latin-ext'
        ),
        'styles' => array(
            'src' => 'css/styles.css'
        )
    ),


    /**
     * Include dynamic stylesheet
     *
     *   show   : mixed boolean or callble - shows if true
     */
    'dynamic_styles' => array(
        'theme-settings' => array(
            'src' => 'css/theme-settings.css',
            'file' => Themety\Themety::get('core', 'templateUri') . 'stylesheets/theme-settings.php',
            'show' => true
        )
    ),


    /**
     * Include javascript files
     *
     * Params:
     *   src        : theme-relative or full url
     *   deps
     *   ver
     *   in_footer
     *   zone       : backend | frontend | both.  Default "frontend"
     *   params     : (callable, array, string) variables, used by the script
     *
     * See more http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     *          http://codex.wordpress.org/Function_Reference/wp_localize_script
     */
    'scripts' => array(
        'app' => array(
            'src' => 'js/app.js',
            'deps' => 'jquery',
            'params' => array(
                'custom_variable' => array('hello' => 'world'),
            )
        ),

    ),




    /**
     * Register menu
     *
     * See more http://codex.wordpress.org/Function_Reference/register_nav_menus
     */
    'menus' => array(
        'header-menu' => __('Header Menu'),
        'footer-menu' => __('Footer Menu'),
    ),





    /**
     * Register sidebars
     *
     * See more http://codex.wordpress.org/Function_Reference/register_sidebar
     */
    'sidebars' => array(
        'sidebar' => array(
            'name'          => __( 'Sidebar name', 'theme_text_domain' ),
            'id'            => 'unique-sidebar-id',
            'description'   => '',
            'class'         => '',
            'before_widget' => '<li id="%1$s" class="widget %2$s">',
            'after_widget'  => '</li>',
            'before_title'  => '<h2 class="widgettitle">',
            'after_title'   => '</h2>'
        ),
        'footer' => __('Footer'),
    ),




    /**
     * Image sizes
     *
     * Examples
     * 'post-full' => array(200, 100)                 // x, y
     * 'post-full' => array(200, 100, true)           // x, y, crop
     * 'post-full' => array(200, 100, array(10, 10))  // x, y, crop using possition
     *
     * See more http://codex.wordpress.org/Function_Reference/add_image_size
     */
    'image_sizes' => array(
        'img_thumb' => array( 80, 80, true ),
    ),



);