# DOCUMENTACION TECNICA 2026

**Proyecto:** CMS Hotelero  
**Estado:** Documento vivo de decisiones tecnicas  
**Inicio de consolidacion:** 17 de marzo de 2026

---

## 0. Proposito del documento

Este documento sera la referencia tecnica viva del proyecto a partir de ahora.

No reemplaza por completo la documentacion anterior, pero si corrige, aclara y consolida las decisiones reales que se vayan validando durante el desarrollo.

La idea es revisar modulo por modulo y dejar asentado:

- que se mantiene
- que se corrige
- que se elimina
- que cambia respecto a la documentacion anterior

---

## 1. Vision real del proyecto

Este proyecto no es solamente un panel hotelero.

Su objetivo real es reemplazar una instalacion basada en WordPress y plugins premium, especialmente:

- constructor visual tipo Brick Builder
- SEO avanzado tipo Yoast SEO Pro
- campos dinamicos tipo ACF Pro
- multiidioma tipo Polylang Pro
- plugins de seguridad
- plugins de optimizacion y rendimiento

Por eso, los pilares del CMS son:

- SEO avanzado
- GEO avanzado
- Schema avanzado
- multiidioma nativo
- seguridad
- rendimiento
- edicion viva del frontend
- asistencia con IA

Estos elementos no son complementarios. Son parte del motor principal del sistema.

---

## 1.1 IA como capacidad transversal del CMS

Se establece como criterio oficial del proyecto que el CMS debe incorporar una capa de asistencia mediante IA.

La IA no se considera un agregado opcional aislado, sino una capacidad transversal que debe apoyar multiples modulos del sistema.

### 1.1.1 Proposito de la IA

La IA debe existir para ayudar a:

- acelerar tareas editoriales
- mejorar calidad de contenido
- apoyar la configuracion SEO
- apoyar configuracion GEO
- apoyar configuracion Schema
- sugerir mejoras estructurales y semanticas
- facilitar trabajo al administrador sin depender de plugins externos

### 1.1.2 Alcance funcional de la IA

La IA debe poder asistir en al menos las siguientes areas:

- generacion de contenidos para blog
- apoyo en descripciones de habitaciones
- apoyo en textos de paginas
- apoyo en FAQs
- apoyo en descripciones promocionales
- apoyo en textos de formularios o mensajes del sistema cuando aplique
- sugerencias SEO basicas
- sugerencias SEO avanzadas
- sugerencias GEO basicas
- sugerencias GEO avanzadas
- sugerencias y generacion asistida de Schema

### 1.1.3 Regla de uso

La IA debe funcionar como asistente, no como autoridad automatica incuestionable.

Esto significa que:

- la IA propone
- la IA sugiere
- la IA optimiza
- la IA completa
- la IA analiza

Pero el sistema no debe asumir automaticamente que toda salida generada por IA es correcta o debe publicarse sin revision.

### 1.1.4 Principio de control humano

Toda salida de IA relevante para contenido o SEO/GEO/Schema debe ser:

- visible
- editable
- revisable
- confirmable por un usuario autorizado

La IA no debe reemplazar el criterio humano en decisiones criticas del proyecto.

### 1.1.5 IA en contenido editorial

La IA podra utilizarse para:

- redactar borradores
- mejorar textos existentes
- resumir contenido
- ampliar contenido
- reescribir con otro tono
- proponer titulos
- proponer extractos
- traducir o adaptar contenido entre idiomas cuando corresponda

Esto aplica especialmente a modulos futuros como:

- blog
- habitaciones
- paginas
- promociones
- FAQs

### 1.1.6 IA en SEO, GEO y Schema

La IA tambien debera asistir tanto en nivel basico como en nivel avanzado.

#### SEO basico asistido por IA

La IA puede ayudar a:

- proponer meta titles
- proponer meta descriptions
- sugerir slugs
- mejorar claridad editorial del contenido
- detectar ausencia de elementos SEO basicos

#### SEO avanzado asistido por IA

La IA puede ayudar a:

- proponer estrategia de keywords
- revisar estructura semantica
- detectar oportunidades de enlazado interno
- sugerir mejoras de headings
- sugerir mejoras de contenido orientadas a posicionamiento
- analizar enfoque de contenido por intencion de busqueda
- apoyar reglas mas complejas de optimizacion

#### GEO basico asistido por IA

La IA puede ayudar a:

- redactar contenido con contexto geografico
- mejorar textos de ubicacion
- completar informacion local de forma asistida
- reforzar coherencia entre negocio y contexto geografico

#### GEO avanzado asistido por IA

La IA puede ayudar a:

- proponer enfoque geolocalizado por pagina o entidad
- detectar oportunidades de SEO local
- fortalecer consistencia entre contenido, ubicacion y entidad del negocio
- apoyar estrategia local por zona, ciudad, region o segmento geografico

#### Schema basico asistido por IA

La IA puede ayudar a:

- completar propiedades faltantes
- sugerir valores de campos estructurados
- asistir en generacion de datos estructurados comunes
- apoyar el llenado de esquemas standard del sistema

#### Schema avanzado asistido por IA

La IA puede ayudar a:

- proponer estructuras mas ricas
- completar relaciones entre entidades
- sugerir extensiones de JSON-LD
- asistir en validaciones semanticas
- mejorar coherencia entre schema, contenido y SEO

### 1.1.7 Niveles de permiso para funciones asistidas por IA

La IA no debe romper el modelo de roles y permisos.

Por tanto:

- un usuario solo debe poder usar funciones asistidas por IA dentro del alcance de permisos que tenga
- el `super_admin` debe tener control total sobre funciones avanzadas asistidas por IA
- el `admin` debe tener acceso a funciones basicas y a aquellas avanzadas que el `super_admin` habilite
- usuarios con menos privilegios solo deben acceder a herramientas asistidas compatibles con su rol o permisos otorgados

### 1.1.8 Coste y proveedor

Se establece como criterio actual que la IA debe integrarse, si es posible, mediante una API gratuita o de muy bajo costo, suficiente para tareas de asistencia del sistema.

Esto implica que la arquitectura debe contemplar:

- limites de uso
- control de errores
- posibilidad de desactivar funciones de IA
- comportamiento estable aunque la API no responda
- degradacion elegante del sistema

### 1.1.9 Regla de resiliencia

El CMS no debe depender de la IA para funcionar en su operacion principal.

Si la IA falla o no esta disponible:

- el CMS debe seguir funcionando
- el contenido debe seguir siendo editable manualmente
- SEO/GEO/Schema deben poder gestionarse manualmente

La IA mejora el sistema, pero no debe volverlo dependiente de un servicio externo para su funcionamiento basico.

### 1.1.10 Decision oficial

La IA queda declarada como capacidad transversal del CMS.

Esto afecta desde ahora la definicion de modulos presentes y futuros.

En adelante, al revisar cada modulo, debera considerarse tambien:

- si requiere asistencia por IA
- en que nivel
- con que permisos
- y con que grado de control humano

---

## 2. Modulo 1: Arquitectura base y coherencia general

### 2.1 Evaluacion general

La arquitectura base del proyecto esta bien orientada.

La separacion actual entre carpetas principales es correcta y se mantiene:

- `config/`
- `includes/`
- `api/`
- `admin/`
- `themes/`
- `public/`
- `languages/`
- `logs/`
- `sql/`

Esta estructura si es valida para un CMS propio, modular, mantenible y replicable por cliente.

### 2.2 Decision

Se mantiene la arquitectura general del proyecto.

No se considera necesario rehacer la estructura base de carpetas.

Lo que si se debe corregir progresivamente es la coherencia interna entre:

- documentacion
- schema
- codigo implementado

---

## 3. Frontend publico y backend administrativo

### 3.1 Frontend

El frontend publico es lo que debe ver el cliente final o usuario del negocio.

En local, deben resolver al frontend:

- `http://localhost/hotel`
- `http://localhost/hotel/index.php`

En hosting o produccion, debe resolver al frontend:

- `https://www.midominio.com`

### 3.2 Backend

El backend administrativo debe resolver en:

- `http://localhost/hotel/admin`
- `http://localhost/hotel/admin/index.php`

Regla obligatoria:

- si el usuario no esta autenticado, debe ser redirigido al login
- si esta autenticado, puede ingresar segun sus permisos

### 3.3 Decision tecnica

Se recomienda mantener un unico punto de entrada publico para el frontend, idealmente mediante `public/index.php` o una implementacion equivalente bien resuelta con reglas de servidor.

El backend se mantiene separado bajo `/admin`.

---

## 4. Themes

### 4.1 Funcion del theme

`themes/` representa el frontend completo del hotel.

No se considera un simple sistema de skins ni plantillas menores.

Cada cliente tendra:

- su propio hosting
- su propia instalacion
- su propio theme

Por ahora, cada instalacion trabajara con un solo theme.

No se desarrollara por el momento un sistema multi-theme dentro de una sola instalacion.

### 4.2 Contenido del theme

El theme debe contener:

- estructura
- layout
- componentes
- maquetacion visual
- estilos del frontend
- comportamiento visual del sitio publico

El theme puede incluir paginas como:

- Inicio
- Nosotros
- Habitaciones
- Blog
- Contacto

Y cualquier otra pagina o seccion requerida por el cliente.

### 4.3 Decision importante

El theme no debe ser la fuente principal del contenido editable.

El contenido editable debe vivir en base de datos.

El theme define estructura y presentacion.

La base de datos define contenido editable.

Esto aplica especialmente a:

- textos
- bloques editables
- contenido por idioma
- metadatos SEO
- contenido GEO
- schemas generados o asociados

---

## 5. Edicion en vivo

### 5.1 Regla adoptada

La edicion en vivo no debe modificar archivos fisicos del theme.

Debe modificar contenido guardado en base de datos.

### 5.2 Motivo

Esta decision se adopta porque editar archivos:

- vuelve fragil el sistema
- complica el mantenimiento
- dificulta el versionado
- complica auditoria
- dificulta rollback
- complica traducciones

En cambio, editar contenido en base de datos permite:

- historial de cambios
- bloqueo de edicion
- auditoria
- restauracion
- multiidioma
- integracion SEO/GEO/Schema

### 5.3 Regla tecnica

Cada seccion editable del frontend debe tener una clave estable definida por desarrollo.

El desarrollador crea la estructura en el theme.

El CMS asocia esa estructura a contenido editable en BD.

---

## 6. CSS y separacion visual

Se establece como criterio global del proyecto:

- el CSS del backend debe ser totalmente independiente del frontend
- el CSS del frontend debe ser totalmente independiente del backend

No se debe compartir la misma hoja de estilos como base entre ambos contextos.

