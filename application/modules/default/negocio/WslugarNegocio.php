<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelo pela conexão do web service
 * pesquisa de endereço com a kinghost
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */

class NDefault_WslugarNegocio
{
    /**
     * Método responsável pela busca das cidades
     *
     * @param integer $chvCidade
     * @return array
     */
    public static function buscarCidade($chvCidade = null)
    {
        $options = array(
            'location' => LOCATION_WSLUGAR,
            'uri' => URI_WSLUGAR
        );
        $client = new SoapClient(null, $options);
        //buscando os estados do Brasil
        $aCidadesWs = $client->buscarCidade($chvCidade);
        $aResponse = array('Cidade' => '', 'Estado' => '');
        if ( !empty($aCidadesWs) ) {
            $aCidade = explode('&', str_replace('|', '', $aCidadesWs));
            $aResponse['Cidade'] = $aCidade[1];
            $aResponse['Estado'] = $aCidade[4];
        }
        return $aResponse;
    }

    /**
     * Método responsável pela busca do cep
     *
     * @param string $cep
     * @return string
     * @throws Zend_Exception
     */
    public function buscaCep($cep)
    {
        $options = array(
            'location' => LOCATION_WSLUGAR,
            'uri' => URI_WSLUGAR
        );
        $client = new SoapClient(null, $options);
        $result = $client->buscaCep($cep);

        if ( empty($result) ) {
            throw new Zend_Exception('Cep não localizado');
        }

        $aResult = explode('|', $result);
        $aResultado = array();
        foreach ( $aResult as $k => $rs ) {
            if ( !empty($rs) ) {
                $aRs = explode('&', $rs);
                $aResultado[$aRs[0]] = $aRs[1];
            }
        }
        return $aResultado;
    }
}