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
});