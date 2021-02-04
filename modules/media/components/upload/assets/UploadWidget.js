var UploadWidget = new function()
{
    
    this.init = function(options)
    {
        $(function () {
            'use strict';
            // Change this to the location of your server-side upload handler:
            var url = options.url;
            
            $('#'+options.inputId).fileupload({
                url: url,
                dataType: 'json',
                done: function (e, data) {
                    if(data.result.status == 'success'){
                        $('[data-media-preview-list]').append(data.result.preview);
                    }else{
                        for(e in data.result.errors){
                            $('[data-messages]').append('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span></button>'+data.result.errors[e]+'</div>');
                        }
                    }
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css(
                        'width',
                        progress + '%'
                    );
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
        });
        
        $('[data-media-preview-list]').on('click', '[data-media-delete]', function(event){ deleteMedia(event, this); });
        
        $('[data-media-preview-list]').sortable();
    }
    
    function deleteMedia(event, element)
    {
        
        $(element).closest('[data-media]').hide(200, function(){ $(this).remove(); });
        
    }
    
}