<ul class="RecentBlogPostsElement">
	<% if ShowCommentsOnly %>
		<% control RecentBlogPosts %>
			<% if Comment %>
			<li <% if FirstLast %>class="$FirstLast"<% end_if %> >
				<h4 class="title"><a href="$Link" title="$Parent.Title">$Parent.Title</a></h4>
				<span class="text">$Comment.FirstSentence &hellip;</span>
				<p class="links sans-serif"><a href="$Link" class="readmore" title="Read the Full Post">Read more</a></p>
			</li>
			<% end_if %>
			<p>$NoPosts</p>
		<% end_control %>
	<% else %>
		<% control RecentBlogPosts %>
			<% if Content %>
			<li <% if FirstLast %>class="$FirstLast"<% end_if %> >
				<h4 class="title"><a href="$Link" title="$Title">$Title</a></h4>
				<span class="text">$Content.FirstSentence &hellip;</span>
				<p class="links sans-serif"><a href="$Link#PageComments_holder" class="comments" title="View Comments for this post"><% if Comments.Count %>$Comments.Count <% end_if %> Comments</a> | <a href="$Link" class="readmore" title="Read the Full Post">Read more</a></p>
			</li>
			<% end_if %>
			<p>$NoPosts</p>
		<% end_control %>
	<% end_if %>
</ul>
