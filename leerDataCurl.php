<?php
/**
 * @propiedad: De Nadie
 * @Autor: Gregorio Bolivar
 * @email: elalconxvii@gmail.com
 * @Fecha de Creacion: 05/09/2015
 * @Auditado por: Gregorio J Bolivar B
 * @Fecha de Modificacion: 12/02/2016
 * @Descripcin: Encargado de Buscar Personas Juridicas antes el Seniat mediante CURL
 * @package: leerData.class.php
 * @version: 2.0
 */
	$rif='V236114682';
	$url = "http://contribuyente.seniat.gob.ve/getContribuyente/getrif?rif=$rif";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');//esto si usa metodo GET
    /* Comentar estas dos lineas si no usa proxy */
    //curl_setopt ($ch, CURLOPT_PROXY, "http://192.168.0.5");
    //curl_setopt ($ch, CURLOPT_PROXYPORT, 8080);
    /* ----------------------------------------- */
    $resultado = curl_exec ($ch);
    if ($resultado) {
    	try {
    		if (substr($resultado, 0, 1) != '<')
    			throw new Exception($resultado);
    		$xml = simplexml_load_string($resultado);
    		if (!is_bool($xml)) {
    			$elements = $xml->children('rif');
    			$seniat = array();
    			$response_json['result'] = 1;
    			foreach ($elements as $indice => $node) {
    				$index = strtolower($node->getName());
    				$seniat[$index] = (string) $node;
    			}
    			$response_json['data'] = $seniat;
    		}
    	} catch (Exception $e) {
    		$result = explode(' ', @$resultado, 2);
    		$response_json['result'] = (int) $result[0];
    	}
    } else {
    	$response_json['result'] = 0;
    	$response_json['data'] = '452 El Contribuyente no está registrado ';
    }
    die(json_encode($response_json));
    ?>