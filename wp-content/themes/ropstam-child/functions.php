<?php
// CUSTOM JS FILE ENQUEUE
function enqueue_scripts() {
    wp_enqueue_style('main-stylesheet', get_stylesheet_uri());
    wp_enqueue_script('js-scripts', get_stylesheet_directory_uri() . '/js/projects.js', array('jquery'), time(), true);
    wp_localize_script('js-scripts', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_scripts');

// CHILD THEME STYLE.CSS ENQUEUE
function ropstam_child_enqueue_styles() {
    $parent_style = 'parent-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'ropstam_child_enqueue_styles' );

//REDIRECT SPECIFIC IP USERS
function redirect_specific_ip_users() {
    $user_ip = $_SERVER['REMOTE_ADDR'];
    if (strpos($user_ip, '77.29') === 0) {
        wp_redirect('https://blockusers.com');
        exit;
    }
}
add_action('template_redirect', 'redirect_specific_ip_users');
//END

// REGISTER CUSTOM POST TYPE PROJECTS
function register_custom_post_type_projects() {
    $labels = array(
        'name'               => _x( 'Projects', 'post type general name', 'your-plugin-textdomain' ),
        'singular_name'      => _x( 'Project', 'post type singular name', 'your-plugin-textdomain' ),
        'menu_name'          => _x( 'Projects', 'admin menu', 'your-plugin-textdomain' ),
        'name_admin_bar'     => _x( 'Project', 'add new on admin bar', 'your-plugin-textdomain' ),
        'add_new'            => _x( 'Add New', 'project', 'your-plugin-textdomain' ),
        'add_new_item'       => __( 'Add New Project', 'your-plugin-textdomain' ),
        'new_item'           => __( 'New Project', 'your-plugin-textdomain' ),
        'edit_item'          => __( 'Edit Project', 'your-plugin-textdomain' ),
        'view_item'          => __( 'View Project', 'your-plugin-textdomain' ),
        'all_items'          => __( 'All Projects', 'your-plugin-textdomain' ),
        'search_items'       => __( 'Search Projects', 'your-plugin-textdomain' ),
        'parent_item_colon'  => __( 'Parent Projects:', 'your-plugin-textdomain' ),
        'not_found'          => __( 'No projects found.', 'your-plugin-textdomain' ),
        'not_found_in_trash' => __( 'No projects found in Trash.', 'your-plugin-textdomain' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'project' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );

    register_post_type( 'project', $args );
}
add_action( 'init', 'register_custom_post_type_projects' );

// REGISTER PROJECT TYPE TAXANOMY FOR THE "PROJECTS".
function register_project_type_taxonomy() {
    $labels = array(
        'name'              => _x( 'Project Types', 'taxonomy general name', 'your-plugin-textdomain' ),
        'singular_name'     => _x( 'Project Type', 'taxonomy singular name', 'your-plugin-textdomain' ),
        'search_items'      => __( 'Search Project Types', 'your-plugin-textdomain' ),
        'all_items'         => __( 'All Project Types', 'your-plugin-textdomain' ),
        'parent_item'       => __( 'Parent Project Type', 'your-plugin-textdomain' ),
        'parent_item_colon' => __( 'Parent Project Type:', 'your-plugin-textdomain' ),
        'edit_item'         => __( 'Edit Project Type', 'your-plugin-textdomain' ),
        'update_item'       => __( 'Update Project Type', 'your-plugin-textdomain' ),
        'add_new_item'      => __( 'Add New Project Type', 'your-plugin-textdomain' ),
        'new_item_name'     => __( 'New Project Type Name', 'your-plugin-textdomain' ),
        'menu_name'         => __( 'Project Type', 'your-plugin-textdomain' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'project-type' ),
    );

    register_taxonomy( 'project_type', array( 'project' ), $args );
}
add_action( 'init', 'register_project_type_taxonomy' );
//END

// AJAX- GET LAST THREE ARCHITECTURE PROJECTS IS USER NOT LOGEDIN.
//IF LOGEDIN THEN SHOW 6.
function get_last_three_architecture_projects() {
    $projects_per_page = is_user_logged_in() ? 6 : 3;
    $args = array(
        'post_type'      => 'project',
        'posts_per_page' => $projects_per_page,
        'order'          => 'DESC',
        'orderby'        => 'date',
        'tax_query'      => array(
            array(
                'taxonomy' => 'project_type',
                'field'    => 'slug',
                'terms'    => 'architecture',
            ),
        ),
    );

    $query = new WP_Query($args);

    $projects = array();
    while ($query->have_posts()) {
        $query->the_post();
        $projects_id = get_the_ID();
        $projects[] = array(
            'id'    => $projects_id,
            'title' => get_the_title(),
            'link'  => get_permalink($projects_id),
        );
    }

    wp_reset_postdata();

    return $projects;
}

// AJAX FETCHING THE PROJECTS
function ajax_get_last_three_architecture_projects() {
    $projects = get_last_three_architecture_projects();

    if ($projects) {
        wp_send_json_success(array('data' => $projects));
    } else {
        wp_send_json_error(array('message' => 'No posts found'));
    }
}

add_action('wp_ajax_get_last_three_architecture_projects', 'ajax_get_last_three_architecture_projects');
add_action('wp_ajax_nopriv_get_last_three_architecture_projects', 'ajax_get_last_three_architecture_projects');
//END.

// GIVE ME COFFEE - RANDOM COFFEE API URL.
function hs_give_me_coffee() {
    $api_url = 'https://coffee.alexflipnote.dev/random.json';
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return 'Error: Unable to retrieve coffee image.';
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);


    if (isset($data['file'])) {
        return $data['file'];
    } else {
        return 'Error: No coffee image found.';
    }
}
// REGISTER COFFEE SHORTCODE
function hs_give_me_coffee_shortcode() {
    $image_url = hs_give_me_coffee();
    if (strpos($image_url, 'Error:') === false) {
        return '<h2>Here is your random coffee image!</h2><img src="' . esc_url($image_url) . '" alt="Random Coffee">';
    } else {
        return '<p>' . $image_url . '</p>'; // Display the error message
    }
}
add_shortcode('give_me_coffee', 'hs_give_me_coffee_shortcode');

