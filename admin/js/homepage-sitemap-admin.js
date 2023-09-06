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
                $('.results').html();
                let list = '<h3>Success! The crawl results are as follows:</h3>';
                list += displayURLsList(response);
                $('.results').html(list);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                try {
                    const response = JSON.parse(jqXHR.responseText);
                    if(response && response.data && response.data.length > 0) {
                        $('.results').html('<h3>'+response.data[0].message+'</h3>');
                    }
                } catch (error) {
                    console.error('JSON parsing error:', error);
                }
            }
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
                $('.results').html();
                let list = '<h3>The currently stored crawled URLs are as follows:</h3>';
                list += displayURLsList(response);
                $('.results').html(list);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                try {
                    const response = JSON.parse(jqXHR.responseText);
                    if(response && response.data && response.data.length > 0) {
                        $('.results').html('<h3>'+response.data[0].message+'</h3>');
                    }
                } catch (error) {
                    console.error('JSON parsing error:', error);
                }
            }
        });
    });
});

function displayURLsList(list) {
    let display = '';
    display += '<ol>';
    list.forEach(link => {
        display += '<li><a href="'+link+'" target="_blank">'+link+'</a></li>';
    });
    display += '</ol>';
    display += '<p>To view the sitemap, please click <a href="'+window.location.origin+'/sitemap.html'+'" target="_blank">here</a>.</p>';
    return display;
}
