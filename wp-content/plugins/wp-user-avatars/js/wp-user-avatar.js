(function($) {
    wp.media.wpUserAvatar = {
		
        get: function() {
            return wp.media.view.settings.post.wpUserAvatarId
        },

        set: function(a) {
            var b = wp.media.view.settings;
            b.post.wpUserAvatarId = a;
            if (b.post.wpUserAvatarId) {
                $('#wp-user-avatar').val(b.post.wpUserAvatarId);
            }
            wp.media.wpUserAvatar.frame().close()
        },

        frame: function() {
            if (this._frame) {
                return this._frame
            }
            this._frame = wp.media({
                library: {
                    type: 'image'
                },
                multiple: false,
                title: $('#wpua-add').data('title')
            });
            this._frame.on('open', function() {
                var a = $('#wp-user-avatar').val();
                if (a == "") {
                    $('div.media-router').find('a:first').trigger('click')
                } else {
                    var b = this.state().get('selection');
                    attachment = wp.media.attachment(a);
                    attachment.fetch();
                    b.add(attachment ? [attachment] : [])
                }
            }, this._frame);
            this._frame.state('library').on('select', this.select);
            return this._frame
        },

        select: function(a) {
            const selection = this.get('selection').single();
            wp.media.wpUserAvatar.set(selection ? selection.id : -1)
            document.querySelector('#current-image-container > img').src = selection.changed.url;

        },

        init: function() {
            $('body').on('click', '#wpua-add', function(e) {
                e.preventDefault();
                e.stopPropagation();
                wp.media.wpUserAvatar.frame().open()
            })
        }

    }

    wp.media.wpUserAvatar.init();

    const currentImage = $('#current-image-container img');
    const avatarIdVal = $('#wp-user-avatar').val();

    // When clicking on remove button
    $('body').on('click', '#wpua-remove-button', function(e) {
        e.preventDefault();

        $('#wpua-remove-button, #wpua-thumbnail').hide();

        currentImage.data('previous', currentImage.attr('src'));
        currentImage.attr('src', wpua_custom.avatar_thumb);

        $('#wp-user-avatar').val('');
        $('#wpua-undo-button').show();

    });

    // When clicking on undo
    $('body').on('click', '#wpua-undo-button', function(e) {
        e.preventDefault();

        currentImage.attr('src', currentImage.data('previous'));

        $('#wpua-undo-button').hide();
        $('#wpua-remove-button').show();
        $('#wp-user-avatar').val(avatarIdVal);

    })
})(jQuery);