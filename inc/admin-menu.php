<?php
function ep_admin_menu() {
    add_menu_page( 'Emigratie Portaal', 'Emigratie Portaal', 'manage_options', 'emigratie-portaal', 'ep_dashboard_page', 'dashicons-admin-site-alt3', 6 );
    add_submenu_page( 'emigratie-portaal', 'Beheer Categorieën', 'Categorieën', 'manage_options', 'emigratie-portaal-categories', 'ep_manage_categories_page' );
    add_submenu_page( 'emigratie-portaal', 'Beheer Taken', 'Taken', 'manage_options', 'emigratie-portaal-tasks', 'ep_manage_tasks_page' );
}
add_action( 'admin_menu', 'ep_admin_menu' );
?>
