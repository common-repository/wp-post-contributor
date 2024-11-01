<?php
/*
Class Post Contributors
*/

if( !class_exists( "WP_Post_Contributors" ) ) {

	class WP_Post_Contributors {
		
		public function __construct() {
			
			// Add meta box
			add_action( 'add_meta_boxes', array( $this, 'wpc_add_meta_box' ) );
			
			// Save meta box value
			add_action( 'save_post', array( $this, 'wpc_save' ) );
			
			// Display contributors
			add_filter( 'the_content', array( $this, 'wpc_display' ) );
			
			// Include scripts and stylesheet
			add_action( 'wp_enqueue_scripts', array( &$this, 'wpc_assets' ) );
		}
		
		/*  Add Meta Box */
		public function wpc_add_meta_box( $post_type ) {
			$post_types = array('post');
			     
			if ( in_array( $post_type, $post_types ) ) {
				
				add_meta_box(
					'wpc_meta_box',
					'Contributors',
					array( $this, 'wpc_render_meta_box' ),
					$post_type,
					'side',
					'core'
				);
			}
		}
		
		/* Render Meta Box */
		public function wpc_render_meta_box( $post ) {

			wp_nonce_field( 'wpc_inner_custom_box', 'wpc_inner_custom_box_nonce' );
			$contributors = get_post_meta( $post->ID, '_wp_contributor', true );
			if( !empty($contributors) )
				$contributors = explode( ",", $contributors );
			else {
				$contributors = array();
			}
						
			$authors = get_users( 'orderby=nicename' );
			
			if( count( $authors ) > 0 ) {
				echo "<ul id='wp_contributor_list'>";
				foreach ( $authors as $author ) {
					if( in_array( $author->ID, $contributors ) )
						echo "<li> <input checked='checked' type='checkbox' name='wp_contributor[]' value='".$author->ID."' /> ". esc_html( $author->nickname) . "</li>";
					else
						echo "<li> <input type='checkbox' name='wp_contributor[]' value='".$author->ID."' /> ". esc_html( $author->nickname) . "</li>";
				}
				echo "</ul>";
			}
		}
		
		/* Save values of meta box */
		public function wpc_save( $post_id ) {
		
			if ( ! isset( $_POST['wpc_inner_custom_box_nonce'] ) )
				return $post_id;
		
			$nonce = $_POST['wpc_inner_custom_box_nonce'];
		
			if ( ! wp_verify_nonce( $nonce, 'wpc_inner_custom_box' ) )
				return $post_id;
		
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return $post_id;
		
			if ( ! current_user_can( 'edit_post', $post_id ) )
					return $post_id;

			$wp_contributor = sanitize_meta( $_POST['wp_contributor'] );
			
			if( isset( $wp_contributor ) && $wp_contributor != '' ) {
				
				update_post_meta( $post_id, '_wp_contributor', implode( ",", $wp_contributor ) );
			}
			else {
				update_post_meta( $post_id, '_wp_contributor', '' );
			}
		}
		
		/* Display Post Contributors */
		public function wpc_display( $content ) {
			
			global $post; $html = '';
			
			$html .= "<div class='wrapper-container'> <h4>Contributors</h4>";
			
			$contributors = get_post_meta( $post->ID, '_wp_contributor', true );
			
			if($contributors == '') return $content;
					
			$contributors = explode( ",", $contributors );
			
			$html .= "<ul class='mall-block-grid-6 contrib-list'> <div class = 'row'>";
			$html .= "<div class = 'small columns-12'>";

			foreach ( $contributors as $contributor ) {
				
				$contributor_info = get_userdata( $contributor );
				$html .= "<li class='img-wrapper'><a class = '' href='".get_author_posts_url( $contributor_info->ID )."'>". get_avatar( $contributor, 40 )."<p class = 'author-name'>".esc_html( $contributor_info->nickname ) . "</p> </a></li>";
			}
			
			$html .= "</div></div></ul>";
			$html .= "</div><div style='clear:both;'></div>";
			
			return $content.$html;
		}
		
		public function wpc_assets() {
			wp_enqueue_style( 'wp_contributor_style', plugin_dir_url( __FILE__ ) . 'css/wp-post-contributor.css' );
		}
	}
	
	new WP_Post_Contributors();
}

