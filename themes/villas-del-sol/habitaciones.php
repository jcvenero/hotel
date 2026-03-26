<?php
/**
 * Página de Catálogo de Habitaciones - Tema Villas del Sol
 */
require_once THEME_PATH . '/layout/header.php';

// Obtener todas las habitaciones activas
$stmt = $db->query("SELECT h.*, hi.nombre as nombre_hab, hi.descripcion, i.ruta_webp, i.ruta_thumbnail 
                    FROM habitaciones h 
                    JOIN habitaciones_idiomas hi ON h.id = hi.habitacion_id AND hi.idioma = '$lang'
                    LEFT JOIN habitacion_imagenes himg ON h.id = himg.habitacion_id AND himg.es_principal = 1
                    LEFT JOIN imagenes i ON himg.imagen_id = i.id
                    WHERE h.activa = 1 ORDER BY h.id ASC");
$habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="py-24 bg-stone-50">
    <div class="container mx-auto px-6">
        
        <div class="max-w-3xl mb-20">
            <h1 class="text-stone-400 text-xs font-bold uppercase tracking-[0.4em] mb-4">Nuestro Refugio</h1>
            <h2 class="text-5xl md:text-6xl font-playfair font-bold text-stone-900 italic mb-8">Dormir entre muros de historia</h2>
            <p class="text-stone-600 text-lg leading-loose">
                <?= $lang === 'es' ? 'Cada una de nuestras habitaciones ha sido diseñada para preservar la mística colonial integrando el confort moderno y una decoración que rinde homenaje a la cultura cusqueña.' : 'Each of our rooms has been designed to preserve the colonial mystique by integrating modern comfort and decor that pays tribute to Cusco culture.' ?>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-16">
            <?php foreach($habitaciones as $index => $h): ?>
            <div class="group flex flex-col md:flex-row gap-8 bg-white p-6 rounded-[2rem] shadow-sm hover:shadow-xl transition-all duration-500 border border-stone-200/50">
                <div class="w-full md:w-2/5 h-64 md:h-auto overflow-hidden rounded-2xl relative">
                    <img src="<?= $h['ruta_webp'] ?: '/hotel/public/uploads/webp/hab-simple-60c7b.webp' ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="<?= $h['nombre_hab'] ?>">
                    <div class="absolute inset-0 bg-stone-900/10 active:bg-transparent"></div>
                </div>
                <div class="w-full md:w-3/5 flex flex-col justify-between py-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2 text-amber-800 text-[10px] font-bold uppercase tracking-widest">
                            <i class="fa-solid fa-star text-[8px]"></i>
                            <span>Posada Villa Mayor</span>
                        </div>
                        <h3 class="text-3xl font-playfair font-bold text-stone-900 mb-4 italic"><?= $h['nombre_hab'] ?></h3>
                        <p class="text-stone-500 text-sm leading-relaxed line-clamp-4">
                            <?= $h['descripcion'] ?>
                        </p>
                    </div>
                    
                    <div class="mt-8 flex items-center justify-between">
                        <div class="flex items-center gap-4 text-stone-400">
                            <?php if($h['capacidad_huespedes'] > 0): ?>
                            <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tighter">
                                <i class="fa-solid fa-user-group text-[12px]"></i> <?= $h['capacidad_huespedes'] ?>
                            </span>
                            <?php endif; ?>
                            <?php if($h['num_camas'] > 0): ?>
                            <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-tighter">
                                <i class="fa-solid fa-bed text-[12px]"></i> <?= $h['num_camas'] ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <a href="/hotel/habitacion/<?= $h['id'] ?>" class="bg-stone-900 text-white px-6 py-2.5 rounded-full text-[10px] font-bold uppercase tracking-widest hover:bg-amber-800 transition-all">
                            <?= $lang === 'es' ? 'Ver Detalles' : 'Details' ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-32 bg-amber-900 text-center relative overflow-hidden">
    <!-- Decorative element -->
    <div class="absolute -top-20 -left-20 w-80 h-80 rounded-full bg-amber-800/20 blur-3xl"></div>
    <div class="absolute -bottom-20 -right-20 w-80 h-80 rounded-full bg-stone-900/30 blur-3xl"></div>
    
    <div class="container mx-auto px-6 relative z-10">
        <h3 class="text-white font-playfair text-4xl md:text-5xl italic mb-10"><?= $lang === 'es' ? '¿Buscas una experiencia personalizada?' : 'Looking for a personalized experience?' ?></h3>
        <p class="text-amber-100/70 max-w-xl mx-auto mb-12 text-lg">
            <?= $lang === 'es' ? 'Contáctanos directamente para grupos, lunas de miel o solicitudes especiales.' : 'Contact us directly for groups, honeymoons, or special requests.' ?>
        </p>
        <a href="/hotel/contacto" class="inline-block bg-white text-stone-900 px-12 py-4 rounded-full font-bold uppercase tracking-widest text-xs hover:bg-stone-100 transition-all shadow-xl">
            <?= $lang === 'es' ? 'Contactar con Recepción' : 'Contact Front Desk' ?>
        </a>
    </div>
</section>

<?php require_once THEME_PATH . '/layout/footer.php'; ?>
