# ⚡ Quick Start - Sistema de Diseño Inter Graphics Print

Guía rápida para empezar a usar el nuevo sistema de diseño profesional.

---

## 🚀 5 Minutos - Lo Esencial

### 1. Entender los Colores

```css
/* En style.css :root */
--color-primary: #000000      /* Negro */
--color-secondary: #DC143C    /* Rojo */
--color-white: #FFFFFF        /* Blanco */
```

### 2. Usar Colores en HTML

```html
<!-- Botón rojo -->
<button class="btn btn-primary">Haz clic</button>

<!-- Texto blanco sobre fondo negro -->
<div class="bg-primary text-white p-lg">
  Contenido importante
</div>

<!-- Alerta roja -->
<div class="alert alert-error">Error</div>
```

### 3. Responsive Grid

```html
<!-- Columnas responsivas -->
<div class="container">
  <div class="row gap-md">
    <div class="col-12 col-md-6 col-lg-4">Columna 1</div>
    <div class="col-12 col-md-6 col-lg-4">Columna 2</div>
    <div class="col-12 col-md-6 col-lg-4">Columna 3</div>
  </div>
</div>
```

### 4. Espaciado

```html
<!-- Utilidades de padding/margin -->
<div class="p-lg mt-xl mb-sm">Contenido con espaciado</div>
<!-- p-lg: padding grande, mt-xl: margin-top extra-large, mb-sm: margin-bottom pequeño -->
```

### 5. Notificaciones JavaScript

```javascript
// Mostrar notificación
IGP.showNotification('¡Guardado!', 'success');
IGP.showNotification('Error', 'error');
IGP.showNotification('Información', 'info');
```

---

## 📚 15 Minutos - Lo Importante

### Clases de Botones

```html
<!-- Tamaños -->
<button class="btn btn-sm">Pequeño</button>
<button class="btn">Normal</button>
<button class="btn btn-lg">Grande</button>

<!-- Estilos -->
<button class="btn btn-primary">Primario (Rojo)</button>
<button class="btn btn-secondary">Secundario</button>
<button class="btn btn-success">Éxito</button>
<button class="btn btn-danger">Peligro</button>
<button class="btn btn-warning">Alerta</button>
```

### Cards

```html
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Título</h3>
  </div>
  <div class="card-body">
    Contenido de la tarjeta
  </div>
  <div class="card-footer">
    Pie de página
  </div>
</div>
```

### Alertas

```html
<div class="alert alert-success">
  <span>✓ Operación exitosa</span>
  <button class="alert-close">&times;</button>
</div>

<div class="alert alert-error">
  <span>✗ Ha ocurrido un error</span>
  <button class="alert-close">&times;</button>
</div>
```

### Flexbox

```html
<div class="flex justify-between items-center gap-lg">
  <span>Izquierda</span>
  <span>Centro</span>
  <span>Derecha</span>
</div>

<div class="flex flex-col gap-md">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>
```

---

## 🎨 Personalización

### Cambiar Color Secundario (Rojo)

```css
/* En wp-content/themes/intergraphicsprint/style.css */

:root {
  /* Cambiar este línea */
  --color-secondary: #DC143C;        /* ← Cambiar a otro color */
  --color-secondary-dark: #B91C1C;   /* ← Y estos también */
  --color-secondary-light: #EF4444;
}
```

Todos los botones, alertas y elementos que usan `var(--color-secondary)` cambiarán automáticamente.

### Añadir Nueva Clase

```css
/* En wp-content/themes/intergraphicsprint/assets/css/components.css */

.btn-custom {
  background-color: var(--color-secondary);
  color: var(--color-white);
  padding: 1rem 2rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  transition: all var(--transition-base);
  cursor: pointer;
}

.btn-custom:hover {
  background-color: var(--color-secondary-dark);
  transform: translateY(-2px);
}
```

### Usar en HTML

```html
<button class="btn-custom">Mi botón personalizado</button>
```

---

## 📱 Responsive - Ejemplos

### Breakpoints

```
Móvil:        320px - 767px   (sin sufijo)
Tablet:       768px - 1023px  (-md)
Desktop:      1024px - 1279px (-lg)
Large Desktop: 1280px+        (-xl)
```

### Grid Responsivo

```html
<!-- Móvil: 100% | Tablet: 50% | Desktop: 33.3% | XL: 25% -->
<div class="col-12 col-md-6 col-lg-4 col-xl-3">
  Contenido
</div>
```

### Clases Responsivas

```html
<!-- Ocultar en móvil, mostrar en desktop -->
<div class="hide-sm show-lg">
  Solo visible en desktop
</div>

<!-- Diferentes tamaño de padding por breakpoint -->
<div class="p-sm p-md-md p-lg-lg">
  Móvil: padding pequeño
  Tablet: padding mediano
  Desktop: padding grande
</div>
```

---

## 🔧 Código Común

### Tarjeta de Producto

```html
<div class="card">
  <div class="card-body">
    <img src="producto.jpg" alt="Producto" style="width: 100%; margin-bottom: 1rem;">
    <h3 class="card-title">Nombre del Producto</h3>
    <p>Descripción del producto</p>
    <div class="flex justify-between items-center">
      <span class="text-secondary font-bold">$99.99</span>
      <button class="btn btn-primary btn-sm">Añadir</button>
    </div>
  </div>
</div>
```

### Hero Section

