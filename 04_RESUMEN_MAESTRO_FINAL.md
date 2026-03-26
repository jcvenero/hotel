# PROYECTO GESTOR HOTELERO - DOCUMENTO MAESTRO FINAL

**Fecha:** 08 de marzo de 2026  
**Versión:** 1.0 - FINAL  
**Estado:** Listo para desarrollo 89 

---

## ÍNDICE DE DOCUMENTOS

Este proyecto está documentado en 4 documentos principales:

1. **01_SCHEMA_BASE_DATOS_COMPLETO.md** ← Schema de BD con 37 tablas detalladas
2. **02_TIMELINE_FASE_1_DETALLADO.md** ← Timeline de 12 semanas con tareas específicas
3. **03_ESTRUCTURA_CARPETAS_Y_TECNICA.md** ← Estructura de carpetas y guía técnica
4. **PROYECTO_RESUMEN_MAESTRO.md** ← Resumen ejecutivo (este documento)

---

## 1. VISIÓN GENERAL DEL PROYECTO

### Nombre
**Sistema de Gestión Hotelero**

### Objetivo
Crear un sistema CMS completo para hoteles pequeños (10-20 habitaciones) que:
- ✅ Permita al admin gestionar habitaciones, página web, blog, reservas
- ✅ Sea rápido y liviano (sin frameworks pesados)
- ✅ Sea SEO-friendly desde el inicio
- ✅ Soporte 2 idiomas (ES/EN) de forma nativa
- ✅ Sea seguro contra inyecciones SQL, XSS, CSRF, etc.
- ✅ Sea replicable: cada hotel en su propio hosting

### Alcance
- **Fase 1 (12 semanas)**: MVP completo con todas funcionalidades básicas
- **Fase 2 (después)**: Mejoras avanzadas, integraciones, marketplace

### Tecnología
```
Backend:    PHP 8.1+ | MySQL 5.7+ | PDO
Frontend:   HTML5 | Tailwind CSS v4 | JavaScript vanilla
Servidor:   XAMPP (desarrollo) → Hosting compartido (producción)
```

---

## 2. FUNCIONALIDADES FASE 1

### 🏨 ADMIN PANEL

#### Dashboard
- Estadísticas principales
- Últimas actividades
- Notificaciones
- Gráficos de análisis

#### Habitaciones (Módulo complejo)
- **CRUD completo**: crear, editar, eliminar, listar
- **Tipos de habitación**: Simple, Doble, Suite, Deluxe, etc.
- **Sistema de tarifas**: Baja, Alta, Corporativo con fechas
- **Campos repeaters**:
  - Comodidades (nombre + icono)
  - Amenities (nombre + icono)
  - Distribución de camas (tipo + cantidad + descripción)
- **Descripción HTML**: Editor TinyMCE
- **Galería de imágenes**: imagen principal + múltiples
- **FAQs**: por habitación
- **SEO**: título, descripción, keywords
- **Schema**: LodgingBusiness JSON-LD automático

#### Galería de Imágenes Centralizada
- Upload de imágenes
- Validación: máx 5MB, formatos JPG/PNG/WebP
- Procesamiento automático:
  - Conversión a WebP
  - Redimensionamiento (1920x1080, 300x200, 150x100)
- Organización: etiquetas, búsqueda
- Ver dónde se usa cada imagen

#### Gestión de Páginas
- Crear páginas custom (Inicio, Nosotros, Contacto, etc.)
- **Edición en vivo**: 
  - Admin abre página como cliente la ve
  - Cada sección editable tiene botón ✏️
  - Editor flotante a la derecha
  - Cambios en tiempo real
  - Lock de edición (solo 1 usuario a la vez)
  - Historial de cambios
- Multiidioma (ES/EN)
- SEO: título, descripción, keywords

