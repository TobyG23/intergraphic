# NBDesigner wp_nbdesigner_templates Database Structure Analysis

## 1. CREATE TABLE Statement

Located in: `/home/user/intergraphic/wp-content/plugins/web-to-print-online-designer/includes/class-install.php` (Lines 122-142)

```sql
CREATE TABLE {$wpdb->prefix}nbdesigner_templates ( 
 id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
 product_id BIGINT(20) UNSIGNED NOT NULL,
 variation_id BIGINT(20) NULL, 
 folder varchar(255) NOT NULL,
 user_id BIGINT(20) NULL, 
 created_date DATETIME NOT NULL default '0000-00-00 00:00:00',
 publish TINYINT(1) NOT NULL default 1,
 private TINYINT(1) NOT NULL default 0,
 priority  TINYINT(1) NOT NULL default 0,
 hit BIGINT(20) NULL, 
 sales INT(10) NOT NULL default 0,
 vote INT(10) NOT NULL default 0,
 name varchar(255) NULL,
 type varchar(255) NULL,
 resource varchar(255) NULL,
 tags varchar(255) NULL,
 colors varchar(255) NULL,
 thumbnail INT(10) NULL,
 PRIMARY KEY  (id) 
) $collate;
```

## 2. Column Data Types Summary

| Column | Data Type | Default | Nullable | Description |
|--------|-----------|---------|----------|-------------|
| id | BIGINT(20) UNSIGNED | AUTO_INCREMENT | No | Primary Key - Template ID |
| product_id | BIGINT(20) UNSIGNED | - | No | WooCommerce Product ID |
| variation_id | BIGINT(20) | NULL | Yes | WooCommerce Product Variation ID |
| folder | varchar(255) | - | No | Unique folder identifier (stores design files) |
| user_id | BIGINT(20) | NULL | Yes | WordPress User ID who created template |
| created_date | DATETIME | '0000-00-00 00:00:00' | No | Template creation timestamp |
| publish | TINYINT(1) | 1 | No | 1 = Published, 0 = Unpublished |
| private | TINYINT(1) | 0 | No | 1 = Private, 0 = Public |
| priority | TINYINT(1) | 0 | No | Template priority/ordering |
| hit | BIGINT(20) | NULL | Yes | Number of times template was used |
| sales | INT(10) | 0 | No | Number of sales from this template |
| vote | INT(10) | 0 | No | User voting count |
| name | varchar(255) | NULL | Yes | Template name/title |
| type | varchar(255) | NULL | Yes | Template type (e.g., '2' for custom template) |
| **resource** | varchar(255) | NULL | Yes | **RARELY USED - Legacy field** |
| tags | varchar(255) | NULL | Yes | CSV of template tag IDs (see template_tag taxonomy) |
| colors | varchar(255) | NULL | Yes | CSV of hex colors used in template |
| **thumbnail** | INT(10) | NULL | Yes | **WordPress attachment/media ID (NOT URL)** |

## 3. Resource Field Analysis

### Current State
- **Data Type**: varchar(255) - allows storing a string value
- **Current Usage**: Almost never populated in core code
- **Default Value**: NULL

### Where Resource is Referenced
1. **Design API** (`/includes/launcher/api/design.php`, Line 118):
   - Included in API response: `'resource' => $object->resource,`
   - But no code populates it during template creation

2. **Import/Export** (`/includes/class-import-export-product.php`):
   - Templates are imported but resource field is NOT explicitly set
   - Field remains NULL unless manually populated

### Why Resource Field Exists But Unused
The `resource` field appears to be a **legacy or planned feature** that:
- Was intended for storing a resource identifier or type
- Is included in the CREATE TABLE but never populated by the plugin
- May have been planned for future features like associating templates with external resources

### Correct Usage (Should Be)
The `resource` field should typically be NULL or contain:
- An identifier string if templates are fetched from external API
- A storage type indicator (e.g., 'local', 'cloud', 'api')
- Currently: Leave NULL in normal operation

## 4. Thumbnail Field Analysis - CRITICAL

### Current State
- **Data Type**: INT(10) - stores WordPress media attachment ID
- **NOT a URL string** - This is the most common mistake!
- **Default Value**: NULL

### How Thumbnail is Populated

#### When User Uploads Custom Template
Located in: `/includes/class.template-tags.php` (Lines 170-177)

When a user uploads a custom template in "Create Template" mode with type='2':
```php
if( $type == '2' && isset( $_FILES['template_thumb'] ) ){
    $thumb = $_FILES['template_thumb'];
    if( $thumb['error'] == 0 ){
        $attachment_id = $this->upload_template_thumb( $thumb );  // Returns media ID
        if( $attachment_id ){
            $info['thumbnail'] = $attachment_id;  // Store ATTACHMENT ID (INTEGER)
        }
    }
}
```

