<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelas regras de negócio das contas
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */
class NDefault_ContaNegocio extends PDefault_Models_Conta
{

    /**
     * Método responsável para gravar os dados
     *
     * @param type $dados
     * @param type $files
     * @return void
     */
    public function gravar($dados, $files = null)
    {

        try {
            $objDb = $this->getDefaultAdapter();
            $objDb->beginTransaction();
            $aDados = array(
                'chv_tp_conta' => $dados['chv_tp_conta'],
                'chv_entidade' => $dados['chv_entidade'],
                'chv_usuario' => $dados['chv_usuario'],
                'nom_conta' => $dados['nom_conta']
            );
            $dados['nr_parcela'] = (
                array_key_exists('nr_parcela', $dados)
                ) ? $dados['nr_parcela'] : 0;

            $data = new Zend_Date($dados['dat_vencimento']);
            $dados['dat_vencimento'] = $data->get('YYYY-MM-dd');

            if ( !empty($dados['dat_pagamento']) ) {
                $data = new Zend_Date($dados['dat_pagamento']);
                $dados['dat_pagamento'] = $data->get('YYYY-MM-dd');
            }

            if ( !empty($dados['dat_pagamento']) ) {
                $data = new Zend_Date($dados['dat_pagamento']);
                $dados['dat_pagamento'] = $data->get('YYYY-MM-dd');
            }

            if ( !empty($dados['vlr_conta']) ) {
                $dados['vlr_conta'] = str_replace(
                    'R$ ', '',
                    str_replace(
                        ',', '.',
                        str_replace('.', '', $dados['vlr_conta']
                        )
                    )
                );
            }

            if ( !empty($dados['vlr_pagamento']) ) {
                $dados['vlr_pagamento'] = str_replace(
                    'R$ ', '',
                    str_replace(
                        ',', '.',
                        str_replace('.', '', $dados['vlr_pagamento']
                        )
                    )
                );
            }

            $aDadosD = array(
                'dat_vencimento' => $dados['dat_vencimento'],
                'dat_pagamento' => !empty(
                    $dados['dat_pagamento']
                ) ? $dados['dat_pagamento'] : NULL,
                'vlr_conta' => $dados['vlr_conta'],
                'vlr_pagamento' => !empty($dados['vlr_pagamento'])
                        ? $dados['vlr_pagamento'] : NULL,
                'observacao' => str_replace("\\", '', $dados['observacao']),
                'nr_parcela' => $dados['nr_parcela']
            );

            if ( array_key_exists('chv_conta', $dados) ) {
                $chvConta = $dados['chv_conta'];
                $this->update(
                    $aDados, array('chv_conta = ?' => $chvConta
                    )
                );

                $objDb->update(
                    $this->_schema . '.detalhes_conta', $aDadosD, array(
                    'chv_conta = ?' => $chvConta
                    )
                );
            } else {
                $row = $this->createRow($aDados);
                $chvConta = $row->save();

                $aDadosD['chv_conta'] = $chvConta;
                $objDb->insert($this->_schema . '.detalhes_conta', $aDadosD);
            }
            if ( !empty($files) ) {
                $this->vinculaConta($_FILES['fatura'], 1, $chvConta);
                $this->vinculaConta($_FILES['recibo'], 2, $chvConta);
            }
            $objDb->commit();
        } catch ( Zend_Db_Exception $exc ) {
            $objDb->rollBack();
            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     * Método responsável pela vinculação da conta com o arquivo postado
     *
     * @param string $file
     * @param string $tipo
     * @param integer $chvConta
     * @return void
     */
    public function vinculaConta($file, $tipo, $chvConta)
    {
        if ( !empty($file['name']) ) {
            $nFile = new NDefault_AnexoNegocio();
            $objFile = new stdClass();
            $objFile->nm_file = $file['name'];
            $objFile->type_file = $file['type'];
            $objFile->size_file = $file['size'];
            //fazendo o upload do arquivo
            $chvFile = $nFile->uploadArquivo($file, $objFile);
            if ( !empty($chvFile) ) {
                $objDb = $this->getDefaultAdapter();
                $objDb->insert(
                    $this->_schema . '.conta_arquivo', array(
                    'chv_conta' => $chvConta,
                    'chv_arquivo' => $chvFile,
                    'tipo_arquivo' => $tipo
                    )
                );
            }
        }
    }

    /**
     * Listagem da contas
     *
     * @return ArrayIterator
     */
    public function listagemContaNaoPaga()
    {
        $objDb = $this->getDefaultAdapter();
        $auth = new Zend_Session_Namespace('auth');
        $sql = $objDb->select()
            ->from(
                array('c' => $this->_schema . '.' . $this->_name), array(
                'nomeConta' => 'c.nom_conta',
                'usuario' => 'c.chv_usuario', 'c.chv_conta'
                )
            )
            ->joinInner(
                array(
                'tp' => $this->_schema . '.tipo_conta'
                ), 'c.chv_tp_conta = tp.chv_tp_conta', array('tp' => 'nom_tipo')
            )
            ->joinInner(
                array(
                'pj' => 'pessoa.pessoa_juridica'
                ), 'c.chv_entidade = pj.chv_pessoa_juridica', array('pj' => 'nom_pessoa')
            )
            ->joinInner(
                array(
                'cd' => $this->_schema . '.detalhes_conta'
                ), 'c.chv_conta = cd.chv_conta', array(
                'dataVencimento' => "to_char(dat_vencimento, 'DD/MM/YYYY')",
                'valorConta' => 'vlr_conta', 'nr_parcela'
                )
            )
            ->where('cd.dat_pagamento is null')
            ->order('dat_vencimento asc')
            ->query();
        return $sql->fetchAll();
    }

    /**
     * Detalhes sobre a conta
     *
     * @param type $chvConta
     * @return ArrayIterator
     */
    public function detalhesConta($chvConta)
    {
        try {
            if ( empty($chvConta) ) {
                throw new Zend_Exception('Informe a chave da conta');
            }
            $objDb = $this->getDefaultAdapter();
            $sql = $objDb->select()
                ->from(
                    array(
                    'c' => $this->_schema . '.' . $this->_name
                    ), array('c.nom_conta', 'c.chv_conta','c.chv_usuario')
                )
                ->joinInner(
                    array(
                    'tp' => $this->_schema . '.tipo_conta'
                    ), 'c.chv_tp_conta = tp.chv_tp_conta', array('chv_tp_conta')
                )
                ->joinInner(
                    array(
                    'pj' => 'pessoa.pessoa_juridica'
                    ), 'c.chv_entidade = pj.chv_pessoa_juridica', array('c.chv_entidade')
                )
                ->joinInner(
                    array(
                    'cd' => $this->_schema . '.detalhes_conta'
                    ), 'c.chv_conta = cd.chv_conta', array(
                    'dat_vencimento' =>
                    "to_char(dat_vencimento, 'DD/MM/YYYY')",
                    'vlr_conta' => 'vlr_conta',
                    'dat_pagamento' =>
                    "to_char(dat_pagamento, 'DD/MM/YYYY')",
                    'vlr_pagamento' => 'vlr_pagamento',
                    'observacao', 'nr_parcela'
                    )
                )
                ->where('c.chv_conta = ?', $chvConta)
                ->query();
            return $sql->fetchAll();
        } catch ( Zend_Db_Exception $exc ) {
            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     * Método responsável pela listagem das contas
     *
     * @param type $chvConta
     * @return ArrayIterator
     */
    public function listagemArquivosConta($chvConta)
    {
        $objDb = $this->getDefaultAdapter();

        $sql = $objDb->select()
            ->from(
                array(
                'cf' => $this->_schema . '.conta_arquivo'
                ), array(
                'tipoConta' => new Zend_Db_Expr(
                    "CASE WHEN tipo_arquivo = '1'
                            THEN 'Fatura'  ELSE 'Recibo' end"
                )
                )
            )
            ->joinInner(
                array(
                'a' => 'file'
                ), 'a.chv_file = cf.chv_arquivo', array(
                'a.nm_file', 'a.caminho_file',
                'a.extensao_file', 'a.md5','a.size_file',
                'dat_inclusao' =>
                "to_char(dat_inclusao, 'DD/MM/YYYY HH24:MI:SS')",
                'chv_file'
                )
            )
            ->where('cf.chv_conta = ?', $chvConta)
            ->order('chv_file desc')
            ->query();
        $coArquivos = $sql->fetchAll();

        if ( count($coArquivos) != 0 ) {
            foreach ( $coArquivos as $k => $arquivo ) {
                $coArquivos[$k]['size_file'] =
                    Default_GerenciamentoController::converteByte(
                        $arquivo['size_file']
                );
            }
        }
        return $coArquivos;
    }

    /**
     * Método responsável para excluir o arquivo
     *
     * @param type $chvFile
     * @param type $chvConta
     */
    public function excluirArquivo($chvFile, $chvConta)
    {
        $objDb = $this->getDefaultAdapter();
        try {
            //desvinculando o anexo da conta
            $objDb->delete(
                $this->_schema . '.conta_arquivo', array(
                'chv_arquivo = ?' => $chvFile,
                'chv_conta = ?' => $chvConta
                )
            );
            //removendo o anexo
            $nAnexo = new NDefault_AnexoNegocio();
            $nAnexo->excluir($chvFile);
        } catch ( Zend_Db_Exception $exec ) {
            throw new Zend_Exception($exec->getMessage());
        }
    }

    /**
     * Método responsável para excluir a conta
     *
     * @param type $chvConta
     * @param type $chvUsuario
     * @return void
     */
    public function excluir($chvConta, $chvUsuario)
    {
        $objDb = $this->getDefaultAdapter();
        try {
            $objDb->beginTransaction();
            $coContas = $this->createRow(
                $this->find(
                    array(
                        'chv_conta' => $chvConta,
                        'chv_usuario' => $chvUsuario
                    )
                )->current()->toArray()
            );
            if ( count($coContas) == 0 ) {
                throw new Zend_Db_Exception('Conta não localizada');
            }
            $coArquivos = $this->listagemArquivosConta($chvConta);

            if ( count($coArquivos) != 0 ) {
                foreach ( $coArquivos as $arquivo ) {
                    $this->excluirArquivo($arquivo['chv_file'], $chvConta);
                }
            }
            $objDb->delete(
                $this->_schema . '.detalhes_conta', array(
                'chv_conta = ?' => $chvConta
                )
            );
            $this->delete(
                array(
                    'chv_conta = ?' => $chvConta,
                    'chv_usuario = ?' => $chvUsuario
                )
            );
            $objDb->commit();
        } catch ( Zend_Db_Exception $exec ) {
            $objDb->rollBack();
            throw new Zend_Exception($exec->getMessage());
        }
    }

    /**
     * Listagem das contas de acordo com o filtro
     *
     * @param type $aFiltro
     * @return void
     */
    public function listagemConta($aFiltro = null)
    {
        $objDb = $this->getDefaultAdapter();
        $sql = $objDb->select()
            ->from(
                array(
                'c' => $this->_schema . '.' . $this->_name), array(
                'nomeConta' => 'c.nom_conta',
                'usuario' => 'c.chv_usuario', 'c.chv_conta'
                )
            )
            ->joinInner(
                array(
                'tp' => $this->_schema . '.tipo_conta'
                ), 'c.chv_tp_conta = tp.chv_tp_conta', array(
                'tp' => 'nom_tipo', 'chv_tp_conta'
                )
            )
            ->joinInner(
                array(
                'pj' => 'pessoa.pessoa_juridica'
                ), 'c.chv_entidade = pj.chv_pessoa_juridica', array(
                'pj' => 'nom_pessoa', 'chv_pessoa_juridica'
                )
            )
            ->joinInner(
                array(
                'cd' => $this->_schema . '.detalhes_conta'
                ), 'c.chv_conta = cd.chv_conta', array(
                'dataVencimento' => "to_char(dat_vencimento, 'DD/MM/YYYY')",
                'valorConta' => 'vlr_conta',
                'nr_parcela',
                'dataPagamento' => "to_char(dat_pagamento, 'DD/MM/YYYY')",
                'valorPago' => 'vlr_pagamento'
                )
            )
            ->order('dat_vencimento asc');
        //Filtros
        if ( !empty($aFiltro['chv_tp_conta']) ) {
            $sql->where('c.chv_tp_conta = ?', $aFiltro['chv_tp_conta']);
        }

        if ( !empty($aFiltro['chv_entidade']) ) {
            $sql->where('c.chv_entidade = ?', $aFiltro['chv_entidade']);
        }

        if ( !empty($aFiltro['data_venc_inicial']) &&
            !empty($aFiltro['data_venc_final']) ) {
            $data = new Zend_Date($aFiltro['data_venc_inicial']);
            $sql->where('cd.dat_vencimento >= ?', $data->get('YYYY-MM-dd'));
            $data = new Zend_Date($aFiltro['data_venc_final']);
            $sql->where('cd.dat_vencimento <= ?', $data->get('YYYY-MM-dd'));
        }

        if ( !empty($aFiltro['data_pag_inicial']) &&
            !empty($aFiltro['data_pag_final']) ) {
            $data = new Zend_Date($aFiltro['data_pag_inicial']);
            $sql->where('cd.dat_pagamento >= ?', $data->get('YYYY-MM-dd'));
            $data = new Zend_Date($aFiltro['data_pag_final']);
            $sql->where('cd.dat_pagamento <= ?', $data->get('YYYY-MM-dd'));
        }
        return $sql->query()->fetchAll();
    }

    /**
     * Méto responsável para somar as contas de acordo com o parâmetro
     *
     * @param array $post
     * @return ArrayIterator
     */
    public function somaConta($aFiltro)
    {
        $objDb = $this->getDefaultAdapter();
        $sql = $objDb->select()
            ->from(
                array('c' => $this->_schema . '.' . $this->_name), array()
            )
            ->joinInner(
            array('cd' => $this->_schema . '.detalhes_conta'), 'c.chv_conta = cd.chv_conta', array(
            'ValorConta' => new Zend_Db_Expr("SUM(cd.vlr_conta)"),
            'ValorPagam' => new Zend_Db_Expr("SUM(cd.vlr_pagamento)")
            )
        );
        //Filtros
        if ( !empty($aFiltro['chv_tp_conta']) ) {
            $sql->where('c.chv_tp_conta = ?', $aFiltro['chv_tp_conta']);
        }

        if ( !empty($aFiltro['chv_entidade']) ) {
            $sql->where('c.chv_entidade = ?', $aFiltro['chv_entidade']);
        }

        if ( !empty($aFiltro['data_venc_inicial']) &&
            !empty($aFiltro['data_venc_final']) ) {
            $data = new Zend_Date($aFiltro['data_venc_inicial']);
            $sql->where('cd.dat_vencimento >= ?', $data->get('YYYY-MM-dd'));
            $data = new Zend_Date($aFiltro['data_venc_final']);
            $sql->where('cd.dat_vencimento <= ?', $data->get('YYYY-MM-dd'));
        }

        if ( !empty($aFiltro['data_pag_inicial']) &&
            !empty($aFiltro['data_pag_final']) ) {
            $data = new Zend_Date($aFiltro['data_pag_inicial']);
            $sql->where('cd.dat_pagamento >= ?', $data->get('YYYY-MM-dd'));
            $data = new Zend_Date($aFiltro['data_pag_final']);
            $sql->where('cd.dat_pagamento <= ?', $data->get('YYYY-MM-dd'));
        }
        return $sql->query()->fetchAll();
    }
}