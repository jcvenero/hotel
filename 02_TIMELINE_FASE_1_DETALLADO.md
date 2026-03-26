# TIMELINE FASE 1 - GUÍA DE IMPLEMENTACIÓN DETALLADA

## RESUMEN EJECUTIVO

**Duración:** 12 semanas (84 días)
**Horas totales:** 350-450 horas (1-2 developers)
**Horas/semana:** 35-40 horas
**Arquitectura:** PHP 8.1 + MySQL 5.7 + Vanilla JS + Tailwind CSS v4

---

## SEMANA 1-2: FUNDAMENTOS Y SEGURIDAD (80 horas)

### Semana 1: Setup + Arquitectura Base

#### Día 1-2: Configuración Inicial (16 horas)
- [ ] Instalar XAMPP y configurar
- [ ] Crear estructura de carpetas (proyecto/config, includes, api, admin, themes, etc.)
- [ ] Configurar archivo `.env` con credenciales DB
- [ ] Crear archivo `config/database.php` con constantes
- [ ] Setup de Git + .gitignore
- [ ] Crear BD en MySQL y ejecutar schema.sql (Tabla 1-37)

**Entregable:** Proyecto con estructura clara y BD lista

#### Día 3-5: Clases Base - Parte 1 (24 horas)
- [ ] Crear clase `Database.php` (PDO wrapper)
  - Métodos: query(), findOne(), findAll(), insert(), update(), delete()
  - Prepared statements para TODO
  - Error handling
  
- [ ] Crear clase `Validator.php`
  - Validaciones: email(), integer(), float(), url(), string()
  - Sanitización: sanitizeString(), sanitizeHTML(), sanitizeEmail()
  - File validation: validateFileUpload()

- [ ] Crear clase `Escaper.php`
  - Métodos: html(), attr(), url(), css(), js()
  - Protección contra XSS

- [ ] Crear clase `Security.php`
  - Rate limiting
  - HTTPS enforcement
  - Security headers
  - CORS validation

**Entregable:** 4 clases críticas de seguridad

#### Día 6-10: Autenticación y Roles (40 horas)
- [ ] Crear clase `Auth.php`
  - Método login() con validación
  - Método logout()
  - Método isLoggedIn()
  - Método getCurrentUser()
  - Método hasRole()
  - Método hasPermission()
  - Sistema de roles: super_admin, admin, editor, recepcionista

- [ ] Crear clase `CSRF.php`
  - Generar tokens
  - Validar tokens
  - Método getField() para formularios

- [ ] Crear clase `Language.php`
  - setLanguage()
  - get() para strings
  - getAvailable()
  - Soporte multiidioma

- [ ] Crear `/admin/login.php`
  - Formulario de login
  - Validación con CSRF token
  - Rate limiting (5 intentos x 15 min)
  - Almacenar sesión en BD

- [ ] Crear tabla `usuarios` con primer super admin

**Entregable:** Sistema de autenticación seguro y funcional

### Semana 2: Clases Base - Parte 2 + Primeras APIs

#### Día 1-3: Clases Base - Parte 2 (24 horas)
- [ ] Crear clase `Logger.php`
  - Log errors en `/logs/errors.log`
  - Log security en `/logs/security.log`
  - Log audit en `/logs/audit.log`

- [ ] Crear clase `ImageHandler.php`
  - Validar uploads (peso, tipo MIME)
  - Redimensionar imágenes
  - Convertir a WebP
  - Generar thumbnails (300x200, 150x100)
  - Guardar en `/public/uploads/`

- [ ] Crear clase `Mailer.php`
  - Envío de emails con PHPMailer
  - Templates HTML para emails

- [ ] Crear `/languages/es.php` y `/languages/en.php`
  - Strings de interfaz del admin
  - Strings del frontend

**Entregable:** Sistema de logging, procesamiento de imágenes y emails

#### Día 4-7: Dashboard Admin Base (32 horas)
- [ ] Crear `/admin/dashboard.php`
  - Verificar login
  - Mostrar menú lateral con navegación
  - Mostrar estadísticas básicas

- [ ] Crear `/admin/usuarios.php`
  - CRUD de usuarios
  - Gestión de roles
  - Activar/desactivar usuarios

- [ ] Crear `/admin/ajustes.php`
  - Editar ajustes del sitio
  - Interfaz simple para campos: nombre, email, teléfono, etc.

