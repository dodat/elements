<?php

class FeedReaderElement extends Element {
    
    static $db = array(
        "Url" => "Varchar(255)",
        "Limit" => "Int"
    );
    
    static $defaults = array(
        "Url" => "",
        "Limit" => 10
    );
    
    function getCMSFields() {
        return new FieldSet(
            new TextField("Url"),
            new NumericField("Limit")
        );
    }
    
    
    function parseResponse($rsp) {
        return @simplexml_load_string($xml);
    }
    
    
    function readFeed() {
		
		$cacheName = "FEED-".md5($this->Url);
        
        if(!$rsp = $this->loadCache($cacheName, 3600)) {
            $rsp = @file_get_contents($this->Url);
		}
		
		$this->saveCache($cacheName, $rsp);
		
		
        if($rsp) {
            $feed = simplexml_load_string($rsp);
            
            if($feed) {
                $ds = new DataObjectSet;
				
                foreach ($feed->entry as $Feedentry) {
					
                    $date = new SS_Datetime();
                    $date->setValue((string)$Feedentry->updated);
                    
					$title = new Text();
					$title->setValue((string)$Feedentry->title);
					
					$link = new Text();
					$link->setValue((string)$Feedentry->link['href']);
					
                    $content = new HTMLText();
                    $content->setValue((string)$Feedentry->content);
                    
                    $entry = new ArrayData(
                        array(
                            "Title" => $title,
                            "Content" => $content,
                            "Date" => $date,
							"Link" => $link
                        )
                    );
                    $ds->push($entry);
					
					if($ds->Count() == $this->Limit) {
						break;
					}
                }
            }
            return $ds;
        } else {
            return false;
        }
    }
    
    
    function Entries() {
		
        return $this->readFeed(); 
    }
    
    
    
    
    
    
}
