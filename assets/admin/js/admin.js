jQuery(document).ready(function($) {
    // Initialize color pickers
    $('.wnb-color-picker').wpColorPicker();

    // Initialize Media Uploader
    var mediaUploader;
    
    $('.wnb-upload-image').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var wrapper = button.closest('.wnb-image-upload-wrap');
        var imageInput = wrapper.find('input[type="hidden"]');
        var previewDiv = wrapper.find('.wnb-image-preview');
        var removeButton = wrapper.find('.wnb-remove-image');
        
        // If the media uploader already exists, reopen it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        // Create the media uploader
        mediaUploader = wp.media({
            title: wnbAdmin.mediaTitle,
            button: {
                text: wnbAdmin.mediaButton
            },
            multiple: false
        });
        
        // When an image is selected
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            
            // Set the hidden input value
            imageInput.val(attachment.id);
            
            // Update preview
            var imgUrl = attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
            previewDiv.html('<img src="' + imgUrl + '" style="max-width: 100%; height: auto;">');
            previewDiv.show();
            
            // Show remove button
            removeButton.show();
        });
        
        // Open the uploader
        mediaUploader.open();
    });
    
    // Handle image removal
    $('.wnb-remove-image').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var wrapper = button.closest('.wnb-image-upload-wrap');
        var imageInput = wrapper.find('input[type="hidden"]');
        var previewDiv = wrapper.find('.wnb-image-preview');
        
        // Clear the input and preview
        imageInput.val('');
        previewDiv.empty().hide();
        button.hide();
    });

    // Handle background type dependencies
    function handleBackgroundDependencies() {
        var selectedType = $('#background_type').val();
        
        // Hide all background fields first
        $('.wnb-background-field').closest('tr').hide();
        
        // Show fields based on selected type
        $('.wnb-background-field').each(function() {
            var $field = $(this);
            var depends = $field.data('depends');
            
            if (depends && depends.background_type === selectedType) {
                $field.closest('tr').show();
            }
        });
    }

    // Initial handling of dependencies
    handleBackgroundDependencies();

    // Handle background type changes
    $('#background_type').on('change', handleBackgroundDependencies);

    // Show active tab content
    var showTabContent = function(tabId) {
        // Hide all tab content
        $('.wnb-tab-content').hide();
        
        // Show the selected tab's content
        $('#tab-' + tabId).show();

        // Debug
        console.log('Showing tab:', tabId);
        console.log('Tab content elements:', $('.wnb-tab-content').length);
        console.log('Current tab element:', $('#tab-' + tabId).length);
    };

    // Get the active tab from URL
    var getActiveTab = function() {
        var params = new URLSearchParams(window.location.search);
        return params.get('tab') || 'general';
    };

    // Initialize tabs
    var activeTab = getActiveTab();
    $('.nav-tab[data-tab="' + activeTab + '"]').addClass('nav-tab-active');
    showTabContent(activeTab);

    // Handle tab switching
    $('.wnb-tab-nav .nav-tab').on('click', function(e) {
        e.preventDefault();
        var tab = $(this).data('tab');
        
        // Update URL without reload
        var newUrl = new URL(window.location);
        newUrl.searchParams.set('tab', tab);
        window.history.pushState({}, '', newUrl);

        // Update active tab state
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // Show the selected tab content
        showTabContent(tab);
    });

    // Optional: Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        var tab = getActiveTab();
        $('.nav-tab').removeClass('nav-tab-active');
        $('.nav-tab[data-tab="' + tab + '"]').addClass('nav-tab-active');
        showTabContent(tab);
    });
});
