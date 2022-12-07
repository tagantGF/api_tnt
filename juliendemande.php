<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: text/html; charset=utf-8");
	include_once('model/bigModelForMe.php');
	include_once('model/getStatut.php');

	//************************************************************************************** */
    
	$recup = $manager->selectionUnique2('mytable',array('*'),'');

	foreach($recup as $k=>$v){
		foreach($v as $k2=>$v2){
			$recup = $manager->selectionUnique2('numCommand',array('*'),"code_clt='$v2'");
			if($recup[0]->ncommand !=''){
				echo $recup[0]->ncommand.'<br>';
			}
			
		}
	}
   
			
 ?>
