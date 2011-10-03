<?php

class VimeoElement extends Element {
	
	static $NiceName = "Vimeo Video";
	
	static $db = array(
		"video_id" => "Int",
		"height" => "Int",
		"width" => "Int",
		"thumbnail" => "Varchar(100)",
		"autoplay" => "Boolean"
	);
	
	static $defaults = array(
		"width" => 640,
		"height" => 512,
		"autoplay" => false
	);
	
	function getCMSFields() {
		return new FieldSet(	
			new NumericField("video_id", "Video ID", $this->video_id),
			new CheckboxField("autoSettings", "Retrieve Settings"),
			new NumericField("width", "Width", $this->width),
			new NumericField("height", "Height", $this->height),
			new CheckboxField("autoplay", "Autoplay")
		);
	}
	
	function write() {
		if($this->autoSettings && $this->video_id) {
			$this->retrieveProportions();
		}
		return parent::write();
	}
	
	function retrieveProportions() {
		$vimeo_url = "http://vimeo.com/api/clip/{$this->video_id}.php";
		$rsp = file_get_contents($vimeo_url);
		$clips = unserialize($rsp);
		foreach($clips as $clip) {
			if($clip['clip_id'] == $this->video_id) {
				$this->width = $clip['width'];
				$this->height = $clip['height'];
				$this->thumbnail = $clip['thumbnail_large'];
			}
		}
	}
	
	function Content() {
		
		if($this->video_id) {
			$Content = "<div id=\"Vimeo-{$this->ID}\">";
			if($thumb = $this->thumbnail) {
				$Content .= "<a href=\"http://vimeo.com/{$this->video_id}\" target=\"_blank\"><img src=\"$thumb\" width=\"{$this->width}\" height=\"{$this->height}\" alt=\"\"/></a>";
			} else {
				$Content .= "Watch this video at Vimeo: <a href=\"http://vimeo.com/{$this->video_id}\" target=\"_blank\">http://vimeo.com/{$this->video_id}</a>";
			}
			$Content .= "</div>";
			$this->Content = $Content;
			return $this->Content;
		}
	}
	
	function forTemplate() {
		
		$path = substr(Director::makeRelative(dirname(__FILE__)), 1);
		//Requirements::javascript($path."/javascript/swfobject.js");
		$expresspath = $path."/javascript/expressInstall.swf";
		
		Requirements::javascript("http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js");
		
		Requirements::customScript(<<<JS

var flashvars = {
	"wmode": "transparent",
	"allowfullscreen": "true",
	"server": "www.vimeo.com",
	"clip_id": "{$this->video_id}",
	"show_portrait": "0",
	"autoplay": {$this->autoplay},
	"show_title": "1",
	"show_byline": "0",
	"fullscreen": "1",
	"color": "FFFFFF",
	"js_api": 1,
	"js_onLoad": 'test',
	"js_swf_id": "Vimeo-{$this->ID}"
};

var params = {
	allowscriptaccess: 'always',
	allowfullscreen: 'true',
	wmode: "transparent"
};

var attributes = {
	id: "Vimeo-{$this->ID}"
};


swfobject.embedSWF("http://www.vimeo.com/moogaloop.swf", "Vimeo-{$this->ID}", "{$this->width}", "{$this->height}", "9", "{$expresspath}", flashvars, params, attributes);

JS
);
		return $this->Content();
	}
	
	function forCMSTemplate() {
		return $this->Content();
	}
	
}