Esto aplica tambien a:

- componentes
- scripts
- patrones visuales

Motivo:

- evitar acoplamientos innecesarios
- mantener claridad tecnica
- facilitar mantenimiento
- permitir evolucion visual independiente

---

## 7. Scripts sueltos en raiz

Se revisaron los siguientes archivos:

- `check_forms.php`
- `check_seasons.php`
- `check_struct.php`
- `check_tables.php`
- `check_tarifas.php`
- `create_forms_tables.php`
- `seed_forms.php`

### 7.1 Conclusiones

Estos scripts no forman parte del sistema normal de produccion.

No son mecanismos de seguridad reales del CMS.

No actuan como proteccion contra ataques, sanitizacion activa, firewall, hardening ni defensa de runtime.

Su funcion actual es principalmente:

- diagnostico manual
- inspeccion de base de datos
- soporte temporal de desarrollo
- residuos de iteraciones anteriores

### 7.2 Clasificacion actual

#### Utilitarios temporales de desarrollo

- `check_tables.php`
- `check_tarifas.php`
- `check_seasons.php`
- `check_struct.php`

#### Scripts desalineados o potencialmente obsoletos

- `check_forms.php`
- `create_forms_tables.php`
- `seed_forms.php`

Estos ultimos reflejan una variante de formularios que no coincide claramente con el schema principal actual.

### 7.3 Decision

Por ahora no se eliminan automaticamente.

Pero dejan de considerarse parte formal de la arquitectura principal.

Posteriormente deberan:

- moverse a una carpeta de utilidades de desarrollo, o
- eliminarse si se confirma que ya no representan el modelo real del sistema

---

## 8. Configuracion del sistema

Actualmente gran parte de la configuracion esta concentrada en `config/database.php`.

Esto funciona como solucion inicial, pero no es ideal a largo plazo para un sistema de esta complejidad.

### 8.1 Decision preliminar

La configuracion del proyecto debera evolucionar hacia una estructura mas modular.

Ejemplos de separacion futura:

- configuracion de base de datos
- configuracion general de app
- seguridad
- uploads
- idiomas
- SEO

### 8.2 Nota

Esta reorganizacion se considera correcta, pero no se ejecuta todavia dentro del Modulo 1.

Queda asentada como criterio tecnico de evolucion.

---

## 9. Coherencia del dominio

### 9.1 Problema detectado

Actualmente existen diferencias entre:

- documentacion original
- schema SQL
- implementacion real en codigo

Eso genera ambiguedad sobre cual es la version oficial del modelo del sistema.

### 9.2 Criterio adoptado

Antes de seguir creciendo modulo por modulo, cada modulo debe consolidarse dejando clara una sola version oficial de su dominio.

Esto no significa detener el proyecto.

Significa que cada modulo auditado debe terminar con decisiones tecnicas claras y vigentes.

### 9.3 Aplicacion

Desde este documento, cada modulo revisado debera dejar definido:

- que sigue vigente
- que se corrige
- que se descarta
- que cambia respecto a documentos anteriores

---

## 10. Estado final del Modulo 1

### Se mantiene

- estructura general de carpetas
- separacion entre frontend y backend
- sistema de themes por cliente
- backend en `/admin`
- frontend publico desacoplado del panel
- `public/uploads` como base de archivos publicos

### Se corrige conceptualmente

- el contenido editable no vivira en archivos del theme
- la edicion en vivo debe trabajar sobre base de datos
- los scripts sueltos dejan de considerarse parte formal del sistema
- el CSS de backend y frontend debe permanecer separado

### Se deja como criterio global

- SEO avanzado, GEO avanzado y Schema avanzado son parte central del CMS
- el sistema debe construirse como reemplazo real de WordPress + plugins premium
- cada modulo revisado debe consolidarse por escrito en este documento

---

## 11. Proximos modulos

El siguiente modulo a revisar es:

- Usuarios, roles y permisos

Luego se continuara con los demas modulos en el orden que se vaya definiendo y validando.

---

## 13. Modulo 2: Usuarios, roles y permisos

### 13.1 Evaluacion general

El sistema actual de usuarios existe y funciona como base operativa.

Actualmente se apoya en una jerarquia simple de roles, suficiente para un MVP administrativo, pero todavia no representa el sistema avanzado de permisos que el proyecto necesita.

La direccion correcta del modulo no es quedarse en jerarquia fija solamente.

La direccion correcta es evolucionar hacia:

- roles base
- permisos por caracteristica o modulo
- capacidad del super admin para asignar o bloquear capacidades
- auditoria visible de acciones de usuarios

### 13.2 Roles base vigentes

Se mantienen como roles base del sistema:

- `super_admin`
- `admin`
- `editor`
- `recepcionista`

Estos roles siguen siendo la base inicial del control de acceso.

### 13.3 Decision estructural

El sistema no debe quedarse limitado a una simple jerarquia fija.

Se adopta como criterio oficial que el proyecto evolucionara hacia:

- roles base iniciales
- permisos asignables por modulo o funcionalidad
- posibilidad futura de roles personalizados

### 13.4 Regla principal del super admin

El `super_admin` es la autoridad maxima del sistema.

Debe poder:

- crear usuarios
- asignar roles
- otorgar permisos especiales
- bloquear caracteristicas o accesos a otros usuarios
- definir quienes acceden a funciones avanzadas del sistema

Esto incluye especialmente permisos sobre:

- SEO avanzado
- GEO avanzado
- Schema avanzado
- configuraciones criticas
- usuarios
- seguridad

### 13.5 Alcance del admin

El `admin` no tendra acceso automatico a capacidades avanzadas completas.

Su comportamiento debe dividirse asi:

- puede manejar administracion general del CMS
- puede usar SEO, GEO y Schema en nivel basico
- no puede usar por defecto SEO avanzado, GEO avanzado o Schema avanzado

El acceso a funciones adicionales podra depender de permisos otorgados por el `super_admin`.

En esta misma logica:

- el `admin` podra usar SEO, GEO y Schema basico
- el acceso a funciones avanzadas o asistidas por IA en esos dominios dependera de permisos otorgados por `super_admin`

### 13.6 Alcance del editor

El `editor` queda orientado a contenido.

No se considera automaticamente autorizado para funciones avanzadas de SEO, GEO o Schema.

Si en algun momento se requiere, eso debera depender de permisos puntuales otorgados por `super_admin`.

Esto tambien aplica a funciones asistidas por IA fuera de su alcance editorial permitido.

### 13.7 Alcance del recepcionista

El `recepcionista` debe poder trabajar con funciones operativas relacionadas con negocio y atencion.

Se acepta que pueda acceder, al menos, a:

- formularios
- reservas
- contactos
- reseñas
- promociones
- habitaciones, segun el criterio operativo que se vaya afinando por permisos

La implementacion exacta despues debera concretarse por permisos por modulo, no solo por rol nominal.

### 13.8 Sistema de permisos futuro

Se adopta como decision tecnica que el proyecto debe pasar de:

- control por jerarquia simple

a:

- control por rol base mas permisos por modulo o accion

Ejemplos de permisos futuros:

- ver usuarios
- crear usuarios
- editar usuarios
- eliminar usuarios
- ver ajustes
- editar ajustes
- gestionar habitaciones
- gestionar galeria
- gestionar formularios
- gestionar blog
- gestionar paginas
- gestionar reseñas
- gestionar promociones
- usar SEO basico
- usar SEO avanzado
- usar GEO basico
- usar GEO avanzado
- usar Schema basico
- usar Schema avanzado
- usar IA editorial
- usar IA para SEO basico
- usar IA para SEO avanzado
- usar IA para GEO basico
- usar IA para GEO avanzado
- usar IA para Schema basico
- usar IA para Schema avanzado

### 13.9 Primer acceso y contraseñas

Se define lo siguiente:

- el `super_admin` inicial no esta obligado a cambiar su contraseña en el primer acceso
- los usuarios nuevos creados por el sistema si deben cambiar su contraseña en su primer login

Esto queda como comportamiento deseado oficial del modulo.

### 13.10 Auditoria

Se confirma como requisito del proyecto:

- debe existir historial o auditoria visible de acciones de usuarios dentro del panel

Esta auditoria debera cubrir, idealmente:

- login
- logout
- creacion de registros
- actualizacion de registros
- eliminacion de registros
- cambios de permisos
- cambios de configuracion
- cambios de contenido critico

### 13.11 Estado actual del modulo

#### Lo que ya existe y se mantiene

- autenticacion funcional
- validacion de password con hash
- sesion con timeout
- roles base funcionales
- CRUD basico de usuarios
- restricciones basicas sobre `super_admin`
- login administrativo con CSRF y rate limiting

#### Lo que se considera insuficiente a futuro

- sistema de permisos solo por jerarquia
- falta de permisos finos por modulo
- falta de politica completa de primer login para nuevos usuarios
- falta de auditoria visible integrada en el panel
- falta de modelo formal de capacidades asignables por `super_admin`

### 13.12 Decision final del modulo 2

Se mantiene el sistema actual como base operativa.

Pero se declara oficialmente que la meta del modulo no es una jerarquia simple, sino un sistema avanzado de:

- roles base
- permisos asignables
- capacidades bloqueables o habilitables por `super_admin`
- auditoria de acciones

Tambien se establece que el sistema de permisos futuro debera contemplar funciones asistidas por IA como parte del modelo de acceso.

Esto pasa a ser el criterio tecnico vigente para futuras correcciones e implementaciones del modulo.

---

## 14. Modulo 3: Ajustes del sitio

### 14.1 Evaluacion general

El modulo de ajustes del sitio es un componente central del CMS.

No se considera un simple formulario auxiliar de configuracion.

Cumple una funcion estructural porque reemplaza parte de lo que en otros entornos se resolveria con:

- opciones globales del tema
- campos globales personalizados
- plugins de configuracion
- plugins de branding
- plugins de integracion

### 14.2 Decision sobre el nombre de la tabla

Se define como criterio oficial que el nombre simplificado correcto debe ser:

- `ajustes`

No se adopta `sitio_ajustes` como nombre final para la implementacion vigente.

Por tanto, a futuro la documentacion y el codigo deben converger hacia `ajustes` como referencia oficial.

### 14.3 Naturaleza del modulo

El area de Ajustes debe existir como area madre de configuracion global del sistema.

Sin embargo, no debe crecer como un megamodulo unico, plano y sin jerarquia.

La direccion correcta es mantener el dominio general de `Ajustes`, pero dividido internamente por submodulos o secciones con responsabilidades claras.

### 14.4 Estructura recomendada del area de ajustes

El area de ajustes debe dividirse conceptualmente en:

#### Ajustes basicos

