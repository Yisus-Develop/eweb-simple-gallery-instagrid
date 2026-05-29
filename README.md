# EWEB - Simple Gallery Instagrid

Plugin de galeria estilo Instagram para WordPress.

## Nuevo flujo

- CPT propio: `instagrid_post`
- Metabox para gestionar orden/eliminacion de imagenes de galeria
- Modal frontend con GLightbox

## Shortcodes

- `[instagrid_feed posts_per_page="12" columns="3"]`
- `[instagrid_feed]` (idioma automatico)
- `[instagrid_feed lang="en"]` (forzar idioma)
- `[instagrid_carousel posts_per_page="12" slides="4"]`
- `[instagrid_feed layout="carousel" posts_per_page="12" slides="4"]` (compatibilidad)
- `[simple_gallery_instagrid id="123"]`
- `[simple_gallery_instagrid id="123" lang="pt"]`

Compatibilidad legacy:

- `[simple_gallery_comparator]` (alias del shortcode simple)


## Elementor

- Widget: `Instagrid Carousel`
- Controls: posts por pagina, slides desktop, mostrar flechas
