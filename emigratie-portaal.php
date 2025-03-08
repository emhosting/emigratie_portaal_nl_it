<?php
/*
Plugin Name: Emigratie Portaal (Nederland - Italie)
Description: Plugin met gescheiden omgevingen: de admin beheert globale standaardrecords (user_id = 0) en gebruikers krijgen via registratie of automatisch een persoonlijke kopie. De frontend toont zowel globale als persoonlijke records via de shortcode [emigratie_portaal].
Version: 1.9-final
Author: Martijn van den Berg | emigratienaaritalie.nl
*/

// Laad de benodigde bestanden uit de submap 'inc'
require_once( plugin_dir_path( __FILE__ ) . 'inc/database.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/functions.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/user-actions.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/admin-menu.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/assets.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/ajax-handlers.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/frontend.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/dashboard.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/manage-categories.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/manage-tasks.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/manage-attachments.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/user-interface.php' );

// Registreer de activatiehook zodat de database-installatie wordt uitgevoerd.
register_activation_hook( __FILE__, 'ep_install' );

/**
 * Hoofdfunctie voor de adminpagina.
 * Wordt opgeroepen via het hoofdmenu-item.
 */
function ep_main_page() {
    if ( function_exists( 'ep_dashboard_page' ) ) {
        ep_dashboard_page();
    } else {
        echo '<div class="notice notice-error"><p>Dashboard functie niet gevonden.</p></div>';
    }
}
?>