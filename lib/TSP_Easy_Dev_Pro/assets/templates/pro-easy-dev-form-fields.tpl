<style>
.{$field_prefix}_form_element label{
	width: 220px;
	float: left;
	clear: left;
}

.{$field_prefix}_form_element {
	padding-bottom: 5px;
}
.{$field_prefix}_url_display {
    width: 100%; 
    background-color: #FFFFFF; 
    padding: 3px; 
    border: #c6d9e9 1px solid; 
    font-size: 13px;
}
.{$field_prefix}_image_info
 {
	position: relative;
    margin-left: 220px;
 }
</style>
<div class="{$field_prefix}_form_element" id="{$field.name}_container_div" style="">
	<label for="{$field.id}">{$field.label}</label>
	{if $field.type == 'INPUT'}
	   <input class="{$class}" id="{$field.id}" name="{$field.name}" value="{$field.value}" />
	{elseif $field.type == 'TEXTAREA'}
	   <textarea class="{$class}" id="{$field.id}" name="{$field.name}">{$field.value}</textarea>
	{elseif $field.type == 'SELECT'}
	   <select class="{$class}" id="{$field.id}" name="{$field.name}" >
	   		{foreach $field.options as $okey => $ovalue}
	   			<option class="level-0" value="{$ovalue}" {if $field.value == $ovalue}selected='selected'{/if}>{$okey}</option>
	   		{/foreach}
	   </select>
	{elseif $field.type == 'IMAGE'}
		<input type="hidden" id="{$field.id}" name="{$field.name}" value="{$field.value}" />
		<input type="hidden" id="{$field.id}_prefix" name="{$field.name}_prefix" value="{$field_prefix}_{$field.name}" />
    	
    	<div id="{$field_prefix}_{$field.name}_image_info" name="{$field_prefix}_{$field.name}_image_info" class="{$field_prefix}_image_info">
	    	<div id="{$field_prefix}_{$field.name}_selected_image" name="{$field_prefix}_{$field.name}_selected_image" class="{$field_prefix}_selected_image">
	      		{if $field.value != ''}<img src="{$field.value}" /><br/>{/if}
	    	</div>
	    	<div name="{$field_prefix}_{$field.name}_url_display" id="{$field_prefix}_{$field.name}_url_display" class="{$field_prefix}_url_display">
	      		{if $field.value != ''}{$field.value}{else}No image selected{/if}
	    	</div>
	    	
    		<div id="{$field_prefix}_{$field.name}_image_funcs" name="{$field_prefix}_{$field.name}_image_funcs" class="{$field_prefix}_image_funcs">
		        <img src="images/media-button-image.gif" alt="Add photos from your media" /> 
				
				<a href="#" onclick="tspedev_show_media_window()" class="thickbox" title="Add an Image"> <strong>Click here to add/change your image</strong></a><br />
				<small>Note: To choose image click the "insert into post" button in the media uploader</small><br />
				
				<img src="images/media-button-image.gif" alt="Remove existing image" /> 
				<a href="#" onclick="tspedev_remove_image_url('{$field.id}', 'No image selected')"><strong>Click here to remove the existing image</strong></a><br />
    		</div>
    	</div>
    	
   		<script>
			{literal}
			jQuery(document).ready(function() {
			 
				window.send_to_editor = function(html) {
				  
					imgurl = jQuery('img',html).attr('src');
			{/literal}
					field_id = "{$field.id}";
			{literal}
					tspedev_save_image_url(imgurl, field_id);
					tb_remove();
				}
			 
			})
			{/literal}
		</script>
	{/if}
	<div class="clear"></div>
	<div id="error-message-name"></div>
</div>

