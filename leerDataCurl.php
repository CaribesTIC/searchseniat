<?php
require('simple_html_dom.php');

/**
 * Clase encargada de gestionar diferentes paginas mediantes consumo sea por curl
 * hay que tener claro que podemos ejercer el consumo del html en caso que alla un cambio del resultado del
 * html del CNE hay que modificar las clases porque cambian las posiciones
 */
class SearchCurl {

    /**
     * Permite consumir e interpretar la informacion del resultado del curl para solo extraer los datos necesarios
     * @author Gregorio Jose Bolivar Bolivar <elalconxvii@gmail.com>
     * @param string $nac Nacionalidad de la persona
     * @param integer $ci Cedula de la persona
     * @return string Json del resultado consultado de los datos asociados a la persona
     */
    public static function searchSeniat($rif) {
        $url = "http://www.elrif.com/?rif=$rif";
        $resource = self::geUrl($url);
       
        
        $findme[0] = 'RIF de la empresa:'; // Identifica si tiene identificacion de empresa
        $findme[1] = 'Cédula del contribuyente';    
        $pos0 = strpos($resource, $findme[0]);
        $pos1 = strpos($resource, $findme[1]);

        if ($pos0 == TRUE OR $pos1 == TRUE) {
            /*** a new dom object ***/ 
            $dom = new domDocument; 

            /*** load the html into the object ***/ 
            @$dom->loadHTML($resource); 

            /*** discard white space ***/ 
            $dom->preserveWhiteSpace = false; 

            /*** the table by its tag name ***/ 
            $tables = $dom->getElementsByTagName('table'); 


            /*** get all rows from the table ***/ 
            $rows = $tables->item(2)->getElementsByTagName('tr'); 

            /*** loop over the table rows ***/ 
            foreach ($rows AS $item=>$values) 
            { 
                $datoJson['error']=0;
                
                $resource = explode(":", $values->textContent);


                $a=(bool)strpos($resource[0], 'Razón Social');
                if($a){
                   $datoJson['razonSocial'] =  self::limpiarCampo($resource[1]);
                }


                $b=(bool)strpos($resource[0], 'Sector económico');
                if($b){
                   $datoJson['sector'] =  self::limpiarCampo($resource[1]);
                }else{
                    $datoJson['sector'] = ' ';
                }

                $c=(bool)strpos($resource[0], 'Condición');
                if($c){
                   $datoJson['condicion'] =  self::limpiarCampo($resource[1]);
                }

                $d=(bool)strpos($resource[0], ' RIF del contribuyente');
                if($d){
                   $datoJson['rif'] =  self::limpiarCampo($resource[1]);
                }
            }
        }else{
            $datoJson['error']=1;
        }

        echo json_encode($datoJson);
    }

    /**
     * Permite consultar cualquier pagina mediante curl
     * @author Gregorio Jose Bolivar Bolivar <elalconxvii@gmail.com>
     * @param string $url url al cual desea consultar
     * @return string HTML del resultado consultado
     */
    public static function geUrl($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // almacene en una variable
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if (curl_exec($curl) === false) {
            echo 'Curl error: ' . curl_error($curl);
        } else {
            $return = curl_exec($curl);
        }
        curl_close($curl);

        return $return;
    }

    /**
     * Permite limpiar los valores del renorno del carro (\n \r \t) 
     * @author Gregorio Jose Bolivar Bolivar <elalconxvii@gmail.com>
     * @param string $valor Valor que queremos limpiar de caracteres no permitidos
     * @return string Te devuelve los mismo valores pero sin los valores del renorno del carro
     */
    public static function limpiarCampo($valor) {
        $rempl = array('\n', '\t');
        $r = trim(str_replace($rempl, ' ', $valor));
        return str_replace("\r", "", str_replace("\n", "", str_replace("\t", "", $r)));
    }

}

$curls = new SearchCurl();
$curls->searchSeniat('V174429312');
?>
