{if $first_cat}
<div id="makeMeScrollable" style="width: {$box_width}px; height: {$box_height}px;">
	<div class="scrollingHotSpotLeft"></div>
	<div class="scrollingHotSpotRight"></div>
	<div class="scrollWrapper">
		<div class="scrollableArea">
{/if}
			<div class="img" style="width: {$thumb_width}px; height: {$thumb_height}px;">
				<img src="{$image}" width="100%" height="100%"/>
				<div class="title" style="top:{$adj_thumb_height}px; width:{$thumb_width}px">
					<a href="{$url}" title="{$title}">{$title}</a>
				</div>
			</div>
{if $last_cat}
	    </div>																		
	</div>
</div>
{/if}
