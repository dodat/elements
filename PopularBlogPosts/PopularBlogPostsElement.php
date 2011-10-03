<?php

class PopularBlogPostsElement extends Element
{
	static $db = array(
		'DisplayAmount' => 'Int',
		'BlogIdToShow' => 'Varchar'
	);
	static $defaults = array(
		'DisplayAmount' => 5
	);
	
	static $blog_posts;
	
	static $title = "Popular Posts";
	static $cmsTitle = "Popular Posts";
	static $description = "List of most popular blog posts, ranked by number of comments.";
	
	function PopularBlogPosts()	{
		//to prevent a call to Count from re-executing the DB query
		if ($this->blog_posts == null){
			//if blogId to show is greater than 0 show that particular blog's posts only
			if (isset($this->BlogIdToShow) && $this->BlogIdToShow > 0){
				$this->blog_posts = DataObject::get("BlogEntry", '`ParentID` = '.$this->BlogIdToShow);
			} else {
				//show all blog's posts
				$this->blog_posts = DataObject::get("BlogEntry");
			}
			if($this->blog_posts){
				foreach($this->blog_posts as $post)	{
					$post->Comments = $post->Comments();
				}
				$this->blog_posts->sort("Comments", "DESC");
			}
		}
		if($this->blog_posts){
			return $this->blog_posts->getRange(0, $this->DisplayAmount);
		} else {
			return;
		}
		
	}
	
	
	function getCMSFields()	{
		//list all the blog holders in the dropdown field as ID => BlogHolderTitle
		$blog_holders = DataObject::get('BlogHolder');
		$blog_holders_dropdown = $blog_holders->toDropDownMap('ID', 'Title');
		
		return new FieldSet(new NumericField('DisplayAmount', _t('PopularBlogPostsWidget.DISPLAYAMOUNT', 'The maximum number of popular blog posts to display.')),
							new DropdownField('BlogIdToShow', 'Select the blog to list the most recent posts', $blog_holders_dropdown, null, null, "All")
		);
	}
}
?>
