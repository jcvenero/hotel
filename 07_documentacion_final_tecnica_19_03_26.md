# DOCUMENTACION FINAL TECNICA

**Proyecto:** CMS Hotelero  
**Fecha:** 19 de marzo de 2026  
**Estado:** Documento tecnico final consolidado  
**Fuente principal:** `00_Documentacion_tecnica_2026.md`

---

## 0. Objetivo del documento

Este documento consolida la definicion tecnica final del proyecto tomando como base:

- `00_Documentacion_tecnica_2026.md` como documento rector y final
- la documentacion tecnica previa como referencia historica
- las decisiones adicionales confirmadas por el cliente el 19/03/2026

Su objetivo es dejar una base unica, clara y operativa para el desarrollo del sistema.

---

## 1. Regla de jerarquia documental

Se establece oficialmente que:

- `00_Documentacion_tecnica_2026.md` corrige, mejora y reemplaza conceptualmente la documentacion anterior cuando exista conflicto
- este documento resume la version tecnica final vigente
- los documentos anteriores se conservan como apoyo, pero no prevalecen sobre esta definicion consolidada

---

## 2. Vision final del proyecto

El proyecto es un **CMS hotelero propio** desarrollado en PHP y MySQL para entornos XAMPP y hosting tradicional.

No se trata solo de un panel administrativo. Su proposito real es reemplazar una instalacion basada en WordPress y plugins premium, incluyendo capacidades equivalentes a:

- constructor visual controlado
- SEO avanzado
- GEO avanzado
- Schema avanzado
- multiidioma nativo
- seguridad transversal
- optimizacion de rendimiento
- biblioteca multimedia reutilizable
- asistencia transversal con IA

El sistema debe servir como producto base reutilizable para hoteles, con frontend publico, backend administrativo y arquitectura mantenible.

---

## 3. Stack y entorno oficial

### Backend

- PHP 8.1+
- MySQL 5.7+
- PDO
- Composer

### Frontend

- HTML5
- CSS propio servido localmente
- JavaScript vanilla

### Librerias base previstas

- `intervention/image`
- `phpmailer/phpmailer`
- `monolog/monolog`

### IA

- proveedor inicial: Gemini API
- integracion desacoplada del proveedor

### Entorno local oficial

- frontend: `http://localhost/hotelcore/`
- admin: `http://localhost/hotelcore/admin`

### Produccion

- frontend: `https://www.midominio.com`
- admin bajo `/admin`

---

## 4. Arquitectura general final

Se mantiene la arquitectura base:

- `config/`
- `includes/`
- `api/`
- `admin/`
- `themes/`
- `public/`
- `languages/`
- `logs/`
- `sql/`

### Criterio general

- el frontend publico vive en el theme
- el backend administrativo vive bajo `/admin`
- el contenido editable no vive en archivos del theme
- el theme define estructura, layout y componentes
- la base de datos guarda el contenido editable
- la edicion en vivo actua sobre contenido en BD, no sobre archivos fisicos

---

## 5. Modelo funcional del CMS

Los modulos principales oficiales son:

- usuarios, roles y permisos
- ajustes
- multiidioma
- habitaciones
- tipos de habitacion
- tarifas
- galeria multimedia
- paginas del theme
- edicion en vivo
- paginas legales
- blog
- formularios dinamicos
- respuestas de formularios
- reseñas
- promociones
- FAQs
- SEO basico
- SEO avanzado
- GEO basico
- GEO avanzado
- Schema basico
- Schema avanzado
- analytics
- auditoria y seguridad
- IA transversal

---

## 6. Reglas oficiales del panel y frontend

### Panel administrativo

- el panel sera monolingue en español
- si el usuario no esta autenticado y entra a `/admin`, debe ser redirigido al login
- si esta autenticado, podra acceder segun sus permisos

### Frontend publico

- el frontend sera multidioma real desde el inicio
- idiomas iniciales: `es` y `en`
- el selector de idioma debe intentar conservar la pagina equivalente
- si falta traduccion en ingles, el sistema mostrara el contenido en español con aviso visible

