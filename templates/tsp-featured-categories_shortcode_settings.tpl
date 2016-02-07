<p>Changing the default post options below allows you to place <strong>[tsp-featured-categories]</strong> shortcode tag into any post or page with these options.</p>
<p>However, if you wish to add different options to the <strong>[tsp-featured-categories]</strong> shortcode please use the following settings:</p>
<ul style="list-style-type:square; padding-left: 30px;">
	<li>Title: <strong>title="Title of Posts"</strong></li>
	<li>Title Position: <strong>title_pos="below"</strong></li>
	<li>Number Categories: <strong>number_cats="5"</strong></li>
	<li>Shrink to Fit: <strong>shrink_fit="Y"</strong></li>
    <li>Category IDs: <strong>cat_ids="5,3,4"</strong></li>
	<li>Parent Category: <strong>parent_cat="5"</strong></li>
	<li>Category Type: <strong>cat_type="all"</strong>(Options: all, featured)</li>
	<li>Hide Empty Categories: <strong>hide_empty="Y"</strong>(Options: Y, N)</li>
	<li>Hide Description: <strong>hide_desc="N"</strong>(Options: Y, N)</li>
	<li>Max Chars for Description: <strong>max_desc="60"</strong></li>
	<li>Layout: <strong>layout="0"</strong>(Options: 0, 1, 2)
		<ul style="padding-left: 30px;">
			<li>0: Image (left), Title, Text (right) [Horizontal]</li>
			<li>1: Image (left), Title, Text (right) [Vertical]</li>
			<li>2: Scrolling Gallery [Horizontal]</li>
		</ul>
	</li>
	<li>Box Width: <strong>box_width="500"</strong></li>
	<li>Box Height: <strong>box_height="300"</strong></li>
	<li>Order By: <strong>order_by="none"</strong>(Options: none,name,date,count,ID)</li>
	<li>Thumbnail Width: <strong>thumb_width="80"</strong></li>
	<li>Thumbnail Height: <strong>thumb_height="80"</strong></li>
	<li>HTML Tag Before Title: <strong>before_title="&lt;h3&gt;"</strong></li>
	<li>HTML Tag After Title: <strong>after_title="&lt;/h3&gt;"</strong></li>
</ul>
<hr>
A shortcode with all the options will look like the following:<br><br>
<strong>[tsp-featured-categories title="Featured Categories" title_pos="below" number_cats="3" shrink_fit="Y" cat_ids="5,3,4" cat_type="all" hide_empty="Y" hide_desc="N" max_desc="60" layout="0" parent_cat="3" box_width=500 box_height=300 order_by="count" thumb_width="80" thumb_height="80" before_title="" after_title=""]</strong>

