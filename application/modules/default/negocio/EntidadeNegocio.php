<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelas regras de negócio da entidade
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */

class NDefault_EntidadeNegocio extends PDefault_Models_Entidade
{
    /**
     * Método da listagem das entidades
     *
     * @param boolean $dsStatus
     * @param integer $chvEntidade
     * @return ArrayIterator
     * @throws Zend_Exception
     */
    public function listagem($dsStatus = null, $chvEntidade = null)
    {
        try {
            $objDb = $this->getDefaultAdapter();
            $sql = $objDb->select()
                ->from(array('pj' => $this->_schema . '.' . $this->_name))
                ->join(
                    array(
                    'tp' => $this->_schema . '.tipo_pessoa'
                    ), 'pj.chv_tp_pessoa = tp.chv_tp_pessoa'
                )
                ->order('nom_fantasia asc');

            if ( !empty($dsStatus) ) {
                $sql->where('pj.ds_status = ?', $dsStatus);
            }
            //chave da empresa
            if ( !empty($chvEntidade) ) {
                $sql->where('pj.chv_pessoa_juridica = ?', $chvEntidade);
            }
            //buscando as entidades
            $coListagem = $sql->query()->fetchAll();
            return $coListagem;
        } catch ( Zend_Db_Exception $exc ) {

            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     * Método responsável pelos detalhes das entidades mostrando o endereço
     *
     * @param type $chvPessoaJuridica
     * @return ArrayIteratory
     */
    public function detalhesEntidadeEndereco($chvPessoaJuridica)
    {
        try {
            $objDb = $this->getDefaultAdapter();
            $sql = $objDb->select()
                ->from(
                    array(
                        'pje' => $this->_schema . '.pessoa_juridica_endereco'
                    )
                )
                ->joinLeft(
                    array(
                        'e' => 'endereco'
                    ), 'e.chv_endereco = pje.chv_endereco'
                )
                ->where('pje.chv_pessoa_juridica = ?', $chvPessoaJuridica);
            $coListagem = $sql->query()->fetchAll();
        } catch ( Zend_Db_Exception $exc ) {
            throw new Zend_Exception($exc->getMessage());
        }
        return $coListagem;
    }

    /**
     * Método responsávelo para alterar situação da entidade
     *
     * @param type $chvPessoaJuridica
     * @param type $dsStatus
     * @return type
     * @throws Zend_Exception
     * @throws Zend_Db_Exception
     * @return ArrayIterator
     */
    public function alteraSituacao($chvPessoaJuridica, $dsStatus = true)
    {
        try {
            if ( $chvPessoaJuridica == null ) {
                throw new Zend_Exception(
                    'Não foi informado a chave da entidade'
                );
            }
            $coPesssoaJuridica = $this->find($chvPessoaJuridica)->toArray();
            if ( count($coPesssoaJuridica) == 0 ) {
                throw new Zend_Db_Exception('Entidade não encontrado');
            }
            $dsStatus = ($dsStatus != '0') ? 1 : 0;
            return $this->update(
                array(
                    'ds_status' => $dsStatus
                ),
                array(
                    'chv_pessoa_juridica = ?' => $chvPessoaJuridica
                )
            );
        } catch ( Zend_Db_Exception $exc ) {
            throw new Zend_Exception($exc);
        }
    }

    /**
     * Método responsável para salvar os dados da entidade
     *
     * @param type $aDados
     * @return void
     */
    public function salvaEntidade($aDados)
    {
        $objBd = $this->getDefaultAdapter();
        $objBd->beginTransaction();
        try {
            if ( !empty($aDados['chv_pessoa_juridica']) ) {
                $this->update(
                    $aDados,
                    array(
                        'chv_pessoa_juridica = ?' =>
                            $aDados['chv_pessoa_juridica']
                    )
                );
                $chvPessoaJuridica = $aDados['chv_pessoa_juridica'];
            } else {
                $aDados['chv_pessoa_juridica'] = null;
                $row = $this->createRow($aDados);
                $chvPessoaJuridica = $row->save();
            }
            $objBd->commit();
            return $chvPessoaJuridica;
        } catch ( Zend_Exception $exc ) {
            $objBd->rollBack();
            Zend_Debug::dump($exc->getMessage());
            die;
            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     * Método responsável para excluir a entidade
     *
     * @param type $chvPessoaJuridica
     * @throws Zend_Exception
     * @return void
     */
    public function excluirEntidade($chvPessoaJuridica)
    {
        $objBd = $this->getDefaultAdapter();
        try {
            $coPesssoaJuridicaEndereco = $this->detalhesEntidadeEndereco(
                $chvPessoaJuridica
            );
            if ( count($coPesssoaJuridicaEndereco) != 0 ) {
                $nEndereco = new NDefault_EnderecoNegocio();
                foreach ( $coPesssoaJuridicaEndereco as $pessoaJuridica ) {
                    $nEndereco->excluir($pessoaJuridica['chv_endereco']);
                }
            }
            $objBd->beginTransaction();
            $this->delete(
                array(
                    'chv_pessoa_juridica = ?' => $chvPessoaJuridica
                )
            );
            $objBd->commit();
        } catch ( Zend_Exception $exc ) {
            $objBd->rollBack();
            throw new Zend_Exception($exc->getMessage());
        }
    }

}