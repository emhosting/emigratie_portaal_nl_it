<?php
if (!defined('ABSPATH')) {
    exit;
}

// Generate HTML list items for tasks of a given category (used in template and AJAX)
function ep_render_tasks($category_id) {
    $output = '';
    $tasks = get_posts(array(
        'post_type'   => 'emigratie_task',
        'numberposts' => -1,
        'tax_query'   => array(array(
            'taxonomy' => 'emigratie_category',
            'field'    => 'term_id',
            'terms'    => $category_id
        )),
        'orderby'     => 'menu_order',
        'order'       => 'ASC'
    ));
    if ($tasks) {
        foreach ($tasks as $task) {
            $completed = get_post_meta($task->ID, 'completed', true);
            $start = get_post_meta($task->ID, 'start_date', true);
            $end = get_post_meta($task->ID, 'end_date', true);
            $is_done = ($completed === '1');
            $output .= '<li id="task-' . $task->ID . '" data-id="' . $task->ID . '" class="ep-task-item' . ($is_done ? ' completed' : '') . '">';
            $output .= '<label><input type="checkbox" class="task-complete" ' . ($is_done ? 'checked' : '') . '> ' . esc_html($task->post_title) . '</label>';
            $output .= '<div class="task-dates">';
            $output .= '<span class="start-date-label">' . __('Start Date:', 'emigratie-portaal') . '</span> ';
            $output .= '<span class="start-date-value">' . (!empty($start) ? esc_html($start) : __('not set', 'emigratie-portaal')) . '</span> ';
            $output .= '<span class="end-date-label">' . __('End Date:', 'emigratie-portaal') . '</span> ';
            $output .= '<span class="end-date-value">' . (!empty($end) ? esc_html($end) : __('not set', 'emigratie-portaal')) . '</span> ';
            $output .= '<a href="#" class="edit-dates">' . __('Edit', 'emigratie-portaal') . '</a> ';
            $output .= '<span class="save-dates" style="display:none;">';
            $output .= '<input type="date" class="date-input start-date-input" value="' . esc_attr($start) . '"> ';
            $output .= '<input type="date" class="date-input end-date-input" value="' . esc_attr($end) . '"> ';
            $output .= '<button class="save-dates-btn">' . __('Save', 'emigratie-portaal') . '</button>';
            $output .= '</span>';
            $output .= '</div></li>';
        }
    }
    return $output;
}
