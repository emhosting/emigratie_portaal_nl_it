<?php
function ep_init() {
    // Initialiseer de plugin
    ep_create_tables();
}

add_action('init', 'ep_init');

function ep_enqueue_scripts() {
    // Zorg ervoor dat de standaard jQuery geladen is (WordPress heeft dit al in de kern)
    wp_enqueue_script('jquery');

    // Laad jQuery UI - dit voegt ook draggable toe
    wp_enqueue_script(
        'jquery-ui',
        'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js',
        array('jquery'),
        '1.13.2',
        true
    );

    // Laad je eigen script, afhankelijk van jQuery en jQuery UI
    wp_enqueue_script(
        'ep-script',
        plugin_dir_url(__FILE__) . '../assets/js/script.js',
        array('jquery', 'jquery-ui'),
        '1.0',
        true
    );
}

add_action('wp_enqueue_scripts', 'ep_enqueue_scripts');
?>