<?php
/*
Plugin Name: WCK CTC
Description: Creates Custom taxonomies
*/

/* include Custom Fields Creator API */
require_once('wordpress-creation-kit-api/wordpress-creation-kit.php');

/* Create the WCK Page */

$args = array(							
			'page_title' => 'Wordpress Creation Kit',
			'menu_title' => 'WCK',
			'capability' => 'edit_theme_options',
			'menu_slug' => 'wck-page',									
			'page_type' => 'menu_page',
			'position' => 30,
			'priority' => 12
		);
new WCK_CTC_WCK_Page_Creator( $args );

add_action('admin_menu', 'wck_ctc_remove_wck_submanu_page', 14);
function wck_ctc_remove_wck_submanu_page(){	
	remove_submenu_page( 'wck-page', 'wck-page' );
}

/* Create the CTC Page */
$args = array(							
			'page_title' => 'WCK Taxonomy Creator',
			'menu_title' => 'Taxonomy Creator',
			'capability' => 'edit_theme_options',
			'menu_slug' => 'ctc-page',									
			'page_type' => 'submenu_page',
			'parent_slug' => 'wck-page',
			'priority' => 13			
		);
new WCK_CTC_WCK_Page_Creator( $args );


add_action( 'init', 'wck_ctc_create_box', 11 );

function wck_ctc_create_box(){

	$args = array(
			'public'   => true
		);
	$output = 'objects'; // or objects
	$post_types = get_post_types($args,$output);
	$post_type_names = array(); 
	foreach ($post_types  as $post_type ) {
		if ( $post_type->name != 'attachment' && $post_type->name != 'wck-meta-box' ) 
			$post_type_names[] = $post_type->name;
	}
	
	
	$ct_creation_fields = array( 
		array( 'type' => 'text', 'title' => 'Taxonomy', 'description' => '(The name of the taxonomy. Name must not contain capital letters or spaces.)', 'required' => true ),			
		array( 'type' => 'text', 'title' => 'Singular Label', 'required' => true, 'description' => 'ex. Writer' ),
		array( 'type' => 'text', 'title' => 'Plural Label', 'required' => true, 'description' => 'ex. Writers' ),
		array( 'type' => 'checkbox', 'title' => 'Attach to', 'options' => $post_type_names ),
		array( 'type' => 'select', 'title' => 'Hierarchical', 'options' => array( 'false', 'true' ), 'default' => 'false', 'description' => 'Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.' ),

		array( 'type' => 'text', 'title' => 'Search Items', 'description' => 'ex. Search Writers' ),
		array( 'type' => 'text', 'title' => 'Popular Items', 'description' => 'ex. Popular Writers' ),
		array( 'type' => 'text', 'title' => 'All Items', 'description' => 'ex. All Writers' ),
		array( 'type' => 'text', 'title' => 'Parent Item', 'description' => 'ex. Parent Genre' ),
		array( 'type' => 'text', 'title' => 'Parent Item Colon', 'description' => 'ex. Parent Genre:' ),
		array( 'type' => 'text', 'title' => 'Edit Item', 'description' => 'ex. Edit Writer' ),
		array( 'type' => 'text', 'title' => 'Update Item', 'description' => 'ex. Update Writer' ),
		array( 'type' => 'text', 'title' => 'Add New Item', 'description' => 'ex. Add New Writer'),		
		array( 'type' => 'text', 'title' => 'New Item Name', 'description' => 'ex. New Writer Name' ),
		array( 'type' => 'text', 'title' => 'Separate Items With Commas', 'description' => 'ex. Separate writers with commas' ),
		array( 'type' => 'text', 'title' => 'Add Or Remove Items', 'description' => 'ex. Add or remove writers' ),
		array( 'type' => 'text', 'title' => 'Choose From Most Used', 'description' => 'ex. Choose from the most used writers' ),
		array( 'type' => 'text', 'title' => 'Menu Name' ),	
		
		array( 'type' => 'select', 'title' => 'Public', 'options' => array( 'false', 'true' ), 'default' => 'true', 'description' => 'Meta argument used to define default values for publicly_queriable, show_ui, show_in_nav_menus and exclude_from_search' ),
		array( 'type' => 'select', 'title' => 'Show UI', 'options' => array( 'false', 'true' ), 'default' => 'true', 'description' => 'Whether to generate a default UI for managing this post type.' ),
		array( 'type' => 'select', 'title' => 'Show Tagcloud', 'options' => array( 'false', 'true' ), 'default' => 'true', 'description' => 'Whether to allow the Tag Cloud widget to use this taxonomy.' )
	);

	$args = array(
		'metabox_id' => 'ctc_creation_box',
		'metabox_title' => 'Custom Taxonomy Creation',
		'post_type' => 'ctc-page',
		'meta_name' => 'wck_ctc',
		'meta_array' => $ct_creation_fields,	
		'context' 	=> 'option'
	);


	new WCK_CTC_Wordpress_Creation_Kit( $args );
}

