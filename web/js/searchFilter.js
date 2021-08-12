$(document).ready(function () {
    var searchBar = $("#search");

    searchBar.on("keyup", function (event) {
        var inputValue = this.value;
        var srcStrHtml = $("ul#w0").find("li");

        if (inputValue.length > 0) {
            
            srcStrHtml.hide();
            var srcStrHtml = srcStrHtml.filter(function () {
                
                var text = $(this).text();
                text = removeAccents(text);
                text = text.toLowerCase();
                return (
                    //indexOf returns the index of the first occurrence inside a string. if not found, -1.
                    text.indexOf(searchBar.val().toLowerCase()) != -1
                );
            });
            srcStrHtml.show();
            
        } else {
            srcStrHtml.show();
            srcStrHtml.each(function(){
                $(this).removeClass("highlight");
            });
        }
    });


    // Remove tildes from text filtering
    const removeAccents = (str) => {
        const regex = /[\u0300-\u036f]/g;
        return str.normalize("NFD").replace(regex, "");
    } 

    // CSS logic
    const icon = $('.search-icon');
    const search = $('.search-container');
    var locked = false;
    const animationTime = 300;
    icon.click(function (evt) {
        evt.stopImmediatePropagation();
        if(!locked){
            locked = true;
            setTimeout(unlock, animationTime);
            // we utilize this function to prevent a propagation error. https://stackoverflow.com/questions/8238599/jquery-click-fires-twice-when-clicking-on-label/49336534
            search.toggleClass('active');
            (search.hasClass('active')) ? setTimeout(() => {$("#search").focus();}, animationTime) : clearSearchBar();        
        }
        
    });
    function unlock() {
        locked = false;
    }


    // clear button logic
    const clearBtn = $('.close-icon').children('span');
    clearBtn.click(function () {clearSearchBar()});

    const clearSearchBar = ()=>{
        searchBar.val('');
        searchBar.keyup();
    }
});
