/**
 *
 * Dashboard
 *
 * Copyright 2015, Author Name
 * Some information on the license.
 *
**/

;(function(Module, $, undefined){
    'use strict';

    /**
     * Module.init
     * Init module.
    **/
    Module.init = function(){
        Module.binds();
    }

    /**
     * Module.binds
     * jQuery event binds.
    **/
    Module.binds = function(){
        $(function(){
            // Add Gist event.
            $(document).on('click', '.js-add-gist', function(e){
                e.preventDefault();

                var id   = $(this).data('gist-id'),
                    user = $(this).data('gist-user');

                // Add to WYSIWYG editor.
                Module.add_gist_to_wysiwyg(user, id);
            });

            // Change username event.
            var timer;
            $('#_WPLG_cmb2_gists_username').on('keyup', function(){
                var self = $(this);
                timer && clearTimeout(timer);
                timer = setTimeout(function(){
                    if(self.val().length > 0){
                        // Get username.
                        var username = self.val();
                        // Retrieve gists.
                        Module.retrieve_temp_gists(username);
                    }
                    else{
                    }
                }, 300);
            })
        });
    }

    /**
     * Module.preloader
     * Generates a preloader.
     *
     * @since 1.0.0
     * @version 1.0.0
    **/
    Module.preloader = function(el, destroy){
        destroy = (typeof destroy === 'undefined') ? false : true;
        el      = (typeof el === 'undefined') ? $('body') : el;
        var loader = $('<div class="spinner-wrapper"><svg class="loader" width="35px" height="35px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg></div>');

        if(!destroy){
            if(!$('.spinner-wrapper', el).length){
                el.css({'position': 'relative'}).prepend(loader);
                el.addClass('fade-gists');
            }
        }
        else{
            $('.spinner-wrapper', el).fadeOut(500, function(){
                el.removeClass('fade-gists');
                $(this).remove();
            })
        }
    }

    /**
     * Module.add_gist_to_wysiwyg
     * NULLED.
    **/
    Module.add_gist_to_wysiwyg = function(user, id){
        tinyMCE.activeEditor.setContent(parent.tinyMCE.activeEditor.getContent() + '[gist user="' + user + '" id="' + id + '"]');
    }

    /**
     * Module.retrieve_temp_gists
     * NULLED.
    **/
    Module.retrieve_temp_gists = function(username){
        $.ajax({
            type: 'GET',
            url: 'https://api.github.com/users/' + username + '/gists',
            cache: false,
            beforeSend: function(){
                // List DOM element.
                var ul = $('.wplg__gist-list');
                // Add preloader.
                Module.preloader(ul);
            },
            success: function(xhr){
                // List DOM element.
                var ul = $('.wplg__gist-list');
                // Destroy preloader.
                Module.preloader(ul, true);
                // Empty current list.
                ul.empty();
                // Check array length.
                if(xhr.length > 0){
                    $.each(xhr, function(){
                        var self = $(this)[0];
                        var desc = (self.description != null) ? self.description : 'No name';
                        ul.append('<li><a href="' + self.url + '" class="js-add-gist" data-gist-user="" data-gist-id="' + self.id + '">' + desc + '</a></li>');
                    });
                }
                else{
                    ul.prepend('<div>No Gists available.</div>');
                }
            },
            error: function(xhr){
                // List DOM element.
                var ul = $('.wplg__gist-list');
                // Destroy preloader.
                Module.preloader(ul, true);
                // Empty current list.
                ul.empty();
                // Check message.
                if(xhr.responseJSON.message !== 'undefined'){
                    ul.prepend('<div>' + xhr.responseJSON.message + '</div>');
                }
                else{
                    console.log(xhr);
                }
            }
        });
    }

    Module.init();

}(window, jQuery));