Incluye:

- branding
- contacto
- informacion publica del negocio
- datos generales del hotel
- redes sociales
- OTAs y enlaces publicos
- informacion legal visible del sitio

#### Ajustes SEO, GEO y Schema globales

SEO, GEO y Schema son pilares del CMS desde el inicio del proyecto.

Por lo tanto:

- no se consideran un agregado futuro
- no se postergan para una fase posterior
- deben existir desde el inicio como parte del sistema

Pero al mismo tiempo:

- no deben quedar absorbidos y diluidos dentro de ajustes generales como si fueran solo un campo mas

La solucion correcta es esta:

- deben estar presentes desde el inicio
- deben tener relacion con ajustes globales
- deben separarse conceptualmente del resto de ajustes comunes

Es decir:

- forman parte del sistema desde el principio
- pero deben tratarse como una capa diferenciada y de alta prioridad

#### Ajustes avanzados

Incluye configuraciones de mayor impacto o sensibilidad funcional, especialmente cuando el sistema crezca en complejidad.

#### Ajustes tecnicos restringidos

Incluye:

- API keys
- integraciones sensibles
- webhooks
- parametros tecnicos
- mantenimiento
- debugging
- limites operativos criticos

Esta parte no debe quedar abierta como configuracion general para cualquier usuario administrativo.

### 14.5 Regla de acceso por rol

Se adopta el siguiente criterio:

- `admin` puede gestionar la parte basica del modulo
- `super_admin` puede gestionar la parte avanzada y tecnica

Adicionalmente, en el futuro el `super_admin` podra habilitar permisos puntuales segun el sistema avanzado de permisos definido en el modulo 2.

### 14.6 SEO, GEO y Schema dentro de ajustes

Se deja formalmente establecido lo siguiente:

- SEO, GEO y Schema son pilares del CMS desde el inicio
- no deben desaparecer del area de configuracion global
- pero tampoco deben reducirse a simples campos secundarios dentro de un formulario de ajustes generales

Por eso, la decision correcta del proyecto es:

- mantener su presencia desde el inicio
- integrarlos como parte esencial del sistema
- tratarlos como una capa diferenciada dentro de la arquitectura

Esto significa que:

- existiran configuraciones globales SEO, GEO y Schema del sitio
- y al mismo tiempo existiran reglas y capacidades mas profundas en sus propios contextos funcionales

### 14.7 Integraciones y API keys

Se establece como criterio oficial que las integraciones sensibles y API keys deben quedar en una seccion tecnica mas restringida.

No se considera correcto que queden mezcladas al mismo nivel que branding, contacto o redes.

### 14.8 Estado actual del modulo

#### Se mantiene

- la existencia del modulo de ajustes
- el enfoque de configuracion global
- branding
- contacto
- redes y OTAs
- datos legales
- integraciones
- configuraciones generales del negocio

#### Se corrige conceptualmente

- el nombre oficial debe simplificarse a `ajustes`
- el modulo no debe crecer como bloque unico sin estructura interna
- debe diferenciar claramente lo basico, lo avanzado y lo tecnico
- SEO, GEO y Schema deben estar desde el inicio, pero con tratamiento de pilar estructural, no como campos secundarios dispersos

### 14.9 Decision final del modulo 3

El modulo de Ajustes del sitio queda oficialmente definido asi:

- existe como area madre del sistema
- usa `ajustes` como nombre simplificado de referencia
- se organizara por submodulos o secciones funcionales
- separara lo basico de lo tecnico
- mantendra SEO, GEO y Schema desde el inicio como pilar estructural del CMS
- el acceso a partes avanzadas o tecnicas quedara restringido a `super_admin` o permisos equivalentes

---

## 15. Modulo 4: Multiidioma

### 15.1 Evaluacion general

El multiidioma no se considera una caracteristica secundaria.

Es una capacidad estructural del CMS y forma parte directa del objetivo de reemplazar soluciones tipo Polylang Pro o similares.

En este proyecto, el multiidioma debe ser nativo.

No se debe implementar como un agregado superficial sobre contenido ya construido.

### 15.2 Regla general del sistema

Se define como criterio oficial:

- el panel administrativo sera monolingue en español
- el frontend sera multidioma real

Esto significa que:

- la interfaz interna del CMS no necesita traducirse completamente a otros idiomas
- pero todo lo que impacta el frontend si debe contemplar version por idioma

### 15.3 Alcance del multiidioma

El multiidioma debe aplicarse al menos a:

- habitaciones
- paginas del frontend
- blog
- promociones
- FAQs visibles al usuario final
- SEO basico
- SEO avanzado
- GEO basico
- GEO avanzado
- Schema basico
- Schema avanzado

Esto aplica tambien a las paginas construidas desde el theme.

### 15.4 Idiomas objetivo iniciales

Los idiomas iniciales oficiales del proyecto son:

- español (`es`)
- ingles (`en`)

### 15.5 Idioma del panel administrativo

El panel administrativo se define como:

- solo en español

No se considera prioritario traducir toda la interfaz administrativa a otros idiomas en esta etapa.

Sin embargo, dentro del panel deben existir herramientas para editar contenido del frontend en ambos idiomas.

### 15.6 Edicion de contenido multidioma

La edicion del contenido multidioma se realizara mediante:

- pestañas por idioma

Esto se considera mejor que mezclar ambos idiomas en un solo flujo visual sin separacion clara.

La regla adoptada es:

- el admin trabaja en español
- el contenido editable del frontend debe presentar pestañas `ES` y `EN`

### 15.7 Slugs

Se establece como criterio oficial:

- los slugs deben ser traducidos por idioma

No se adopta una estrategia de slug unico compartido para todos los idiomas.

### 15.8 Comportamiento del frontend cuando falta traduccion

Si un contenido en ingles no tiene traduccion completa disponible, el sistema debe:

- mostrar el contenido en español
- presentar un aviso amable indicando que la traduccion aun no esta disponible

Esto se adopta como fallback oficial del proyecto.

### 15.9 Alertas de traduccion faltante en el panel

El panel administrativo debe alertar cuando falten traducciones.

Esto aplica a entidades como:

- habitaciones
- paginas
- blog
- contenido legal
- metadatos SEO
- datos GEO
- configuracion Schema

La idea es que el sistema permita detectar de forma visible que partes siguen incompletas en otro idioma.

### 15.10 Paginas del theme y contenido multidioma

Las paginas del frontend construidas en el theme tambien deben ser multidioma.

Ejemplos:

- inicio
- nosotros
- contacto
- landings especiales
- cualquier otra pagina del theme

La forma correcta de resolverlo no es guardar el contenido traducido dentro de archivos fisicos del theme.

La arquitectura correcta es:

- el theme define estructura, layout y componentes
- la base de datos guarda contenido editable por idioma

### 15.11 Modelo recomendado para paginas del theme

Se adopta como criterio de implementacion:

- las paginas del theme tendran bloques o secciones con claves estables
- cada clave podra tener contenido por idioma en base de datos

Ejemplos conceptuales:

- `home.hero_title`
- `home.hero_subtitle`
- `home.cta_text`
- `nosotros.historia_title`
- `contacto.intro_text`

Esto permite:

- diseñar libremente el theme
- conservar edicion en vivo
- soportar multiidioma real
- conectar contenido con SEO/GEO/Schema por idioma

### 15.12 Edicion en vivo multidioma

La edicion en vivo debe soportar tambien el contexto multidioma.

Regla oficial:

- el admin entra al panel en español
- al editar una pagina o seccion visible del frontend, el panel lateral debe poder mostrar pestañas por idioma
- el contenido de cada idioma se guarda por separado

Adicionalmente, la IA podra asistir por idioma en:

- generacion de textos
- traduccion o adaptacion
- optimizacion editorial
- SEO
- GEO
- Schema

### 15.13 Menu del frontend

El menu del header del frontend debe seguir la estructura de paginas que tenga el theme.

Pero su contenido debe ser multidioma.

Esto implica:

- labels del menu por idioma
- URLs por idioma
- slugs por idioma

### 15.14 Cambio de idioma en frontend

Se adopta como comportamiento recomendado oficial:

- cuando el usuario cambia de idioma, el sistema debe intentar conservar la pagina equivalente en el otro idioma

Regla:

- si existe equivalente traducido, se redirige a esa version
- si no existe traduccion completa, se muestra contenido en español con aviso de traduccion pendiente

Esto se considera mejor que redirigir siempre al inicio.

### 15.15 Contenido legal

Se establece que el contenido legal debe existir en ambos idiomas desde el inicio.

Esto incluye al menos:

- politica de privacidad
- terminos
- cookies
- aviso legal

### 15.16 SEO, GEO y Schema dentro del multiidioma

SEO, GEO y Schema deben ser multidioma de forma obligatoria.

Esto significa que cada idioma puede tener sus propios:

- titles
- descriptions
- keywords si se usan
- slugs
- datos GEO
- datos estructurados
- configuraciones avanzadas

### 15.17 SEO, GEO y Schema para paginas del theme

Las paginas del theme tambien deben soportar:

- SEO basico por idioma
- SEO avanzado por idioma
- GEO basico por idioma
- GEO avanzado por idioma
- Schema basico por idioma
- Schema avanzado por idioma

Esto no aplica solo a habitaciones o blog.

Tambien aplica a:

- inicio
- nosotros
- contacto
- cualquier pagina del theme con relevancia publica

### 15.18 Regla de permisos en contexto multidioma

La gestion multidioma no anula el sistema de permisos definido previamente.

Se mantiene la regla:

- `admin` puede trabajar la capa basica del contenido multidioma y sus capas basicas de SEO/GEO/Schema
- `super_admin` accede tambien a la capa avanzada de SEO/GEO/Schema multidioma
- otros permisos especiales podran ser otorgados por `super_admin`

### 15.19 Estado actual del modulo

#### Se mantiene

- la idea de URLs por idioma
- el uso de tablas por idioma
- la separacion entre idioma de interfaz y contenido traducido
- el selector de idioma en frontend

#### Se corrige conceptualmente

- el panel no sera multidioma completo, sino solo en español
- el frontend si debe ser multidioma real
- las paginas del theme tambien deben conectarse a contenido traducido en BD
- los slugs deben ser traducidos
- la edicion debe trabajar por pestañas de idioma
- debe existir fallback a español con aviso cuando falte traduccion
- deben existir alertas de traducciones faltantes en el panel
- SEO/GEO/Schema multidioma aplica tambien a paginas del theme, en nivel basico y avanzado

### 15.20 Decision final del modulo 4

El multiidioma queda oficialmente definido asi:

