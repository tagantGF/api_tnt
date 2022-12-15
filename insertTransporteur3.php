<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: text/html; charset=utf-8");
	include_once('model/bigModelForMe.php');
	include_once('model/getStatut.php');
			function saveTransporteur($manager,$wsse_header){
				$commands = $manager->selectionUnique2('numCommand',array('*'),"transporteur NOT IN('tnt','eurocoop') LIMIT 500");
                if(true){
					foreach($commands as $key=>$val){
						$idcmd = 0;
						foreach($val as $key2=>$val2){
							if($key2 == "bl"){
								$recupStatut = getShortStatut($wsse_header,"00$val2");
								if($recupStatut != ''){
									$table = array(
										'transporteur'=>"tnt",
									);
									$manager->modifier('numCommand',$table,"bl='$val2'");
									break;
								}else if($recupStatut == ''){
									$table = array(
										'transporteur'=>"eurocoop",
									);
									$manager->modifier('numCommand',$table,"bl='$val2'");
									break;
								}
							}
						}
					}
                }
				echo 'ajout fait ! ';
			}
            saveTransporteur($manager,$wsse_header);	
 ?>
