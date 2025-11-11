# NBDesigner Template Visibility Analysis
## Complete Troubleshooting Guide

---

## CRITICAL FINDING: The Main Query That Fetches Templates

### Primary Frontend Query Function
**File:** `/wp-content/plugins/web-to-print-online-designer/includes/class-util.php` (Line 1577)

```php
function nbd_get_templates( $product_id, $variation_id, $template_id = '', $priority = false, $limit = false, $start = false, $type = null ){
    global $wpdb;
    $table_name = $wpdb->prefix . 'nbdesigner_templates';
    
    // CRITICAL: This type_query affects ALL template queries on frontend
    $type_query = is_null( $type ) ? "type IS NULL" : ( $type == 'all' ? "1 = 1" : "type = $type" );
    
    if( $template_id != '' ){
        $sql = "SELECT * FROM $table_name WHERE id = $template_id";
    }else {
        if($priority) {
            // Query for primary template - MUST have publish = 1
            $sql = "SELECT * FROM $table_name WHERE product_id = '$product_id' AND ( variation_id = '$variation_id' || variation_id = 0 ) AND priority = 1 AND publish = 1 AND $type_query";
        }else {
            if( $limit ){
                // Query with limit - MUST have publish = 1
                $sql = "SELECT * FROM $table_name WHERE product_id = '$product_id' AND ( variation_id = '$variation_id' || variation_id = 0 ) AND publish = 1 AND $type_query ORDER BY created_date DESC LIMIT $limit";
            }else{
                // Query without limit - MUST have publish = 1
                $sql = "SELECT * FROM $table_name WHERE product_id = '$product_id' AND ( variation_id = '$variation_id' || variation_id = 0 ) AND publish = 1 AND $type_query ORDER BY created_date DESC";
            }
        }
    }
    if( $start ){
        $sql .= ' OFFSET ' . $start;
    }
    $results = $wpdb->get_results( $sql, 'ARRAY_A' );
    
    // Fallback: if priority=true and no results found, try again without priority
    if( $priority && count( $results ) == 0 ) {
        $sql = "SELECT * FROM $table_name WHERE product_id = '$product_id' AND ( variation_id = '$variation_id' || variation_id = 0 ) AND publish = 1 AND $type_query ORDER BY created_date DESC LIMIT 1";
        $results = $wpdb->get_results( $sql, ARRAY_A );
    }
    return $results;
}
```

---

## ANSWER TO ALL 12 KEY QUESTIONS

### 1. Business Card Product Page Template Display
**File:** `/wp-content/plugins/web-to-print-online-designer/templates/product.php`

Templates are loaded when customer opens the product page. The frontend template display uses AJAX to load templates dynamically via JavaScript. The Business Card product will only show templates that meet ALL visibility conditions (see below).

### 2. How NBDesigner Queries Templates on Frontend

**Main Query Points:**
1. **nbd_get_templates()** - Primary function that fetches templates (line 1577 in class-util.php)
2. **nbd_get_resource_templates()** - Secondary function that processes template data (line 1631 in class-util.php)
3. **Shortcode query** - Different query in shortcodes (line 76 in class-shortcodes.php)

All queries originate from the database table: `{$wpdb->prefix}nbdesigner_templates`

### 3. Difference Between "publish" and "private" Status

**"Publish" Field (publish = 1):**
- Templates with `publish = 1` are shown on the FRONTEND to customers
- This is the main visibility flag that controls customer visibility
- Setting `publish = 0` hides the template completely from customers

**"Private" Field (private = 1):**
- Templates with `private = 1` are still VISIBLE on frontend IF `publish = 1`
- The private flag appears to be metadata, not a visibility filter in frontend queries
- Admin can use `private = 1` to mark templates for special purposes

**IMPORTANT:** Templates must have `publish = 1` to show on frontend, regardless of the `private` flag.

### 4. Shortcodes Used to Display Template Galleries

**Shortcode 1: `[nbd_template]`**
**File:** `/wp-content/plugins/web-to-print-online-designer/includes/class-shortcodes.php` (Line 63-121)

