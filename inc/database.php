<?php
if (!defined('ABSPATH')) exit;

function ep_install() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Tabel voor categorieën
    $table_name = $wpdb->prefix . 'ep_categories';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        name varchar(100) NOT NULL,
        ordering int(11) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Tabel voor taken
    $table_tasks = $wpdb->prefix . 'ep_tasks';
    $sql_tasks = "CREATE TABLE IF NOT EXISTS $table_tasks (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        category_id mediumint(9) NOT NULL,
        title varchar(200) NOT NULL,
        status varchar(50) DEFAULT '',
        color varchar(10) DEFAULT '#cccccc',
        start_date date DEFAULT NULL,
        deadline date DEFAULT NULL,
        notes text,
        ordering int(11) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    dbDelta($sql_tasks);

    // Controleer of de 'color' kolom bestaat en voeg deze toe indien nodig
    $columns = $wpdb->get_col("DESC $table_tasks", 0);
    if (!in_array('color', $columns)) {
        $wpdb->query("ALTER TABLE $table_tasks ADD COLUMN color varchar(10) DEFAULT '#cccccc'");
    }
}
add_action('plugins_loaded', 'ep_install');
?>