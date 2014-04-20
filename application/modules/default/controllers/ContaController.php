<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pela controller do erro
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */
class Default_ContaController extends Default_SegurancaController
{

    /**
     * Método principal.
     *
     * @return void
     */
    public function indexAction()
    {
        $nConta = new NDefault_ContaNegocio();
        $coContas = $nConta->listagemContaNaoPaga();

        if ( count($coContas) != 0 ) {
            foreach ( $coContas as $k => $contas ) {
                if ( $contas['usuario'] !=
                    $this->_objDadosUsuario->chv_usuario ) {
                    unset($coContas[$k]);
                }
            }

            foreach ( $coContas as $k => $conta ) {
                $coContas[$k]['nr_parcela'] = $this->quantidadeParcelas(
                    $conta['nr_parcela'], $conta['nomeConta']
                );
            }
        }
        $this->view->coContas = $coContas;
    }

    /**
     * Nova conta
     *
     * @return void
     */
    public function novoAction()
    {
        $form = new Form_Conta();
        $form->getElement('nr_parcela')
            ->setAttrib('disabled', true);
        $this->view->form = $form;
        if ( $this->getRequest()->isPost() ) {
            $objSession = new Zend_Session_Namespace('Data');
            $post = $this->getAllParams();

            //validando os valores
            if ( $form->isValid($post) ) {
                $post = $form->getValidValues($post);
                if ( (int) $post['gerar_conta'] == 1 ) {
                    $nrParcelas = $post['nr_parcela'];
                    for ( $i = 0; $i < $nrParcelas; $i++ ) {
                        $post['nr_parcela'] = $i + 1;
                        if ( $i != 0 ) {
                            $post['dat_vencimento'] =
                                $this->retornaPeriodicidade(
                                $post['dat_vencimento'], $post['tipo_vencimento']
                            );
                        }
                        $this->salvar($post);
                    }
                } else {
                    $this->salvar($post);
                }

                $objSession->__set(
                    'Mensagem', array(
                    'status' => 'alert alert-block alert-success fade in',
                    'mensagem' => 'Conta cadastrada com sucesso!'
                    )
                );
                $this->_redirect('conta');
            } else {
                $objSession->__set(
                    'Mensagem', array(
                    'status' => 'alert alert-block alert-error fade in',
                    'mensagem' => 'Não foi possível cadastar a conta!'
                    )
                );
                $form->populate($post);
            }
        }
    }

    /**
     * Método responsável para salvar a inclusão ou edição das contas
     *
     * @param type $post
     * @return void
     */
    public function salvar($post)
    {
        try {
            $post['chv_usuario'] = $this->_objDadosUsuario->chv_usuario;
            $nConta = new NDefault_ContaNegocio();
            $nConta->gravar($post, $_FILES);
        } catch ( Exception $exc ) {
            throw new Zend_Exception($exc);
        }
    }

    /**
     * Alteração da conta
     *
     * @return void
     */
    public function editarAction()
    {
        try {
            $chvConta = $this->getParam('id');
            $nConta = new NDefault_ContaNegocio();
            $coConta = $nConta->detalhesConta($chvConta);
            if ( $coConta[0]['chv_usuario'] != $this->_objDadosUsuario->chv_usuario ) {
                $objSession = new Zend_Session_Namespace('Data');
                $objSession->__set(
                    'Mensagem', array(
                    'status' =>
                    'alert alert-block alert-error fade in',
                    'mensagem' => 'Você não tem acesso a esta conta!'
                    )
                );
                $this->_redirect('conta');
            }

            $form = new Form_Conta();
            $form->addDisplayGroup(
                array(
                'chv_tp_conta',
                'chv_entidade',
                'nom_conta',
                'nr_parcela'
                ), 'informacao'
            );
            $form->addDisplayGroup(
                array(
                'dat_vencimento',
                'vlr_conta',
                'fatura'
                ), 'vencimento'
            );

            if ( $coConta[0]['nr_parcela'] == 0 ) {
                $form->removeElement('nr_parcela');
            } else {
                $form->getElement('nom_conta')
                    ->setAttrib('readonly', true);

                $form->getElement('nr_parcela')
                    ->setAttrib('readonly', true);
            }

            $this->view->form = $form->populate($coConta[0]);
            $this->view->chvConta = $chvConta;
            if ( $this->getRequest()->isPost() ) {
                $objSession = new Zend_Session_Namespace('Data');
                try {
                    $post = $this->getAllParams();
                    if ( $form->isValid($post) ) {
                        $post = $form->getValidValues($post);
                        $post['chv_conta'] = $this->_getParam('chv_conta');
                        $post['chv_usuario'] = $this->_objDadosUsuario->chv_usuario;
                        //salvando as edições
                        $this->salvar($post);
                        $objSession->__set(
                            'Mensagem', array(
                            'status' =>
                            'alert alert-block alert-success fade in',
                            'mensagem' => 'Conta alterada com sucesso!'
                            )
                        );
                        $this->_redirect('conta/editar/id/' . $chvConta);
                    }
                    $post['chv_conta'] = $this->_getParam('chv_conta');
                    $this->view->form = $form->populate($post);
                } catch ( Zend_Exception $exc ) {
                    $nErro = new NDefault_ErroNegocio();
                    $chvErro = $nErro->registraErro($exc);
                    $objSession->__set(
                        'Mensagem', array(
                        'status' =>
                        'alert alert-block alert-error fade in',
                        'mensagem' => 'Não foi possível salvar as alterações!'
                        )
                    );
                }
            }

            //buscando os arquivos
            $coAnexos = $nConta->listagemArquivosConta($chvConta);

            $this->view->coAnexos = $coAnexos;
        } catch ( Zend_Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
        }
    }

