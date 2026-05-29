(function($) {
    'use strict';

    var igState = {
        postIds: [],
        postIndex: 0,
        images: [],
        imageIndex: 0,
        title: '',
        socialUrl: '',
        currentLang: ''
    };

    function i18n(key, fallback) {
        if (window.ewgcs_params && ewgcs_params.i18n && ewgcs_params.i18n[key]) {
            return ewgcs_params.i18n[key];
        }
        return fallback;
    }

    function buildModalOnce() {
        if ($('.ewgcs-modal-overlay').length) {
            return;
        }

        var html = '' +
            '<div class="ewgcs-modal-overlay" aria-hidden="true">' +
                '<button type="button" class="ewgcs-modal-close" aria-label="' + i18n('close', 'Close') + '">&times;</button>' +
                '<button type="button" class="ewgcs-post-nav ewgcs-post-prev" aria-label="' + i18n('prev_post', 'Previous post') + '" data-tooltip="' + i18n('prev_post', 'Previous post') + '">&#10094;</button>' +
                '<div class="ewgcs-modal-shell">' +
                    '<div class="ewgcs-modal-stage">' +
                        '<button type="button" class="ewgcs-image-nav ewgcs-image-prev" aria-label="' + i18n('prev_image', 'Previous image') + '">&#10094;</button>' +
                        '<img class="ewgcs-modal-image" src="" alt="" />' +
                        '<button type="button" class="ewgcs-image-nav ewgcs-image-next" aria-label="' + i18n('next_image', 'Next image') + '">&#10095;</button>' +
                    '</div>' +
                '</div>' +
                '<button type="button" class="ewgcs-post-nav ewgcs-post-next" aria-label="' + i18n('next_post', 'Next post') + '" data-tooltip="' + i18n('next_post', 'Next post') + '">&#10095;</button>' +
                '<button type="button" class="ewgcs-social-cta" style="display:none;" aria-label="' + i18n('view_post', 'View post ↗') + '">' + i18n('view_post', 'View post ↗') + '</button>' +
            '</div>';

        $('body').append(html);

        $(document).on('click', '.ewgcs-modal-overlay', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        $(document).on('click', '.ewgcs-modal-close', function() {
            closeModal();
        });

        $(document).on('click', '.ewgcs-post-prev', function(e) {
            e.stopPropagation();
            movePost(-1);
        });

        $(document).on('click', '.ewgcs-post-next', function(e) {
            e.stopPropagation();
            movePost(1);
        });

        $(document).on('click', '.ewgcs-image-prev', function(e) {
            e.stopPropagation();
            moveImage(-1);
        });

        $(document).on('click', '.ewgcs-image-next', function(e) {
            e.stopPropagation();
            moveImage(1);
        });
        
        $(document).on('click', '.ewgcs-social-cta', function(e) {
            e.stopPropagation();
            if (igState.socialUrl) {
                window.open(igState.socialUrl, '_blank', 'noopener,noreferrer');
            }
        });

        $(document).on('keydown', function(e) {
            if (!$('.ewgcs-modal-overlay').hasClass('active')) {
                return;
            }

            if (e.key === 'Escape') {
                closeModal();
            } else if (e.key === 'ArrowLeft') {
                moveImage(-1);
            } else if (e.key === 'ArrowRight') {
                moveImage(1);
            } else if (e.key === 'ArrowUp') {
                movePost(-1);
            } else if (e.key === 'ArrowDown') {
                movePost(1);
            }
        });
    }

    function refreshPostIds(currentPostId) {
        var ids = [];
        $('.ewgcs-portfolio-item').each(function() {
            var id = parseInt($(this).data('id') || $(this).data('post-id'), 10);
            if (id && ids.indexOf(id) === -1) {
                ids.push(id);
            }
        });

        igState.postIds = ids;
        igState.postIndex = Math.max(0, ids.indexOf(parseInt(currentPostId, 10)));
    }

    function renderImage() {
        if (!igState.images.length) {
            return;
        }

        $('.ewgcs-modal-image').attr('src', igState.images[igState.imageIndex]);
        if (igState.images.length > 1) {
            $('.ewgcs-image-nav').show();
        } else {
            $('.ewgcs-image-nav').hide();
        }
    }

    function setPostData(data) {
        var images = Array.isArray(data.gallery) ? data.gallery.filter(Boolean) : [];
        if (data.featured_image && images.indexOf(data.featured_image) === -1) {
            images.unshift(data.featured_image);
        }

        if (!images.length) {
            return;
        }

        igState.title = data.title || '';
        igState.socialUrl = data.social_url || '';
        igState.images = images;
        igState.imageIndex = 0;
        if (igState.socialUrl) {
            $('.ewgcs-social-cta').show();
        } else {
            $('.ewgcs-social-cta').hide();
        }
        renderImage();
    }

    function fetchPost(postId, done) {
        $.ajax({
            url: ewgcs_params.ajax_url,
            type: 'POST',
            data: {
                action: 'ewgcs_get_project_details',
                post_id: postId,
                lang: igState.currentLang || '',
                nonce: ewgcs_params.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    done(response.data);
                }
            }
        });
    }

    function openModalFromPost(postId, lang) {
        buildModalOnce();
        refreshPostIds(postId);
        igState.currentLang = lang || '';

        var $overlay = $('.ewgcs-modal-overlay');
        $overlay.addClass('active').attr('aria-hidden', 'false');
        $('body').addClass('ewgcs-modal-open');

        fetchPost(postId, function(data) {
            setPostData(data);
        });
    }

    function closeModal() {
        $('.ewgcs-modal-overlay').removeClass('active').attr('aria-hidden', 'true');
        $('body').removeClass('ewgcs-modal-open');
    }

    function movePost(step) {
        if (!igState.postIds.length) {
            return;
        }

        var total = igState.postIds.length;
        igState.postIndex = (igState.postIndex + step + total) % total;
        var nextPostId = igState.postIds[igState.postIndex];

        fetchPost(nextPostId, function(data) {
            setPostData(data);
        });
    }

    function moveImage(step) {
        if (igState.images.length <= 1) {
            return;
        }

        var total = igState.images.length;
        igState.imageIndex = (igState.imageIndex + step + total) % total;
        renderImage();
    }

    $(document).on('click', '.ewgcs-portfolio-item', function(e) {
        e.preventDefault();

        var postId = parseInt($(this).data('id') || $(this).data('post-id'), 10);
        if (!postId) {
            return;
        }

        openModalFromPost(postId, ($(this).data('lang') || '').toString());
    });
})(jQuery);
