<?php
/*
 * Name:	ocaku API
 * URL:		http://ocacu.com
 * Version:	0.1
 * Date:	06/08/2012
 * Author:	Chema Garrido
 * License: GPL v3
 * Notes:	API Class for Ocaku.com
 */
class ocaku{
	private $returnReq=false;//returns the output for the request
	private $apiUrl='http://ocacu.com/api/';//url for the requests
	private $timeout=10;//timeout for the request
	
	function __construct($return=false){
		$this->returnReq=$return;
	}
	
	public function newSite($data){
		return json_decode($this->sendRequest("newSite",$data,true));
	}
	
	public function editSite($data){
		return $this->sendRequest("editSite",$data,$this->returnReq);
	}
	
	public function rememberKEY($data){
		return $this->sendRequest("rememberKEY",$data,$this->returnReq);
	}
	
	//sends the request to the server, uses curl
	private function sendRequest($action,$data,$return=false){
		$ch = curl_init();
		if ($ch) {
			$data=$this->generateArrayParam($data);//var_dump($data);
			curl_setopt($ch, CURLOPT_URL,$this->apiUrl);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"&action=$action".$data);
			curl_setopt($ch, CURLOPT_TIMEOUT,$this->timeout); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output=curl_exec ($ch);
			curl_close ($ch); 
			if ($return) return $server_output;
		}
		else return false;
	}
	//end send request

////////////////////////Class Tools////////////////////////
	//Generate array parameter
    private function generateArrayParam($values){
    	$commandstring = '';
        if (is_array($values)) { 
            foreach ($values as $key => $value) {
                  $commandstring .= '&'.$key."=".$value;
            }
        } 
        else  $commandstring = $values;//not array    
        return $commandstring;
    }
	//end Generate array parameter
}
?>