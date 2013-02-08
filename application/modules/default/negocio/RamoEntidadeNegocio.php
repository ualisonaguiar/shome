<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelas regras de negócio do ramo da entidade
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */
class NDefault_RamoEntidadeNegocio extends PDefault_Models_RamoEntidade
{

    /**
     * Listagem do ramo da entidade
     *
     * @param integer $chvTpPessoa
     * @return type
     */
    public function listagem($chvTpPessoa = null)
    {
        $sWhere = array();
        $aRamoEntidade = array('' => 'Selecione o ramo');

        if ( !empty($chvTpPessoa) ) {
            $sWhere['chv_tp_pessoa = ?'] = $chvTpPessoa;
            unset($aRamoEntidade);
        }

        $coEntidade = $this->fetchAll($sWhere, 'ds_tipo_pessoa asc')->toArray();
        if ( count($coEntidade) != 0 ) {
            foreach ( $coEntidade as $ramoEntidade ) {
                $aRamoEntidade[$ramoEntidade['chv_tp_pessoa']] =
                    $ramoEntidade['ds_tipo_pessoa'];
            }
        }
        return $aRamoEntidade;
    }

    /**
     * Método responsável para gravar os dados do ramo da entidade
     *
     * @param type $post
     * @return void
     */
    public function gravar($post)
    {
        try {
            if ( empty($post['ds_tipo_pessoa']) ) {
                throw new Zend_Exception('Não foi informado o tipo de pessoa');
            }
            if ( array_key_exists('chv_tp_pessoa', $post) ) {
                $row = $this->fetchRow(
                    $this->select()->where(
                        'chv_tp_pessoa = ? ', $post['chv_tp_pessoa']
                    )
                );
                if ( !$row ) {
                    throw new Zend_Exception(
                        'Tipo de entidade não encontrada.'
                    );
                }
            } else {
                $row = $this->createRow();
            }
            $row->ds_tipo_pessoa = $post['ds_tipo_pessoa'];
            return $row->save();
        } catch ( Zend_Db_Exception $exc ) {
            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     * Método responsável para excluir o ramo
     *
     * @param type $chvTpPessoa
     * @throws Zend_Exception
     * @return void
     */
    public function excluir($chvTpPessoa)
    {
        try {
            if ( empty($chvTpPessoa) ) {
                throw new Zend_Exception('Não foi informado a chave');
            }
            $nEntidade = new NDefault_EntidadeNegocio();
            $coEntidade = $nEntidade->select()
                ->where('chv_tp_pessoa = ?', $chvTpPessoa)
                ->query()
                ->rowCount();
            if ( $coEntidade != 0 ) {
                throw new Zend_Exception(
                    'Esta tipo de conta esta vinculada a uma conta'
                );
            }
            $this->delete(
                array(
                    'chv_tp_pessoa = ?' => $chvTpPessoa
                )
            );
        } catch ( Exception $exc ) {
            throw new Zend_Exception($exc->getMessage());
        }
    }

}