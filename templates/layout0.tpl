{if $first_cat}<div class="row">{/if}
	<div id="category" style="float:left; width:{$cat_width}; padding:10px 10px 10px 0px">
		<div>	 
			<img src="{$image}" width="{$widththumb}" height="{$heightthumb}"/>
		</div>
		<div>
			<span class="title"><a href="{$url}" title="{$title}">{$title}</a></span>
			{if $hidedesc == 'N'}
				<br/><span class="text">{$desc}</span>
			{/if}
		</div>
	</div>   
{if $last_cat}
	<div style="clear:left;"></div>
</div>
{/if}
