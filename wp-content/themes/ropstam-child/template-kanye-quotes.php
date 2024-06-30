<?php
/*
Template Name: Listing Kanye Quotes
*/
get_header();
?>
<div class="kanye-quotes">
    <h1>Listing Kanye Quotes</h1>
    <?php
    for ($i = 0; $i < 5; $i++) {
        $response = wp_remote_get('https://api.kanye.rest/');
        if (is_wp_error($response)) {
            continue;
        }
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (isset($data['quote'])) {
            echo '<p>' . esc_html($data['quote']) . '</p>';
        }
    }
    ?>
</div>
<?php get_footer(); ?>