---

## 7. Usuarios, roles y permisos

### Roles base oficiales

- `super_admin`
- `admin`
- `editor`
- `recepcionista`

### Decision final

El sistema no trabajara solo con jerarquia simple. Desde la primera fase funcional debe existir:

- roles base
- permisos por modulo
- permisos por accion
- capacidad del `super_admin` para ajustar permisos por usuario

### Matriz base

Los permisos deben venir predeterminados por rol, pero el `super_admin` podra modificarlos segun las tareas del usuario.

### Alcances generales

- `super_admin`: control total del sistema
- `admin`: gestion general con limites segun permisos avanzados
- `editor`: contenido
- `recepcionista`: operacion de formularios, contactos, reservas y modulos permitidos

### Requisitos obligatorios

- auditoria visible en panel
- control de acciones sensibles
- capacidad del `super_admin` para bloquear usuarios
- capacidad del `super_admin` para invalidar sesiones

---

## 8. Ajustes del sistema

### Nombre oficial

El nombre oficial del modulo y referencia tecnica final es:

- `ajustes`

### Funcion del modulo

`ajustes` es el area madre de configuracion global del sistema.

Debe organizarse por secciones:

- ajustes basicos
- ajustes SEO, GEO y Schema globales
- ajustes avanzados
- ajustes tecnicos restringidos

### Acceso

- `admin` puede gestionar la capa basica
- `super_admin` controla la capa avanzada y tecnica

---

## 9. Multiidioma final

### Regla general

- admin en español
- frontend multidioma real
- contenido editable por idioma

### Aplicacion obligatoria

El multiidioma aplica al menos a:

- habitaciones
- paginas del theme
- blog
- promociones
- FAQs visibles
- contenido legal
- SEO
- GEO
- Schema

### Slugs

- los slugs son traducidos por idioma

### Fallback oficial

Si falta traduccion completa en EN:

- se muestra ES
- se muestra aviso de traduccion pendiente

---

## 10. Paginas del theme y edicion en vivo

### Modelo oficial

Las paginas publicas principales viven como estructura del theme y contenido en base de datos.

En esta fase:

- no habra paginas publicas custom libres
- no habra constructor libre de bloques
- no se podran crear bloques nuevos desde admin
- los bloques editables los define el desarrollador

### Paginas base oficiales

- inicio
- nosotros
- contacto

Adicionalmente:

- paginas legales

### Regla de contenido

El contenido editable debe vivir por bloques o claves tecnicas estables en base de datos.

Ejemplos conceptuales:

- `home.hero.title`
- `home.hero.subtitle`
- `contacto.formulario.intro`

### Edicion en vivo

- se edita viendo la pagina completa
- se guarda por bloque o seccion
- el lock de trabajo aplica por pagina
- debe existir historial y versionado

---

## 11. Habitaciones

### Naturaleza del modulo

Habitaciones es un modulo central del CMS y una entidad comercial publicable.

### Modelo oficial

- la habitacion depende obligatoriamente de un `tipo_habitacion`
- `numero_habitacion` deja de ser obligatorio
- el nombre comercial por idioma es el eje del modulo
- la habitacion no gobierna el precio directamente

### Contenido obligatorio por habitacion

- nombre comercial por idioma
- descripcion por idioma
- slugs por idioma
- comodidades por idioma
- amenities por idioma
- configuracion de camas
- imagen principal
- galeria de imagenes
- FAQs por idioma
- SEO basico y avanzado por idioma
- GEO basico y avanzado por idioma
- Schema basico y avanzado por idioma

### Frontend obligatorio

La pagina individual de habitacion es obligatoria desde el inicio.

Debe incluir al menos:

- galeria
- descripcion
- comodidades
- amenities
- camas
- tarifas visibles
- FAQs
- reseñas relacionadas
- formulario de reserva contextual

---

## 12. Tipos de habitacion

