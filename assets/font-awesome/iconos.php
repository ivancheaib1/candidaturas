<link href="css/all.min.css" rel="stylesheet" />
<?php
  $fn = fopen("css/all.css","r");
  $leerDesde = false;
  while(! feof($fn))  {
	$result = fgets($fn);
	  if(!$leerDesde){
		$leerDesde = (trim($result) == "readers do not read off random characters that represent icons */");
	  }else{
		  if(substr($result, 0, 3) == ".fa"){
			  $icono = explode(":", $result);
			  echo '<div style="float:left;width: 100px;height: 100px;"><i class="fa '.trim($icono[0],".").' fa-3x"></i></br>fa '.trim($icono[0],".").'</div>';
			  
		  }
			
	  }
  }

  fclose($fn);
?> 