- admin monolingue en español
- frontend multidioma real
- contenido editable del frontend por idioma
- slugs traducidos por idioma
- fallback a español con aviso cuando falte traduccion
- alertas de traduccion incompleta en el panel
- pestañas por idioma en la edicion
- SEO, GEO y Schema multidioma desde el inicio
- SEO, GEO y Schema basico y avanzado tambien para paginas del theme

---

## 16. Modulo 5: SEO avanzado, GEO avanzado y Schema avanzado

### 16.1 Importancia del modulo

SEO, GEO y Schema no se consideran complementos opcionales del CMS.

Se establecen como uno de los pilares tecnicos y comerciales mas importantes del proyecto.

Este modulo existe desde el inicio y no se pospone para una etapa tardia.

Su funcion dentro del CMS es reemplazar y superar una parte importante de lo que normalmente se delegaria a plugins premium en WordPress.

### 16.2 Estado actual observado

La vision del modulo esta bien planteada en la documentacion general del proyecto.

Sin embargo, la implementacion real actual es todavia limitada.

Se detecta que:

- la base de datos ya contempla campos y tablas orientadas a SEO, GEO y Schema
- varias entidades ya reservan espacio para metadatos y `schema_json`
- pero la capa operativa avanzada todavia no esta consolidada de extremo a extremo

Esto significa que el modulo esta fuerte en intencion y diseno conceptual, pero todavia debe consolidarse como sistema funcional completo.

### 16.3 Alcance oficial del modulo

Se establece como criterio oficial que la capa SEO, GEO y Schema debe existir para todas las entidades publicas que aporten valor al posicionamiento, la conversion o la presencia internacional del hotel.

Esto incluye como minimo:

- paginas del theme
- habitaciones
- blog
- promociones
- paginas legales cuando aplique
- cualquier landing o contenido publico que se incorpore despues

No se limita solo al blog ni solo a habitaciones.

### 16.4 Arquitectura recomendada

Se adopta como criterio oficial una arquitectura mixta y controlada.

Esto significa:

- el sistema debe generar una base automatica coherente
- el usuario debe poder completar o ajustar la capa basica
- el `super_admin` debe poder intervenir la capa avanzada

Por tanto, no se adopta un modelo totalmente manual desde el inicio.

La recomendacion oficial es:

- generacion automatica de base SEO/GEO/Schema
- edicion basica guiada para usuarios autorizados
- override avanzado para `super_admin`

Esta estrategia ofrece mejor equilibrio entre:

- escalabilidad
- consistencia tecnica
- facilidad operativa
- potencia avanzada

### 16.5 SEO basico y avanzado

Se formaliza la separacion ya definida previamente:

- SEO basico
- SEO avanzado

#### SEO basico

La capa basica debe cubrir al menos:

- meta title
- meta description
- slug
- canonical simple cuando aplique
- imagen social principal
- index o noindex basico
- configuracion editorial por idioma

#### SEO avanzado

La capa avanzada debe cubrir al menos:

- estrategia por keyword principal y secundarias
- analisis semantico de contenido
- estructura de headings
- enlazado interno sugerido o gestionado
- canonical avanzado
- reglas robots avanzadas
- hreflang avanzado
- automatizaciones SEO
- scoring
- recomendaciones priorizadas
- reglas globales y por entidad

### 16.6 GEO basico y avanzado

Se confirma que GEO no debe reducirse a unas pocas coordenadas o campos aislados.

#### GEO basico

La capa basica debe cubrir:

- nombre del negocio
- direccion
- ciudad
- region
- pais
- telefono
- coordenadas
- enlace o referencia de ubicacion
- datos basicos por idioma cuando corresponda

#### GEO avanzado

La capa avanzada debe cubrir:

- enfoque geografico por entidad
- contexto geolocalizado por pagina, habitacion o contenido
- relaciones entre ubicacion, intencion de busqueda y contenido
- estrategia local por ciudad, zona, region o segmento geografico
- consistencia entre contenido, negocio, entidad y schema
- capacidad de enriquecer el contexto de posicionamiento local e internacional

### 16.7 Schema basico y avanzado

Se establece que el CMS debe manejar Schema de forma nativa y no como un agregado superficial.

#### Schema basico

Debe incluir al menos:

- schema autogenerado por entidad
- tipos comunes predefinidos por el sistema
- campos guiados para completar datos visibles
- validacion basica

#### Schema avanzado

Debe incluir al menos:

- override controlado del JSON-LD
- extensiones avanzadas por entidad
- relaciones entre entidades
- combinacion de schemas cuando aplique
- ajustes por idioma
- ajustes por pagina o contenido
- validaciones mas estrictas

### 16.8 Regla oficial de generacion y override

La recomendacion adoptada oficialmente para el proyecto es:

- generar automaticamente una base de SEO
- generar automaticamente una base de GEO
- generar automaticamente una base de Schema
- permitir edicion basica guiada
- permitir override avanzado solo a usuarios autorizados

Esto se considera superior a un enfoque completamente manual porque:

- reduce errores
- mejora consistencia
- acelera carga operativa
- facilita escalar el CMS a nuevos proyectos

### 16.9 Analisis avanzado

Se establece como criterio oficial que el analisis avanzado no debe limitarse solo al blog.

Debe existir, cuando aplique, tambien para:

- paginas del theme
- habitaciones
- blog
- promociones
- otros contenidos publicos relevantes

El criterio general sera:

- si una entidad tiene impacto SEO real, debe poder beneficiarse del analisis avanzado

### 16.10 Asociacion por entidad e idioma

Se establece como criterio oficial que SEO, GEO y Schema deben poder asociarse por:

- entidad
- idioma

Esto aplica al menos a:

- pagina
- habitacion
- entrada de blog
- promocion
- bloque o seccion especial cuando se requiera

Por tanto, la capa avanzada no sera solo global.

Tambien sera contextual y ligada a cada contenido relevante.

### 16.11 Panel dedicado y campos integrados

La recomendacion oficial adoptada es un modelo combinado.

Esto significa que el sistema debe tener:

- campos basicos integrados dentro de cada modulo o entidad
- y un panel especializado de SEO/GEO/Schema para la gestion avanzada, analitica y global

Esta es la mejor solucion para el proyecto porque:

- evita ocultar el SEO basico lejos del contenido
- permite trabajar el contenido normal sin salir del flujo editorial
- reserva la complejidad avanzada a un espacio tecnico mas potente

#### Regla operativa

En cada modulo o entidad publica debe existir al menos:

- bloque SEO basico
- bloque GEO basico cuando corresponda
- bloque Schema basico cuando corresponda

Adicionalmente, debe existir un modulo o panel especializado donde se gestione:

- analisis avanzado
- configuraciones globales
- reglas avanzadas
- validaciones avanzadas
- generacion enriquecida
- supervision de IA

### 16.12 IA dentro del modulo

Se establece como criterio oficial que la IA dentro de SEO, GEO y Schema no sera solo sugerente.

Tambien podra generar borradores completos.

Esto incluye:

- borradores completos de meta title
- borradores completos de meta description
- propuestas de slugs
- borradores GEO
- borradores de contexto local
- borradores o propuestas completas de Schema

#### Regla obligatoria

Aunque la IA pueda generar borradores completos:

- el resultado debe ser visible
- el resultado debe poder editarse
- el resultado no debe publicarse de forma ciega
- el control humano se mantiene obligatorio

### 16.13 Permisos

Se mantiene y se refuerza la separacion de niveles:

- `admin` accede a la capa basica de SEO, GEO y Schema
- `super_admin` accede a la capa avanzada de SEO, GEO y Schema
- el `super_admin` puede delegar permisos avanzados puntuales si se implementa esa capacidad

Esto aplica tambien a:

- paginas del theme
- habitaciones
- blog
- promociones
- contenido legal si corresponde

### 16.14 Implicacion para el theme

Las paginas del theme no quedan fuera de este sistema.

Cada pagina publica relevante del theme debe poder tener:

- SEO basico por idioma
- SEO avanzado por idioma
- GEO basico por idioma
- GEO avanzado por idioma
- Schema basico por idioma
- Schema avanzado por idioma

Esto incluye desde el inicio:

- home
- nosotros
- contacto
- listados
- landings
- cualquier pagina estrategica del hotel

### 16.15 Decision oficial del modulo 5

Queda definido oficialmente que:

- SEO, GEO y Schema son pilares del CMS desde el inicio
- deben existir para todas las entidades publicas relevantes
- deben funcionar por entidad y por idioma
- deben tener capa basica y capa avanzada
- el sistema debe generar una base automatica coherente
- debe existir edicion basica guiada
- debe existir override avanzado
- el analisis avanzado no se limita al blog
- debe existir un modelo combinado de campos integrados y panel especializado
- la IA puede asistir y tambien generar borradores completos
- el control humano sigue siendo obligatorio
- `super_admin` gobierna la capa avanzada

---

## 17. Modulo 6: Galeria

### 17.1 Importancia del modulo

La galeria no se considera un modulo secundario ni un simple cargador de imagenes para habitaciones.

Se define oficialmente como una biblioteca multimedia central del CMS.

Su funcion es servir como repositorio reutilizable para multiples modulos presentes y futuros del sistema.

### 17.2 Alcance oficial

La galeria debe estar preparada desde el inicio para crecer mas alla del uso exclusivo en habitaciones.

Esto incluye su uso en:

- habitaciones
- paginas del theme
- blog
- promociones
- ajustes del sitio
- imagenes sociales o institucionales
- futuros modulos que requieran activos multimedia

Por tanto, se adopta oficialmente una vision de biblioteca multimedia preparada para evolucionar.

### 17.3 Tipo de biblioteca

En esta etapa inicial, la implementacion operativa se enfoca en imagenes.

Sin embargo, arquitectonicamente se define como biblioteca multimedia extensible.

Esto significa que:

- hoy se priorizan imagenes
- manana puede crecer a otros tipos de activos si el proyecto lo requiere

Sin romper su concepto base.

### 17.4 Límite de peso oficial

Se adopta oficialmente el limite de `1MB` por archivo como politica del proyecto.

No se retorna al limite de `5MB` planteado en documentacion anterior.

Esta decision se considera correcta porque favorece:

- rendimiento
- SEO
- tiempos de carga
- uso en hosting realista
- mejor experiencia movil
- disciplina de optimizacion del contenido visual

### 17.5 Procesamiento y derivados

Se mantiene como criterio oficial que la galeria debe procesar imagenes y generar derivados optimizados.

Se conserva la logica de contar con al menos:

- original o respaldo fisico
- version WebP principal
- thumbnail
- tiny

Esto se considera correcto para:

- frontend
- listados
- selectores del panel
- reutilizacion en distintas interfaces

