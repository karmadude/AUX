<?php
/*
Plugin Name:    AUX
Plugin URI:     http://github.com/karmadude/AUX
Description:    Run WordPress Auto Upgrade via XML-RPC
Version:        0.1
Author:         Liji Jinaraj
Author URI:     http://liji.jinaraj.com
 */
 
add_filter( 'xmlrpc_methods', 'addAuxXMLRPCMethods' );
function addAuxXMLRPCMethods( $methods ) {
    $methods['aux.autoUpgradeWP'] = 'aux_autoUpgradeWP';
    return $methods;
}
 
/**
 * Perform Auto Upgrade
 *
 * @param array $args Method parameters. Contains:
 *  - username
 *  - password
 *  - version
 *  - locale
 * @return array. Contains:
 *  - 'isAdmin'
 *  - 'url'
 *  - 'blogName'
 *  - 'status'
 */
function aux_autoUpgradeWP( $args ) {
    global $wp_xmlrpc_server;
    global $current_site;

    $wp_xmlrpc_server->escape($args);

    $username = $args[0];
    $password = $args[1];
    $version = $args[2];
    $locale = $args[3];

    if ( !$user = $wp_xmlrpc_server->login($username, $password) )
        return $wp_xmlrpc_server->error;

    if ( !current_user_can( 'update_core' ) )
        return new IXR_Error(1, __( 'You do not have sufficient permissions to update this site.' ));

    $upgradeStatus = aux_do_core_upgrade($version, $locale);

    if( $upgradeStatus instanceof IXR_Error) return $upgradeStatus;

    $response[] = array(
        'isAdmin'       => current_user_can('manage_options'),
        'url'           => get_option( 'home' ),
        'blogName'      => get_option( 'blogname' ),
        'status'        => $upgradeStatus
    );

    return $response;
}

/**
 * @see do_core_upgrade in wp-admin/upgrade-code.php
 */
function aux_do_core_upgrade($version, $locale='en_US') {
    global $wp_filesystem;

    $msg = '';

    $url = 'update-core.php?action=do-core-upgrade';
    $url = wp_nonce_url($url, 'upgrade-core');
    if ( false === ($credentials = request_filesystem_credentials($url, '', false, ABSPATH)))
        return new IXR_Error(2, "No file crendentials");

    $update = find_core_update( $version, $locale );
    if ( !$update )
        return new IXR_Error(3, "No updates found for $version and $locale");


    if ( ! WP_Filesystem($credentials, ABSPATH) ) {
        request_filesystem_credentials($url, '', true, ABSPATH); //Failed to connect, Error and request again
        return new IXR_Error(4,  'Failed to connect');
    }

    if ( $wp_filesystem->errors->get_error_code()) {
        foreach ( $wp_filesystem->errors->get_error_messages() as $message )
            $msg .= $message . "\n";
        return new IXR_Error(5, "File System Errors: $msg");
    }

    $result = wp_update_core($update, 'show_message');

    if ( is_wp_error($result) || true) {
        $msg = $result->get_error_message();
        if ('up_to_date' != $result->get_error_code())
            $msg .= "\n" . __('Installation Failed');
        return new IXR_Error(6, $msg);
    } else {
        $msg = __('WordPress updated successfully');
    }
    
    return $msg;
}