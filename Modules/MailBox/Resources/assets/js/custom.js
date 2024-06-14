function summernote() { 
    if ($(".summernote").length > 0) {
        $( $(".summernote") ).each(function( index,element ) {
            var id = $(element).attr('id');
            $('#'+id).summernote({
                placeholder: "Write Here… ",
                dialogsInBody: !0,
                tabsize: 2,
                minHeight: 200,
                toolbar: [
                    ['style', ['style']],
                    ["font", ["bold", "italic", "underline", "clear", "strikethrough"]],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                ]
            });
        });
    }
}
