<?php  
include('../phpexcel/PHPExcel.php'); 
include('../phpexcel/PHPExcel/Writer/Excel5.php'); 

class Excel { 
     
    var $xls; 
    var $sheet; 
    var $data; 
    var $blacklist = array(); 

    /* constructor */
    function Excel() { 
        $this->xls = new PHPExcel(); 
        $this->sheet = $this->xls->getActiveSheet(); 
        $this->sheet->getDefaultStyle()->getFont()->setName('Verdana'); 
    }

    /** 
    *
    * transforma la primera tabla del html a xls
    *
    **/
    function htmltoxls($html, $tdcolor = 'E1E1E1',$thbackground = '000000',$thcolor = 'ffffff'){
      //buscar tabla
      preg_match_all("/<table[^>]*>(.*?)<\/table>/si", $html, $table_content, PREG_PATTERN_ORDER);
      //buscar lineas
      preg_match_all("/<tr[^>]*>(.*?)<\/tr>/si", $table_content[1][0], $arrows_content, PREG_PATTERN_ORDER);
      $y = 0;
      $format_count = 0;
      $max_x =0;

      foreach( $arrows_content[1] as $arrow)
      {                        
        //por cada linea buscar encabezado o celda normal
        preg_match_all("/<th[^>]*>(.*?)<\/th>/si", $arrow, $cellsth_content, PREG_PATTERN_ORDER);
        preg_match_all("/<td[^>]*>(.*?)<\/td>/si", $arrow, $cellstd_content, PREG_PATTERN_ORDER);        
        
        //aplicar formato
        $format = array();
        $cols = array();
        if(isset($cellsth_content[1][0]))
        {
          $cols = $cellsth_content;
          $format = array('bold' => true, 'background' => $thbackground, 'color' => $thcolor);
          $format_count = 0;
        }
        else
        {
          $cols = $cellstd_content;
          if($format_count%2!=0)
            $format = array('background' => $tdcolor);
          else
            $formar = array();
          $format_count++;
        }
        
        $numero_celdas = count($cols[1]);
        if($numero_celdas > $max_x)
          $max_x = $numero_celdas;
        
        $x = 0;
        for($i=0;$i < $numero_celdas; $i++)
        {          
          $this->changeRowCol($cols[1][$i],$y,$x,$format);
          
          //buscar colspans
          preg_match_all("/colspan\s*=\s*('.*?'|\".*?\")/si", $cols[0][$i], $valor_colspan, PREG_PATTERN_ORDER);       
          if($valor_colspan[1] == array())
            $valor_colspan = 1;
          else
            $valor_colspan = str_replace(array("\"","'"),array("",""),$valor_colspan[1][0]);                      
          if($valor_colspan > 1)
          {
            $this->mergeRowCol($x,$y,($x + $valor_colspan - 1),$y);
            $x = $x + $valor_colspan;            
          }
          else
          {
            $x++;
          }        
        }
        $y++;        
      }   

      //agregando bordes
      $max_x--;
      $y--;     
      $this->addBorder(0,0,$max_x,$y);
    }

    /* transforma numeros a letras */
    function num2alpha($n)
    {
        for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
            $r = chr($n%26 + 0x41) . $r;
        return $r;
    }     

    /* transformas letras a numeros*/
    function alpha2num($a)
    {
        $l = strlen($a);
        $n = 0;
        for($i = 0; $i < $l; $i++)
            $n = $n*26 + ord($a[$i]) - 0x40;
        return $n-1;
    }

    /* carga archivo excel para ser modificado */
    function loadFile($file) 
    { 
        $this->reader = new PHPExcel_Reader_Excel5(); 
        $this->xls = $this->reader->load("{$file}"); 
         
        $this->xls->setActiveSheetIndex(0); 
        $this->sheet = $this->xls->getActiveSheet(); 
        $this->sheet->getDefaultStyle()->getFont()->setName('Verdana'); 
    } 
     
    /* crea un numero archivo */
    function newFile() 
    { 
        $this->xls = new PHPExcel(); 
        $this->sheet = $this->xls->getActiveSheet(); 
        $this->sheet->getDefaultStyle()->getFont()->setName('Verdana'); 
    } 
     
    /* agregar valor a una celda (valor,fila,columna,opciones)*/
    function changeRowCol($value = null, $row = null, $col = null, $options = array()){
      $cell = $this->num2alpha($col).($row+1);
      $this->changeCell($value, $cell); 
      foreach($options as $key => $valueoption){
        switch($key){
          case 'bold':
            $this->setBold($cell, $valueoption);
            break;
          case 'background':
            $this->setBackground($cell, $valueoption);
            break;
          case 'size':
            $this->setSize($cell, $valueoption);
            break;
          case 'color':
            $this->setColor($cell, $valueoption);
            break;           
        }
      }
    } 

