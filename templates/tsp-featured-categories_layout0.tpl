{if $first_cat}<div class="row">{/if}
	<div id="category" style="float:left; width:{$cat_width}; padding:10px 10px 10px 0px">
		<div>	 
			<a href="{$url}" title="{$title}">
			 <img src="{$image}" width="{$thumb_width}" height="{$thumb_height}" style="width:{$thumb_width}px; height:{$thumb_height}px;" border="0"/>
			</a>
		</div>
		<div>
			<span class="title"><a href="{$url}" title="{$title}">{$title}</a></span>
			{if $hide_desc == 'N'}
				<br/><span class="text">{$desc}</span>
			{/if}
		</div>
	</div>   
{if $last_cat}
	<div style="clear:left;"></div>
</div>
{/if}
