;(function($, window, document, undefined){
    'use strict';

    var utils = utils || {};
    var lag   = lag || {};

    /**
     * utils.loadingAnimation
     * null.
    **/
    utils.loadingAnimation = function(){
        var loader = new CanvasLoader('loader');
        loader.setShape('spiral');
        loader.setDiameter(20);
        loader.setDensity(15);
        loader.setRange(0.6);
        loader.setSpeed(1);
        loader.setColor('#919191');
        loader.show();
    }

    /**
     * lag.createGistShortcode
     * null.
    **/
    lag.createGistShortcode = function(user, id){
        tinyMCE.activeEditor.setContent(parent.tinyMCE.activeEditor.getContent() + '[gist user="' + user + '" id="' + id + '"]');
    }

    /**
     * lag.setNewUsername
     * null.
    **/
    lag.setNewUsername = function(username){
        $.ajax({
            type: 'POST',
            url: window.location.href + '&temp_github_username=' + username,
            cache: false,
            beforeSend: function(){
                $('.gist-list').html('<div id="loader"></div>');
                utils.loadingAnimation();
            },
            success: function(data){
                var response = $(data);
                var gistlist = response.find('.gist-list').html();

                $('.gist-list').hide();
                if(gistlist !== undefined){
                    $('.gist-list').html(gistlist).fadeIn(500);
                }
                else{
                    $('.gist-list').html('User could not be found').fadeIn(500);
                }
            }
        });
    }

    $(function(){
        $(document).on('click', '.add-gist', function(e){
            e.preventDefault();
            var user = $(this).data('gist-user');
            var id   = $(this).data('gist-id');
            lag.createGistShortcode(user, id);
        });

        $('#wplg_cf_github_username').on({
            blur: function(){
                if($(this).val().length > 0){
                    var username = $(this).val();
                    lag.setNewUsername(username);
                }
                else{
                    var defaultuser = $('.wplg_cf_github_username_id').text();
                    lag.setNewUsername(defaultuser);
                }
            }
        })
    });

})(jQuery, window, document);
