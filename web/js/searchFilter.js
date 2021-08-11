$(document).ready(function () {
    var searchBar = $("#search");
    searchBar.on("keyup", function (event) {
        var inputValue = this.value;
        /* var regex = new RegExp("/^[A-Z]+$/i");
        //console.log(inputValue);
        //console.log(regex);
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
       // console.log(key);

        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }
         */
        if (inputValue.length > 0) {
            $("li", "#w0")
                .hide()
                .filter(function () {
                    return (
                        removeAccents($(this).text())                        
                                .toLowerCase()
                                .indexOf(searchBar.val().toLowerCase()) != -1
                    );
                })
                .show();
        } else {
            $("li", "#w0").show();
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
    icon.click(function (evt) {
        // we utilize this function to prevent a propagation error. https://stackoverflow.com/questions/8238599/jquery-click-fires-twice-when-clicking-on-label/49336534
        evt.stopImmediatePropagation();
        search.toggleClass('active');
    });

    // clear button logic
    const clearBtn = $('.close-icon').children('span');
    clearBtn.click(function () {
        searchBar.val('');
        searchBar.keyup();
    })
});