add_action( 'init', 'wck_ctc_create_taxonomy' );

function wck_ctc_create_taxonomy(){
	$cts = get_option('wck_ctc');
	if( !empty( $cts ) ){
		foreach( $cts as $ct ){
			
			$labels = array(
				'name' => _x( $ct['plural-label'], 'taxonomy general name'),
				'singular_name' => _x( $ct['singular-label'], 'taxonomy singular name'),
				'search_items' => __( $ct['search-items'] ? $ct['search-items'] : 'Search '.$ct['plural-label'] ),
				'popular_items' => __( $ct['popular-items'] ? $ct['popular-items'] : "Popular ".$ct['plural-label'] ),
				'all_items' => __( $ct['all-items'] ? $ct['all-items'] : "All ".$ct['plural-label'] ) ,
				'parent_item' => __( $ct['parent-item'] ? $ct['parent-item'] : "Parent ".$ct['singular-label']),
				'parent_item_colon' => __( $ct['parent-item-colon'] ? $ct['parent-item-colon'] : "Parent ".$ct['singular-label'].':' ),
				'edit_item' => __( $ct['edit-item'] ? $ct['edit-item'] : "Edit ".$ct['singular-label']),
				'update_item' => __( $ct['update-item'] ? $ct['update-item'] : "Update ".$ct['singular-label']),
				'add_new_item' =>  __( $ct['add-new-item'] ? $ct['add-new-item'] : "Add New ". $ct['singular-label'] ),
				'new_item_name' => __( $ct['new-item-name'] ? $ct['new-item-name'] :  "New ". $ct['singular-label']. ' Name' ), 
				'separate_items_with_commas' => __( $ct['separate-items-with-commas'] ? $ct['separate-items-with-commas'] :  "Separate  ". strtolower( $ct['plural-label'] ). ' with commas' ), 
				'add_or_remove_items' => __( $ct['add-or-remove-items'] ? $ct['add-or-remove-items'] : "Add or remove " .strtolower( $ct['plural-label'] ) ),
				'choose_from_most_used' => __( $ct['choose-from-most-used'] ? $ct['choose-from-most-used'] : "Choose from the most used " .strtolower( $ct['plural-label'] ) ),				
				'menu_name' => $ct['menu-name'] ? $ct['menu-name'] : $ct['plural-label']
			);
			
			$args = array(
				'labels' => $labels,
				'public' => $ct['public'] == 'false' ? false : true,								
				'show_ui' => $ct['show-ui'] == 'false' ? false : true, 								
				'hierarchical' => $ct['hierarchical'] == 'false' ? false : true,
				'show_tagcloud' => $ct['show-tagcloud'] == 'false' ? false : true
			);

			if( !empty( $ct['attach-to'] ) )
				$object_type = explode( ', ', $ct['attach-to'] );
			else 
				$object_type = '';
			
			register_taxonomy( $ct['taxonomy'], $object_type, $args );
		}
	}
}

/* Flush rewrite rules */
add_action('init', 'ctc_flush_rules', 20);
function ctc_flush_rules(){
	if( isset( $_GET['page'] ) && $_GET['page'] == 'ctc-page' && isset( $_GET['updated'] ) && $_GET['updated'] == 'true' )
		flush_rewrite_rules( false  );
}

/* add refresh to page */
add_action("wck_refresh_list_wck_ctc", "wck_ctc_after_refresh_list");
add_action("wck_refresh_entry_wck_ctc", "wck_ctc_after_refresh_list");
function wck_ctc_after_refresh_list(){
	echo '<script type="text/javascript">window.location="'. get_admin_url() . 'admin.php?page=ctc-page&updated=true' .'";</script>';
}

