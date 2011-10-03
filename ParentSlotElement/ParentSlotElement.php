<?php

class ParentSlotElement extends Element {
	
	function forTemplate() {
		if($this->Slot()->GridPage()->ParentID) {
			$parent = $this->Slot()->GridPage()->Parent();
			$name = $this->Slot()->Name;
			return $parent->Slot($name)->forTemplate();
		}
	}
	
	function forCMSTemplate() {
		if($this->Slot()->GridPage()->ParentID) {
			$parent = $this->Slot()->GridPage()->Parent();
			$name = $this->Slot()->Name;
			return "Showing elements from Page <i>{$parent->Title}</i> in Slot <i>{$name}</i>";
			return $parent->Slot($name)->forCMSTemplate();
		}
	}
	
}
