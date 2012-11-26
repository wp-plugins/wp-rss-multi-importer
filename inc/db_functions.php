<?php



function delete_db_transients() {

    global $wpdb;

  
    $expired = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_wprssmi_%';" );

    foreach( $expired as $transient ) {

        $key = str_replace('_transient_', '', $transient);
        delete_transient($key);

    }
}


function list_the_plugins() {
    $plugins = get_option ( 'active_plugins', array () );
    foreach ( $plugins as $plugin ) {
        echo "<li>$plugin</li>";
    }
}


?>