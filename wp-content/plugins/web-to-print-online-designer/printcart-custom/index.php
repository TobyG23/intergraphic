<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('Printcart_Custom')) {

    class Printcart_Custom
    {
        protected static $instance;
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function __construct() {
            //todo
        }
        public function init(){

        }
    }
}
$printcart_custom = Printcart_Custom::instance();
$printcart_custom->init();