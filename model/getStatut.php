<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=utf-8");
		
   
	$username = 'quincaillerie.feraud@gmail.com';
	$password = '@Tnt_13';
	$wsse_header = new WsseAuthHeader($username, $password);
   
    function getShortStatut($wsse_header,$ref){
        $wsdl = "https://www.tnt.fr/service/?wsdl";
        $getTracking = new SoapClient($wsdl, array(
            //"trace" => 1,
            //"exceptions" => 0
            )
        );
        $getTracking->__setSoapHeaders(array($wsse_header));
        $params = array(
            'accountNumber' =>'08912866',
            'reference' =>$ref //00925294  //00925335
        );
        $result = $getTracking->__soapCall("trackingByReference",array($params));
        $object_encoded = json_encode($result);
        $object_decoded = json_decode($object_encoded,true);
        if(count($object_decoded) >0){
            // if($object_decoded['Parcel'][0]){
            //     $shortStatut = $object_decoded['Parcel'][0]['shortStatus'];
            //     return $shortStatut;
            // }else if($object_decoded['Parcel']['shortStatus']){
            //     $shortStatut = $object_decoded['Parcel']['shortStatus'];
            //     return $shortStatut;
            // }
            if($object_decoded['Parcel'][0]){
                $longStatut = $object_decoded['Parcel'][0]['longStatus'][0];
                if($longStatut == 'V'){
                    $shortStatut = $object_decoded['Parcel'][0]['shortStatus'];
                    return $shortStatut;
                }else{
                    return $longStatut;
                }
            }else if($object_decoded['Parcel']['shortStatus']){
                $longStatut = $object_decoded['Parcel']['longStatus'][0];
                if(strlen($longStatut) == 1){
                    $shortStatut = $object_decoded['Parcel']['shortStatus'];
                    return $shortStatut;
                }else{
                    return $longStatut;
                }
            }
        }else{
            return '';
        }
    }

	class WsseAuthHeader extends SoapHeader{
		private $wss_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        function __construct($user, $pass, $ns = null) {
            if ($ns) {
                $this->wss_ns = $ns;
            }
            $auth = new stdClass();
            $auth->Username = new SoapVar($user, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
            $auth->Password = new SoapVar($pass, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
            $username_token = new stdClass();
            $username_token->UsernameToken = new SoapVar($auth, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns);
            $security_sv = new SoapVar(
                    new SoapVar($username_token, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns), SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'Security', $this->wss_ns);
            parent::__construct($this->wss_ns, 'Security', $security_sv, true);
        }
	}
?>