jQuery(document).ready(function($) {
    // Show/hide fields based on background type
    function toggleBackgroundFields() {
        var type = $('#background_type').val();
        
        // Hide all background-specific fields first
        $('.aab-field-wrap[id*="gradient_"]').hide();
        $('.aab-field-wrap[id*="background_image"]').hide();
        $('.aab-field-wrap[id*="background_opacity"]').hide();
        $('.aab-field-wrap[id*="background_color"]').hide();

        // Show relevant fields based on type
        switch(type) {
            case 'color':
                $('.aab-field-wrap[id*="background_color"]').show();
                $('.aab-field-wrap[id*="background_opacity"]').show();
                break;
            case 'gradient':
                $('.aab-field-wrap[id*="gradient_"]').show();
                $('.aab-field-wrap[id*="background_opacity"]').show();
                break;
            case 'image':
                $('.aab-field-wrap[id*="background_image"]').show();
                $('.aab-field-wrap[id*="background_opacity"]').show();
                break;
        }
    }

    // Initialize WordPress media uploader
    var mediaUploader;
    
    $('.aab-upload-image').click(function(e) {
        e.preventDefault();
        
        var button = $(this);
        var wrap = button.closest('.aab-image-upload-wrap');
        var preview = wrap.find('.aab-image-preview');
        var input = wrap.find('input[type="hidden"]');
        var remove = wrap.find('.aab-remove-image');

        // If media uploader exists, open it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create media uploader
        mediaUploader = wp.media({
            title: 'Choose Background Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        // When image is selected
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            input.val(attachment.id);
            preview.find('img').attr('src', attachment.url);
            preview.show();
            remove.show();
        });

        mediaUploader.open();
    });

    // Remove image
    $('.aab-remove-image').click(function(e) {
        e.preventDefault();
        
        var button = $(this);
        var wrap = button.closest('.aab-image-upload-wrap');
        var preview = wrap.find('.aab-image-preview');
        var input = wrap.find('input[type="hidden"]');

        input.val('');
        preview.hide();
        button.hide();
    });

    // Initial toggle on load
    toggleBackgroundFields();

    // Toggle on background type change
    $('#background_type').on('change', toggleBackgroundFields);
});
