<?php
$rif='V174429312';
$url = "http://contribuyente.seniat.gob.ve/getContribuyente/getrif?rif=$rif";
$resultado = @file_get_contents($url);
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