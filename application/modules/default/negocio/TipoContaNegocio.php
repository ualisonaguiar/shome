<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelo tratamento dos tipos de contas
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */

class NDefault_TipoContaNegocio extends PDefault_Models_TipoConta
{

    /**
     * Método responsável para cadastrar o tipo de conta
     *
     * @param type $post
     * @return type
     * @throws Zend_Exception
     */
    public function gravar($post)
    {
        if ( empty($post['nom_tipo']) ) {
            throw new Zend_Exception(
                'Não foi informado o nome do tipo de conta'
            );
        }
        $objDb = $this->getDefaultAdapter();
        try {
            $objDb->beginTransaction();

            if ( !empty($post['chv_tp_conta']) ) {
                $chvTipoConta = $post['chv_tp_conta'];
                $this->update(
                    array(
                    'nom_tipo' => $post['nom_tipo']
                    ),
                    array(
                        'chv_tp_conta = ?' => $post['chv_tp_conta']
                    )
                );
            } else {
                unset($post['chv_tp_conta']);
                $row = $this->createRow($post);
                $chvTipoConta = $row->save();
            }
            $objDb->commit();
            return $chvTipoConta;
        } catch ( Zend_Db_Exception $exc ) {
            $objDb->rollBack();
            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     * Método responsável pela listagem
     *
     * @param integer $chvTpConta
     * @return ArrayIterator
     */
    public function listagem($chvTpConta = null)
    {
        $where = array();
        if ( !empty($chvTpConta) ) {
            $where['chv_tp_conta = ?'] = $chvTpConta;
        }

        return $this->fetchAll($where, 'nom_tipo asc')->toArray();
    }

    /**
     * Método responsávelo pela listagegem dos tipos de
     * conta vinculada a conta
     *
     * @param integer $chvTpConta
     * @return ArrayIterator
     */
    public function listagemVinculacaoTipoConta($chvTpConta = null)
    {
        $objDb = $this->getAdapter();

        $sql = $objDb->select()
            ->from(
                array(
                    'tp' => $this->_schema . '.tipo_conta'
                ), array(
                    'chv_tp_conta', 'nom_tipo'
                )
            )
            ->joinLeft(
                array(
                    'c' => $this->_schema . '.conta'
                ),
                'tp.chv_tp_conta = c.chv_tp_conta',
                array(
                    'quantidade' => new Zend_Db_Expr(
                        'count(c.chv_tp_conta)'
                    )
                )
            )
            ->group(array('c.chv_tp_conta', 'tp.nom_tipo', 'tp.chv_tp_conta'))
            ->order(array('quantidade desc', 'nom_tipo asc'));

        if ( !empty($chvTpConta) ) {
            $sql->where('tp.chv_tp_conta = ?', $chvTpConta);
        }
        $sql->order('nom_tipo asc');
        return $sql->query()->fetchAll();
    }

    /**
     * Método responsável para excluir o tio de conta
     *
     * @param type $chvTpConta
     */
    public function excluir($chvTpConta)
    {
        try {
            $coDetalhes = $this->listagemVinculacaoTipoConta($chvTpConta);


            if ( $coDetalhes[0]['quantidade'] != 0 ) {
                throw new Zend_Exception(
                    'Não foi possível excluir este tipo de conta, '
                    . 'pois esta relacioando alguma conta'
                );
            }
            $this->delete(array('chv_tp_conta = ?' => $chvTpConta));
        } catch ( Zend_Db_Exception $exc ) {
            throw new Zend_Exception($exc->getMessage());
        }
    }

}