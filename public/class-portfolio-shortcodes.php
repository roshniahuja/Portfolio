<?php
/**
 * Shortcode to display Portfolio and filters.
 */
//[recent-posts posts="2"]
function recent_posts_function($atts){
   extract(shortcode_atts(array(
      'posts' => 1,
   ), $atts));

   $return_string = '<ul>';
   query_posts(array('post_type' => 'portfolio','orderby' => 'date', 'order' => 'DESC' , 'showposts' => $posts));
   if (have_posts()) :
      while (have_posts()) : the_post();
      	$image_id = get_post_meta( get_the_ID(), '_image_id', true );
      	$portfolio_text = get_post_meta( get_the_ID(), 'portfolio_text', true );
	     $image_src = wp_get_attachment_url( $image_id );
         $return_string .= '<li><a href="javascript:void(0);" class="trigger_popup_fricc">'.get_the_title().'</a> <div class="hover_bkgr_fricc" style="display:none;">
		    <span class="helper"></span>
		    <div>
		        <div class="popupCloseButton">&times;</div>';
		        if($image_src)
		        {
		        	$return_string .= '<img src="'.$image_src.'">';
		        }
		        if($portfolio_text){
		        	$return_string .= '<p>'.__('Additional info','portfolio').' :'.$portfolio_text.'</p>';
		        }
		    $return_string .= get_the_content().'</div>
		</div></li>
        ';
      endwhile;
   endif;
   $return_string .= '</ul>';

   wp_reset_query();
   return $return_string;
}
add_shortcode('recent-posts', 'recent_posts_function');
//[get-post-img show='false']
function getPostwithImage($atts) {
		$return_string = '<ul>';
   		query_posts(array('post_type' => 'portfolio','orderby' => 'date', 'order' => 'DESC' ));
	    if (have_posts()) :
	      while (have_posts()) : the_post();
	      	$image_id = get_post_meta( get_the_ID(), '_image_id', true );
	      	$image_src = wp_get_attachment_url( $image_id );
	      	if($atts["show"] == "true") {
				$return_string .= '<li>';
				if($image_src){
					$return_string .= '<img src="'.$image_src.'">';
				}
					$return_string .= '<a href="javascript:void(0);" class="pop">'.get_the_title().'</a></li>';
			}else
			{
	         $return_string .= '<li><a href="javascript:void(0);" class="pop">'.get_the_title().'</a></li>';
			}
	      endwhile;
	      if($atts["page"] == "true") {
				the_posts_pagination( array(
					'mid_size'  => 2,
					'prev_text' => __( 'Back', 'textdomain' ),
					'next_text' => __( 'Onward', 'textdomain' ),
				) );
			}
	    endif;
	    $return_string .= '</ul>';

	    wp_reset_query();
	    return $return_string;

		
		
	}
add_shortcode('get-post-img', 'getPostwithImage');
function portfolio_posts_function(){
  
   $return_string = '<ul>';
   query_posts(array('post_type' => 'portfolio','orderby' => 'date', 'order' => 'DESC' , 'posts_per_page' => 5));
   if (have_posts()) :
      while (have_posts()) : the_post();
      	$image_id = get_post_meta( get_the_ID(), '_image_id', true );
      	$portfolio_text = get_post_meta( get_the_ID(), 'portfolio_text', true );
	     $image_src = wp_get_attachment_url( $image_id );
         $return_string .= '<li><a href="javascript:void(0);" class="trigger_popup_fricc">'.get_the_title().'</a> <div class="hover_bkgr_fricc" style="display:none;">
		    <span class="helper"></span>
		    <div>
		        <div class="popupCloseButton">&times;</div>';
		        if($image_src)
		        {
		        	$return_string .= '<img src="'.$image_src.'">';
		        }
		        if($portfolio_text){
		        	$return_string .= '<p>'.__('Additional info','portfolio').' :'.$portfolio_text.'</p>';
		        }
		    $return_string .= get_the_content().'</div>
		</div></li>
        ';
      endwhile;
      
   endif;
   $return_string .= '</ul>';

   wp_reset_query();
   return $return_string;
}
add_shortcode('portfolio-posts', 'portfolio_posts_function');
?>