#### Blog
- CRUD de artículos
- **Categorías**: clasificación
- **Tags**: palabras clave (artículos relacionados automático)
- **SEO Básico**: auto-generado + override manual
- **SEO Avanzado**: análisis de contenido, score 0-100, sugerencias
- **Schema**: BlogPosting JSON-LD automático
- Multiidioma (ES/EN)

#### Formularios Dinámicos
- **2 formularios preestablecidos en Fase 1**:
  1. Formulario de Reserva (en cada habitación)
  2. Formulario de Contacto (página contacto)
- Campos dinámicos: texto, email, teléfono, textarea, select, checkbox, fecha
- Validación servidor + cliente
- Gestión de respuestas en admin
- Notificaciones por email automáticas
- reCAPTCHA v3 para anti-spam
- Rate limiting: 5 respuestas por IP/hora

#### Reseñas y Ratings
- Admin agrega reseñas manualmente
- Campos: nombre, país, puntuación (1-5), título, contenido
- Mostrar/ocultar en sitio
- Rating promedio auto-calculado
- Schema AggregateRating automático
- Mostrar en: Inicio (últimas 3), Habitación (últimas 5)

#### Promociones
- Crear/editar promociones
- Código de descuento
- Tipo: porcentaje | cantidad fija
- Fechas vigencia (o sin fecha)
- Imagen personalizada
- Mostrar en **MODAL** cuando cliente entra
- Se cierra y recuerda por 1 día (cookie)

#### Usuarios
- CRUD de usuarios
- **Roles**:
  - Super Admin: todo
  - Admin: gestionar contenido, NO usuarios
  - Editor: solo contenido
  - Recepcionista: solo gestionar reservas/contactos
- Historial de sesiones

#### Ajustes del Sitio
- **Branding**: nombre, descripción, logo, favicon
- **Contacto**: dirección, teléfono, email, WhatsApp, horario
- **SEO global**: meta tags, Google Analytics ID
- **Redes sociales**: Facebook, Instagram, TripAdvisor
- **Páginas legales**: Privacidad, Términos, Cookies (editable)
- **Integraciones**: APIs de servicios

#### SEO/GEO Avanzado
- **SEO Básico**: auto-generado + override manual
- **SEO Avanzado**: análisis de contenido
- **GEO**: Ubicación, LocalBusiness Schema, keywords geográficos
- **Google My Business**: integración URL
- **Tracking de rankings**: tabla para guardar posiciones
- **Schema**: Hotel, Habitación, BlogPosting, FAQ, Rating

#### Analytics
- Panel de estadísticas
- Google Analytics 4 integrado
- Métricas: usuarios, pageviews, tasa de rebote
- Top 5 páginas más vistas
- Última actualización: 24-48 horas (limitación de Google)

### 🌍 FRONTEND (Sitio Público)

#### Páginas Principales
- **Inicio**: Custom según cliente (Hero, Servicios, Testimonios, Promociones, etc.)
- **Nosotros**: Custom según cliente (Historia, Equipo, Valores, etc.)
- **Habitaciones**: Listado grid de habitaciones activas
- **Detalle Habitación**: Galería, descripción, comodidades, FAQs, reseñas, formulario de reserva
- **Blog**: Listado de artículos, categorías, tags
- **Detalle Artículo**: Contenido completo, autor, compartir, relacionados
- **Contacto**: Formulario, información, mapa
- **Páginas legales**: Privacidad, Términos, Cookies, Aviso legal

#### Características
- **Multiidioma nativo**: ES/EN en URLs limpias (/es/..., /en/...)
- **Responsive**: Mobile-first, todos los dispositivos
- **SEO optimizado**: Meta tags, Schema, Sitemap, Robots.txt
- **Fast**: Sin frameworks pesados, Tailwind v4
- **Accesible**: WCAG 2.1

#### Componentes Interactivos
- Galería lightbox
- Formularios con validación en tiempo real
- Selector de idioma
- Botón WhatsApp flotante (elegible on/off)

---

## 3. ESTRUCTURA DE BASE DE DATOS

### Total: 37 Tablas

