# 📐 Sistema Canvas - Documentación Completa

## NBDesigner - Web to Print Online Designer

**Versión:** 1.2.1
**Autor:** Printcart / NetBase Team
**Licencia:** GPLv2 o posterior
**Requisitos:** WordPress 4.6+, WooCommerce 3.0+, PHP 5.6+

---

## 📋 Tabla de Contenidos

1. [Resumen Ejecutivo](#resumen-ejecutivo)
2. [Arquitectura General](#arquitectura-general)
3. [Componentes Principales](#componentes-principales)
4. [Flujo de Datos](#flujo-de-datos)
5. [Cómo Funciona el Canvas](#cómo-funciona-el-canvas)
6. [Guía de Uso](#guía-de-uso)
7. [API y Métodos Principales](#api-y-métodos-principales)
8. [Ejemplos Prácticos](#ejemplos-prácticos)
9. [Troubleshooting](#troubleshooting)

---

## Resumen Ejecutivo

NBDesigner es un **plugin de WordPress que proporciona un editor visual tipo canvas** para diseñar productos personalizables (camisetas, tazas, teléfonos, etc.).

### Características Principales:
- ✅ **Editor interactivo basado en Fabric.js** - Canvas HTML5 con soporte para múltiples elementos
- ✅ **Múltiples páginas** - Cada producto puede tener varias caras/lados editables
- ✅ **Sistema de capas** - Panel lateral para organizar elementos
- ✅ **Transformaciones avanzadas** - Rotación, escala, sesgo, espejo, texto curvo
- ✅ **Herramientas de alineación** - Snap/alineación automática y reglas guía
- ✅ **Integración WooCommerce** - Funciona como producto personalizable
- ✅ **Soporte multi-idioma** - Compatible con WPML
- ✅ **Exportación** - Descarga como imagen, PDF o para producción

---

## Arquitectura General

### Stack Tecnológico

```
┌─────────────────────────────────────────────────────────────┐
│                   Frontend (AngularJS 1.6.9)                │
├─────────────────────────────────────────────────────────────┤
│  app-modern.min.js (704 KB)                                 │
│  ├─ designCtrl (Controlador principal)                      │
│  ├─ FabricWindow Factory (Gestor de canvas)                 │
│  ├─ nbd-canvas Directive (Renderizado)                      │
│  └─ nbdLayer Directive (Panel de capas)                     │
├─────────────────────────────────────────────────────────────┤
│                   Canvas Library (Fabric.js 2.6.0)           │
├─────────────────────────────────────────────────────────────┤
│  HTML5 Canvas + SVG                                         │
│  ├─ Renderizado de objetos                                  │
│  ├─ Transformaciones                                        │
│  └─ Eventos interactivos                                    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│               Backend (PHP 5.6+ / WordPress)                │
├─────────────────────────────────────────────────────────────┤
│  class.launcher.php (Controlador principal)                 │
│  class.design.php (Modelo de datos)                         │
│  class.designer.php (Modelo de usuario)                     │
│  └─ API REST endpoints                                      │
├─────────────────────────────────────────────────────────────┤
│              Database (MySQL / WordPress)                    │
└─────────────────────────────────────────────────────────────┘
```

### Estructura de Directorios

```
wp-content/plugins/web-to-print-online-designer/
├── assets/
│   ├── js/
│   │   ├── app-modern.min.js          ⭐ Aplicación principal compilada
│   │   ├── nbdesigner.js              ⭐ Script de integración
│   │   ├── _layout.js                 ⭐ Gestión de UI
│   │   ├── fabric.curvedText.js       - Extensión: texto curvo
│   │   ├── fabric.removeColor.js      - Extensión: remover colores
│   │   └── libs/
│   │       └── fabric.2.6.0.min.js    ⭐ Librería Fabric.js
│   └── css/
│       └── modern/                    - Estilos CSS
├── includes/
│   ├── launcher/
│   │   ├── class.designer.php         ⭐ Modelo Usuario
│   │   ├── class.design.php           ⭐ Modelo Diseño
│   │   ├── class.launcher.php         ⭐ Controlador Principal
│   │   └── api/
│   │       ├── design.php             - API de diseños
│   │       ├── designer.php           - API de usuarios
│   │       └── designer_disabled.php  - Gestión permisos
│   └── class.nbdesigner.php           ⭐ Clase principal
├── views/
│   └── modern/
│       ├── stages.php                 ⭐ Plantilla canvas
│       ├── sidebar.php                - Panel de herramientas
│       ├── main-bar.php               - Barra principal
│       ├── context-menu.php           - Menú contextual
│       └── toolbars/
│           ├── toolbar-text.php
│           ├── toolbar-image.php
│           └── toolbar-*.php
└── nbdesigner.php                     ⭐ Punto de entrada del plugin
```

---

## Componentes Principales

### 1. **Fabric.js (Canvas Core)**

**Ubicación:** `assets/libs/fabric.2.6.0.min.js` (222 KB)

Librería JavaScript que proporciona:
- Renderizado HTML5 Canvas
- Manipulación de objetos 2D (imágenes, texto, formas, paths)
- Transformaciones (translate, rotate, scale, skew)
- Event handling (mouse, touch)
- Serialización JSON
- Soporte para selección y transformación interactiva

**Objetos Soportados:**
- `fabric.Image` - Imágenes rasterizadas
- `fabric.Text` - Texto editable
- `fabric.IText` - Texto con edición inline
- `fabric.Textbox` - Caja de texto multilínea
- `fabric.Rect` - Rectángulos
- `fabric.Circle` - Círculos
- `fabric.Line` - Líneas
- `fabric.Path` - Paths SVG
- `fabric.Polygon` - Polígonos
- `fabric.Triangle` - Triángulos

### 2. **AngularJS Controller: designCtrl**

**Ubicación:** `assets/js/app-modern.min.js`

Controlador principal que maneja:
- `$scope.stages` - Array de páginas/canvas (múltiples páginas)
- `$scope.resource` - Recurso/producto actual
- `$scope.settings` - Configuración global del editor
- `$scope.currentStage` - Página seleccionada

**Métodos Principales:**
```javascript
// Crear/Eliminar elementos
$scope.addElement(type, data)
$scope.deleteElement(index)

// Gestión de páginas
$scope.addStage()                    // Añadir nueva página
$scope.switchStage(index, direction) // Cambiar página

// Guardado y sincronización
$scope.saveDesign()
$scope.loadDesign(designId)

// Transformaciones
$scope.rotateObject(angle)
$scope.scaleObject(scaleX, scaleY)
$scope.flipObject(direction)

// Propiedades
$scope.updateObjectProperty(property, value)
$scope.setObjectColor(color)
$scope.setObjectFont(fontName)

// Zoom
$scope.setZoom(level)

// Selección
$scope.selectObject(object)
$scope.deselectAll()

// Alineación
$scope.alignObjects(direction)
$scope.distributeObjects(direction)
```

### 3. **Factory: FabricWindow**

**Ubicación:** `assets/js/app-modern.min.js`

Wrapper de Fabric.js que:
- Inicializa instancias de `fabric.Canvas`
- Gestiona eventos del canvas
- Proporciona métodos helper para manipulación de objetos
- Sincroniza estado entre vista y controlador

**Métodos Principales:**
```javascript
FabricWindow.createCanvas(elementId, config)
FabricWindow.addObject(canvas, object)
FabricWindow.removeObject(canvas, object)
FabricWindow.selectObject(canvas, object)
FabricWindow.toJSON(canvas)
FabricWindow.fromJSON(canvas, json)
FabricWindow.clearCanvas(canvas)
```

### 4. **Directive: nbd-canvas**

**Ubicación:** `assets/js/app-modern.min.js` + `views/modern/stages.php:36`

```html
<canvas nbd-canvas stage="stage" ctx="ctxMenuStyle" index="{{$index}}"
        id="nbd-stage-{{$index}}" last="{{$last ? 1 : 0}}"></canvas>
```

**Responsabilidades:**
- Crear instancia de `fabric.Canvas`
- Inicializar canvas con configuración del stage
- Cargar objetos desde JSON (loadLayer)
- Manejar eventos de canvas (selection, modification)
- Sincronizar vista con modelo de datos
- Renderizar en tiempo real

**Event Listeners:**
- `object:added` - Cuando se añade un objeto
- `object:removed` - Cuando se elimina un objeto
- `object:modified` - Cuando se modifica un objeto
- `selection:created` - Cuando se selecciona un objeto
- `selection:cleared` - Cuando se deselecciona

### 5. **Directive: nbdLayer**

**Ubicación:** `assets/js/app-modern.min.js` + HTML templates

Panel lateral que muestra:
- Jerarquía de elementos (capas)
- Estado de selección
- Visibilidad de objetos
- Opciones de reorden (drag & drop)

### 6. **Backend: class.design.php**

**Ubicación:** `includes/launcher/class.design.php`

Modelo de datos que:
- Guarda/carga diseños en base de datos
- Serializa objetos de canvas a JSON
- Maneja versiones de diseños
- Proporciona métodos para CRUD

**Métodos Principales:**
```php
class_design->save_design()           // Guardar diseño
class_design->load_design($id)        // Cargar diseño
class_design->get_design_data()       // Obtener datos JSON
class_design->delete_design($id)      // Eliminar diseño
class_design->get_design_history()    // Historial de versiones
```

---

## Flujo de Datos

### Inicialización del Editor

```
1. Usuario accede a producto en WooCommerce
   ↓
2. WordPress carga plugin (nbdesigner.php)
   ↓
3. class.nbdesigner.php inicializa hooks
   ↓
4. class.launcher.php configura la página del editor
   ↓
5. views/modern/index.php renderiza HTML
   ↓
6. app-modern.min.js carga y ejecuta AngularJS
   ↓
7. designCtrl inicializa módulo 'nbd-app'
   ↓
8. Views cargan: stages.php, sidebar.php, main-bar.php
   ↓
9. FabricWindow Factory se inicializa
   ↓
10. nbd-canvas Directive detecta <canvas> elements
    ↓
11. Para cada <canvas>, se crea fabric.Canvas instance
    ↓
12. Se cargan datos del diseño (JSON) desde la BD
    ↓
13. loadLayer() reconstruye objetos:
    - fabric.Image.fromObject()
    - fabric.Text.fromObject()
    - etc.
    ↓
14. canvas.add(_item) añade elementos al canvas
    ↓
15. Listeners de Fabric activados, canvas listo para editar
```

### Flujo de Edición

```
Usuario interactúa con canvas
    ↓
Fabric.js detecta evento (mouse, touch)
    ↓
Event handler de AngularJS se ejecuta
    ↓
designCtrl actualiza $scope
    ↓
Propiedades de objeto se sincronizen
    ↓
$scope.saveDesign() se ejecuta automáticamente
    ↓
AJAX POST a backend (includes/launcher/api/design.php)
    ↓
class.design.php->save_design()
    ↓
JSON del canvas se serializa y guarda en BD
    ↓
Respuesta AJAX confirma guardado
    ↓
Usuario ve confirmación (toast notification)
```

### Guardar Diseño

```
$scope.saveDesign()
    ↓
Serializa canvas: canvas.toJSON()
    ↓
Obtiene propiedades: objeto.left, objeto.top, objeto.width, etc.
    ↓
AJAX POST: {
        action: 'nbd_save_design',
        design_data: JSON.stringify(canvas),
        product_id: productId,
        design_id: designId
    }
    ↓
Backend: includes/launcher/api/design.php
    ↓
class.design->save_design()
    ↓
INSERT/UPDATE en tabla nbd_designs
    ↓
JSON guardado en columna 'design_data'
```

---

## Cómo Funciona el Canvas

### Estructura de una Página (Stage)

```javascript
{
    // Configuración visual
    config: {
        width: 200,              // Ancho del área imprimible (mm)
        height: 200,             // Alto del área imprimible (mm)
        cwidth: 214,             // Ancho incluyendo sangrado
        cheight: 214,            // Alto incluyendo sangrado
        bgColor: '#ffffff',      // Color de fondo
        bgType: 'color',         // Tipo: 'color' o 'image'
        bgImage: 'url(...)',     // Imagen de fondo (si aplica)
        name: 'Page 1',          // Nombre de la página
        bleed_lr: 3,             // Sangrado izq-derecha (mm)
        bleed_tb: 3,             // Sangrado arriba-abajo (mm)
        bleed_radius: 0,         // Radio de esquinas
        margin_lr: 0,            // Margen de seguridad izq-der
        margin_tb: 0             // Margen de seguridad arr-aba
    },

    // Estado dinámico
    states: {
        currentScaleIndex: 0,    // Índice de zoom actual
        scaleRange: [
            {ratio: 0.5},        // 50% zoom
            {ratio: 1.0},        // 100% zoom
            {ratio: 1.5}         // 150% zoom
        ],
        boundingObject: {        // Caja de selección actual
            left: '10px',
            top: '20px',
            width: '100px',
            height: '150px',
            display: 'block'
        },
        rotate: {angle: 0},      // Ángulo de rotación del objeto
        corners: [],             // Esquinas para transformación
        snaplines: {},           // Líneas de alineación visibles
        lostCharLayers: [],      // Caracteres con fuentes no disponibles
        coordinates: {           // Posición actual
            left: '10',
            top: '20'
        }
    },

    // Instancia de Fabric.js
    canvas: fabric.Canvas {},

    // Array de objetos en el canvas
    objects: [
        {
            type: 'image',
            src: 'url(...)',
            left: 50,
            top: 50,
            width: 100,
            height: 100,
            scaleX: 1,
            scaleY: 1,
            angle: 0,
            opacity: 1
        },
        {
            type: 'text',
            text: 'Mi Texto',
            left: 50,
            top: 150,
            fontSize: 20,
            fontFamily: 'Arial',
            fill: '#000000'
        }
    ],

    // Guías de regla
    rulerLines: {
        hors: [],    // Líneas horizontales
        vers: []     // Líneas verticales
    }
}
```

### Objetos Soportados

#### Imagen (fabric.Image)
```javascript
{
    type: 'image',
    src: 'http://example.com/image.jpg',
    left: 10,
    top: 20,
    width: 100,
    height: 80,
    scaleX: 1,
    scaleY: 1,
    angle: 0,
    opacity: 1,
    filters: [],  // Fabric.js filters
    crossOrigin: 'anonymous'
}
```

#### Texto (fabric.IText)
```javascript
{
    type: 'text',
    text: 'Hello World',
    left: 10,
    top: 20,
    fontSize: 20,
    fontFamily: 'Arial',
    fontWeight: 'normal',
    fontStyle: 'normal',
    fill: '#000000',
    angle: 0,
    scaleX: 1,
    scaleY: 1,
    lineHeight: 1.16,
    textAlign: 'left',
    shadow: null
}
```

#### Forma (fabric.Rect, Circle, etc.)
```javascript
{
    type: 'rect',  // o 'circle', 'triangle', etc.
    left: 10,
    top: 20,
    width: 100,
    height: 100,
    fill: '#ff0000',
    stroke: '#000000',
    strokeWidth: 2,
    angle: 0,
    opacity: 1
}
```

#### Código QR
```javascript
{
    type: 'qrcode',
    text: 'http://example.com',
    left: 10,
    top: 20,
    width: 100,
    height: 100,
    fill: '#000000'
}
```

---

## Guía de Uso

### Para Usuarios (Clientes)

#### 1. Abrir el Editor
```
1. En la página del producto, hacer clic en "Personalizar" o "Diseña el tuyo"
2. Se abrirá el editor interactivo
3. Esperando a que cargue (puede tomar 3-5 segundos)
```

#### 2. Añadir Elementos
```
Panel Izquierdo: Herramientas

Texto:
├─ Hacer clic en "Añadir Texto"
├─ Escribir el texto en el canvas
├─ Modificar propiedades en el panel derecho

Imagen:
├─ Hacer clic en "Añadir Imagen"
├─ Subir archivo o seleccionar de biblioteca
├─ Posicionar en el canvas

Forma:
├─ Hacer clic en "Formas"
├─ Seleccionar forma (rect, círculo, etc.)
└─ Hacer clic en el canvas para crear
```

#### 3. Editar Elementos
```
Seleccionar:
├─ Hacer clic en el elemento en el canvas
└─ Aparece bounding box con esquinas

Transformar:
├─ Arrastra para mover
├─ Tira de esquinas para redimensionar
├─ Gira desde la esquina superior para rotar
└─ Usa propiedades del panel derecho

Propiedades:
├─ Color: Selector de colores
├─ Tamaño: Ancho y alto
├─ Posición: Coordenadas X, Y
├─ Rotación: Ángulo en grados
└─ Opacidad: Transparencia (0-100%)
```

#### 4. Múltiples Páginas
```
Si el producto tiene múltiples lados (ej. frente/atrás):
├─ Flechas en la barra superior para cambiar página
├─ Panel inferior muestra número de página
└─ Cada página se edita independientemente
```

#### 5. Guardar y Descargar
```
Guardar (automático):
├─ Cada cambio se guarda automáticamente
├─ Toast notification confirma guardado
└─ Nube de sincronización indica estado

Descargar:
├─ Menú principal: "Descargar"
├─ Seleccionar formato (PNG, PDF, etc.)
└─ El archivo se descarga al navegador
```

### Para Administradores (WordPress)

#### Configuración del Plugin

**Ubicación:** WordPress Admin → NBDesigner

```
Configuración General:
├─ Habilitar/deshabilitar funcionalidades
├─ Configurar tamaños de imagen
├─ Seleccionar tema (moderno/clásico)
└─ Idioma

Restricciones:
├─ Limitar número de caracteres
├─ Limitar tamaño de archivo
├─ Controlar herramientas disponibles
└─ Restricciones de color

Fuentes:
├─ Subir fuentes personalizadas
├─ Gestionar fuentes disponibles
└─ Establecer fuente por defecto
```

#### Asignar Canvas a Producto

```
1. Editar producto en WooCommerce
2. Buscar sección "NBDesigner"
3. Seleccionar "Habilitar diseño personalizado"
4. Configurar:
   ├─ Plantilla de diseño
   ├─ Número de páginas
   ├─ Restricciones
   └─ Precios adicionales por personalización
5. Guardar producto
```

---

## API y Métodos Principales

### JavaScript API (Frontend)

#### Canvas Control

```javascript
// Acceder a canvas actual
var canvas = window.nbd_window.stages[currentStageIndex].canvas;

// Obtener objeto seleccionado
var activeObject = canvas.getActiveObject();

// Seleccionar objeto
canvas.setActiveObject(fabricObject);
canvas.renderAll();

// Deseleccionar todo
canvas.discardActiveObject();
canvas.renderAll();

// Serializar a JSON
var json = canvas.toJSON();

// Cargar desde JSON
canvas.loadFromJSON(json, function() {
    canvas.renderAll();
});

// Limpiar canvas
canvas.clear();

// Obtener todos los objetos
var objects = canvas.getObjects();

// Obtener objeto por índice
var obj = canvas.item(0);

// Contar objetos
var count = canvas.getObjects().length;
```

#### Manipulación de Objetos

```javascript
var obj = canvas.getActiveObject();

// Posición
obj.set({left: 100, top: 200});

// Tamaño
obj.set({width: 200, height: 150});
obj.scaleToWidth(200);
obj.scaleToHeight(150);

// Rotación
obj.set({angle: 45});

// Color (text y shapes)
obj.set({fill: '#ff0000'});
obj.set({stroke: '#000000'});
obj.set({strokeWidth: 2});

// Opacidad
obj.set({opacity: 0.5});

// Texto específicamente
if (obj.type === 'text') {
    obj.set({
        text: 'Nuevo texto',
        fontSize: 20,
        fontFamily: 'Arial',
        fill: '#000000'
    });
}

// Renderizar cambios
canvas.renderAll();
```

#### Utilidades

```javascript
// Convertir a imagen
var imageData = canvas.toDataURL('image/png');

// Copiar seleccionado
var jsonSelected = JSON.stringify(canvas.getActiveObject().toJSON());

// Duplicar objeto
var cloned = fabric.util.object.clone(canvas.getActiveObject());
cloned.set({left: cloned.left + 10, top: cloned.top + 10});
canvas.add(cloned);
canvas.renderAll();

// Alineación
canvas.alignCenterH();        // Centro horizontal
canvas.alignCenterV();        // Centro vertical
canvas.alignLeft();
canvas.alignRight();
canvas.alignTop();
canvas.alignBottom();

// Distribución
canvas.distributeObjectsHorizontally();
canvas.distributeObjectsVertically();

// Undo/Redo (si está implementado)
$scope.undo();
$scope.redo();
```

### PHP API (Backend)

#### Guardar Diseño

```php
require_once('class.design.php');

$design = new NBDesign_Design();
$design->set_id($design_id);
$design->set_product_id($product_id);
$design->set_user_id($user_id);
$design->set_title('Mi Diseño');
$design->set_data(json_encode($canvas_data));

$saved = $design->save_design();

if ($saved) {
    wp_send_json_success(['message' => 'Diseño guardado']);
} else {
    wp_send_json_error(['message' => 'Error al guardar']);
}
```

#### Cargar Diseño

```php
$design = new NBDesign_Design();
$design_data = $design->load_design($design_id);

if ($design_data) {
    $canvas_json = json_decode($design_data->design_data, true);
    wp_send_json_success($canvas_json);
} else {
    wp_send_json_error(['message' => 'Diseño no encontrado']);
}
```

#### Listar Diseños de Usuario

```php
$designer = new NBDesign_Designer();
$designs = $designer->get_user_designs($user_id);

foreach ($designs as $design) {
    echo $design->title . ' - ' . $design->created_at;
}
```

#### Eliminar Diseño

```php
$design = new NBDesign_Design();
$deleted = $design->delete_design($design_id);

if ($deleted) {
    wp_send_json_success();
} else {
    wp_send_json_error(['message' => 'No se pudo eliminar']);
}
```

### Hooks y Filtros (WordPress)

#### Hooks de Acción

```php
// Después de cargar el plugin
add_action('nbd_loaded', function() {
    // Tu código aquí
});

// Antes de renderizar stage
add_action('nbd_modern_before_stage', function() {
    // Tu código aquí
});

// Después de cargar canvas
add_action('nbd_modern_after_design_wrap', function() {
    // Tu código aquí
});

// Extra toolbar de página
add_action('nbd_modern_extra_page_toolbar', function() {
    // Tu código aquí
});
```

#### Filtros

```php
// Filtrar canvas HTML
add_filter('nbd_canvas_html', function($html, $stage) {
    // Modificar HTML
    return $html;
}, 10, 2);

// Filtrar datos del diseño antes de guardar
add_filter('nbd_design_data_before_save', function($data) {
    // Modificar datos
    return $data;
});
```

---

## Ejemplos Prácticos

### Ejemplo 1: Añadir Texto Programáticamente

```javascript
// En el contexto de la aplicación AngularJS
var canvas = stages[0].canvas;

var text = new fabric.Text('Hola Mundo', {
    left: 50,
    top: 50,
    fontSize: 30,
    fontFamily: 'Arial',
    fill: '#000000',
    editable: true
});

canvas.add(text);
canvas.setActiveObject(text);
canvas.renderAll();
```

### Ejemplo 2: Añadir Imagen con URL

```javascript
fabric.Image.fromURL('https://example.com/image.jpg', function(img) {
    img.set({
        left: 100,
        top: 100,
        width: 200,
        height: 150
    });

    canvas.add(img);
    canvas.renderAll();
});
```

### Ejemplo 3: Exportar Canvas a PNG

```javascript
// JavaScript
var imageData = canvas.toDataURL({
    format: 'png',
    quality: 1,
    multiplier: 2  // Para mejor resolución
});

// Descargar
var link = document.createElement('a');
link.href = imageData;
link.download = 'design.png';
link.click();
```

### Ejemplo 4: Sincronizar Cambios en Tiempo Real

```javascript
// El sistema ya lo hace automáticamente, pero aquí está el flujo:

canvas.on('object:modified', function() {
    // Se ejecuta cada vez que un objeto se modifica
    $scope.$apply(function() {
        $scope.saveDesign();  // Guarda automáticamente
    });
});

// O manualmente:
$scope.saveDesign = function() {
    var designData = {
        stages: $scope.stages,
        resource: $scope.resource
    };

    $http.post(ajaxurl, {
        action: 'nbd_save_design',
        design_data: JSON.stringify(designData)
    }).then(function(response) {
        console.log('Guardado exitosamente');
    });
};
```

### Ejemplo 5: Crear Plugin Personalizado

```php
<?php
/**
 * Plugin Name: Extensión NBDesigner
 * Description: Personalizaciones del canvas
 */

add_action('nbd_loaded', function() {
    // Tu código aquí
});

// Filtrar elementos disponibles
add_filter('nbd_available_elements', function($elements) {
    $elements['custom'] = 'Elemento Personalizado';
    return $elements;
});

// Guardar datos adicionales
add_filter('nbd_design_data_before_save', function($data) {
    $data['custom_field'] = 'valor';
    return $data;
});

// Validar datos antes de guardar
add_filter('nbd_validate_design_data', function($valid, $data) {
    if (empty($data['title'])) {
        return false;  // Rechazar si no hay título
    }
    return true;
}, 10, 2);
```

---

## Troubleshooting

### Problema: Canvas no carga

**Causas Comunes:**
1. JavaScript no se carga correctamente
2. jQuery no está disponible
3. AngularJS falla silenciosamente
4. Conflicto con otro plugin

**Solución:**
```javascript
// En la consola del navegador (F12)
console.log(angular);              // Verificar AngularJS
console.log(fabric);               // Verificar Fabric.js
console.log(window.nbd_window);    // Verificar variables globales

// Limpiar cache
// Ctrl+Shift+Delete (Firefox) o Ctrl+Shift+R (Chrome)
```

### Problema: Los cambios no se guardan

**Causas Comunes:**
1. Permisos insuficientes
2. Erro de AJAX en el backend
3. Nonce inválido
4. Session expirada

**Solución:**
```javascript
// Verificar en consola (F12)
// Buscar errores en Network tab
// POST a /wp-admin/admin-ajax.php debe retornar 200

// Verificar permisos en backend
if (current_user_can('edit_posts')) {
    // Usuario tiene permisos
}

// Verificar nonce
$nonce = wp_create_nonce('nbd_save_design');
// En frontend debe incluir: _nonce: nonce_value
```

### Problema: Objetos de más textos se ven borrosos

**Causas Comunes:**
1. Fuente no disponible
2. Renderizado de canvas con baja resolución
3. Zoom muy bajo

**Solución:**
```javascript
// Asegurar fuente está disponible
// En backend: uploads/nbdesigner/fonts/

// Aumentar resolución de exportación
var imageData = canvas.toDataURL({
    format: 'png',
    quality: 1,
    multiplier: 3  // Aumentar este valor
});

// Aumentar zoom
$scope.setZoom(1.5);  // 150%
```

### Problema: Canvas muy lento

**Causas Comunes:**
1. Muchos objetos en el canvas (> 100)
2. Imágenes muy grandes sin optimizar
3. Filtros de canvas activos
4. Renders excesivos

**Solución:**
```javascript
// Deshabilitar renderizado automático
canvas.renderOnAddRemove = false;

// Hacer cambios
canvas.add(obj1);
canvas.add(obj2);
canvas.add(obj3);

// Renderizar una sola vez
canvas.renderAll();

// Optimizar imágenes
fabric.Image.fromURL(url, function(img) {
    img.scaleToWidth(200);  // No cargar a tamaño original
    canvas.add(img);
});
```

### Problema: Errores de permisos CORS

**Causas Comunes:**
1. Imagen de otro dominio sin CORS headers
2. Servidor no permite acceso cross-origin

**Solución:**
```javascript
// En backend, añadir headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

// En frontend, usar crossOrigin
fabric.Image.fromURL(url, function(img) {
    img.set({crossOrigin: 'anonymous'});
    canvas.add(img);
}, {crossOrigin: 'anonymous'});
```

### Problema: Conflictos con otros plugins

**Diagnóstico:**
```javascript
// Verificar conflictos jQuery
console.log($);  // Verificar $ está disponible
console.log(jQuery.fn.jquery);  // Versión jQuery

// Deshabilitar otros plugins temporalmente
// WordPress Admin → Plugins → Deshabilitar todos
// Reactivar de uno en uno para encontrar conflicto
```

---

## Checklist de Verificación del Sistema

```
✓ Canvas se carga sin errores
✓ Fabric.js se inicializa correctamente
✓ AngularJS genera módulo 'nbd-app'
✓ Directiva nbd-canvas crea instancias de fabric.Canvas
✓ Se pueden añadir elementos (texto, imagen, formas)
✓ Se pueden editar propiedades (color, tamaño, posición)
✓ Transformaciones funcionan (rotate, scale, flip)
✓ Múltiples páginas funcionan correctamente
✓ Panel de capas (layers) actualiza en tiempo real
✓ Guardado automático funciona
✓ Exportación a PNG/PDF funciona
✓ Responsive design funciona en móvil
✓ Sin errores en consola del navegador (F12)
✓ Sin errores en logs de PHP
✓ Permisos de usuario correcto
✓ Base de datos guarda datos correctamente
```

---

## Recursos Adicionales

### Documentación Oficial
- **Fabric.js:** http://fabricjs.com/docs/
- **AngularJS:** https://angularjs.org/
- **WordPress Plugin Dev:** https://developer.wordpress.org/plugins/
- **WooCommerce:** https://github.com/woocommerce/woocommerce/wiki

### Archivos Clave del Proyecto
- Plugin principal: `/includes/class.nbdesigner.php`
- Controlador: `/includes/launcher/class.launcher.php`
- Modelo de datos: `/includes/launcher/class.design.php`
- Plantilla HTML: `/views/modern/stages.php`
- Aplicación AngularJS: `/assets/js/app-modern.min.js`
- Librería Fabric: `/assets/libs/fabric.2.6.0.min.js`

### Contacto
- **Autor:** Printcart / NetBase Team
- **Sitio:** https://printcart.com
- **Soporte:** support@printcart.com

---

**Última actualización:** Noviembre 2025
**Versión del documento:** 1.0
