jQuery(document).ready(function($) {
    function logError(action, error) {
        console.error(`Error in ${action}:`, error);
    }

    // -------------------------------
    // CATEGORIEËN
    // -------------------------------

    // Toon categorieformulier
    $('#add-category-btn').on('click', function() {
        $('#category-form').show();
    });

    // Verwijder categorie
    $('.delete-category-btn').on('click', function() {
        if (!confirm('Weet je zeker dat je deze categorie wilt verwijderen?')) {
            return;
        }
        const data = {
            action: 'ep_delete_personal_category',
            category_id: $(this).data('id')
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

    // Importeer admincategorieën
    $('#import-admin-categories-btn').on('click', function() {
        const data = { action: 'ep_import_admin_categories' };
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

    // Maak categorie-elementen draggable met jQuery UI
    $('.category-link, .delete-category-btn').draggable({
        revert: true,
        cursor: 'move',
        helper: 'clone'
    });

    // -------------------------------
    // TAKEN
    // -------------------------------

    // Voeg taak toe: maak formulier leeg en toon het
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

    // Sla taak op via wijzigen in invoervelden (formulier met FormData voor file uploads)
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
        formData.append('ordering', $('#task-ordering').val() || 0);
        if ($('#task-attachment')[0].files[0]) {
            formData.append('attachment', $('#task-attachment')[0].files[0]);
        }

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
            task_id: $(this).data('id')
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

    // Sla wijzigingen in taak op vanuit een taak-kolom (inline bewerken)
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
        formData.append('ordering', taskColumn.find('.task-ordering').val() || 0);
        if (taskColumn.find('.task-attachment')[0].files[0]) {
            formData.append('attachment', taskColumn.find('.task-attachment')[0].files[0]);
        }
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

    // Laad taken voor een specifieke categorie
    $('.category-link').on('click', function(e) {
        e.preventDefault();
        const categoryId = $(this).data('id');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'ep_get_tasks_by_category',
                category_id: categoryId
            },
            success: function(response) {
                if (response && response.success) {
                    const tasks = response.data;
                    $('#user-task-list').empty();
                    tasks.forEach(function(task) {
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
            },
            error: function(jqXHR, textStatus, errorThrown) {
                logError('ep_get_tasks_by_category', errorThrown);
            }
        });
    });

    // Toon alle taken (pagina herladen)
    $('#show-all-tasks-btn').on('click', function() {
        location.reload();
    });

    // Extra: Een knop voor het opslaan van een taak buiten inline bewerken
    $('#save-task-button').on('click', function() {
        const taskData = {
            title: $('#task-title').val(),
            status: $('#task-status').val(),
            priority: $('#task-priority').val(),
            start_date: $('#task-start-date').val(),
            deadline: $('#task-deadline').val(),
            notes: $('#task-notes').val(),
            ordering: $('#task-ordering').val() || 0
        };
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'ep_save_personal_task',
                task_data: taskData
            },
            success: function(response) {
                if (response && response.success) {
                    alert('Taak succesvol opgeslagen.');
                } else {
                    logError('ep_save_personal_task', response && response.data ? response.data.message : 'Unknown error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                logError('ep_save_personal_task', errorThrown);
            }
        });
    });

    // Extra: Voeg categorie toe via een aparte knop met enkel de naam
    $('#add-category-button').on('click', function() {
        const categoryName = $('#category-name').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'add_category',
                name: categoryName
            },
            success: function(response) {
                if (response && response.success) {
                    alert('Categorie succesvol toegevoegd.');
                } else {
                    logError('add_category', response && response.data ? response.data.message : 'Unknown error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                logError('add_category', errorThrown);
            }
        });
    });
});