    /* agrega bordes (desde y hasta) */
    function addBorder($desde_x,$desde_y,$hasta_x,$hasta_y)
    {
      $cell_desde = $this->num2alpha($desde_x).($desde_y+1);
      $cell_hasta = $this->num2alpha($hasta_x).($hasta_y+1);
      $styleArray = array(
        'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
          )
        )
      );
      $this->sheet->getStyle($cell_desde.':'.$cell_hasta)->applyFromArray($styleArray);
    }

    /* combina celdas (desde y hasta) */
    function mergeRowCol($desde_x,$desde_y,$hasta_x,$hasta_y)
    {
      $cell_desde = $this->num2alpha($desde_x).($desde_y+1);
      $cell_hasta = $this->num2alpha($hasta_x).($hasta_y+1);
      $this->sheet->mergeCells($cell_desde.':'.$cell_hasta);
    }
    
    /* combina celdas (desde y hasta con formato letranumero) */
    function mergeCells($desde,$hasta){
      $this->sheet->mergeCells($desde.':'.$hasta);
    }

    /* agregar valor a una celda (valor, celda con formato LETRANUMERO) */
    function changeCell($value = null, $cell = null) 
    { 
      $this->sheet->setCellValue($cell, $value);    
    } 
    
    function changeColWidth($value = null, $col = null){
      $col = $this->num2alpha($col);
      $this->sheet->getColumnDimension($col)->setWidth($value);
    }

    /* agregar bold a una celda */
    function setBold($cell = null, $val = true)
    {
      $this->sheet->getStyle($cell)->getFont()->setBold($val); 
    }

    function setColor($cell = null, $val = '000000')
    {
      $FontColor = new PHPExcel_Style_Color();
      $FontColor->setRGB($val);
      $this->sheet->getStyle($cell)->getFont()->setColor($FontColor);
    }
  
    /* cambia el tamaño a una celda */
    function setSize($cell = null, $val = true)
    {
      $this->sheet->getStyle($cell)->getFont()->setSize($val); 
    }

    /* setea el color de fondo a una celda */
    function setBackground($cell = null, $color = '969696')
    {
      $this->sheet->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID); 
      $this->sheet->getStyle($cell)->getFill()->getStartColor()->setRGB($color); 
    }
  
    /* encabezados y salida del archivo xls */
    function _output($title) { 
        header("Content-type: application/vnd.ms-excel");  
        header('Content-Disposition: attachment;filename="'.$title.'.xls"'); 
        header('Cache-Control: max-age=0'); 
        $objWriter = new PHPExcel_Writer_Excel5($this->xls); 
        $objWriter->setTempDir(TMP); 
        $objWriter->save('php://output'); 
    } 


    /* genera un excel a partir de resultado de una consulta al algun modelo */
    function generate(&$data, $title = 'Report') { 
         $this->data =& $data; 
         $this->_title($title); 
         $this->_headers(); 
         $this->_rows(); 
         $this->_output($title); 
         return true; 
    } 
     
    /* setea el titulo automatico para las tablas cargadas de los modelos */
    function _title($title) { 
        $this->sheet->setCellValue('A2', $title); 
        $this->sheet->getStyle('A2')->getFont()->setSize(14); 
        $this->sheet->getRowDimension('2')->setRowHeight(23); 
    } 

    /* setea los encabezados de las tablas cargadas de losmodelos */
    function _headers() { 
        $i=0; 
        foreach ($this->data[0] as $field => $value) { 
            if (!in_array($field,$this->blacklist)) { 
                $columnName = Inflector::humanize($field); 
                $this->sheet->setCellValueByColumnAndRow($i++, 4, $columnName); 
            } 
        } 
        $this->sheet->getStyle('A4')->getFont()->setBold(true); 
        $this->sheet->getStyle('A4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID); 
        $this->sheet->getStyle('A4')->getFill()->getStartColor()->setRGB('969696'); 
        $this->sheet->duplicateStyle( $this->sheet->getStyle('A4'), 'B4:'.$this->sheet->getHighestColumn().'4'); 
        for ($j=1; $j<$i; $j++) { 
            $this->sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($j))->setAutoSize(true); 
        } 
    } 
    
    /* carga las celdas para la tabla cargada del modelo */
    function _rows() { 
        $i=5; 
        foreach ($this->data as $row) { 
            $j=0; 
            foreach ($row as $field => $value) { 
                if(!in_array($field,$this->blacklist)) { 
                    $this->sheet->setCellValueByColumnAndRow($j++,$i, $value); 
                } 
            } 
            $i++; 
        } 
    } 
        
} 
?>