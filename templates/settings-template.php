<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap">
    <h1><?php _e('Emigratie Portaal Settings', 'emigratie-portaal'); ?></h1>
    <?php if (!empty($message)) echo $message; ?>
    <form method="post">
        <?php wp_nonce_field('ep_settings'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="ep_welcome_text"><?php _e('Welcome Message', 'emigratie-portaal'); ?></label></th>
                <td><textarea name="ep_welcome_text" id="ep_welcome_text" rows="4" cols="50"><?php echo esc_textarea(get_option('ep_welcome_text', '')); ?></textarea></td>
            </tr>
        </table>
        <p><input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'emigratie-portaal'); ?>"></p>
    </form>
</div>
