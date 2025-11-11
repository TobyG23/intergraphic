<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<?php
$changelogs = $nbd_news->sections->changelog;
$faqs = $nbd_news->sections->faq;
?>

<?php do_action( 'pc_head', 'support' ); ?>

<div class="nbd-support-wrap">
    <section class="nbd-welcome-nav">
        <span class="nbd-welcome-nav__version">NBDESIGNER <?php echo NBDESIGNER_VERSION; ?></span>
        <ul>
            <li><a href="https://cmsmart.net/support_ticket" target="_blank"><?php esc_html_e( 'Support', 'web-to-print-online-designer' ); ?></a></li>
            <li><a href="https://cmsmart.net/community/woocommerce-online-product-designer-plugin/userguides" target="_blank"><?php esc_html_e( 'Documentation', 'web-to-print-online-designer' ); ?></a></li>
            <li><a href="https://cmsmart.net/community/woocommerce-online-product-designer-plugin" target="_blank"><?php esc_html_e( 'Community', 'web-to-print-online-designer' ); ?></a></li>
        </ul>        
    </section>
    <div class="nbd-logo">
        <img src="<?php echo NBDESIGNER_PLUGIN_URL; ?>/assets/images/logo.svg" alt="Storefront" />
    </div> 
    <div class="nbd-intro">
        <p><?php esc_html_e('Hello! You might be interested in the following NBDesigner NEWS and our printing solutions.', 'web-to-print-online-designer'); ?></p>
    </div>
    <div class="nbd-enhance">
        <div class="nbd-enhance__column nbd-change-log">
            <h3><?php esc_html_e( 'Change log', 'web-to-print-online-designer' ); ?></h3>
            <div class="nbd-change-log__wrap">
                <?php 
                    if( is_array( $changelogs ) ):
                    foreach ( $changelogs as $log ):
                    $date = new DateTime($log->created);    
                ?>
                <h4><?php echo( $log->version_number.' &#8211; '.$date->format('F j, Y') ); ?></h4>
                <div><?php echo( $log->descriptions ); ?></div>
                <?php 
                    endforeach;
                    endif; 
                ?>
            </div>
        </div>
        <div class="nbd-enhance__column nbd-faq">
            <h3><?php esc_html_e( 'FAQs', 'web-to-print-online-designer' ); ?></h3>
            <div class="nbd-faq__wrap">
                <?php 
                    if( is_array( $faqs ) ):
                    foreach ( $faqs as $faq ):   
                ?>
                <h4><?php echo( $faq->title ); ?></h4>
                <div><?php echo( $faq->description ); ?></div>
                <?php 
                    endforeach;
                    endif; 
                ?>
            </div>            
        </div>
        <div class="nbd-enhance__column nbd-other-product">
            <h3><?php esc_html_e( 'Printing solution', 'web-to-print-online-designer' ); ?></h3>
            <a href="https://cmsmart.net/tshirt-printing-store-ecommerce-website-with-online-designer" target="_blank"><img src="<?php echo NBDESIGNER_PLUGIN_URL; ?>/assets/images/t-shirt.jpg" /></a>
            <h4><a href="https://cmsmart.net/tshirt-printing-store-ecommerce-website-with-online-designer" target="_blank"><?php esc_html_e( 'T-SHIRT PRINTING SOLUTION', 'web-to-print-online-designer' ); ?></a></h4>
            <p><?php esc_html_e( 'You have a T-shirt printing business and you want your customers to have a great experience at your site.', 'web-to-print-online-designer' ); ?></p>
            <a href="https://cmsmart.net/wordpress-themes/wordpress-printshop-website-templates-with-online-design-packages" target="_blank"><img src="<?php echo NBDESIGNER_PLUGIN_URL; ?>/assets/images/print-solution.jpg" /></a>
            <h4><a href="https://cmsmart.net/wordpress-themes/wordpress-printshop-website-templates-with-online-design-packages" target="_blank"><?php esc_html_e( 'PRINTING ECOMMERCE SOLUTION', 'web-to-print-online-designer' ); ?></a></h4>
            <p><?php esc_html_e( 'You got a big printing business and you want to manage all things clean and clear.', 'web-to-print-online-designer' ); ?></p>            
        </div>      
    </div>
    <div class="nbd-project">
        <p>
            <?php printf( esc_html__( 'A %s project', 'web-to-print-online-designer' ), '<a href="http://netbaseteam.com/" target="_blank"><img src="' . NBDESIGNER_PLUGIN_URL . '/assets/images/netbaseteam.png" alt="Netbase Team" /></a>' ); ?>
        </p>
    </div>
</div>
