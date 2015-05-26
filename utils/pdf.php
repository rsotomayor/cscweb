<?php

class pdf {
	
	private $data;
  private $default_options;

	function __construct() {
    $this->data = "";
    $this->default_options = "";
  }
    
  function addhtml($html)
  {
    $this->data .= utf8_decode($html);
#    $this->data .= $html;

  }

    
  function options ($options)
  {
    $this->default_options .= " ".$options;
  }
      	
	function getRandomFilename($dir)
	{
		$rd = rand(0,getrandmax());
  		$tmp_filename = "tmpfile".$rd.".html";
  		if( file_exists($dir.$tmp_filename) )
  			$tmp_filename = $this->getRandomFilename($dir);  		
  		return $tmp_filename;		
	}

  function generate($output = null, $www = false) {
    $tmp_dir = "/u/savtec/public_html/rso.cl/apps/tmp/";
    $tmp_filename = $this->getRandomFilename($tmp_dir);  		
    $tmp_complete_path = $tmp_dir.$tmp_filename; 
    $fp = fopen($tmp_complete_path, "w+");
    $html = $this->data;
    fwrite($fp, $html);		
    fclose($fp);
	    			
    if ($www) {                  
      $output = $output.".html";
      header('Content-Type: text/html; charset=UTF-8');
      header('Content-Disposition: attachment; filename="'.$output.'"');	  	
      header('Content-Length: ' . filesize($tmp_complete_path));
      flush();
      readfile($tmp_complete_path);		
    } else {
      header("Content-Type: application/pdf");
      header('Content-Disposition: attachment; filename="'.$output.'"');
      flush();
      passthru("htmldoc -t pdf14 --charset utf-8 --quiet --jpeg --webpage ".$this->default_options." '$tmp_complete_path'");			
      @unlink($tmp_complete_path);
    }
  }

  function generateFile($output = null, $www = false) {
    $tmp_dir = "/u/savtec/public_html/rso.cl/apps/tmp/";
    $tmp_filename = $this->getRandomFilename($tmp_dir);  		
    $tmp_complete_path = $tmp_dir.$tmp_filename; 
    $fp = fopen($tmp_complete_path, "w+");
    $html = $this->data;
    fwrite($fp, $html);		
    fclose($fp);
    
    return $tmp_complete_path ;

    //~ echo $tmp_complete_path."\n";
      //~ passthru("htmldoc -t pdf14 --charset utf-8 --quiet --jpeg --webpage ".$this->default_options." '$tmp_complete_path'");			
      //~ @unlink($tmp_complete_path);
  }


}
?>
