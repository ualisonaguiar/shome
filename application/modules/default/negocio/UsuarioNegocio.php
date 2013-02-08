<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelo tratamento dos usuários do sistema
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */

class NDefault_UsuarioNegocio extends PDefault_Models_Usuario
{
    /**
     * Listagem dos usuários cadastros
     *
     * @param integer $chvUsuario
     * @param bolean $status
     * @return ArrayIterator
     */
    public function listagemUsuario($chvUsuario = null, $status = true)
    {
        $objDb = $this->getAdapter();
        $sql = $objDb->select()
            ->from(array('u' => $this->_schema . '.' . $this->_name))
            ->order('u.nom_usuario asc');
        //chave do usuário
        if ( !empty($chvUsuario) ) {
            $sql->where('u.chv_usuario = ?', $chvUsuario);
        }
        //situação do usuário
        if ( $status ) {
            $sql->where('u.dat_exclusao is null');
        } else {
            $sql->where('u.dat_exclusao is not null');
        }

        return $sql->query()->fetchAll();
    }

    /**
     * Método responsável para alterar a situação do usuário
     *
     * @param integer $chvUsuario chave do usuário
     * @param boolean $dsStatus status do usuário
     */
    public function alteraSituacao($chvUsuario, $dsStatus = true)
    {
        try {
            if ( $chvUsuario == null ) {
                throw new Zend_Exception(
                    'Não foi informado a chave do usuário'
                );
            }
            $coUsuario = $this->find($chvUsuario)->toArray();
            if ( count($coUsuario) == 0 ) {
                throw new Zend_Db_Exception('Usuário não encontrado');
            }
            $dsStatus = ($dsStatus != '0') ? 1 : 0;
            return $this->update(
                array(
                    'ds_status' => $dsStatus
                ),
                array(
                    'chv_usuario = ?' => $chvUsuario
                )
            );
        } catch ( Zend_Db_Exception $exc ) {
            throw new Zend_Exception($exc);
        }
    }

    /**
     * método responsável pela exclusão do usuário
     *
     * @param type $chvUsuario
     * @throws Exception
     * @throws Zend_Exception
     */
    public function excluirUsuario($chvUsuario)
    {
        try {
            if ( empty($chvUsuario) ) {
                throw new Zend_Exception(
                    'Não foi informado a chave do usuário'
                );
            }
            $objDb = $this->getAdapter();
            //verificando se existe usuário informado
            $coUsuario = $this->find($chvUsuario)->toArray();
            if ( count($coUsuario) == 0 ) {
                throw new Zend_Exception('Usuário não existente no sistema');
            }

            $objDb->beginTransaction();
            $this->update(
                array(
                'dat_exclusao' => 'now',
                'ds_status'    => 'false',
                'ds_login'     => null,
                'ds_senha'     => null
                ), array(
                'chv_usuario = ?' => $chvUsuario
                )
            );
            $objDb->commit();
        } catch ( Zend_Db_Exception $exc ) {
            $objDb->rollBack();
            throw new Exception($exc);
        }
    }

    /**
     * Método responsável pelo reenvio da senha
     *
     * @param integer $chvUsuario
     * @param string $novaSenha
     * @throws Exception
     * @throws Zend_Exception
     */
    public function reenviarSenha($chvUsuario, $novaSenha)
    {
        try {
            if ( empty($chvUsuario) ) {
                throw new Zend_Exception(
                    'Não foi informado a chave do usuário'
                );
            }
            $objDb = $this->getAdapter();
            $objDb->beginTransaction();
            $coUsuario = $this->find($chvUsuario)->toArray();
            //verificando se existe usuário informado
            if ( count($coUsuario) == 0 ) {
                throw new Zend_Exception('Usuário não existente no sistema');
            }
            //verificando se o usuário possui e-mail cadastrado
            if ( empty($coUsuario[0]['ds_email']) ) {
                throw new Zend_Exception(
                    'Usuário informado não possui e-mail cadastrado'
                );
            }

            //verificando se o usuário possui acesso ao sistema
            if ( empty($coUsuario[0]['ds_login']) ) {
                throw new Zend_Exception(
                    'Usuário informado não possui login cadastrado'
                );
            }

            $this->update(
                array(
                    'ds_senha' => md5($novaSenha)
                ),
                array(
                    'chv_usuario = ?' => $chvUsuario
                )
            );
            $objDb->commit();
        } catch ( Zend_Exception $exc ) {
            $objDb->rollBack();
            throw new Exception($exc);
        }
    }

    /**
     * Método responsável para salvar o usuário
     *
     * @param array $aUsuario
     * @return int
     */
    public function salvarUsuario($aUsuario)
    {
        try {
            $objBd = $this->getDefaultAdapter();
            $objBd->beginTransaction();
            $coUsuario = $this->listagemUsuario();

            if ( !empty($aUsuario['chv_usuario']) ) {
                $coUsuario = $this->find(
                    $aUsuario['chv_usuario']
                )->current()->toArray();
                $aUsuario['cpf_usuario'] = $coUsuario['cpf_usuario'];
                $chvUsuario = $aUsuario['chv_usuario'];
                unset($aUsuario['chv_usuario']);
                $this->update(
                    $aUsuario,
                    array(
                        'chv_usuario = ?' => $chvUsuario
                    )
                );
            } else {
                $row = $this->createRow($aUsuario);
                $chvUsuario = $row->save();
            }
            $objBd->commit();
            return $chvUsuario;
        } catch ( Zend_Db_Exception $exc ) {
            $objBd->rollBack();
            throw new Zend_Db_Exception($exc->getMessage());
        }
    }

    /**
     * Método que retorna os logins cadastrados
     */
    public function getListagemLogin()
    {
        return $this->select()
            ->from($this->_schema.'.'.$this->_name, array('ds_login'))
            ->query()
            ->fetchAll();
    }
}
