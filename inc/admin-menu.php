<?php
function ep_admin_menu() {
    add_menu_page(
        'Emigratie Portaal',
        'Emigratie Portaal',
        'manage_options',
        'emigratie-portaal',
        'ep_admin_page',
        'dashicons-admin-site',
        6
    );
}

function ep_admin_page() {
    ?>
    <div id="ep-admin-container">
        <h1>Emigratie Portaal Beheer</h1>
        <button id="add-category-btn">Categorie Toevoegen</button>
        <!-- Voeg hier de HTML toe voor het beheren van categorieën en taken -->
    </div>
    <?php
}

add_action('admin_menu', 'ep_admin_menu');
?>