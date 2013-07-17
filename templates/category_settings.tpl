<link rel="stylesheet" href="{$stylesheet}" type="text/css" media="screen" />
<div id="tsp-featured-categories" class="postbox">
<h3 class='handle'><span>{$title}</h3>
<div class="inside">

<input value="tspfc_edit" type="hidden" name="tspfc_edit" />	
<input type="hidden" name="image_field" id="image_field" value="" />
<table class="form-table">
  <tr>
    <th scope="row" valign="top">
		<label for="{$field_prefix}_category">{$subtitle}</label>
    </th>
    <td valign="top">
		<select name="{$field_prefix}_category" id="{$field_prefix}_category">
			<option value=0 {if !$featured}selected="selected"{/if}>No</option>
			<option value=1 {if $featured}selected="selected"{/if}>Yes</option>
		</select>
    </td>
  </tr>
  <tr>
    <th rowspan="2" valign="top" scope="row">
		<label for="{$field_prefix}">Category Thumbnail</label>    
	</th>
    <td align="left" valign="top">
    	<div name="{$field_prefix}_selected_image"  id="{$field_prefix}_selected_image" class="tsp-featured-categories-selected_image">
      		{if $cur_image != ''}<img src="{$cur_image}" />{/if}
    	</div>
    </td>
  </tr>
  <tr>
    <td valign="top">
    	<div name="{$field_prefix}_url_display" id="{$field_prefix}_url_display" class="tsp-featured-categories-url_display">
      		{if $cur_image != ''}{$cur_image}{else}No image selected{/if}
    	</div>
        <img src="images/media-button-image.gif"
			alt="Add photos from your media" /> 
		<a href="#"
		   onclick="store_image_field('{$field_prefix}')"
		   class="thickbox" title="Add an Image"> <strong>Click here to add/change your image</strong>
        </a><br />
      <small>Note: To choose image click the "insert into post" button in the media uploader</small><br />
      
      <img src="images/media-button-image.gif" 
			alt="Remove existing image" /> 
	  <a href="#"
		onclick="remove_image_url('{$field_prefix}', 'No image selected')">
        <strong>Click here to remove the existing image</strong>
        </a><br />
      <input type="hidden" name="{$field_prefix}"
			id="{$field_prefix}"
			value="{$cur_image}" />
    </td>
  </tr>
</table>
</div>
</div>
<script>
{literal}
jQuery(document).ready(function() {
 
	window.send_to_editor = function(html) {
	  
		imgurl = jQuery('img',html).attr('src');
		save_image_url(imgurl);
		tb_remove();
	}
 
})
{/literal}
</script>
