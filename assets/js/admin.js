/**
 * JavaScript para la administración del plugin EWGCS
 * Maneja galería de imágenes (incluyendo variantes por idioma).
 */

jQuery(document).ready(function($) {
    function initSortable($scope) {
        $scope.find('.sgc-image-grid').sortable({
            handle: '.sgc-drag-handle',
            placeholder: 'sgc-sortable-placeholder',
            forcePlaceholderSize: true,
            opacity: 0.7,
            cursor: 'move',
            tolerance: 'pointer'
        });
    }

    initSortable($(document));

    $(document).on('click', '.sgc-lang-tab', function(e) {
        e.preventDefault();

        var lang = $(this).data('lang');
        $('.sgc-lang-tab').css({ background: '', color: '' });
        $(this).css({ background: '#2271b1', color: '#fff' });

        $('.sgc-lang-panel').hide();
        $('.sgc-lang-panel[data-lang="' + lang + '"]').show();
    });

    $(document).on('click', '.sgc-add-gallery-image', function(e) {
        e.preventDefault();

        if (typeof wp === 'undefined' || !wp.media) {
            console.error('WP media library is not available on this screen.');
            return;
        }

        var lang = $(this).data('lang') || 'default';
        var $container = $('.sgc-lang-panel[data-lang="' + lang + '"] .sgc-image-grid');

        var galleryUploader = wp.media({
            title: 'Selecionar imagens para galeria',
            button: { text: 'Adicionar imagens' },
            multiple: true
        });

        galleryUploader.on('select', function() {
            var attachments = galleryUploader.state().get('selection').toJSON();

            $.each(attachments, function(i, att) {
                if ($container.find('[data-id="' + att.id + '"]').length > 0) {
                    return;
                }

                var thumbUrl = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
                var fileName = att.filename || att.title;

                var html = '<div class="sgc-image-item" data-id="' + att.id + '">' +
                           '<div class="sgc-drag-handle" title="Arrastar para ordenar">⋮⋮</div>' +
                           '<img src="' + thumbUrl + '" alt="">' +
                           '<div class="sgc-image-name">' + fileName + '</div>' +
                           '<button type="button" class="sgc-remove-image button">Eliminar</button>' +
                           '<input type="hidden" name="ewgcs_gallery_images[' + lang + '][]" value="' + att.id + '">' +
                           '</div>';

                $container.append(html);
            });
        });

        galleryUploader.open();
    });

    $(document).on('click', '.sgc-remove-image', function() {
        $(this).closest('.sgc-image-item').remove();
    });
});
