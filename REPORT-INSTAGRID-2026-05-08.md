# Informe de trabajo - Simple Gallery Instagrid

Fecha: 2026-05-08  
Proyecto: davion.pt  
Plugin: simple-gallery-instagrid  
Versión: 2.0.3

## Objetivo
Implementar una galería tipo Instagram para mostrar múltiples publicaciones en grid, con apertura en modal fullscreen, navegación interna por imágenes del post y navegación externa entre publicaciones.

## Cambios realizados

1. Estructura y naming
- Se unificó el plugin bajo el nombre `simple-gallery-instagrid`.
- Se ajustaron referencias internas para evitar inconsistencias de carga.

2. Correcciones de admin
- Se restauró la funcionalidad de “Adicionar/Eliminar imágenes” en el metabox.
- Se corrigió problema de carga de assets (`admin.css`) provocado por permisos/ruta MIME en servidor.
- Se actualizó el texto de contexto del metabox para reflejar “galería del post”.
- Se eliminó el bloque de ayuda redundante del metabox.

3. Página de ayuda en menú
- Se añadió submenú en wp-admin: `Instagrid > Como Usar`.
- Se centralizó allí la documentación de shortcodes y uso.

4. Frontend (UX estilo Instagram)
- Grid principal de publicaciones tipo feed.
- Indicador visual cuando una publicación tiene múltiples imágenes.
- Modal fullscreen con:
  - Flechas internas (navegan imágenes de la publicación actual).
  - Flechas externas (navegan entre publicaciones), visualmente diferenciadas para evitar confusión.
  - Cierre por botón `X` y `ESC`.
- Ajustes CSS para resistir colisiones con estilos de Elementor/tema (flechas, cierre, fullscreen).

5. Enlace a redes sociales
- Se añadió campo por post para URL social (`_ewgcs_social_url`).
- Se muestra señal/CTA en frontend para abrir la publicación original en red social.

6. Internacionalización
- Se migraron textos a funciones i18n nativas de WordPress (`__()`, etc.).
- Se retiró dependencia lógica específica de Polylang para mantener compatibilidad con cualquier traductor (Polylang, WPML, Loco, TranslatePress, etc.).

## Archivos principales modificados
- `simple-gallery-instagrid.php`
- `includes/class-sgc-admin.php`
- `includes/class-sgc-assets.php`
- `includes/class-sgc-shortcode-simple.php`
- `includes/class-sgc-portfolio-loop.php`
- `assets/js/main.js`
- `assets/css/main.css`
- `assets/css/modal.css`

## Validación
- Validación de sintaxis PHP (`php -l`) en archivos modificados: OK.
- Verificaciones funcionales en admin/frontend durante iteraciones.

## Deploy
- Método: FTP
- Destino: `/public_html/wp-content/plugins/simple-gallery-instagrid/`
- Estado: sincronizado con la versión local 2.0.3.

## Resultado
Se entrega un plugin de galería más sólido y mantenible, con experiencia visual inspirada en Instagram, navegación dual clara, soporte de enlace social y arquitectura preparada para traducción estándar en WordPress.
