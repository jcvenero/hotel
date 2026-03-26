<?php
/**
 * Página de Nosotros - Tema Villas del Sol
 */
require_once THEME_PATH . '/layout/header.php';
?>

<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto text-center mb-20">
            <h1 class="text-stone-400 text-xs font-bold uppercase tracking-[0.4em] mb-4"><?= $lang === 'es' ? 'Nuestra Historia' : 'Our Story' ?></h1>
            <h2 class="text-5xl md:text-6xl font-playfair font-bold text-stone-900 italic mb-8"><?= $lang === 'es' ? 'Tradición y Calidez en Cusco' : 'Tradition and Warmth in Cusco' ?></h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center mb-32">
            <div class="order-2 md:order-1">
                <div class="aspect-square bg-stone-100 rounded-[3rem] overflow-hidden shadow-2xl rotate-2">
                    <img src="/hotel/public/uploads/webp/logo-71b85.webp" class="w-full h-full object-contain p-20 grayscale" alt="History">
                </div>
            </div>
            <div class="order-1 md:order-2 space-y-6 text-stone-600 leading-loose text-lg">
                <p>
                    <?= $lang === 'es' ? 'Desde el año 2005, Posada Villa Mayor ha sido un refugio de paz en el epicentro del Centro Histórico del Cusco. Nuestra casona, rescatada de los cimientos coloniales, conserva la piedra original que una vez formó parte de la gran capital Inka.' : 'Since 2005, Posada Villa Mayor has been a sanctuary of peace in the epicenter of Cusco\'s Historic Center. Our mansion, rescued from colonial foundations, preserves the original stone that once formed part of the great Inca capital.' ?>
                </p>
                <p>
                    <?= $lang === 'es' ? 'Nuestra misión es brindar una experiencia de alojamiento auténtica, donde el lujo no se mide por la opulencia, sino por la atención al detalle, la tranquilidad de nuestros patios y el respeto por nuestra herencia cultural.' : 'Our mission is to provide an authentic lodging experience, where luxury is not measured by opulence, but by attention to detail, the tranquility of our courtyards, and respect for our cultural heritage.' ?>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
            <div class="p-10 bg-stone-50 rounded-3xl border border-stone-100">
                <i class="fa-solid fa-hotel text-3xl text-amber-800 mb-6"></i>
                <h4 class="font-playfair font-bold text-xl mb-4 italic"><?= $lang === 'es' ? 'Arquitectura Única' : 'Unique Architecture' ?></h4>
                <p class="text-sm text-stone-500"><?= $lang === 'es' ? 'Preservamos techos de teja y vigas de madera original.' : 'We preserve tile roofs and original wooden beams.' ?></p>
            </div>
            <div class="p-10 bg-stone-50 rounded-3xl border border-stone-100">
                <i class="fa-solid fa-heart text-3xl text-amber-800 mb-6"></i>
                <h4 class="font-playfair font-bold text-xl mb-4 italic"><?= $lang === 'es' ? 'Hospitalidad Andina' : 'Andean Hospitality' ?></h4>
                <p class="text-sm text-stone-500"><?= $lang === 'es' ? 'Servicio personalizado que le hará sentir como en casa.' : 'Personalized service that will make you feel at home.' ?></p>
            </div>
            <div class="p-10 bg-stone-50 rounded-3xl border border-stone-100">
                <i class="fa-solid fa-map-location-dot text-3xl text-amber-800 mb-6"></i>
                <h4 class="font-playfair font-bold text-xl mb-4 italic"><?= $lang === 'es' ? 'Ubicación Premium' : 'Premium Location' ?></h4>
                <p class="text-sm text-stone-500"><?= $lang === 'es' ? 'A solo metros de la Plaza de Armas del Cusco.' : 'Just meters away from Cusco\'s Main Square.' ?></p>
            </div>
        </div>
    </div>
</section>

<?php require_once THEME_PATH . '/layout/footer.php'; ?>