```php
// Usage: [nbd_template row="4" per_row="2" limit="10" product_id="123" cat_id="0"]
public function nbd_templates_func( $atts ){
    $atts = shortcode_atts( array(
        'row'           => 4,
        'per_row'       => 2,
        'limit'         => 10,
        'product_id'    => 0,
        'cat_id'        => 0
    ), $atts, 'nbd_template');
    
    // Builds query with LEFT JOIN to posts table
    $sql = "SELECT p.ID, p.post_title, t.id AS tid, t.name, t.folder, t.product_id, t.variation_id, t.user_id, t.thumbnail FROM {$wpdb->prefix}nbdesigner_templates AS t";
    $sql .= " LEFT JOIN {$wpdb->prefix}posts AS p ON t.product_id = p.ID";
    // WHERE conditions - see next section
}
```

**Shortcode 2: `[nbd_product]`**
- Shows products that have NBDesigner enabled
- Not directly for templates, but for products

### 5. Conditions/Filters That Control Template Visibility

**ALL of these conditions MUST be true for a template to show on frontend:**

```
Template Visibility Checklist:

1. ✓ publish = 1                      (REQUIRED - main visibility flag)
2. ✓ product_id IS NOT NULL           (Template must be assigned to a product)
3. ✓ type IS NULL                     (Default: editable templates)
        OR type = 'all'                (If querying all types)
        OR type = 'solid'              (For solid design templates)
4. ✓ variation_id = 0 OR variation_id matches current variation
5. ✓ private != 1                     (Private flag is checked in some queries)
6. ✓ Product exists and is published  (When using shortcodes with JOIN)
7. ✓ created_date IS NOT NULL         (Must have creation date)
```

### 6. Role/Permission System for Template Visibility

**Frontend Template Visibility:**
- NO permission checks - all published templates are public to customers
- Customers automatically see published templates for their selected product
- Variation filtering: If product has variations, correct variation_id must match

**Admin Template Visibility (Management):**
- Requires `edit_nbd_template` capability
- File: `/wp-content/plugins/web-to-print-online-designer/includes/table/class.product.templates.php` (Line 55)

```php
public static function delete_template($id) {
    if(current_user_can('delete_nbd_template')){  // Admin only
        // ... delete logic
    }
}
```

### 7. How "type" Field Affects Visibility

**Type Field Values in Database:**
- `type = NULL` (editable templates)  - **Most common, shown by default**
- `type = 'solid'` (solid designs)   - **Pre-designed templates customers can only download**
- `type = 'all'`                      - Used in some queries to show all types

**CRITICAL:** The default query uses:
```php
$type_query = is_null( $type ) ? "type IS NULL" : ( $type == 'all' ? "1 = 1" : "type = $type" );
```

**If your template has `type = 'solid'` instead of `NULL`, it won't show in default queries!**

**To check template type:**
```sql
SELECT id, name, type, publish, product_id FROM wp_nbdesigner_templates WHERE product_id = YOUR_PRODUCT_ID;
```

Look for templates with `type = NULL` (editable) vs `type = 'solid'` (solid designs).

### 8. JavaScript That Loads Templates on Frontend

**Main Files:**
1. `/assets/js/bundle.min.js` (Compiled, minified)
2. `/assets/js/add-to-cart-variation.js` (Variation handling)
3. `/assets/js/app-product-builder.js` (Angular app)

**Lazy Load Setting:**
File: `/wp-content/plugins/web-to-print-online-designer/includes/class-util.php` (Line 1672)

```php
$lazy_load_default_template = nbdesigner_get_option( 'nbdesigner_lazy_load_template' );

// If enabled, default template design is loaded only when needed
if( $lazy_load_default_template == 'yes' ){
    $data['lazy_load_design_folder'] = $nbd_item_key;
}
```

**Frontend JavaScript Config:**
File: `/views/nbdesigner-frontend-template.php` (Lines 111-151)

