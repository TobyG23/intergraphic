# 👨‍💻 Guía de Desarrollo - Sistema Canvas NBDesigner

Guía práctica para desarrolladores que quieren personalizar, extender o integrar el sistema canvas de NBDesigner.

---

## 📚 Tabla de Contenidos

1. [Configuración del Entorno](#configuración-del-entorno)
2. [Estructura del Código](#estructura-del-código)
3. [Cómo Añadir Elementos Personalizados](#cómo-añadir-elementos-personalizados)
4. [Crear Extensiones de Fabric.js](#crear-extensiones-de-fabricjs)
5. [Personalizar la Interfaz](#personalizar-la-interfaz)
6. [Validación y Restricciones](#validación-y-restricciones)
7. [Debugging y Testing](#debugging-y-testing)
8. [Casos de Uso Comunes](#casos-de-uso-comunes)

---

## Configuración del Entorno

### Requisitos Previos

```bash
# Sistema operativo
- Linux, macOS o Windows

# Software necesario
- PHP 5.6 o superior
- MySQL 5.6 o superior
- WordPress 4.6 o superior
- WooCommerce 3.0 o superior
- Node.js (opcional, para development)

# Navegador para desarrollo
- Chrome/Firefox con DevTools
- Complemento Vue/React DevTools (opcional)
```

### Instalación Local

```bash
# 1. Clonar/descargar el repositorio
git clone <repositorio>
cd intergraphic

# 2. Asegurar permisos
chmod 755 wp-content/plugins/web-to-print-online-designer/

# 3. Crear directorio de subidas si no existe
mkdir -p wp-content/uploads/nbdesigner/{fonts,cliparts,designs,downloads,temp,logs,pdfs}
chmod 777 wp-content/uploads/nbdesigner/

# 4. Verificar instalación de WordPress
# Navegar a http://localhost/intergraphic/wp-admin/

# 5. Activar plugin
# WordPress Admin > Plugins > NBDesigner > Activate
```

### Configuración de Desarrollo

**Habilitar modo debug en WordPress:**

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);  // No mostrar en frontend

// Los logs van a: wp-content/debug.log
```

**Deshabilitar minificación en desarrollo:**

```javascript
// assets/js/app-modern.min.js
// En lugar de usar la versión minificada, usar:
// assets/js/app-modern.js (si existe código fuente)

// O usar source maps para debugging
// En Chrome DevTools: F12 > Sources
```

### Estructura de Carpetas Recomendada

```
tu-extension/
├── tu-extension.php          // Punto de entrada
├── assets/
│   ├── js/
│   │   ├── main.js          // Código principal
│   │   └── fabric-ext.js    // Extensiones de Fabric
│   ├── css/
│   │   └── styles.css
│   └── img/
├── includes/
│   ├── class-canvas.php     // Clase custom
│   └── api.php              // Endpoints custom
├── views/
│   └── admin.php            // Admin views
└── README.md
```

---

## Estructura del Código

### Flujo de Ejecución Actual

```
1. Plugin Entry Point
   └─ nbdesigner.php
      ├─ Define constants
      ├─ Require classes
      └─ Initialize plugin

2. Plugin Class
   └─ class.nbdesigner.php
      ├─ Register hooks
      ├─ Load assets
      └─ Initialize pages

3. Launcher (Editor)
   └─ class.launcher.php
      ├─ Setup canvas
      ├─ Load template
      └─ Initialize AngularJS

4. Frontend (AngularJS)
   └─ app-modern.min.js
      ├─ Create module
      ├─ Initialize controller
      ├─ Load directives
      └─ Setup Fabric.js

5. Canvas (Fabric.js)
   └─ fabric.2.6.0.min.js
      ├─ Create canvas instance
      ├─ Add/remove objects
      ├─ Handle events
      └─ Render canvas

6. Backend (PHP)
   └─ API endpoints
      ├─ Save design
      ├─ Load design
      ├─ Delete design
      └─ Generate preview
```

### Puntos de Extensión (Hooks)

**Hooks de Acción (Actions):**

```php
// En nbdesigner.php línea 206
do_action('nbd_loaded');  // Después de cargar plugin

// En stages.php línea 4
do_action('nbd_modern_before_stage');  // Antes de canvas

// En stages.php línea 21
do_action('nbd_modern_before_design_wrap');

// En stages.php línea 119
do_action('nbd_modern_after_design_wrap');

// En stages.php línea 131
do_action('nbd_modern_extra_page_toolbar');

// En stages.php línea 191
do_action('nbd_modern_extra_stages');
```

**Filtros (Filters):**

```php
// Personalizar opciones del plugin
apply_filters('nbdesigner_option', $default_value);

// Personalizar canvas HTML
apply_filters('nbd_canvas_html', $html, $stage);

// Personalizar datos antes de guardar
apply_filters('nbd_design_data_before_save', $data);

// Personalizar datos después de cargar
apply_filters('nbd_design_data_after_load', $data);
```

---

## Cómo Añadir Elementos Personalizados

### Paso 1: Crear Clase para el Elemento

**archivo: includes/class-custom-element.php**

```php
<?php

class NBDesigner_Custom_Element {

    public function __construct() {
        add_action('nbd_loaded', array($this, 'register_element'));
    }

    public function register_element() {
        // Registrar elemento personalizado
        add_filter('nbd_element_types', array($this, 'add_element_type'));

        // Enqueue script
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

        // Crear endpoint para validación
        add_action('wp_ajax_nbd_validate_custom', array($this, 'validate_custom'));
    }

    public function add_element_type($types) {
        $types['custom_badge'] = array(
            'label' => 'Badge Personalizado',
            'icon' => 'icon-star',
            'category' => 'shapes',
            'properties' => array(
                'text',
                'color',
                'size',
                'shape'  // circle, square, hexagon
            )
        );
        return $types;
    }

    public function enqueue_assets() {
        wp_enqueue_script('nbd-custom-element',
            plugin_dir_url(__FILE__) . '../assets/js/custom-element.js',
            array('fabric', 'angular'),
            '1.0.0'
        );
    }

    public function validate_custom() {
        // Validación del lado del servidor
        $data = $_POST['data'];

        // Validaciones
        if (empty($data['text'])) {
            wp_send_json_error('El texto es requerido');
        }

        if (strlen($data['text']) > 50) {
            wp_send_json_error('El texto no puede exceder 50 caracteres');
        }

        wp_send_json_success();
    }
}

new NBDesigner_Custom_Element();
```

### Paso 2: Crear Extensión de Fabric.js

**archivo: assets/js/custom-element.js**

```javascript
// Crear clase personalizada que extienda fabric.Group
fabric.CustomBadge = fabric.util.createClass(fabric.Group, {

    type: 'custom_badge',

    initialize: function(options) {
        options = options || {};

        var text = new fabric.Text(options.text || 'Badge', {
            fontSize: 16,
            fontFamily: 'Arial',
            fill: options.textColor || '#ffffff',
            textAlign: 'center'
        });

        var shape;
        if (options.shape === 'circle') {
            shape = new fabric.Circle({
                radius: 50,
                fill: options.color || '#ff0000'
            });
        } else if (options.shape === 'hexagon') {
            shape = this.createHexagon(options.color);
        } else {
            shape = new fabric.Rect({
                width: 100,
                height: 100,
                fill: options.color || '#ff0000'
            });
        }

        this.callSuper('initialize', [shape, text], options);

        // Propiedades adicionales
        this.customColor = options.color || '#ff0000';
        this.customText = options.text || 'Badge';
    },

    createHexagon: function(color) {
        var points = this.getHexagonPoints(50);
        return new fabric.Polygon(points, {
            fill: color || '#ff0000'
        });
    },

    getHexagonPoints: function(radius) {
        var points = [];
        for (var i = 0; i < 6; i++) {
            points.push({
                x: radius * Math.cos(i * Math.PI / 3),
                y: radius * Math.sin(i * Math.PI / 3)
            });
        }
        return points;
    },

    toJSON: function() {
        return fabric.util.object.extend(this.callSuper('toJSON'), {
            customColor: this.customColor,
            customText: this.customText
        });
    }
});

// Crear desde JSON
fabric.CustomBadge.fromObject = function(object, callback) {
    fabric.CustomBadge.fromObject(object, function(badge) {
        callback(badge);
    });
};

// Registrar clase en fabric
fabric.CustomBadge.async = false;
```

### Paso 3: Integrar con AngularJS

**En app-modern.min.js o en un script adicional:**

```javascript
// Dentro del controlador designCtrl:

$scope.addCustomBadge = function() {
    var badge = new fabric.CustomBadge({
        text: 'Mi Badge',
        color: '#ff0000',
        shape: 'circle',
        textColor: '#ffffff'
    });

    badge.set({
        left: 100,
        top: 100
    });

    var canvas = stages[currentStage].canvas;
    canvas.add(badge);
    canvas.setActiveObject(badge);
    canvas.renderAll();

    // Guardar automáticamente
    $scope.saveDesign();
};

// Listener para cambios en propiedades
$scope.updateCustomBadgeProperty = function(property, value) {
    var obj = stages[currentStage].canvas.getActiveObject();

    if (obj && obj.type === 'custom_badge') {
        if (property === 'color') {
            obj.customColor = value;
            obj.item(0).set({fill: value});
        } else if (property === 'text') {
            obj.customText = value;
            obj.item(1).set({text: value});
        }

        stages[currentStage].canvas.renderAll();
        $scope.saveDesign();
    }
};
```

### Paso 4: Crear Interfaz de Usuario

**En el panel lateral (sidebar.php o equivalente):**

```html
<div ng-show="selectedElementType === 'custom_badge'" class="property-panel">
    <h4>Badge Personalizado</h4>

    <div class="property-group">
        <label>Texto del Badge</label>
        <input type="text"
               ng-model="customBadgeText"
               ng-change="updateCustomBadgeProperty('text', customBadgeText)"
               maxlength="50">
    </div>

    <div class="property-group">
        <label>Color</label>
        <input type="color"
               ng-model="customBadgeColor"
               ng-change="updateCustomBadgeProperty('color', customBadgeColor)">
    </div>

    <div class="property-group">
        <label>Forma</label>
        <select ng-model="customBadgeShape"
                ng-change="updateCustomBadgeProperty('shape', customBadgeShape)">
            <option value="circle">Círculo</option>
            <option value="square">Cuadrado</option>
            <option value="hexagon">Hexágono</option>
        </select>
    </div>
</div>
```

---

## Crear Extensiones de Fabric.js

### Extensión: Texto con Efecto de Sombra Avanzada

```javascript
fabric.TextWithShadow = fabric.util.createClass(fabric.IText, {

    type: 'text_with_shadow',

    initialize: function(text, options) {
        this.callSuper('initialize', text, options);

        this.shadowColor = options.shadowColor || '#000000';
        this.shadowBlur = options.shadowBlur || 4;
        this.shadowOffsetX = options.shadowOffsetX || 2;
        this.shadowOffsetY = options.shadowOffsetY || 2;
    },

    _renderText: function(ctx) {
        // Guardar el shadow original
        var originalShadow = ctx.shadowColor;
        var originalBlur = ctx.shadowBlur;
        var originalOffsetX = ctx.shadowOffsetX;
        var originalOffsetY = ctx.shadowOffsetY;

        // Aplicar sombra personalizada
        ctx.shadowColor = this.shadowColor;
        ctx.shadowBlur = this.shadowBlur;
        ctx.shadowOffsetX = this.shadowOffsetX;
        ctx.shadowOffsetY = this.shadowOffsetY;

        // Llamar a renderizado normal
        this.callSuper('_renderText', ctx);

        // Restaurar shadow
        ctx.shadowColor = originalShadow;
        ctx.shadowBlur = originalBlur;
        ctx.shadowOffsetX = originalOffsetX;
        ctx.shadowOffsetY = originalOffsetY;
    },

    toJSON: function() {
        return fabric.util.object.extend(this.callSuper('toJSON'), {
            shadowColor: this.shadowColor,
            shadowBlur: this.shadowBlur,
            shadowOffsetX: this.shadowOffsetX,
            shadowOffsetY: this.shadowOffsetY
        });
    }
});

fabric.TextWithShadow.fromObject = function(object, callback) {
    callback(new fabric.TextWithShadow(object.text, object));
};
```

### Extensión: Imagen con Marco Personalizado

```javascript
fabric.ImageWithFrame = fabric.util.createClass(fabric.Image, {

    type: 'image_with_frame',

    initialize: function(element, options) {
        this.callSuper('initialize', element, options);

        this.frameColor = options.frameColor || '#cccccc';
        this.frameWidth = options.frameWidth || 10;
        this.shadowFrame = options.shadowFrame || false;
    },

    _render: function(ctx) {
        // Dibujar marco
        ctx.fillStyle = this.frameColor;
        ctx.fillRect(
            -this.frameWidth,
            -this.frameWidth,
            this.width + this.frameWidth * 2,
            this.height + this.frameWidth * 2
        );

        // Dibujar sombra si está habilitada
        if (this.shadowFrame) {
            ctx.shadowColor = 'rgba(0,0,0,0.3)';
            ctx.shadowBlur = 10;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 2;
        }

        // Renderizar imagen
        this.callSuper('_render', ctx);
    },

    toJSON: function() {
        return fabric.util.object.extend(this.callSuper('toJSON'), {
            frameColor: this.frameColor,
            frameWidth: this.frameWidth,
            shadowFrame: this.shadowFrame
        });
    }
});
```

---

## Personalizar la Interfaz

### Añadir Panel Personalizado en Sidebar

**1. Crear archivo de vista:**

```php
// views/modern/my-custom-panel.php
?>
<div class="nbd-sidebar-panel" id="custom-panel">
    <h3><?php esc_html_e('Mi Panel Personalizado', 'mi-plugin'); ?></h3>

    <div class="panel-content">
        <div class="form-group">
            <label><?php esc_html_e('Opción 1'); ?></label>
            <input type="text" id="option1" placeholder="Ingresa valor">
        </div>

        <button class="button button-primary" id="apply-custom">
            <?php esc_html_e('Aplicar', 'mi-plugin'); ?>
        </button>
    </div>
</div>
```

**2. Registrar en el plugin:**

```php
// En tu class-custom-element.php
add_action('nbd_modern_extra_page_toolbar', function() {
    include('views/modern/my-custom-panel.php');
});

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('my-custom-panel',
        plugin_dir_url(__FILE__) . '/assets/js/my-panel.js',
        array('jquery'),
        '1.0.0'
    );
});
```

**3. Crear interactividad:**

```javascript
// assets/js/my-panel.js
jQuery(document).ready(function($) {
    $('#apply-custom').on('click', function() {
        var option1 = $('#option1').val();

        // Hacer algo con el valor
        console.log('Opción 1:', option1);

        // Actualizar canvas
        var canvas = window.nbd_window.stages[0].canvas;
        if (canvas && canvas.getActiveObject()) {
            canvas.getActiveObject().set({
                customProp: option1
            });
            canvas.renderAll();
        }
    });
});
```

### Modificar CSS del Canvas

**archivo: assets/css/custom-styles.css**

```css
/* Cambiar color de grid */
.nbd-stage .stage-grid {
    filter: brightness(0.8);
}

/* Cambiar color de ruler */
.nbd-hoz-ruler svg,
.nbd-ver-ruler svg {
    stroke: #ff0000 !important;
}

/* Cambiar líneas de alineación */
.snapline {
    background-color: #ff0000 !important;
}

/* Cambiar bounding box */
.bounding-rect {
    border-color: #0099ff !important;
}

/* Cambiar panel de propiedades */
.property-panel {
    background-color: #f9f9f9 !important;
}
```

---

## Validación y Restricciones

### Validar en Frontend (AngularJS)

```javascript
// En designCtrl:

$scope.validateDesignBeforeSave = function() {
    var valid = true;
    var errors = [];

    // Validar que haya al menos un elemento
    var canvas = stages[currentStage].canvas;
    if (canvas.getObjects().length === 0) {
        valid = false;
        errors.push('El diseño debe tener al menos un elemento');
    }

    // Validar tamaño de elementos
    canvas.forEachObject(function(obj) {
        if (obj.width < 10 || obj.height < 10) {
            valid = false;
            errors.push('Los elementos deben tener al menos 10x10 píxeles');
        }

        // Validar que esté dentro del área
        if (obj.left < 0 || obj.top < 0) {
            valid = false;
            errors.push('Los elementos no pueden estar fuera del área de diseño');
        }
    });

    if (!valid) {
        alert('Errores de validación:\n' + errors.join('\n'));
        return false;
    }

    return true;
};

// Hook antes de guardar
$scope.saveDesign = function() {
    if (!$scope.validateDesignBeforeSave()) {
        return false;
    }

    // Continuar con guardado
    // ... código original
};
```

### Validar en Backend (PHP)

```php
// includes/api/design.php o equivalent

add_action('wp_ajax_nbd_save_design', function() {
    // Verificar nonce
    if (!isset($_POST['_nonce']) ||
        !wp_verify_nonce($_POST['_nonce'], 'nbd_save_design')) {
        wp_send_json_error('Nonce verification failed');
    }

    // Validar datos
    $design_data = isset($_POST['design_data']) ?
        json_decode(stripslashes($_POST['design_data']), true) : array();

    // Validación personalizada
    if (empty($design_data)) {
        wp_send_json_error('Design data is empty');
    }

    // Validar cada objeto
    foreach ($design_data as $stage) {
        foreach ($stage['objects'] as $obj) {
            // Validar tipo
            if (!in_array($obj['type'], ['image', 'text', 'rect', 'circle', 'path'])) {
                wp_send_json_error('Invalid object type: ' . $obj['type']);
            }

            // Validar propiedades obligatorias
            if (!isset($obj['left']) || !isset($obj['top'])) {
                wp_send_json_error('Object must have left and top properties');
            }
        }
    }

    // Si pasó validación, guardar
    // ... código de guardado

    wp_send_json_success(['message' => 'Design saved successfully']);
});
```

### Restricciones Personalizadas

```javascript
// Limitar número de objetos
$scope.MAX_OBJECTS = 50;

$scope.addElement = function(type) {
    var canvas = stages[currentStage].canvas;

    if (canvas.getObjects().length >= $scope.MAX_OBJECTS) {
        alert('No puedes añadir más de ' + $scope.MAX_OBJECTS + ' elementos');
        return false;
    }

    // Continuar con adición de elemento
};

// Limitar texto
$scope.MAX_TEXT_LENGTH = 200;

fabric.IText.prototype.set = (function(originalSet) {
    return function(prop, value) {
        if (prop === 'text' && value.length > $scope.MAX_TEXT_LENGTH) {
            console.warn('Texto demasiado largo, limitado a ' + $scope.MAX_TEXT_LENGTH + ' caracteres');
            value = value.substring(0, $scope.MAX_TEXT_LENGTH);
        }
        return originalSet.call(this, prop, value);
    };
})(fabric.IText.prototype.set);

// Limitar tamaño de imagen
$scope.MAX_IMAGE_SIZE = 5 * 1024 * 1024;  // 5 MB

$scope.uploadImage = function(file) {
    if (file.size > $scope.MAX_IMAGE_SIZE) {
        alert('Archivo demasiado grande. Máximo 5 MB');
        return false;
    }

    // Continuar con upload
};
```

---

## Debugging y Testing

### Debugging en Frontend

**Usar Chrome DevTools (F12):**

```javascript
// Console tab
console.log('Objetos en canvas:', stages[0].canvas.getObjects());
console.log('Objeto seleccionado:', stages[0].canvas.getActiveObject());
console.log('Datos del diseño:', JSON.stringify(stages[0].canvas.toJSON(), null, 2));

// Breakpoints
// 1. Ir a Source tab
// 2. Ctrl+P y buscar app-modern.min.js
// 3. Hacer clic en número de línea para setear breakpoint
// 4. Interactuar con canvas, va a pausar en breakpoint

// Network tab
// 1. Ir a Network tab
// 2. Hacer clic en elemento del canvas
// 3. Ver POST request a /wp-admin/admin-ajax.php
// 4. Ver response y request payload
```

**Logger personalizado:**

```javascript
// Crear función de debug
window.nbDebug = {
    log: function(message, data) {
        console.log('[NBDesigner] ' + message, data || '');
    },

    dumpCanvas: function(stageIndex) {
        var canvas = stages[stageIndex].canvas;
        console.log('=== Canvas Dump ===');
        console.log('Width:', canvas.width);
        console.log('Height:', canvas.height);
        console.log('Objects:', canvas.getObjects().length);
        console.log('JSON:', canvas.toJSON());
        console.log('===================');
    },

    dumpObject: function(obj) {
        console.log('=== Object Properties ===');
        console.log('Type:', obj.type);
        console.log('Left:', obj.left);
        console.log('Top:', obj.top);
        console.log('Width:', obj.width);
        console.log('Height:', obj.height);
        console.log('Full JSON:', JSON.stringify(obj.toJSON(), null, 2));
        console.log('========================');
    }
};

// Uso:
nbDebug.log('Canvas initialized');
nbDebug.dumpCanvas(0);
```

### Debugging en Backend

**Usar logs de WordPress:**

```php
// wp-config.php debe tener:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// En tu código:
error_log('Información de debug');
error_log(print_r($_POST, true));
error_log(json_encode($design_data));

// Los logs van a: wp-content/debug.log
```

### Testing

**Test unitario (PHP):**

```php
<?php

class Test_Design_Model extends WP_UnitTestCase {

    public function setUp() {
        parent::setUp();
        // Setup para cada test
    }

    public function test_save_design() {
        $design = new NBDesign_Design();
        $design->set_product_id(123);
        $design->set_data(json_encode(['test' => 'data']));

        $result = $design->save_design();

        $this->assertTrue($result);
        $this->assertEquals(123, $design->get_product_id());
    }

    public function test_load_design() {
        $design = new NBDesign_Design();
        $design->set_id(456);

        $loaded = $design->load_design(456);

        $this->assertNotNull($loaded);
        $this->assertEquals(456, $loaded->id);
    }
}
```

**Test funcional (JavaScript):**

```javascript
// Usar framework como Jest o Jasmine

describe('Canvas Initialization', function() {

    it('should create canvas instance', function() {
        var canvas = new fabric.Canvas('test-canvas');
        expect(canvas).toBeDefined();
        expect(canvas.width).toBe(800);
    });

    it('should add object to canvas', function() {
        var canvas = new fabric.Canvas('test-canvas');
        var rect = new fabric.Rect({width: 100, height: 100});

        canvas.add(rect);

        expect(canvas.getObjects().length).toBe(1);
    });

    it('should serialize to JSON', function() {
        var canvas = new fabric.Canvas('test-canvas');
        var text = new fabric.Text('Test');

        canvas.add(text);
        var json = canvas.toJSON();

        expect(json.objects.length).toBe(1);
        expect(json.objects[0].text).toBe('Test');
    });
});
```

---

## Casos de Uso Comunes

### Caso 1: Limitar Colores Disponibles

```javascript
// Define colores permitidos
var ALLOWED_COLORS = [
    '#ff0000',  // Rojo
    '#00ff00',  // Verde
    '#0000ff',  // Azul
    '#ffff00',  // Amarillo
    '#000000',  // Negro
    '#ffffff'   // Blanco
];

// Mostrar solo colores permitidos
$scope.availableColors = ALLOWED_COLORS;

// Validar selección
$scope.setObjectColor = function(color) {
    if (ALLOWED_COLORS.indexOf(color) === -1) {
        alert('Color no permitido');
        return false;
    }

    var obj = stages[currentStage].canvas.getActiveObject();
    if (obj) {
        obj.set({fill: color});
        stages[currentStage].canvas.renderAll();
        $scope.saveDesign();
    }
};
```

### Caso 2: Crear Plantilla Predefinida

```php
// En backend:
$predefined_design = array(
    'stages' => array(
        array(
            'config' => array(
                'width' => 200,
                'height' => 200,
                'bgColor' => '#ffffff'
            ),
            'objects' => array(
                array(
                    'type' => 'text',
                    'text' => 'MI NOMBRE',
                    'left' => 50,
                    'top' => 50,
                    'fontSize' => 24,
                    'fontFamily' => 'Arial',
                    'fill' => '#000000'
                )
            )
        )
    )
);

// Guardar como template
update_option('nbd_default_template', json_encode($predefined_design));
```

```javascript
// En frontend:
$scope.loadDefaultTemplate = function() {
    var template = nbd_window.DEFAULT_TEMPLATE;  // Pasar desde backend

    stages[0].canvas.loadFromJSON(template, function() {
        stages[0].canvas.renderAll();
        $scope.saveDesign();
    });
};
```

### Caso 3: Sincronización en Tiempo Real (Múltiples Usuarios)

```javascript
// Usar WebSocket o polling para sincronización

setInterval(function() {
    // Cada 5 segundos, checkear cambios remotos
    $http.get('/api/design/' + designId + '/latest-version').then(
        function(response) {
            if (response.data.version > currentVersion) {
                // Hay cambios remotos
                $scope.promptUserForUpdate();
            }
        }
    );
}, 5000);

$scope.promptUserForUpdate = function() {
    if (confirm('El diseño ha sido actualizado por otro usuario. ¿Recargar?')) {
        location.reload();
    }
};
```

### Caso 4: Exportar a Múltiples Formatos

```javascript
$scope.exportDesign = function(format) {
    var canvas = stages[0].canvas;

    if (format === 'png') {
        var imageData = canvas.toDataURL({
            format: 'png',
            multiplier: 2
        });
        downloadImage(imageData, 'design.png');

    } else if (format === 'svg') {
        var svgData = canvas.toSVG();
        downloadFile(svgData, 'design.svg', 'image/svg+xml');

    } else if (format === 'json') {
        var jsonData = JSON.stringify(canvas.toJSON());
        downloadFile(jsonData, 'design.json', 'application/json');
    }
};

function downloadImage(dataUrl, filename) {
    var link = document.createElement('a');
    link.href = dataUrl;
    link.download = filename;
    link.click();
}

function downloadFile(data, filename, mimeType) {
    var blob = new Blob([data], {type: mimeType});
    var url = window.URL.createObjectURL(blob);
    var link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.click();
    window.URL.revokeObjectURL(url);
}
```

### Caso 5: Historial de Cambios (Undo/Redo)

```javascript
// Implementar sistema de undo/redo

class DesignHistory {
    constructor() {
        this.history = [];
        this.currentIndex = -1;
    }

    push(state) {
        // Eliminar items después del índice actual
        this.history = this.history.slice(0, this.currentIndex + 1);

        // Añadir nuevo state
        this.history.push(JSON.stringify(state));
        this.currentIndex++;
    }

    undo() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
            return JSON.parse(this.history[this.currentIndex]);
        }
        return null;
    }

    redo() {
        if (this.currentIndex < this.history.length - 1) {
            this.currentIndex++;
            return JSON.parse(this.history[this.currentIndex]);
        }
        return null;
    }

    canUndo() {
        return this.currentIndex > 0;
    }

    canRedo() {
        return this.currentIndex < this.history.length - 1;
    }
}

// Usar en designCtrl:
var designHistory = new DesignHistory();

// Cada vez que se modifica:
canvas.on('object:modified', function() {
    designHistory.push(canvas.toJSON());
});

$scope.undo = function() {
    var state = designHistory.undo();
    if (state) {
        canvas.loadFromJSON(state, function() {
            canvas.renderAll();
        });
    }
};

$scope.redo = function() {
    var state = designHistory.redo();
    if (state) {
        canvas.loadFromJSON(state, function() {
            canvas.renderAll();
        });
    }
};
```

---

## Recursos de Desarrollo

### Documentación de Referencia
- Fabric.js Documentation: http://fabricjs.com/
- AngularJS Documentation: https://docs.angularjs.org/
- WordPress Hooks: https://developer.wordpress.org/plugins/hooks/
- WooCommerce Development: https://github.com/woocommerce/woocommerce/wiki

### Herramientas Recomendadas
- Chrome DevTools (F12)
- VS Code + extensions (Debugger for Chrome, WordPress extension)
- Postman (para testing de API)
- Git (para versionamiento)
- WP-CLI (para operaciones WordPress desde terminal)

### Comunidades
- WordPress Plugin Repository: https://wordpress.org/plugins/
- GitHub: https://github.com/Printcart/web-to-print-online-designer/
- Fabric.js GitHub: https://github.com/fabricjs/fabric.js/

---

**Última actualización:** Noviembre 2025
**Versión del documento:** 1.0
