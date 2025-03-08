<?php
// Algemene functies voor Emigratie Portaal
function ep_get_setting( $key ) {
    return get_option( 'ep_' . $key );
}
?>