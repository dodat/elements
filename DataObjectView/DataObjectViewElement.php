<?php

class DataObjectViewElement extends Element {

	static $db = array(
		"Model" => "Varchar",
		"TemplateContent" => "Text",
		"Filter" => "Varchar",
		"Sort" => "Varchar",
		"Limit" => "Int"
	);
	
	static $defaults = array(
		"Limit" => 10
	);
	
	function getCMSFields() {
		$TemplateContent = new TextareaField("TemplateContent");
		$TemplateContent->addExtraClass("elastic");
		$TemplateContent->addExtraClass("tabby");
		return new FieldSet(
			new DropdownField("Model", "Object to get", $this->listModels()),
			new TextField("Filter"),
			new TextField("Sort"),
			new NumericField("Limit"),
			$TemplateContent
		);
	}
	
	function listModels() {
		$ret = array();
		foreach(ClassInfo::subclassesFor("DataObject") as $Model) {
			$ret[$Model] = $Model;
		}
		return $ret;
	}
	
	function loadModels() {
		if($this->Model) {
			$ds = DataObject::get($this->Model, $this->Filter, $this->Sort, "", $this->Limit);
			return $ds;
		}
	}
	
	function forCMSTemplate() {
		return $this->obj("TemplateContent")->XML();
	}
	
	function forTemplate() {
		if($this->Model && $this->TemplateContent) {
			$template = new SSViewer_FromString($this->TemplateContent);
			return $this->loadModels()->renderWith($template);
		}
	}


}
