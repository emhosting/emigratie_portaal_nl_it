<?php
function ep_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$wpdb->prefix}ep_categories (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        user_id mediumint(9) NOT NULL,
        ordering mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;

    CREATE TABLE {$wpdb->prefix}ep_tasks (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title tinytext NOT NULL,
        notes text NOT NULL,
        status tinytext NOT NULL,
        priority tinytext NOT NULL,
        start_date date NOT NULL,
        deadline date NOT NULL,
        category_id mediumint(9) NOT NULL,
        user_id mediumint(9) NOT NULL,
        ordering mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'ep_create_tables');
?>