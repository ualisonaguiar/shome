<?php

class Default_GerenciamentoController extends Default_SegurancaController
{

    /**
     *
     */
    public function indexAction()
    {
        $pGerencimaneo = new PDefault_Models_Gerenciamento();
        $tamanhoDB = $pGerencimaneo->tamanhoDatabase();
        $this->view->espacoBD = self::converteByte($tamanhoDB->pg_database_size);
        $coFiles = $pGerencimaneo->tamanhoFile();

        $aEspacoFile['total'] = 0;
        if ( count($coFiles) != 0 ) {
            foreach ( $coFiles as $files ) {
                $aEspacoFile['total'] += $files['sum_file'];
                $aEspacoFile['files'][] = array(
                    'total' => $files['sum_file'],
                    'total_' => self::converteByte($files['sum_file']),
                    'type' => $files['extensao_file']
                );
            }
        }
        $aEspacoFile['total_'] = self::converteByte($aEspacoFile['total']);
        $this->view->espacoFile = $aEspacoFile;

        //recuperando a listagem dos arquivos
        $nArquivos = new NDefault_AnexoNegocio();
        $coArquivos = $nArquivos->listagem();
        $this->view->coArquivos = $coArquivos;
    }

    /**
     *
     * @param type $valor
     */
    static function converteByte($size)
    {
        $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
    }

    /**
     *
     */
    public function indexerroAction()
    {
        $nErros = new NDefault_ErroNegocio();
        $coErrosAberto = $nErros->listagemErro(null, true);
        $coErrosFechado = $nErros->listagemErro(null, false);

        $this->view->coErrosAbertos = $coErrosAberto;
        $this->view->coErrosFechados = $coErrosFechado;
    }

    public function resolveerroAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $objSession = new Zend_Session_Namespace('Data');
        $nErros = new NDefault_ErroNegocio();

        $post = $this->_getAllParams();
        $chvErros = preg_split('/&/', preg_replace('/chv_erro_modal=/', '', $post['chv_erro']));
        try {
            if ( count($chvErros) == 0 ) {
                throw new Zend_Exception('Não foi informado a chave do erro');
            }

            if ( $post['ds_solucao'] == null ) {
                throw new Zend_Exception('Não foi informado nenhuma solução para o problema.');
            }

            foreach ( $chvErros as $chvErro ) {
                $nErros->resolverErro($chvErro, $post['ds_solucao']);
            }
            $objSession->__set('Mensagem', array(
                'status' => 'alert alert-block alert-success fade in',
                'mensagem' => 'Erro resolvido com sucesso!'
            ));
        } catch ( Zend_Db_Exception $erro ) {
            $objSession->__set('Mensagem', array(
                'status' => 'alert alert-block alert-error fade in',
                'mensagem' => $erro->getMessage()
            ));
        }
        $this->redirect('gerenciamento/indexerro');
    }

    public function detalheserroAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        try {
            $chvErro = $this->_getParam('chv_erro');
            $dsStaus = $this->_getParam('status');
            $dsStaus = ($dsStaus == 'true') ? true : false;

            if ( empty($chvErro) ) {
                throw new Zend_Exception('Não foi informado a chave do erro');
            }
            $nErro = new NDefault_ErroNegocio();
            $coErro = $nErro->listagemErro($chvErro, $dsStaus);

            if ( count($coErro) == 0 ) {
                throw new Zend_Exception('Erro não localizado');
            }

            echo json_encode(
                array(
                    'status' => true,
                    'erro' => $coErro[0]['erro'],
                    'tracert' => $coErro[0]['tracert']
                )
            );
        } catch ( Zend_Exception $exec ) {
            echo json_encode(
                array(
                    'status' => false,
                    'mensagem' => $exec->getMessage()
                )
            );
        }
    }

    public function emailAction()
    {
        $nMensagem = new NDefault_MensagemNegocio();
        $coEmail = $nMensagem->getListagemConfiguracaoEmail();
        $this->view->coEmails = $coEmail;
    }

    public function salvaremailAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $objSession = new Zend_Session_Namespace('Data');
        $post = $this->_getAllParams();

        try {
            if ( !$this->getRequest()->isPost() ) {
                throw new Zend_Exception('Não foi informados os dados da configuração');
            }
            $nMensagem = new NDefault_MensagemNegocio();
            $nMensagem->salvar($post);

            $objSession->__set(
                'Mensagem', array(
                'status' =>
                'alert alert-block alert-success fade in',
                'mensagem' => 'Configuração salva com sucesso!'
                )
            );
        } catch ( Zend_Exception $exc ) {
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
        $this->_redirect('gerenciamento/email');
    }

    public function informacaoemailAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $chvEmail = $this->getParam('chv_email');
        try {
            if ( empty($chvEmail) ) {
                throw new Zend_Exception('Não foi informado a chave do e-mail');
            }
            $nEmail = new NDefault_MensagemNegocio();
            $coEmails = $nEmail->getListagemConfiguracaoEmail($chvEmail);

            if ( count($coEmails) == 0 ) {
                throw new Zend_Exception(
                    'Não existe configuração de e-mail informado'
                );
            }
            echo json_encode(
                array(
                    'status' => true,
                    'dados' => $coEmails[0]
                )
            );
        } catch ( Zend_Exception $exec ) {
            echo json_encode(
                array(
                    'status' => false,
                    'mensagem' => $exec->getMessage()
                )
            );
        }
    }

    public function statusemailAction()
    {
        $chvEmail = $this->getParam('chv_email');
        $status = $this->getParam('status');

        try {
            $objSession = new Zend_Session_Namespace('Data');
            if ( empty($chvEmail) ) {
                throw new Zend_Exception(
                    'Não foi informado nenhuma chave de e-mail'
                );
            }
            $nMensage = new NDefault_MensagemNegocio();
            $nMensage->alteraStatus($chvEmail, $status);

            $objSession->__set(
                'Mensagem', array(
                'status' =>
                'alert alert-block alert-success fade in',
                'mensagem' => 'Alteração de situação feita com sucesso!'
                )
            );
        } catch ( Zend_Exception $exc ) {
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
        $this->_redirect('gerenciamento/email');
    }

}