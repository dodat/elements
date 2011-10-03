<?php


class CycleElement extends Element {
	
	static $NiceName = "Cycle Slideshow";
	
	static $db = array(
		"NumberElements" => "Int",
		"Recursive" => "Boolean",
		"Width" => "Int",
		"Height" => "Int",
		"FileType" => "Varchar",
		"Orderby" => "Enum('ID,Created,Name, RAND()', 'Created')",
		"Direction" => "Enum('ASC,DESC','DESC')"
	);
	
	static $has_one = array(
		'Folder' => 'Folder',
	);
	
	static $defaults = array(
		"Width" => "350",
		"Height" => "280",
		"Orderby" => "Created",
		"Direction" => "DESC"
	);
	
	
	function getCMSFields() {
		$folder = new TreeDropdownField( 'FolderID', "Show The Files From", 'Folder' );
		$folder->setFilterFunction(
			create_function('$obj', 'return $obj->class == "Folder";')
		);
		return new FieldSet(
			$folder,
			new DropdownField("FileType", "File Type to display", $this->getValidFileTypes()),
			new NumericField("NumberElements"),
			new DropdownField("Orderby", "Order By", $this->dbObject('Orderby')->enumValues(), $this->Orderby),
			new DropdownField("Direction", "Direction", $this->dbObject('Direction')->enumValues(), $this->Direction),
			new CheckboxField("Recursive"),
			new NumericField("Width"),
			new NumericField("Height")
		);
		
	}
	
	
	function getValidFileTypes() {
		$validTypes = array();
		$FileTypes = ClassInfo::subclassesFor("File");
		foreach($FileTypes as $FileType) {
			if(singleton($FileType)->hasMethod("forTemplate")) {
				$validTypes[$FileType] = $FileType;
			}
		}
		return $validTypes;
	}
	
	
	function getSubFolders(Folder $parentFolder) {
		$ds = new DataObjectSet();
		$subFolders = DataObject::get("Folder", "ParentID='{$parentFolder->ID}'");
		if($subFolders) foreach($subFolders as $subFolder) {
			$ds->push($subFolder);
			$ds->merge($this->getSubFolders($subFolder));
		}
		return $ds;
	}
	
	
	private function getFolderChildren(Folder $Folder) {
		$Where = "ParentID='{$Folder->ID}'";
		if($this->Recursive) {
			foreach($this->getSubFolders($Folder) as $subFolder) {
				$Where .= " OR ParentID='{$subFolder->ID}'";
			}
		}
		
		return DataObject::get(
			"File",
			"(".$Where.") AND ClassName='{$this->FileType}'",
			"`File`.".$this->Orderby." ".$this->Direction,
			"",
			$this->NumberElements
		);
		
	}
	
	public function Files() {
		
		$ds = new DataObjectSet;
		if($this->Folder()->exists()) {
			if($Childs = $this->getFolderChildren($this->Folder())) :
			foreach($Childs as $Child) {
				if($Child->hasMethod("SetSize")) { 
					if($this->Width && $this->Height) {
						$Child->View = $Child->SetSize($this->Width, $this->Height);
					} elseif ($this->Width) {
						$Child->View = $Child->SetWidth($this->Width);
					} elseif($this->Height) {
						$Child->View = $Child->SetHeight($this->Height);
					} else {
						$Child->View = $Child;
					}
				} else {
					$Child->View = $Child;
				}
				
				
				$ds->push($Child);
				
			}
			endif;
		}
		
		return $ds;
	}
	
	function forTemplate() {
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery-packed.js");
		Requirements::javascript(substr(Director::makeRelative(dirname(__FILE__))."/javascript/jquery.cycle.min.js", 1));
		
		$addSlides = "";
		foreach($this->Files() as $File) {
			if($File->First()) continue;//skip the first file
			$addSlides .= "\n\t$('<img />').css('display','none').attr('src', '{$File->View->getRelativePath()}').appendTo(slide)";
			if($File->Last()) {
				$addSlides .= ".load(function() {
			slide.cycle({
				fx:      'fade', 
				timeout:  5000,
				containerResize: false
			});
		});";
			} else {
				$addSlides .= ";";
			}
		}
		
		$js = <<<JS

jQuery(function($) {
	var slide = $("#{$this->HTMLID()}");
	slide.height(slide.find('img').height());
	$addSlides
});

JS;

		
		Requirements::customScript($js);
		return parent::forTemplate();
	}
	
	
	function forCMSTemplate() {
		return parent::forTemplate();
	}


}
