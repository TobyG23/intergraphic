<?php 

add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles', 100);

function salient_child_enqueue_styles() {
		
		$nectar_theme_version = nectar_get_theme_version();
		wp_enqueue_style( 'salient-child-style', get_stylesheet_directory_uri() . '/style.css', '', $nectar_theme_version );
		
    if ( is_rtl() ) {
   		wp_enqueue_style(  'salient-rtl',  get_template_directory_uri(). '/rtl.css', array(), '1', 'screen' );
		}
}







// Leemos LANG desde el child theme para traducir ----------------------------------------------------------------------------------------------

add_action('after_setup_theme', 'child_lang_setup');

function child_lang_setup(){
   load_child_theme_textdomain('salient', get_stylesheet_directory() . '/lang');
}







// ocultar barra admin ----------------------------------------------------------------------------------------------

show_admin_bar( false );







// style login ----------------------------------------------------------------------------------------------
/*
function custom_login() { ?>
    <style type="text/css">
        .login h1 a {
            background-image: url('<?php echo get_stylesheet_directory_uri() ?>/images/logo.png') !important;
			width: 320px !important;
			height: 110px !important;
			background-size: contain !important;
			box-shadow: none !important;
		}
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'custom_login' );
*/






// Permitimos subir SVG a media ----------------------------------------------------------------------------------------------

function custom_upload_mimes($mimes = array()) {
	$mimes['svg'] = "image/svg+xml";
	return $mimes;
}

add_action('upload_mimes', 'custom_upload_mimes');







// Desactivamos Gutemberg post y widget blocks ----------------------------------------------------------------------------------------------

add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_widgets_block_editor', '__return_false', 10 );







// Añadimos al header de todas las paginas ----------------------------------------------------------------------------------------------
/*
function add_head() {
	
	?>

	<meta name="theme-color" content="#D1E9EE">
	<meta name="msapplication-navbutton-color" content="#D1E9EE">
	<meta name="apple-mobile-web-app-status-bar-style" content="#D1E9EE">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	
	<script>
		
		jQuery(document).ready(function(){
			
			// Add floating whatsapp button
			jQuery('#ajax-content-wrap').append('<div id="whatsapp-button"><a target="_blank" href="https://wa.me/34711039564?text=%C2%A1Hola!%20Quisiera%20consultar%20sobre%20uno%20de%20sus%20productos%20en%20TheVapeLab3.com"></a></div>');

		});
		
		jQuery(window).on( "load", function() {
			
			// Nothing yet
			
		});
		
	</script>
	
	<?php
	
};
add_action('wp_head', 'add_head');
*/






// Añadimos shortcode para mostrar año actual (copyright) ----------------------------------------------------------------------------

function current_year( $atts ) {
	$year = date("Y");
	return $year;
}
add_shortcode( 'current_year', 'current_year');







// End