/* advanced labels container for add form */
add_action( "wck_before_add_form_wck_ctc_element_5", 'wck_ctc_form_label_wrapper_start' );
function wck_ctc_form_label_wrapper_start(){
	echo '<li><a href="javascript:void(0)" onclick="jQuery(\'#ctc-advanced-label-options-container\').toggle(); if( jQuery(this).text() == \'Show Advanced Label Options\' ) jQuery(this).text(\'Hide Advanced Label Options\');  else if( jQuery(this).text() == \'Hide Advanced Label Options\' ) jQuery(this).text(\'Show Advanced Label Options\');">Show Advanced Label Options</a></li>';
	echo '<li id="ctc-advanced-label-options-container" style="display:none;"><ul>';
}

add_action( "wck_after_add_form_wck_ctc_element_17", 'wck_ctc_form_label_wrapper_end' );
function wck_ctc_form_label_wrapper_end(){
	echo '</ul></li>';	
}

/* advanced options container for add form */
add_action( "wck_before_add_form_wck_ctc_element_18", 'wck_ctc_form_wrapper_start' );
function wck_ctc_form_wrapper_start(){
	echo '<li><a href="javascript:void(0)" onclick="jQuery(\'#ctc-advanced-options-container\').toggle(); if( jQuery(this).text() == \'Show Advanced Options\' ) jQuery(this).text(\'Hide Advanced Options\');  else if( jQuery(this).text() == \'Hide Advanced Options\' ) jQuery(this).text(\'Show Advanced Options\');">Show Advanced Options</a></li>';
	echo '<li id="ctc-advanced-options-container" style="display:none;"><ul>';
}

add_action( "wck_after_add_form_wck_ctc_element_20", 'wck_ctc_form_wrapper_end' );
function wck_ctc_form_wrapper_end(){
	echo '</ul></li>';	
}

/* advanced label options container for update form */
add_filter( "wck_before_update_form_wck_ctc_element_5", 'wck_ctc_update_form_label_wrapper_start', 10, 2 );
function wck_ctc_update_form_label_wrapper_start( $form, $i ){
	$form .=  '<li><a href="javascript:void(0)" onclick="jQuery(\'#ctc-advanced-label-options-update-container-'.$i.'\').toggle(); if( jQuery(this).text() == \'Show Advanced Label Options\' ) jQuery(this).text(\'Hide Advanced Label Options\');  else if( jQuery(this).text() == \'Hide Advanced Label Options\' ) jQuery(this).text(\'Show Advanced Label Options\');">Show Advanced Label Options</a></li>';
	$form .= '<li id="ctc-advanced-label-options-update-container-'.$i.'" style="display:none;"><ul>';
	return $form;
}

add_filter( "wck_after_update_form_wck_ctc_element_17", 'wck_ctc_update_form_label_wrapper_end', 10, 2 );
function wck_ctc_update_form_label_wrapper_end( $form, $i ){
	$form .=  '</ul></li>';
	return $form;
}

/* advanced options container for update form */
add_filter( "wck_before_update_form_wck_ctc_element_18", 'wck_ctc_update_form_wrapper_start', 10, 2 );
function wck_ctc_update_form_wrapper_start( $form, $i ){
	$form .=  '<li><a href="javascript:void(0)" onclick="jQuery(\'#ctc-advanced-options-update-container-'.$i.'\').toggle(); if( jQuery(this).text() == \'Show Advanced Options\' ) jQuery(this).text(\'Hide Advanced Options\');  else if( jQuery(this).text() == \'Hide Advanced Options\' ) jQuery(this).text(\'Show Advanced Options\');">Show Advanced Options</a></li>';
	$form .= '<li id="ctc-advanced-options-update-container-'.$i.'" style="display:none;"><ul>';
	return $form;
}

add_filter( "wck_after_update_form_wck_ctc_element_20", 'wck_ctc_update_form_wrapper_end', 10, 2 );
function wck_ctc_update_form_wrapper_end( $form, $i ){
	$form .=  '</ul></li>';	
	return $form;
}


/* advanced label options container for display */
add_filter( "wck_before_listed_wck_ctc_element_5", 'wck_ctc_display_label_wrapper_start', 10, 2 );
function wck_ctc_display_label_wrapper_start( $form, $i ){
	$form .=  '<li><a href="javascript:void(0)" onclick="jQuery(\'#ctc-advanced-label-options-display-container-'.$i.'\').toggle(); if( jQuery(this).text() == \'Show Advanced Labels\' ) jQuery(this).text(\'Hide Advanced Labels\');  else if( jQuery(this).text() == \'Hide Advanced Labels\' ) jQuery(this).text(\'Show Advanced Labels\');">Show Advanced Labels</a></li>';
	$form .= '<li id="ctc-advanced-label-options-display-container-'.$i.'" style="display:none;"><ul>';
	return $form;
}

