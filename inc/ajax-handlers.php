<?php
if (!defined('ABSPATH')) {
    exit;
}

function ep_ensure_logged_in() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Niet ingelogd'));
    }
}

function ep_log($message) {
    if (WP_DEBUG === true) {
        error_log($message);
    }
}

/**
 * Maak een persoonlijke categorie aan of werk een bestaande categorie bij.
 */
function ep_save_personal_category() {
    ep_ensure_logged_in();
    $user_id = get_current_user_id();

    // Als de gebruiker een administrator is, gebruik dan user_id 0
    if (current_user_can('administrator')) {
        $user_id = 0;
    }

    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $ordering = isset($_POST['ordering']) ? intval($_POST['ordering']) : 0;

    ep_log("Saving category: ID=$category_id, Name=$name, Ordering=$ordering, UserID=$user_id");

    if (empty($name)) {
        wp_send_json_error(array('message' => 'Categorie naam is verplicht.'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'ep_categories';
    if ($category_id > 0) {
        $result = $wpdb->update(
            $table,
            array(
                'name' => $name,
                'ordering' => $ordering,
            ),
            array(
                'id' => $category_id,
                'user_id' => $user_id
            ),
            array('%s', '%d'),
            array('%d', '%d')
        );
    } else {
        $result = $wpdb->insert(
            $table,
            array(
                'user_id' => $user_id,
                'name' => $name,
                'ordering' => $ordering,
            ),
            array('%d', '%s', '%d')
        );
    }

    if ($result !== false) {
        wp_send_json_success(array('message' => 'Categorie succesvol opgeslagen.'));
    } else {
        wp_send_json_error(array('message' => 'Er is een fout opgetreden.'));
    }
}
add_action('wp_ajax_ep_save_personal_category', 'ep_save_personal_category');

/**
 * Verwijder een persoonlijke categorie.
 */
function ep_delete_personal_category() {
    ep_ensure_logged_in();
    $user_id = get_current_user_id();

    // Als de gebruiker een administrator is, gebruik dan user_id 0
    if (current_user_can('administrator')) {
        $user_id = 0;
    }

    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    ep_log("Deleting category: ID=$category_id, UserID=$user_id");

    if (!$category_id) {
        wp_send_json_error(array('message' => 'Categorie ID is verplicht.'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'ep_categories';
    $result = $wpdb->delete(
        $table,
        array(
            'id' => $category_id,
            'user_id' => $user_id
        ),
        array('%d', '%d')
    );

    if ($result !== false) {
        wp_send_json_success(array('message' => 'Categorie succesvol verwijderd.'));
    } else {
        wp_send_json_error(array('message' => 'Er is een fout opgetreden.'));
    }
}
add_action('wp_ajax_ep_delete_personal_category', 'ep_delete_personal_category');

/**
 * Importeer admincategorieën naar de persoonlijke omgeving.
 */
function ep_import_admin_categories() {
    ep_ensure_logged_in();
    $user_id = get_current_user_id();

    // Als de gebruiker een administrator is, gebruik dan user_id 0
    if (current_user_can('administrator')) {
        $user_id = 0;
    }

    ep_log("Importing admin categories for UserID=$user_id");

    global $wpdb;
    $table = $wpdb->prefix . 'ep_categories';
    $global_categories = $wpdb->get_results("SELECT * FROM $table WHERE user_id = 0");

    if (empty($global_categories)) {
        wp_send_json_error(array('message' => 'Geen admincategorieën gevonden.'));
    }

    $imported = 0;
    foreach ($global_categories as $cat) {
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_id = %d AND name = %s", $user_id, $cat->name));
        if (!$exists) {
            $result = $wpdb->insert(
                $table,
                array(
                    'user_id' => $user_id,
                    'name' => $cat->name,
                    'ordering' => $cat->ordering,
                ),
                array('%d', '%s', '%d')
            );
            if ($result) {
                $imported++;
            }
        }
    }

    ep_log("Imported $imported admin categories for UserID=$user_id");

    if ($imported > 0) {
        wp_send_json_success(array('message' => "$imported admincategorieën geïmporteerd."));
    } else {
        wp_send_json_error(array('message' => 'Geen nieuwe admincategorieën geïmporteerd.'));
    }
}
add_action('wp_ajax_ep_import_admin_categories', 'ep_import_admin_categories');

/**
 * Maak een persoonlijke taak aan of werk een bestaande taak bij.
 */
function ep_save_personal_task() {
    ep_ensure_logged_in();
    $user_id = get_current_user_id();

    // Als de gebruiker een administrator is, gebruik dan user_id 0
    if (current_user_can('administrator')) {
        $user_id = 0;
    }

    $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
    $priority = isset($_POST['priority']) ? sanitize_text_field($_POST['priority']) : '';
    $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
    $deadline = isset($_POST['deadline']) ? sanitize_text_field($_POST['deadline']) : '';
    $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
    $ordering = isset($_POST['ordering']) ? intval($_POST['ordering']) : 0;

    ep_log("Saving task: ID=$task_id, Title=$title, Status=$status, Priority=$priority, StartDate=$start_date, Deadline=$deadline, UserID=$user_id");

    // Verwerking van de bijlage
    if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attachment_id = media_handle_upload('attachment', 0);
        if (is_wp_error($attachment_id)) {
            wp_send_json_error(array('message' => 'Fout bij uploaden bijlage.'));
        }
    }

    if (empty($title)) {
        wp_send_json_error(array('message' => 'Taaktitel is verplicht.'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'ep_tasks';
    if ($task_id > 0) {
        $result = $wpdb->update(
            $table,
            array(
                'title' => $title,
                'status' => $status,
                'priority' => $priority,
                'start_date' => $start_date,
                'deadline' => $deadline,
                'notes' => $notes,
                'ordering' => $ordering,
                'attachment_id' => $attachment_id ?? null,
            ),
            array(
                'id' => $task_id,
                'user_id' => $user_id
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d'),
            array('%d', '%d')
        );
    } else {
        $result = $wpdb->insert(
            $table,
            array(
                'user_id' => $user_id,
                'title' => $title,
                'status' => $status,
                'priority' => $priority,
                'start_date' => $start_date,
                'deadline' => $deadline,
                'notes' => $notes,
                'ordering' => $ordering,
                'attachment_id' => $attachment_id ?? null,
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d')
        );
    }

    if ($result !== false) {
        wp_send_json_success(array('message' => 'Taak succesvol opgeslagen.'));
    } else {
        wp_send_json_error(array('message' => 'Er is een fout opgetreden.'));
    }
}
add_action('wp_ajax_ep_save_personal_task', 'ep_save_personal_task');

/**
 * Verwijder een persoonlijke taak.
 */
function ep_delete_personal_task() {
    ep_ensure_logged_in();
    $user_id = get_current_user_id();

    // Als de gebruiker een administrator is, gebruik dan user_id 0
    if (current_user_can('administrator')) {
        $user_id = 0;
    }

    $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;

    ep_log("Deleting task: ID=$task_id, UserID=$user_id");

    if (!$task_id) {
        wp_send_json_error(array('message' => 'Taak ID is verplicht.'));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'ep_tasks';
    $result = $wpdb->delete(
        $table,
        array(
            'id' => $task_id,
            'user_id' => $user_id
        ),
        array('%d', '%d')
    );

    if ($result !== false) {
        wp_send_json_success(array('message' => 'Taak succesvol verwijderd.'));
    } else {
        wp_send_json_error(array('message' => 'Er is een fout opgetreden.'));
    }
}
add_action('wp_ajax_ep_delete_personal_task', 'ep_delete_personal_task');

/**
 * Haal taken op voor een specifieke categorie.
 */
function ep_get_tasks_by_category() {
    ep_ensure_logged_in();
    $user_id = get_current_user_id();

    // Als de gebruiker een administrator is, gebruik dan user_id 0
    if (current_user_can('administrator')) {
        $user_id = 0;
    }

    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    ep_log("Getting tasks for category: ID=$category_id, UserID=$user_id");

    global $wpdb;
    $tasks = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}ep_tasks WHERE user_id = %d AND category_id = %d ORDER BY ordering ASC",
        $user_id, $category_id
    ));

    if (!empty($tasks)) {
        wp_send_json_success($tasks);
    } else {
        wp_send_json_error(array('message' => 'Geen taken gevonden.'));
    }
}
add_action('wp_ajax_ep_get_tasks_by_category', 'ep_get_tasks_by_category');
?>