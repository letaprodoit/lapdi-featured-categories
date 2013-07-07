<div id="category" style="padding:10px 0px">
	{if $hidedesc == 'N'}
		<div class="row">
			<div class="6u">	 
				<img src="{$image}" width="{$widththumb}" height="{$heightthumb}"/>
			</div>
			<div class="6u">
				<span class="title"><a href="{$url}" title="{$title}">{$title}</a></span>
				<br><span class="text">{$desc}</span>
			</div>
		</div>
	{else}
		<div>	 
			<img src="{$image}" width="{$widththumb}" height="{$heightthumb}"/>
		</div>
		<div>
			<span class="title"><a href="{$url}" title="{$title}">{$title}</a></span>
			<br><span class="text">{$desc}</span>
		</div>
	{/if}
</div> 