- [ ] Crear `/admin/assets/css/admin.css`
  - Diseño minimalista
  - Paleta: #E8EDF0, #E1E4E3, #8FA8AE, #425363, #A90101
  - Font: Inter de Google Fonts

**Entregable:** Admin panel funcional (básico)

#### Día 8-10: APIs Base (24 horas)
- [ ] Crear `/api/usuarios/crear.php` (POST)
- [ ] Crear `/api/usuarios/actualizar.php` (POST)
- [ ] Crear `/api/usuarios/eliminar.php` (POST)
- [ ] Crear `/api/ajustes/actualizar.php` (POST)
- [ ] Crear `/api/auth/login.php` (POST)
- [ ] Crear `/api/auth/logout.php` (POST)

**Entregable:** APIs funcionando con validación y seguridad

---

## SEMANA 3-4: HABITACIONES (100 horas)

### Semana 3: Estructura de Habitaciones

#### Día 1-5: CRUD de Habitaciones (40 horas)
- [ ] Crear `/admin/habitaciones.php` (listado)
  - Tabla con todas las habitaciones
  - Botones: Crear, Editar, Eliminar
  - Filtros básicos (estado, tipo)

- [ ] Crear `/admin/habitaciones/editar.php` (formulario)
  - Campos básicos: numero, tipo, estado, capacidad, precio_base
  - Multiidioma: nombre (ES/EN), slug (ES/EN), descripción (ES/EN)
  - SEO: titulo, descripcion, keywords

- [ ] Crear `/api/habitaciones/crear.php`
- [ ] Crear `/api/habitaciones/actualizar.php`
- [ ] Crear `/api/habitaciones/eliminar.php`
- [ ] Crear `/api/habitaciones/obtener.php`

**Entregable:** CRUD básico de habitaciones

#### Día 6-10: Sistema de Tarifas (40 horas)
- [ ] Crear interfaz de tarifas en formulario de habitación
  - Campos: tipo_tarifa (baja, alta, corporativo)
  - Precio, fecha_inicio, fecha_fin
  - Botón "+ Agregar tarifa"
  - Listado de tarifas activas

- [ ] Crear `/api/habitaciones/tarifas/crear.php`
- [ ] Crear `/api/habitaciones/tarifas/actualizar.php`
- [ ] Crear `/api/habitaciones/tarifas/eliminar.php`

- [ ] Crear función getPrecioActual() en `Database.php`
  - Obtiene tarifa vigente según fecha actual

**Entregable:** Sistema de tarifas flexible

### Semana 4: Campos Complejos + Galería

#### Día 1-4: Repeaters (Comodidades, Amenities, Camas) (32 horas)
- [ ] Crear interfaz de repeater para COMODIDADES
  - JavaScript para agregar/quitar items
  - Cada item: nombre + icono (select con Feather Icons)
  - Guardar como JSON en BD
  - Mostrar en admin editable

- [ ] Crear interfaz de repeater para AMENITIES
  - Mismo sistema que comodidades

- [ ] Crear interfaz de repeater para DISTRIBUCIÓN DE CAMAS
  - Cada item: tipo_cama (select) + cantidad + descripción
  - JavaScript que calcula capacidad_total_personas automáticamente
  - Guardar como JSON

- [ ] APIs: /api/habitaciones/comodidades/actualizar.php

**Entregable:** Campos repeaters funcionales

#### Día 5-10: Galería de Imágenes (40 horas)
- [ ] Crear `/admin/galeria.php` (listado)
  - Grid de imágenes subidas
  - Botón "+ Subir imagen"
  - Ver: nombre, tamaño, fecha, etiquetas
  - Botones: Ver detalle, Eliminar

- [ ] Crear `/admin/galeria/subir.php` (formulario)
  - Upload drag-drop (o file input)
  - Validación: peso máximo 5MB, formatos JPG/PNG/WebP
  - Vista previa

- [ ] Crear `/api/galeria/subir.php` (POST)
  - Recibe imagen
  - Valida
  - Redimensiona (1920x1080, 300x200, 150x100)
  - Convierte a WebP
  - Guarda en `/public/uploads/`
  - Retorna datos para BD

- [ ] Crear `/api/galeria/obtener-imagenes.php` (GET)
  - Retorna lista de imágenes para modal selector

- [ ] Crear `/admin/habitaciones/editar.php` - Sección de imágenes
  - Selector de imagen principal
  - Galería múltiple con drag-drop para ordenar
  - Modal para seleccionar de galería existente