#### When Templates Are Retrieved for Display
Located in: `/includes/class-util.php` - `nbd_get_resource_templates()` function:

```php
if( isset( $tem['thumbnail'] ) && $tem['thumbnail'] ){
    $_temp['thumbnail'] = wp_get_attachment_url( $tem['thumbnail'] );  // CONVERTS ID to URL
}else{
    $_temp['thumbnail'] = $_temp['src'][0];  // Falls back to first preview image
}
```

### What Values Should Be

| Scenario | Value | Type | Correct? |
|----------|-------|------|----------|
| No custom thumbnail | NULL | NULL | YES |
| Custom thumbnail uploaded | 1234 | INT | YES |
| Custom thumbnail uploaded | '1234' | STRING | NO (but will work) |
| URL string | 'https://example.com/image.jpg' | STRING | NO - CAUSES BUG! |

### The Bug Pattern
If `thumbnail` field contains a URL string instead of an attachment ID:
1. Display logic calls: `wp_get_attachment_url('https://example.com/image.jpg')`
2. WordPress tries to find attachment with that URL
3. Fails and returns NULL or default image
4. Template displays incorrectly

## 5. Template Creation Process - How Fields Are Populated

### Main Function: `nbdesigner_insert_table_templates()`
Located in: `/includes/class.nbdesigner.php` (Lines 3384+)

When a template is created via UI (saves design as template):

```php
private function nbdesigner_insert_table_templates( $product_id, $variation_id, $folder, $priority, $publish = 1, $private = 0 ){
    global $wpdb;
    $created_date   = new DateTime();
    $user_id        = wp_get_current_user()->ID;
    $table_name     = $wpdb->prefix . 'nbdesigner_templates';
    $publish        = !nbd_check_publish_design_permission( $user_id ) ? 0 : $publish;
    $wpdb->insert( $table_name, array(
        'product_id'    => $product_id,
        'variation_id'  => $variation_id,
        'folder'        => $folder,              // UNIQUE identifier for design files
        'user_id'       => $user_id,
        'created_date'  => current_time( 'mysql' ),
        'publish'       => $publish,
        'private'       => $private,
        'priority'      => $priority
        // NOTE: resource, thumbnail, name, tags, colors NOT SET HERE
        // They remain NULL unless set by after_nbd_save_customer_design hook
    ) );
    return true;
}
```

### Initial Values When Template is Saved
- `product_id` - From POST parameter
- `variation_id` - From POST parameter (0 if not set)
- `folder` - Unique identifier based on timestamp + random (e.g., "a1b2c1609440000123")
- `user_id` - Current WordPress user ID
- `created_date` - Server time (MySQL format: 'Y-m-d H:i:s')
- `publish` - 1 by default (unless user lacks permission)
- `private` - 0 by default
- `priority` - 0 by default

### Fields NOT Set During Initial Save
- `name` - NULL (set later via `after_nbd_save_customer_design` hook in template-tags.php)
- `type` - NULL (optional, can be '2' for custom templates)
- `resource` - NULL (virtually never set in core plugin)
- `tags` - NULL (set later when associating with template tag taxonomy)
- `colors` - NULL (could be populated but rarely is)
- `thumbnail` - NULL (only set if user uploads custom thumbnail with type=2)

### Post-Save Updates
Located in: `/includes/class.template-tags.php` (Lines 161-184)

After design is saved, the hook `after_nbd_save_customer_design` triggers:
```php
public function update_template( $result ){
    $task = (isset($_POST['task']) && $_POST['task'] != '') ? wc_clean( $_POST['task'] ) : 'new';
    $design_type = (isset($_POST['design_type']) && $_POST['design_type'] != '') ? wc_clean( $_POST['design_type'] ) : '';
    
    if( $task == 'create' || ( $task == 'edit' && $design_type == 'template' ) ){
        $info['name']    = (isset($_POST['template_name']) && $_POST['template_name'] != '') ? wc_clean( $_POST['template_name'] ) : '';
        $type            = (isset($_POST['template_type']) && $_POST['template_type'] != '') ? wc_clean( $_POST['template_type'] ) : '';
        $info['tags']    = (isset($_POST['template_tags']) && $_POST['template_tags'] != '') ? wc_clean( $_POST['template_tags'] ) : '';
        $info['colors']  = (isset($_POST['template_colors']) && $_POST['template_colors'] != '') ? wc_clean( $_POST['template_colors'] ) : '';
        
        if( $type == '2' && isset( $_FILES['template_thumb'] ) ){
            $thumb = $_FILES['template_thumb'];
            if( $thumb['error'] == 0 ){
                $attachment_id = $this->upload_template_thumb( $thumb );  // RETURNS ATTACHMENT ID
                if( $attachment_id ){
                    $info['thumbnail'] = $attachment_id;  // Store the ID, not URL!
                }
            }
        }
        // Update the template record with additional info
        $templates = $this->get_template_by_folder( $result['folder'] );
        if( is_array($templates) && isset( $templates[0] ) ){
            $tid = $templates[0]['id'];
            $this->update_template_info( $tid, $info );
        }
    }
}
```

