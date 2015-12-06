var anyPopisekClass = "anyTitle";
var anyPopisekIdPlovouciDiv = "plovouciHlaska";

$(document).on({
    mouseenter: function () {
        var title = $(this).attr('popis');
        $('<div class="posta_tooltip"></div>')
            .html(title)
            .appendTo('body')
            .fadeIn();
    },

    mouseleave: function() {
        // Hover out code
        //   $(this).attr('title', $(this).data('tipText'));
        $('.posta_tooltip').remove();
    },

    mousemove: function(e) {
        var mousex = e.pageX + 20; //Get X coordinates
        var mousey = e.pageY + 10; //Get Y coordinates
        $('.posta_tooltip')
            .css({ top: mousey, left: mousex });
    }
}, "."+anyPopisekClass);