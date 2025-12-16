// admin.js - Custom admin scripts

// Initialize tooltips
$(document).ready(function() {
    // Enable tooltips everywhere
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Enable popovers everywhere
    $('[data-bs-toggle="popover"]').popover();
    
    // Initialize select2
    $('.select2').select2({
        theme: 'bootstrap-5'
    });
    
    // Initialize datepicker
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });
    
    // Initialize summernote
    $('.summernote').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'hr']],
            ['view', ['fullscreen', 'codeview']],
            ['help', ['help']]
        ]
    });
});