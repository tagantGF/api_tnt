<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: text/html; charset=utf-8");
    include_once('model/bigModelForMe.php');
    include_once('model/getStatut.php');

        $username = 's.djenane@groupe-feraud.com';
        $password = 'Agence06';
        $wsse_header = new WsseAuthHeader($username, $password);
        
        $marqueur_nice = $manager->selectionUnique2('numCommand',array('*'),"marqueur_nice <> ''");
        $allArticle = $manager->selectionUnique2('numCommand',array('*'),"transporteur='tnt' AND ville='Nice'");
        $lastTntCmd = $allArticle[count($allArticle)-1]->marqueur_nice;
        $commands = array();
        if(count($marqueur_nice) > 0){
            $marqueur_nice = (int)($marqueur_nice[0]->marqueur_nice);
            $table0 = array(
                'marqueur_nice'=>""
            );
            $manager->modifier('numCommand',$table0,"marqueur_nice='$marqueur_nice'");
            if($marqueur_nice == $lastTntCmd){
                $marqueur_nice = 0;
            }
            $commands = $manager->selectionUnique2('numCommand',array('*'),"transporteur='tnt' AND ville='Nice' AND num_cmd > $marqueur_nice LIMIT 100");
        }else{
            $commands = $manager->selectionUnique2('numCommand',array('*'),"transporteur='tnt' AND ville='Nice' LIMIT 100");
        }
       
    //*****************************************save in bdd ********************************** */
        try{
            $compteur = 0;
            foreach($commands as $key=>$val){
                $compteur++;
                $bl = '';
                $numcmd = '';
                $ref = '';
                $email = '';
                $code_chantier = '';
                $id = 0;
                foreach($val as $key2=>$val2){
                    if($key2 == "bl"){
                        $bl = $val2;
                        if(intval($bl) != 0){
                            $ref = "00$bl";
                        }else{
                            $ref = "$bl";
                        }
                        $statusInit = getShortStatut($wsse_header,$ref,'08950259');
                        $status = $statusInit[0];
                        $bonTransport = $statusInit[1];
                        if($status !=''){
                           $status0 = mb_substr($status, 0, 11, 'UTF-8');
                            $recup = $manager->selectionUnique2('suivi_expedition_tnt_nice',array('*'),"ref LIKE '%$ref%'");
                            if(count($recup) == 0){
                                if($status0 == "Colis livré" || $status == 'Livré'){
                                    if(count($commands) == $compteur){
                                        $table0 = array(
                                            'marqueur_nice'=>"$id"
                                        );
                                        $manager->modifier('numCommand',$table0,"num_cmd=$id");
                                    }
                                    $table = array(
                                        'status'=>"$status",
                                        'numcmd'=>"$numcmd",
                                        'ref'=>$ref,
                                        'bonTransport'=>$bonTransport,
                                        'send'=>"true"
                                    );
                                    redirectTo($status,$email,$numcmd,$bonTransport,$code_chantier);
                                    $manager->insertion('suivi_expedition_tnt_nice',$table,'');
                                }else{
                                    if(count($commands) == $compteur){
                                        $table0 = array(
                                            'marqueur_nice'=>"$id"
                                        );
                                        $manager->modifier('numCommand',$table0,"num_cmd=$id");
                                    }
                                    $table = array(
                                        'status'=>"$status",
                                        'numcmd'=>"$numcmd",
                                        'ref'=>$ref,
                                        'bonTransport'=>$bonTransport,
                                        'send'=>"false"
                                    );
                                   redirectTo($status,$email,$numcmd,$bonTransport,$code_chantier);
                                    $manager->insertion('suivi_expedition_tnt_nice',$table,'');
                                }
                            }else{
                                if($status != $recup[0]->status){
                                    if(mb_substr($status, 0, 11, 'UTF-8') == "Colis livré" || $status == 'Livré'){
                                        if(count($commands) == $compteur){
                                            $table0 = array(
                                                'marqueur_nice'=>"$id"
                                            );
                                            $manager->modifier('numCommand',$table0,"num_cmd=$id");
                                        }
                                        $table = array(
                                            'status'=>"$status",
                                            'send'=>"true"
                                        );
                                        $num_s_tnt = $recup[0]->num_s_tnt;
                                        redirectTo($status,$email,$numcmd,$bonTransport,$code_chantier);
                                        $manager->modifier('suivi_expedition_tnt_nice',$table,"num_s_tnt=$num_s_tnt");
                                    }else{
                                        if(count($commands) == $compteur){
                                            $table0 = array(
                                                'marqueur_nice'=>"$id"
                                            );
                                            $manager->modifier('numCommand',$table0,"num_cmd=$id");
                                        }
                                        $table = array(
                                            'status'=>"$status",
                                            'send'=>"false"
                                        );
                                        $num_s_tnt = $recup[0]->num_s_tnt;
                                        redirectTo($status,$email,$numcmd,$bonTransport,$code_chantier);
                                        $manager->modifier('suivi_expedition_tnt_nice',$table,"num_s_tnt=$num_s_tnt"); 
                                    }
                                }else{
                                    if(count($commands) == $compteur){
                                        $table0 = array(
                                            'marqueur_nice'=>"$id"
                                        );
                                        $manager->modifier('numCommand',$table0,"num_cmd=$id");
                                    }
                                }
                            }
                        }
                    }else if($key2 == "ncommand"){
                        $numcmd = $val2;
                    }
                    else if($key2 == "code_clt"){
                        $clients = getUser("$val2",$manager);
                        if($clients->client->email != ''){
                            $email = $clients->client->email;
                        }
                    }else if($key2 == "num_cmd"){
                        $id = $val2;
                    }else if($key2 == "code_chantier"){
                        $code_chantier = $val2;
                    }
                }
            }
            
            echo 'Entrée ajoutée dans la table';
        }catch(PDOException $e){
            echo "Erreur : " . $e->getMessage();
        }
	//****************************************************************************************** */
    //***********************get api****************************************************** */
        function getApi($manager){
            $recup = $manager->selectionUnique2('api',array('*'),'');
            return $recup[0]->valeur;
        }
    //************************************************************************************************ */
    
    //****************************************get users******************************************* */
        function getUser($code,$manager){
            $url = "http://www.quincaillerie-feraud.fr/yzyapi/1.0.0/clients/$code"; //AA001 for one user
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
                "customer-header2:value2"
            ));
            // Get response
            $responseUsers = curl_exec($ch2);
            curl_close($ch2); 
            // Decode
            $client = json_decode($responseUsers);
            //echo "mon email : ".$clients->client->email;
            return $client;
        }
    //**************************************************************************************************** */
    //*******************************send mail function********************************************* */
        function redirectTo($statut,$email,$numcmd,$bonTransport){
            $ch = curl_init();
            // define options
            $optArray = array(
                CURLOPT_URL => "https://it-feraud.com/api_tnt/send_mail.php?statut=$statut&mail=$email&numCommand=$numcmd&town=Nice&bonTransport=$bonTransport",
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
        }
    //********************************************************************************************** */
    //***************************************************send mail********************************* */
        
?>