<?php if (!defined('ABSPATH')) exit; ?>
<div class="ep-checklist">
    <div class="ep-sidebar">
        <ul id="ep-categories">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <li id="cat-<?php echo $cat->term_id; ?>" data-id="<?php echo $cat->term_id; ?>" class="ep-category-item<?php echo ($active_cat && $cat->term_id == $active_cat->term_id ? ' active' : ''); ?>">
                        <?php echo esc_html($cat->name); ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
    <div class="ep-tasks">
        <ul id="ep-tasks-list" data-category="<?php echo $active_cat ? intval($active_cat->term_id) : 0; ?>">
            <?php 
                if ($active_cat) {
                    echo ep_render_tasks($active_cat->term_id);
                }
            ?>
        </ul>
    </div>
</div>
