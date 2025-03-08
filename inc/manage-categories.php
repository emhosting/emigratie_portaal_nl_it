<?php
function ep_manage_categories_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'ep_categories';

    if ( isset( $_POST['new_category'] ) ) {
        $name = sanitize_text_field( $_POST['category_name'] );
        $ordering = intval( $_POST['category_ordering'] );

        $wpdb->insert(
            $table,
            array(
                'user_id'  => 0,
                'name'     => $name,
                'ordering' => $ordering,
            ),
            array( '%d', '%s', '%d' )
        );
    }

    if ( isset( $_POST['delete_category'] ) ) {
        $id = intval( $_POST['category_id'] );

        $wpdb->delete(
            $table,
            array( 'id' => $id ),
            array( '%d' )
        );
    }

    $categories = $wpdb->get_results( "SELECT * FROM $table WHERE user_id = 0 ORDER BY ordering ASC" );

    echo '<div class="wrap">';
    echo '<h1>Beheer Categorieën</h1>';
    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th scope="row">Naam</th>';
    echo '<td><input type="text" name="category_name" required /></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row">Volgorde</th>';
    echo '<td><input type="number" name="category_ordering" required /></td>';
    echo '</tr>';
    echo '</table>';
    echo '<input type="submit" name="new_category" class="button button-primary" value="Categorie Toevoegen" />';
    echo '</form>';

    echo '<h2>Bestaande Categorieën</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Naam</th><th>Volgorde</th><th>Acties</th></tr></thead>';
    echo '<tbody>';
    foreach ( $categories as $cat ) {
        echo '<tr>';
        echo '<td>' . esc_html( $cat->name ) . '</td>';
        echo '<td>' . intval( $cat->ordering ) . '</td>';
        echo '<td>';
        echo '<form method="post" style="display:inline;">';
        echo '<input type="hidden" name="category_id" value="' . intval( $cat->id ) . '" />';
        echo '<input type="submit" name="delete_category" class="button button-secondary" value="Verwijder" />';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
?>