```html
<div class="bg-primary text-white py-3xl">
  <div class="container">
    <h1>Bienvenido a Inter Graphics Print</h1>
    <p class="lead">Diseña tu producto personalizado</p>
    <button class="btn btn-primary">Empezar</button>
  </div>
</div>
```

### Header Simple

```html
<header id="header-outer" class="bg-white border-bottom">
  <div class="container flex justify-between items-center py-md">
    <h1 style="margin: 0;">Logo</h1>
    <nav class="flex gap-lg">
      <a href="#" class="text-primary hover:text-secondary">Home</a>
      <a href="#" class="text-primary hover:text-secondary">Productos</a>
      <a href="#" class="text-primary hover:text-secondary">Contacto</a>
    </nav>
  </div>
</header>
```

### Footer

```html
<footer class="bg-primary text-white py-3xl">
  <div class="container">
    <div class="row gap-lg">
      <div class="col col-md-6 col-lg-3">
        <h3 class="text-white">Empresa</h3>
        <ul class="list-unstyled">
          <li><a href="#" class="text-gray-300 hover:text-secondary">Sobre nosotros</a></li>
          <li><a href="#" class="text-gray-300 hover:text-secondary">Blog</a></li>
        </ul>
      </div>
      <!-- Más columnas aquí -->
    </div>
    <div class="footer-bottom">
      <p>&copy; <span>[current_year]</span> Inter Graphics Print. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>
```

---

## 📝 Utilidades Comunes

### Espaciado

```html
<!-- Margin -->
.m-0 .m-xs .m-sm .m-md .m-lg .m-xl .m-2xl .m-3xl
.mt-* .mb-* .ml-* .mr-* .mx-auto .my-auto

<!-- Padding -->
.p-0 .p-xs .p-sm .p-md .p-lg .p-xl .p-2xl .p-3xl
.px-* .py-*

<!-- Ej: .p-lg m-md mt-xl -->
```

### Flexbox

```html
<!-- Dirección -->
.flex-row .flex-row-reverse .flex-col .flex-col-reverse

<!-- Justificación -->
.justify-start .justify-end .justify-center .justify-between .justify-around

<!-- Alineación -->
.items-start .items-end .items-center .items-baseline .items-stretch

<!-- Gap (espacio entre items) -->
.gap-0 .gap-xs .gap-sm .gap-md .gap-lg .gap-xl .gap-2xl .gap-3xl
```

### Tamaño

```html
<!-- Ancho -->
.w-full .w-auto .w-1-2 .w-1-3 .w-2-3 .w-1-4 .w-3-4

<!-- Alto -->
.h-full .h-auto .h-screen

<!-- Mínimo/Máximo -->
.min-h-full .min-h-screen .max-w-full
```

### Texto

```html
<!-- Alineación -->
.text-left .text-center .text-right

<!-- Estilo -->
.font-light .font-normal .font-medium .font-semibold .font-bold

<!-- Tamaño -->
.small .tiny

<!-- Transformación -->
.uppercase .lowercase .capitalize

<!-- Truncado -->
.truncate .line-clamp-2 .line-clamp-3
```

---

## 🎯 Checklist de Verificación

Antes de publicar:

- [ ] Colores institucionales aplicados (Negro, Rojo, Blanco)
- [ ] Botones tienen estados hover
- [ ] Formularios tienen validación
- [ ] Alertas se cierran correctamente
- [ ] Cards se ven bien en móvil
- [ ] Navigation responsive
- [ ] Footer con información completa
- [ ] Enlace [current_year] en footer
- [ ] Sin errores en consola (F12)
- [ ] Performance acceptable (< 3s carga)

---

## 🆘 Troubleshooting Rápido

### Problema: Color no cambia

**Solución:**
1. Verifica estar editando `:root` en `style.css`
2. Vacía caché del navegador (Ctrl+Shift+Delete)
3. Recarga la página (Ctrl+F5)

### Problema: Responsive no funciona

**Solución:**
1. Verifica tener `<meta name="viewport" content="width=device-width, initial-scale=1.0">`
2. En mobile: abre DevTools (F12), toggle device toolbar
3. Verifica breakpoints: 768px (tablet), 1024px (desktop)

### Problema: Botón no se ve

**Solución:**
1. Verifica tener la clase `.btn`
2. Verifica tener una variante como `.btn-primary`
3. Verifica no tener `display: none` en CSS

### Problema: Spacing no funciona

**Solución:**
1. Usa `.p-md` (no `.padding-md`)
2. Usa `.m-lg` (no `.margin-lg`)
3. Verifica estar usando clases de utilidad correctas

---

## 📖 Documentación Completa

Para más detalles, ver:
- **REFACTORING_NOTES.md** - Documentación técnica completa
- **style.css** - Variables CSS y estilos base
- **assets/css/components.css** - Todos los componentes
- **assets/css/layout.css** - Sistema de grid

---

## 💬 Preguntas Frecuentes

**P: ¿Puedo cambiar las fuentes?**
R: Sí, edita `@import` en `typography.css`

**P: ¿Cómo hago una sección full-width?**
R: Usa `.w-screen` o `.max-w-none`

**P: ¿Cómo centro contenido?**
R: Usa `.flex justify-center items-center` o `.text-center`

**P: ¿Puedo usar otras fuentes de Google?**
R: Sí, añade `@import` en `typography.css` y actualiza `--font-primary`

---

**¡Listo para comenzar!** 🚀

Para preguntas más detalladas, consulta REFACTORING_NOTES.md
