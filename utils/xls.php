<?php

// Agrega comentario
class XLS {

	function __construct() {
  }
  	
  function addhtml($html)
  {
    if ( isset($this->data) ) {
      $this->data .= utf8_decode($html);
    } else {
      $this->data = utf8_decode($html);
    }
  }
      	
	function getRandomFilename($dir)
	{
		$rd = rand(0,getrandmax());
  		$tmp_filename = "tmpfile".$rd.".html";
  		if( file_exists($dir.$tmp_filename) )
  			$tmp_filename = $this->getRandomFilename($dir);  		
  		return $tmp_filename;		
	}

  function generate($output)
  {
    $tmp_dir = "/u/savtec/public_html/rso.cl/tmp/";
		$tmp_filename = $this->getRandomFilename($tmp_dir);  		
		$tmp_complete_path = $tmp_dir.$tmp_filename; 
		
    $fp = fopen($tmp_complete_path, "w+");
	    
    $html = $this->data;
	    
    fwrite($fp, $html);		
    fclose($fp);
	    		
		//header("Content-type: application/octet-stream");
    //header("Content-Type: application/x-msdownload");
		header("Content-type: application/vnd.ms-excel");
	  header('Content-Disposition: attachment; filename="'.$output.'"');
		header("Pragma: no-cache");
		header("Expires: 0");
		
		flush();
	  readfile($tmp_complete_path);			
	  @unlink($tmp_complete_path);
  }

}
?>
