<?php
/*
Plugin Name: tab Leihausrüstung
Description: Custom Post Rentals
Version: 0.1
Author: Flo Gesell
Author URI: https://flogesell.de
*/

// Call an instance from our class
$plugin_start = new tabRentals();

class tabRentals {

	public function __construct() {
        // registriert den neuen custom post type
        add_action( 'init', array( $this, 'tab_rentals' ) );

        add_action( 'init', array( $this, 'tab_create_the_rental_taxonomy' ) );

        // Shortcode für die Ausgabe aller Mitarbeiter
        add_shortcode( 'alle-projekte', array( $this, 'rentals_shortcode' ) );

        // Custom Single Page Design for Rentals
        add_filter('single_template', array( $this, 'custom_post_type_single_mapping' ));
	}

    public function tab_rentals()  {

        $labels = array(
            'name'               => _x( 'Leihequipment', 'post type general name' ),
            'singular_name'      => _x( 'Leihequipment', 'post type singular name' ),
            'add_new'            => __( 'Neues Leihequipment anlegen'),
            'add_new_item'       => __( 'Neues Leihequipment anlegen' ),
            'edit_item'          => __( 'Leihequipment bearbeiten' ),
            'new_item'           => __( 'Neues Leihequipment' ),
            'all_items'          => __( 'Alle Leihsachen' ),
            'view_item'          => __( 'Leihequipment ansehen' ),
            'search_items'       => __( 'Leihequipment durchsuchen' ),
            'not_found'          => __( 'Kein Leihequipment gefunden' ),
            'not_found_in_trash' => __( 'Kein Leihequipment im Papierkorb gefunden' ),
            'parent_item_colon'  => '',
            'menu_name'          => 'Leihequipment'
        );

        // args for the new post_type
        $args = array(
                   // Sichtbarkeit des Post Types
                   'public'              => true,
                   // Standart Ansicht im Backend aktivieren (Wie Artikel / Seiten)
                   'show_ui'             => true,
                   // Soll es im Backend Menu sichtbar sein?
                   'show_in_menu'        => true,
                   // Menu Icon
                   'menu_icon' => 'dashicons-tag',
                   // Position im Menu
                   'menu_position'       => 5,
                   // Post Type in der oberen Admin-Bar anzeigen?
                   'show_in_admin_bar'   => true,
                   // in den Navigations Menüs sichtbar machen?
                   'show_in_nav_menus'   => true,
                    
                   // Hier können Berechtigungen in einem Array gesetzt werden
                   // oder die standart Werte post und page in form eines Strings gesetzt werden
                   'capability_type'     => 'post',
        
                   // Soll es im Frontend abrufbar sein?
                   'publicly_queryable'  => true,
        
                   // Soll der Post Type aus der Suchfunktion ausgeschlossen werden?
                   'exclude_from_search' => false,
        
                   // Welche Elemente sollen in der Backend-Detailansicht vorhanden sein?
                   'supports'            => array( 'title', 'editor','author', 'thumbnail', 'custom-fields', 'revisions' ),
        
                   // Soll der Post Type Archiv-Seiten haben?
                   'has_archive'         => false,
                   
                   // Soll man den Post Type exportieren können?
                   'can_export'          => false,
                    
                   // Slug unseres Post Types für die redirects
                   // dieser Wert wird später in der URL stehen
                   'rewrite'             => array('slug' => 'rentals' ),
                   'labels'              => $labels,
                   'show_in_rest'        => true,
        );

        register_post_type( 'tab_rentals', $args );
        
    }

    public function tab_create_the_rental_taxonomy() {  
        register_taxonomy(  
            'rental-category',  					// This is a name of the taxonomy. Make sure it's not a capital letter and no space in between
            'tab_rentals',        			
            array(  
                'hierarchical' => true,  
                'label' => 'Leihkategorie',  	
                'query_var' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'rental-category')
            )  
        );  
    }  

    public function rentals_shortcode() {

        ob_start();

        // Loop Argumente
        $args = array(
            'post_type'         => 'tab_rentals',
            'post_status'       => array( 'publish' ),
            'posts_per_page'    => -1
        );

        // Daten abfragen
        $loop = new WP_Query( $args );

        while ( $loop->have_posts() ) : $loop->the_post();
        // post id abfragen
        $post_id = get_the_ID();
        // abfrage unseres custom fields "position"
        $position = get_post_meta( $post_id, 'position' );

        // Template Ausgabe
        ?>
        <div class="col-md-4 text-center">
            <img style="max-height: 100px;" class="img-fluid mx-auto d-block rounded-circle" src="<?php echo get_the_post_thumbnail_url( $post_id, 'full' ) ?>">
                
            <span style="font-size: 1.5rem; font-weight: 700; color: #0e7c7b;" class="text-center"><?php echo get_the_title( $post_id ) ?></span>
            <p class="text-center">
                <b><?php echo $position[0] ?></b><br>
                <a href="<?php echo get_the_permalink( $post_id ) ?>" class="btn btn-tobi2" >Mehr erfahren</a>
            </p>
        </div>
        <?php
        // Ende unserer while-schleife
        endwhile;

        // stellt den ursprünglichen data Kontext wieder her
        wp_reset_postdata();

        return ob_get_clean();

    }

    public function custom_post_type_single_mapping($single) {

        global $post;

        /* Checks for single template by post type */
        if ( $post->post_type == 'tab_rentals' ) {
            if ( file_exists( plugin_dir_path( __FILE__ ) . '/rentals_single.php' ) ) {
                return plugin_dir_path( __FILE__ ) . '/rentals_single.php';
            }
        }

        return $single;
    }


}
?>