# 🚀 Ejemplos Prácticos - Sistema Canvas NBDesigner

Colección de ejemplos reales y casos de uso implementables para el sistema canvas de NBDesigner.

---

## 📑 Tabla de Contenidos

1. [Ejemplos de JavaScript](#ejemplos-de-javascript)
2. [Ejemplos de PHP/Backend](#ejemplos-de-phpbackend)
3. [Ejemplos de Integración](#ejemplos-de-integración)
4. [Snippets Útiles](#snippets-útiles)
5. [Troubleshooting](#troubleshooting)

---

## Ejemplos de JavaScript

### Ejemplo 1: Crear Plugin que Añada Watermark

**archivo: plugins/nbd-watermark/nbd-watermark.php**

```php
<?php
/**
 * Plugin Name: NBDesigner Watermark
 * Description: Añade automáticamente watermark a los diseños
 * Version: 1.0.0
 */

add_action('nbd_loaded', function() {
    // Enqueue script personalizado
    add_action('wp_enqueue_scripts', function() {
        wp_enqueue_script('nbd-watermark',
            plugin_dir_url(__FILE__) . 'assets/watermark.js',
            array('fabric'),
            '1.0.0'
        );
    });

    // Registrar filtro
    add_filter('nbd_canvas_initialized', 'add_watermark_to_canvas');
});

function add_watermark_to_canvas($canvas) {
    // El script JavaScript se encargará de la lógica
    return $canvas;
}
```

**archivo: plugins/nbd-watermark/assets/watermark.js**

```javascript
// Hook que se ejecuta cuando el canvas está listo
if (typeof window.nbCanvasReady === 'undefined') {
    window.nbCanvasReady = [];
}

window.nbCanvasReady.push(function(stages, designCtrl) {
    console.log('Adding watermark to canvas');

    // Configuración del watermark
    var watermarkConfig = {
        text: '© 2024 Mi Empresa',
        fontSize: 14,
        opacity: 0.3,
        angle: -45,
        position: 'bottom-right'  // 'center', 'bottom-right', etc.
    };

    // Añadir watermark a cada stage
    stages.forEach(function(stage, index) {
        addWatermarkToStage(stage, watermarkConfig);
    });

    // También cuando se añade un nuevo stage
    designCtrl.$scope.$watch('stages.length', function(newVal, oldVal) {
        if (newVal > oldVal) {
            var newStage = stages[stages.length - 1];
            addWatermarkToStage(newStage, watermarkConfig);
        }
    });
});

function addWatermarkToStage(stage, config) {
    var watermark = new fabric.Text(config.text, {
        left: 10,
        top: 10,
        fontSize: config.fontSize,
        fontFamily: 'Arial',
        fill: '#999999',
        opacity: config.opacity,
        angle: config.angle,
        selectable: false,      // No se puede seleccionar
        evented: false,         // No genera eventos
        perPixelTargetFind: false,
        name: '_watermark'      // Identificador especial
    });

    stage.canvas.add(watermark);

    // Enviar al fondo (z-index)
    stage.canvas.sendToBack(watermark);
    stage.canvas.renderAll();

    // Proteger watermark en guardado
    var originalToJSON = stage.canvas.toJSON.bind(stage.canvas);
    stage.canvas.toJSON = function() {
        var json = originalToJSON();

        // Filtrar watermark
        json.objects = json.objects.filter(function(obj) {
            return obj.name !== '_watermark';
        });

        return json;
    };
}

// Hook de guardado
if (typeof window.nbBeforeSave === 'undefined') {
    window.nbBeforeSave = [];
}

window.nbBeforeSave.push(function(designData) {
    // Validaciones antes de guardar
    console.log('Validando diseño antes de guardar...');
    return true;  // Continuar con guardado
});
```

### Ejemplo 2: Sistema de Templates Predefini

```javascript
/**
 * Sistema de templates personalizados
 */

class TemplateManager {
    constructor() {
        this.templates = {
            'tshirt_front': {
                name: 'Camiseta - Frente',
                width: 200,
                height: 200,
                bgColor: '#ffffff',
                defaultElements: [
                    {
                        type: 'text',
                        text: 'Tu Texto Aquí',
                        left: 50,
                        top: 50,
                        fontSize: 24,
                        fontFamily: 'Arial',
                        fill: '#000000'
                    }
                ]
            },
            'tshirt_back': {
                name: 'Camiseta - Espalda',
                width: 200,
                height: 200,
                bgColor: '#f0f0f0',
                defaultElements: []
            },
            'mug_wrap': {
                name: 'Taza - Envolvente',
                width: 300,
                height: 100,
                bgColor: '#ffffff',
                defaultElements: [
                    {
                        type: 'text',
                        text: 'Nombre',
                        left: 10,
                        top: 30,
                        fontSize: 20,
                        fontFamily: 'Comic Sans MS'
                    }
                ]
            }
        };
    }

    getTemplate(templateId) {
        return this.templates[templateId] || null;
    }

    loadTemplate(templateId, canvas) {
        var template = this.getTemplate(templateId);

        if (!template) {
            console.error('Template not found:', templateId);
            return false;
        }

        // Limpiar canvas
        canvas.clear();

        // Cargar configuración
        canvas.setBackgroundColor(template.bgColor);

        // Añadir elementos por defecto
        template.defaultElements.forEach(function(elem) {
            switch (elem.type) {
                case 'text':
                    var text = new fabric.IText(elem.text, {
                        left: elem.left,
                        top: elem.top,
                        fontSize: elem.fontSize,
                        fontFamily: elem.fontFamily,
                        fill: elem.fill || '#000000'
                    });
                    canvas.add(text);
                    break;

                case 'rect':
                    var rect = new fabric.Rect({
                        left: elem.left,
                        top: elem.top,
                        width: elem.width,
                        height: elem.height,
                        fill: elem.fill || '#ffffff',
                        stroke: elem.stroke || '#000000'
                    });
                    canvas.add(rect);
                    break;
            }
        });

        canvas.renderAll();
        return true;
    }

    createCustomTemplate(id, name, width, height, elements) {
        this.templates[id] = {
            name: name,
            width: width,
            height: height,
            bgColor: '#ffffff',
            defaultElements: elements
        };

        // Guardar en localStorage
        localStorage.setItem('nbTemplate_' + id, JSON.stringify(this.templates[id]));

        return true;
    }

    deleteTemplate(id) {
        delete this.templates[id];
        localStorage.removeItem('nbTemplate_' + id);
        return true;
    }

    listAllTemplates() {
        return Object.keys(this.templates).map(key => ({
            id: key,
            name: this.templates[key].name
        }));
    }
}

// Uso:
var templateManager = new TemplateManager();

// Mostrar lista de templates
var templates = templateManager.listAllTemplates();
templates.forEach(function(tpl) {
    console.log(tpl.id + ': ' + tpl.name);
});

// Cargar template
templateManager.loadTemplate('tshirt_front', canvas);

// Crear template personalizado
templateManager.createCustomTemplate('custom_1', 'Mi Template', 250, 250, [
    {type: 'text', text: 'Hola', left: 50, top: 50, fontSize: 20}
]);
```

### Ejemplo 3: Sistema de Capas Avanzado

```javascript
/**
 * Sistema de capas avanzado con grupos
 */

class LayerManager {
    constructor(canvas) {
        this.canvas = canvas;
        this.layers = [];
        this.groupStack = [];
    }

    addLayer(name, objects) {
        var layer = {
            id: 'layer_' + Date.now(),
            name: name,
            visible: true,
            locked: false,
            objects: objects || [],
            opacity: 1
        };

        this.layers.push(layer);
        return layer;
    }

    createGroup(name) {
        var group = {
            id: 'group_' + Date.now(),
            type: 'group',
            name: name,
            children: [],
            visible: true,
            locked: false
        };

        this.groupStack.push(group);
        return group;
    }

    endGroup() {
        if (this.groupStack.length > 0) {
            return this.groupStack.pop();
        }
        return null;
    }

    addToCurrentGroup(object) {
        if (this.groupStack.length > 0) {
            var currentGroup = this.groupStack[this.groupStack.length - 1];
            currentGroup.children.push(object);
        }
    }

    toggleLayerVisibility(layerId) {
        var layer = this.getLayer(layerId);
        if (layer) {
            layer.visible = !layer.visible;
            this.updateCanvasVisibility();
            return layer.visible;
        }
        return null;
    }

    toggleLayerLock(layerId) {
        var layer = this.getLayer(layerId);
        if (layer) {
            layer.locked = !layer.locked;
            this.updateCanvasLock();
            return layer.locked;
        }
        return null;
    }

    getLayer(layerId) {
        return this.layers.find(l => l.id === layerId);
    }

    updateCanvasVisibility() {
        this.canvas.forEachObject(function(obj) {
            var layer = this.layers.find(l =>
                l.objects.some(o => o === obj)
            );

            if (layer) {
                obj.visible = layer.visible;
            }
        }.bind(this));

        this.canvas.renderAll();
    }

    updateCanvasLock() {
        this.canvas.forEachObject(function(obj) {
            var layer = this.layers.find(l =>
                l.objects.some(o => o === obj)
            );

            if (layer) {
                obj.selectable = !layer.locked;
                obj.evented = !layer.locked;
            }
        }.bind(this));
    }

    setLayerOpacity(layerId, opacity) {
        var layer = this.getLayer(layerId);
        if (layer && opacity >= 0 && opacity <= 1) {
            layer.opacity = opacity;

            layer.objects.forEach(function(obj) {
                obj.opacity = opacity;
            });

            this.canvas.renderAll();
            return true;
        }
        return false;
    }

    exportLayers() {
        return JSON.stringify(this.layers, null, 2);
    }

    importLayers(jsonString) {
        try {
            this.layers = JSON.parse(jsonString);
            return true;
        } catch (e) {
            console.error('Error importing layers:', e);
            return false;
        }
    }
}

// Uso:
var layerManager = new LayerManager(canvas);

// Crear grupos
layerManager.createGroup('Textos');
// ... añadir objetos de texto
layerManager.endGroup();

layerManager.createGroup('Imágenes');
// ... añadir imágenes
layerManager.endGroup();

// Exportar/Importar
var layersJSON = layerManager.exportLayers();
// ... guardar en BD ...

// Importar después
layerManager.importLayers(savedLayersJSON);
```

### Ejemplo 4: Selector de Color Avanzado

```javascript
/**
 * Selector de color con presets
 */

class ColorManager {
    constructor() {
        this.presets = {
            'brand': ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8'],
            'pastel': ['#FFB3BA', '#FFCCCB', '#FFFFBA', '#BAE1FF', '#E0BBE4'],
            'dark': ['#1A1A2E', '#16213E', '#0F3460', '#533483', '#662D91'],
            'bright': ['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF'],
            'metallic': ['#C0C0C0', '#FFD700', '#CD7F32', '#87CEEB', '#4B0082']
        };

        this.customColors = [];
        this.recentColors = [];
    }

    getPreset(presetId) {
        return this.presets[presetId] || [];
    }

    getAllPresets() {
        return Object.keys(this.presets);
    }

    addCustomColor(color) {
        if (!this.customColors.includes(color)) {
            this.customColors.push(color);
            localStorage.setItem('nbCustomColors', JSON.stringify(this.customColors));
        }
    }

    addRecentColor(color) {
        this.recentColors.unshift(color);
        if (this.recentColors.length > 10) {
            this.recentColors.pop();
        }
    }

    getRecentColors() {
        return this.recentColors;
    }

    getCustomColors() {
        return this.customColors;
    }

    validateColor(color) {
        // Validar formato hex
        return /^#[0-9A-F]{6}$/i.test(color);
    }

    hexToRGB(hex) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    rgbToHex(r, g, b) {
        return '#' + [r, g, b].map(x => {
            const hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('').toUpperCase();
    }

    getLuminance(hex) {
        var rgb = this.hexToRGB(hex);
        if (!rgb) return 0;

        // Calcular luminancia relativa (W3C)
        var luminance = (0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b) / 255;
        return luminance;
    }

    getContrastColor(hex) {
        // Retorna color de contraste (blanco o negro)
        return this.getLuminance(hex) > 0.5 ? '#000000' : '#FFFFFF';
    }

    generateColorScheme(baseColor, type) {
        // Generar esquema de colores basado en uno base
        var rgb = this.hexToRGB(baseColor);
        var scheme = [];

        if (type === 'complementary') {
            // Color complementario
            scheme.push(baseColor);
            // ... lógica de complementario
        } else if (type === 'analogous') {
            // Colores análogos
            // ... lógica análoga
        } else if (type === 'triadic') {
            // Triada de colores
            // ... lógica triada
        }

        return scheme;
    }
}

// Uso:
var colorManager = new ColorManager();

// Obtener preset
var brandColors = colorManager.getPreset('brand');
console.log(brandColors);  // ['#FF6B6B', ...]

// Validar color
if (colorManager.validateColor('#FF6B6B')) {
    console.log('Color válido');
}

// Obtener color de contraste
var textColor = colorManager.getContrastColor('#FF6B6B');
console.log(textColor);  // '#FFFFFF' (blanco, para buen contraste)

// Añadir color personalizado
colorManager.addCustomColor('#ABC123');
colorManager.addRecentColor('#FF6B6B');
```

---

## Ejemplos de PHP/Backend

### Ejemplo 1: API Custom para Validación

**archivo: includes/api/custom-validation.php**

```php
<?php

add_action('wp_ajax_nbd_validate_custom', function() {
    // Verificar nonce
    check_ajax_referer('nbd_validate_custom_nonce');

    $design_data = isset($_POST['design_data']) ?
        json_decode(stripslashes($_POST['design_data']), true) : array();

    $errors = array();

    // Validación 1: Mínimo un elemento
    if (empty($design_data['objects'])) {
        $errors[] = 'El diseño debe tener al menos un elemento';
    }

    // Validación 2: Máximo 100 elementos
    if (count($design_data['objects']) > 100) {
        $errors[] = 'Máximo 100 elementos permitidos';
    }

    // Validación 3: Verificar que haya texto
    $has_text = false;
    foreach ($design_data['objects'] as $obj) {
        if ($obj['type'] === 'text') {
            $has_text = true;
            break;
        }
    }

    if (!$has_text) {
        $errors[] = 'El diseño debe contener al menos un texto';
    }

    // Validación 4: Limitar tamaño total
    $design_json_size = strlen(json_encode($design_data));
    $max_size = 5 * 1024 * 1024;  // 5 MB

    if ($design_json_size > $max_size) {
        $errors[] = 'El diseño es demasiado grande. Máximo 5 MB';
    }

    if (!empty($errors)) {
        wp_send_json_error(array('errors' => $errors));
    } else {
        wp_send_json_success(array('message' => 'Validación exitosa'));
    }
});

// Registrar nonce
wp_nonce_field('nbd_validate_custom_nonce', 'nonce');
```

### Ejemplo 2: Generar Miniatura de Diseño

**archivo: includes/class-design-thumbnail.php**

```php
<?php

class NBDesigner_Thumbnail_Generator {

    public static function generate_thumbnail($design_data, $output_size = 'thumbnail') {
        /**
         * Genera miniatura de diseño
         *
         * @param array $design_data Datos JSON del diseño
         * @param string $output_size Tamaño: 'thumbnail', 'medium', 'large'
         */

        // Convertir JSON a imagen
        require_once(NBDESIGNER_PLUGIN_DIR . 'includes/class-html2canvas.php');

        $sizes = array(
            'thumbnail' => array(150, 150),
            'medium' => array(300, 300),
            'large' => array(600, 600)
        );

        $size = isset($sizes[$output_size]) ? $sizes[$output_size] : $sizes['thumbnail'];

        try {
            // Crear imagen usando bibliotecas
            $image = self::render_design_as_image($design_data, $size[0], $size[1]);

            // Guardar archivo
            $filename = 'design_thumb_' . time() . '.png';
            $upload_dir = wp_upload_dir();
            $filepath = $upload_dir['basedir'] . '/nbdesigner/' . $filename;

            imagepng($image, $filepath);
            imagedestroy($image);

            return array(
                'success' => true,
                'url' => $upload_dir['baseurl'] . '/nbdesigner/' . $filename,
                'path' => $filepath
            );

        } catch (Exception $e) {
            return array(
                'success' => false,
                'error' => $e->getMessage()
            );
        }
    }

    private static function render_design_as_image($design_data, $width, $height) {
        // Crear imagen GD
        $image = imagecreatetruecolor($width, $height);

        // Color de fondo
        $bg_color = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bg_color);

        // Procesar cada objeto del diseño
        if (isset($design_data['objects']) && is_array($design_data['objects'])) {
            foreach ($design_data['objects'] as $object) {
                if ($object['type'] === 'text') {
                    self::draw_text_on_image($image, $object, $width, $height);
                } else if ($object['type'] === 'image') {
                    self::draw_image_on_image($image, $object, $width, $height);
                } else if ($object['type'] === 'rect') {
                    self::draw_rect_on_image($image, $object, $width, $height);
                }
            }
        }

        return $image;
    }

    private static function draw_text_on_image(&$image, $text_obj, $canv_w, $canv_h) {
        $text = isset($text_obj['text']) ? $text_obj['text'] : '';
        $x = isset($text_obj['left']) ? intval($text_obj['left']) : 10;
        $y = isset($text_obj['top']) ? intval($text_obj['top']) : 10;

        // Color del texto
        $fill = isset($text_obj['fill']) ? $text_obj['fill'] : '#000000';
        $rgb = self::hex2rgb($fill);
        $text_color = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);

        // Fuente (usar fuente del sistema)
        $font = 4;  // Fuente integrada

        imagestring($image, $font, $x, $y, $text, $text_color);
    }

    private static function draw_image_on_image(&$image, $img_obj, $canv_w, $canv_h) {
        $src = isset($img_obj['src']) ? $img_obj['src'] : '';
        $x = isset($img_obj['left']) ? intval($img_obj['left']) : 0;
        $y = isset($img_obj['top']) ? intval($img_obj['top']) : 0;
        $w = isset($img_obj['width']) ? intval($img_obj['width']) : 100;
        $h = isset($img_obj['height']) ? intval($img_obj['height']) : 100;

        // Descargar y insertar imagen
        $img = @imagecreatefromstring(file_get_contents($src));
        if ($img) {
            imagecopyresampled($image, $img, $x, $y, 0, 0, $w, $h, imagesx($img), imagesy($img));
            imagedestroy($img);
        }
    }

    private static function draw_rect_on_image(&$image, $rect_obj, $canv_w, $canv_h) {
        $x = isset($rect_obj['left']) ? intval($rect_obj['left']) : 0;
        $y = isset($rect_obj['top']) ? intval($rect_obj['top']) : 0;
        $w = isset($rect_obj['width']) ? intval($rect_obj['width']) : 100;
        $h = isset($rect_obj['height']) ? intval($rect_obj['height']) : 100;
        $fill = isset($rect_obj['fill']) ? $rect_obj['fill'] : '#000000';

        $rgb = self::hex2rgb($fill);
        $color = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);

        imagefilledrectangle($image, $x, $y, $x + $w, $y + $h, $color);
    }

    private static function hex2rgb($color) {
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        if (strlen($color) == 6) {
            list($r, $g, $b) = array(
                $color[0].$color[1],
                $color[2].$color[3],
                $color[4].$color[5]
            );
        } else if (strlen($color) == 3) {
            list($r, $g, $b) = array(
                $color[0].$color[0],
                $color[1].$color[1],
                $color[2].$color[2]
            );
        } else {
            return array('r' => 0, 'g' => 0, 'b' => 0);
        }

        return array(
            'r' => hexdec($r),
            'g' => hexdec($g),
            'b' => hexdec($b)
        );
    }
}

// Uso
$thumbnail = NBDesigner_Thumbnail_Generator::generate_thumbnail($design_data, 'medium');
if ($thumbnail['success']) {
    echo 'Miniatura creada: ' . $thumbnail['url'];
} else {
    echo 'Error: ' . $thumbnail['error'];
}
```

### Ejemplo 3: Exportar Diseño a PDF

**archivo: includes/class-pdf-export.php**

```php
<?php

class NBDesigner_PDF_Export {

    /**
     * Exporta diseño a PDF de alta calidad
     *
     * @param array $design_data Datos del diseño
     * @param array $options Opciones de exportación
     * @return string Ruta del archivo PDF
     */
    public static function export_to_pdf($design_data, $options = array()) {
        require_once(NBDESIGNER_PLUGIN_DIR . 'includes/libs/TCPDF/tcpdf.php');

        // Configuración por defecto
        $defaults = array(
            'title' => 'Mi Diseño',
            'dpi' => 300,
            'quality' => 95,
            'format' => 'A4'
        );

        $options = array_merge($defaults, $options);

        // Crear documento PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $options['format']);

        // Configurar documento
        $pdf->SetCreator('NBDesigner');
        $pdf->SetAuthor('NBDesigner');
        $pdf->SetTitle($options['title']);
        $pdf->SetSubject('Diseño personalizado');

        // Márgenes
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        // Procesar cada stage (página)
        if (isset($design_data['stages']) && is_array($design_data['stages'])) {
            foreach ($design_data['stages'] as $index => $stage) {
                if ($index > 0) {
                    $pdf->AddPage();  // Nueva página para cada stage
                }

                // Convertir canvas a imagen
                $image_path = self::render_stage_to_image($stage, $options['dpi']);

                if (file_exists($image_path)) {
                    // Insertar imagen en PDF
                    $pdf->Image($image_path, 10, 10, 190, 190, 'PNG');

                    // Eliminar imagen temporal
                    @unlink($image_path);
                }
            }
        }

        // Guardar PDF
        $upload_dir = wp_upload_dir();
        $filename = 'design_' . time() . '.pdf';
        $filepath = $upload_dir['basedir'] . '/nbdesigner/pdfs/' . $filename;

        // Asegurar directorio
        wp_mkdir_p(dirname($filepath));

        $pdf->Output($filepath, 'F');

        return array(
            'success' => file_exists($filepath),
            'url' => $upload_dir['baseurl'] . '/nbdesigner/pdfs/' . $filename,
            'path' => $filepath,
            'filename' => $filename
        );
    }

    private static function render_stage_to_image($stage, $dpi) {
        /**
         * Renderiza un stage a imagen PNG
         */

        // Esta función requeriría una librería como
        // wkhtmltoimage o phantomjs para renderizar el canvas

        // Por ahora, retornamos un placeholder
        $upload_dir = wp_upload_dir();
        $temp_file = $upload_dir['basedir'] . '/nbdesigner/temp/' . 'stage_' . time() . '.png';

        // Crear imagen de ejemplo (en producción, renderizar real)
        // ... código de renderizado ...

        return $temp_file;
    }
}

// Uso
$result = NBDesigner_PDF_Export::export_to_pdf($design_data, array(
    'title' => 'Mi Camiseta Personalizada',
    'dpi' => 300,
    'format' => 'A4'
));

if ($result['success']) {
    echo 'PDF descargable en: ' . $result['url'];
}
```

---

## Ejemplos de Integración

### Ejemplo 1: Integración con WooCommerce

**archivo: includes/class-woo-integration.php**

```php
<?php

class NBDesigner_WooCommerce_Integration {

    public function __construct() {
        // Mostrar botón personalizar en producto
        add_action('woocommerce_product_thumbnails', array($this, 'add_customize_button'));

        // Añadir precio por personalización
        add_action('woocommerce_product_get_price', array($this, 'add_customization_fee'));

        // Guardar datos de diseño en orden
        add_action('woocommerce_checkout_create_order_line_item', array($this, 'save_design_to_order'));
    }

    public function add_customize_button() {
        global $product;

        if ($this->is_customizable_product($product->get_id())) {
            echo '<button class="button btn-customize" data-product-id="' . $product->get_id() . '">';
            esc_html_e('Personalizar', 'nbdesigner');
            echo '</button>';
        }
    }

    public function add_customization_fee($price) {
        if (is_admin() || !is_product()) {
            return $price;
        }

        global $product;

        if ($this->is_customizable_product($product->get_id())) {
            $customization_fee = get_post_meta($product->get_id(), '_customization_fee', true);
            if ($customization_fee) {
                return (float)$price + (float)$customization_fee;
            }
        }

        return $price;
    }

    public function save_design_to_order($item, $cart_item_key, $values, $order) {
        if (isset($values['nbd_design_id'])) {
            $item->add_meta_data('nbd_design_id', $values['nbd_design_id']);
            $item->add_meta_data('nbd_design_data', $values['nbd_design_data']);
        }
    }

    private function is_customizable_product($product_id) {
        $customizable = get_post_meta($product_id, '_enable_customization', true);
        return $customizable === 'yes';
    }
}

new NBDesigner_WooCommerce_Integration();
```

### Ejemplo 2: Guardar Diseño en Base de Datos Personalizada

**archivo: includes/class-custom-database.php**

```php
<?php

class NBDesigner_Custom_Database {

    public static function save_design_with_metadata($design_data, $user_id, $metadata = array()) {
        /**
         * Guarda diseño con metadatos adicionales
         */

        global $wpdb;

        $table = $wpdb->prefix . 'nbdesigner_custom';

        // Asegurar tabla existe
        self::create_table();

        // Preparar datos
        $insert_data = array(
            'user_id' => $user_id,
            'design_data' => json_encode($design_data),
            'metadata' => json_encode($metadata),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
            'status' => 'draft'
        );

        // Guardar
        $result = $wpdb->insert($table, $insert_data);

        if ($result === false) {
            return array(
                'success' => false,
                'error' => $wpdb->last_error
            );
        }

        return array(
            'success' => true,
            'design_id' => $wpdb->insert_id
        );
    }

    public static function get_user_designs($user_id, $limit = 20) {
        /**
         * Obtiene todos los diseños del usuario
         */

        global $wpdb;
        $table = $wpdb->prefix . 'nbdesigner_custom';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC LIMIT %d",
            $user_id,
            $limit
        ));

        return $results;
    }

    public static function delete_design($design_id) {
        /**
         * Elimina un diseño
         */

        global $wpdb;
        $table = $wpdb->prefix . 'nbdesigner_custom';

        $result = $wpdb->delete($table, array('id' => $design_id));

        return $result !== false;
    }

    private static function create_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'nbdesigner_custom';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            design_data longtext NOT NULL,
            metadata longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'draft',
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Uso
$result = NBDesigner_Custom_Database::save_design_with_metadata(
    $design_data,
    get_current_user_id(),
    array(
        'product_id' => 123,
        'customization_type' => 'tshirt',
        'notes' => 'Personalización especial'
    )
);

if ($result['success']) {
    echo 'Diseño guardado con ID: ' . $result['design_id'];
}
```

---

## Snippets Útiles

### Snippet 1: Monitorear Cambios del Canvas

```javascript
var originalSaveDesign = $scope.saveDesign;

$scope.saveDesign = function() {
    console.log('Guardando diseño...');

    var startTime = performance.now();

    originalSaveDesign();

    var endTime = performance.now();
    console.log('Diseño guardado en ' + (endTime - startTime).toFixed(2) + 'ms');
};

// Monitorear eventos
canvas.on('object:added', function(e) {
    console.log('Objeto añadido:', e.target.type);
});

canvas.on('object:modified', function(e) {
    console.log('Objeto modificado:', e.target.type);
});

canvas.on('object:removed', function(e) {
    console.log('Objeto removido');
});
```

### Snippet 2: Resetear Canvas Completamente

```javascript
function resetCanvas(canvas) {
    // Deseleccionar todo
    canvas.discardActiveObject();

    // Limpiar canvas
    canvas.clear();

    // Resetear propiedades
    canvas.setBackgroundColor('#ffffff');
    canvas.setBackgroundImage(null);

    // Renderizar
    canvas.renderAll();

    console.log('Canvas reseteado');
}

// Uso
resetCanvas(stages[0].canvas);
```

### Snippet 3: Exportar e Importar Estado del Canvas

```javascript
function exportCanvasState(canvas) {
    return JSON.stringify({
        canvas: canvas.toJSON(),
        timestamp: new Date().toISOString(),
        version: '1.0'
    });
}

function importCanvasState(canvas, stateJson) {
    var state = JSON.parse(stateJson);

    canvas.loadFromJSON(state.canvas, function() {
        canvas.renderAll();
        console.log('Estado importado');
    });
}

// Uso
var state = exportCanvasState(canvas);
localStorage.setItem('canvasState', state);

// Después...
var savedState = localStorage.getItem('canvasState');
importCanvasState(canvas, savedState);
```

### Snippet 4: Validación de Fuentes

```php
function validate_font_availability($fontName) {
    $available_fonts = get_option('nbdesigner_available_fonts', array());

    if (in_array($fontName, $available_fonts)) {
        return true;
    }

    error_log('Fuente no disponible: ' . $fontName);
    return false;
}

// Uso
if (!validate_font_availability('MyCustomFont')) {
    // Usar fuente por defecto
    $fontName = 'Arial';
}
```

### Snippet 5: Limpieza de Archivos Temporales

```php
function cleanup_old_design_files($days = 7) {
    global $wpdb;

    $upload_dir = wp_upload_dir();
    $temp_dir = $upload_dir['basedir'] . '/nbdesigner/temp/';

    $files = glob($temp_dir . '*');
    $now = time();
    $max_age = $days * 24 * 60 * 60;

    foreach ($files as $file) {
        if (is_file($file) && ($now - filemtime($file)) > $max_age) {
            unlink($file);
            error_log('Archivo temporal eliminado: ' . $file);
        }
    }
}

// Ejecutar cada día
add_action('wp_scheduled_delete', 'cleanup_old_design_files');

if (!wp_next_scheduled('wp_scheduled_delete')) {
    wp_schedule_event(time(), 'daily', 'wp_scheduled_delete');
}
```

---

## Troubleshooting

### Problema: Canvas no se sincroniza

```javascript
// Solución: Forzar sincronización manual
function forceSyncCanvas() {
    var designData = {
        stages: $scope.stages,
        resource: $scope.resource
    };

    $http.post(ajaxurl, {
        action: 'nbd_save_design',
        design_data: JSON.stringify(designData),
        _nonce: nbd_nonce
    }).then(function(response) {
        console.log('Sincronizado correctamente');
        nbDebug.log('Canvas sincronizado');
    }).catch(function(error) {
        console.error('Error de sincronización:', error);
    });
}
```

### Problema: Memoria insuficiente

```javascript
// Solución: Limpiar objetos no utilizados
function optimizeCanvasMemory(canvas) {
    canvas.forEachObject(function(obj) {
        // Limpiar propiedades innecesarias
        delete obj._cacheCanvas;
        delete obj._cacheProperties;
    });

    // Forzar garbage collection (si es posible)
    if (window.gc) {
        window.gc();
    }

    console.log('Memoria optimizada');
}
```

---

**Última actualización:** Noviembre 2025
**Versión del documento:** 1.0
