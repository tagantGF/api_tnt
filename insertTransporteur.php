<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: text/html; charset=utf-8");
	include_once('model/bigModelForMe.php');
	include_once('model/getStatut.php');

		$username = 'quincaillerie.feraud@gmail.com';
		$password = '@Tnt_13';
		$wsse_header_marseille = new WsseAuthHeader($username, $password);
		

		$username_n = 's.djenane@groupe-feraud.com';
		$password_n = 'Agence06';
		$wsse_header_nice = new WsseAuthHeader($username_n, $password_n);

		$username_r = 'jbernardi@vedis.pro';
		$password_r = 'Vedis@94150';
		$wsse_header_rungis = new WsseAuthHeader($username_r, $password_r);
		
		function getTransporteur($bl,$wsse_header_marseille,$wsse_header_nice,$wsse_header_rungis,$trp){
            if($trp == ''){
                $status1 = getShortStatut($wsse_header_marseille,"00$bl",'08912866');
                if($status1 == ''){
                    return 'test1';
                }else{
                    return $status1;
                }
            }else if($trp == 'test1'){
                $status2 = getShortStatut($wsse_header_nice,"00$bl",'08950259');
                if($status2 == ''){
                    return 'test2';
                }else{
                    return $status2;
                }
            }else if($trp == 'test2'){
                $status3 = getShortStatut($wsse_header_rungis,"00$bl",'03803869');
                if($status3 == ''){
                    return 'test3';
                }else{
                    return $status3;
                }
            }
		}

		function saveTransporteur($manager,$wsse_header_marseille,$wsse_header_nice,$wsse_header_rungis){
			$commands = $manager->selectionUnique2('numCommand',array('*'),"transporteur NOT IN('tnt','eurocoop') LIMIT 200");
			if(true){
				foreach($commands as $key=>$val){
					$idcmd = 0;
                    $bl = '';
					foreach($val as $key2=>$val2){
						if($key2 == "bl"){
							$bl = $val2;
						}else if($key2 == 'transporteur' ){
                            $recupStatut = getTransporteur($bl,$wsse_header_marseille,$wsse_header_nice,$wsse_header_rungis,$val2);
							if($val2 == ''){
                                if($recupStatut == 'test1'){
                                    $table = array(
                                        'transporteur'=>$recupStatut,
                                        'ville'=> ''
                                    );
                                    $manager->modifier('numCommand',$table,"bl='$bl'");
                                    break;
                                }else{
                                    $table = array(
                                        'transporteur'=>"tnt",
                                        'ville'=> 'Marseille'
                                    );
                                    $manager->modifier('numCommand',$table,"bl='$bl'");
                                    break;
                                }
                            }else if($val2 == 'test1'){
                                if($recupStatut == 'test2'){
                                    $table = array(
                                        'transporteur'=>$recupStatut,
                                        'ville'=> ''
                                    );
                                    $manager->modifier('numCommand',$table,"bl='$bl'");
                                    break;
                                }else{
                                    $table = array(
                                        'transporteur'=>"tnt",
                                        'ville'=> 'Nice'
                                    );
                                    $manager->modifier('numCommand',$table,"bl='$bl'");
                                    break;
                                }
                            }else if($val2 == 'test2'){
                                if($recupStatut == 'test3'){
                                    $table = array(
                                        'transporteur'=>'eurocoop',
                                        'ville'=> 'Marseille'
                                    );
                                    $manager->modifier('numCommand',$table,"bl='$bl'");
                                    break;
                                }else{
                                    $table = array(
                                        'transporteur'=>"tnt",
                                        'ville'=> 'Rungis'
                                    );
                                    $manager->modifier('numCommand',$table,"bl='$bl'");
                                    break;
                                }
                            }
                        }
					}
				}
			}
			echo 'ajout fait ! ';
		}
		saveTransporteur($manager,$wsse_header_marseille,$wsse_header_nice,$wsse_header_rungis);	
 ?>
 