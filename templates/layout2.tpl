{if $first_cat}
<div id="makeMeScrollable" style="width: {$widthbox}px; height: {$heightbox}px;">
	<div class="scrollingHotSpotLeft"></div>
	<div class="scrollingHotSpotRight"></div>
	<div class="scrollWrapper">
		<div class="scrollableArea">
{/if}
			<div class="img" style="width: {$widththumb}px; height: {$heightthumb}px;">
				<img src="{$image}" width="100%" height="100%"/>
				<div class="title" style="top:{math equation='x / 2' x=$heightthumb}px; width:{$widththumb}px">
					<a href="{$url}" title="{$title}">{$title}</a>
				</div>
			</div>
{if $last_cat}
	    </div>																		
	</div>
</div>
{/if}
