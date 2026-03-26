<?php
/**
 * Página de Contacto - Tema Villas del Sol
 */
require_once THEME_PATH . '/layout/header.php';

$title = $lang === 'es' ? 'Contacto' : 'Contact Us';
$subtitle = $lang === 'es' ? 'Estamos aquí para ayudarle' : 'We are here to help you';
?>

<section class="py-24 bg-stone-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-20">
            <h1 class="text-stone-400 text-xs font-bold uppercase tracking-[0.4em] mb-4"><?= $title ?></h1>
            <h2 class="text-5xl font-playfair font-bold text-stone-900 italic"><?= $subtitle ?></h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
            
            <!-- Info Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-white p-10 rounded-[2rem] shadow-sm border border-stone-200/50">
                    <h3 class="text-xl font-playfair font-bold mb-8 italic"><?= $lang === 'es' ? 'Información de Contacto' : 'Contact Information' ?></h3>
                    
                    <div class="space-y-8">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-location-dot text-amber-800"></i>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-1"><?= $lang === 'es' ? 'Dirección' : 'Address' ?></span>
                                <span class="text-stone-700 text-sm leading-relaxed"><?= Language::translateJsonField($ajustes['address']) ?></span>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-phone text-amber-800"></i>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-1"><?= $lang === 'es' ? 'Teléfono' : 'Phone' ?></span>
                                <span class="text-stone-700 text-sm"><?= $ajustes['contact_phone'] ?></span>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-envelope text-amber-800"></i>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-1"><?= $lang === 'es' ? 'Correo Electrónico' : 'Email' ?></span>
                                <span class="text-stone-700 text-sm"><?= $ajustes['contact_email'] ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-10 border-t border-stone-100">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-6"><?= $lang === 'es' ? 'Síguenos' : 'Follow Us' ?></h4>
                        <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 rounded-full bg-stone-900 text-white flex items-center justify-center hover:bg-amber-800 transition-all"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" class="w-10 h-10 rounded-full bg-stone-900 text-white flex items-center justify-center hover:bg-amber-800 transition-all"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="w-10 h-10 rounded-full bg-stone-900 text-white flex items-center justify-center hover:bg-amber-800 transition-all"><i class="fa-brands fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Main -->
            <div class="lg:col-span-8">
                <div class="bg-white p-10 md:p-16 rounded-[3rem] shadow-xl border border-stone-100">
                    <div class="mb-10">
                        <h3 class="text-3xl font-playfair font-bold text-stone-900 italic mb-4"><?= $lang === 'es' ? 'Envíenos un mensaje' : 'Send us a message' ?></h3>
                        <p class="text-stone-500 text-sm">
                            <?= $lang === 'es' ? 'Complete el formulario a continuación y nuestro personal de conserjería le responderá a la brevedad.' : 'Complete the form below and our concierge staff will respond shortly.' ?>
                        </p>
                    </div>

                    <!-- AQUÍ INTEGRAMOS EL FORMBUILDER DINÁMICO -->
                    <div class="form-container">
                        <?= FormBuilder::render('contacto', $lang, ['pagina_origen' => 'Página de Contacto']) ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- MAP FULL WIDTH -->
<section class="h-[500px] w-full bg-stone-200 grayscale hover:grayscale-0 transition-all duration-1000 overflow-hidden">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m18!1m12!1m18!1m12!1m3!1d969.8661793732049!2d-71.98069697080645!3d-13.517595499298288!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x916e7f09339e1a01%3A0xe9938af8c64c764e!2sPlaza%20Regocijo!5e0!3m2!1ses!2spe!4v1710984000000!5m2!1ses!2spe" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
</section>

<?php require_once THEME_PATH . '/layout/footer.php'; ?>
