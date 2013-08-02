<style>
#{$field_prefix}_box h3 {
	font-size:12px;
	font-weight:bold;
	line-height:1;
	margin:0;
	padding:7px 9px;
}
</style>
<div id="{$field_prefix}_box" class="postbox">
	<h3 class='handle'><span>{$title}</h3>
	<div class="inside">
		{foreach $form_fields as $field}
			{include file="$EASY_DEV_FORM_FIELDS" field=$field}
		{/foreach}
	</div>
</div>