### Regla final

Los tipos de habitacion son una entidad propia administrable.

El admin puede:

- crear tipos
- editar tipos
- eliminar tipos

Ejemplos:

- simple
- doble
- matrimonial
- deluxe

### Funcion principal

El tipo de habitacion organiza la oferta comercial y gobierna la logica tarifaria.

No se define en esta fase como pagina publica independiente ni como entidad con contenido publico propio.

---

## 13. Tarifas y temporadas

### Estructura oficial

Existen cuatro tarifas oficiales:

- `baja`
- `regular`
- `alta`
- `manual`

### Temporadas globales

Las temporadas:

- `baja`
- `regular`
- `alta`

se configuran globalmente para todo el hotel.

Estas temporadas deben permitir multiples rangos de fechas.

Ejemplo conceptual:

- baja: `01 enero - 31 marzo`
- baja: `01 octubre - 29 diciembre`

### Configuracion tarifaria

El sistema debe tener dos capas:

1. Configuracion global de temporadas
2. Tarifas por `tipo_habitacion`

### Tarifas por tipo

Para cada `tipo_habitacion` se guardan los importes de:

- baja
- regular
- alta
- manual

### Tarifa manual

- la tarifa manual tiene switch de activacion
- se activa por `tipo_habitacion`
- cuando esta activa, tiene prioridad maxima
- si esta activa, domina sobre cualquier temporada por fecha

### Habitaciones

Cuando se crea una habitacion:

- ya queda vinculada a un `tipo_habitacion`
- sus precios se resuelven automaticamente segun ese tipo
- no se cargan precios manuales por habitacion en esta fase

### Regla de visualizacion

Si no existe tarifa valida:

- no se debe inventar precio
- el frontend debe mostrar un mensaje equivalente a consultar precio o solicitar cotizacion

---

## 14. Galeria multimedia

### Naturaleza del modulo

La galeria es una biblioteca multimedia central del CMS.

### Alcance

Debe servir al menos a:

- habitaciones
- paginas del theme
- blog
- promociones
- ajustes

### Politica de archivos

- limite oficial por imagen: `1MB`
- `alt_text` es obligatorio
- metadatos minimos: `alt_text`, `tipo`, `etiquetas`

### Procesamiento

El sistema debe generar derivados optimizados:

- original o respaldo
- webp principal
- thumbnail
- tiny

### Eliminacion segura

La recomendacion adoptada es implementar desde el inicio un sistema de validacion de referencias de uso para evitar eliminacion ciega.

Debe revisar al menos referencias desde:

- habitaciones
- blog
- promociones
- ajustes
- contenido de paginas del theme segun el modelo implementado

---

## 15. Formularios dinamicos

### Formularios base obligatorios

- contacto
- reserva

### Modelo oficial

Se mantiene:

- `formularios`
- `formulario_campos`
- `formulario_respuestas`

### Contexto operativo

La recomendacion aprobada es:

- guardar el payload del usuario en `datos` JSON
- guardar el contexto operativo en columnas estructuradas

Columnas sugeridas de contexto:

- `pagina_origen`
- `tipo_entidad_origen`
- `entidad_origen_id`
- `idioma_origen`
- `ip_cliente`
- `user_agent`

### Reserva contextual

Si el formulario se envia desde una habitacion:

- el usuario no selecciona la habitacion manualmente
- el sistema toma el contexto automaticamente

### Permisos

- `admin` puede definir formularios
- `recepcionista` puede gestionar respuestas

---

## 16. Blog

### Modelo editorial minimo por idioma

- titulo
- slug
- resumen o extracto
- contenido
- SEO basico
- SEO avanzado
- GEO basico
- GEO avanzado
- Schema basico
- Schema avanzado

### Reglas

- multidioma real desde el inicio
- categorias y tags
- imagen destacada desde la galeria central
- articulos relacionados por categoria y tags
- `editor` puede crear contenido

---

## 17. Reseñas y promociones

### Reseñas

