<?php
/**
 * Inter Graphics Print Child Theme
 *
 * @package InterGraphicsPrint
 * @since 1.0.0
 *
 * This is a professional child theme for the Salient WordPress theme
 * featuring custom branding with black, red, and white color scheme.
 */

// ============================================================================
// 1. ENQUEUE STYLES AND SCRIPTS
// ============================================================================

/**
 * Enqueue child theme styles
 *
 * @since 1.0.0
 */
add_action( 'wp_enqueue_scripts', 'intergraphicsprint_enqueue_styles', 100 );

function intergraphicsprint_enqueue_styles() {
	$theme_version = wp_get_theme()->get( 'Version' );

	// Main child theme stylesheet
	wp_enqueue_style(
		'intergraphicsprint-style',
		get_stylesheet_directory_uri() . '/style.css',
		array(),
		$theme_version
	);

	// Modular CSS files
	wp_enqueue_style(
		'intergraphicsprint-colors',
		get_stylesheet_directory_uri() . '/assets/css/colors.css',
		array(),
		$theme_version
	);

	wp_enqueue_style(
		'intergraphicsprint-typography',
		get_stylesheet_directory_uri() . '/assets/css/typography.css',
		array(),
		$theme_version
	);

	wp_enqueue_style(
		'intergraphicsprint-components',
		get_stylesheet_directory_uri() . '/assets/css/components.css',
		array(),
		$theme_version
	);

	wp_enqueue_style(
		'intergraphicsprint-layout',
		get_stylesheet_directory_uri() . '/assets/css/layout.css',
		array(),
		$theme_version
	);

	wp_enqueue_style(
		'intergraphicsprint-responsive',
		get_stylesheet_directory_uri() . '/assets/css/responsive.css',
		array(),
		$theme_version
	);

	// RTL support
	if ( is_rtl() ) {
		wp_enqueue_style(
			'salient-rtl',
			get_template_directory_uri() . '/rtl.css',
			array(),
			'1',
			'screen'
		);
	}

	// Child theme JavaScript
	wp_enqueue_script(
		'intergraphicsprint-main',
		get_stylesheet_directory_uri() . '/assets/js/main.js',
		array( 'jquery' ),
		$theme_version,
		true
	);
}

// ============================================================================
// 2. THEME SETUP
// ============================================================================

/**
 * Set up child theme localization
 *
 * @since 1.0.0
 */
add_action( 'after_setup_theme', 'intergraphicsprint_setup' );

function intergraphicsprint_setup() {
	load_child_theme_textdomain( 'intergraphicsprint', get_stylesheet_directory() . '/languages' );
}

// ============================================================================
// 3. SECURITY AND ADMIN SETTINGS
// ============================================================================

/**
 * Hide WordPress admin bar from frontend
 *
 * @since 1.0.0
 */
show_admin_bar( false );

/**
 * Disable Gutenberg block editor
 * Keep using classic editor for better compatibility
 *
 * @since 1.0.0
 */
add_filter( 'use_block_editor_for_post', '__return_false', 10 );
add_filter( 'use_widgets_block_editor', '__return_false', 10 );

// ============================================================================
// 4. MEDIA UPLOAD SETTINGS
// ============================================================================

/**
 * Allow SVG file uploads
 * Required for modern icon systems and vector graphics
 *
 * @param array $mimes Allowed MIME types
 * @return array Modified MIME types
 * @since 1.0.0
 */
function intergraphicsprint_custom_upload_mimes( $mimes = array() ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
}

add_action( 'upload_mimes', 'intergraphicsprint_custom_upload_mimes' );

// ============================================================================
// 5. UTILITIES AND HELPERS
// ============================================================================

/**
 * Display current year
 * Useful for copyright notices
 *
 * Usage: [current_year]
 *
 * @param array $atts Shortcode attributes
 * @return string Current year
 * @since 1.0.0
 */
function intergraphicsprint_current_year( $atts ) {
	return date( 'Y' );
}

add_shortcode( 'current_year', 'intergraphicsprint_current_year' );

/**
 * Get theme color
 *
 * @param string $color_name Color variable name
 * @return string Color value
 * @since 1.0.0
 */
function intergraphicsprint_get_color( $color_name = 'primary' ) {
	$colors = array(
		'primary'     => '#000000',    // Black
		'secondary'   => '#DC143C',    // Red
		'accent'      => '#FFFFFF',    // White
		'success'     => '#10B981',    // Green
		'warning'     => '#F59E0B',    // Amber
		'error'       => '#EF4444',    // Red
		'info'        => '#3B82F6',    // Blue
		'muted'       => '#9CA3AF',    // Gray
	);

	return isset( $colors[ $color_name ] ) ? $colors[ $color_name ] : $colors['primary'];
}

/**
 * Get theme primary color
 *
 * @return string Primary accent color
 * @since 1.0.0
 */
function intergraphicsprint_get_primary_color() {
	return intergraphicsprint_get_color( 'primary' );
}

/**
 * Get theme secondary color
 *
 * @return string Secondary accent color
 * @since 1.0.0
 */
function intergraphicsprint_get_secondary_color() {
	return intergraphicsprint_get_color( 'secondary' );
}

// ============================================================================
// 6. CUSTOM HEAD TAGS
// ============================================================================

/**
 * Add custom meta tags to site head
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'intergraphicsprint_custom_head_tags' );

function intergraphicsprint_custom_head_tags() {
	?>
	<!-- Theme Color -->
	<meta name="theme-color" content="#000000">
	<meta name="msapplication-navbutton-color" content="#000000">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">

	<!-- Mobile Web App -->
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<?php
}

// ============================================================================
// 7. CUSTOM LOGIN PAGE
// ============================================================================

/**
 * Customize WordPress login page
 *
 * @since 1.0.0
 */
add_action( 'login_enqueue_scripts', 'intergraphicsprint_custom_login_styles' );

function intergraphicsprint_custom_login_styles() {
	?>
	<style type="text/css">
		/* Login page branding */
		.login {
			background: #f5f5f5;
		}

		.login h1 a {
			background-image: url('<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/logo.png' ); ?>');
			background-size: contain;
			background-repeat: no-repeat;
			background-position: center;
			width: 320px !important;
			height: 110px !important;
			box-shadow: none !important;
		}

		.login form {
			background: #ffffff;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
		}

		.login input[type="text"],
		.login input[type="password"],
		.login input[type="email"] {
			border-color: #ddd;
			border-radius: 4px;
		}

		.login input[type="text"]:focus,
		.login input[type="password"]:focus,
		.login input[type="email"]:focus {
			border-color: #DC143C;
			box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.1);
		}

		.login .button-primary {
			background-color: #DC143C;
			border-color: #B91C1C;
			box-shadow: none;
		}

		.login .button-primary:hover {
			background-color: #B91C1C;
			border-color: #991B1B;
		}
	</style>
	<?php
}

// ============================================================================
// END OF FILE
// ============================================================================
