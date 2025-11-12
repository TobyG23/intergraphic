# NBDesigner Database Analysis - Key Findings Summary

## Quick Reference

### CREATE TABLE Statement
File: `/home/user/intergraphic/wp-content/plugins/web-to-print-online-designer/includes/class-install.php`
Lines: 122-142

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

## Critical Findings

### 1. resource Field (varchar(255))
- **Status**: Legacy field, almost never populated
- **Default**: NULL
- **Usage**: Virtually none in core plugin code
- **Correct Value**: NULL (or identifier string if using external APIs)
- **Location in code**: Only included in REST API response (design.php line 118)

### 2. thumbnail Field (INT(10)) - MOST IMPORTANT
- **Status**: CRITICAL - Frequently misused
- **Data Type**: INTEGER (WordPress attachment ID) - NOT a URL!
- **Default**: NULL
- **Expected Values**:
  - NULL (valid - uses preview images)
  - Integer attachment ID (e.g., 1234)
  - Never: URL strings like 'https://example.com/image.jpg'
  
- **Where Populated**:
  - File: `/includes/class.template-tags.php` Lines 170-177
  - Function: `upload_template_thumb()` returns media attachment ID
  - Usage: When user uploads custom thumbnail (type='2')

- **How Retrieved**:
  - File: `/includes/class-util.php`
  - Function: `nbd_get_resource_templates()`
  - Converts ID to URL: `wp_get_attachment_url($tem['thumbnail'])`
  - Falls back to `/preview/{first-image}.png` if NULL

### 3. folder Field (varchar(255))
- **Status**: Essential - NEVER NULL
- **Purpose**: Unique identifier pointing to `/wp-content/uploads/nbdesigner/customer/{folder}/`
- **Contains**: design.json, config.json, preview/, used_font.json, etc.
- **IS the actual "resource"**: The saved design files
- **Generated**: MD5 hash substring + random number + timestamp

### 4. Template Creation Flow
File: `/includes/class.nbdesigner.php` Line 3384+
Function: `nbd_save_customer_design()` + `nbdesigner_insert_table_templates()`

**Initial Insert** (AUTO-POPULATED):
- product_id
- variation_id
- folder
- user_id
- created_date
- publish
- private
- priority

**Later Updated via Hook** (Lines 161-184 in class.template-tags.php):
- name
- tags
- colors
- thumbnail (if file uploaded)

**NEVER AUTO-POPULATED** (remain NULL):
- resource
- type (optional, can be set to '2' for custom)
- hit (tracking)

### 5. Data Types Summary Table

| Column | Type | Default | Nullable | Notes |
|--------|------|---------|----------|-------|
| id | BIGINT(20) UNSIGNED | AUTO_INCREMENT | No | Primary key |
| product_id | BIGINT(20) UNSIGNED | - | No | WooCommerce product |
| variation_id | BIGINT(20) | NULL | Yes | Product variation |
| folder | varchar(255) | - | No | Design files location |
| user_id | BIGINT(20) | NULL | Yes | Creator user ID |
| created_date | DATETIME | 0000-00-00 00:00:00 | No | Creation timestamp |
| publish | TINYINT(1) | 1 | No | 1=yes, 0=no |
| private | TINYINT(1) | 0 | No | 1=yes, 0=no |
| priority | TINYINT(1) | 0 | No | Display order |
| hit | BIGINT(20) | NULL | Yes | Usage count |
| sales | INT(10) | 0 | No | Sales count |
| vote | INT(10) | 0 | No | Rating count |
| name | varchar(255) | NULL | Yes | Template name |
| type | varchar(255) | NULL | Yes | Type indicator |
| **resource** | varchar(255) | NULL | Yes | **Legacy - keep NULL** |
| tags | varchar(255) | NULL | Yes | CSV tag IDs |
| colors | varchar(255) | NULL | Yes | CSV hex colors |
| **thumbnail** | INT(10) | NULL | Yes | **Media attachment ID ONLY** |

## Example Values