### 17.6 Metadatos obligatorios

Se establece como criterio oficial que las imagenes deben gestionarse con metadatos minimos obligatorios.

Como minimo:

- `alt_text`
- `tipo`
- `etiquetas`

De estos, `alt_text` queda definido como obligatorio.

Esto se adopta por:

- accesibilidad
- SEO
- coherencia editorial
- reutilizacion correcta del activo visual

### 17.7 Eliminacion segura

Se establece oficialmente que una imagen no debe poder eliminarse si esta vinculada a otras entidades activas del sistema, salvo que exista una accion explicita de reemplazo o desvinculacion.

Esto aplica al menos a:

- habitaciones
- paginas
- blog
- promociones
- ajustes
- cualquier otra entidad que la referencie

La eliminacion ciega queda descartada como comportamiento final del modulo.

### 17.8 Seguridad

La seguridad del modulo es obligatoria.

Por tanto, se adopta como criterio oficial que:

- CSRF debe estar activo en operaciones sensibles
- la subida debe validarse correctamente
- la eliminacion debe validarse correctamente
- no se debe dejar el modulo con protecciones comentadas como estado final

### 17.9 Permisos

La galeria debe trabajar con permisos por accion.

Se define oficialmente que el sistema debe contemplar, como minimo:

- ver
- subir
- editar metadatos
- eliminar

La disponibilidad de estas acciones dependera del sistema general de roles y permisos definido previamente.

### 17.10 Relacion con SEO, GEO y Schema

La galeria no solo cumple una funcion visual.

Tambien debe integrarse desde el inicio con la estrategia SEO, GEO y Schema del CMS.

Esto implica que la biblioteca multimedia debe poder servir activos para:

- imagenes destacadas de contenido
- imagenes sociales
- representacion visual de paginas
- imagenes de habitaciones
- elementos institucionales del hotel
- activos visuales relevantes para metadatos y datos estructurados

En consecuencia, la galeria tambien forma parte indirecta del motor SEO del sistema.

### 17.11 Estado actual del modulo

#### Se mantiene

- galeria centralizada
- procesamiento de imagenes con derivados
- selector reutilizable dentro de habitaciones
- uso de metadatos basicos
- orientacion a optimizacion de imagenes

#### Se corrige conceptualmente

- ya no se considera un modulo solo para habitaciones
- se oficializa como biblioteca multimedia base del CMS
- `1MB` queda como limite oficial
- `alt_text` pasa a ser obligatorio
- la eliminacion debe respetar referencias existentes
- la seguridad del modulo debe cerrarse correctamente
- se integra formalmente con la estrategia SEO/GEO/Schema desde el inicio

### 17.12 Decision final del modulo 6

La galeria queda oficialmente definida asi:

- biblioteca multimedia central del CMS
- inicialmente enfocada en imagenes, pero preparada para crecer
- reutilizable por multiples modulos
- optimizada desde el inicio
- con limite oficial de `1MB`
- con metadatos minimos obligatorios
- con eliminacion segura basada en referencias
- con permisos por accion
- y conectada desde el inicio con SEO, GEO y Schema

---

## 18. Modulo 7: Formularios dinamicos

### 18.1 Importancia del modulo

Los formularios dinamicos se consideran un modulo base del CMS.

No se limitan a un simple formulario de contacto aislado.

Cumplen una funcion operativa y comercial critica para el hotel, especialmente en:

- contacto
- reservas
- captacion de leads
- interaccion con paginas especificas del sitio

### 18.2 Formularios base oficiales de Fase 1

Se establece oficialmente que en Fase 1 deben existir como minimo estos dos formularios preestablecidos:

- formulario de contacto
- formulario de reserva

Adicionalmente, el sistema podra soportar formularios custom.

Pero los dos anteriores son obligatorios desde el inicio.

### 18.3 Modelo de datos oficial

Se adopta como recomendacion oficial y definitiva el uso de:

- `formularios`
- `formulario_campos`
- `formulario_respuestas`

No se adopta como modelo final el uso de `formulario_entregas`.

La razon es que `formulario_respuestas` representa mejor la necesidad real del sistema porque permite:

- almacenar envios
- marcar lectura
- marcar respuesta
- guardar respuesta administrativa
- guardar quien respondio
- guardar fecha de respuesta
- dar mejor trazabilidad operativa

Por tanto, `formulario_entregas` no se considera la fuente de verdad final del modulo.

### 18.4 Constructor dinamico

Se mantiene como criterio oficial que el CMS debe permitir construir formularios desde el panel.

Esto incluye:

- crear campos
- ordenar campos
- marcar requeridos
- definir tipos
- manejar opciones cuando aplique

El constructor se mantiene como parte importante del modulo.

### 18.5 Separacion funcional del modulo

Se establece que el modulo debe trabajar con una separacion clara entre:

- definicion del formulario
- estructura de campos
- envio publico
- gestion interna de respuestas

Esto evita mezclar en una misma capa:

- configuracion editorial
- operacion diaria
- seguimiento administrativo

### 18.6 Permisos

Se define oficialmente:

- `admin` puede crear y editar formularios
- `recepcionista` puede ver y gestionar respuestas
- `recepcionista` no debe editar la estructura del formulario

Adicionalmente, el sistema de permisos por accion podra afinar despues operaciones como:

- ver formularios
- crear formularios
- editar formularios
- eliminar formularios
- ver respuestas
- responder respuestas
- marcar leidas

### 18.7 Multiidioma

Los formularios publicos deben ser multidioma.

Esto incluye:

- labels
- placeholders
- mensajes visibles
- textos de validacion visibles al usuario
- mensajes de exito

El panel administrativo puede seguir operando en espanol, pero el frontend debe presentar el formulario segun el idioma activo del sitio.

### 18.8 Contexto automatico por entidad o pagina

Se adopta como criterio oficial que el formulario no debe pedir datos que el sistema ya conoce por contexto.

Esto es especialmente importante para:

- formulario de reserva
- formulario de contacto contextual

#### Reserva contextual

Si el formulario de reserva se renderiza dentro de la pagina de una habitacion concreta:

- no debe pedir manualmente el tipo o nombre de la habitacion
- el sistema debe asociar automaticamente la habitacion desde la pagina actual

Ejemplo conceptual:

- el usuario esta viendo `Habitacion Matrimonial`
- al enviar la reserva, el sistema ya guarda o transmite que la solicitud corresponde a esa habitacion

#### Contacto contextual

De la misma forma, si un formulario de contacto se envia desde una pagina especifica:

- el sistema debe registrar desde que pagina o seccion fue enviado

Esto permite que luego en correo, panel o auditoria se vea algo como:

- correo desde la pagina `Contacto`
- consulta enviada desde la pagina `Habitacion Matrimonial`

### 18.9 Campo visible vs contexto oculto

Se establece como recomendacion oficial que el contexto automatico no se trate como un campo visible normal del formulario.

Debe manejarse como metadata contextual del envio.

Esto significa que el sistema puede adjuntar internamente datos como:

- pagina_origen
- slug_origen
- tipo_entidad_origen
- entidad_origen_id
- nombre_entidad_origen
- idioma_origen

Sin obligar al usuario a rellenar informacion redundante.

### 18.10 Notificaciones y operacion

El modulo debe contemplar desde el inicio:

- persistencia de respuestas
- gestion administrativa de respuestas
- notificaciones por correo cuando aplique
- trazabilidad del origen del envio

Esto es especialmente importante para contacto y reservas.

### 18.11 Seguridad

Se mantiene como criterio oficial que este modulo debe incorporar:

- validacion del lado servidor
- sanitizacion fuerte
- CSRF cuando aplique
- rate limiting
- anti-spam
- reCAPTCHA o mecanismo equivalente
- logs de actividad relevante

Esto es obligatorio por tratarse de uno de los puntos mas expuestos del frontend publico.

### 18.12 IA en el modulo

Se acepta como criterio futuro que la IA pueda asistir en este modulo.

Esto puede incluir:

- sugerencias de labels
- sugerencias de mensajes de exito
- textos de ayuda
- estructura sugerida de formularios

La IA no se considera obligatoria para la operacion basica del modulo, pero si una capacidad valida de asistencia futura.

### 18.13 Estado actual del modulo

#### Se mantiene

- formularios base de contacto y reserva
- constructor dinamico de campos
- gestion desde panel
- idea de respuestas persistidas

#### Se corrige conceptualmente

- se oficializa `formulario_respuestas` como modelo correcto
- `formulario_entregas` no queda como modelo final
- la gestion de respuestas debe separarse bien de la definicion del formulario
- los formularios publicos deben ser multidioma
- el contexto de pagina o entidad debe viajar automaticamente
- el formulario de reserva no debe pedir el tipo de habitacion si ya esta dentro de una pagina de habitacion
- el formulario de contacto debe registrar desde que pagina fue enviado

### 18.14 Decision final del modulo 7

Los formularios dinamicos quedan oficialmente definidos asi:

- modulo base del CMS
- con formularios oficiales de contacto y reserva desde Fase 1
- con capacidad de formularios custom
- con constructor dinamico
- con `formulario_respuestas` como modelo oficial
- con gestion administrativa de respuestas
- con permisos diferenciados entre estructura y operacion
- con soporte multidioma en frontend
- con contexto automatico por pagina o entidad
- con integracion operativa para habitaciones y contacto

---

## 19. Modulo 8: Habitaciones

### 19.1 Importancia del modulo

El modulo de habitaciones se considera uno de los nucleos mas importantes del CMS y del producto hotelero.

No es un simple CRUD operativo.

Es una pieza central porque conecta:

- frontend publico
- conversion comercial
- galeria
- formularios
- multiidioma
- SEO
- GEO
- Schema
- IA asistiva

Por tanto, este modulo debe tratarse como uno de los motores principales del sistema.

### 19.2 Rol funcional de la habitacion

Se establece como criterio oficial que la habitacion debe entenderse principalmente como una entidad comercial publicable del hotel.

No se modela primero como un inventario tecnico de numero fisico, sino como una oferta visible para el usuario final.

### 19.3 Campo principal del modelo

Se confirma oficialmente que el eje principal del modulo es el nombre comercial de la habitacion.

Por tanto:

- el nombre comercial por idioma es obligatorio
- `numero_habitacion` no se considera obligatorio en el modelo final
- `identificador` o `codigo_interno` no se consideran el centro del dominio

Esto se adopta porque refleja mejor la realidad del producto y del frontend publico.

### 19.4 Multiidioma

Las habitaciones deben ser multidioma desde el inicio.

Esto aplica a:

- nombre comercial
- descripcion
- slugs
- FAQs
- comodidades
- amenities
- SEO
- GEO
- Schema

