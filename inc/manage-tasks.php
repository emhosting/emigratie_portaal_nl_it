<?php
function ep_manage_tasks_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'ep_tasks';
    $categories_table = $wpdb->prefix . 'ep_categories';

    if (isset($_POST['new_task'])) {
        $title = sanitize_text_field($_POST['task_title']);
        $category_id = intval($_POST['task_category']);
        $status = sanitize_text_field($_POST['task_status']);
        $color = sanitize_hex_color($_POST['task_color']);
        $start_date = sanitize_text_field($_POST['task_start_date']);
        $deadline = sanitize_text_field($_POST['task_deadline']);
        $notes = sanitize_textarea_field($_POST['task_notes']);
        $ordering = intval($_POST['task_ordering']);

        $wpdb->insert(
            $table,
            array(
                'user_id' => 0,
                'category_id' => $category_id,
                'title' => $title,
                'status' => $status,
                'color' => $color,
                'start_date' => $start_date,
                'deadline' => $deadline,
                'notes' => $notes,
                'ordering' => $ordering,
            ),
            array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d')
        );
    }

    if (isset($_POST['delete_task'])) {
        $id = intval($_POST['task_id']);

        $wpdb->delete(
            $table,
            array('id' => $id),
            array('%d')
        );
    }

    $tasks = $wpdb->get_results("SELECT * FROM $table WHERE user_id = 0 ORDER BY ordering ASC");
    $categories = $wpdb->get_results("SELECT * FROM $categories_table WHERE user_id = 0 ORDER BY ordering ASC");

    echo '<div class="wrap">';
    echo '<h1>Beheer Taken</h1>';
    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th scope="row">Titel</th>';
    echo '<td><input type="text" name="task_title" required /></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row">Categorie</th>';
    echo '<td>';
    echo '<select name="task_category" required>';
    foreach ($categories as $cat) {
        echo '<option value="' . intval($cat->id) . '">' . esc_html($cat->name) . '</option>';
    }
    echo '</select>';
    echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row">Status</th>';
    echo '<td><input type="text" name="task_status" /></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row">Kleur</th>';
    echo '<td><input type="color" name="task_color" value="#cccccc" /></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row">Startdatum</th>';
    echo '<td><input type="date" name="task_start_date" /></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row">Deadline</th>';
    echo '<td><input type="date" name="task_deadline" /></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row">Notities</th>';
    echo '<td><textarea name="task_notes"></textarea></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row">Volgorde</th>';
    echo '<td><input type="number" name="task_ordering" required /></td>';
    echo '</tr>';
    echo '</table>';
    echo '<input type="submit" name="new_task" class="button button-primary" value="Taak Toevoegen" />';
    echo '</form>';

    echo '<h2>Bestaande Taken</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Titel</th><th>Categorie</th><th>Status</th><th>Kleur</th><th>Startdatum</th><th>Deadline</th><th>Notities</th><th>Volgorde</th><th>Acties</th></tr></thead>';
    echo '<tbody>';
    foreach ($tasks as $task) {
        $category_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM $categories_table WHERE id = %d", $task->category_id));
        echo '<tr>';
        echo '<td>' . esc_html($task->title) . '</td>';
        echo '<td>' . esc_html($category_name) . '</td>';
        echo '<td>' . esc_html($task->status) . '</td>';
        echo '<td><span style="background-color:' . esc_attr($task->color) . '; display:inline-block; width:20px; height:20px;"></span></td>';
        echo '<td>' . esc_html($task->start_date) . '</td>';
        echo '<td>' . esc_html($task->deadline) . '</td>';
        echo '<td>' . esc_html($task->notes) . '</td>';
        echo '<td>' . intval($task->ordering) . '</td>';
        echo '<td>';
        echo '<form method="post" style="display:inline;">';
        echo '<input type="hidden" name="task_id" value="' . intval($task->id) . '" />';
        echo '<input type="submit" name="delete_task" class="button button-secondary" value="Verwijder" />';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
?>