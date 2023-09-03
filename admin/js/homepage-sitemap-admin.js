jQuery(document).ready(function($) {
    $('#crawl-button').on('click', function() {
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'crawl_store_links',
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                $('.results').html();
                let list = '<ol>';
                response.forEach(link => {
                    list += '<li><a href="'+link+'" target="_blank">'+link+'</a></li>';
                });
                list += '</ol>';
                $('.results').html(list);
            },
        });
    });

    $('#view-button').on('click', function() {
        // Make an AJAX request to retrieve the results
        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'get_results' // This is the action we'll use in PHP
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                $('.results').html();
                let list = '<ol>';
                response.forEach(link => {
                    list += '<li><a href="'+link+'" target="_blank">'+link+'</a></li>';
                });
                list += '</ol>';
                $('.results').html(list);
            },
        });
    });
});
