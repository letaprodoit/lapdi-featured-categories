// Store the field that contains the image URL
function tspedev_save_image_url(image_url, field_id)
{
    selected_image = "<img src='" + image_url + "' />";
       
    url_display = image_url;
    
    if (image_url == '') 
    	url_display = 'No image selected';
    	
    image_id = '#' + field_id;
    
    prefix = jQuery(image_id + "_prefix").val();
    
    url_display_id = '#' + prefix + '_url_display';
    selected_image_id = '#' + prefix + '_selected_image';
    
	jQuery(image_id).val(image_url);
    jQuery(url_display_id).html(url_display);
	jQuery(selected_image_id).html(selected_image);
}//end tspedev_save_image_url
	
// Store the field that contains the image URL
function tspedev_show_media_window()
{
	tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true&amp;height=500&amp;width=640');
}//end tspedev_show_media_window

// Remove the field name from the #image_field
function tspedev_remove_image_url(field_id, message)
{
    image_id = '#' + field_id;

    prefix = jQuery(image_id + "_prefix").val();

    url_display_id = '#' + prefix + '_url_display';
    selected_image_id = '#' + prefix + '_selected_image';
    
	jQuery(image_id).val('');
    jQuery(url_display_id).html(message);
	jQuery(selected_image_id).html('');
}//end tspedev_remove_image_url
