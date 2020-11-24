<?php

/**
 * Registers the portfolio taxonomies: portfolio-Image, portfolio-category and optinally portfolio-tag
 * Hooked onto init
 *
 * @ignore
 * @access private
 * @since 1.0
 */
/**
 * Registers the portfolio post type.
 */
class Custom_Post_Type_Image_Upload {
	
	
	public function __construct() {
		
		add_action( 'init', array( &$this, 'init' ) );
		
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		}
	}
	
	
	/** Frontend methods ******************************************************/
	
	
	/**
	 * Register the custom post type
	 */
	public function init() {
	    register_post_type( 'portfolio', array( 'public' => true, 'label' => 'Portfolios' ) );
	     $labels = array(
	    'name' => _x( 'portfolio Type','portfolio' ),
	    'singular_name' => _x( 'portfolio Type','portfolio' ),
	    'search_items' =>  __( 'Search portfolio Types','portfolio' ),
	    'all_items' => __( 'All portfolio Types','portfolio' ),
	    'parent_item' => __( 'Parent portfolio Type','portfolio' ),
	    'parent_item_colon' => __( 'Parent portfolio Type:','portfolio' ),
	    'edit_item' => __( 'Edit portfolio Type','portfolio' ),
	    'update_item' => __( 'Update portfolio Type','portfolio' ),
	    'add_new_item' => __( 'Add New portfolio Type','portfolio' ),
	    'new_item_name' => __( 'New portfolio Type Name','portfolio' ),
	    'menu_name' => __( 'portfolio Types','portfolio' ),
	  );    
	  //Now register the taxonomy
	  register_taxonomy('portfolio_types',array('portfolio'), array(
	    'hierarchical' => true,
	    'labels' => $labels,
	    'show_ui' => true,
	    'show_admin_column' => true,
	    'query_var' => true,
	    'rewrite' => array( 'slug' => 'portfolio_type' ),
	  ));
	}
	
	
	/** Admin methods ******************************************************/
	
	
	/**
	 * Initialize the admin, adding actions to properly display and handle 
	 * the portfolio custom post type add/edit page
	 */
	public function admin_init() {
		global $pagenow;
		
		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
			
			add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
			add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );
			
			add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
		}
	}
	
	
	/**
	 * Save meta boxes
	 * 
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 */
	public function meta_boxes_save( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		if ( $post->post_type != 'portfolio' ) return;
			
		$this->process_portfolio_meta( $post_id, $post );
	}
	
	
	/**
	 * Function for processing and storing all portfolio data.
	 */
	private function process_portfolio_meta( $post_id, $post ) {
		update_post_meta( $post_id, '_image_id', $_POST['upload_image_id'] );
		update_post_meta( $post_id, 'portfolio_text', $_POST['portfolio_text'] );
	}
	
	
	/**
	 * Set a more appropriate placeholder text for the New portfolio title field
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == 'portfolio' ) return __( 'portfolio Title' );
		return $text;
	}
	
	
	/**
	 * Add and remove meta boxes from the edit page
	 */
	public function meta_boxes() {
		add_meta_box( 'portfolio-image', __( 'portfolio Image' ), array( &$this, 'portfolio_image_meta_box' ), 'portfolio', 'normal', 'high' );
	}
	
	
	/**
	 * Display the image meta box
	 */
	public function portfolio_image_meta_box() {
		global $post;
		
		$image_src = '';
		
		$image_id = get_post_meta( $post->ID, '_image_id', true );
		$portfolio_text = get_post_meta( $post->ID, 'portfolio_text', true );
		$image_src = wp_get_attachment_url( $image_id );
		
		?>
		
		<img id="portfolio_image" src="<?php echo $image_src ?>" style="max-width:100%;" />
		<input type="hidden" name="upload_image_id" id="upload_image_id" value="<?php echo $image_id; ?>" />
		<p>
			<a title="<?php esc_attr_e( 'Set portfolio image' ) ?>" href="#" id="set-portfolio-image"><?php _e( 'Set portfolio image' ) ?></a>
			<a title="<?php esc_attr_e( 'Remove portfolio image' ) ?>" href="#" id="remove-portfolio-image" style="<?php echo ( ! $image_id ? 'display:none;' : '' ); ?>"><?php _e( 'Remove portfolio image' ) ?></a>
		</p>
		<input type="text" placeholder="Portfolio Text" name="portfolio_text" value="<?php echo esc_html( $portfolio_text );?>" class="portfolio_text">
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			// save the send_to_editor handler function
			window.send_to_editor_default = window.send_to_editor;
	
			$('#set-portfolio-image').click(function(){
				
				// replace the default send_to_editor handler function with our own
				window.send_to_editor = window.attach_image;
				tb_show('', 'media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=true');
				
				return false;
			});
			
			$('#remove-portfolio-image').click(function() {
				
				$('#upload_image_id').val('');
				$('img').attr('src', '');
				$(this).hide();
				
				return false;
			});
			
			// handler function which is invoked after the user selects an image from the gallery popup.
			// this function displays the image and sets the id so it can be persisted to the post meta
			window.attach_image = function(html) {
				
				// turn the returned image html into a hidden image element so we can easily pull the relevant attributes we need
				$('body').append('<div id="temp_image">' + html + '</div>');
					
				var img = $('#temp_image').find('img');
				
				imgurl   = img.attr('src');
				imgclass = img.attr('class');
				imgid    = parseInt(imgclass.replace(/\D/g, ''), 10);
	
				$('#upload_image_id').val(imgid);
				$('#remove-portfolio-image').show();
	
				$('img#portfolio_image').attr('src', imgurl);
				try{tb_remove();}catch(e){};
				$('#temp_image').remove();
				
				// restore the send_to_editor handler function
				window.send_to_editor = window.send_to_editor_default;
				
			}
	
		});
		</script>
		<?php
	}
}

// finally instantiate our plugin class and add it to the set of globals
$GLOBALS['custom_post_type_image_upload'] = new Custom_Post_Type_Image_Upload();