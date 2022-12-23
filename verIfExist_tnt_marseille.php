<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: text/html; charset=utf-8");
	include_once('model/bigModelForMe.php');
	include_once('model/getStatut.php');

		$username = 'quincaillerie.feraud@gmail.com';
		$password = '@Tnt_13';
		$wsse_header_marseille = new WsseAuthHeader($username, $password);

		// $username_n = 's.djenane@groupe-feraud.com';
		// $password_n = 'Agence06';
		// $wsse_header_nice = new WsseAuthHeader($username_n, $password_n);

		// $username_r = 'jbernardi@vedis.pro';
		// $password_r = 'Vedis@94150';
		// $wsse_header_rungis = new WsseAuthHeader($username_r, $password_r);


        $marqueur = $manager->selectionUnique2('numCommand',array('*'),"verifexisttntmarseille <> ''");
        $allArticle = $manager->selectionUnique2('numCommand',array('*'),"transporteur='eurocoop'");
        $lastEurocoopCmd = $allArticle[count($allArticle)-1]->verifexisttntmarseille;
        $commands = array();
        if(count($marqueur) > 0){
            $marqueur = (int)($marqueur[0]->verifexisttntmarseille); 
            $table0 = array(
                'verifexisttntmarseille'=>""
            );
            $yu = $manager->modifier('numCommand',$table0,"verifexisttntmarseille='$marqueur'");
            if($marqueur == $lastEurocoopCmd){
                $marqueur = 0;
            }
            $commands = $manager->selectionUnique2('numCommand',array('*'),"transporteur='eurocoop' AND num_cmd > $marqueur LIMIT 200");
        }else{
            $commands = $manager->selectionUnique2('numCommand',array('*'),"transporteur='eurocoop' LIMIT 200");
        }
        // $table0 = array(
        //     'verifexisttntmarseille'=>"726"
        // );
        // $t = $manager->modifier('numCommand',$table0,"num_cmd LIKE '%726%'");
        // echo '<pre>';
        //     print_r($commands);
        // echo '</pre>';
        $c = 0;
        foreach($commands as $key=>$val){
            $num = 0;
            $c += 1;
            foreach($val as $key2=>$val2){
                if($key2 == "bl"){
                    $bl = $val2;
                    getTransporteur($bl,$wsse_header_marseille,$num);
                    if(count($commands) == $key+1){
                        echo $num;
                        $table0 = array(
                            'verifexisttntmarseille'=>"'$num'"
                        );
                        $t = $manager->modifier('numCommand',$table0,"num_cmd LIKE '%$num%'");
                        break;
                    }
                }
                else if($key2 == "num_cmd"){
                    $num = $val2;
                }
            }
        }

		function getTransporteur($bl,$wsse_header_marseille,$num){
            $status_marseille = getShortStatut($wsse_header_marseille,"00$bl",'08912866');
            if($status_marseille[0] != ''){
                $table0 = array(
                    'transporteur'=>"tnt"
                );
                $manager->modifier('numCommand',$table0,"num_cmd LIKE '%$num%'");
            }
		}
 ?>
 