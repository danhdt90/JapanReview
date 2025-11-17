// Admin JavaScript for Article Bulk Importer
jQuery(document).ready(function($) {
    'use strict';

    var ArticleBulkImporter = {
        
        init: function() {
            this.bindEvents();
            this.csvFile = null;
            this.isValidated = false;
        },
        
        bindEvents: function() {
            $('#csv_file').on('change', this.handleFileChange.bind(this));
            $('#validate-csv').on('click', this.validateCsv.bind(this));
            $('#book-import-form').on('submit', this.handleImport.bind(this));
            
            // Reset file input khi click vào input (trước khi chọn file)
            // Cho phép chọn lại cùng file sau khi sửa nội dung
            $('#csv_file').on('click', function() {
                this.value = '';
            });
        },
        
        handleFileChange: function(e) {
            var file = e.target.files[0];
            
            if (!file) {
                this.resetForm();
                return;
            }
            
            // Validate file type
            if (file.type !== 'text/csv' && !file.name.toLowerCase().endsWith('.csv')) {
                alert('Please select a CSV file.');
                this.resetForm();
                return;
            }
            
            // Validate file size (2MB)
            if (file.size > 2097152) {
                alert('File is too large. Maximum size is 2MB.');
                this.resetForm();
                return;
            }
            
            this.csvFile = file;
            this.isValidated = false;
            $('#import-books').prop('disabled', true);
            $('#validation-results').hide();
            $('#import-progress').hide();
        },
        
        validateCsv: function(e) {
            e.preventDefault();
            
            if (!this.csvFile) {
                alert('Please select a CSV file first.');
                return;
            }
            
            var formData = new FormData();
            formData.append('action', 'book_validate_csv');
            formData.append('nonce', bookBulkImporter.nonce);
            formData.append('csv_file', this.csvFile);
            
            var $button = $('#validate-csv');
            var originalText = $button.text();
            
            $button.addClass('processing').text('Validating...');
            
            $.ajax({
                url: bookBulkImporter.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: this.handleValidationSuccess.bind(this),
                error: this.handleValidationError.bind(this),
                complete: function() {
                    $button.removeClass('processing').text(originalText);
                }
            });
        },
        
        handleValidationSuccess: function(response) {
            if (response.success) {
                this.isValidated = true;
                $('#import-books').prop('disabled', false);
                this.displayValidationResults(response.data);
            } else {
                this.handleValidationError(response);
            }
        },
        
        handleValidationError: function(response) {
            this.isValidated = false;
            $('#import-books').prop('disabled', true);
            
            var message = 'Validation failed.';
            if (response.data && response.data.message) {
                message = response.data.message;
            } else if (response.data) {
                message = response.data;
            }
            
            this.displayValidationResults({
                success: false,
                message: message,
                errors: response.data && response.data.errors ? response.data.errors : []
            });
        },
        
        displayValidationResults: function(data) {
            var $container = $('#validation-content');
            var html = '';
            
            if (data.success) {
                html += '<div class="validation-summary">';
                html += '<h4>✓ Validation Successful</h4>';
                html += '<p><strong>File:</strong> ' + this.csvFile.name + '</p>';
                html += '<p><strong>Total rows:</strong> ' + data.row_count + '</p>';
                html += '<p><strong>Headers found:</strong> ' + data.headers.join(', ') + '</p>';
                html += '</div>';
                
                // Show warnings if any
                if (data.validation_details && data.validation_details.warnings && data.validation_details.warnings.length > 0) {
                    html += '<div class="validation-warnings">';
                    html += '<h4>⚠ Warnings</h4>';
                    html += '<ul>';
                    data.validation_details.warnings.forEach(function(warning) {
                        html += '<li>' + warning + '</li>';
                    });
                    html += '</ul>';
                    html += '</div>';
                }
                
                // Show preview
                if (data.preview && data.preview.length > 0) {
                    html += '<h4>Data Preview (first 5 rows):</h4>';
                    html += this.generatePreviewTable(data.headers, data.preview);
                }
                
            } else {
                html += '<div class="validation-errors">';
                html += '<h4>✗ Validation Failed</h4>';
                html += '<p>' + data.message + '</p>';
                
                if (data.errors && data.errors.length > 0) {
                    html += '<ul>';
                    data.errors.forEach(function(error) {
                        html += '<li>' + error + '</li>';
                    });
                    html += '</ul>';
                }
                html += '</div>';
            }
            
            $container.html(html);
            $('#validation-results').show();
        },
        
        generatePreviewTable: function(headers, data) {
            var html = '<table class="preview-table">';
            
            // Headers
            html += '<thead><tr>';
            headers.forEach(function(header) {
                html += '<th>' + header + '</th>';
            });
            html += '</tr></thead>';
            
            // Data rows
            html += '<tbody>';
            data.forEach(function(row) {
                html += '<tr>';
                headers.forEach(function(header) {
                    var cellData = row[header] || '';
                    if (cellData.length > 50) {
                        cellData = cellData.substring(0, 47) + '...';
                    }
                    html += '<td>' + $('<div>').text(cellData).html() + '</td>';
                });
                html += '</tr>';
            });
            html += '</tbody>';
            
            html += '</table>';
            return html;
        },
        
        handleImport: function(e) {
            e.preventDefault();
            
            if (!this.isValidated) {
                alert('Please validate the CSV file first.');
                return;
            }
            
            var formData = new FormData();
            formData.append('action', 'book_bulk_import');
            formData.append('nonce', bookBulkImporter.nonce);
            formData.append('csv_file', this.csvFile);
            formData.append('update_existing', $('#update_existing').is(':checked') ? '1' : '0');
            formData.append('dry_run', $('#dry_run').is(':checked') ? '1' : '0');
            
            this.startImportProgress();
            
            $.ajax({
                url: bookBulkImporter.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: this.handleImportSuccess.bind(this),
                error: this.handleImportError.bind(this),
                complete: function() {
                    // Reset form sau khi import hoàn tất (thành công hoặc thất bại)
                    // Cho phép chọn file mới hoặc chọn lại file đã sửa
                    setTimeout(function() {
                        $('#csv_file').val('');
                    }, 100);
                }
            });
        },
        
        startImportProgress: function() {
            $('#import-progress').show();
            $('#progress-fill').css('width', '0%');
            $('#import-status').removeClass('success error').addClass('processing').text('Starting import...');
            $('#import-log').empty();
            
            // Simulate progress for better UX
            this.animateProgress();
        },
        
        animateProgress: function() {
            var $progressFill = $('#progress-fill');
            var currentWidth = 0;
            
            this.progressInterval = setInterval(function() {
                currentWidth += Math.random() * 10;
                if (currentWidth > 90) {
                    currentWidth = 90;
                    clearInterval(this.progressInterval);
                }
                $progressFill.css('width', currentWidth + '%');
            }.bind(this), 200);
        },
        
        handleImportSuccess: function(response) {
            clearInterval(this.progressInterval);
            $('#progress-fill').css('width', '100%');
            
            if (response.success) {
                var isDryRun = response.data.dry_run;
                var statusText = isDryRun ? 'Validation Complete' : 'Import Complete';
                
                $('#import-status').removeClass('processing error').addClass('success').text(statusText);
                
                // Display summary
                var summaryHtml = '<div class="log-entry success">';
                summaryHtml += '<strong>Summary:</strong> ' + response.data.message;
                summaryHtml += '</div>';
                
                if (!isDryRun) {
                    summaryHtml += '<div class="log-entry info">';
                    summaryHtml += 'Imported: ' + response.data.imported + ' | ';
                    summaryHtml += 'Updated: ' + response.data.updated + ' | ';
                    summaryHtml += 'Errors: ' + response.data.errors;
                    summaryHtml += '</div>';
                }
                
                $('#import-log').append(summaryHtml);
                
                // Display detailed log
                if (response.data.log && response.data.log.length > 0) {
                    response.data.log.forEach(function(logEntry) {
                        var logClass = logEntry.toLowerCase().includes('error') ? 'error' : 
                                     logEntry.toLowerCase().includes('created') || logEntry.toLowerCase().includes('updated') ? 'success' : 'info';
                        
                        $('#import-log').append('<div class="log-entry ' + logClass + '">' + logEntry + '</div>');
                    });
                }
                
            } else {
                this.handleImportError(response);
            }
            
            // Scroll log to bottom
            var $log = $('#import-log');
            $log.scrollTop($log[0].scrollHeight);
        },
        
        handleImportError: function(response) {
            clearInterval(this.progressInterval);
            $('#progress-fill').css('width', '100%');
            
            var message = 'Import failed. Please try again.';
            if (response.data && response.data.message) {
                message = response.data.message;
            } else if (response.data) {
                message = response.data;
            }
            
            $('#import-status').removeClass('processing success').addClass('info').text('Import Complete');
            $('#import-log').append('<div class="log-entry info">' + message + '</div>');
        },
        
        resetForm: function() {
            // Clear file input completely
            var $fileInput = $('#csv_file');
            $fileInput.val('');
            
            // Clear stored file reference
            this.csvFile = null;
            this.isValidated = false;
            
            // Reset UI state
            $('#import-books').prop('disabled', true);
            $('#validation-results').hide();
            $('#import-progress').hide();
            
            // Clear progress interval if exists
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
                this.progressInterval = null;
            }
        }
    };
    
    // Initialize
    ArticleBulkImporter.init();
    
    // Download sample CSV
    $(document).on('click', '.download-sample-csv', function(e) {
        e.preventDefault();
        
        var format = $(this).data('format') || 'indexed';
        var button = $(this);
        var originalText = button.text();
        
        // Show loading state
        button.text('Generating...').attr('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'book_download_sample_csv',
                format: format,
                nonce: $('#book_import_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    // Create and download file
                    var csvContent = "data:text/csv;charset=utf-8," + encodeURIComponent(response.data.content);
                    var link = document.createElement("a");
                    link.setAttribute("href", csvContent);
                    link.setAttribute("download", response.data.filename);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    alert('Error generating CSV: ' + response.data);
                }
            },
            error: function() {
                alert('Error downloading sample CSV file');
            },
            complete: function() {
                // Restore button state
                button.text(originalText).attr('disabled', false);
            }
        });
    });
});