<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Usuario
 *
 * @author Ualison
 */
class PDefault_Models_Erros extends Zend_Db_Table
{

    /**
     * Nome do schema
     *
     * @var String
     */
    protected $_schema = 'admin';

    /**
     * Nome da tabela
     * @var string
     */
    protected $_name = "erro";

    /**
     * Chave primaria
     * @var string
     */
    protected $_primary = "chv_erro";

    /**
     * Método responsável para registrar o tracert do erro
     *
     * @param type $dsFile nome do arquivo
     * @param type $dsLine linha do arquivo
     * @param type $dsFunction nome da função
     * @param type $dsClass nome da classe
     * @param type $chvErro chave do erro
     */
    public function insereErroTracert($dsFile, $dsLine, $dsFunction, $dsClass, $chvErro)
    {
        try {
            $objDb = $this->getAdapter();
            $objDb->insert(
                $this->_schema . '.erro_trace', array(
                'chv_erro' => $chvErro,
                'ds_file' => $dsFile,
                'ds_line' => $dsLine,
                'ds_function' => $dsClass,
                'ds_class' => $dsClass
                )
            );
        } catch ( Zend_Db_Exception $exc ) {
            Zend_Debug::dump($exc->getMessage());
            die;
        }
    }

}