add_filter( "wck_after_listed_wck_ctc_element_17", 'wck_ctc_display_label_wrapper_end', 10, 2 );
function wck_ctc_display_label_wrapper_end( $form, $i ){
	$form .=  '</ul></li>';	
	return $form;
}

/* advanced options container for display */
add_filter( "wck_before_listed_wck_ctc_element_18", 'wck_ctc_display_adv_wrapper_start', 10, 2 );
function wck_ctc_display_adv_wrapper_start( $form, $i ){
	$form .=  '<li><a href="javascript:void(0)" onclick="jQuery(\'#ctc-advanced-options-display-container-'.$i.'\').toggle(); if( jQuery(this).text() == \'Show Advanced Options\' ) jQuery(this).text(\'Hide Advanced Options\');  else if( jQuery(this).text() == \'Hide Advanced Options\' ) jQuery(this).text(\'Show Advanced Options\');">Show Advanced Options</a></li>';
	$form .= '<li id="ctc-advanced-options-display-container-'.$i.'" style="display:none;"><ul>';
	return $form;
}

add_filter( "wck_after_listed_wck_ctc_element_20", 'wck_ctc_display_adv_wrapper_end', 10, 2 );
function wck_ctc_display_adv_wrapper_end( $form, $i ){
	$form .=  '</ul></li>';	
	return $form;
}

/* Add side metaboxes */
add_action('add_meta_boxes', 'wck_ctc_add_side_boxes' );
function wck_ctc_add_side_boxes(){
	add_meta_box( 'wck-ctc-side', 'Wordpress Creation Kit', 'wck_ctc_side_box_one', 'wck_page_ctc-page', 'side', 'high' );
}
function wck_ctc_side_box_one(){
	?>
		<iframe src="http://www.cozmoslabs.com/iframes/cozmoslabs_plugin_iframe.php?origin=<?php echo get_option('home'); ?>" width="260" id="wck-iframe"></iframe>
		<script type="text/javascript">			
			var onmessage = function(e) {
				if( e.origin == 'http://www.cozmoslabs.com' )
					jQuery('#wck-iframe').height(e.data);			
			}
			if(window.postMessage) {
				if(typeof window.addEventListener != 'undefined') {
					window.addEventListener('message', onmessage, false);
				}
				else if(typeof window.attachEvent != 'undefined') {
					window.attachEvent('onmessage', onmessage);
				}
			}			
		</script>
	<?php
}

/* Contextual Help */
add_action('load-wck_page_ctc-page', 'wck_ctc_help');

function wck_ctc_help () {    
    $screen = get_current_screen();

    /*
     * Check if current screen is wck_page_cptc-page
     * Don't add help tab if it's not
     */
    if ( $screen->id != 'wck_page_ctc-page' )
        return;

    // Add help tabs
    $screen->add_help_tab( array(
        'id'	=> 'wck_ctc_overview',
        'title'	=> __('Overview'),
        'content'	=> '<p>' . __( 'WCK Custom Taxonomy Creator allows you to easily create custom taxonomy for Wordpress without any programming knowledge.<br />Most of the common options for creating a taxonomy are displayed by default while the advanced options and label are just one click away.' ) . '</p>',
    ) );
	
	$screen->add_help_tab( array(
        'id'	=> 'wck_ctc_labels',
        'title'	=> __('Labels'),
        'content'	=> '<p>' . __( 'For simplicity you are required to introduce only the Singular Label and Plural Label from wchich the rest of the labels will be formed.<br />For a more detailed control of the labels you just have to click the "Show Advanced Label Options" link and all the availabel labels will be displayed' ) . '</p>',
    ) );
	
	$screen->add_help_tab( array(
        'id'	=> 'wck_ctc_advanced',
        'title'	=> __('Advanced Options'),
        'content'	=> '<p>' . __( 'The Advanced Options are set to the most common defaults for taxonomies. To display them click the "Show Advanced Options" link.' ) . '</p>',
    ) );
}
?>