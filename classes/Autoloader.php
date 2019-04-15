<?php

/**
 * WP HTML5 Outliner: Autoloader class
 * 
 * @package WP_HTML5_Outliner
 * @since   1.0.0
 */

namespace wph5o;

/**
 * Supports autoloading for a single namespace in a single class directory.
 *
 * @since 1.0.0
 */
class Autoloader {

    /**
     * Registers an autoloader.
     * 
     * @since 1.0.0
     */
    public static function register() {

        spl_autoload_register( function ( $class ) {

            $ns  = __NAMESPACE__;
            $dir = dirname( __FILE__ );

            $name = str_replace( $ns . '\\', DIRECTORY_SEPARATOR, $class );
            $file = $dir . $name . '.php';

            if ( file_exists( $file ) ) {

                require $file;

            }

        } );

    }

}

// Ensures register() is called wherever this class file is required.
Autoloader::register();