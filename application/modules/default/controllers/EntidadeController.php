<?php

class Default_EntidadeController extends Default_SegurancaController
{

    /**
     * Método principal.
     *
     * @return void
     */
    public function indexAction()
    {
        $nEntidade = new NDefault_EntidadeNegocio();
        $coEntidade = $nEntidade->listagem();

        $this->view->coEntidades = $coEntidade;
    }

    public function detalhesAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        try {
            $chvPessoaJuridica = $this->getParam('chv_pessoa_juridica');

            $nEntidade = new NDefault_EntidadeNegocio();
            //buscando informações da entidade
            $coEntidade = $nEntidade->listagem(true, $chvPessoaJuridica);
            //buscando endereço
            $coEntidade[0]['endereco'] = $nEntidade->detalhesEntidadeEndereco($chvPessoaJuridica);

            echo json_encode(array('status' => true, 'data' => $coEntidade[0]));
        } catch ( Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            echo json_encode(array('status' => false, 'mensagem' => 'Não foi possível carregar informações sobre a entidade'));
        }
    }

    public function situacaoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        try {
            $chvPessoaJuridica = $this->getParam('chv_pessoa_juridica');
            $dsStatus = $this->getParam('status');

            $nUsuario = new NDefault_EntidadeNegocio();
            $nUsuario->alteraSituacao($chvPessoaJuridica, $dsStatus);

            echo json_encode(array('status' => true, 'mensagem' => 'Situação da entidade alterado com sucesso'));
        } catch ( Zend_Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            echo json_encode(array('status' => false, 'mensagem' => 'Não foi possível alterar a situação.'));
        }
    }

    public function novoAction()
    {
        try {
            $form = new Form_Entidade();
            $this->view->form = $form->novo();

            if ( $this->getRequest()->isPost() ) {
                $post = $this->_getAllParams();
                $aChvEndereco = '';
                if ( array_key_exists('chv_endereco', $post) ) {
                    $aChvEndereco = $post['chv_endereco'];
                    $aTelefone = $post['telefone'];
                    $aOutro = $post['outro'];
                }

                unset($post['chv_endereco']);
                if ( $form->novo()->isValid($post) ) {
                    $post = $form->novo()->getValidValues($post);

                    $nEntidade = new NDefault_EntidadeNegocio();
                    $chvPessoaJuridica = $nEntidade->salvaEntidade($post);

                    if ( count($aChvEndereco) != 0 ) {
                        $nEndereco = new NDefault_EnderecoNegocio();
                        $i = 0;
                        foreach ( $aChvEndereco as $chvEndereco ) {
                            $nEndereco->vinculaEnderecoPessoaJuridica(
                                $chvPessoaJuridica, $chvEndereco, $aTelefone[$i], $aOutro[$i]
                            );
                            $i++;
                        }
                    }
                } else {
                    $this->view->form = $form->novo()->populate($post);
                }
                $objSession = new Zend_Session_Namespace('Data');
                $objSession->__set('Mensagem', array(
                    'status' => 'alert alert-block alert-success fade in',
                    'mensagem' => 'Cadastro feito com sucesso!'
                ));
                $this->_redirect('entidade');
            }
        } catch ( Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            $objSession = new Zend_Session_Namespace('Data');
            $objSession->__set('Mensagem', array(
                'status' => 'alert alert-block alert-error fade in',
                'mensagem' => $exc->getMessage()
            ));
            $this->_redirect('entidade');
        }
    }

    public function alterarAction()
    {
        $id = $this->getParam('id');
        $form = new Form_Entidade();
        $nEntidade = new NDefault_EntidadeNegocio();

        $coEntidade = $nEntidade->listagem(true, $id);
        $coEntidadeEndereco = $nEntidade->detalhesEntidadeEndereco($id);
        $achvEndereco = array();

        if ( count($coEntidadeEndereco) != 0 ) {
            foreach ( $coEntidadeEndereco as $endereco ) {
                $achvEndereco[] = $endereco['chv_endereco'];
            }
        }

        $this->view->form = $form->novo()->populate($coEntidade[0]);
        $this->view->chvEnderecoEntidade = implode(',', $achvEndereco);

        if ( $this->getRequest()->isPost() ) {
            $post = $this->_getAllParams();
            $aChvEndereco = $post['chv_endereco'];
            $aTelefone = $post['telefone'];
            $aOutro = $post['outro'];

            try {
                if ( $form->novo()->valid($post) ) {
                    $post = $form->novo()->getValidValues($post);
                    $chvEntidade = $nEntidade->salvaEntidade($post);

                    if ( count($aChvEndereco) != 0 ) {
                        $nEndereco = new NDefault_EnderecoNegocio();
                        $i = 0;
                        foreach ( $aChvEndereco as $chvEndereco ) {
                            $nEndereco->vinculaEnderecoPessoaJuridica(
                                $chvEntidade, $chvEndereco, $aTelefone[$i], $aOutro[$i]
                            );
                            $i++;
                        }
                    }
                    $objSession = new Zend_Session_Namespace('Data');
                    $objSession->__set('Mensagem', array(
                        'status' => 'alert alert-block alert-success fade in',
                        'mensagem' => 'Alteração feito com sucesso!'
                    ));
                    $this->_redirect('entidade');
                } else {
                    $this->view->form = $form->novo()->populate($post);
                }
            } catch ( Exception $exc ) {
                $nErro = new NDefault_ErroNegocio();
                $chvErro = $nErro->registraErro($exc);
                $objSession = new Zend_Session_Namespace('Data');
                $objSession->__set('Mensagem', array(
                    'status' => 'alert alert-block alert-error fade in',
                    'mensagem' => $exc->getMessage()
                ));
                $this->_redirect('entidade');
            }
        }
    }

    public function excluirAction()
    {
        $objSession = new Zend_Session_Namespace('Data');
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $post = $this->_getAllParams();

        $chvEntidade = str_replace('chv_pessoa_juridica=', '', $post['chv_entidade']);
        $aEntidade = explode('&', $chvEntidade);

        $nEntidade = new NDefault_EntidadeNegocio();
        try {
            foreach ( $aEntidade as $chvEntidade ) {
                $nEntidade->excluirEntidade($chvEntidade);
            }
            $objSession->__set('Mensagem', array(
                'status' => 'alert alert-block alert-success fade in',
                'mensagem' => 'Entidade excluída com sucesso'
            ));
        } catch ( Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            $objSession->__set('Mensagem', array(
                'status' => 'alert alert-block alert-error fade in',
                'mensagem' => 'Não foi possível excluir a entidade'
            ));
        }
        $this->_redirect('entidade');
    }

    /**
     *
     */
    public function tipoentidadeAction()
    {
        $nEntidade = new NDefault_RamoEntidadeNegocio();
        $coEntidade = $nEntidade->listagem();
        unset($coEntidade['']);

        $this->view->coEntidade = $coEntidade;
    }

    /**
     *
     */
    public function novotipoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $nomTipo = $this->_getParam('nom_tipo');
        try {
            $nEntidade = new NDefault_RamoEntidadeNegocio();
            $chvTipoEntidade = $nEntidade->gravar(
                array(
                    'ds_tipo_pessoa' => $nomTipo
                )
            );
            $coEntidade = $nEntidade->listagem($chvTipoEntidade);
            $values = array_keys($coEntidade);

            echo json_encode(
                array(
                    'status' => true,
                    'mensagem' => 'Entidade cadastrada com sucesso.',
                    'data' => array(
                        'chv_tp_pessoa' => $values[0],
                        'ds_tipo_pessoa' => $coEntidade[$values[0]]
                    )
                )
            );
        } catch ( Zend_Db_Exception $exc ) {
            echo json_encode(
                array(
                    'status' => false,
                    'mensagem' => 'Não foi possível cadastrar.'
                )
            );
        }
    }

    public function excluirtipoAction()
    {
        $objSession = new Zend_Session_Namespace('Data');
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $chvTpPessoa = $this->_getParam('chv_tp_pessoa');
        $chvTpPessoa = preg_split('/&/', preg_replace('/chv_tp_pessoa=/', '', $chvTpPessoa));

        if ( count($chvTpPessoa) != 0 ) {
            $nTpEntidade = new NDefault_RamoEntidadeNegocio();
            try {
                foreach ( $chvTpPessoa as $tpPessoa ) {
                    $nTpEntidade->excluir($tpPessoa);
                }
                $objSession->__set('Mensagem', array(
                    'status' => 'alert alert-block alert-success fade in',
                    'mensagem' => 'Tipo de entidade excluída com sucesso'
                ));
            } catch ( Zend_Exception $exc ) {
                $nErro = new NDefault_ErroNegocio();
                $chvErro = $nErro->registraErro($exc);
                $objSession->__set('Mensagem', array(
                    'status' => 'alert alert-block alert-error fade in',
                    'mensagem' => $exc->getMessage()
                ));
            }
        }

        $this->redirect('entidade/tipoentidade');
    }

    public function editartipoAction()
    {
        $objSession = new Zend_Session_Namespace('Data');
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $chvTpEntidade = $this->_getParam('chv_tp_conta');

        $nTipoEntidade = new NDefault_RamoEntidadeNegocio();
        $coTipo = $nTipoEntidade->listagem($chvTpEntidade);

        if ( $this->getRequest()->isPost() ) {
            $post = $this->_getAllParams();
            try {
                $nTipoEntidade->gravar($post);
                $objSession->__set('Mensagem', array(
                    'status' => 'alert alert-block alert-success fade in',
                    'mensagem' => 'Tipo de entidade alterada com sucesso'
                ));
            } catch ( Zend_Exception $exec ) {
                $nErro = new NDefault_ErroNegocio();
                $chvErro = $nErro->registraErro($exc);
                $objSession->__set('Mensagem', array(
                    'status' => 'alert alert-block alert-error fade in',
                    'mensagem' => $exc->getMessage()
                ));
            }
        } else {
            $values = array_keys($coTipo);
            echo json_encode(
                array(
                    'status' => true,
                    'data' => array(
                        'chv_tp_pessoa' => $values[0],
                        'ds_tipo_pessoa' => $coTipo[$values[0]]
                    )
                )
            );
        }
    }

}