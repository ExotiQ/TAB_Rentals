<?php
/*
Plugin Name: TAB Leihequipment
Plugin URI: none
Description: Fügt Leihequipment hinzu
Version: 0.1
Author: Flo Gesell
Author URI: https://flogesell.de
*/

// Call an instance from our class
$plugin_start = new TABRentals();

class TABRentals {

	/*
	 * Constructor - the brain of our class
	 * */
	public function __construct() {

        // registriert den neuen custom post type
        add_action( 'init', array( $this, 'TAB_rentals' ) );

        add_action( 'init', array( $this, 'create_rentals_taxonomy' ) );

        // Shortcode für die Ausgabe aller Mitarbeiter
        add_shortcode( 'alle-projekte', array( $this, 'rentals_shortcode' ) );

        // Custom Single Page Design for rentals
        add_filter('single_template', array( $this, 'custom_post_type_single_mapping' ));

        wp_register_style('TABLeihequipment_dashicons', plugins_url( TABLeihequipment.'/css/TABLeihequipment.css'));
        wp_enqueue_style('TABLeihequipment_dashicons');

	}

    // https://wordpress.stackexchange.com/questions/35165/how-do-i-create-a-custom-role-capability


    // Add the new capability to all roles having a certain built-in capability
    
    public function TAB_rentals()  {

        $labels = array(
            'name'               => _x( 'Leihequipment', 'post type general name' ),
            'singular_name'      => _x( 'Leihequipment', 'post type singular name' ),
            'add_new'            => __( 'Neues Leihequipment anlegen'),
            'add_new_item'       => __( 'Neues Leihequipment anlegen' ),
            'edit_item'          => __( 'Leihequipment bearbeiten' ),
            'new_item'           => __( 'Neues Leihequipment' ),
            'all_items'          => __( 'Das Ganze Leihequipment' ),
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
                   'menu_icon' => 'dashicons-pistol',
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
                   'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' ),
        
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

        register_post_type( 'TAB_rentals', $args );
        
    }

    function create_rentals_taxonomy() {
 
         
          $labels = array(
            'name' => _x( 'Leihequipmentkategorien', 'taxonomy general name' ),
            'singular_name' => _x( 'Leihequipmentkategorie', 'taxonomy singular name' ),
            'search_items' =>  __( 'Durchsuche Leihequipmentkategorien' ),
            'all_items' => __( 'Alle Leihequipmentkategorien' ),
            'parent_item' => __( 'Übergeordnete Leihequipmentkategorie' ),
            'parent_item_colon' => __( 'Übergeordnete Leihequipmentkategorie:' ),
            'edit_item' => __( 'Bearbeite Leihequipmentkategorie' ), 
            'update_item' => __( 'Überschreibe Leihequipmentkategorien' ),
            'add_new_item' => __( 'Füge eine neue Leihequipmentkategorie hinzu' ),
            'new_item_name' => __( 'Neuer Leihequipmentkategoriename' ),
            'menu_name' => __( 'Leihequipmentkategorien' ),
          );    
         
        // Now register the taxonomy
          register_taxonomy('rentals', array('TAB_rentals'), array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'rentals' ),
          ));
         
    }

    public function rentals_shortcode() {

        ob_start();

        // Loop Argumente
        $args = array(
            'post_type'         => 'TAB_rentals',
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
        if ( $post->post_type == 'TAB_rentals' ) {
            if ( file_exists( plugin_dir_path( __FILE__ ) . '/rentals_single.php' ) ) {
                return plugin_dir_path( __FILE__ ) . '/rentals_single.php';
            }
        }

        return $single;
    }


}