**Entregable:** Sistema de galería centralizado y funcional

#### Día 8-10: Descripción HTML + Finales (24 horas)
- [ ] Integrar TinyMCE en campo de descripción
  - Botones: bold, italic, underline, lists, link, image
  - Guardar HTML en BD
  - Sanitización en servidor (solo tags permitidos)

- [ ] Crear función getRelacionadasHabitacion()
  - Obtiene habitaciones del mismo tipo

**Entregable:** Habitaciones 100% completas

---

## SEMANA 5: MULTIIDIOMA (40 horas)

#### Día 1-3: Sistema de Rutas Multiidioma (24 horas)
- [ ] Crear `/public/index.php` (enrutador principal)
  - Detectar idioma de URL: `/es/...` `/en/...`
  - Parsear rutas: habitaciones, habitacion/slug, blog, contacto, etc.
  - Redirigir `/habitaciones` → `/es/habitaciones`

- [ ] Crear `/public/.htaccess`
  - Rewrite rules para URLs limpias
  - Redirigir a index.php

- [ ] Crear sistema de vistas:
  - `/themes/default/inicio.php`
  - `/themes/default/habitaciones.php`
  - `/themes/default/habitacion-single.php`
  - `/themes/default/contacto.php`
  - `/themes/default/legal.php`
  - `/themes/default/404.php`

**Entregable:** Sistema de rutas multiidioma funcional

#### Día 4-7: Traducción de Slugs + Backend (24 horas)
- [ ] Crear función slugify() en Helpers.php
  - Convierte "Habitación Suite" → "habitacion-suite"
  - Maneja acentos y caracteres especiales

- [ ] Crear función getSlugTraducido()
  - Obtiene slug según idioma (habitación "Suite" → ES: "suite", EN: "deluxe-suite")

- [ ] Agregar selector de idioma en admin
  - Al editar habitación: [Español ▼] [Inglés ▼]
  - Mostrar campos para ambos idiomas

**Entregable:** Multiidioma integrado en admin

#### Día 8-10: Testing Multiidioma (8 horas)
- [ ] Verificar que URLs funcionan correctamente
- [ ] Verificar que contenido se muestra en idioma correcto
- [ ] Verificar SEO (meta tags por idioma)

**Entregable:** Multiidioma testeado

---

## SEMANA 6: PÁGINAS + EDICIÓN EN VIVO (50 horas)

#### Día 1-4: CRUD de Páginas (32 horas)
- [ ] Crear `/admin/paginas.php` (listado)
  - Tabla: página, slug, estado, acciones
  - Botones: Editar, Editar en Vivo, Eliminar

- [ ] Crear `/admin/paginas/editar.php` (formulario)
  - Título (ES/EN)
  - Slug (ES/EN)
  - Contenido HTML (TinyMCE)
  - SEO: titulo, descripcion, keywords
  - Checkboxes: Activa, Mostrar en menú
  - Orden (para menú)

- [ ] Crear APIs:
  - `/api/paginas/crear.php`
  - `/api/paginas/actualizar.php`
  - `/api/paginas/eliminar.php`

**Entregable:** CRUD de páginas funcional

#### Día 5-10: Edición en Vivo (40 horas)
- [ ] Crear `/themes/default/inicio.php?edit=1`
  - Sistema de edición en vivo
  - Cada sección editable tiene atributos: data-editable="true", data-field="seccion_hero"
  - Botón ✏️ flotante en cada sección

- [ ] Crear `/themes/default/assets/js/editor-vivo.js`
  - Mostrar/ocultar overlay de edición
  - Activar editor al hacer click
  - Panel flotante a la derecha
  - Guardar cambios con AJAX
  - Cancelar cambios (rollback)
  - Lock de edición (solo 1 usuario a la vez)

- [ ] Crear `/themes/default/assets/css/editor-vivo.css`
  - Estilos para overlay de edición
  - Estilos para panel flotante
  - Estilos para botón ✏️

- [ ] Crear `/api/paginas/actualizar-vivo.php` (POST)
  - Recibe: pagina_id, campo, datos_nuevos
  - Valida CSRF token
  - Verifica permiso (admin o super admin)
  - Verifica lock de edición
  - Actualiza en BD
  - Guarda en tabla de versiones (historial)
  - Retorna éxito/error

