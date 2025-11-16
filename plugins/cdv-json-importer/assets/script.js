jQuery(document).ready(function($) {
    let jsonData = null;
    let activitiesData = null;

    // Upload Area - Drag & Drop
    const uploadArea = $('#upload-area');
    const fileInput = $('#json-file');

    uploadArea.on('click', function() {
        fileInput.click();
    });

    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('drag-over');
    });

    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
    });

    uploadArea.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');

        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    fileInput.on('change', function() {
        if (this.files.length > 0) {
            handleFile(this.files[0]);
        }
    });

    // Handle File
    function handleFile(file) {
        if (!file.name.endsWith('.json')) {
            alert('Per favore seleziona un file JSON valido');
            return;
        }

        if (file.size > 10 * 1024 * 1024) { // 10MB
            alert('Il file è troppo grande. Massimo 10MB');
            return;
        }

        $('#file-name').text(file.name);
        $('#file-size').text(formatFileSize(file.size));
        $('#upload-area').hide();
        $('#file-info').show();

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                jsonData = e.target.result;
                JSON.parse(jsonData); // Validate JSON
            } catch (error) {
                alert('Errore: il file non contiene JSON valido');
                resetUpload();
            }
        };
        reader.readAsText(file);
    }

    // Format File Size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Remove File
    $('#remove-file').on('click', function() {
        resetUpload();
    });

    function resetUpload() {
        jsonData = null;
        activitiesData = null;
        fileInput.val('');
        $('#file-info').hide();
        $('#upload-area').show();
    }

    // Validate Button
    $('#validate-btn').on('click', function() {
        if (!jsonData) {
            alert('Nessun file caricato');
            return;
        }

        $('#upload-progress').show();
        $('.progress-text').text('Validazione in corso...');

        $.ajax({
            url: cdvImporter.ajaxurl,
            type: 'POST',
            data: {
                action: 'cdv_validate_json',
                nonce: cdvImporter.nonce,
                json_data: jsonData
            },
            success: function(response) {
                $('#upload-progress').hide();

                if (response.success) {
                    activitiesData = JSON.parse(jsonData);
                    showPreview(response.data);
                } else {
                    alert('Errore: ' + response.data.message);
                }
            },
            error: function() {
                $('#upload-progress').hide();
                alert('Errore di comunicazione con il server');
            }
        });
    });

    // Show Preview
    function showPreview(data) {
        $('#step-upload').hide();
        $('#step-preview').show();

        // Summary
        $('#total-count').text(data.total);
        $('#valid-count').text(data.valid);
        $('#error-count').text(data.invalid);
        $('#preview-summary').show();

        // Validation message
        if (data.invalid > 0) {
            const notice = $('<div class="notice notice-warning"><p><strong>Attenzione:</strong> ' +
                data.invalid + ' annunci contengono errori e non verranno importati.</p></div>');
            $('#validation-results').html(notice);
        } else {
            const notice = $('<div class="notice notice-success"><p><strong>Ottimo!</strong> ' +
                'Tutti gli annunci sono validi e pronti per l\'importazione.</p></div>');
            $('#validation-results').html(notice);
        }

        // Preview items
        const previewHtml = data.previews.map((item, index) => {
            const validClass = item.valid ? 'is-valid' : 'has-errors';
            const badge = item.valid ?
                '<span class="preview-badge badge-valid">✓ Valido</span>' :
                '<span class="preview-badge badge-error">✗ Errori</span>';

            let errorsHtml = '';
            if (!item.valid && item.errors.length > 0) {
                errorsHtml = `
                    <div class="preview-errors">
                        <h4>Errori trovati:</h4>
                        <ul>
                            ${item.errors.map(err => '<li>' + err + '</li>').join('')}
                        </ul>
                    </div>
                `;
            }

            return `
                <div class="preview-item ${validClass}">
                    <div class="preview-header">
                        <h3 class="preview-title">#${index + 1} - ${item.title}</h3>
                        ${badge}
                    </div>
                    <div class="preview-meta">
                        <span>📍 ${item.destination}</span>
                        <span>📊 ${item.level}</span>
                        <span>💰 €${item.budget}</span>
                        <span>📅 ${item.date_type === 'precise' ? 'Date precise' : 'Mese flessibile'}</span>
                    </div>
                    ${errorsHtml}
                </div>
            `;
        }).join('');

        $('#preview-content').html(previewHtml);
        $('#preview-actions').show();
    }

    // Back to Upload
    $('#back-to-upload').on('click', function() {
        $('#step-preview').hide();
        $('#step-upload').show();
    });

    // Start Import
    $('#start-import').on('click', function() {
        if (!activitiesData) {
            alert('Nessun dato da importare');
            return;
        }

        // Filter only valid activities
        const validActivities = activitiesData.filter((activity, index) => {
            const errors = validateActivity(activity);
            return errors.length === 0;
        });

        if (validActivities.length === 0) {
            alert('Nessun annuncio valido da importare');
            return;
        }

        $('#step-preview').hide();
        $('#step-import').show();

        startImport(validActivities);
    });

    // Start Import Process
    function startImport(activities) {
        let batchIndex = 0;
        let importedCount = 0;
        let errorCount = 0;
        const total = activities.length;

        $('#import-status').text('Importazione in corso...');
        $('#import-detail').text('0 di ' + total + ' annunci importati');

        function importBatch() {
            $.ajax({
                url: cdvImporter.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_import_activities',
                    nonce: cdvImporter.nonce,
                    activities: JSON.stringify(activities),
                    batch_index: batchIndex
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;

                        // Update log
                        data.results.forEach(result => {
                            const logClass = result.success ? 'log-success' : 'log-error';
                            const icon = result.success ? '✓' : '✗';
                            const message = result.success ?
                                'Importato: ' + result.title + ' (ID: ' + result.post_id + ')' :
                                'Errore: ' + result.title + ' - ' + result.error;

                            $('#import-log').append(
                                '<div class="log-entry ' + logClass + '">' + icon + ' ' + message + '</div>'
                            );

                            if (result.success) {
                                importedCount++;
                            } else {
                                errorCount++;
                            }
                        });

                        // Update progress
                        const progress = (data.processed / data.total) * 100;
                        $('#import-progress-fill').css('width', progress + '%');
                        $('#import-detail').text(data.processed + ' di ' + data.total + ' annunci processati');

                        // Scroll log to bottom
                        $('#import-log').scrollTop($('#import-log')[0].scrollHeight);

                        // Continue or complete
                        if (data.has_more) {
                            batchIndex = data.next_batch;
                            setTimeout(importBatch, 500); // Small delay between batches
                        } else {
                            completeImport(importedCount, errorCount, total);
                        }
                    } else {
                        alert('Errore durante l\'importazione: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('Errore di comunicazione con il server');
                }
            });
        }

        importBatch();
    }

    // Complete Import
    function completeImport(imported, errors, total) {
        $('#step-import').hide();
        $('#step-complete').show();

        const summaryHtml = `
            <div class="summary-stats">
                <div class="stat-box stat-total">
                    <span class="stat-number">${total}</span>
                    <span class="stat-label">Totale Processati</span>
                </div>
                <div class="stat-box stat-valid">
                    <span class="stat-number">${imported}</span>
                    <span class="stat-label">Importati con Successo</span>
                </div>
                <div class="stat-box stat-errors">
                    <span class="stat-number">${errors}</span>
                    <span class="stat-label">Errori</span>
                </div>
            </div>
        `;

        $('#import-summary').html(summaryHtml);

        if (errors === 0) {
            $('#import-summary').prepend(
                '<h3>🎉 Tutti gli annunci sono stati importati con successo!</h3>'
            );
        } else {
            $('#import-summary').prepend(
                '<h3>✅ Importazione completata con alcuni errori</h3>' +
                '<p>Controlla il log sopra per i dettagli degli errori.</p>'
            );
        }
    }

    // Import More
    $('#import-more').on('click', function() {
        location.reload();
    });

    // Validate Activity (client-side)
    function validateActivity(activity) {
        const errors = [];

        const requiredFields = [
            'activity_title', 'activity_description', 'activity_destination',
            'activity_country', 'date_type', 'activity_time', 'activity_duration',
            'activity_budget', 'activity_max_participants', 'activity_level'
        ];

        requiredFields.forEach(field => {
            if (!activity[field] && activity[field] !== '0') {
                errors.push(field + ' mancante');
            }
        });

        return errors;
    }
});
