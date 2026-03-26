<?php

class FormBuilder {
    /**
     * Renderiza el HTML de un formulario basado en su slug o ID
     */
    public static function render($identifier, $lang = 'es', $context = []) {
        $db = Database::getInstance();
        
        if (is_numeric($identifier)) {
            $stmt = $db->prepare("SELECT * FROM formularios WHERE id = ? AND activo = 1");
        } else {
            $stmt = $db->prepare("SELECT * FROM formularios WHERE slug = ? AND activo = 1");
        }
        
        $stmt->execute([$identifier]);
        $form = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$form) return "<!-- Formulario $identifier no encontrado o inactivo -->";

        $stmt = $db->prepare("SELECT * FROM formulario_campos WHERE formulario_id = ? ORDER BY orden ASC");
        $stmt->execute([$form['id']]);
        $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        ?>
        <form action="/hotel/api/formularios/enviar.php" method="POST" class="dynamic-form space-y-4" data-id="<?= $form['id'] ?>">
            <input type="hidden" name="formulario_id" value="<?= $form['id'] ?>">
            <input type="hidden" name="idioma" value="<?= $lang ?>">
            
            <?php if (!empty($context['pagina_origen'])): ?>
                <input type="hidden" name="pagina_origen" value="<?= htmlspecialchars($context['pagina_origen']) ?>">
            <?php endif; ?>
            
            <?php if (!empty($context['tipo_entidad'])): ?>
                <input type="hidden" name="tipo_entidad" value="<?= htmlspecialchars($context['tipo_entidad']) ?>">
                <input type="hidden" name="entidad_id" value="<?= (int)($context['entidad_id'] ?? 0) ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($campos as $campo): 
                    $label = ($lang === 'en' && !empty($campo['label_en'])) ? $campo['label_en'] : $campo['label'];
                    $placeholder = $campo['placeholder'] ?: '';
                    $name = $campo['nombre_campo'];
                    $req = $campo['requerido'] ? 'required' : '';
                    $colClass = in_array($campo['tipo_campo'], ['textarea']) ? 'md:col-span-2' : 'col-span-1';
                ?>
                <div class="<?= $colClass ?>">
                    <label class="block text-sm font-medium text-gray-700 mb-1"><?= htmlspecialchars($label) ?><?= $req ? ' *' : '' ?></label>
                    
                    <?php if ($campo['tipo_campo'] === 'textarea'): ?>
                        <textarea name="<?= $name ?>" <?= $req ?> placeholder="<?= htmlspecialchars($placeholder) ?>"
                                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none h-32"></textarea>
                    
                    <?php elseif ($campo['tipo_campo'] === 'select'): 
                        $opciones = json_decode($campo['opciones'], true) ?: [];
                    ?>
                        <select name="<?= $name ?>" <?= $req ?> class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            <option value=""><?= $lang === 'en' ? '-- Select --' : '-- Seleccionar --' ?></option>
                            <?php foreach ($opciones as $op): ?>
                                <option value="<?= htmlspecialchars($op) ?>"><?= htmlspecialchars($op) ?></option>
                            <?php endforeach; ?>
                        </select>

                    <?php elseif ($campo['tipo_campo'] === 'checkbox'): ?>
                        <div class="flex items-center gap-2 mt-2">
                            <input type="checkbox" name="<?= $name ?>" id="field_<?= $campo['id'] ?>" <?= $req ?> class="w-4 h-4 accent-blue-600">
                            <label for="field_<?= $campo['id'] ?>" class="text-sm text-gray-600 cursor-pointer"><?= htmlspecialchars($label) ?></label>
                        </div>

                    <?php else: ?>
                        <input type="<?= $campo['tipo_campo'] === 'fecha' ? 'date' : ($campo['tipo_campo'] === 'email' ? 'email' : 'text') ?>" 
                               name="<?= $name ?>" <?= $req ?> placeholder="<?= htmlspecialchars($placeholder) ?>"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition-all shadow-lg active:scale-95">
                <?= $lang === 'en' ? 'Send Message' : 'Enviar Mensaje' ?>
            </button>
            <div class="form-response hidden mt-4 p-4 rounded-lg text-sm font-medium"></div>
        </form>

        <script>
        (function() {
            const form = document.querySelector('form[data-id="<?= $form['id'] ?>"]');
            if(!form) return;
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                const resp = form.querySelector('.form-response');
                
                btn.disabled = true;
                btn.innerHTML = '<?= $lang === 'en' ? 'Sending...' : 'Enviando...' ?>';
                
                try {
                    const formData = new FormData(form);
                    const res = await fetch(form.action, { method: 'POST', body: formData });
                    const data = await res.json();
                    
                    resp.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
                    resp.classList.add(data.exito ? 'bg-green-100' : 'bg-red-100', data.exito ? 'text-green-700' : 'text-red-700');
                    resp.innerHTML = data.mensaje || data.error;
                    
                    if(data.exito) {
                        form.reset();
                        if(data.redirect) setTimeout(() => window.location.href = data.redirect, 2000);
                    }
                } catch(e) {
                    resp.classList.remove('hidden');
                    resp.classList.add('bg-red-100', 'text-red-700');
                    resp.innerHTML = '<?= $lang === 'en' ? 'Connection error' : 'Error de conexión' ?>';
                }
                btn.disabled = false;
                btn.innerHTML = '<?= $lang === 'en' ? 'Send Message' : 'Enviar Mensaje' ?>';
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