- [ ] Crear tabla `pagina_locks`
  - Controla que solo 1 usuario edite a la vez
  - Lock expira en 30 minutos

- [ ] Crear tabla `paginas_versiones`
  - Historial de cambios (campo, valor_anterior, valor_nuevo, usuario, fecha)

**Entregable:** Edición en vivo completamente funcional

---

## SEMANA 7: FORMULARIOS DINÁMICOS (50 horas)

#### Día 1-3: CRUD de Formularios (24 horas)
- [ ] Crear `/admin/formularios.php` (listado)
  - Tabla: formulario, tipo, estado, acciones
  - Botones: Editar, Ver respuestas, Eliminar

- [ ] Crear `/admin/formularios/editar.php` (formulario)
  - Nombre del formulario
  - Tipo: reserva | contacto | custom
  - Email de notificación
  - Mensaje de éxito

- [ ] Crear formularios preestablecidos:
  - Ejecutar script `/api/formularios/crear-preestablecidos.php` una sola vez
  - Crea: "Formulario de Reserva" + "Formulario de Contacto"

- [ ] Crear APIs:
  - `/api/formularios/crear.php`
  - `/api/formularios/actualizar.php`
  - `/api/formularios/eliminar.php`

**Entregable:** CRUD de formularios

#### Día 4-7: Campos Dinámicos + Respuestas (32 horas)
- [ ] Crear `/admin/formularios/editar.php` - Sección Campos
  - Interfaz para agregar/editar campos
  - Tipos: texto, email, telefono, textarea, select, checkbox, fecha
  - Label (ES/EN)
  - Requerido: sí/no
  - Placeholder
  - Orden (drag-drop)

- [ ] Crear `/admin/formularios/respuestas.php` (listado)
  - Tabla: todas las respuestas de formularios
  - Filtros: por formulario, por estado, por fecha
  - Botón: Ver detalle, Marcar como leída, Responder

- [ ] Crear `/admin/formularios/respuesta-detalle.php`
  - Mostrar datos de respuesta
  - Campo para respuesta del admin
  - Botón: Enviar respuesta

- [ ] Crear clase `FormularioRenderer.php`
  - Método render($slug) que genera formulario HTML dinámico
  - Valida en cliente (HTML5) + servidor (PHP)

- [ ] Crear `/api/formularios/procesar.php` (POST)
  - Recibe datos de formulario
  - Valida CSRF
  - Valida cada campo según configuración
  - Guarda en `formulario_respuestas`
  - Envía email de notificación a admin
  - Envía email de confirmación a cliente
  - Retorna JSON: éxito/error

- [ ] APIs:
  - `/api/formularios/respuestas/obtener.php`
  - `/api/formularios/respuestas/marcar-leida.php`
  - `/api/formularios/respuestas/responder.php`

**Entregable:** Sistema de formularios dinámicos funcional

#### Día 8-10: Integración en Frontend (18 horas)
- [ ] Crear modal para formulario de reserva
  - Se muestra en cada página de habitación
  - Populado dinámicamente con FormularioRenderer

- [ ] Crear página dedicada `/contacto` con formulario
  - Formulario de contacto
  - Información de contacto
  - Mapa de ubicación

- [ ] Integrar reCAPTCHA v3 en ambos formularios

**Entregable:** Formularios integrados en frontend

---

## SEMANA 8: BLOG CON SEO (50 horas)

#### Día 1-4: CRUD de Blog (32 horas)
- [ ] Crear `/admin/blog.php` (listado)
  - Tabla: artículo, autor, estado, fecha, acciones
  - Filtros: estado (borrador/publicado), categoría, autor
  - Botones: Editar, Editar en Vivo, Ver, Eliminar

- [ ] Crear `/admin/blog/editar.php` (formulario)
  - Título (ES/EN)
  - Slug (auto-generado pero editable)
  - Contenido HTML (TinyMCE)
  - Imagen destacada (selector de galería)
  - Categoría (select)
  - Tags (multiselect con buscar)
  - Estado: borrador | publicado | archivado
  - Marcar como destacado
  - Autor: (asigna automáticamente al usuario actual)

- [ ] Crear tablas:
  - `blog_entradas`
  - `blog_entradas_idiomas`
  - `blog_categorias`
  - `blog_tags`
  - `blog_entrada_tags`

