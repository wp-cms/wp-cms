(function() {

    // Setup Media Library
    wp.media.wpUserAvatar = {
		
        get: function() {
            return wp.media.view.settings.post.wpUserAvatarId
        },

        set: function(a) {
            let b = wp.media.view.settings;
            b.post.wpUserAvatarId = a;
            if (b.post.wpUserAvatarId) {
                document.getElementById('wp-user-avatar').value = b.post.wpUserAvatarId;
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
                title: document.getElementById('wpua-add').dataset.title
            });
            this._frame.on('open', function() {
                let a = document.getElementById('wp-user-avatar').value;
                if (a.length > 0) {
                    let b = this.state().get('selection');
                    let attachment = wp.media.attachment(a);
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
            document.getElementById('wpua-add').addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                wp.media.wpUserAvatar.frame().open()
            });
        }

    }

    // Initialize Media Library
    wp.media.wpUserAvatar.init();

    // Reference image and input field DOM Elements
    const currentImage = document.querySelector('#current-image-container > img');
    const avatarId = document.getElementById('wp-user-avatar');

    // Handle clicks on remove button
    document.getElementById('wpua-remove-button').addEventListener('click', function(e) {
        e.preventDefault();

        // Toggle displayed buttons
        document.getElementById('wpua-remove-button').style.display = 'none';
        document.getElementById('wpua-undo-button').style.display = 'inline-block';

        // Save image for later in case user wants to undo
        currentImage.dataset.previousImage = currentImage.src;
        avatarId.dataset.previousId = avatarId.value;

        // Update current image
        currentImage.src = wpua_custom.avatar_thumb;

        // Update input field value
        avatarId.value='';


    });

    // Handle clicks on undo button
    document.getElementById('wpua-undo-button').addEventListener('click', function(e) {
        e.preventDefault();

        // Toggle displayed buttons
        document.getElementById('wpua-undo-button').style.display = 'none';
        document.getElementById('wpua-remove-button').style.display = 'inline-block';

        // Reset previous image and input value
        currentImage.src = currentImage.dataset.previousImage;
        avatarId.value = avatarId.dataset.previousId;

    })

})();