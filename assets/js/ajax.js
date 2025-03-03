jQuery(document).ready(function($) {
    // Category click: load tasks for selected category via AJAX
    $('#ep-categories').on('click', 'li', function() {
        if ($(this).hasClass('active')) return;
        $('#ep-categories li').removeClass('active');
        $(this).addClass('active');
        var catId = $(this).data('id');
        $.post(EmigratieAjax.ajax_url, {
            action: 'ep_get_tasks',
            category_id: catId,
            _ajax_nonce: EmigratieAjax.nonce
        }, function(response) {
            if (response.success) {
                // Replace task list HTML with new category's tasks
                $('#ep-tasks-list').attr('data-category', catId).html(response.data.tasks_html);
                // Refresh sortable to recognize new items
                $('#ep-tasks-list').sortable('refresh');
                // Reinitialize draggable for new task items
                $('.ep-task-item').draggable({
                    revert: 'invalid',
                    start: function(event, ui) { ui.helper.addClass('dragging'); },
                    stop: function(event, ui) { ui.helper.removeClass('dragging'); }
                });
            }
        });
    });

    // Task completion checkbox toggled
    $('#ep-tasks-list').on('change', '.task-complete', function() {
        var taskId = $(this).closest('li').data('id');
        var completed = $(this).is(':checked') ? '1' : '0';
        // Mark complete/incomplete via AJAX
        $.post(EmigratieAjax.ajax_url, {
            action: 'ep_mark_complete',
            task_id: taskId,
            completed: completed,
            _ajax_nonce: EmigratieAjax.nonce
        }, function(response) {
            if (response.success) {
                // Update progress bar if present on page
                if ($('.ep-progress').length) {
                    var perc = response.data.progress;
                    $('.ep-progress').css('width', perc + '%');
                    $('.ep-progress-text').text(perc + '% ' + EmigratieAjax.completed_text);
                }
            }
        });
        // Update UI styling of task item
        $(this).closest('li').toggleClass('completed', $(this).is(':checked'));
    });

    // Click "Edit" to show date input fields
    $('#ep-tasks-list').on('click', '.edit-dates', function(e) {
        e.preventDefault();
        var $dates = $(this).closest('.task-dates');
        $(this).hide();
        $dates.find('.start-date-value, .end-date-value').hide();
        $dates.find('.save-dates').show();
    });

    // Click "Save" to save new dates
    $('#ep-tasks-list').on('click', '.save-dates-btn', function(e) {
        e.preventDefault();
        var $li = $(this).closest('li');
        var taskId = $li.data('id');
        var $dates = $(this).closest('.task-dates');
        var startDate = $dates.find('.start-date-input').val();
        var endDate = $dates.find('.end-date-input').val();
        // AJAX save of dates
        $.post(EmigratieAjax.ajax_url, {
            action: 'ep_save_task_dates',
            task_id: taskId,
            start_date: startDate,
            end_date: endDate,
            _ajax_nonce: EmigratieAjax.nonce
        }, function(response) {
            if (response.success) {
                // Update displayed dates text
                $dates.find('.start-date-value').text(startDate || EmigratieAjax.not_set_text);
                $dates.find('.end-date-value').text(endDate || EmigratieAjax.not_set_text);
            }
        });
        // Hide inputs and show the static text and edit link again
        $dates.find('.save-dates').hide();
        $dates.find('.start-date-value, .end-date-value').show();
        $dates.find('.edit-dates').show();
    });
});

jQuery(document).ready(function ($) {
    function loadTasks(categoryId) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_tasks',
                category_id: categoryId
            },
            success: function (response) {
                $('#task-list').html(response);
            }
        });
    }

    $('.category-item').click(function () {
        var categoryId = $(this).data('id');
        loadTasks(categoryId);
    });
});