**Core**:
- usuarios
- sitio_ajustes

**Habitaciones** (11 tablas):
- habitaciones
- habitaciones_idiomas
- habitacion_tarifas
- habitacion_comodidades
- habitacion_amenities
- habitacion_configuracion_camas
- habitacion_imagenes
- habitacion_faqs
- imagenes
- tipos_habitacion

**Páginas** (5 tablas):
- paginas
- paginas_idiomas
- paginas_versiones
- pagina_locks
- paginas_legales
- paginas_legales_idiomas

**Blog** (5 tablas):
- blog_entradas
- blog_entradas_idiomas
- blog_categorias
- blog_tags
- blog_entrada_tags

**Formularios** (3 tablas):
- formularios
- formulario_campos
- formulario_respuestas

**Reseñas** (1 tabla):
- resenas
- hotel_faqs

**Promociones** (3 tablas):
- promociones
- promociones_idiomas
- promocion_habitaciones

**SEO/Analytics** (4 tablas):
- seo_geo
- ranking_keywords
- blog_seo_basico
- blog_seo_avanzado
- event_logs
- analytics

**Ver documento 01_SCHEMA_BASE_DATOS_COMPLETO.md para detalles de cada tabla**

---

## 4. TIMELINE FASE 1

### Duración Total: 12 semanas (84 días)

#### Semana 1-2: Fundamentos (80 horas)
- Setup XAMPP + estructura
- BD complete
- Clases base: Database, Auth, Validator, Escaper, Security
- Sistema de CSRF
- Admin login + dashboard básico

#### Semana 3-4: Habitaciones (100 horas)
- CRUD completo
- Tarifas por temporada
- Repeaters: comodidades, amenities, camas
- Descripción HTML
- Galería completa

#### Semana 5: Multiidioma (40 horas)
- Sistema de rutas (/es/..., /en/...)
- Traducción de slugs
- Admin con selector de idioma

#### Semana 6: Páginas + Edición en Vivo (50 horas)
- CRUD de páginas
- Edición en vivo (editor flotante)
- Lock de edición
- Historial de versiones

#### Semana 7: Formularios Dinámicos (50 horas)
- CRUD de formularios
- Campos dinámicos
- Validación + emails
- Gestión de respuestas

#### Semana 8: Blog con SEO (50 horas)
- CRUD de artículos
- Categorías + Tags
- SEO básico + avanzado
- Artículos relacionados

#### Semana 9: Frontend (50 horas)
- Maquetación base
- Integración de contenido
- Responsivo

#### Semana 10: SEO Global (40 horas)
- Meta tags dinámicos
- Sitemap XML
- Schema.org
- Hreflang

#### Semana 11: Reseñas + Promociones + Analytics (50 horas)
- Sistema de reseñas
- Módulo de promociones
- Panel analytics
- FAQs

#### Semana 12: QA + Deployment (40 horas)
- Testing completo
- Optimización
- Documentación
- Deploy a hosting

**TOTAL: ~550 horas**

**Ver documento 02_TIMELINE_FASE_1_DETALLADO.md para tareas día a día**

---

## 5. SEGURIDAD IMPLEMENTADA

✅ **SQL Injection Prevention**: Prepared statements con PDO  
✅ **XSS Prevention**: htmlspecialchars() en todo output  
✅ **CSRF Protection**: CSRF tokens en formularios  
✅ **Password Hashing**: bcrypt con password_hash()  
✅ **Input Validation**: Validación exhaustiva  
✅ **Rate Limiting**: Login (5 intentos/15 min), Formularios (5/hora)  
✅ **Session Timeout**: 30 minutos sin actividad  
✅ **Logging**: Auditoría completa de cambios  
✅ **HTTPS**: Obligatorio en producción  
✅ **Security Headers**: X-Frame-Options, X-Content-Type-Options, CSP  
✅ **File Upload Security**: Validación peso, tipo MIME, no-ejecución  
✅ **Anti-spam**: reCAPTCHA v3 en formularios  

