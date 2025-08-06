jQuery(document).ready(function($) {
    'use strict';
    
    // Handle schedule fields visibility
    function toggleScheduleFields() {
        var isEnabled = $('#enable_schedule').val() === 'yes';
        $('.aab-field-wrap[id*="schedule_"]').toggle(isEnabled);
    }
    
    $('#enable_schedule').on('change', toggleScheduleFields).trigger('change');

    // Initialize color pickers
    $('.aab-color-picker').wpColorPicker();

    // Handle background type changes
    $('#background_type').on('change', function() {
        var type = $(this).val();
        var $colorField = $('#background_color').closest('.aab-field-wrap');
        var $gradientFields = $('#gradient_start_color, #gradient_end_color, #gradient_direction')
            .closest('.aab-field-wrap');
        var $imageField = $('#background_image').closest('.aab-field-wrap');

        // Hide all fields first
        $colorField.hide();
        $gradientFields.hide();
        $imageField.hide();

        // Show relevant fields based on type
        switch(type) {
            case 'color':
                $colorField.show();
                break;
            case 'gradient':
                $gradientFields.show();
                break;
            case 'image':
                $imageField.show();
                break;
        }
    }).trigger('change');

    // Handle image upload
    $('.aab-upload-image').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $wrap = $button.closest('.aab-image-upload-wrap');
        var $input = $wrap.find('input[type="hidden"]');
        var $preview = $wrap.find('.aab-image-preview');
        var $removeButton = $wrap.find('.aab-remove-image');

        var frame = wp.media({
            title: aabAdmin.mediaTitle,
            button: {
                text: aabAdmin.mediaButton
            },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            
            // Update input and preview immediately
            $input.val(attachment.id);
            
            // Make sure we use the best available size
            var imageUrl = attachment.sizes.medium ? 
                          attachment.sizes.medium.url : 
                          (attachment.sizes.full ? attachment.sizes.full.url : attachment.url);
            
            $preview.find('img').attr('src', imageUrl);
            $preview.show();
            $removeButton.show();

            // Trigger change event to ensure form state is updated
            $input.trigger('change');
        });

        frame.open();
    });

    // Handle image removal
    $('.aab-remove-image').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $wrap = $button.closest('.aab-image-upload-wrap');
        var $input = $wrap.find('input[type="hidden"]');
        var $preview = $wrap.find('.aab-image-preview');

        $input.val('');
        $preview.hide();
        $button.hide();
    });
});
