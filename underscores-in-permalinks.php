<?php
    /*
    Plugin Name: Underscores in Permalinks
    Plugin URI: https://github.com/joeywohleb/underscores-in-permalinks
    Description: Wordpress plugin for using underscores instead of dashes in permalinks.
    Author: Joey Wohleb
    Version: 0.1.0
    Author URI: http://joeywohleb.com/
    */

    /**
     * Sanitizes a title, replacing whitespace and a few other characters with underscores.
     *
     * Limits the output to alphanumeric characters, underscore (_) and dash (-).
     * Whitespace becomes an underscore.
     *
     * Replaces the sanitize_title_with_dashes() function in includes/formatting.php
     *
     * @since 1.2.0
     *
     * @param string $title The title to be sanitized.
     * @param string $raw_title Optional. Not used.
     * @param string $context Optional. The operation for which the string is sanitized.
     * @return string The sanitized title.
     */
    function uip_sanitize_title_with_underscores( $title, $raw_title = '', $context = 'display' ) {
    $title = strip_tags($title);
    // Preserve escaped octets.
    $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
    // Remove percent signs that are not part of an octet.
    $title = str_replace('%', '', $title);
    // Restore octets.
    $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

    if (seems_utf8($title)) {
        if (function_exists('mb_strtolower')) {
            $title = mb_strtolower($title, 'UTF-8');
        }
        $title = utf8_uri_encode($title, 200);
    }

    $title = strtolower($title);
    $title = preg_replace('/&.+?;/', '', $title); // kill entities
    $title = str_replace('.', '_', $title);

    if ( 'save' == $context ) {
        // Convert nbsp, ndash and mdash to hyphens
        $title = str_replace( array( '%c2%a0', '%e2%80%93', '%e2%80%94' ), '_', $title );

        // Strip these characters entirely
        $title = str_replace( array(
            // iexcl and iquest
            '%c2%a1', '%c2%bf',
            // angle quotes
            '%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
            // curly quotes
            '%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
            '%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
            // copy, reg, deg, hellip and trade
            '%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
            // acute accents
            '%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
            // grave accent, macron, caron
            '%cc%80', '%cc%84', '%cc%8c',
        ), '', $title );

        // Convert times to x
        $title = str_replace( '%c3%97', 'x', $title );
    }

    $title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
    $title = preg_replace('/\s+/', '_', $title);
    $title = preg_replace('|-+|', '_', $title);
    $title = trim($title, '_');

    return $title;
}

add_filter('sanitize_title', 'uip_sanitize_title_with_underscores', 10, 3);

?>