<?php
/**
 * Footer Global del Tema Villas del Sol
 */
$hotelName = Language::translateJsonField($ajustes['hotel_name'] ?? 'HotelCore');
$address = Language::translateJsonField($ajustes['address'] ?? '');
$phone = $ajustes['contact_phone'] ?? '';
$email = $ajustes['contact_email'] ?? '';
$socialRaw = $ajustes['social_networks'] ?? '[]';
$socials = json_decode($socialRaw, true) ?: [];
$platformsRaw = $ajustes['hotel_platforms'] ?? '[]';
$platforms = json_decode($platformsRaw, true) ?: [];
?>
    
    <footer class="bg-stone-900 text-stone-300 pt-16 pb-8">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
            
            <!-- Brand Column -->
            <div class="col-span-1 md:col-span-1">
                <h3 class="text-white font-playfair text-2xl font-bold mb-6 italic underline decoration-amber-600/50"><?= $hotelName ?></h3>
                <p class="text-sm leading-relaxed mb-6 opacity-80">
                    <?= Language::get($ajustes, 'hotel_description') ?>
                </p>
                <div class="flex gap-4">
                    <?php foreach($socials as $s): ?>
                        <a href="<?= $s['url'] ?>" target="_blank" class="w-10 h-10 rounded-full border border-stone-700 flex items-center justify-center hover:bg-white hover:text-black transition-all">
                            <i class="<?= $s['icon'] ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Links Column -->
            <div>
                <h4 class="text-white font-bold uppercase tracking-widest text-xs mb-8">Navegación</h4>
                <ul class="space-y-4 text-sm font-medium">
                    <li><a href="/hotel/" class="hover:text-gold transition-colors">Inicio</a></li>
                    <li><a href="/hotel/nosotros" class="hover:text-gold transition-colors">Nosotros</a></li>
                    <li><a href="/hotel/habitaciones" class="hover:text-gold transition-colors">Habitaciones</a></li>
                    <li><a href="/hotel/contacto" class="hover:text-gold transition-colors">Contacto</a></li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div>
                <h4 class="text-white font-bold uppercase tracking-widest text-xs mb-8">Ubicación & Contacto</h4>
                <div class="space-y-4 text-sm opacity-90">
                    <p class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot text-amber-600 mt-1"></i>
                        <span><?= $address ?></span>
                    </p>
                    <p class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-amber-600"></i>
                        <span><?= $phone ?></span>
                    </p>
                    <p class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-amber-600"></i>
                        <span><?= $email ?></span>
                    </p>
                </div>
            </div>

            <!-- Booking Platforms -->
            <div>
                <h4 class="text-white font-bold uppercase tracking-widest text-xs mb-8">Reservar en</h4>
                <ul class="space-y-4 text-sm">
                    <?php foreach($platforms as $p): ?>
                    <li>
                        <a href="<?= $p['url'] ?>" target="_blank" class="flex items-center gap-2 hover:text-amber-500 transition-colors">
                            <i class="<?= $p['icon'] ?> text-amber-600"></i>
                            <span><?= $p['name'] ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="container mx-auto px-6 mt-16 pt-8 border-t border-stone-800 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-bold uppercase tracking-[0.2em] opacity-50">
            <p>&copy; <?= date('Y') ?> <?= $hotelName ?>. Todos los derechos reservados.</p>
            <p>Diseño & Programación: <span class="text-white">Juan Carlos Venero Chacon</span></p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= THEME_URL ?>/assets/js/theme.js"></script>
    <script>
        // Configuración básica de Tailwind (en modo CDN para rapidez en esta demo)
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        playfair: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
</body>
</html>