### Correct Minimal Template
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
thumbnail: NULL
```

### Correct Full Template
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
name: "Business Card"
type: "2"
resource: NULL
tags: "1,5,8"
colors: "FF0000,00FF00"
thumbnail: 1234
```

### WRONG - Do Not Use This
```
thumbnail: "https://example.com/image.jpg"  // WRONG - STRING URL!
```

## Bug Pattern: URL in Thumbnail Field

### Symptom
```php
$tem['thumbnail'] = 'https://example.com/wp-content/uploads/2024/11/thumb.jpg'
```

### Why It Breaks
1. Field expects INT (attachment ID)
2. Display calls: `wp_get_attachment_url('https://...')`
3. WordPress can't find attachment by URL string
4. Returns NULL
5. Template displays broken/missing image

### How to Fix
```sql
-- Option 1: Find correct attachment ID and update
UPDATE wp_nbdesigner_templates 
SET thumbnail = 1234 
WHERE id = 42;

-- Option 2: Use preview images instead
UPDATE wp_nbdesigner_templates 
SET thumbnail = NULL 
WHERE id = 42;

-- Option 3: Clear all incorrect URLs
UPDATE wp_nbdesigner_templates 
SET thumbnail = NULL 
WHERE thumbnail LIKE 'https://%' 
   OR thumbnail LIKE 'http://%';
```

## Source Code References

### Key Files
1. **Database Definition**: `/wp-content/plugins/web-to-print-online-designer/includes/class-install.php`
2. **Template Creation**: `/wp-content/plugins/web-to-print-online-designer/includes/class.nbdesigner.php`
3. **Template Tags/Updates**: `/wp-content/plugins/web-to-print-online-designer/includes/class.template-tags.php`
4. **Template Retrieval**: `/wp-content/plugins/web-to-print-online-designer/includes/class-util.php`
5. **API/Design Response**: `/wp-content/plugins/web-to-print-online-designer/includes/launcher/api/design.php`

### Key Functions
- `nbdesigner_insert_table_templates()` - Initial insert (class.nbdesigner.php ~3384)
- `nbd_save_customer_design()` - Main save handler (class.nbdesigner.php ~3384)
- `update_template()` - Post-save updates (class.template-tags.php ~161)
- `upload_template_thumb()` - Media upload (class.template-tags.php ~186)
- `nbd_get_resource_templates()` - Template retrieval (class-util.php)

## Relationship Architecture

```
wp_nbdesigner_templates
├── folder --> /wp-content/uploads/nbdesigner/customer/{folder}/
│             ├── design.json
│             ├── config.json
│             ├── product.json
│             ├── used_font.json
│             └── preview/
│                 ├── 1.png
│                 ├── 2.png
│                 └── 3.png
├── thumbnail --> wp_posts (post_type='attachment')
│                 └── wp_postmeta (stores image URLs, sizes, etc.)
└── tags --> wp_terms (taxonomy='template_tag')
           └── wp_termmeta (stores term metadata)
```

## Best Practices Checklist

- [ ] resource field: Always NULL (legacy)
- [ ] thumbnail field: Integer media ID or NULL only
- [ ] folder field: Never NULL, always required
- [ ] folder format: Generated automatically, don't manually set
- [ ] tags format: Comma-separated numeric IDs (e.g., "1,3,5")
- [ ] colors format: Hex codes without # (e.g., "FF0000,00FF00")
- [ ] thumbnail display: Always use `wp_get_attachment_url()` to convert ID to URL
- [ ] Fallback: If thumbnail NULL, use `/preview/{first-side}.png`
- [ ] created_date format: MySQL datetime ('Y-m-d H:i:s')
- [ ] publish values: 1 (yes) or 0 (no)

## Critical Points

1. **Never store URLs in thumbnail field** - Causes display failure
2. **folder IS the actual resource** - Contains all design files
3. **resource field is legacy** - Leave NULL in normal operation
4. **Thumbnail conversion needed** - ID in DB, URL for display
5. **Tags are taxonomy IDs** - Link to wp_terms with template_tag taxonomy