    /**
     * Inserção do tipo de conta
     * @return string
     */
    public function tipocontaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $nomTipo = strip_tags(trim($this->_getParam('nom_tipo_conta')));
        $chvTipoConta = strip_tags(trim($this->_getParam('chv_tp_conta')));
        $chvTipoContaPai = strip_tags(
            trim(
                $this->_getParam('chv_tp_conta_pai')
            )
        );
        try {
            $nTipoConta = new NDefault_TipoContaNegocio();
            $chvTpConta = $nTipoConta->gravar(
                array(
                    'nom_tipo' => $nomTipo,
                    'chv_tp_conta' => $chvTipoConta,
                    'chv_tp_conta_pai' => $chvTipoContaPai
                )
            );
            $coTpConta = $nTipoConta->listagem($chvTpConta);
            $msg = '';

            if ( empty($chvTipoConta) ) {
                $msg = 'Tipo de conta cadastrada com sucesso';
            } else {
                $msg = 'Tipo de conta alterada com sucesso';
            }

            echo json_encode(
                array(
                    'status' => true,
                    'mensagem' => $msg,
                    'data' => $coTpConta[0]
                )
            );
        } catch ( Zend_Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            echo json_encode(
                array(
                    'status' => false,
                    'mensagem' => 'Não foi possível cadastrar o tipo da conta'
                )
            );
        }
    }

    /**
     * Excluir arquivo da conta
     *
     * @return void
     */
    public function excluirfileAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $chvFile = $this->_getParam('chv_file');
        $chvConta = $this->_getParam('chv_conta');

        $nConta = new NDefault_ContaNegocio();
        $objSession = new Zend_Session_Namespace('Data');

        try {
            $nConta->excluirArquivo($chvFile, $chvConta);
            $objSession->__set(
                'Mensagem', array(
                'status' =>
                'alert alert-block alert-success fade in',
                'mensagem' => 'Arquivo excluído com sucesso'
                )
            );
        } catch ( Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            $objSession->__set(
                'Mensagem', array(
                'status' =>
                'alert alert-block alert-error fade in',
                'mensagem' => $exc->getMessage()
                )
            );
        }
        $this->_redirect('conta/editar/id/' . $chvConta);
    }

    /**
     * Excluir conta
     *
     * @throws Zend_Exception
     * @return void
     */
    public function excluirAction()
    {
        $objSession = new Zend_Session_Namespace('Data');
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $chvConta = $this->_getParam('chv_contas');
        $aContas = preg_split(
            '/&/', preg_replace(
                '/chv_conta=/', '', $chvConta
            )
        );
        $nConta = new NDefault_ContaNegocio();
        try {
            if ( count($aContas) == 0 ) {
                throw new Zend_Exception('Não foi informado a chave da conta');
            }

            foreach ( $aContas as $conta ) {
                $nConta->excluir($conta, $this->_objDadosUsuario->chv_usuario);
            }
            $objSession->__set(
                'Mensagem', array(
                'status' => 'alert alert-block alert-success fade in',
                'mensagem' => 'Conta excluído com sucesso'
                )
            );
        } catch ( Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            $objSession->__set(
                'Mensagem', array(
                'status' => 'alert alert-block alert-error fade in',
                'mensagem' => $exc->getMessage()
                )
            );
        }
        $this->_redirect('conta');
    }

    /**
     * Método que cálcula a periodicidade da conta
     *
     * @param type $dataVencimento
     * @param type $tipo
     * @return string
     */
    protected function retornaPeriodicidade($dataVencimento, $tipo)
    {
        $aux = explode('/', $dataVencimento);
        $d = $aux[0];
        $m = $aux[1];
        $y = $aux[2];
        switch ( $tipo ) {
            //Semanal
            case '1':
                $data = date('d/m/Y', mktime(0, 0, 0, $m, $d + 7, $y));
                break;
            //mensal
            case '2':
                $data = date('d/m/Y', mktime(0, 0, 0, $m + 1, $d, $y));
                break;
            //bimestral
            case '3':
                $data = date('d/m/Y', mktime(0, 0, 0, $m + 2, $d, $y));
                break;
            //trimestral
            case '4':
                $data = date('d/m/Y', mktime(0, 0, 0, $m + 3, $d, $y));
                break;
            //Semestral
            case '5':
                $data = date('d/m/Y', mktime(0, 0, 0, $m + 6, $d, $y));
                break;
            //Anual
            case '6':
                $data = date('d/m/Y', mktime(0, 0, 0, $m, date('d'), $y + 1));
                break;
        }
        return $data;
    }

    /**
     * Método que lista os tipos de conta
     *
     * @return void
     */
    public function listagemtipocontaAction()
    {
        $nTpConta = new NDefault_TipoContaNegocio();
        $coTipoConta = $nTpConta->listagemVinculacaoTipoConta();

        $nConta = new NDefault_ContaNegocio();
        $coContas = $nConta->listagemConta();

        if ( count($coContas) != 0 ) {
            foreach ( $coContas as $k => $contas ) {
                if ( $contas['usuario'] !=
                    $this->_objDadosUsuario->chv_usuario ) {
                    unset($coContas[$k]);
                }
            }

            foreach ( $coTipoConta as $k => $tipoConta ) {
                foreach ( $coContas as $contas ) {
                    if ( $tipoConta['chv_tp_conta'] ==
                        $contas['chv_tp_conta'] ) {
                        $coTipoConta[$k]['contas'][] = $contas;
                    }
                }
            }
        }
        $this->view->conTipoContas = $coTipoConta;
    }

    /**
     * Método que excluir o tipo de conta
     *
     * @throws Zend_Exception
     */
    public function excluirtipocontaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $chvConta = $this->_getParam('chv_contas');
        $aContasTp = preg_split(
            '/&/', preg_replace(
                '/chv_conta=/', '', $chvConta
            )
        );

        $nTipoConta = new NDefault_TipoContaNegocio();

        try {
            $objSession = new Zend_Session_Namespace('Data');
            if ( count($aContasTp) == 0 ) {
                throw new Zend_Exception(
                    'Não foi informado a chave do tipo da conta'
                );
            }

            foreach ( $aContasTp as $tpConta ) {
                $nTipoConta->excluir($tpConta);
            }

            $objSession->__set('Mensagem', array(
                'status' => 'alert alert-block alert-success fade in',
                'mensagem' => 'Tipo de conta, excluído com sucesso'
            ));
        } catch ( Zend_Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);

            $objSession->__set('Mensagem', array(
                'status' => 'alert alert-block alert-error fade in',
                'mensagem' => $exc->getMessage()
            ));
        }
        $this->_redirect('conta/listagemtipoconta');
    }

    /**
     * Método que editar o tipo de conta
     *
     * @throws Zend_Exception
     */
    public function editartipocontaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $nTipoConta = new NDefault_TipoContaNegocio();
        $chvTpConta = $this->_getParam('chv_tp_conta');

        try {
            if ( empty($chvTpConta) ) {
                throw new Zend_Exception(
                    'Não foi informado a chave do tipo da conta'
                );
            }
            $coDetalhes = $nTipoConta->listagem($chvTpConta);
            echo json_encode(array('status' => true, 'data' => $coDetalhes[0]));
        } catch ( Exception $exc ) {

            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            echo json_encode(
                array(
                    'status' => false,
                    'mensagem' => $exc->getMessage()
                )
            );
        }
    }

    /**
     * Método responsávelo pelo relatório
     *
     * @return void
     */
    public function relatorioAction()
    {
        $form = new Form_relatorioConta();
        $this->view->resultados = array();
        $this->view->msg = '';
        $this->view->totalConta = 0;
        $this->view->totalPago = 0;

        if ( $this->getRequest()->isPost() ) {
            $post = $this->_getAllParams();
            if ( $form->isValid($post) ) {
                $nRelatorio = new NDefault_ContaNegocio();
                $coContas = $nRelatorio->listagemConta($post);

                if ( count($coContas) != 0 ) {
                    $coSoma = $nRelatorio->somaConta($post);
                    $coSoma = $coSoma[0];
                    $this->view->totalConta = number_format(
                        str_replace(
                            '$', '', $coSoma['ValorConta']
                        ), 2, '.', ' '
                    );
                    if ( is_double($coSoma['ValorPagam']) ) {
                        $this->view->totalPago = number_format(
                            str_replace(
                                '$', '', $coSoma['ValorPagam']
                            ), 2, '.', ' '
                        );
                    }

                    foreach ( $coContas as $k => $conta ) {
                        $coContas[$k]['nr_parcela'] = $this->quantidadeParcelas(
                            $conta['nr_parcela'], $conta['nomeConta']
                        );
                    }

                    $this->view->resultados = $coContas;
                } else {
                    $this->view->msg =
                        'Conta não existente de acordo com os dados informados.';
                }
            } else {
                $this->view->msg = 'Parâmetros inválido';
                $form->populate($post);
            }
        }
        $this->view->form = $form;
    }

    public function quantidadeParcelas($nrParcela, $nmConta)
    {
        $nConta = new NDefault_ContaNegocio();
        $coContas = $nConta->listagemConta(
            array(
                'nom_conta' => $nmConta
            )
        );
        $qtd = count($coContas);
        return (($nrParcela == 0) ? '1' : $nrParcela) . '/' . $qtd;
    }

}