$(window).on("load", function () {
    var searchBar = $("#search"); // textinput
    var menuNavbar = $("ul.menu-layout-navbar"); 
    searchBar.on("keyup", function (event) {
        var inputValue = this.value;
        var srcStrHtml = menuNavbar.children().add(menuNavbar.find("a[href^='/'],li"));
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
        }
        srcStrHtml.show();
    });


    // Remove tildes from text filtering
    const removeAccents = (str) => {
        const regex = /[\u0300-\u036f]/g;
        return str.normalize("NFD").replace(regex, "");
    }

    // CSS logic
    const icon = $('.search-icon');
    const search = $('.search-container');
    const animationTime = 301;
    icon.click((evt) => {
        evt.stopImmediatePropagation();
        animateSearchBar();
    });

    var locked = false;
    const animateSearchBar = () => {
        if (!locked) {
            locked = true;
            // we utilize this function to prevent a propagation error. https://stackoverflow.com/questions/8238599/jquery-click-fires-twice-when-clicking-on-label/49336534
            search.toggleClass('active');

            search.hasClass('active') ?
                setTimeout(
                    focusSearchBar,
                    animationTime
                )
                : clearSearchBar();
                
            setTimeout(unlock, animationTime);    
        }
    }

    function unlock() {
        locked = false;
    }

    // clear button logic
    const clearBtn = $('.close-icon').children('span');
    clearBtn.click(()=>  { clearSearchBar() });

    const clearSearchBar = () => {
        searchBar.val('');
        if (search.hasClass('active')) setTimeout(
            focusSearchBar,
            animationTime
        );
        searchBar.keyup();
    }

    const focusSearchBar = () => {
        searchBar.focus();
    }

    setTimeout(
        animateSearchBar,
        animationTime
    );
    console.log('Search-bar initialized.');
});