No se adopta una traduccion superficial o parcial.

### 19.5 Tipos de habitacion

Se mantiene oficialmente el uso de tipos de habitacion como entidad propia.

Esto se considera correcto porque facilita:

- organizacion interna
- agrupacion comercial
- herencia de logicas comunes
- administracion de tarifas

### 19.6 Modelo oficial de tarifas

Se adopta oficialmente un modelo de tarifas por tipo de habitacion.

No se adopta como modelo final la gestion primaria por habitacion individual.

Esto se considera mejor para el proyecto porque:

- reduce complejidad operativa
- encaja mejor con hoteles pequenos
- facilita administracion centralizada
- es coherente con temporadas globales del negocio

### 19.7 Estructura oficial de precios

Se establecen oficialmente cuatro capas de precio:

- temporada baja
- temporada regular
- temporada alta
- tarifa manual especial

La tarifa manual especial debe tener prioridad maxima sobre cualquier logica por fecha.

Si esta activa, debe imponerse aunque la fecha corresponda a otra temporada.

### 19.8 Automatizacion de temporadas

Se mantiene como criterio oficial que las tarifas deben poder resolverse automaticamente por periodos y fechas configuradas.

Esto implica que el sistema debe ser capaz de determinar la tarifa vigente segun:

- tipo de habitacion
- temporada activa por fecha
- override manual especial

### 19.9 Recomendacion oficial cuando no exista tarifa activa

Se adopta como recomendacion oficial que una habitacion no debe mostrar un precio falso o improvisado si no existe una tarifa valida.

Por tanto:

- no se debe usar `precio_base` como fallback visual final
- si no existe tarifa activa ni override manual, el sistema debe mostrar un estado equivalente a:
  - consultar precio
  - solicitar cotizacion
  - o mensaje comercial similar

Esto se considera mejor que inventar un precio o arrastrar un valor viejo.

### 19.10 Precio base

Se establece oficialmente que `precio_base` no forma parte del modelo funcional principal del modulo.

No debe gobernar el precio mostrado al usuario.

Si existe en estructuras antiguas o transicionales, se considera residual y no estrategico.

### 19.11 Contenido enriquecido por habitacion

Cada habitacion debe poder gestionar, como minimo:

- descripcion por idioma
- comodidades por idioma
- amenities por idioma
- configuracion de camas
- galeria de imagenes
- imagen principal
- FAQs por idioma

Este modulo no se considera completo sin esa capa enriquecida.

### 19.12 Galeria e imagenes

Se mantiene oficialmente la integracion con la galeria central del CMS.

Cada habitacion debe poder tener:

- una imagen principal
- multiples imagenes secundarias

Y estas deben venir de la biblioteca multimedia oficial del sistema.

### 19.13 Formulario de reserva contextual

Se establece oficialmente que la pagina de una habitacion debe integrar el formulario de reserva de manera contextual.

Esto significa que:

- el formulario no debe pedir manualmente que habitacion desea el usuario
- el sistema ya debe conocer la habitacion actual desde la pagina donde se envia

Por tanto, la habitacion se convierte en contexto automatico del envio.

### 19.14 Frontend publico obligatorio

Se confirma oficialmente que la pagina individual de habitacion es una pieza obligatoria del theme.

No se considera opcional ni secundaria.

Es obligatoria porque de ella dependen:

- la ficha comercial del producto
- el SEO/GEO/Schema por habitacion
- la galeria
- el formulario de reserva contextual
- la conversion del usuario final

### 19.15 SEO, GEO y Schema

Dado que habitaciones es uno de los modulos mas importantes del servicio hotelero, se establece oficialmente que debe contar desde el inicio con:

- SEO basico por idioma
- SEO avanzado por idioma
- GEO basico por idioma
- GEO avanzado por idioma
- Schema basico por idioma
- Schema avanzado por idioma

Esto no se considera opcional ni posterior.

Debe seguir todas las reglas globales ya establecidas para estos pilares.

### 19.16 IA en el modulo

Se establece oficialmente que el modulo de habitaciones debe poder apoyarse en IA de forma asistiva.

Esto incluye como minimo:

- apoyo en descripciones
- apoyo en traducciones o adaptaciones
- apoyo en FAQs
- apoyo en SEO
- apoyo en GEO
- apoyo en Schema

La IA puede asistir y generar borradores, pero el control humano sigue siendo obligatorio.

### 19.17 Estado actual del modulo

#### Se mantiene

- habitaciones como entidad rica y central
- tipos de habitacion
- integracion con galeria
- FAQs
- contenido multidioma
- formulario de reserva contextual
- orientacion publica/comercial del modulo

#### Se corrige conceptualmente

- el nombre comercial se oficializa como centro real del modulo
- `numero_habitacion` deja de ser obligatorio
- `identificador` no se toma como eje funcional final
- `precio_base` deja de ser el modelo de precio del negocio
- la logica tarifaria oficial pasa a ser por tipo de habitacion
- se oficializan temporadas baja, regular y alta
- se oficializa una tarifa manual especial con prioridad maxima
- el precio no debe inventarse si no existe tarifa valida
- comodidades y amenities deben contemplarse traducidas desde el inicio
- la pagina individual de habitacion queda como pieza obligatoria del frontend

### 19.18 Decision final del modulo 8

Las habitaciones quedan oficialmente definidas asi:

- modulo central del CMS hotelero
- entidad comercial publicable por idioma
- con nombre comercial como eje principal
- con tipos de habitacion como entidad propia
- con tarifas por tipo de habitacion
- con temporadas baja, regular y alta
- con tarifa manual especial de prioridad maxima
- sin dependencia funcional de `precio_base`
- con galeria integrada
- con FAQs, comodidades, amenities y camas
- con formulario de reserva contextual
- con pagina individual obligatoria en el theme
- con SEO, GEO y Schema basico y avanzado desde el inicio
- con asistencia por IA bajo control humano

---

## 20. Modulo 9: Paginas y edicion en vivo

### 20.1 Importancia del modulo

El modulo de paginas y edicion en vivo se considera uno de los pilares mas importantes del CMS porque es una de las piezas que debe reemplazar de forma mas clara a herramientas tipo constructor visual, campos dinamicos y parte del sistema de contenido multidioma.

No se trata solo de crear paginas.

Se trata de controlar la presentacion publica del hotel sin depender de WordPress ni de un page builder externo.

### 20.2 Naturaleza del modulo

Se establece oficialmente que este modulo no se construira como un constructor visual libre tipo drag-and-drop generalista.

No se busca replicar de forma exacta un page builder abierto como los del ecosistema WordPress.

En su lugar, se adopta un enfoque controlado y mas mantenible.

### 20.3 Modelo oficial adoptado

Se adopta oficialmente el siguiente modelo:

- el theme define la estructura visual
- el theme define layouts y secciones
- el CMS expone bloques editables dentro de esas paginas
- el contenido editable vive en base de datos
- la edicion en vivo actua sobre esos bloques

Por tanto, el sistema no edita archivos del theme.

Edita contenido asociado a claves o bloques definidos por la maquetacion.

### 20.4 Paginas base del theme

Se confirma oficialmente que el theme tendra paginas base estables, al menos:

- inicio
- nosotros
- contacto

Y cualquier otra pagina estructural que el proyecto o el cliente requiera.

Estas paginas existen como parte del theme, pero su contenido debe poder variar por cliente y por idioma.

### 20.5 Claves tecnicas por bloque

Se adopta como recomendacion oficial y criterio del proyecto que cada bloque editable tenga una clave tecnica estable en base de datos.

Esto se considera superior a guardar una pagina como un bloque gigante de HTML libre.

Ejemplos conceptuales:

- `home.hero.title`
- `home.hero.subtitle`
- `home.hero.cta_primary`
- `nosotros.historia.titulo`
- `contacto.formulario.intro`

Este enfoque permite:

- mantener orden tecnico
- versionar cambios
- trabajar por secciones
- soportar multiidioma real
- asociar SEO, GEO y Schema por pagina
- integrar IA con mas control

### 20.6 Edicion en vivo

La edicion en vivo se mantiene como una capacidad central del modulo.

La regla oficial es:

- el administrador visualiza la pagina completa
- desde esa vista puede editar lo necesario
- los cambios se aplican sobre bloques o secciones
- no se reemplaza la pagina entera como una sola masa de contenido

Esto permite una experiencia cercana a la edicion visual, pero sin perder control estructural.

### 20.7 Alcance del lock de edicion

Se establece como criterio oficial que el lock de edicion debe aplicarse a nivel de pagina completa.

La razon es que el administrador debe poder ver y editar la pagina completa desde una sola experiencia de trabajo.

No se adopta inicialmente un lock por bloque independiente.

Esto simplifica:

- coordinacion
- consistencia visual
- proteccion contra conflictos
- historial de cambios

### 20.8 Historial y versiones

Se mantiene oficialmente la necesidad de:

- historial de cambios
- registro de versiones
- trazabilidad del usuario que edita

La tabla de versiones definida en la documentacion se considera coherente con este objetivo.

### 20.9 Multiidioma

Las paginas del theme deben ser multidioma desde el inicio.

Esto aplica a:

- textos visibles
- botones
- bloques editables
- slugs
- contenido estructural editable
- mensajes publicos

La edicion debe permitir trabajar por idioma sin romper la estructura del theme.

### 20.10 SEO, GEO y Schema

Se establece oficialmente que las paginas del theme deben contar desde el inicio con:

- SEO basico por idioma
- SEO avanzado por idioma
- GEO basico por idioma
- GEO avanzado por idioma
- Schema basico por idioma
- Schema avanzado por idioma

Esto sigue exactamente las reglas globales ya definidas para estos pilares.

No se considera una capa opcional ni una mejora futura.

### 20.11 IA en el modulo

Se establece oficialmente que la IA tambien debe poder asistir este modulo.

Esto incluye como minimo:

- redaccion de bloques
- mejora de textos
- traduccion o adaptacion por idioma
- optimizacion SEO
- apoyo GEO
- apoyo Schema
- generacion de borradores para secciones del theme

La IA asiste, pero no reemplaza el control humano.

### 20.12 Diferencia respecto a un page builder libre

Se deja explicitamente asentado que este modulo no busca:

- arrastrar bloques arbitrarios sin control
- editar HTML completo de forma libre
- convertir cada pagina en un constructor caotico

Se busca, en cambio:

- diseÃ±o controlado por el equipo
- contenido editable por el cliente
- experiencia visual de edicion
- arquitectura mantenible

### 20.13 Estado actual del modulo

#### Se mantiene

- vision de paginas del theme como frontend principal
- necesidad de edicion en vivo
- necesidad de historial
- necesidad de lock
- paginas base del theme

