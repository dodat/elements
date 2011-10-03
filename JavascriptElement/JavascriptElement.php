<?php

class JavascriptElement extends Element implements HiddenElement {
	
	static $NiceName = "Javascript Code";
	
	static $db = array(
		"Content" => "Text",
		"Libraries" => "Text"
	);
	
	
	function getCMSFields() {
		$Libraries = new TextareaField("Libraries", "Libraries to include (one per line)");
		$Libraries->addExtraClass("elastic");
		
		$Content = new TextareaField("Content");
		$Content->addExtraClass("elastic");
		$Content->addExtraClass("tabby");
		
		return new FieldSet(
			$Libraries,
			$Content
		);
	}
	
	
	function forTemplate() {
		foreach(explode("\n", $this->Libraries) as $Library) {
			if(is_file(THIRDPARTY_PATH."/".$Library)) {
				Requirements::javascript(THIRDPARTY_DIR."/".$Library);
			} elseif(Permission::check("CMS_ACCESS_CMSMain") || Director::isDev()) {
				//only scan when logged in or in dev mode to save resources
				if($File = $this->findLibrary(trim($Library))) {
					Requirements::javascript($File);
					$this->Libraries = str_replace($Library, $File, $this->Libraries);
					$this->write();
				}
			}
		}
		Requirements::customScript($this->Content);
	}
	
	
	function forCMSTemplate() {
		return "<pre>".$this->obj("Content")->escapeXML()."</pre>";
	}
	
	
	function findLibrary($Library) {
		if($Files = $this->listJSFiles(THIRDPARTY_PATH, $Library)) {
			$Filepath = substr($Files[0], strlen(THIRDPARTY_PATH)+1);
			return $Filepath;
		}
		
		return false;
	}
	
	
	function listJSFiles($path, $file) {
		$files = glob($path."/".$file);
		if(count($files) > 0){
			return $files;
		}
		foreach(glob($path . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $path) {
			$files = array_merge($files, $this->listJSFiles($path, $file));
		}
		return $files;
	}
	
}