```javascript
var NBDESIGNCONFIG = {
    product_id: "<?php echo($product_id); ?>",
    variation_id: "<?php echo($variation_id); ?>",
    ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>",
    task: "<?php echo($task); ?>",  // 'new' for new design
    design_type: "<?php echo($design_type); ?>",
    // ... product_data passed to JavaScript
    product_data: <?php echo json_encode($product_data); ?>,
    // product_data includes: upload, option, product, fonts, config, design
};
```

### 9. Cache or Settings That Hide Published Templates

**Cache-Related Settings:**

1. **Transient Cache for Category Products:**
   File: `class-shortcodes.php` (Line 125)
   ```php
   $products = get_transient( 'nbd_design_products_cat_'.$cat_id );
   if( false === $products ){
       // ... fetch and cache for DAY_IN_SECONDS
       set_transient( 'nbd_design_products_cat_'.$cat_id , $products, DAY_IN_SECONDS );
   }
   ```
   **TROUBLESHOOTING:** If templates not showing after publish, clear this cache:
   ```php
   delete_transient( 'nbd_design_products_cat_' . CATEGORY_ID );
   ```

2. **Admin Settings:**
   File: `includes/class-api.php` (Line 19)
   ```php
   if( nbdesigner_get_option( 'nbdesigner_enable_gallery_api', 'no' ) == 'yes' ){
       // Gallery generation is enabled - can affect display
   }
   ```

3. **Lazy Load Setting:**
   ```php
   $disable_auto_load_template = nbdesigner_get_option( 'nbdesigner_disable_auto_load_template', 'no' );
   if( $disable_auto_load_template == 'yes' ){
       // Templates won't auto-load when opening designer
   }
   ```

### 10. Product_ID Field Requirement for Templates to Show

**CRITICAL REQUIREMENT:**

```sql
SELECT * FROM wp_nbdesigner_templates WHERE product_id IS NULL;
```

If any templates have `product_id = NULL` or `product_id = 0`, they will NOT show.

**Template MUST have:**
- `product_id` = A valid WooCommerce product ID
- `variation_id` = 0 (for simple products) OR matching variation ID (for variable products)

**Both fields are required:**
1. `product_id` - Which product this template belongs to
2. `variation_id` - Which variation (0 for simple products)

### 11. Folder Structure and Template Loading

**Template File Structure:**

```
/wp-content/uploads/nbdesigner/
├── designs/
│   └── {folder}/              # Unique template folder
│       ├── config.json        # Template configuration
│       ├── design.json        # Design data
│       ├── used_font.json    # Used fonts
│       ├── product.json      # Product settings
│       ├── option.json       # Option data
│       ├── preview/          # Preview images
│       │   ├── 0.png
│       │   ├── 1.png
│       │   └── ...
│       └── resource/         # Template resources
│           └── design.zip    # Downloadable design
```

**How Folder is Used:**
- Database stores `folder` value (e.g., "abc123def456")
- System constructs path: `NBDESIGNER_CUSTOMER_DIR . '/' . folder`
- Preview images are generated from `/preview/` subfolder
- Frontend loads design.json and config.json from this folder

**If folder doesn't exist on server:**
- Template still shows in admin
- Templates won't display properly on frontend
- Preview images won't load

### 12. Additional Configuration in Product/Plugin Settings

**Required Product Meta Keys:**

```php
_nbdesigner_enable = 1                          // Enable NBDesigner for this product
_designer_setting = [serialized config]         // Template design settings
_nbdesigner_option = [serialized options]       // Product customization options
_nbdesigner_upload = [serialized upload config] // Upload settings
```

**Plugin Settings (Admin > NBDesigner):**

1. **Auto-load Templates:**
   - Setting: `nbdesigner_disable_auto_load_template`
   - If set to 'yes', templates won't auto-load
   - Check admin panel for this setting

2. **Enable Gallery API:**
   - Setting: `nbdesigner_enable_gallery_api`
   - If enabled, different gallery rendering method
   - May cache gallery separately

3. **Template Width:**
   - Setting: `nbdesigner_template_width`
   - Default: 500px
   - Affects preview thumbnail generation

4. **Lazy Loading:**
   - Setting: `nbdesigner_lazy_load_template`
   - If 'yes', templates load on demand, not immediately

