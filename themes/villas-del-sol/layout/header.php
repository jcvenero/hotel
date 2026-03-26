<?php
/**
 * Header Global del Tema Villas del Sol
 */
$hotelName = Language::translateJsonField($ajustes['hotel_name'] ?? 'HotelCore');
$seoTitle = Language::translateJsonField($ajustes['seo_title'] ?? '');
$seoDesc = Language::translateJsonField($ajustes['seo_description'] ?? '');
$seoKw = Language::translateJsonField($ajustes['seo_keywords'] ?? '');
$logo = $ajustes['logo_main'] ?? '';
$lang = Language::current();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $seoTitle ?: $hotelName . ' | Cusco' ?></title>
    <meta name="description" content="<?= $seoDesc ?>">
    <meta name="keywords" content="<?= $seoKw ?>">
    
    <!-- Favicon -->
    <?php if(!empty($ajustes['logo_favicon'])): ?>
    <link rel="icon" href="<?= $ajustes['logo_favicon'] ?>" type="image/x-icon">
    <?php endif; ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?= THEME_URL ?>/assets/css/main.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-stone-50 text-stone-900 font-outfit">

    <!-- Header / Navigation -->
    <header class="fixed w-full top-0 left-0 z-50 transition-all duration-500" 
            x-data="{ atTop: true }" 
            @scroll.window="atTop = (window.pageYOffset > 50 ? false : true)"
            :style="atTop ? 'background-color: transparent !important; box-shadow: none !important; padding-top: 1.5rem; padding-bottom: 1.5rem;' : 'background-color: #ffffff !important; box-shadow: 0 10px 15px rgba(0,0,0,0.1) !important; padding-top: 0.75rem; padding-bottom: 0.75rem;'"
            :class="atTop ? 'border-b border-white/10' : 'border-b border-stone-100'"
            class="transition-all duration-500">
        
        <div class="container mx-auto px-6 flex justify-between items-center">
            
            <!-- Logo & Name -->
            <a href="/hotel/" class="flex items-center gap-4">
                <?php if($logo): ?>
                    <img src="<?= $logo ?>" alt="<?= $hotelName ?>" 
                         class="h-10 w-auto object-contain transition-all duration-300">
                <?php endif; ?>
                <span class="text-xl font-playfair font-bold tracking-tight transition-colors duration-300"
                      :class="atTop ? 'text-white' : 'text-stone-900'">
                    <?= $hotelName ?>
                </span>
            </a>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center gap-0 font-bold text-[14px] uppercase tracking-[0.2em]">
                <a href="/hotel/" class="px-6 py-2 transition-colors border-r" :class="atTop ? 'text-white border-white/10 hover:text-gold' : 'text-stone-700 border-stone-100 hover:text-gold'">Inicio</a>
                <a href="/hotel/nosotros" class="px-6 py-2 transition-colors border-r" :class="atTop ? 'text-white border-white/10 hover:text-gold' : 'text-stone-700 border-stone-100 hover:text-gold'">Nosotros</a>
                <a href="/hotel/habitaciones" class="px-6 py-2 transition-colors border-r" :class="atTop ? 'text-white border-white/10 hover:text-gold' : 'text-stone-700 border-stone-100 hover:text-gold'">Habitaciones</a>
                <a href="/hotel/contacto" class="px-6 py-2 transition-colors border-r" :class="atTop ? 'text-white border-white/10 hover:text-gold' : 'text-stone-700 border-stone-100 hover:text-gold'">Contacto</a>
                
                <!-- Simple Lang Switcher -->
                <div class="flex items-center gap-4 ml-6 pl-2" :class="atTop ? 'text-white' : 'text-stone-700'">
                    <a href="?lang=es" :class="atTop ? (Language::current() === 'es' ? 'text-white border-b-2 border-gold shadow-gold/50 shadow-sm' : 'text-white/50') : (Language::current() === 'es' ? 'text-gold font-bold' : 'text-stone-400')" class="hover:text-gold transition-all leading-none pb-1">ES</a>
                    <span class="opacity-20">|</span>
                    <a href="?lang=en" :class="atTop ? (Language::current() === 'en' ? 'text-white border-b-2 border-gold shadow-gold/50 shadow-sm' : 'text-white/50') : (Language::current() === 'en' ? 'text-gold font-bold' : 'text-stone-400')" class="hover:text-gold transition-all leading-none pb-1">EN</a>
                </div>
            </nav>

            <!-- Reservation Button -->
            <div class="hidden md:block">
                <a href="/hotel/habitaciones" class="bg-gold text-white px-8 py-3 rounded-full text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-white hover:text-gold border border-transparent hover:border-gold transition-all shadow-xl active:scale-95">
                    Book Now
                </a>
            </div>

            <!-- Mobile Toggle -->
            <div class="md:hidden" x-data="{ mobileMenu: false }">
                <button @click="mobileMenu = !mobileMenu" :class="atTop ? 'text-white' : 'text-stone-900'" class="text-2xl">
                    <i class="fa-solid fa-bars-staggered" x-show="!mobileMenu"></i>
                    <i class="fa-solid fa-xmark" x-show="mobileMenu"></i>
                </button>
                
                <!-- Mobile Menu Overlay -->
                <div x-show="mobileMenu" x-transition class="fixed inset-0 top-0 left-0 w-full h-screen bg-stone-900 z-50 p-12 flex flex-col justify-center gap-8 text-3xl font-playfair text-white text-center">
                    <button @click="mobileMenu = false" class="absolute top-8 right-8 text-4xl"><i class="fa-solid fa-xmark"></i></button>
                    <a href="/hotel/" @click="mobileMenu = false" class="hover:text-gold">Inicio</a>
                    <a href="/hotel/nosotros" @click="mobileMenu = false" class="hover:text-gold">Nosotros</a>
                    <a href="/hotel/habitaciones" @click="mobileMenu = false" class="hover:text-gold">Habitaciones</a>
                    <a href="/hotel/contacto" @click="mobileMenu = false" class="hover:text-gold">Contacto</a>
                </div>
            </div>
        </div>
    </header>
<?php // End of header ?>
