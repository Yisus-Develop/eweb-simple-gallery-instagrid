# EWEB - Simple Gallery Instagrid

<p align="center">
  <img src="https://img.shields.io/github/v/release/Yisus-Develop/eweb-simple-gallery-instagrid?style=for-the-badge&color=blue" alt="Release Version">
  <img src="https://img.shields.io/github/license/Yisus-Develop/eweb-simple-gallery-instagrid?style=for-the-badge&color=green" alt="License">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-8892BF?style=for-the-badge&logo=php" alt="PHP Version">
  <img src="https://img.shields.io/badge/WordPress-6.0%2B-21759B?style=for-the-badge&logo=wordpress" alt="WordPress Compatibility">
</p>

<p align="center">
  <strong>An ultra-modern, zero-bloat, touch-enabled Instagram-style gallery system for WordPress. Fully integrated with GLightbox, Elementor, and an intelligent Same-Post Multilingual Fallback system for Polylang and WPML.</strong>
</p>

<p align="center">
  <em>Una galería premium estilo Instagram para WordPress, optimizada, táctil y de alto rendimiento. Totalmente integrada con GLightbox, Elementor y un sistema inteligente de respaldo multilingüe en el mismo post para Polylang y WPML.</em>
</p>

---

## 🔍 SEO & Search Discoverability Keywords
`wordpress-instagram-gallery` | `glightbox-wordpress-plugin` | `polylang-gallery-fallback` | `wpml-image-synchronization` | `instagrid-feed` | `elementor-custom-gallery` | `zero-bloat-wordpress-gallery` | `responsive-touch-lightbox` | `custom-post-type-gallery`

---

## 🇺🇸 English Documentation

### 🌟 Key Features
* **Touch-Enabled Lightbox (GLightbox):** Premium fullscreen modal experience with full support for mobile swipe gestures, zoom, keyboard controls, and beautiful transitions.
* **Smart Same-Post Multilingual Fallback:** Fully compatible with Polylang and WPML. When using multi-language tabs on a non-translatable post ID, if a translation tab (e.g., Portuguese) is empty, the plugin automatically falls back to retrieve the images, featured covers, and social links from the default language tab (e.g., English) on the *same post*, eliminating duplicate asset uploads!
* **Agnostic CPT Structure:** Powered by a clean, lightweight Custom Post Type (`instagrid_post`), completely separated from standard posts.
* **Drag-and-Drop Order:** Fully responsive admin interface to add, remove, and sort images effortlessly.
* **Social CTA Signals:** Custom per-language metadata fields to redirect each gallery item directly to its original Instagram/Facebook source publication.

### 🔌 Shortcodes Guide
Desplay galleries anywhere with high flexibility:

* **Grid Layout (Dynamic Feed):**
  ```html
  [instagrid_feed posts_per_page="12" columns="3"]
  ```
* **Carousel Layout:**
  ```html
  [instagrid_carousel posts_per_page="12" slides="4"]
  ```
* **Single Post Gallery:**
  ```html
  [simple_gallery_instagrid id="123"]
  ```
* **Force Language Filter:**
  ```html
  [instagrid_feed lang="pt"]
  ```
  *(Omitting `lang` automatically detects the current active frontend language).*

---

## 🇪🇸 Documentación en Español

### 🌟 Características Clave
* **Lightbox Táctil Premium (GLightbox):** Experiencia modal a pantalla completa con soporte total para gestos táctiles móviles, zoom real, controles de teclado y transiciones fluidas.
* **Respaldo Inteligente Multilingüe en el Mismo Post:** Compatible al 100% con Polylang y WPML. Si trabajas con pestañas de idioma sobre un post que no está registrado como traducible, si una pestaña (ej. Portugués) queda vacía, el plugin automáticamente hereda la galería, portada destacada y enlaces de la pestaña de idioma principal (ej. Inglés) en el *mismo post*, eliminando la duplicación de subidas.
* **Arquitectura de CPT Independiente:** Estructurado bajo un Custom Post Type limpio (`instagrid_post`), separado por completo de los artículos comunes.
* **Ordenamiento por Arrastrar y Soltar:** Panel de administración intuitivo para añadir, eliminar y reordenar imágenes en segundos.
* **Llamado a la Acción Social (Social CTA):** Campos semánticos por idioma para redirigir cada tarjeta de la galería directamente a su publicación original en Instagram, Facebook u otras redes.

### 🔌 Guía de Shortcodes
Despliega tus galerías de forma sumamente sencilla en cualquier rincón de tu web:

* **Diseño en Grid (Feed Dinámico):**
  ```html
  [instagrid_feed posts_per_page="12" columns="3"]
  ```
* **Diseño en Carrusel (Carousel):**
  ```html
  [instagrid_carousel posts_per_page="12" slides="4"]
  ```
* **Galería de una Sola Publicación:**
  ```html
  [simple_gallery_instagrid id="123"]
  ```
* **Forzar Filtro de Idioma:**
  ```html
  [instagrid_feed lang="pt"]
  ```
  *(Al omitir el atributo `lang`, el plugin detectará automáticamente el idioma activo en el frontend).*

---

## 📦 Elementor Native Widget / Widget Nativo de Elementor
The plugin registers a highly optimized, native Elementor widget:
* **Widget Name:** `Instagrid Carousel`
* **Controls:** Adjust posts per page, slides on desktop/tablet/mobile, infinite loop, and navigation arrows directly from the Elementor visual interface.

*El plugin registra un widget nativo altamente optimizado para Elementor:*
* **Nombre del Widget:** `Instagrid Carousel`
* **Controles:** Ajusta posts por página, diapositivas visibles en móviles/tablets/desktop, bucle infinito y flechas de navegación directamente desde el editor de Elementor.

---

## 🛠️ Installation & Requirements / Instalación y Requisitos
* **PHP:** `8.1+` (Strictly typed, Yoda conditions compliant).
* **WordPress:** `6.0+`
1. Upload the `eweb-simple-gallery-instagrid` folder to `/wp-content/plugins/`.
2. Activate the plugin through the WordPress admin panel.
3. Paste the shortcode `[instagrid_feed]` on any page or use the `Instagrid Carousel` widget in Elementor.

---
*Developed under the **enlaweb Elite Standard** - Premium, zero-bloat, secure, and open-source ready.*
