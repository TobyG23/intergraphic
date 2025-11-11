# NBDesigner Staff Configuration Guide
## Complete Setup & Management Manual

**Version:** 1.0
**Last Updated:** November 2024
**Plugin:** Web-to-Print Online Designer v1.2.1 by Printcart
**Target Audience:** Staff Members & Administrators

---

## Table of Contents

1. [Quick Start (5 minutes)](#quick-start)
2. [Admin Dashboard Overview](#admin-dashboard)
3. [General Settings Configuration](#general-settings)
4. [Product Configuration](#product-configuration)
5. [Printing Options Management](#printing-options)
6. [Design Templates](#design-templates)
7. [Cliparts & Media Management](#cliparts-media)
8. [Fonts Management](#fonts-management)
9. [Common Staff Tasks](#common-tasks)
10. [Troubleshooting](#troubleshooting)
11. [API Keys & External Services](#api-keys)
12. [Staff Roles & Permissions](#roles-permissions)

---

## Quick Start

### Accessing NBDesigner Admin

1. Log in to WordPress admin (`/wp-admin/`)
2. Look for **"NBDesigner"** in the left sidebar menu (it's positioned as the 26th menu item)
3. Available sections:
   - Settings
   - Products Manager
   - Cliparts Manager
   - Fonts Manager
   - Printing Options
   - System Info
   - Tools

### First-Time Setup Checklist

- [ ] **Step 1**: Configure General Settings (layout, DPI, units)
- [ ] **Step 2**: Enable NBDesigner on at least one test product
- [ ] **Step 3**: Create a Printing Option configuration
- [ ] **Step 4**: Test the designer on the product page
- [ ] **Step 5**: Configure optional API keys (Google, Pixabay, etc.)

---

## Admin Dashboard Overview

### What You'll See

When you click **NBDesigner** in the admin menu, you'll see the main dashboard with these options:

```
NBDesigner
├── Settings (General plugin configuration)
├── Products Manager (Enable/disable on products)
├── Cliparts Manager (Manage design elements)
├── Fonts Manager (Available fonts for designs)
├── Printing Options (Configure product customization)
├── System Info (Check plugin status)
└── Tools (Advanced utilities)
```

### Required Capabilities

To access NBDesigner admin features, you need these WordPress capabilities:
- `manage_nbd_setting` - General settings access
- `manage_nbd_product` - Product management
- `manage_nbd_art` - Cliparts/artwork management
- `manage_nbd_font` - Font management
- `manage_nbd_tool` - Tool access

**Standard Assignment:**
- **Administrator** = Full access to all NBDesigner features
- **Editor** = Can manage products and printing options (configure manually)
- **Author** = Read-only access

---

## General Settings Configuration

### Where to Find It

1. Go to **NBDesigner** → **Settings**
2. You'll see multiple setting groups on this page

### Essential Settings

#### 1. **Design Layout Selection**

**Current:** Modern (Recommended)
**Options Available:**
- `modern` (m) - ✅ Actively maintained, best performance
- `visual` (v) - ❌ Deprecated
- `classic` (c) - ❌ Deprecated

**Action:** Keep set to "modern" for best results.

#### 2. **Design Dimensions**

- **DPI (Dots Per Inch)**: Default = 72 DPI
  - Use for screen display
  - For print products: Set to 300 DPI (requires professional print setup)

- **Unit of Measurement**:
  - Pixels (px)
  - Millimeters (mm) - Recommended for physical products
  - Inches (in)

**Action:**
- For web designs: Keep as pixels
- For print products: Use mm with 300 DPI

#### 3. **Download & Export Settings**

Available download formats:
- PNG (recommended)
- PDF
- SVG
- JPG

**Action:** Typically enable PNG and PDF for most products.

#### 4. **Thumbnail Settings**

- Thumbnail size: Usually 200x200px to 400x400px
- Controls preview image display

#### 5. **Notification Settings**

- [ ] Enable admin notifications
- [ ] Enable customer notifications
- Specify notification email addresses

---

## Product Configuration

### Enabling NBDesigner on Products

#### Method 1: Individual Product Edit

1. Go to **Products** (in main WordPress menu)
2. Edit any product
3. Scroll to **"NBD options"** meta box (below product details)
4. Click on **Tab 1: Printing Option**
5. Check **"Enable custom design"** (if option exists)
6. Select a Printing Option configuration (or create new)
7. Click **Save/Update**

#### Method 2: Bulk via Products Manager

1. Go to **NBDesigner** → **Products Manager**
2. You'll see a table of all products
3. Columns available:
   - Product name
   - Category
   - NBDesigner enabled (Y/N)
   - Current printing option
4. Click product row to edit
5. Configure and save

### Product Configuration Options

#### Tab 1: Printing Option

- **Enable Printing Options**: Toggle to activate NBDesigner
- **Select Printing Option**: Choose a pre-configured printing option
- **Show in Product Page**: Display printing tab on frontend
- **Tab Title**: Name shown to customers (e.g., "Design Your Own")

#### Tab 2: Product Builder (Advanced)

- Enable/disable product builder mode
- Allows customers to configure product specifications before designing

#### Tab 3: Printing Info Tab

- **Tab Title**: Display name for information (e.g., "How to Design")
- **Tab Content**: HTML/text description shown to customers
- Use for instructions or terms & conditions

### Example: Enabling T-Shirt Product

```
Product: Custom T-Shirt
├── Enable: ✓ Checked
├── Printing Option: "T-Shirt Standard"
├── Tab Title: "Customize Your Design"
├── Product Builder: ✗ Unchecked
└── Info Tab:
    Title: "Design Guidelines"
    Content: "Upload PNG or JPG. Max 2MB. We'll print it exactly as you design it."
```

---

## Printing Options Management

### What Are Printing Options?

Printing Options are **reusable configurations** that define how customers can customize a product. Instead of configuring each product individually, you create a template once and apply it to multiple products.

### Creating a New Printing Option

1. Go to **NBDesigner** → **Printing Options**
2. Click **"New Printing Option"** button
3. Fill out the form:

#### Basic Information

- **Title**: e.g., "T-Shirt Design - Front Print"
- **Description**: Internal note (not shown to customers)

#### Design Canvas Settings

- **Canvas Width**: Design area width (e.g., 300 px)
- **Canvas Height**: Design area height (e.g., 300 px)
- **Default Background**: Upload default design background
- **Canvas Background Color**: Fallback color if no image

#### Customization Fields

Define what customers can customize:

**Example: T-Shirt**

| Field Name | Type | Required | Default Value |
|-----------|------|----------|----------------|
| Size | Select | Yes | Medium |
| Color | Select | Yes | Black |
| Print Position | Select | No | Front |
| Quantity | Number | Yes | 1 |

#### Pricing Rules

- **Base Price**: Starting price (e.g., $15.00)
- **Additional Pricing Rules**:
  - Size premium: Medium +$0, Large +$2, XL +$4
  - Color premium: Custom color +$1.50
  - Quantity discount: 10+ units = -5%

#### Role & Date Restrictions

- **Visible to**: All, Logged-in only, Specific roles
- **Date restrictions**: Start date and end date
- **Priority**: If multiple options apply, higher priority wins

### Example: Complete Printing Option Setup

```
Title: "T-Shirt Standard"

Design Canvas:
├── Width: 350px
├── Height: 350px
├── Background Image: [t-shirt-template.png]
└── Background Color: #FFF

Customization Fields:
├── Size (Select): S, M, L, XL, 2XL
├── Color (Select): Black, White, Red, Blue, Gray
├── Position (Select): Front, Back, Sleeve
├── Quantity (Number): 1-100
└── Rush Order (Checkbox): +$5

Pricing:
├── Base: $15.00
├── Size: L +$2, XL +$4, 2XL +$6
├── Color Premium: +$1 if not Black
├── Quantity: 10+ = -10%, 20+ = -15%
└── Rush: +$5.00

Restrictions:
├── Visible to: All users
├── Date: Always available
└── Priority: 1 (highest)
```

### Managing Multiple Printing Options

Staff can create different options for different products:

- **Printing Option 1**: "T-Shirt Custom Front"
- **Printing Option 2**: "Mug Standard"
- **Printing Option 3**: "Custom Canvas"
- **Printing Option 4**: "Gift Box Premium"

Each can be reused across multiple products to maintain consistency.

---

## Design Templates

### What Are Templates?

Templates are **pre-made designs** that customers can use as a starting point for their designs. They're stored in the `wp_nbdesigner_templates` database table and displayed in the template gallery.

### Accessing Templates

1. Go to **NBDesigner** → **Printing Options** (or dedicated Templates section if available)
2. Look for template management options

### Template Management

#### Creating a Template

1. Click **"New Template"**
2. Configure:
   - **Name**: E.g., "Birthday Party Flyer"
   - **Category**: Organize templates
   - **Thumbnail**: Design preview image (150x150 minimum)
   - **Design Data**: The actual design (exported from designer)
   - **Description**: Shown to customers

#### Organizing Templates

```
Templates by Category:
├── Business
│   ├── Flyers
│   ├── Business Cards
│   └── Letterhead
├── Personal
│   ├── Invitations
│   ├── Posters
│   └── Greeting Cards
├── Products
│   ├── T-Shirts
│   ├── Mugs
│   └── Phone Cases
└── Seasonal
    ├── Holiday
    ├── Birthday
    └── Wedding
```

### Shortcode for Template Gallery

Display templates on your website:

```
[nbdesigner_gallery row="6" per_row="5"]
```

**Attributes:**
- `row`: Number of rows (default: 4)
- `per_row`: Templates per row (default: 4)

---

## Cliparts & Media Management

### What Are Cliparts?

Cliparts are design elements (icons, illustrations, images) that customers can add to their designs.

### Accessing Cliparts Manager

1. Go to **NBDesigner** → **Cliparts Manager**
2. View all available cliparts
3. See organized by categories

### Adding Cliparts

#### Method 1: Individual Upload

1. Click **"New Clipart"** button
2. Configure:
   - **Title**: Name (e.g., "Heart Icon")
   - **Category**: Organize (e.g., "Icons", "Illustrations")
   - **Image**: Upload SVG, PNG, or JPG
   - **License**: Attribution info if needed

#### Method 2: Bulk Upload

1. Use **"Import Cliparts"** function
2. Select folder with images
3. System auto-categorizes
4. Set license/attribution

### Organizing Cliparts

```
Clipart Categories:
├── Icons (1000+ items)
│   ├── Business
│   ├── Social Media
│   └── Decorative
├── Illustrations (500+ items)
├── Backgrounds
├── Text Elements
└── Patterns
```

### File Formats

- **SVG** (Recommended): Scalable, perfect for graphics
- **PNG**: Good for complex images with transparency
- **JPG**: Large photos and images

**Recommendation:** Keep SVG for icons and illustrations, PNG for photos.

### Storage Location

All cliparts are stored in:
```
/wp-content/uploads/nbdesigner/cliparts/
```

---

## Fonts Management

### Where to Access

1. Go to **NBDesigner** → **Fonts Manager**
2. View all available fonts

### Available Fonts

#### Default System Fonts

Pre-loaded fonts:
- Arial
- Helvetica
- Georgia
- Times New Roman
- Courier New
- Verdana
- Comic Sans MS

#### Google Fonts Integration

Our system integrates with Google Fonts for 1000+ additional fonts.

### Adding Custom Fonts

#### Method 1: Import from Google Fonts

1. Click **"Add from Google Fonts"**
2. Search for font name
3. Select font style (Regular, Bold, Italic, etc.)
4. Click **"Import"**
5. Font becomes available in designer

#### Method 2: Upload Custom Font

1. Click **"Upload Font"**
2. Upload font file (TTF, OTF, WOFF, WOFF2)
3. Provide:
   - Font name
   - Category
   - License info
4. Save

#### Method 3: Disable Font

1. Find font in list
2. Click **"Disable"** (doesn't delete, just hides)
3. Existing designs keep font, but new designs can't use it

### Font Categories

Organize fonts for easy customer access:

```
Font Categories:
├── Serif (Professional, Traditional)
│   ├── Georgia
│   ├── Times New Roman
│   └── Playfair Display (Google)
├── Sans-Serif (Modern, Clean)
│   ├── Arial
│   ├── Helvetica
│   └── Roboto (Google)
├── Display (Decorative, Bold)
│   └── Poppins Bold (Google)
├── Handwriting (Casual, Personal)
└── Monospace (Code, Technical)
```

### Storage

All fonts stored in:
```
/wp-content/uploads/nbdesigner/fonts/
```

---

## Common Staff Tasks

### Task 1: Launch New Product with NBDesigner

**Scenario:** You're adding a new custom mug product to the store.

**Steps:**

1. **Create the product** in WooCommerce:
   - Go to **Products** → **Add New**
   - Title: "Custom Photo Mug"
   - Set price: $14.99
   - Upload product image
   - Fill description

2. **Enable NBDesigner**:
   - Scroll to **"NBD options"** meta box
   - Check **"Enable custom design"**
   - Select or create Printing Option: "Mug Standard"
   - Set tab title: "Personalize Your Mug"

3. **Configure Printing Option** (if new):
   - Canvas size: 400x500px
   - Base price: $14.99 (already set in product)
   - Add field: "Wrap Around" (Yes/No) = +$2
   - Add field: "Photo Quality" (Standard/Premium) = +$5

4. **Add description tab**:
   - Title: "Design Tips"
   - Content: "Upload high-res photos (300 DPI). We print exactly what you design!"

5. **Save product**

6. **Test on frontend**:
   - Visit product page
   - Verify "Personalize Your Mug" tab appears
   - Click and verify designer launches
   - Test adding elements
   - Save design
   - Add to cart

### Task 2: Create Category of Cliparts

**Scenario:** You want to add a new set of holiday icons to the designer.

**Steps:**

1. **Go to Cliparts Manager**: NBDesigner → Cliparts Manager

2. **Create category**:
   - (If category creation available, otherwise skip to step 3)
   - Click **"New Category"**
   - Name: "Holiday"
   - Description: "Holiday and seasonal icons"

3. **Add individual cliparts**:
   - Click **"New Clipart"**
   - Title: "Christmas Tree Icon"
   - Category: "Holiday"
   - Upload SVG file
   - License: "Free Use"
   - Save

   - Repeat for: "Santa", "Snowflake", "Ornament", "Gift", "Candy Cane", etc.

4. **Organize**:
   - View Holiday category
   - Reorder if needed (drag and drop)
   - Verify all appear in designer

### Task 3: Set Up Quantity Discounts

**Scenario:** Encourage bulk orders with volume pricing.

**Steps:**

1. **Go to Printing Options**: NBDesigner → Printing Options

2. **Edit option** or create new: "Bulk T-Shirt Order"

3. **Set base price**: $12.00 per shirt

4. **Add pricing rule**: Quantity-based
   ```
   Quantity 1-5: $12.00 each
   Quantity 6-20: $11.00 each
   Quantity 21-50: $10.00 each
   Quantity 51+: $9.00 each
   ```

5. **Save**

6. **Add to products**:
   - Products for bulk orders → Set to this Printing Option
   - Customers see tiered pricing as they increase quantity

### Task 4: Restrict Design Access by Date

**Scenario:** Running holiday promotion only through December 25th.

**Steps:**

1. **Create Printing Option**: "Holiday Special"

2. **Set availability**:
   - **Start Date**: Today
   - **End Date**: December 25, 2024
   - **Visible to**: All users

3. **Apply to products**: Holiday-themed products

4. **Result**: After Dec 25, customers can't use this design option

### Task 5: Set Role-Based Access

**Scenario:** VIP customers get exclusive design options.

**Steps:**

1. **Create user role** (in WordPress):
   - Go to **Settings** → **Custom Roles** (if plugin available)
   - Create role: "VIP Customer"

2. **Create Printing Option**: "VIP Premium Design"

3. **Set access**:
   - **Visible to**: "VIP Customer" role only
   - **Priority**: High
   - **Pricing**: Premium (e.g., add $50 for white-glove service)

4. **Assign customers**:
   - Edit user → Change role to "VIP Customer"

5. **Result**: Only VIP customers see premium design option on applicable products

---

## Troubleshooting

### Issue 1: NBDesigner Not Appearing on Product Page

**Symptoms:** Product page doesn't show design/customize tab

**Solution:**
1. Go to product edit page
2. Scroll to **"NBD options"** meta box
3. Verify **"Enable custom design"** is checked
4. Verify a **Printing Option** is selected
5. Save product
6. Clear browser cache (Ctrl+Shift+Delete)
7. Visit product page in incognito window

**If still not working:**
- Check user permissions (must be logged in or anonymous allowed)
- Check date restrictions on Printing Option
- Check System Info (NBDesigner → System Info) for errors

### Issue 2: Designer Loads Slowly or Hangs

**Symptoms:** Designer takes >5 seconds to load, freezes when adding elements

**Solution:**
1. Check System Info for warnings
2. Reduce canvas size (smaller dimensions = faster)
3. Disable unnecessary cliparts categories
4. Clear `/wp-content/uploads/nbdesigner/temp/` folder
5. Upgrade server PHP to 7.4+

**Advanced:**
- Check browser console (F12) for JavaScript errors
- Check `/wp-content/uploads/nbdesigner/logs/` for errors

### Issue 3: Customers Can't Download Their Design

**Symptoms:** "Download" button missing or returns error

**Solution:**
1. Verify download formats enabled (Settings → Export formats)
2. Check if `/wp-content/uploads/nbdesigner/download/` folder is writable
3. Verify file permissions: should be 755 (writable by server)

**Check file permissions:**
```bash
ls -la /wp-content/uploads/nbdesigner/
```
Should show `rwxr-xr-x` for all folders.

### Issue 4: Custom Fonts Not Appearing in Designer

**Symptoms:** Uploaded fonts don't show in font list

**Solution:**
1. Go to Fonts Manager
2. Verify font is **not disabled**
3. Try upload again if corrupted
4. Clear browser cache
5. Test in private/incognito window

**Font file issues:**
- Supported: TTF, OTF, WOFF, WOFF2
- File must be <5MB
- Filename shouldn't have special characters

### Issue 5: Database Getting Large (Many Saved Designs)

**Symptoms:** Database slow, NBDesigner admin pages lag

**Solution:**
1. Go to **NBDesigner** → **Tools**
2. Look for **"Cleanup"** or **"Optimize"** option
3. Archive old customer designs (>1 year)
4. Delete temporary design files
5. Run database optimization

### Issue 6: Printing Option Not Showing on Product

**Symptoms:** Product has Printing Option selected, but it's not visible to customers

**Solution:**
1. Check Printing Option status: Not archived/disabled
2. Check date restrictions: Today within date range?
3. Check role restrictions: Current user role included?
4. Check product: Printing Option actually selected?

**Debug checklist:**
```
Product Edit Page:
├── [ ] "Enable custom design" = Checked
├── [ ] Printing Option = Selected (not blank)
├── [ ] Save/Update = Clicked
└── [ ] Cache = Cleared

Printing Option:
├── [ ] Status = Active (not archived)
├── [ ] Start date = Today or earlier
├── [ ] End date = Today or later
├── [ ] Visible to = Current user role included
└── [ ] Priority = Not conflicting with another option
```

---

## API Keys & External Services

### Optional Integration Services

NBDesigner can integrate with external services for enhanced functionality. These require API keys.

### Service: Google Photos

**Purpose:** Let customers search and import their Google Photos into designs

**Setup:**
1. Go to **Settings** → **API Keys**
2. Find **"Google Photos API"**
3. Enter Google API Key
4. Customers can now import photos

### Service: Pixabay Images

**Purpose:** Free stock images for designs (millions available)

**Setup:**
1. Go to **Settings** → **API Keys**
2. Find **"Pixabay API"**
3. Enter Pixabay API Key (get free key from pixabay.com)
4. Customers can search Pixabay

### Service: Unsplash Images

**Purpose:** High-quality free stock photos

**Setup:**
1. Go to **Settings** → **API Keys**
2. Find **"Unsplash API"**
3. Enter Unsplash API Key
4. Customers can search Unsplash

### Service: Pexels Images

**Purpose:** Curated stock photos

**Setup:**
1. Go to **Settings** → **API Keys**
2. Find **"Pexels API"**
3. Enter Pexels API Key
4. Customers can search Pexels

### Service: Flaticon Vectors

**Purpose:** Vector icons and illustrations

**Setup:**
1. Go to **Settings** → **API Keys**
2. Find **"Flaticon API"**
3. Enter Flaticon API Key
4. Customers can search Flaticon

### Service: Instagram & Facebook

**Purpose:** Let customers import from social media profiles

**Setup:**
1. Create app on Facebook Developer Console
2. Get API credentials
3. Go to **Settings** → **API Keys**
4. Enter credentials
5. Customers can link accounts

### How to Get API Keys

Each service provides free API keys:

| Service | Website | Key Type | Limit |
|---------|---------|----------|-------|
| Pixabay | pixabay.com/api | Free Key | 5000/hr |
| Unsplash | unsplash.com/developers | Free Key | 50/hr |
| Pexels | pexels.com/api | Free Key | 200/hr |
| Flaticon | flaticon.com/api | Freemium | Limited free |
| Google | console.developers.google.com | OAuth 2.0 | Custom |
| Facebook | developers.facebook.com | App Key | Custom |

---

## Staff Roles & Permissions

### WordPress Roles vs. NBDesigner Capabilities

**Standard WordPress Roles:**
- Administrator (full access)
- Editor (manage content)
- Author (create content)
- Contributor (submit content)
- Subscriber (read-only)

**NBDesigner Specific Capabilities:**

#### `manage_nbd_setting`
**Allows:** Access to General Settings page
**Typical roles:** Administrator
**Use case:** Configure plugin-wide settings

#### `manage_nbd_product`
**Allows:** Enable/disable NBDesigner on products, edit Printing Options
**Typical roles:** Administrator, Editor
**Use case:** Staff managing which products have design features

#### `manage_nbd_art`
**Allows:** Add/edit/delete cliparts
**Typical roles:** Administrator, Designer role
**Use case:** Content creators adding design elements

#### `manage_nbd_font`
**Allows:** Add/edit/delete fonts
**Typical roles:** Administrator
**Use case:** Managing typography options

#### `manage_nbd_tool`
**Allows:** Access Tools section (cleanup, database optimization)
**Typical roles:** Administrator
**Use case:** Maintenance tasks

### Recommended Staff Setup

#### Admin User (Full Access)
```
Roles: Administrator
Capabilities:
├── manage_nbd_setting ✓
├── manage_nbd_product ✓
├── manage_nbd_art ✓
├── manage_nbd_font ✓
└── manage_nbd_tool ✓
```

#### Product Manager (Product Setup Only)
```
Roles: Editor
Capabilities:
├── manage_nbd_setting ✗
├── manage_nbd_product ✓
├── manage_nbd_art ✗
├── manage_nbd_font ✗
└── manage_nbd_tool ✗
```

#### Design Curator (Cliparts & Templates)
```
Roles: Contributor/Author
Capabilities:
├── manage_nbd_setting ✗
├── manage_nbd_product ✗
├── manage_nbd_art ✓
├── manage_nbd_font ✗
└── manage_nbd_tool ✗
```

### Assigning Capabilities

**Using a Role Management Plugin (Recommended):**
1. Install plugin like "User Role Editor"
2. Go to **Users** → **Roles & Capabilities**
3. Edit specific role
4. Enable/disable NBDesigner capabilities
5. Save

**Manual Assignment (Advanced):**
See [Roles & Permissions section](#roles-permissions)

---

## Database Tables Reference

### For Technical Support

NBDesigner creates these custom database tables:

#### `wp_nbdesigner_templates`
- **Purpose:** Stores design templates
- **Key fields:** id, name, category, template_data, thumbnail_url
- **Size:** Usually small (< 1MB)

#### `wp_nbdesigner_mydesigns`
- **Purpose:** Stores customer saved designs
- **Key fields:** id, product_id, customer_id, design_data, created_date
- **Size:** Can grow large (100MB+) with many customers
- **Maintenance:** Archive old designs regularly

#### `wp_nbdesigner_user_designs`
- **Purpose:** Customer design folders/organization
- **Key fields:** id, user_id, folder_name
- **Size:** Usually small

#### `wp_nbdesigner_options`
- **Purpose:** Plugin configuration and settings
- **Key fields:** option_name, option_value, autoload
- **Size:** Small (< 100KB)

### Database Optimization

Regular maintenance keeps NBDesigner fast:

1. **Weekly:** Run System Info check
2. **Monthly:** Run database optimization (Tools section)
3. **Quarterly:** Archive designs > 1 year old
4. **Yearly:** Full database backup and cleanup

---

## Quick Reference Cheat Sheet

### Common URLs

| Page | URL |
|------|-----|
| Admin Settings | /wp-admin/admin.php?page=nbdesigner_general |
| Products Manager | /wp-admin/admin.php?page=nbdesigner_product |
| Cliparts Manager | /wp-admin/admin.php?page=nbdesigner_art |
| Fonts Manager | /wp-admin/admin.php?page=nbdesigner_font |
| Printing Options | /wp-admin/admin.php?page=nbdesigner_option |
| System Info | /wp-admin/admin.php?page=nbdesigner_system_info |
| Tools | /wp-admin/admin.php?page=nbdesigner_tools |

### Shortcodes

```
Display template gallery:
[nbdesigner_gallery row="6" per_row="5"]

Display design studio:
[nbdesigner_studio]

Login redirect (custom page):
[nbd_loggin_redirect]

Display products with NBDesigner:
[nbd_product limit="8" columns="4"]

Display specific template collection:
[nbd_template limit="10" per_row="2"]
```

### File Locations

```
Plugin root: /wp-content/plugins/web-to-print-online-designer/
Config: /wp-content/plugins/web-to-print-online-designer/includes/
Frontend: /wp-content/plugins/web-to-print-online-designer/views/
Assets: /wp-content/uploads/nbdesigner/
└── fonts/
└── cliparts/
└── designs/
└── temp/
└── logs/
```

### Important Files

| File | Purpose |
|------|---------|
| `/nbdesigner.php` | Main plugin file |
| `/includes/settings/general.php` | Settings structure |
| `/views/nbdesigner-frontend-modern.php` | Frontend designer interface |
| `/assets/css/custom.css` | Project-specific CSS |
| `/assets/js/custom.js` | Custom JavaScript hooks |

---

## Support & Resources

### Need Help?

1. **Check System Info** (NBDesigner → System Info)
   - Shows PHP version, WordPress version
   - Lists any warnings/errors

2. **Check Logs**
   - Location: `/wp-content/uploads/nbdesigner/logs/`
   - Contains error messages from the system

3. **Browser Console** (F12 in Chrome/Firefox)
   - Check for JavaScript errors
   - Often shows the exact problem

### Common Errors & Solutions

**Error: "Can't save design"**
- Check file permissions on `/wp-content/uploads/nbdesigner/`
- Verify disk space available

**Error: "Fonts not loading"**
- Check font files exist in `/wp-content/uploads/nbdesigner/fonts/`
- Verify file permissions are readable

**Error: "Canvas not rendering"**
- Check browser compatibility (Chrome recommended)
- Clear browser cache
- Try incognito window

**Error: "Can't add to cart"**
- Check WooCommerce integration
- Verify product price is set
- Check for JavaScript errors in console

---

## Checklists for Common Scenarios

### Checklist: Launch New E-commerce Site with NBDesigner

- [ ] Install NBDesigner plugin
- [ ] Go to Settings, configure general options
- [ ] Create basic Printing Option ("Standard")
- [ ] Create 3-5 test products
- [ ] Enable NBDesigner on test products
- [ ] Test designer on each product (multiple browsers)
- [ ] Configure API keys (optional)
- [ ] Upload product cliparts/templates
- [ ] Add Google Fonts (recommended)
- [ ] Create product categories
- [ ] Write product descriptions with design tips
- [ ] Test full purchase flow (design → cart → checkout)
- [ ] Train staff on common tasks
- [ ] Setup backup schedule for database

### Checklist: Add New Product Type

- [ ] Create product in WooCommerce
- [ ] Write product description
- [ ] Upload product images
- [ ] Set product price
- [ ] Create/select Printing Option
- [ ] Enable NBDesigner on product
- [ ] Test on frontend
- [ ] Create template designs (optional)
- [ ] Add related cliparts/vectors
- [ ] Update product category
- [ ] Test on multiple devices
- [ ] Announce to sales team

### Checklist: Troubleshoot Slow NBDesigner

- [ ] Check server PHP version (7.4+ recommended)
- [ ] Check database size (optimize if > 500MB)
- [ ] Reduce canvas size (smaller = faster)
- [ ] Reduce number of cliparts loaded at once
- [ ] Clear temporary files in `/wp-content/uploads/nbdesigner/temp/`
- [ ] Clear browser cache
- [ ] Test in incognito window
- [ ] Check for JavaScript errors (F12 console)
- [ ] Run System Info diagnostic
- [ ] Check web server error logs
- [ ] Consider upgrading hosting plan if persistent

---

## Document Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Nov 2024 | Initial comprehensive guide |

---

**For questions, contact your system administrator or refer to the built-in NBDesigner System Info page.**

Last Updated: November 11, 2024