---

## 6. ARQUITECTURA

### Backend
```
includes/ → Clases principales (Database, Auth, Validator, etc.)
api/      → Endpoints REST
admin/    → Panel administrativo
```

### Frontend
```
themes/default/ → Tema del sitio
public/        → Carpeta web (uploads, index.php)
```

### Seguridad
```
Prepared statements (PDO)
CSRF tokens
Input validation
Output escaping
Rate limiting
Session management
Logging auditoría
```

**Ver documento 03_ESTRUCTURA_CARPETAS_Y_TECNICA.md para detalles completos**

---

## 7. COLORES Y DISEÑO

### Paleta Corporativa (Admin)
```
#E8EDF0  → Fondo principal
#E1E4E3  → Fondos secundarios
#8FA8AE  → Textos secundarios
#425363  → Textos principales
#A90101  → Acentos (botones críticos)
```

### Tipografía
- **Font**: Inter (Google Fonts)
- **Estilo**: Minimalista, limpio
- **Espacios**: Generosos, sin desorden

### Frontend
- Según requerimiento cliente (custom por cada proyecto)

---

## 8. CARACTERÍSTICAS ESPECIALES

### Edición en Vivo
Admin abre página como cliente la ve, hace click en sección, panel flotante aparece a la derecha, cambios se ven en tiempo real.

### Sistema de Tarifas Flexible
Baja, Alta, Corporativo con fechas configurables. Permite cambios sin afectar schema.

### Multiidioma Nativo
Cada tabla tiene tabla_idiomas. Slugs traducidos por idioma.

### Blog con SEO Completo
- Análisis automático de contenido
- Score SEO 0-100
- Sugerencias de mejora
- BlogPosting Schema automático
- Artículos relacionados por tags

### Promociones con Modal
Se muestra al cliente al entrar, se recuerda por 1 día.

### Formularios Dinámicos
Admin puede crear campos sin código. Preestablecidos para Reserva y Contacto.

### FAQs por Habitación
Cada habitación tiene sus propias FAQs con Schema FAQPage automático.

---

## 9. PRÓXIMAS ACCIONES

### Antes de Empezar
1. ✅ Leer los 4 documentos en orden
2. ✅ Entender la estructura de BD (37 tablas)
3. ✅ Entender el timeline (12 semanas, tareas día a día)
4. ✅ Preparar XAMPP y herramientas

### Semana 1
1. Clonar/crear estructura de proyecto
2. Configurar .env y BD
3. Ejecutar schema.sql
4. Crear clases base

### Semana 2
1. Implementar Auth
2. Crear dashboard
3. Integrar CSRF, Logger, Mailer

### Semana 3+
Seguir timeline exactamente

---

## 10. FASE 2 - LO QUE VIENE DESPUÉS

### Funcionalidades Fase 2
- Constructor visual de formularios (drag-drop bloques)
- Integraciones Booking.com / Airbnb / TripAdvisor
- Sincronización de disponibilidad automática
- Sistema de pagos (Stripe, PayPal)
- Calendario interactivo
- Email marketing integrado
- Más idiomas (FR, DE, PT, IT)
- Reseñas sincronizadas con Google/TripAdvisor
- Mobile app
- Dashboard de inteligencia (IA)

### Lo que NO se hace
- ❌ Marketplace multi-propiedad (no está en planes)
- ❌ Sistema de bloques drag-drop (Fase 3)

---

## 11. ENTREGABLES FASE 1

✅ Panel administrativo funcional y seguro  
✅ Frontend público optimizado para SEO  
✅ Base de datos normalizada (37 tablas)  
✅ Multiidioma nativo (ES/EN)  
✅ Sistema de seguridad completo  
✅ Blog con SEO avanzado  
✅ Formularios dinámicos reutilizables  
✅ Galería de imágenes con WebP  
✅ Edición en vivo de páginas  
✅ Reseñas y promociones  
✅ Analytics integrado  
✅ Documentación completa  
✅ Script de deploy  

