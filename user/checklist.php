<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$user_id = get_current_user_id();
$table_name = $wpdb->prefix . 'emigratie_checklist';

// Haal de taken op
$tasks = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $user_id ORDER BY id ASC");

echo '<div class="checklist-container">';
echo '<h2>Mijn Checklist</h2>';
echo '<ul id="task-list">';
if ($tasks) {
    foreach ($tasks as $task) {
        echo '<li data-id="' . esc_attr($task->id) . '" class="task-item">' . esc_html($task->task) . '</li>';
    }
} else {
    echo '<li>Geen taken gevonden.</li>';
}
echo '</ul>';
echo '</div>';


// Shortcode handler for the checklist page (categories and tasks)
function ep_checklist_shortcode() {
    // Fetch all categories (terms)
    $categories = get_terms(array('taxonomy' => 'emigratie_category', 'hide_empty' => false));
    if (is_wp_error($categories)) {
        $categories = array();
    }
    // Order categories by custom saved order if available
    $order_opt = get_option('ep_category_order');
    if ($order_opt && is_array($order_opt)) {
        usort($categories, function($a, $b) use ($order_opt) {
            $posA = array_search($a->term_id, $order_opt);
            $posB = array_search($b->term_id, $order_opt);
            if ($posA === false) $posA = PHP_INT_MAX;
            if ($posB === false) $posB = PHP_INT_MAX;
            return $posA - $posB;
        });
    }
    // Determine the active category (selected category)
    $active_cat = null;
    if (isset($_GET['category'])) {
        $slug = sanitize_text_field($_GET['category']);
        $term = get_term_by('slug', $slug, 'emigratie_category');
        if ($term && !is_wp_error($term)) {
            $active_cat = $term;
        }
    }
    if (!$active_cat && !empty($categories)) {
        $active_cat = $categories[0];
    }
    // Start output buffering and include the template for checklist UI
    ob_start();
    include EP_PLUGIN_DIR . 'templates/checklist-template.php';
    return ob_get_clean();
}
