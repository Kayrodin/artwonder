$(window).scroll(function() {
    var wintop = $(window).scrollTop(), docheight = $(document).height(), winheight = $(window).height();
    //Modify this parameter to establish how far down do you want to make the ajax call
    var scrolltrigger = 0.80;
    if ((wintop / (docheight - winheight)) > scrolltrigger) {
    //I added the is_processing variable to keep the ajax asynchronous but avoiding making the same call multiple times
        if (last_page === false && is_processing === false) {
            if(search) {
                addMoreElementsWithSearch();
            }else{
                addMoreElements();
            }
        }
    }
});

is_processing = false;
last_page = false;
function addMoreElementsWithSearch() {
    is_processing = true;
    $('.wait').show();
    $.ajax({
        type: "GET",
        //FOS Routing
        url: Routing.generate('get_wondarts_search', {'from': start, 'limit': limit, 'search': search }),
        success: function(response) {
            $('.wait').hide();
            if (response.html.length > 0) {
                $('#contenedor_wondarts').append(response.html);
                start += limit;
                //The server can answer saying it's the last page so that the browser doesn't make anymore calls
                last_page = response.lastpage;
            } else {
                last_page = true;
            }
            is_processing = false;
        },
        error: function(data) {
            is_processing = false;
            alert("Error in async callback");
        }
    });
}

function addMoreElements() {
    is_processing = true;
    $('.wait').show();
    $.ajax({
        type: "GET",
        //FOS Routing
        url: Routing.generate('get_wondarts', {'from': start, 'limit': limit}),
        success: function(response) {
            $('.wait').hide();
            if (response.html.length > 0) {
                $('#contenedor_wondarts').append(response.html);
                start += limit;
                //The server can answer saying it's the last page so that the browser doesn't make anymore calls
                last_page = response.lastpage;
            } else {
                last_page = true;
            }
            is_processing = false;
            if ($(window).scrollBottom() == 0 && scrollStart){
                addMoreElements();
                scrollStart = false;
            }
        },
        error: function(data) {
            is_processing = false;
            alert("Error in async callback");
        }
    });
}

$.fn.scrollBottom = function() {
    return $(document).height() - this.scrollTop() - this.height();
};

var start = 0;
var scrollStart = true;


//Este es el prototipo

// is_processing = false;
// last_page = false;
// function addMoreElements() {
//     is_processing = true;
//     $.ajax({
//         type: "GET",
//         //FOS Routing
//         url: Routing.generate('route_name', {page: page}),
//         success: function(data) {
//             if (data.html.length > 0) {
//                 $('.selector').append(data.html);
//                 page = page + 1;
//                 //The server can answer saying it's the last page so that the browser doesn't make anymore calls
//                 last_page = data.last_page;
//             } else {
//                 last_page = true;
//             }
//             is_processing = false;
//         },
//         error: function(data) {
//             is_processing = false;
//         }
//     });
// }
//
// $(window).scroll(function() {
//     var wintop = $(window).scrollTop(), docheight = $(document).height(), winheight = $(window).height();
//     //Modify this parameter to establish how far down do you want to make the ajax call
//     var scrolltrigger = 0.80;
//     if ((wintop / (docheight - winheight)) > scrolltrigger) {
//     //I added the is_processing variable to keep the ajax asynchronous but avoiding making the same call multiple times
//         if (last_page === false && is_processing === false) {
//             addMoreElements();
//         }
//     }
// });