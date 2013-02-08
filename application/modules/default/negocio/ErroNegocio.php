<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelas regras de negócio de tratamento dos erros
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */
class NDefault_ErroNegocio extends PDefault_Models_Erros
{

    /**
     * Método responsável para registrar o erro
     *
     * @param type $erro
     * @return void
     */
    public function registraErro($erro = array())
    {
        $aErro = array();
        $aErro['chv_erro'] = null;
        $aErro['ds_file'] = $erro->getFile();
        $aErro['ds_line'] = $erro->getLine();
        $aErro['ds_message'] = $erro->getMessage();
        $aTracert = $erro->getTrace();
        $objDB = $this->getDefaultAdapter();
        $chvErro = $this->createRow($aErro)->save();
        //gravando o tracert do erro
        if ( count($aTracert) != 0 ) {
            foreach ( $aTracert as $tracert ) {
                $tracert['function'] = (
                    array_key_exists(
                        'function', $tracert
                    )
                    ) ? $tracert['function'] : null;
                $tracert['class'] = (
                    array_key_exists(
                        'class', $tracert
                    )
                    ) ? $tracert['class'] : null;
                $this->insereErroTracert(
                    $tracert['file'], $tracert['line'], $tracert['function'], $tracert['class'], $chvErro
                );
            }
        }
        return $chvErro;
    }

    /**
     * Método responsável pela listagem dos erros encontrados no sistema
     *
     * @param type $id
     * @param type $dsStatus
     * @return type
     */
    public function listagemErro($id = null, $dsStatus = null)
    {
        $objData = $this->getAdapter();
        try {
            $sqlErro = $this->select()
                ->order('dat_log desc')
                ->limit(300);
            if ( !empty($id) ) {
                $sqlErro->where('chv_erro = ?', $id);
            }

            $dsStatus = ($dsStatus) ? "true" : "false";

            $result = $sqlErro->where('ds_status = ?', $dsStatus)->query();

            if ( $result ) {
                $coErro = $result->fetchAll();
                $aErro = array();
                if ( count($coErro) != 0 ) {
                    foreach ( $coErro as $key => $erro ) {
                        $data = new Zend_Date($erro['dat_log']);
                        $erro['dat_log'] = $data->get('dd/MM/YYYY HH:m:s');

                        if ( !empty($erro['dat_correcao']) ) {
                            $data = new Zend_Date($erro['dat_correcao']);
                            $erro['dat_correcao'] = $data->get(
                                'dd/MM/YYYY HH:m:s'
                            );
                        }

                        $aErro[$key]['erro'] = $erro;
                        $sqlTracert = $objData->select()
                            ->from('erro_trace', '*', $this->_schema)
                            ->where('chv_erro = ?', $erro['chv_erro']);

                        $resultTracer = $sqlTracert->query();
                        $aErro[$key]['tracert'] = $resultTracer->fetchAll();
                    }
                }
                return $aErro;
            }
        } catch ( Zend_Db_Exception $exc ) {
            Zend_Debug::dump($exc->getMessage());
            die;
        }
    }

    /**
     * Método responsável que salva a solução do erro gerado no sistema
     *
     * @param type $chvErro
     * @param type $dsSolucao
     * @throws Zend_Exception
     */
    public function resolverErro($chvErro, $dsSolucao)
    {
        try {
            $this->update(
                array(
                'dat_correcao' => new Zend_Db_Expr('now()'),
                'ds_solucao' => $dsSolucao,
                'ds_status' => 'false'
                ), array(
                'chv_erro = ?' => $chvErro
                )
            );
        } catch ( Zend_Db_Exception $exc ) {
            throw new Zend_Exception($exc->getMessage());
        }
    }

}