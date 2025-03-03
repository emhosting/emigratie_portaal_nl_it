jQuery(document).ready(function($) {
    // Initialize sortable for category list (vertical sort)
    $('#ep-categories').sortable({
        axis: 'y',
        update: function(event, ui) {
            var order = $(this).sortable('toArray', { attribute: 'data-id' });
            $.post(EmigratieAjax.ajax_url, {
                action: 'ep_update_cat_order',
                order: JSON.stringify(order),
                _ajax_nonce: EmigratieAjax.nonce
            });
        }
    });

    // Initialize sortable for tasks within the active category
    $('#ep-tasks-list').sortable({
        axis: 'y',
        update: function(event, ui) {
            var order = $(this).sortable('toArray', { attribute: 'data-id' });
            var category = $(this).data('category');
            $.post(EmigratieAjax.ajax_url, {
                action: 'ep_update_task_order',
                category: category,
                order: order,
                _ajax_nonce: EmigratieAjax.nonce
            });
        }
    });

    // Make task items draggable (for dragging to categories)
    function initTaskDraggable() {
        $('.ep-task-item').draggable({
            revert: 'invalid',
            start: function(event, ui) {
                ui.helper.addClass('dragging');
            },
            stop: function(event, ui) {
                ui.helper.removeClass('dragging');
            }
        });
    }
    // Initialize draggable on initial tasks
    initTaskDraggable();

    // Make categories droppable to accept dragged tasks
    $('#ep-categories li').droppable({
        accept: '.ep-task-item',
        hoverClass: 'drop-hover',
        drop: function(event, ui) {
            var newCat = $(this).data('id');
            var taskId = ui.draggable.data('id');
            $.post(EmigratieAjax.ajax_url, {
                action: 'ep_move_task',
                task_id: taskId,
                new_cat: newCat,
                _ajax_nonce: EmigratieAjax.nonce
            }, function(response) {
                if (response.success) {
                    // Remove the task from current list UI after moving
                    ui.draggable.remove();
                }
            });
        }
    });
});