- gestion interna por el equipo
- no habra envio publico libre de reseñas en esta fase
- tipos: hotel y habitacion
- deben integrarse con schema de rating y estrellas
- el promedio debe recalcularse automaticamente al crear, editar u ocultar reseñas

### Promociones

- multidioma desde el inicio
- aplicables al hotel o a habitaciones especificas
- visibles publicamente en modal o bloques del theme

---

## 18. SEO, GEO y Schema

### Regla general

SEO, GEO y Schema son pilares del CMS desde el inicio.

No son una mejora futura.

### Cobertura obligatoria

Deben existir desde el inicio para:

- paginas del theme
- habitaciones
- blog
- promociones cuando aplique
- contenido legal cuando corresponda

### Decision de arquitectura

Se adopta un modelo hibrido:

- capa basica integrada dentro de cada entidad o modulo
- capa avanzada centralizada por entidad e idioma

### Criterio operativo

Cada entidad publica relevante debe poder tener:

- SEO basico por idioma
- SEO avanzado por idioma
- GEO basico por idioma
- GEO avanzado por idioma
- Schema basico por idioma
- Schema avanzado por idioma

### Generacion y control

- el sistema genera una base automatica coherente
- el usuario autorizado puede editar la capa basica
- la capa avanzada y overrides quedan controlados por permisos

---

## 19. IA transversal

### Regla oficial

La IA forma parte del sistema desde el inicio.

### Alcance

Debe asistir al menos en:

- blog
- habitaciones
- paginas del theme
- promociones
- FAQs
- formularios cuando aplique
- SEO
- GEO
- Schema

### Proveedor inicial

- Gemini API

### Regla tecnica

La integracion debe quedar desacoplada del proveedor para permitir cambio futuro.

### Regla de seguridad y publicacion

- toda salida IA debe ser visible
- toda salida IA debe ser editable
- toda salida IA debe ser revisable
- ninguna salida IA debe publicarse automaticamente
- la IA actua como borrador, sugerencia o apoyo

---

## 20. Seguridad transversal

La seguridad es transversal y obligatoria desde el inicio.

Incluye como minimo:

- autenticacion segura
- autorizacion por permisos
- CSRF en endpoints de escritura
- validacion de inputs
- escape de outputs
- sanitizacion consistente de HTML enriquecido
- rate limiting
- anti-spam y captcha en formularios publicos
- proteccion de uploads
- headers de seguridad
- HTTPS en produccion
- auditoria visible
- control de sesiones

---

## 21. Performance y optimizacion

### Reglas oficiales

- arquitectura liviana
- CSS servido localmente
- sin dependencia de runtime CSS externo en produccion
- imagenes optimizadas con politica oficial de `1MB`
- fuentes servidas localmente cuando aplique
- `robots.txt` y `sitemap.xml` obligatorios
- optimizacion de consultas de contenido publico

---

## 22. Criterios finales de implementacion

### Lo que no se implementa como modelo principal en esta fase

- page builder libre
- creacion libre de bloques desde admin
- paginas publicas custom libres
- publicacion automatica por IA
- tarifas manuales por habitacion como modelo principal

### Lo que si se implementa desde el inicio

- permisos finos por accion y modulo
- auditoria visible
- control de sesiones
- IA transversal
- SEO, GEO y Schema basico y avanzado
- frontend multidioma real
- bloques del theme editables desde BD
- galeria central con eliminacion segura
- habitaciones ligadas a tipos
- temporadas globales y tarifas por tipo

---

## 23. Conclusion

La definicion tecnica final del proyecto queda establecida sobre una arquitectura modular, controlada y escalable.

El sistema se desarrollara como un CMS hotelero propio con:

- frontend publico controlado por theme
- backend administrativo por permisos
- contenido editable en base de datos
- multiidioma nativo
- SEO, GEO y Schema como pilares
- IA como capacidad transversal bajo control humano

Este documento debe entenderse como la referencia tecnica consolidada vigente para iniciar el desarrollo.
