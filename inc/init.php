<?php
function ep_init() {
    // Initialiseer de plugin
    ep_create_tables();
}

add_action('init', 'ep_init');
?>