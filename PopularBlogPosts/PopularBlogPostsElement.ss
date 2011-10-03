<ul class="PopularBlogPostsElement">
		<% if PopularBlogPosts %>
				<% control PopularBlogPosts %>
				<li <% if FirstLast %>class="$FirstLast"<% end_if %> >
						<h4 class="title"><a href="$Link" title="$Title">$Title</a></h4>
						<p class="links sans-serif"><a href="$Link#PageComments_holder" class="comments" title="View Comments for this post">$Comments.Count Comments</a></p>
				</li>
				<% end_control %>
		<% else %>
		There are no blog posts yet.
		<% end_if %>
</ul>
