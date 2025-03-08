<?php
/**
 * User Interface voor de gebruikersomgeving.
 * Registreert de shortcodes [ep_user_interface] en [emigratie_portaal].
 */
function ep_render_user_interface() {
    if ( ! is_user_logged_in() ) {
        return "<p>Log in om de gebruikersinterface te zien.</p>";
    }

    global $wpdb;
    $user_id = get_current_user_id();

    // Als de gebruiker een administrator is, gebruik dan user_id 0
    if (current_user_can('administrator')) {
        $user_id = 0;
    }

    // Haal persoonlijke records op.
    $categories = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}ep_categories WHERE user_id = %d ORDER BY ordering ASC", $user_id
    ));
    $tasks = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}ep_tasks WHERE user_id = %d ORDER BY ordering ASC", $user_id
    ));

    // Haal globale (admin) records op (user_id = 0).
    $global_categories = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ep_categories WHERE user_id = 0 ORDER BY ordering ASC" );
    $global_tasks = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ep_tasks WHERE user_id = 0 ORDER BY ordering ASC" );

    // Mogelijke opties voor status en andere dropdowns
    $status_options = ['Not Started', 'In Progress', 'Completed'];
    $priority_options = ['Low', 'Medium', 'High'];

    // Voeg inline CSS toe om de achtergrondkleur van de gehele pagina te veranderen en hover-effect toe te voegen
    echo '<style>
        body, .ast-plain-container, .ast-page-builder-template {
            background-color: #e6ccb2;
        }
        #ep-container {
            display: flex;
            gap: 20px;
            padding: 20px;
        }
        .ep-sidebar-left, .ep-sidebar-right {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            flex-grow: 1;
        }
        .category-link:hover, .task-link:hover {
            background-color: #d1a374;
            color: #ffffff;
            text-decoration: none;
        }
        .task-column {
            border: 1px solid #d1a374;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>';
    
    ob_start();
    ?>
<div id="ep-container">
    <div class="ep-sidebar-left">
        <h2>Categorieën</h2>
        <button id="add-category-btn" class="button">Categorie Toevoegen</button>
        <?php if ( empty( $categories ) && ! empty( $global_categories ) ) : ?>
            <p>Er zijn admincategorieën beschikbaar. Wilt u deze importeren?</p>
            <button id="import-admin-categories-btn" class="button">Importeer Admin categorieën</button>
        <?php else : ?>
            <ul id="user-category-list">
                <?php foreach ( $categories as $cat ) : ?>
                    <li>
                        <a href="#" class="category-link" data-id="<?php echo intval( $cat->id ); ?>"><?php echo esc_html( $cat->name ); ?> (<?php echo intval( $cat->ordering ); ?>)</a>
                        <button class="edit-category-btn button" data-id="<?php echo intval( $cat->id ); ?>">Bewerk</button>
                        <button class="delete-category-btn button" data-id="<?php echo intval( $cat->id ); ?>">Verwijder</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <button id="show-all-tasks-btn" class="button">Alle Taken Weergeven</button>
    </div>
    <div class="ep-sidebar-right">
        <h2>Taken</h2>
        <button id="add-task-btn" class="button">Taak Toevoegen</button>
        <div id="user-task-list">
            <?php foreach ( $tasks as $task ) : 
                // Voeg een standaardwaarde toe als de eigenschap 'priority' niet bestaat
                if (!isset($task->priority)) {
                    $task->priority = 'Medium';
                }
            ?>
                <div class="task-column" data-id="<?php echo intval( $task->id ); ?>" data-category-id="<?php echo intval( $task->category_id ); ?>">
                    <input type="text" class="task-title" value="<?php echo esc_html( $task->title ); ?>">
                    <select class="task-status">
                        <?php foreach ( $status_options as $status ) : ?>
                            <option value="<?php echo esc_attr( $status ); ?>" <?php selected( $task->status, $status ); ?>><?php echo esc_html( $status ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="task-priority">
                        <?php foreach ( $priority_options as $priority ) : ?>
                            <option value="<?php echo esc_attr( $priority ); ?>" <?php selected( $task->priority, $priority ); ?>><?php echo esc_html( $priority ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <textarea class="task-notes"><?php echo esc_html( $task->notes ); ?></textarea>
                    <input type="date" class="task-start-date" value="<?php echo esc_html( $task->start_date ); ?>">
                    <input type="date" class="task-deadline" value="<?php echo esc_html( $task->deadline ); ?>">
                    <input type="file" class="task-attachment">
                    <button class="delete-task-btn button" data-id="<?php echo intval( $task->id ); ?>">Verwijder</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<form id="category-form" style="display:none;">
    <input type="hidden" id="category-id">
    <input type="text" id="category-name" class="input-field" placeholder="Categorie Naam">
    <input type="number" id="category-ordering" class="input-field" placeholder="Volgorde">
</form>

<script>
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>
<script src="<?php echo plugin_dir_url( dirname(__FILE__) ) . 'assets/js/script.js'; ?>"></script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'ep_user_interface', 'ep_render_user_interface' );
add_shortcode( 'emigratie_portaal', 'ep_render_user_interface' );
?>