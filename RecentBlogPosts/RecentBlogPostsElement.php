<?php


class RecentBlogPostsElement extends Element {
	
    static $db = array(
		'DisplayAmount' => 'Int',
		'BlogIdToShow' => 'Varchar',
		'ShowCommentsOnly' => 'Boolean'
	);
	
    static $defaults = array(
		'DisplayAmount' => 3,
		'BlogIdToShow' => 0,
		'ShowCommentsOnly' => false
	);
    
	public $blog_posts;
	
    static $NiceName = "Recent Posts";

	/**
	 * Returns the 'DisplayAmount' most recent blog posts for the blog referenced by the ID stored in 'BlogIdToShow'
	 */
    function RecentBlogPosts() {
		//if the blog posts have not been called yet
		if(!$this->blog_posts) {
			//get all blog entries corresponding to the selected blog holder, or all if blog holder == 0
			if (isset($this->BlogIdToShow) && $this->BlogIdToShow > 0) {
				$this->blog_posts = DataObject::get('BlogEntry', '`ParentID` = '.$this->BlogIdToShow, '`Date` DESC', null, $this->DisplayAmount);
			} else {
				//show all blog's posts
				$this->blog_posts = DataObject::get('BlogEntry', null, '`Date` DESC', null, $this->DisplayAmount);
			}
		}

		if ($this->ShowCommentsOnly) {
			$blog_post_ids = array();
			foreach ($this->blog_posts as $blog_post) {
				$blog_post_ids[] = $blog_post->ID;
			}
			
			$posts_ids_string = implode(",", $blog_post_ids);
			
			//$blog_post_comments = DataObject::get('PageComment', "`NeedsModeration` = '0' AND `IsSpam` = '0' AND `ParentID` IN (".$posts_ids_string.")", "`Created` DESC", null, $this->DisplayAmount);
			$blog_post_comments = DataObject::get('PageComment', "`NeedsModeration` = '0' AND `IsSpam` = '0'", "`Created` DESC", null, $this->DisplayAmount);
			
			if ($blog_post_comments) {
				return $blog_post_comments;
			} else {
				return new ArrayData(array('NoPosts'=>'There are no comments on any posts in this blog right now.'));
			}
		} else {
			if($this->blog_posts) {
				return $this->blog_posts;
			} else	{
				return new ArrayData(
					array(
						'NoPosts'=>'There are no blog posts for this blog right now.'
					)
				);
			}
		}
    }
    
    function getCMSFields()
    {
		//list all the blog holders in the dropdown field as ID => BlogHolderTitle
		$blog_holders = DataObject::get('BlogHolder');
		$blog_holders_dropdown = $blog_holders->toDropDownMap('ID', 'Title');
		
		/* CHECKBOXES DONT WORK IN WIDGETS AS OF 8-5-09 */
		
        return new FieldSet(
			new NumericField('DisplayAmount','The maximum number of posts / comments to display.'),
			new DropdownField('BlogIdToShow', 'Select the blog to list the most recent posts', $blog_holders_dropdown, null, null, "All"),
			new OptionsetField(
				"ShowCommentsOnly",
				"What should the widget show?",
				array(
					"0" => "Recent Blog Posts",
					"1" => "Recent Comments",
				),
				$this->ShowCommentsOnly
			));
    }
	
}
