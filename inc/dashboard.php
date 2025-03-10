<?php
function ep_dashboard_widget() {
    wp_add_dashboard_widget(
        'ep_dashboard_widget',
        'Emigratie Portaal Overzicht',
        'ep_dashboard_widget_display'
    );
}

function ep_dashboard_widget_display() {
    echo '<p>Welkom bij het Emigratie Portaal. Hier kunt u uw taken en categorieën beheren.</p>';
}

add_action('wp_dashboard_setup', 'ep_dashboard_widget');
?>