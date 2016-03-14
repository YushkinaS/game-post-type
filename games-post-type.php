<?php
/*
Plugin Name: games post type

*/
add_action( 'init', 'cptui_register_my_cpts' );
function cptui_register_my_cpts() {
	$labels = array(
		"name" => "Games",
		"singular_name" => "Game",
		);

	$args = array(
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"show_ui" => true,
		"has_archive" => true,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "games", "with_front" => true ),
		"query_var" => true,
						
	);
	register_post_type( "games", $args );

// End of cptui_register_my_cpts()
}


add_action( 'init', 'cptui_register_my_taxes' );
function cptui_register_my_taxes() {

	$labels = array(
		"name" => "genre",
		"label" => "Genres",
		);

	$args = array(
		"labels" => $labels,
		"hierarchical" => true,
		"label" => "Genres",
		"show_ui" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'genre', 'with_front' => true ),
		"show_admin_column" => false,
	);
	register_taxonomy( "genre", array( "games" ), $args );


	$labels = array(
		"name" => "platform",
		"label" => "Platforms",
		);

	$args = array(
		"labels" => $labels,
		"hierarchical" => true,
		"label" => "Platforms",
		"show_ui" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'platform', 'with_front' => true ),
		"show_admin_column" => false,
	);
	register_taxonomy( "platform", array( "games" ), $args );

	
	$labels = array(
		"name" => "game",
		"label" => "Games",
		);

	$args = array(
		"labels" => $labels,
		"hierarchical" => false,
		"label" => "Games",
		"show_ui" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'game', 'with_front' => true ),
		"show_admin_column" => false,
		//"meta_box_cb" => "post_categories_meta_box",
		"meta_box_cb" => "display_game_meta_box",
		"show_in_quick_edit" => false,
	);
	register_taxonomy( "game", array( "post" ), $args );
// End cptui_register_my_taxes
}

function display_game_meta_box( $post ) {
	


			
			
	$taxonomy = get_taxonomy( 'game' );
	$tax_name = 'game';
    $user_can_assign_terms = current_user_can( $taxonomy->cap->assign_terms )
	
	
	?>





	<select data-placeholder="игры"  multiple  name='game33[]' class="chosen-select" style="width:100%;">	
		<option value=""></option>
<?php
	$post_games=get_the_terms($post->ID,'game');  
	$ids_for_exclude='';
	foreach ( $post_games as $game ) {
		echo '<option selected value='.$game->name.'>'.$game->name.'</option>';
		$ids_for_exclude.=$game->term_id.',';
	}
	
	$args = array(
		'hide_empty'        => false, 
		'exclude'           => $ids_for_exclude

	); 
	
	$games=get_terms('game',$args); 	
		
	foreach ( $games as $game ) {
		echo '<option value='.$game->name.'>'.$game->name.'</option>';
	}
?> 
 </select>
 

<?php
	

	
}

add_action( 'save_post', 'save_game_tax' );
function save_game_tax( $ID ) {
	if ( isset( $_POST['game33'] ) && $_POST['game33'] != '' ) {
		wp_set_post_terms( $ID, $_POST['game33'] , 'game', false );
	}

}

add_filter ('the_content', 'dop_fields');
function dop_fields($content) {

			$games=get_the_terms($post->ID,'game'); 
			$home_url=get_option('home');
			$dop='';
		
			foreach ( $games as $game ) {
				$dop.= "<a href='$home_url/games/$game->name'>$game->name</a> "; 
			}
			
			if ($dop<>"") {
				$dop = "Игра: ".$dop;
			}
			
			$content.=$dop;
			return $content;
}


//add_filter( 'template_include','game_include_template_function', 1 );
function game_include_template_function( $template_path ) {
if ( get_post_type() == 'games' ) {
//	if ( $theme_file = locate_template( array( 'single-games.php' ) ) ) {
//		$template_path = $theme_file;
//	} 
	if ( is_single() ) {
		// checks if the file exists in the theme first,
		// otherwise serve the file from the plugin
			if ( $theme_file = locate_template( array( 'single-games.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . '/single-games.php';
			}
	} 
	elseif ( is_archive() ) {
        if ( $theme_file = locate_template( array ( 'archive-games.php' ) ) ) {
			$template_path = $theme_file;
		} else { $template_path = plugin_dir_path( __FILE__ ) . '/archive-games.php';
           }
      }
}
return $template_path;

}


add_action( 'publish_games', 'create_term_for_new_game' ,10,2);
function create_term_for_new_game($ID, $post ) {
	wp_create_term(urldecode($post->post_name),'game');
}


add_action('wp_enqueue_scripts', 'site_scripts_and_styles');
function site_scripts_and_styles() {
	
		wp_enqueue_script(
		'jquery-1-6-4',
		'https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js'
	
	);
	
	wp_enqueue_script(
		'jquery-ui',
		'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',
		array('jquery')
	);
	
	wp_enqueue_script(
		'chosen',
		plugin_dir_url( __FILE__ ).'chosen/chosen.jquery.js',
		array('jquery-1-6-4')
	);	
	
	wp_enqueue_script(
		'custom-script',
		plugin_dir_url( __FILE__ ).'script.js',
		array('jquery-ui')
	);

	wp_enqueue_style(
    	'smoothness',
    	'//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css'
	);
	
	wp_enqueue_style(
		'games-post-type',
		plugin_dir_url( __FILE__ ).'style.css'
	);
	
	wp_enqueue_style(
		'chosen',
		plugin_dir_url( __FILE__ ).'chosen/chosen.css'
	);
}

add_action( 'admin_enqueue_scripts', 'admin_scripts_and_styles' );
function admin_scripts_and_styles() {
	
	wp_enqueue_script(
		'jquery-ui',
		'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',
		array('jquery')
	);
	
	wp_enqueue_script(
		'jquery-1-6-4',
		'https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js'
	
	);
		
	wp_enqueue_script(
		'chosen',
		plugin_dir_url( __FILE__ ).'chosen/chosen.jquery.js',
		array('jquery-1-6-4')
	);	
	


	wp_enqueue_script(
		'custom-script',
		plugin_dir_url( __FILE__ ).'script.js',
		array('jquery-ui','chosen')
	);
	
	wp_enqueue_style(
		'chosen',
		plugin_dir_url( __FILE__ ).'chosen/chosen.css'
	);
}





