<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: text/html; charset=utf-8");
	include_once('model/bigModelForMe.php');
	include_once('model/getStatut.php');

	
	//************************************get api key ************************************* */
		function getApi($manager){
			$recup = $manager->selectionUnique2('api',array('*'),'');
			$t = $recup[0]->letemps;
			$pp = intval(time());
			$diff = $pp-$t;
			if(count($recup) > 0 && $diff < 5*3600){
				return  $recup[0]->valeur;
			}else{
				// init curl object        
				$ch = curl_init();
				// define options
				$optArray = array(
					CURLOPT_URL => 'http://www.quincaillerie-feraud.fr/yzyapi/1.0.0/login?username=ITFERAUD&password=PASS4FERO',
					CURLOPT_RETURNTRANSFER => true
				);
				// apply those options
				curl_setopt_array($ch, $optArray);
				// execute request and get response
				$result = curl_exec($ch);
				// also get the error and response code
				$errors = curl_error($ch);
				$response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				// var_dump($errors);
				// var_dump($response);
				$result = json_decode($result);
				$api_key = $result->api_key;
				if(count($recup) > 0 && $api_key!=""){
					$table = array(
						'valeur'=>"$api_key",
						'letemps'=>time(),
					);
					$manager->modifier('api',$table,"letemps=$t");
				}else{
					$table = array(
						'valeur'=>"$api_key",
						'letemps'=>time(),
					);
					if($api_key!=""){
						$manager->insertion('api',$table,'');
					}
				}
				return $api_key;
			}
		 }
	//************************************************************************************** */
			function saveCommandStatut($manager,$wsse_header){
                //$confirm = $manager->viderTable('tnt_command');
				$confirm = 'ok';
                if($confirm == 'ok'){
                    $nbElmt = 100000;
                    $fois = $nbElmt/500;
                    $indicateur = 0;
                    $tt = array();
                    for($a=0;$a<$fois;$a++){
                        $i = ($indicateur*500)+1;
                        $url = "http://www.quincaillerie-feraud.fr/yzyapi/1.0.0/commandes?curseur=$i&limite=500"; //AA001 for one user
                        $apiKey = getApi($manager);   // should match with Server key
                        $headers = array(	 
                            'Authorization: '.$apiKey
                        );
                        // Send request to Server
                        $ch2 = curl_init();
                        // To save response in a variable from server, set headers;
                        curl_setopt( $ch2, CURLOPT_URL, $url);
                        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
                            "X-API-Key: $apiKey",
                            "customer-header2:value2",
                        ));
                        // Get response
                        $responseUsers = curl_exec($ch2);
                        curl_close($ch2); 
                        // Decode
                        $recup = json_decode($responseUsers);
                       
                        foreach($recup->commandes as $key=>$val){
                            $u = "";
                            $t = "";
							$code = "";
							
                            foreach($val as $k=>$v){
                                if($k == "numero_bl"){
                                    $table = array(
                                        'bl'=>"$v",
                                        'ncommand'=>$u,
										'code_clt'=>"$code"
                                    );
									
									$recupStatut = getShortStatut($wsse_header,"00$v");
									if($recupStatut != ''){
										echo $recupStatut.'<br>';
										$manager->insertion('tnt_command',$table,'');
										break;
									}else{
										break;
									}
                                }
                                else if($k == "numero"){
                                    $u = $v;
                                }
								else if($k == "client"){
                                    $code = $v->code;
                                }
                            }
                        }
                        $indicateur++;
                    }
					echo 'modificaton faite!';
                }
			}
            saveCommandStatut($manager,$wsse_header);	
 ?>
