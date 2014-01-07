$(document).ajaxStart(function () {
    function labelIn()
    {
        $(this).animate({'opacity' : '1.0'}, 800, 'swing', labelOut);
    }

    function labelOut()
    {
        $(this).animate({'opacity' : '0.3'}, 800, 'swing', labelIn);
    }

    $('#ajax_floating_indicator')
        .css('z-index', '10')
        .animate({'opacity': '0.6'}, 200, 'swing', function() {
            labelOut.apply($(this).find('span.label'));
        });
});

$(document).ajaxStop(function () {
    $('#ajax_floating_indicator span.label').stop();
    $('#ajax_floating_indicator').animate({'opacity': '0'}, 200, 'swing', function () {
        $(this).css('z-index', '-99');
    });
});
