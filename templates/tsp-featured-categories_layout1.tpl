<div id="category" style="padding:10px 0px">
	{if $hide_desc == 'N'}
		<div class="row">
			<div class="6u">	 
				<img src="{$image}" width="{$thumb_width}" height="{$thumb_height}" style="width:{$thumb_width}px; height:{$thumb_height}px;"/>
			</div>
			<div class="6u">
				<span class="title"><a href="{$url}" title="{$title}">{$title}</a></span>
				<br><span class="text">{$desc}</span>
			</div>
		</div>
	{else}
		<div>	 
			<img src="{$image}" width="{$thumb_width}" height="{$thumb_height}"/>
		</div>
		<div>
			<span class="title"><a href="{$url}" title="{$title}">{$title}</a></span>
			<br><span class="text">{$desc}</span>
		</div>
	{/if}
</div> 
