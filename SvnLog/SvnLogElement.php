<?php

class SvnLogElement extends Element {
    
    static $db = array(
        "Url" => "Varchar",
        "Limit" => "Int"
    );
    
    static $defaults = array(
        "Url" => "http://svn",
        "Limit" => 10
    );
    
    function getCMSFields() {
        return new FieldSet(
            new TextField("Url"),
            new NumericField("Limit")
        );
    }
    
    
    function parseResponse($rsp) {
        $xml = implode("", $rsp);
        return @simplexml_load_string($xml);
    }
    
    
    function readLog() {
        exec("svn log {$this->Url} --limit {$this->Limit} --xml", $rsp);
        if($rsp) {
            $log = $this->parseResponse($rsp);
            
            if($log) {
                $ds = new DataObjectSet;
                foreach ($log->logentry as $Logentry) {
                    $date = new SS_Datetime();
                    $date->setValue((string)$Logentry->date);
                    
                    $message = new Text();
                    $message->setValue((string)$Logentry->msg);
                    
                    $entry = new ArrayData(
                        array(
                            "Revision" => (int)$Logentry['revision'],
                            "Author" => (string)$Logentry->author,
                            "Message" => $message,
                            "Date" => $date
                        )
                    );
                    $ds->push($entry);
                }
	            return $ds;
            }
        } else {
            return false;
        }
    }
    
    
    function Entries() {
        $cacheName = "SVNLOG-".md5($this->Url.$this->Limit);
        
        if($data = $this->loadCache($cacheName, 3600)) {
            return $data;
	}
	$data = $this->readLog();
	$this->saveCache($cacheName, $data);
        return $data;
        
    }
    
    function asdforTemplate() {
        
    }
    
    
    
    
    
}