#### Se corrige conceptualmente

- no se adopta un page builder libre estilo drag-and-drop general
- se oficializa el modelo de bloques editables por clave
- el contenido se guarda en base de datos, no en archivos del theme
- la edicion se hace viendo la pagina completa, pero guardando por seccion o bloque
- el lock de trabajo se define a nivel de pagina
- las paginas del theme deben integrar multiidioma, IA y SEO/GEO/Schema basico y avanzado desde el inicio

### 20.14 Decision final del modulo 9

Las paginas y la edicion en vivo quedan oficialmente definidas asi:

- paginas base controladas por el theme
- contenido editable almacenado en base de datos
- bloques editables con claves tecnicas estables
- edicion en vivo sobre la pagina completa
- guardado por bloque o seccion
- lock de edicion por pagina
- historial y trazabilidad de cambios
- soporte multidioma real
- SEO, GEO y Schema basico y avanzado por idioma
- asistencia por IA bajo control humano

---

## 21. Modulo 10: Blog

### 21.1 Importancia del modulo

El blog se considera un modulo editorial estrategico del CMS.

No se entiende solo como un espacio para publicar noticias.

Su funcion dentro del proyecto es apoyar:

- posicionamiento organico
- autoridad tematica del hotel
- captacion internacional
- contenido de apoyo comercial
- estrategia SEO
- estrategia GEO
- uso de Schema
- generacion asistida por IA

### 21.2 Multiidioma

Se establece oficialmente que el blog debe ser multidioma real desde el inicio.

Esto aplica a cada articulo y a sus componentes editoriales relevantes.

### 21.3 Estructura editorial minima del articulo

Se establece como criterio oficial que cada articulo debe poder manejar, por idioma:

- titulo
- slug
- contenido
- resumen o extracto
- SEO basico
- SEO avanzado
- GEO basico
- GEO avanzado
- Schema basico
- Schema avanzado

El resumen o extracto por idioma queda oficialmente aceptado como parte del modelo editorial recomendado, aunque deba reflejarse mejor en la implementacion futura.

### 21.4 Clasificacion del contenido

Se mantiene oficialmente el uso de:

- categorias
- tags

Ambos se consideran utiles y complementarios.

No se adopta un modelo donde solo exista uno de los dos.

### 21.5 Articulos relacionados

Se establece como criterio oficial que los articulos relacionados deben poder resolverse usando:

- categoria
- tags

No se limitara la relacion a una sola logica.

### 21.6 Estados editoriales

Se mantiene como criterio oficial el uso de estados editoriales para las entradas del blog.

Como minimo:

- borrador
- publicada
- archivada

Esto se considera importante para ordenar el flujo de trabajo del contenido.

### 21.7 Imagen destacada

Se mantiene oficialmente la idea de imagen destacada por articulo.

Esta debe integrarse con la biblioteca multimedia central del CMS.

No se considera un sistema de imagenes aislado del resto del proyecto.

### 21.8 SEO, GEO y Schema

Se confirma oficialmente que el blog debe regirse por las mismas reglas globales establecidas para estos pilares.

Por tanto, cada articulo debe poder contar con:

- SEO basico por idioma
- SEO avanzado por idioma
- GEO basico por idioma
- GEO avanzado por idioma
- Schema basico por idioma
- Schema avanzado por idioma

Esto no se considera opcional ni posterior.

### 21.9 IA en el modulo

Se establece oficialmente que la IA puede asistir intensamente en el modulo de blog.

Esto incluye:

- generacion de borradores completos de articulos
- sugerencias de titulos
- generacion de resumenes o extractos
- mejora de redaccion
- adaptacion o traduccion por idioma
- apoyo SEO
- apoyo GEO
- apoyo Schema

La IA puede generar borradores completos, pero el control humano sigue siendo obligatorio.

### 21.10 Permisos y flujo editorial

Se establece como criterio oficial que el rol `editor` debe poder crear contenido del blog.

Como recomendacion tecnica del proyecto:

- `editor` puede crear y trabajar articulos
- la publicacion final puede quedar regulada por permisos del sistema si despues se desea un flujo editorial mas estricto

Esto deja una base flexible sin bloquear la operacion de contenido.

### 21.11 Relacion con el frontend

El blog debe existir tambien como parte funcional del frontend publico del theme.

Esto incluye:

- listado de articulos
- detalle de articulo
- categorias
- tags
- articulos relacionados

No se considera suficiente un backend sin salida publica bien resuelta.

### 21.12 Estado actual del modulo

#### Se mantiene

- blog como modulo editorial propio
- categorias
- tags
- estados editoriales
- imagen destacada
- multiidioma
- orientacion fuerte a SEO

#### Se corrige conceptualmente

- el blog no se limita a SEO basico
- se integra formalmente a SEO/GEO/Schema basico y avanzado
- el resumen o extracto por idioma pasa a ser parte editorial recomendada
- la relacion entre articulos debe usar tanto categoria como tags
- la IA puede generar borradores completos
- `editor` debe poder crear contenido

### 21.13 Decision final del modulo 10

El blog queda oficialmente definido asi:

- modulo editorial estrategico del CMS
- multidioma real desde el inicio
- con categorias y tags
- con articulos relacionados por ambos criterios
- con imagen destacada desde la galeria central
- con resumen o extracto por idioma
- con SEO, GEO y Schema basico y avanzado por idioma
- con asistencia por IA para borradores y optimizacion
- con capacidad del `editor` para crear contenido
- y con presencia completa en frontend mediante listado y detalle

---

## 22. Modulo 11: Resenas y promociones

### 22.1 Naturaleza del modulo

Este modulo agrupa dos capacidades comerciales distintas pero complementarias:

- resenas
- promociones

Ambas son importantes para el producto hotelero porque una trabaja la confianza y la prueba social, mientras la otra impulsa la conversion y las campanas comerciales.

### 22.2 Resenas

#### 22.2.1 Modelo operativo

Se establece oficialmente que las resenas seran cargadas y gestionadas por el equipo administrativo.

No se adopta en esta etapa un sistema de envio publico libre de resenas.

Esto implica:

- alta manual
- moderacion interna
- control editorial
- control de visibilidad

#### 22.2.2 Tipos de resena

Se confirma oficialmente que las resenas pueden pertenecer a:

- hotel
- habitacion

Ambos casos se consideran validos y utiles para el proyecto.

#### 22.2.3 Datos base de resena

Se mantiene como criterio oficial que cada resena puede incluir:

- nombre del cliente
- pais
- puntuacion
- titulo
- contenido
- fecha de estancia
- visibilidad
- estado de moderacion

#### 22.2.4 Uso publico

Las resenas deben poder mostrarse en el frontend cuando corresponda, al menos en:

- home
- detalle de habitacion

Esto se considera importante para reforzar confianza y conversion.

#### 22.2.5 Schema de resenas y rating

Se establece oficialmente que las resenas deben integrarse desde el inicio con Schema orientado a valoraciones y estrellas.

Se adopta como criterio general:

- usar la representacion que mejor favorezca la coherencia SEO/GEO/Schema
- aprovechar rating agregado cuando corresponda
- usar estructura de review o aggregate rating segun la entidad y el caso

Por tanto, las resenas no se consideran solo decorativas.

Tambien forman parte del motor de datos estructurados del CMS.

### 22.3 Promociones

#### 22.3.1 Modelo operativo

Se establece oficialmente que las promociones son un modulo comercial nativo del CMS.

No se consideran un sistema complejo de ecommerce o cuponera avanzada.

Se orientan a campanas simples, controladas y efectivas.

#### 22.3.2 Alcance funcional

Cada promocion puede incluir al menos:

- codigo
- tipo de descuento
- valor del descuento
- fechas de vigencia
- imagen
- estado de publicacion
- contenido por idioma

#### 22.3.3 Alcance por entidad

Se confirma oficialmente que una promocion puede aplicarse:

- globalmente al hotel
- o a habitaciones especificas

Ambas modalidades se consideran correctas y coexistentes.

#### 22.3.4 Uso publico

Se mantiene como criterio oficial que las promociones deben poder mostrarse publicamente, por ejemplo:

- en modal
- en bloques del theme
- en paginas relevantes del frontend

La logica exacta de visualizacion podra definirse despues en el theme, pero la capacidad del modulo queda aprobada desde ahora.

### 22.4 Multiidioma

Se establece oficialmente que tanto resenas como promociones deben contemplar el contexto multidioma cuando aplique.

En especial:

- promociones deben ser multidioma real desde el inicio
- resenas pueden convivir en contexto multidioma del frontend, aun si su contenido editorial se maneja con control interno

### 22.5 SEO, GEO y Schema

Este modulo no queda fuera de la estrategia general del proyecto.

Se adopta oficialmente que:

- resenas deben integrarse con la capa de schema de valoraciones
- promociones, cuando tengan salida publica o landing visible, deben contemplar SEO/GEO/Schema segun las reglas generales del CMS

Esto se alinea con la estrategia ya definida de pilares globales.

### 22.6 IA en el modulo

Se establece oficialmente que la IA puede asistir este modulo.

Esto aplica especialmente a promociones, en tareas como:

- redaccion de titulos
- copies cortos
- textos promocionales
- versiones por idioma

Y en resenas puede asistir en:

- normalizacion editorial
- apoyo de presentacion o resumen interno

Siempre bajo control humano.

### 22.7 Permisos

Se mantiene la idea de gestion administrativa controlada.

Como criterio general:

- usuarios autorizados podran gestionar resenas y promociones
- la carga manual de resenas queda en manos del equipo interno
- la publicacion o visibilidad de promociones y resenas dependera del sistema de permisos general

### 22.8 Estado actual del modulo

#### Se mantiene

- resenas como prueba social del hotel y de habitaciones
- promociones como herramienta comercial nativa
- aplicacion de promociones a hotel completo o a habitaciones especificas
- orientacion publica de ambos submodulos

#### Se corrige conceptualmente

- resenas quedan definidas como gestionadas internamente y no como sistema abierto al publico
- schema de estrellas y rating se vuelve obligatorio desde el inicio para resenas
- promociones se integran al enfoque multidioma
- promociones pueden apoyarse en IA para su copy comercial
- ambos submodulos se alinean a la estrategia general de SEO/GEO/Schema cuando corresponda

### 22.9 Decision final del modulo 11

Resenas y promociones quedan oficialmente definidas asi:

- resenas gestionadas manualmente por el equipo
- resenas de hotel y de habitacion
- resenas integradas con schema de ratings y estrellas
- promociones multidioma desde el inicio
- promociones aplicables al hotel o a habitaciones especificas
- promocion publica mediante modal o bloques del theme
- apoyo de IA para copy promocional
- integracion con SEO, GEO y Schema segun el contexto publico de cada pieza

