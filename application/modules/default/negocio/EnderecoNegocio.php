<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelas regras de negócio do endereço
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */

class NDefault_EnderecoNegocio extends PDefault_Models_Endereco
{

    /**
     * Método responsável para incluir ou alterar o endereço
     *
     * @param array $post
     * @return integer chve do endereço
     * @throws Zend_Exception
     */
    public function gravar($post)
    {
        $aDados = array(
            'ds_logradouro' => $post['logradouro'],
            'ds_bairro' => $post['bairro'],
            'ds_complemento' => $post['complemento'],
            'ds_cep' => str_replace('-', '', $post['dsCep']),
            'chv_cidade' => $post['chv_cidade'],
        );

        $objBd = $this->getDefaultAdapter();
        try {
            $objBd->beginTransaction();
            if ( !empty($post[$this->_primary]) ) {

            } else {
                $row = $this->createRow($aDados);
                $chvEndereco = $row->save();
            }
            $objBd->commit();
            return $chvEndereco;
        } catch ( Exception $exc ) {
            $objBd->rollBack();
            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     * Método responsável pela listagem dos endereços
     *
     * @param integer $chvEndereco
     * @return ArrayIterator
     * @throws Zend_Exception
     */
    public function listagem($chvEndereco = null)
    {
        $aWhere = array();
        if ( !empty($chvEndereco) ) {
            if ( strpos($chvEndereco, ',') ) {
                $aWhere['chv_endereco IN(?)'] = explode(',', $chvEndereco);
            } else {
                $aWhere['chv_endereco IN(?)'] = $chvEndereco;
            }
        }

        try {
            $coEnderecos = $this->fetchAll($aWhere)->toArray();
            if ( count($coEnderecos) == 0 ) {
                throw new Zend_Exception('Endereço inexistente');
            }

            foreach ( $coEnderecos as $key => $end ) {
                if ( !empty($end['chv_cidade']) ) {
                    $coCidade = NDefault_WslugarNegocio::buscarCidade(
                        $end['chv_cidade']
                    );
                    $coEnderecos[$key]['nom_cidade'] = $coCidade['Cidade'];
                    $coEnderecos[$key]['nom_estado'] = $coCidade['Estado'];
                    $coEnderecos[$key]['ds_telefone1'] = $end['ds_telefone1'];
                    $coEnderecos[$key]['ds_telefone2'] = $end['ds_telefone2'];
                }
            }
            return $coEnderecos;
        } catch ( Exception $exc ) {
            Zend_Debug::dump($exc->getMessage());
            die;
            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     * Método responsável para excluir o endereço
     *
     * @param type $chvEndereco
     * @return void
     */
    public function excluir($chvEndereco)
    {
        $objBd = $this->getDefaultAdapter();
        try {
            $objBd->beginTransaction();
            $objBd->delete(
                'pessoa.pessoa_juridica_endereco',
                array(
                    'chv_endereco = ?' => $chvEndereco
                )
            );
            $this->delete(array('chv_endereco = ?' => $chvEndereco));
            $objBd->commit();
        } catch ( Exception $exc ) {
            $objBd->rollBack();
            throw new Zend_Exception(
                'Não foi possível excluir este endereço'
            );
        }
    }

    /**
     * Método responsável para vincular o endereço com a pessoa jurídica
     *
     * @param type $chvPesJur
     * @param type $chvEnd
     * @param type $foneP
     * @param type $foneO
     * @throws Zend_Exception
     * @return void
     */
    public function vinculaEnderecoPessoaJuridica(
        $chvPesJur, $chvEnd, $foneP, $foneO
    )
    {
        $objBd = $this->getDefaultAdapter();
        try {
            $objBd->delete(
                'pessoa.pessoa_juridica_endereco',
                array(
                    'chv_pessoa_juridica = ?' => $chvPesJur,
                    'chv_endereco = ?' => $chvEnd
                )
            );
            $objBd->insert(
                'pessoa.pessoa_juridica_endereco',
                array(
                    'chv_pessoa_juridica' => $chvPesJur,
                    'chv_endereco' => $chvEnd
                )
            );

            $objBd->update(
                'endereco',
                array(
                    'ds_telefone1' => $foneP,
                    'ds_telefone2' => $foneO
                ),
                array(
                    'chv_endereco = ?' => $chvEnd
                )
            );
        } catch ( Zend_Db_Exception $exc ) {
            throw new Zend_Exception($exc->getMessage());
        }
    }

}