<?php
/**
 * Página 404 - Tema Villas del Sol
 */
require_once THEME_PATH . '/layout/header.php';
?>

<section class="min-h-[70vh] flex items-center justify-center text-center px-6">
    <div>
        <h1 class="text-9xl font-playfair font-bold text-stone-200 mb-4 italic">404</h1>
        <h2 class="text-3xl font-playfair font-bold text-stone-800 mb-8 italic">
            <?= $lang === 'es' ? 'Página Extraviada es la Historia' : 'Page Lost in History' ?>
        </h2>
        <p class="text-stone-500 max-w-md mx-auto mb-12 leading-relaxed">
            <?= $lang === 'es' ? 'Lo sentimos, el portal que busca parece haber desaparecido entre los muros coloniales. Regresemos al presente.' : 'Sorry, the portal you are looking for seems to have disappeared among colonial walls. Let\'s return to the present.' ?>
        </p>
        <a href="/hotel/" class="bg-amber-800 text-white px-10 py-4 rounded-full font-bold uppercase tracking-widest text-xs hover:bg-amber-900 transition-all shadow-lg active:scale-95">
            <?= $lang === 'es' ? 'Volver al Inicio' : 'Return Home' ?>
        </a>
    </div>
</section>

<?php require_once THEME_PATH . '/layout/footer.php'; ?>
