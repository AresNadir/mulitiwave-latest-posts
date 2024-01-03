jQuery(document).ready(function($) {
    $('#load-more-posts').click(function() {
        
        var button = $(this);
        var page = button.data('page');
        var perPage = button.data('per-page');

        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'load_more_posts',
                page: page,
                per_page: perPage
            },
            success: function(response) {
                var data = JSON.parse(response);
                var $newPosts = $(data.posts);
                $('#latest-posts-container').append($newPosts);
                $newPosts.each(function(i, el){
                    setTimeout(function(){
                        $(el).addClass('visible');
                    }, 100 * i); // Stagger the animation
                });
                if (!data.more_posts) {
                    button.hide();
                } else {
                    button.data('page', page + 1);
                }
            }
        });
    });
});
