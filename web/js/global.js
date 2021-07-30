$(document).ready(function(){
    $("table.table").each(function(){
        var cols = $(this).find("thead tr th");
        $(this).find("tbody tr").each(function(){
            $(this).find("td").each(function(i){
                if (!$(this).data("title")){
                    $(this).attr("data-title", $(cols[i]).text().trim());
                }
            });
        });
    });
    if (typeof $('#user-management-permission-view').attr('id') != "undefined") {
        console.log("script running");
        var firstForm = $(".panel-body form").first();
        firstForm.prepend('<div class="row"><div class="col-sm-12"><input id="search-in-child-permissions" autofocus="off" type="text" class="form-control input-sm" placeholder="buscar"></div></div><hr/>')
        
        $('#search-in-child-permissions').on('change keyup', function(){
            console.log("key pressed");
            var input = $(this);
            //console.log(input.val());
        
            if ( input.val() == '' )
            {
                firstForm.find('label').each(function(){
                    $(this).removeClass('hide');
                });
                return;
            }
            //console.log(firstForm.html());

            firstForm.find('label').each(function(){
                var _t = $(this);
                console.log(_t);
                var labelText = _t[0].innerText;
                //console.log("::::::"+_t.html().indexOf(input.val()));
                
                if (_t.html().indexOf(input.val()) > -1) {
                    _t.closest('label').removeClass('hide');
                    _t.closest('label').next().removeClass('hide');
                    _t.closest('label').next().next().removeClass('hide');
                }
                else
                {
                    _t.closest('label').addClass('hide');
                    _t.closest('label').next().addClass('hide');
                    _t.closest('label').next().next().addClass('hide');
                }
                
            });
        });



        
    } else {
        console.log("body ID doesnt match");
    }
});