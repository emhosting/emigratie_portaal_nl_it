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

    // Handle status updates via AJAX
    $('#ep-tasks-list').on('change', '.task-status', function() {
        var taskId = $(this).closest('li').data('id');
        var status = $(this).val();
        $.post(EmigratieAjax.ajax_url, {
            action: 'ep_update_task_status',
            task_id: taskId,
            status: status,
            _ajax_nonce: EmigratieAjax.nonce
        }, function(response) {
            if (response.success) {
                // Update task item class based on new status
                var statusClass = '';
                if (status == 'not_started') {
                    statusClass = 'status-not-started';
                } else if (status == 'in_progress') {
                    statusClass = 'status-in-progress';
                } else if (status == 'completed') {
                    statusClass = 'status-completed';
                }
                var $taskItem = $('#task-list').find('[data-id="' + taskId + '"]');
                $taskItem.removeClass('status-not-started status-in-progress status-completed').addClass(statusClass);
            }
        });
    });

    // Handle adding attachments via AJAX
    $('#ep-tasks-list').on('change', '.task-attachment', function() {
        var taskId = $(this).closest('li').data('id');
        var formData = new FormData();
        formData.append('action', 'ep_add_task_attachment');
        formData.append('task_id', taskId);
        formData.append('attachment', $(this)[0].files[0]);
        formData.append('_ajax_nonce', EmigratieAjax.nonce);

        $.ajax({
            url: EmigratieAjax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    var $taskItem = $('#task-list').find('[data-id="' + taskId + '"]');
                    $taskItem.find('.task-attachment-link').remove();
                    $taskItem.append('<a href="' + response.data.url + '" class="task-attachment-link" target="_blank">View Attachment</a>');
                }
            }
        });
    });

    // Handle adding notes via AJAX
    $('#ep-tasks-list').on('blur', '.task-note', function() {
        var taskId = $(this).closest('li').data('id');
        var note = $(this).val();
        $.post(EmigratieAjax.ajax_url, {
            action: 'ep_add_task_note',
            task_id: taskId,
            note: note,
            _ajax_nonce: EmigratieAjax.nonce
        });
    });
});