## 6. Template Metadata and Relationships

### folder vs resource Field
- **folder**: Unique identifier pointing to `/wp-content/uploads/nbdesigner/customer/{folder}/` directory
  - Contains all design files: design.json, config.json, product.json, preview/, used_font.json
  - IS the actual "resource" (the saved design files)
  - Should never be NULL
  - Example: "a1b2c1609440000123"
  
- **resource**: String field (varchar) - rarely used
  - Could indicate resource type/source
  - Currently deprecated/unused in most installations
  - Expected to be NULL in normal operation

### Proper Relationship Architecture
```
Template Record
├── id: Primary key (e.g., 42)
├── product_id: Links to WooCommerce product (e.g., 123)
├── variation_id: Links to product variation (e.g., 0 or product variation ID)
├── folder: Unique identifier for design files directory (e.g., "a1b2c1609440000123")
│   └── Points to: /wp-content/uploads/nbdesigner/customer/{folder}/
│       ├── design.json (canvas objects, text, images, etc.)
│       ├── config.json (product configuration)
│       ├── product.json (product details at save time)
│       ├── used_font.json (fonts used in design)
│       └── preview/ (preview images for each side of product)
│           ├── 1.png (side 1)
│           ├── 2.png (side 2)
│           └── 3.png (side 3)
├── user_id: Creator's WordPress user ID
├── created_date: Timestamp when template was created
├── thumbnail: WordPress Media Attachment ID (INT) or NULL
│   └── If populated (e.g., 5678):
│       ├── Use: wp_get_attachment_url(5678) to get URL
│       └── Points to: WordPress wp_posts table where post_type='attachment'
│   └── If NULL:
│       └── Display will use: /preview/{first-side-image}.png
└── tags: CSV of template tag taxonomy IDs (e.g., "1,3,5")
    └── Links to: WordPress wp_terms where taxonomy='template_tag'
```

## 7. Common Bug - Storing URL in Thumbnail Field

### The Problem
A template might have been incorrectly created with:
```
thumbnail = 'https://example.com/wp-content/uploads/2024/11/thumb.jpg'  (STRING - WRONG!)
```

Instead of:
```
thumbnail = 5678  (INTEGER - CORRECT!)
```

### Why It's Wrong
1. Field type is `INT(10)` - expects numeric attachment ID, not string URL
2. Display logic calls: `wp_get_attachment_url($thumbnail)` 
3. If $thumbnail is a URL string, WordPress can't find the attachment
4. Returns NULL or broken image
5. Breaks template display functionality

### How to Fix It

#### Option A: Convert URL to Attachment ID and Update
If the thumbnail is still accessible at the URL:
```sql
-- First, import image into WordPress media library
-- Get the attachment ID from wp_posts table
-- Then update: 
UPDATE wp_nbdesigner_templates 
SET thumbnail = {new_attachment_id} 
WHERE id = {template_id};
```

#### Option B: Use Preview Images Instead (Recommended if no custom thumbnail needed)
```sql
-- Set thumbnail to NULL to use preview folder images
UPDATE wp_nbdesigner_templates 
SET thumbnail = NULL 
WHERE id = {template_id};
```

#### Option C: Set to Empty String (Not Recommended)
```sql
-- Convert string URL to NULL
UPDATE wp_nbdesigner_templates 
SET thumbnail = NULL 
WHERE thumbnail LIKE 'https://%' 
   OR thumbnail LIKE 'http://%';
```

### For Future Templates
Use the built-in upload function properly:
```php
// From class.template-tags.php
private function upload_template_thumb( $file ){
    $overrides = array(
        'test_form'     => false,
        'test_size'     => true,
        'test_upload'   => true
    );
    $file_attributes = wp_handle_sideload( $file, $overrides );
    if ( isset($file_attributes['error']) ) {
        return false;
    }
    // ... WordPress media library integration ...
    $attachment_id = wp_insert_attachment( $attachment_data, $file_path );
    // ... generate metadata ...
    return $attachment_id;  // Returns INTEGER media ID
}
```