---

## 12. CHECKLIST FINAL

### Documentación
- ✅ 01_SCHEMA_BASE_DATOS_COMPLETO.md (37 tablas con detalles)
- ✅ 02_TIMELINE_FASE_1_DETALLADO.md (12 semanas, tareas día a día)
- ✅ 03_ESTRUCTURA_CARPETAS_Y_TECNICA.md (carpetas, guía técnica)
- ✅ PROYECTO_RESUMEN_MAESTRO.md (este documento)

### Decisiones Confirmadas
- ✅ PHP vanilla sin frameworks
- ✅ MySQL con PDO prepared statements
- ✅ Tailwind CSS v4
- ✅ Edición en vivo real (no modal)
- ✅ Multiidioma ES/EN nativo
- ✅ Blog en Fase 1
- ✅ Reseñas en Fase 1
- ✅ GEO Avanzado en Fase 1
- ✅ FAQs por habitación en Fase 1
- ✅ Formularios dinámicos en Fase 1
- ✅ Promociones con modal
- ✅ WhatsApp elegible (on/off)
- ✅ Páginas custom según cliente

### Listo para Codificar
✅ SÍ, todo está definido y listo.

---

## REFERENCIAS RÁPIDAS

### Documentos
1. **Schema BD**: 01_SCHEMA_BASE_DATOS_COMPLETO.md
2. **Timeline**: 02_TIMELINE_FASE_1_DETALLADO.md
3. **Técnica**: 03_ESTRUCTURA_CARPETAS_Y_TECNICA.md
4. **Resumen**: PROYECTO_RESUMEN_MAESTRO.md (este)

### Tablas Principales
- `habitaciones` (11 tablas relacionadas)
- `paginas` (5 tablas)
- `blog_entradas` (5 tablas)
- `formularios` (3 tablas)
- `promociones` (3 tablas)

### Clases Principales
- `Database.php` (PDO wrapper)
- `Auth.php` (autenticación)
- `Validator.php` (validación)
- `Escaper.php` (XSS prevention)
- `Security.php` (rate limiting, headers)
- `Language.php` (multiidioma)
- `ImageHandler.php` (imágenes)
- `SEO.php` (meta tags)
- `SEOAnalyzer.php` (análisis SEO)
- `Mailer.php` (emails)

### URLs Principales (Frontend)
- `/es/` → Inicio en español
- `/en/` → Inicio en inglés
- `/es/habitaciones` → Listado habitaciones español
- `/es/habitacion/{slug}` → Detalle habitación
- `/es/blog` → Listado blog
- `/es/contacto` → Contacto
- `/es/{pagina-custom}` → Páginas custom

### URLs Principales (Admin)
- `/admin/login.php` → Login
- `/admin/dashboard.php` → Dashboard
- `/admin/habitaciones.php` → Gestión habitaciones
- `/admin/blog.php` → Gestión blog
- `/admin/paginas.php` → Gestión páginas

---

## CONCLUSIÓN

Este proyecto es **completo, realista y ejecutable** en 12 semanas.

**Está diseñado para ser:**
- ✅ Rápido de desarrollar
- ✅ Fácil de mantener
- ✅ Seguro por defecto
- ✅ Escalable a Fase 2
- ✅ Replicable para otros hoteles

**Todo está documentado al máximo detalle para:**
- Máxima claridad
- Mínimas dudas
- Máxima velocidad de desarrollo

**Todos los 4 documentos son necesarios:**
1. **Schema** → Entiende la BD
2. **Timeline** → Entiende el cronograma
3. **Técnica** → Entiende la arquitectura
4. **Resumen** → Entiende el big picture

---

**Documento Finalizado: 08 de marzo de 2026**  
**Versión: 1.0 - FINAL**  
**Estado: ✅ LISTO PARA DESARROLLO**