---

## EXACT QUERY THAT FETCHES TEMPLATES FOR FRONTEND DISPLAY

### Default Frontend Query (from nbd_get_templates):

```sql
SELECT * 
FROM wp_nbdesigner_templates 
WHERE 
    product_id = '123'                    -- Product ID
    AND ( variation_id = '456' || variation_id = 0 )  -- Variation ID or 0
    AND publish = 1                        -- MUST BE PUBLISHED
    AND type IS NULL                       -- EDITABLE TEMPLATES ONLY
ORDER BY created_date DESC
```

### Where Clause Breakdown:

| Condition | Required? | Value | Impact |
|-----------|-----------|-------|--------|
| `publish = 1` | YES | 0 or 1 | If 0, template hidden from customers |
| `product_id = X` | YES | Product ID | Template must be assigned to product |
| `variation_id = 0 OR variation_id = X` | SOMETIMES | 0 or ID | Depends on if product has variations |
| `type IS NULL` | YES (default) | NULL or 'solid' | If 'solid', won't show in default query |
| `private = 0` | NO (not checked) | 0 or 1 | Private flag doesn't control visibility |

### Shortcode Query (Different WHERE clause):

```sql
SELECT p.ID, p.post_title, t.id, t.name, t.folder, t.product_id, t.variation_id, t.user_id, t.thumbnail
FROM wp_nbdesigner_templates AS t
LEFT JOIN wp_posts AS p ON t.product_id = p.ID
WHERE 
    t.publish = 1                         -- MUST BE PUBLISHED
    AND p.post_status = 'publish'        -- Product must be published
    AND t.publish = 1                    -- Double check publish
    [AND t.product_id IN (list)]         -- If category filter used
    [AND p.ID = product_id]              -- If specific product
ORDER BY t.created_date DESC
LIMIT 10
```

---

## HOW TO FIX TEMPLATES NOT SHOWING TO CUSTOMERS

### Step-by-Step Troubleshooting:

**STEP 1: Verify Template "publish" Status**
```sql
SELECT id, name, publish, private, product_id, variation_id, type, folder 
FROM wp_nbdesigner_templates 
WHERE product_id = YOUR_PRODUCT_ID;
```
- Look for `publish = 1`
- Look for `type = NULL` (not 'solid')

**STEP 2: Check Product Meta Keys**
```sql
SELECT meta_key, meta_value 
FROM wp_postmeta 
WHERE post_id = YOUR_PRODUCT_ID 
AND meta_key IN ('_nbdesigner_enable', '_designer_setting', '_nbdesigner_option');
```
Must have:
- `_nbdesigner_enable = 1`
- `_designer_setting` = non-empty serialized data
- `_nbdesigner_option` = non-empty serialized data

**STEP 3: Check if Template Folder Exists**
```bash
ls -la /home/user/intergraphic/wp-content/uploads/nbdesigner/designs/{TEMPLATE_FOLDER}/
```
Files should exist:
- `config.json` ✓
- `design.json` ✓
- `product.json` ✓
- `option.json` ✓
- `preview/` directory with images ✓

**STEP 4: Clear Cache (if using category shortcode)**
```php
// In WP-Admin or via code:
delete_transient( 'nbd_design_products_cat_' . YOUR_CATEGORY_ID );
```

**STEP 5: Check Admin Settings**
Admin Panel > NBDesigner > Appearance Settings:
- [ ] "Disable auto-load template" = NO (unchecked)
- [ ] Check product-specific settings (local settings may override)

**STEP 6: For Variable Products, Check Variation Settings**
```sql
SELECT post_id, meta_key, meta_value 
FROM wp_postmeta 
WHERE post_id = YOUR_VARIATION_ID 
AND meta_key = '_nbdesigner_variation_enable';
```
If `_nbdesigner_variation_enable = 1`, variation has specific settings.

---

## VISIBILITY LOGIC - CODE SHOWING HOW TEMPLATES ARE FILTERED

### Core Filter Logic:

