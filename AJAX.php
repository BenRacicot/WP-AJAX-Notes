<?php

Bad practice:  // header.php 
function custom_head(){ echo '<script type="text/javascript">var ajaxurl = \''.admin_url('admin-ajax.php').'\';</script>'; }

Good practice: // where you enqueue your script - send it your admin-ajax.php url too!
function my_theme_js_init() { 

	wp_enqueue_script('my_theme_init', get_stylesheet_directory_uri() . '/library/js/init.js', array('jquery'));
	wp_localize_script( 'my_theme_init', 'My_Obj', array( 
		'ajaxurl' => admin_url( 'admin-ajax.php' )
		) 
	); // create nonce here

}
add_action('wp_enqueue_scripts', 'my_theme_js_init');



?>


<script type="text/javascript"> // JAVASCRIPT
// Ben's ajax JS functions
$(window).load(function(){

	var myOBJ = []; // must create empty object var
  var count = 1; 

  // Next button
		// $('#ajaxnext').on('click', function(){ bad practice for dynamicly created button types
		$(document).on('click', '#ajaxnext', function(){ // looks to the dom for a selector
	  count++;
		myOBJ = {
			action: 'pages',
			data: "page_no=" + count,
			count:count
		};
 	  loadArticles(myOBJ);
  }); 

	Prev button
  	// $('#ajaxprev').on('click', function(){ bad practice for dynamicly created button types
		$(document).on('click', '#ajaxprev', function(){ // looks to the dom for a selector
  	if(count != 1) {count--};
		myOBJ = {
			action: 'pages',
			data: "page_no=" + count,
			count:count
		};
 	  loadArticles(myOBJ);
  }); 

  // Query
  function loadArticles(pageNumber){
    $.ajax({
        url: ajaxurl, // defined in header.php
        type:'POST',
        data: myOBJ, 
          beforeSend: function() {
	     $('#loader').show();
	  },
	  complete: function(){
	     $('#loader').hide();
	  },
        success: function(html){
	     	  $('.firstrow').empty(); // emtpy the node
	     	  $('.firstrow').append(html); // append the returned data to the node
        }
    });
		return false;
  }
	// Query on initial load
	myOBJ = {
		action: 'pages',
		data: "page_no=" + count,
		count:count
	};
	loadArticles(myOBJ);

});
// End Ben's JS ajax functions
</script>




<?php // PHP (functions.php)

add_action('wp_ajax_pages', '_custom_paginate');           // for logged in user
add_action('wp_ajax_nopriv_pages', '_custom_paginate');    // if user not logged in

function _custom_paginate(){ 

	$paged = $_POST['count'];
	//$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

	$args = array(
	  'posts_per_page'  => 12,
	  'category'				=> 3,
	  'paged' 					=> $paged, 
	  'post_status'     => 'publish'
	); 

  $query = new WP_Query($args);

	  if ( $query->have_posts() ) :
	  while ($query->have_posts()) : $query->the_post(); ?>
	  <div class="content">
	  	<?php // loop code here ?>
	  </div>


		<?php endwhile; endif; exit;
}// End AJAX