---

## 23. Modulo 12: Seguridad

### 23.1 Naturaleza del modulo

La seguridad no se considera una capa opcional ni una fase final separada del desarrollo.

Se establece oficialmente como una capacidad transversal del CMS.

Esto significa que debe gobernar todos los modulos y no quedar relegada a un ajuste posterior.

### 23.2 Regla general

Se adopta oficialmente que la seguridad debe aplicarse a:

- backend
- frontend publico
- autenticacion
- sesiones
- formularios
- uploads
- contenido editable
- APIs
- permisos
- logs y auditoria

### 23.3 CSRF

Se establece oficialmente que ningun endpoint de escritura debe quedar operativo sin proteccion CSRF, salvo casos publicos muy justificados y cubiertos por otras protecciones equivalentes.

Esto aplica especialmente a:

- crear
- actualizar
- eliminar
- subir archivos
- acciones administrativas sensibles

No se acepta como estado final dejar protecciones comentadas o desactivadas.

### 23.4 Formularios publicos y anti-spam

Se establece oficialmente que los formularios publicos deben integrar desde el inicio:

- rate limiting
- captcha o mecanismo anti-spam equivalente
- logging de intentos relevantes

Esto se considera obligatorio por ser uno de los puntos mas expuestos del sistema.

### 23.5 Politica de contrasenas

Se establece oficialmente que el sistema debe aplicar una politica minima de contrasenas para usuarios nuevos.

Como criterio base:

- longitud minima
- validacion suficiente para evitar claves debiles

La complejidad exacta puede afinarse despues, pero la existencia de una politica minima queda aprobada desde ahora.

### 23.6 Auditoria y visibilidad

Se confirma oficialmente que el sistema debe tener auditoria visible para `super_admin`.

Esto implica la necesidad de una vista o modulo que permita consultar:

- actividad de usuarios
- eventos relevantes
- cambios administrativos
- eventos de seguridad cuando aplique

La seguridad no solo debe ocurrir en silencio.

Tambien debe dejar trazabilidad.

### 23.7 Control administrativo de sesiones y usuarios

Se establece oficialmente que el `super_admin` debe poder:

- invalidar sesiones
- bloquear usuarios manualmente

Esto se considera una capacidad importante de control operativo y respuesta ante incidentes.

### 23.8 Capas de seguridad esperadas

Se mantiene como base oficial el uso de medidas como:

- contrasenas hasheadas
- regeneracion de sesion
- timeout de sesion
- validacion de inputs
- escape de outputs
- proteccion de uploads
- headers de seguridad
- HTTPS en produccion
- logs separados

Estas capas se consideran correctas como base del proyecto.

### 23.9 Contenido enriquecido y sanitizacion

Se deja asentado como criterio oficial que los modulos que acepten contenido enriquecido deben tener una sanitizacion adecuada y consistente.

No basta con confiar de forma ciega en contenido HTML libre.

Esto aplica especialmente a:

- paginas
- blog
- descripciones de habitaciones
- cualquier campo con HTML enriquecido

### 23.10 Seguridad en uploads

Se mantiene oficialmente la necesidad de proteger uploads mediante:

- validacion de tipo real
- validacion de peso
- bloqueo de ejecucion de scripts
- control de rutas y nombres
- limpieza o rechazo de entradas sospechosas

### 23.11 Seguridad y permisos

Se confirma que la seguridad del CMS no depende solo de login.

Tambien depende del sistema de roles y permisos ya definido.

Por tanto, las acciones sensibles deben quedar protegidas por:

- autenticacion
- autorizacion
- validacion del request
- trazabilidad

### 23.12 Estado actual del modulo

#### Se mantiene

- base de autenticacion
- hash de contrasenas
- regeneracion de sesion
- timeout de sesion
- CSRF como mecanismo oficial
- validacion y escape centralizados
- proteccion basica de uploads
- headers de seguridad
- logs del sistema

#### Se corrige conceptualmente

- la seguridad se declara transversal y no final
- los endpoints de escritura no pueden operar sin CSRF activo
- formularios publicos deben tener anti-spam y rate limiting obligatorio
- se oficializa politica minima de contrasenas
- `super_admin` debe tener vista de auditoria y control de sesiones/usuarios
- el contenido enriquecido requiere sanitizacion consistente

### 23.13 Decision final del modulo 12

La seguridad queda oficialmente definida asi:

- capacidad transversal de todo el CMS
- obligatoria en cada modulo y endpoint
- con CSRF activo en escrituras
- con anti-spam y rate limiting en formularios publicos
- con politica minima de contrasenas
- con auditoria visible para `super_admin`
- con capacidad de invalidar sesiones y bloquear usuarios
- con sanitizacion y escape consistentes
- con uploads protegidos
- con HTTPS y headers seguros en produccion

---

## 24. Modulo 13: Performance y optimizacion

### 24.1 Naturaleza del modulo

Performance y optimizacion se consideran capacidades transversales del CMS.

No se tratan como un ajuste final menor.

Se establece oficialmente que el sistema debe construirse pensando desde el inicio en:

- velocidad de carga
- hosting realista
- consumo controlado de recursos
- buena experiencia movil
- SEO tecnico

### 24.2 Regla general

Se adopta como criterio oficial que el CMS debe priorizar una arquitectura liviana y eficiente.

Esto es coherente con:

- PHP puro
- MySQL
- JavaScript vanilla
- sin frameworks pesados innecesarios

### 24.3 Produccion y assets

Se adopta oficialmente la recomendacion de no usar en produccion dependencias runtime innecesarias para estilos del frontend.

Por tanto:

- no se considera adecuado depender de Tailwind por CDN o runtime en produccion
- el frontend debe servir CSS propio del theme desde el servidor

Esto se considera mejor para:

- estabilidad
- rendimiento
- control
- cacheo

### 24.4 Fuentes

Se adopta oficialmente la recomendacion de alojar las fuentes en el propio servidor cuando se decida su uso final en produccion.

Esto significa que, si una fuente externa se vuelve parte del theme:

- debe descargarse
- debe servirse localmente

La razon es evitar dependencias criticas externas que ralenticen el render inicial.

### 24.5 Imagenes

Se mantiene oficialmente la politica de optimizacion de imagenes del proyecto:

- peso maximo de `1MB`
- generacion de WebP
- generacion de derivados utiles
- uso de imagenes optimizadas para listados y selectores

Esto se considera una de las decisiones correctas ya consolidadas del CMS.

### 24.6 Robots y sitemap

Se establece oficialmente que `robots.txt` y `sitemap.xml` son piezas obligatorias desde el inicio real del frontend.

Adicionalmente, se considera recomendable que:

- puedan gestionarse o ajustarse manualmente
- la IA pueda asistir en su contenido o sugerencias cuando aplique

La recomendacion tecnica general es:

- mantener una base automatica consistente
- permitir ajustes controlados manuales

### 24.7 Cache

Se adopta como recomendacion oficial contemplar cacheo publico simple desde una etapa temprana del proyecto, siempre que se aplique con control.

Esto puede beneficiar especialmente a:

- home
- listados publicos
- paginas muy visitadas
- contenido de lectura frecuente

No se propone una capa compleja de cache empresarial desde el inicio.

La recomendacion es un enfoque simple, seguro y compatible con hosting realista.

### 24.8 Assets del proyecto

Se mantiene oficialmente la separacion total entre:

- CSS del backend
- CSS del frontend

Y se establece ademas como criterio que:

- los assets del frontend deben consolidarse como archivos reales del theme
- no deben quedarse eternamente en estado vacio o dependientes de soluciones de prototipo

### 24.9 Consultas y contenido publico

Se establece como criterio oficial que los contenidos publicos mas visitados deben resolverse con consultas razonables y eficientes.

Esto aplica especialmente a:

- home
- habitaciones
- detalle de habitacion
- blog
- paginas del theme

La optimizacion de consultas se considera parte del rendimiento general del sistema.

### 24.10 IA en performance y optimizacion

Se acepta oficialmente que la IA pueda asistir desde el inicio en tareas relacionadas con optimizacion editorial y tecnica no critica.

Esto puede incluir:

- sugerencias de optimizacion de contenido
- sugerencias sobre imagenes o peso editorial
- apoyo en robots y sitemap
- ayuda en consistencia de activos publicos

La IA no gobierna el runtime, pero si puede apoyar decisiones y contenido relacionado.

### 24.11 IA como capa desacoplada

Se establece oficialmente que la integracion de IA debe implementarse desde el inicio mediante una capa desacoplada o proveedor abstracto.

La razon es que el proyecto debe poder:

- comenzar con un proveedor gratuito o de bajo costo
- cambiar de proveedor sin romper la arquitectura del CMS
- activar o desactivar el proveedor segun costo o disponibilidad

Esto implica que no se debe amarrar todo el sistema a una sola API concreta.

### 24.12 Decision practica de proveedor IA

Como criterio actual del proyecto:

- la IA debe existir desde el inicio
- pero debe resolverse de forma costo-eficiente

Se considera recomendable comenzar con un proveedor que ofrezca una capa gratuita o muy barata para desarrollo y operacion inicial, manteniendo al mismo tiempo una arquitectura compatible con migracion futura.

### 24.13 Estado actual del modulo

#### Se mantiene

- orientacion a stack liviano
- politica de imagenes optimizadas
- cache de navegador via servidor
- enfoque general de rendimiento como ventaja del proyecto

#### Se corrige conceptualmente

- produccion no debe depender de Tailwind runtime por CDN
- las fuentes del frontend deben poder servirse localmente
- robots y sitemap pasan a ser piezas obligatorias
- se recomienda cache publico simple desde etapa temprana
- performance se oficializa como capacidad transversal
- la IA para el proyecto se implementara desde el inicio con capa desacoplada de proveedor

### 24.14 Decision final del modulo 13

Performance y optimizacion quedan oficialmente definidos asi:

- capacidad transversal del CMS
- frontend con assets propios servidos localmente
- sin dependencia de runtime CSS externo en produccion
- fuentes alojadas localmente cuando se usen
- imagenes optimizadas con politica oficial de `1MB`
- robots y sitemap obligatorios
- cache publico simple recomendado desde etapa temprana
- optimizacion de consultas para contenido publico
- asistencia por IA desde el inicio en tareas relacionadas
- integracion de IA desacoplada del proveedor especifico

---

## 12. Naturaleza del documento

Este archivo es un documento vivo.

Puede y debe ampliarse a medida que avancemos.

No se considera cerrado hasta completar la revision total del proyecto.