## 8. How Templates Display Thumbnails

When templates are retrieved and displayed (via shortcode or UI):

Located in: `/includes/class-util.php` - `nbd_get_resource_templates()` function:

```php
function nbd_get_resource_templates( $product_id, $variation_id, $limit = 20, $start = 0, $tags = false ){
    $data       = array();
    $templates  = nbd_get_templates( $product_id, $variation_id, '', false, $limit, $start );
    
    foreach ( $templates as $tem ){
        $path_preview   = NBDESIGNER_CUSTOMER_DIR . '/' . $tem['folder'] . '/preview';
        $listThumb      = Nbdesigner_IO::get_list_images( $path_preview );
        $listThumb      = nbd_sort_file_by_side( $listThumb );
        
        if( count( $listThumb ) ){
            $_temp          = array();
            $_temp['id']    = $tem['folder'];
            
            foreach( $listThumb as $img ){
                $_temp['src'][] = Nbdesigner_IO::wp_convert_path_to_url( $img );
            }
            
            // CRITICAL LOGIC HERE:
            if( isset( $tem['thumbnail'] ) && $tem['thumbnail'] ){
                // If thumbnail is set, convert attachment ID to URL
                $_temp['thumbnail'] = wp_get_attachment_url( $tem['thumbnail'] );
            }else{
                // Otherwise use first preview image
                $_temp['thumbnail'] = $_temp['src'][0];
            }
            
            $data[] = $_temp;
        }
    }
    return $data;
}
```

### Expected Data Types
- `$tem['thumbnail']` = **Integer (media attachment ID) or NULL**
- Uses `wp_get_attachment_url()` to convert ID to URL for display
- Falls back to preview folder images if thumbnail not set
- **Never expects a URL string!**

## 9. Example of Correct Template Values

### Minimal Valid Template (Just Created)
```
id: 42
product_id: 123
variation_id: 0
folder: "a1b2c1609440000123"
user_id: 1
created_date: "2024-11-11 10:30:45"
publish: 1
private: 0
priority: 0
hit: NULL
sales: 0
vote: 0
name: NULL
type: NULL
resource: NULL
tags: NULL
colors: NULL
thumbnail: NULL  // Will use preview images from folder/preview/ directory
```

### Template with Custom Thumbnail and Metadata
```
id: 42
product_id: 123
variation_id: 0
folder: "a1b2c1609440000123"
user_id: 1
created_date: "2024-11-11 10:30:45"
publish: 1
private: 0
priority: 0
hit: NULL
sales: 0
vote: 0
name: "Business Card Template"
type: "2"  // Custom template
resource: NULL  // Keep NULL for normal templates
tags: "1,5,8"  // Template tag IDs (numeric, comma-separated)
colors: "FF0000,00FF00,0000FF"  // Hex colors without # (comma-separated)
thumbnail: 1234  // WordPress media attachment ID (INTEGER, NOT URL)
```

## 10. Summary & Best Practices

### For resource Field
- **Leave NULL** for normal templates
- Only populate if integrating with external APIs or custom resource management
- Not required for template functionality
- Legacy field that's included in schema but rarely used

### For thumbnail Field - MOST IMPORTANT
- **ALWAYS store WordPress Attachment ID (INTEGER)** - Never store URL string
- **NULL is valid** - Templates will use preview images from `/folder/preview/` directory instead
- **Must be numeric** - Use `absint()` when updating
- When retrieving: Always convert to URL with `wp_get_attachment_url()`
- Common bug: String URLs stored in this field break template display

### For folder Field
- **Unique identifier** pointing to design files directory
- **Never NULL** - Always required
- Points to: `/wp-content/uploads/nbdesigner/customer/{folder}/`
- Relationship: folder IS the actual "resource" (the saved design)
- Generated format: substring of MD5 hash + random number + timestamp

### For tags/colors Fields
- **Comma-separated values** stored as varchar(255)
- tags: Template tag term IDs (numeric, e.g., "1,3,5")
- colors: Hex color codes without # prefix (e.g., "FF0000,00FF00,0000FF")
- Both can be empty/NULL if not categorized

### For Other Fields
- name: Optional template name/title (NULL is valid)
- type: Template type indicator (e.g., '2' for user-created custom template)
- created_date: Should be in MySQL format 'Y-m-d H:i:s'
- publish: 1 = public/published, 0 = draft/unpublished
- private: 1 = private (only creator can use), 0 = public
- hit: Tracking field (how many times used) - leave NULL or 0
- sales: Number of products sold using this template - leave 0 if new
- vote: User voting/rating - leave 0 if new

