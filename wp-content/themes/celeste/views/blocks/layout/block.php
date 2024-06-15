<?php

add_action( 'wp_head', function() {

?>
    <!-- Celeste Version <?php echo wp_get_theme()->get( 'Version' ) ?> -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
<?php

} );