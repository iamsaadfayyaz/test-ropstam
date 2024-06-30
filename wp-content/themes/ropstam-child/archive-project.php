<?php
get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type'      => 'project', // Custom post type slug
    'posts_per_page' => 6,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'ASC',
);
$projects_query = new WP_Query($args);

if ($projects_query->have_posts()) : ?>
    <div id="projects-archive" class="projects-archive">
        <?php while ($projects_query->have_posts()) : $projects_query->the_post(); ?>
            <div class="project">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="project-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail(); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <h2 class="project-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="project-content">
                    <?php the_excerpt(); ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="pagination">
        <?php
        echo paginate_links(array(
            'total'     => $projects_query->max_num_pages,
            'current'   => max(1, get_query_var('paged')),
            'format'    => '?paged=%#%',
            'prev_text' => '&laquo; Previous',
            'next_text' => 'Next &raquo;',
        ));
        ?>
    </div>

<?php else : ?>
    <p>No projects found</p>
<?php endif;

wp_reset_postdata();
?>
<div class="container">
    <h3>Projects Results in JSON Format in Console</h3>
    <button class="ajax-projects">AJAX BASED CONSOLE RESULTS</button>
</div>
<?php
get_footer();
?>
