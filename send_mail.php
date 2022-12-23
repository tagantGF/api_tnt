<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
//require 'vendor/autoload.php';
$statut = $_GET['statut'];
$leMail = $_GET['mail'];
$town = $_GET['town'];
$bonTransport =  $_GET['bonTransport'];
$url_suivi_coli = "https://www.tnt.fr/public/suivi_colis/recherche/visubontransport.do?bonTransport=$bonTransport&bonTransportTemp=&radiochoixrecherche=BT&radiochoixtypeexpedition=NAT&refInterneInt=";
$numCommand = $_GET['numCommand'];
$mailContact = '';
$code_chantier = $_GET['code_chantier'];
$libCmd = '';
if(!in_array($numCommand,[null,""])){
    $libCmd = "N° $numCommand";
}
if($town == 'Marseille'){
    $mailContact = 'suivi-livraison-marseille@groupe-feraud.com';
}else if($town == 'Nice'){
    $mailContact = 'Suivi-livraison-nice@groupe-feraud.com';
}else if($town == 'Rungis'){
    $mailContact = 'suivi-livraison-rungis@groupe-feraud.com';
}

if(!in_array($code_chantier,[null,""])){
    $code_chantier = "La référence chantier rattachée est: <b>$code_chantier</b><br><br>";
}else{
    $code_chantier = '';
}
//$leMail = "falahometest@gmail.com";

require 'PHPMailer/src/Exception.php';
 
/* Classe-PHPMailer */
require 'PHPMailer/src/PHPMailer.php';
/* Classe SMTP nécessaire pour établir la connexion avec un serveur SMTP */
require 'PHPMailer/src/SMTP.php';

//Create an instance; passing `true` enables exceptions


try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();    //Send using SMTP
    $mail->SMTPAuth = true;   
    //Server settings
    $mail->SMTPDebug = 0;  //SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->Host = 'smtp.office365.com';                     //Set the SMTP server to send through
    //                                //Enable SMTP authentication
    $mail->Username = 'info-feraud@groupe-feraud.com';                     //SMTP username
    $mail->Password = 'g/9Nn0J]Zu8}x@U9';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
   
    //Recipients
    $mail->setFrom('info-feraud@groupe-feraud.com', 'Suivi de commande Feraud');
    $mail->addAddress($leMail, 'Client');     //Add a recipient
    //$mail->addAddress('j.caline@groupe-feraud.com');               //Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('Suivi-livraison@groupe-feraud.com');
    $mail->addBCC('y.bijaoui@groupe-feraud.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = "Statut de votre commande $libCmd";
    $mail->Body = "Bonjour Madame, Monsieur,<br><br>
                    Ce mail est envoyé automatiquement pour vous avertir du statut de livraison, vous serez notifié à chaque changement d’état.<br><br>
                    Le statut de votre commande $libCmd est : <b>$statut</b><br><br>
                    Le transporteur du colis est : <b>TNT Express</b><br><br>
                    $code_chantier
                    Vous pouvez suivre votre colis à cette adresse : <a href='$url_suivi_coli'>Lien de suivi TNT</a><br><br>
                    Ne pas faire répondre, en cas de problème ou pour toutes questions, veuillez nous écrire à <a href='mailto:$mailContact'>$mailContact</a><br><br>
                    L’équipe Feraud vous souhaite une bonne journée.";
                    
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
   
    $mail->CharSet = 'UTF-8';
	$mail->Encoding = 'base64';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}