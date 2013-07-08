//Extracted from fpg_scripts.js (Flash Picture Gallery Plugin) and modified for use here.
function save_image_url(add_image_url){
    view_image_url = "<img src='" + add_image_url + "' />";
       
    if (add_image_url == '') 
    	add_image_url = 'No image selected';
    	
    field = '';
    field = jQuery("#image_field").val();
    
    url_display_id = '#' + field + '_url_display';
    image_display_id = '#' + field + '_selected_image';
    
    jQuery(url_display_id).html(add_image_url);
	jQuery('#' + field).val(add_image_url);
	jQuery(image_display_id).html(view_image_url);
}

// Store the field that contains the image URL
function store_image_field(field){
	jQuery("#image_field").val(field);
	tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true&amp;height=500&amp;width=640');
}

// Remove the field name from the #image_field
function remove_image_url(field, message){
	url_display_id = '#' + field + '_url_display';
    image_display_id = '#' + field + '_selected_image';
    
    jQuery(url_display_id).html(message);
	jQuery('#' + field).val('');
	jQuery(image_display_id).html('');
}

// Store the image url from the media uploader into the form
jQuery(document).ready(function() {
 
	window.send_to_editor = function(html) {
	  
		imgurl = jQuery('img',html).attr('src');
		save_image_url(imgurl);
		tb_remove();
	}
 
})