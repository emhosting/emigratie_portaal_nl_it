<?php
function ep_render_frontend() {
    // Render de frontend interface
    echo do_shortcode('[ep_user_interface]');
}

add_action('wp_footer', 'ep_render_frontend');
?>