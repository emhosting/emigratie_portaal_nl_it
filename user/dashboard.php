<?php
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode handler for the main dashboard page
function ep_dashboard_shortcode() {
    // Calculate progress and task stats
    $progress = ep_get_progress();
    $total = 0;
    $done = 0;
    $count = wp_count_posts('emigratie_task');
    if ($count && isset($count->publish)) {
        $total = (int)$count->publish;
    }
    $done_query = new WP_Query(array(
        'post_type'  => 'emigratie_task',
        'meta_key'   => 'completed',
        'meta_value' => '1'
    ));
    $done = (int)$done_query->found_posts;
    $status_text = sprintf(__('You have completed %d of %d tasks.', 'emigratie-portaal'), $done, $total);

    // Get the URL of the checklist page (for linking)
    $checklist_page = get_page_by_path('emigratie-checklist');
    $checklist_url = $checklist_page ? get_permalink($checklist_page) : '#';
    // Prepare link to checklist filtered to Financial category
    $finance_link = $checklist_url;
    $finance_term = get_term_by('slug', 'financieel', 'emigratie_category');
    if ($finance_term) {
        $finance_link = add_query_arg('category', $finance_term->slug, $checklist_url);
    }

    // Optional welcome text from settings
    $welcome_text = get_option('ep_welcome_text', '');

    // Build the HTML output
    $output  = '<div class="ep-dashboard">';
    $output .= '<div class="ep-header"><img src="' . EP_PLUGIN_URL . 'assets/images/logo.png" alt="Emigratie Portaal" class="ep-logo" /></div>';
    $output .= '<div class="ep-module">';
    $output .= '<h2>' . __('My Emigration Checklist', 'emigratie-portaal') . '</h2>';
    $output .= '<div class="ep-progress-bar"><div class="ep-progress" style="width: ' . intval($progress) . '%;"></div></div>';
    $output .= '<span class="ep-progress-text">' . intval($progress) . '% ' . __('completed', 'emigratie-portaal') . '</span>';
    $output .= '<p>' . $status_text . '</p>';
    if (!empty($welcome_text)) {
        $output .= '<p>' . esc_html($welcome_text) . '</p>';
    }
    $output .= '<a href="' . esc_url($checklist_url) . '" class="button ep-open-checklist">' . __('Open Checklist', 'emigratie-portaal') . '</a>';
    $output .= '</div>'; // end first module
    $output .= '<div class="ep-module">';
    $output .= '<h2>' . __('Financial', 'emigratie-portaal') . '</h2>';
    $output .= '<p>' . __('Manage your financial tasks and overview here.', 'emigratie-portaal') . '</p>';
    $output .= '<a href="' . esc_url($finance_link) . '" class="button ep-open-finance">' . __('View Finances', 'emigratie-portaal') . '</a>';
    $output .= '</div>'; // end second module
    $output .= '</div>'; // end dashboard
    return $output;
}