- [ ] Crear APIs:
  - `/api/blog/crear.php`
  - `/api/blog/actualizar.php`
  - `/api/blog/eliminar.php`
  - `/api/blog/obtener.php`

**Entregable:** CRUD de blog funcional

#### Día 5-7: SEO Básico + Avanzado (24 horas)
- [ ] Crear tabla `blog_seo_basico`
  - Campo "modo": auto | manual
  - Si modo=auto: generar automáticamente titulo, descripcion, keywords
  - Si modo=manual: permitir que admin edite

- [ ] Crear clase `SEOAnalyzer.php`
  - Método analizarContenido($titulo, $contenido, $keywords_objetivo)
  - Retorna: palabras_totales, palabras_unicas, densidad, legibilidad_score, score_seo_total
  - Genera sugerencias

- [ ] Crear tabla `blog_seo_avanzado`
  - score_seo_total (0-100)
  - sugerencias (JSON con array de mejoras)

- [ ] Interfaz en `/admin/blog/editar.php` - Sección SEO
  - Mostrar SEO Básico (auto/manual)
  - Mostrar SEO Avanzado (solo super admin)
  - Score visual (barra 0-100)
  - Sugerencias de mejora

**Entregable:** SEO básico y avanzado para blog

#### Día 8-10: Schema + Artículos Relacionados (18 horas)
- [ ] Generar Schema BlogPosting automático
  - Guardar en tabla `blog_entradas_idiomas` como JSON

- [ ] Crear función getArticulosRelacionados()
  - Obtiene artículos con tags en común
  - Máximo 5 resultados
  - Excluye artículo actual

- [ ] Crear página detalle de artículo (`/blog/slug`)
  - Mostrar: título, contenido, autor, fecha, tags, imagen
  - Mostrar: artículos relacionados
  - Mostrar: botones para compartir en redes

**Entregable:** Blog con SEO y artículos relacionados

---

## SEMANA 9: FRONTEND BÁSICO (50 horas)

#### Día 1-3: Estructura Base del Theme (24 horas)
- [ ] Crear `/themes/default/assets/css/style.css`
  - Estilos base con Tailwind v4
  - Font: Inter de Google Fonts
  - Variables de colores

- [ ] Crear `/themes/default/partials/header.php`
  - Logo
  - Menú de navegación (Inicio, Nosotros, Habitaciones, Blog, Contacto, Legal)
  - Selector de idioma (ES/EN)
  - Botón WhatsApp flotante (si está habilitado)

- [ ] Crear `/themes/default/partials/footer.php`
  - Logo + descripción
  - Links rápidos
  - Información de contacto
  - Redes sociales
  - Links legales (Privacidad, Términos, Cookies)

- [ ] Crear páginas principales:
  - `/themes/default/inicio.php`
  - `/themes/default/habitaciones.php`
  - `/themes/default/habitacion-single.php`
  - `/themes/default/blog.php`
  - `/themes/default/blog-single.php`
  - `/themes/default/contacto.php`
  - `/themes/default/legal.php`
  - `/themes/default/404.php`

**Entregable:** Estructura base del theme

#### Día 4-7: Integración de Contenido (32 horas)
- [ ] Página Inicio
  - Mostrar Hero (de datos en BD)
  - Mostrar últimas 3 reseñas
  - Mostrar últimos 3 artículos del blog
  - CTA a reservar

- [ ] Página Habitaciones
  - Listado de todas las habitaciones activas
  - Grid responsive
  - Mostrar: imagen, nombre, precio actual, botón Ver detalle

- [ ] Página Detalle Habitación
  - Galería de imágenes (lightbox/slider)
  - Nombre + descripción
  - Comodidades con iconos
  - Amenities con iconos
  - Distribución de camas
  - Precios (Baja, Alta, Corporativo)
  - FAQs (acordeón)
  - Últimas 3 reseñas
  - Habitaciones relacionadas
  - Formulario de reserva (modal)

- [ ] Página Blog Listado
  - Grid/lista de artículos publicados
  - Imagen, titulo, extracto, autor, fecha, tags
  - Paginación

- [ ] Página Blog Detalle
  - Contenido completo
  - Autor + fecha
  - Tags
  - Botones compartir (redes sociales)
  - Artículos relacionados

- [ ] Página Contacto
  - Formulario de contacto
  - Información de contacto
  - Mapa con ubicación

**Entregable:** Frontend funcional con contenido

