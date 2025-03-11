<?php
// Zorg dat er niets vóór deze <?php staat (geen spaties, BOM, etc.)

if ( ! function_exists( 'ep_add_category' ) ) {
    function ep_add_category() {
        global $wpdb;
        $name = sanitize_text_field( $_POST['name'] );
        error_log("ep_add_category: name = " . $name);
        $result = $wpdb->insert(
            "{$wpdb->prefix}ep_categories",
            array(
                'name'    => $name,
                'user_id' => 0,
            )
        );
        if ( $result !== false ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => 'Categorie kon niet worden toegevoegd.' ) );
        }
        wp_die();
    }
}

if ( ! function_exists( 'ep_delete_personal_category' ) ) {
    function ep_delete_personal_category() {
        global $wpdb;
        $category_id = intval( $_POST['category_id'] );
        error_log("ep_delete_personal_category: category_id = " . $category_id);
        $result = $wpdb->delete( "{$wpdb->prefix}ep_categories", array( 'id' => $category_id ) );
        if ( $result !== false ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => 'Categorie kon niet worden verwijderd.' ) );
        }
        wp_die();
    }
}

if ( ! function_exists( 'ep_save_personal_task' ) ) {
    function ep_save_personal_task() {
        global $wpdb;
        error_log("ep_save_personal_task: POST data = " . print_r($_POST, true));
        if ( ! isset($_POST['task_data']) || ! is_array($_POST['task_data']) ) {
            error_log("ep_save_personal_task: Ongeldige taakgegevens ontvangen");
            wp_send_json_error( array( 'message' => 'Ongeldige taakgegevens.' ) );
            wp_die();
        }
        $task_data = $_POST['task_data'];
        error_log("ep_save_personal_task: task_data = " . print_r($task_data, true));
        $result = $wpdb->insert( "{$wpdb->prefix}ep_tasks", $task_data );
        if ( $result !== false ) {
            error_log("ep_save_personal_task: Taak succesvol opgeslagen");
            wp_send_json_success();
        } else {
            error_log("ep_save_personal_task: Taak kon niet worden opgeslagen");
            wp_send_json_error( array( 'message' => 'Taak kon niet worden opgeslagen.' ) );
        }
        wp_die();
    }
}

if ( ! function_exists( 'ep_get_tasks_by_category' ) ) {
    function ep_get_tasks_by_category() {
        global $wpdb;
        $category_id = intval( $_POST['category_id'] );
        error_log("ep_get_tasks_by_category: category_id = " . $category_id);
        $tasks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ep_tasks WHERE category_id = %d", $category_id ) );
        if ( $tasks !== null ) {
            wp_send_json_success( $tasks );
        } else {
            wp_send_json_error( array( 'message' => 'Taken konden niet worden opgehaald.' ) );
        }
        wp_die();
    }
}

add_action( 'wp_ajax_add_category', 'ep_add_category' );
add_action( 'wp_ajax_ep_delete_personal_category', 'ep_delete_personal_category' );
add_action( 'wp_ajax_ep_save_personal_task', 'ep_save_personal_task' );
add_action( 'wp_ajax_ep_get_tasks_by_category', 'ep_get_tasks_by_category' );

// Geen sluitende PHP-tag om onbedoelde output te voorkomen.