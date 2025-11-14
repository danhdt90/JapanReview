/**
 * Custom Number Field Validation for Article Post Type
 * Prevents non-numeric input in Pods number fields (volume, start_page, end_page)
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        console.log('Article number field validation loaded');
        
        // Selectors for Pods number fields
        var numberFieldSelectors = [
            'input[name*="pods_meta_volume"]',
            'input[name*="pods_meta_start_page"]',
            'input[name*="pods_meta_end_page"]',
            'input.pods-form-ui-field-type-number',
            '.pods-field-type-number input[type="text"]',
            '.pods-field-type-number input[type="number"]'
        ].join(', ');
        
        /**
         * Prevent non-numeric characters on keypress
         */
        $(document).on('keypress', numberFieldSelectors, function(e) {
            var char = String.fromCharCode(e.which);
            var value = $(this).val();
            
            // Allow control keys
            var allowedKeys = [
                8,   // Backspace
                9,   // Tab
                13,  // Enter
                37,  // Arrow Left
                38,  // Arrow Up
                39,  // Arrow Right
                40,  // Arrow Down
                46   // Delete
            ];
            
            if (allowedKeys.indexOf(e.which) !== -1) {
                return true;
            }
            
            // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+Z
            if (e.ctrlKey || e.metaKey) {
                return true;
            }
            
            // Allow digits (0-9)
            if (char >= '0' && char <= '9') {
                return true;
            }
            
            // Allow decimal point (.) only once
            if (char === '.' && value.indexOf('.') === -1) {
                return true;
            }
            
            // Silently prevent the character from being entered (no warning)
            e.preventDefault();
            return false;
        });
        
        /**
         * Clean pasted content and any input change
         */
        $(document).on('paste input', numberFieldSelectors, function(e) {
            var $input = $(this);
            
            setTimeout(function() {
                var value = $input.val();
                
                // Remove all non-numeric characters except dot (no minus sign)
                var cleaned = value.replace(/[^0-9.]/g, '');
                
                // Ensure only one decimal point
                var parts = cleaned.split('.');
                if (parts.length > 2) {
                    cleaned = parts[0] + '.' + parts.slice(1).join('');
                }
                
                // Silently update value if it was changed (no notification)
                if (value !== cleaned) {
                    $input.val(cleaned);
                }
            }, 10);
        });
        
        /**
         * Clean and format on blur (when field loses focus)
         */
        $(document).on('blur', numberFieldSelectors, function() {
            var $input = $(this);
            var value = $input.val();
            
            // Skip if field is empty
            if (value === '') {
                return;
            }
            
            // Clean any remaining non-numeric characters (no minus sign)
            var cleaned = value.replace(/[^0-9.]/g, '');
            
            // Ensure only one decimal point
            var parts = cleaned.split('.');
            if (parts.length > 2) {
                cleaned = parts[0] + '.' + parts.slice(1).join('');
            }
            
            // Handle incomplete values
            if (cleaned === '.' || cleaned === '') {
                $input.val('');
                return;
            }
            
            // Format the number if it's valid (always positive)
            var numValue = parseFloat(cleaned);
            if (!isNaN(numValue) && numValue >= 0) {
                $input.val(numValue.toString());
            } else {
                // If still not a valid positive number, clear it
                $input.val('');
            }
        });
        
        /**
         * Clean all number fields before form submission
         */
        $('#post').on('submit', function(e) {
            $(numberFieldSelectors).each(function() {
                var $input = $(this);
                var value = $input.val();
                
                // Skip empty fields
                if (!value) {
                    return true; // continue
                }
                
                // Clean any remaining non-numeric characters (no minus sign)
                var cleaned = value.replace(/[^0-9.]/g, '');
                
                // Ensure only one decimal point
                var parts = cleaned.split('.');
                if (parts.length > 2) {
                    cleaned = parts[0] + '.' + parts.slice(1).join('');
                }
                
                // Handle incomplete or invalid values
                if (cleaned === '.' || cleaned === '') {
                    $input.val('');
                    return true; // continue
                }
                
                // Format the number if it's valid (always positive)
                var numValue = parseFloat(cleaned);
                if (!isNaN(numValue) && numValue >= 0) {
                    $input.val(numValue.toString());
                } else {
                    // If still not valid or negative, clear it
                    $input.val('');
                }
            });
            
            // Always allow form submission (no blocking)
            return true;
        });
        
        // Log initialization
        console.log('validate init');
        
    });
    
})(jQuery);