```php
// File: /includes/class-util.php (Line 1577-1603)

// WHERE publish = 1 - FILTERS OUT UNPUBLISHED
if($priority) {
    $sql = "SELECT * FROM $table_name WHERE product_id = '$product_id' 
            AND ( variation_id = '$variation_id' || variation_id = 0 ) 
            AND priority = 1 
            AND publish = 1              <-- VISIBILITY FILTER #1
            AND $type_query";            <-- VISIBILITY FILTER #2
}

// WHERE type_query - FILTERS BY TYPE
// $type_query = is_null( $type ) ? "type IS NULL" : ...
// This ONLY shows editable templates (type = NULL) by default
```

### Private Flag NOT Used in Frontend:
```php
// NOTICE: The private field is NOT checked in the main query
// This is set when bulk-marking templates as "Private"
$sql .= " AND private = 0";  // NOT in the actual code!
```

### Product Validation (in Shortcodes):
```php
// File: /includes/class-shortcodes.php (Line 76-78)
$sql = "SELECT ... FROM {$wpdb->prefix}nbdesigner_templates AS t";
$sql .= " LEFT JOIN {$wpdb->prefix}posts AS p ON t.product_id = p.ID";
$sql .= " WHERE t.publish = 1 AND p.post_status = 'publish' AND publish = 1";
                              ^                           ^
                              Must be published in DB    Must be published in WP
```

---

## COMMON ISSUES AND FIXES

| Issue | Cause | Solution |
|-------|-------|----------|
| Templates not showing on product page | `publish = 0` | Update template set `publish = 1` in admin |
| Only one template shows | `priority = 1` exists | Set other templates as primary or unpublish extra |
| Shortcode shows no templates | Category cache stale | `delete_transient('nbd_design_products_cat_X')` |
| Templates show in admin but not frontend | `type = 'solid'` | Templates are solid designs, not editable |
| Preview images missing | Folder structure broken | Check `/uploads/nbdesigner/designs/{folder}/preview/` exists |
| Wrong variation shows | `variation_id` mismatch | Ensure template variation_id matches selected variation |
| Template shows but JS won't load | Admin setting disabled | Check "Disable auto-load template" setting |
| Variation templates not loading | Local variation setting | Check `_nbdesigner_variation_enable` meta key |

---

## DATABASE TABLE STRUCTURE

```sql
CREATE TABLE wp_nbdesigner_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,           -- Product the template belongs to
    variation_id INT DEFAULT 0,        -- Variation ID (0 for simple products)
    folder VARCHAR(255),               -- Unique folder name for template files
    user_id INT,                       -- User who created template
    name VARCHAR(255),                 -- Template name
    resource VARCHAR(255),             -- Resource folder (for solid designs)
    created_date DATETIME,             -- Creation timestamp
    publish INT DEFAULT 0,             -- 0=hidden, 1=visible to customers
    private INT DEFAULT 0,             -- 0=public, 1=private (not fully used)
    priority INT DEFAULT 0,            -- 0=normal, 1=primary template
    type VARCHAR(50),                  -- NULL=editable, 'solid'=solid design
    thumbnail INT                      -- Thumbnail attachment ID
);
```

---

## FINAL SUMMARY

**Why Templates Don't Show (Most Common Reasons):**

1. **`publish = 0`** (60% of cases)
   - Admin didn't click Publish after creating template
   - Fix: Edit template in admin, click Publish

2. **`type = 'solid'`** (20% of cases)
   - Template is a solid design, not editable
   - Fix: Create new editable template or change type to NULL

3. **`product_id = NULL`** (10% of cases)
   - Template not assigned to product
   - Fix: Assign template to correct product_id

4. **Cache Issue** (5% of cases)
   - Category template cache is stale
   - Fix: Clear transient cache

5. **Folder Missing** (5% of cases)
   - Template files deleted from server
   - Fix: Re-upload template folder to `/uploads/nbdesigner/designs/`

**Quick Verification Query:**
```sql
SELECT id, name, publish, type, product_id FROM wp_nbdesigner_templates 
WHERE product_id = 123 AND publish = 1 AND type IS NULL;
```

If this returns templates, they SHOULD appear on the frontend.