#### Día 8-10: Responsivo + JavaScript (18 horas)
- [ ] Crear `/themes/default/assets/js/main.js`
  - Selector de idioma (cambiar idioma + redirigir)
  - Mostrar modal de promociones (si existen)
  - Validación básica de formularios en cliente
  - Lazy loading de imágenes

- [ ] Crear `/themes/default/assets/css/responsive.css`
  - Mobile first responsive
  - Breakpoints: 640px, 768px, 1024px, 1280px

- [ ] Testing en dispositivos móviles

**Entregable:** Frontend responsive y funcional

---

## SEMANA 10: SEO GLOBAL (40 horas)

#### Día 1-3: Meta Tags + Sitemap (24 horas)
- [ ] Crear función generateMetaTags() en `includes/SEO.php`
  - Genera meta title, description, keywords
  - Genera Open Graph tags (imagen, título, descripción)
  - Genera Twitter card tags

- [ ] Crear `/sitemap.xml` dinámico
  - Incluye: habitaciones, blog, páginas estáticas, legal
  - Genera URLs por idioma (/es/..., /en/...)
  - Actualiza automáticamente cuando hay cambios

- [ ] Crear `/robots.txt`
  - Permite: / (raíz)
  - Disallow: /admin/, /api/ (si es necesario)

- [ ] Integrar en todas las páginas:
  - Llamar a generateMetaTags() en header
  - Meta tags renderizados dinámicamente

**Entregable:** SEO técnico implementado

#### Día 4-7: Schema + Hreflang (24 horas)
- [ ] Crear clase `SchemaGenerator.php`
  - Método generateHotelSchema()
  - Método generateRoomSchema()
  - Método generateArticleSchema()

- [ ] Agregar schema global (Hotel) en homepage

- [ ] Agregar schema en página de habitación (LodgingBusiness)

- [ ] Agregar schema en página de artículo (BlogPosting)

- [ ] Implementar hreflang tags
  - En cada página: <link rel="alternate" hreflang="es" href="...">
  - En cada página: <link rel="alternate" hreflang="en" href="...">
  - En sitemap: alternate URLs

**Entregable:** Schema + hreflang implementados

#### Día 8-10: Google Integrations (16 horas)
- [ ] Crear función integrarGoogleAnalytics()
  - Insertar código de GA4 en footer (si google_analytics_id existe)
  - Rastrear eventos: page_view, generate_lead, view_item

- [ ] Crear modal para Google Search Console (opcional)
  - Verificación de propiedad

**Entregable:** Google Analytics integrado

---

## SEMANA 11: RESEÑAS + PROMOCIONES + ANALYTICS (50 horas)

#### Día 1-3: Sistema de Reseñas (24 horas)
- [ ] Crear `/admin/resenas.php` (listado)
  - Tabla: reseña, puntuación, tipo, mostrar, acciones
  - Filtros: tipo (hotel/habitación), estado (moderada/no)
  - Botones: Ver, Editar, Eliminar, Publicar/Ocultar

- [ ] Crear `/admin/resenas/editar.php`
  - Campos: nombre_cliente, email, pais, puntuación (1-5), título, contenido
  - Checkbox: Mostrar en sitio
  - Checkbox: Moderada

- [ ] Crear APIs:
  - `/api/resenas/crear.php`
  - `/api/resenas/actualizar.php`
  - `/api/resenas/eliminar.php`
  - `/api/resenas/obtener-promedio.php`

- [ ] Crear función getResenas($tipo, $tipo_id, $limite = 5)
  - Obtiene reseñas ordenadas por fecha descendente
  - Calcula rating promedio automáticamente

- [ ] Mostrar reseñas en frontend:
  - En página de inicio: últimas 3 reseñas
  - En página de habitación: últimas 5 reseñas

**Entregable:** Sistema de reseñas funcional

#### Día 4-6: Sistema de Promociones (18 horas)
- [ ] Crear `/admin/promociones.php` (listado)
  - Tabla: promoción, código, descuento, estado, fechas
  - Botones: Editar, Eliminar

- [ ] Crear `/admin/promociones/editar.php`
  - Título (ES/EN)
  - Descripción (ES/EN)
  - Código de descuento
  - Tipo: porcentaje | cantidad_fija
  - Valor
  - Imagen (selector de galería)
  - Publicada: sí/no
  - Fechas: inicio, fin (o sin fecha)

