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
class PDefault_Models_Usuario extends Zend_Db_Table
{

    /**
     * SCHEMA do banco de dados.
     * @var string
     */
    protected $_schema = "acesso";

    /**
     * Nome da tabela
     * @var string
     */
    protected $_name = "usuario";

    /**
     * Chave primaria
     * @var string
     */
    protected $_primary = "chv_usuario";

    /**
     * Define the logic for new values in the primary key.
     * May be a string, boolean true, or boolean false.
     *
     * @var mixed
     */
    protected $_sequence = "acesso.usuario_chv_usuario_seq";

    /**
     * Autêntica os usuários do sistema.
     *
     * @return integer
     */
    final public function autenticar($usuario, $senha)
    {
        try {
            $objZendAuthAdapter = new Zend_Auth_Adapter_DbTable(
                    Zend_Db_Table::getDefaultAdapter(),
                    $this->_schema . '.' . $this->_name,
                    'ds_login',
                    'ds_senha',
                    "MD5(?) AND ds_status = true"
            );
            //seta login e senha do usuário
            $objZendAuthAdapter
                ->setIdentity(trim($usuario))
                ->setCredential($senha);
            //define namespace para guardar as informações do usuário na session
            $objAuth = Zend_Auth::getInstance();
            $objAuth->setStorage(new Zend_Auth_Storage_Session('Zend_Auth'));
            //autentica usuário
            $objAuthResult = $objAuth->authenticate($objZendAuthAdapter);
            if ( $objAuthResult->isValid() ) {
                //armazena dados selecionado do usuário
                $arrUsuario = $objZendAuthAdapter->getResultRowObject();
                $objAuth->getStorage()->write($arrUsuario);
            }
            //retorna Zend_Auth_Result
            return $objAuthResult->getCode();
        } catch ( Exception $exc ) {
            throw new Zend_Exception($exc->getTraceAsString());
        }
    }

}