<p>Changing the default post options below allows you to place <code>[tsp-featured-categories]</code> shortcode tag into any post or page with these options.</p>
<p>However, if you wish to add different options to the <code>[tsp-featured-categories]</code> shortcode please use the following settings:</p>
<ul style="list-style-type:square; padding-left: 30px;">
	<li>Title: <code>title="Title of Posts"</code></li>
	<li>Title Position: <code>title_pos="below"</code></li>
	<li>Number Categories: <code>number_cats="5"</code></li>
	<li>Category Taxonomy: <code>taxonomy="category"</code></li>
	<li>Shrink to Fit: <code>shrink_fit="Y"</code></li>
    <li>Category IDs: <code>cat_ids="5,3,4"</code></li>
	<li>Parent Category: <code>parent_cat="5"</code></li>
	<li>Category Type: <code>cat_type="all"</code>(Options: all, featured)</li>
	<li>Show Text Categories: <code>show_text_categories="Y"</code>(Options: Y, N)</li>
	<li>Hide Empty Categories: <code>hide_empty="Y"</code>(Options: Y, N)</li>
	<li>Hide Description: <code>hide_desc="N"</code>(Options: Y, N)</li>
	<li>Max Chars for Description: <code>max_desc="60"</code></li>
	<li>Layout: <code>layout="0"</code>(Options: 0, 1, 2)
		<ul style="padding-left: 30px;">
			<li>0: Image (left), Title, Text (right) [Horizontal]</li>
			<li>1: Image (left), Title, Text (right) [Vertical]</li>
			<li>2: Scrolling Gallery [Horizontal]</li>
		</ul>
	</li>
	<li>Box Width: <code>box_width="500"</code></li>
	<li>Box Height: <code>box_height="300"</code></li>
	<li>Order By: <code>order_by="none"</code>(Options: none,name,date,count,ID)</li>
	<li>Thumbnail Width: <code>thumb_width="80"</code></li>
	<li>Thumbnail Height: <code>thumb_height="80"</code></li>
	<li>HTML Tag Before Title: <code>before_title="&lt;h3&gt;"</code></li>
	<li>HTML Tag After Title: <code>after_title="&lt;/h3&gt;"</code></li>
</ul>
<hr>
A shortcode with all the options will look like the following:<br><br>
<code>[tsp-featured-categories title="Featured Categories" title_pos="below" number_cats="3" taxonomy="category" shrink_fit="Y" cat_ids="5,3,4" cat_type="all" show_text_categories="Y" hide_empty="Y" hide_desc="N" max_desc="60" layout="0" parent_cat="3" box_width=500 box_height=300 order_by="count" thumb_width="80" thumb_height="80" before_title="" after_title=""]</code>