- [ ] Crear APIs:
  - `/api/promociones/crear.php`
  - `/api/promociones/actualizar.php`
  - `/api/promociones/eliminar.php`
  - `/api/promociones/obtener-activas.php`

- [ ] Mostrar promoción en frontend (modal)
  - JavaScript que obtiene promociones activas
  - Muestra modal con imagen + descripción + código
  - Se cierra y recuerda por 1 día (cookie)

**Entregable:** Sistema de promociones funcional

#### Día 7-10: FAQs + Analytics Admin (24 horas)
- [ ] Crear `/admin/habitaciones/faqs.php`
  - Agregar/editar FAQs por habitación
  - Campos: pregunta (ES/EN), respuesta (ES/EN), orden
  - Drag-drop para reordenar

- [ ] Crear `/admin/hotel-faqs.php`
  - FAQs generales del hotel (mismo formato)

- [ ] Mostrar FAQs en frontend:
  - En página de habitación: acordeón con FAQs
  - Generar Schema FAQPage automático

- [ ] Crear `/admin/dashboard.php` - Sección Analytics
  - Mostrar últimos 7 días: visitas, usuarios, pageviews
  - Top 5 páginas más vistas
  - Tasa de rebote
  - Dispositivos (mobile vs desktop)
  - Datos de Google Analytics (vía API o manualmente)

**Entregable:** FAQs + Analytics dashboard

---

## SEMANA 12: QA + OPTIMIZACIÓN (40 horas)

#### Día 1-3: Testing (24 horas)
- [ ] Testing manual de login
- [ ] Testing de CRUD de habitaciones
- [ ] Testing de galería de imágenes
- [ ] Testing de formularios (validación, envío email)
- [ ] Testing de edición en vivo
- [ ] Testing de multiidioma (URLs, contenido)
- [ ] Testing en navegadores: Chrome, Firefox, Safari, Edge
- [ ] Testing en móvil: iOS, Android

- [ ] Verificar seguridad:
  - SQL Injection (prepared statements)
  - XSS (htmlspecialchars)
  - CSRF (tokens)
  - Rate limiting
  - Session timeout

**Entregable:** Testing completado, bugs arreglados

#### Día 4-6: Optimización + Performance (16 horas)
- [ ] Minificación de CSS/JS
- [ ] Lazy loading de imágenes (nativo)
- [ ] Caché de imagenes (headers HTTP)
- [ ] Compresión GZIP en servidor

- [ ] Lighthouse audit
  - Performance > 90
  - Accessibility > 90
  - SEO > 90

**Entregable:** Optimizaciones completadas

#### Día 7-10: Documentación + Deploy (20 horas)
- [ ] Crear documentación de código (comentarios PHPDoc)
- [ ] Crear README.md con instrucciones de instalación
- [ ] Crear manual de usuario para admin
- [ ] Crear script de backup de BD
- [ ] Crear script de deploy (para servidor de hosting)
- [ ] Setup en servidor de hosting compartido (webempresa)
- [ ] Validar que funciona en producción
- [ ] Setup de HTTPS

**Entregable:** Proyecto listo para producción

---

## RESUMEN DE ENTREGABLES POR SEMANA

| Semana | Componente | Estado |
|--------|-----------|--------|
| 1-2 | Auth, Seguridad, Admin Base | ✅ |
| 3-4 | Habitaciones, Galería | ✅ |
| 5 | Multiidioma | ✅ |
| 6 | Páginas, Edición en Vivo | ✅ |
| 7 | Formularios Dinámicos | ✅ |
| 8 | Blog con SEO | ✅ |
| 9 | Frontend | ✅ |
| 10 | SEO Global | ✅ |
| 11 | Reseñas, Promociones, Analytics | ✅ |
| 12 | Testing, Optimización, Deploy | ✅ |

---

## HORAS ESTIMADAS POR SEMANA

| Semana | Horas | Acumulado |
|--------|-------|-----------|
| 1-2 | 80 | 80 |
| 3-4 | 100 | 180 |
| 5 | 40 | 220 |
| 6 | 50 | 270 |
| 7 | 50 | 320 |
| 8 | 50 | 370 |
| 9 | 50 | 420 |
| 10 | 40 | 460 |
| 11 | 50 | 510 |
| 12 | 40 | 550 |

**TOTAL: ~550 horas**
**Para 1 dev: 13-14 semanas (puede optimizarse)**
**Para 2 devs: 6-7 semanas en paralelo**

