<?php
/**
 * @propiedad: De Nadie
 * @Autor: Gregorio Bolivar
 * @email: elalconxvii@gmail.com
 * @Fecha de Creacion: 05/09/2015
 * @Auditado por: Gregorio J Bolivar B
 * @Fecha de Modificacion: 12/02/2016
 * @Descripcin: Encargado de Buscar Personas Juridicas antes el Seniat
 * @package: leerData.class.php
 * @version: 2.0
 */
$rif='V174429312';
$url = "http://contribuyente.seniat.gob.ve/getContribuyente/getrif?rif=$rif";
/* // Si necesitas procesar por un proxy quita el comentario
$configProxy = array(
    'http' => array(
        'proxy' => 'tcp://192.168.0.2:3128',
        'request_fulluri' => true,
    ),
);
$preproceso = stream_context_create($configProxy);

$resultado = @file_get_contents($url, False, $preproceso);
*/
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