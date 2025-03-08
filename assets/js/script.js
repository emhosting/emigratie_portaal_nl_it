jQuery(document).ready(function($) {
    // Definieer ajaxurl
    var ajaxurl = ajaxurl || '';

    function logError(action, error) {
        console.error(`Error in ${action}:`, error);
    }

    // Voeg categorie toe
    $('#add-category-btn').on('click', function() {
        $('#category-form').show();
        $('#category-id').val('');
        $('#category-name').val('');
        $('#category-ordering').val('');
    });

    // Bewerk categorie
    $('.edit-category-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).siblings('.category-link').text();
        const ordering = $(this).siblings('.category-link').data('ordering');
        $('#category-id').val(id);
        $('#category-name').val(name);
        $('#category-ordering').val(ordering);
        $('#category-form').show();
    });

    // Sla categorie op
    $('#category-name, #category-ordering').on('change', function() {
        const data = {
            action: 'ep_save_personal_category',
            category_id: $('#category-id').val(),
            name: $('#category-name').val(),
            ordering: $('#category-ordering').val(),
        };

        $.post(ajaxurl, data, function(response) {
            if (response && response.success) {
                console.log('Categorie succesvol opgeslagen.');
            } else {
                logError('ep_save_personal_category', response && response.data ? response.data.message : 'Unknown error');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            logError('ep_save_personal_category', errorThrown);
        });
    });

    // Verwijder categorie
    $('.delete-category-btn').on('click', function() {
        if (!confirm('Weet je zeker dat je deze categorie wilt verwijderen?')) {
            return;
        }
        const data = {
            action: 'ep_delete_personal_category',
            category_id: $(this).data('id'),
        };

        $.post(ajaxurl, data, function(response) {
            if (response && response.success) {
                console.log('Categorie succesvol verwijderd.');
                location.reload();
            } else {
                logError('ep_delete_personal_category', response && response.data ? response.data.message : 'Unknown error');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            logError('ep_delete_personal_category', errorThrown);
        });
    });

    // Importeer admin categorieën
    $('#import-admin-categories-btn').on('click', function() {
        const data = {
            action: 'ep_import_admin_categories',
        };

        $.post(ajaxurl, data, function(response) {
            if (response && response.success) {
                console.log('Admin categorieën succesvol geïmporteerd.');
                location.reload();
            } else {
                logError('ep_import_admin_categories', response && response.data ? response.data.message : 'Unknown error');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            logError('ep_import_admin_categories', errorThrown);
        });
    });

    // Voeg taak toe
    $('#add-task-btn').on('click', function() {
        $('#task-form').show();
        $('#task-id').val('');
        $('#task-title').val('');
        $('#task-status').val('');
        $('#task-priority').val('');
        $('#task-start-date').val('');
        $('#task-deadline').val('');
        $('#task-notes').val('');
        $('#task-ordering').val('');
        $('#task-attachment').val('');
    });

    // Sla taak op
    $('#task-title, #task-status, #task-priority, #task-start-date, #task-deadline, #task-notes, #task-ordering').on('change', function() {
        const formData = new FormData();
        formData.append('action', 'ep_save_personal_task');
        formData.append('task_id', $('#task-id').val());
        formData.append('title', $('#task-title').val());
        formData.append('status', $('#task-status').val());
        formData.append('priority', $('#task-priority').val());
        formData.append('start_date', $('#task-start-date').val());
        formData.append('deadline', $('#task-deadline').val());
        formData.append('notes', $('#task-notes').val());
        formData.append('ordering', $('#task-ordering').val());
        formData.append('attachment', $('#task-attachment')[0].files[0]);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response && response.success) {
                    console.log('Taak succesvol opgeslagen.');
                } else {
                    logError('ep_save_personal_task', response && response.data ? response.data.message : 'Unknown error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                logError('ep_save_personal_task', errorThrown);
            }
        });
    });

    // Verwijder taak
    $('#user-task-list').on('click', '.delete-task-btn', function() {
        if (!confirm('Weet je zeker dat je deze taak wilt verwijderen?')) {
            return;
        }
        const data = {
            action: 'ep_delete_personal_task',
            task_id: $(this).data('id'),
        };

        $.post(ajaxurl, data, function(response) {
            if (response && response.success) {
                console.log('Taak succesvol verwijderd.');
                location.reload();
            } else {
                logError('ep_delete_personal_task', response && response.data ? response.data.message : 'Unknown error');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            logError('ep_delete_personal_task', errorThrown);
        });
    });

    // Sla wijzigingen in taak op vanuit kolom
    $('#user-task-list').on('change', '.task-title, .task-status, .task-priority, .task-start-date, .task-deadline, .task-notes, .task-ordering', function() {
        const taskColumn = $(this).closest('.task-column');
        const formData = new FormData();
        formData.append('action', 'ep_save_personal_task');
        formData.append('task_id', taskColumn.data('id'));
        formData.append('title', taskColumn.find('.task-title').val());
        formData.append('status', taskColumn.find('.task-status').val());
        formData.append('priority', taskColumn.find('.task-priority').val());
        formData.append('start_date', taskColumn.find('.task-start-date').val());
        formData.append('deadline', taskColumn.find('.task-deadline').val());
        formData.append('notes', taskColumn.find('.task-notes').val());
        formData.append('ordering', taskColumn.find('.task-ordering').val());
        formData.append('attachment', taskColumn.find('.task-attachment')[0].files[0]);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response && response.success) {
                    console.log('Taak succesvol opgeslagen.');
                } else {
                    logError('ep_save_personal_task', response && response.data ? response.data.message : 'Unknown error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                logError('ep_save_personal_task', errorThrown);
            }
        });
    });

    // Toon taken van een specifieke categorie
    $('.category-link').on('click', function(e) {
        e.preventDefault();
        const categoryId = $(this).data('id');
        const data = {
            action: 'ep_get_tasks_by_category',
            category_id: categoryId,
        };

        $.post(ajaxurl, data, function(response) {
            if (response && response.success) {
                const tasks = response.data;
                $('#user-task-list').empty();

                tasks.forEach(task => {
                    const taskColumn = `
                        <div class="task-column" data-id="${task.id}" data-category-id="${task.category_id}">
                            <input type="text" class="task-title" value="${task.title}">
                            <select class="task-status">
                                ${['Not Started', 'In Progress', 'Completed'].map(status => `
                                    <option value="${status}" ${task.status === status ? 'selected' : ''}>${status}</option>
                                `).join('')}
                            </select>
                            <select class="task-priority">
                                ${['Low', 'Medium', 'High'].map(priority => `
                                    <option value="${priority}" ${task.priority === priority ? 'selected' : ''}>${priority}</option>
                                `).join('')}
                            </select>
                            <textarea class="task-notes">${task.notes}</textarea>
                            <input type="date" class="task-start-date" value="${task.start_date}">
                            <input type="date" class="task-deadline" value="${task.deadline}">
                            <input type="file" class="task-attachment">
                            <button class="delete-task-btn button" data-id="${task.id}">Verwijder</button>
                        </div>
                    `;
                    $('#user-task-list').append(taskColumn);
                });
            } else {
                logError('ep_get_tasks_by_category', response && response.data ? response.data.message : 'Unknown error');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            logError('ep_get_tasks_by_category', errorThrown);
        });
    });

    // Toon alle taken
    $('#show-all-tasks-btn').on('click', function() {
        location.reload();
    });
});