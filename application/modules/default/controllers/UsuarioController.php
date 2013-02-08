<?php

class Default_UsuarioController extends Default_SegurancaController
{

    /**
     * Método principal.
     *
     * @return void
     */
    public function indexAction()
    {
        try {
            $nUsuario = new NDefault_UsuarioNegocio();
            $coUsuario = $nUsuario->listagemUsuario();
            $this->view->coUsuario = $coUsuario;

            $objSession = new Zend_Session_Namespace('Data');
            if ( $objSession->__isset('Mensagem') ) {
                $this->view->retorno = $objSession->__get('Mensagem');
                $objSession->__unset('Mensagem');
            }
        } catch ( Zend_Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            $this->_redirect(END_WEB . '/error/index/id/' . $chvErro);
        }
    }

    /**
     * Método responsável para alterar a situação do usuário
     *
     * @return void
     */
    public function situacaoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        try {
            $chvUsuario = $this->getParam('chv_usuario');
            $dsStatus = $this->getParam('status');

            $nUsuario = new NDefault_UsuarioNegocio();
            $nUsuario->alteraSituacao($chvUsuario, $dsStatus);

            echo json_encode(
                array(
                    'status' => true,
                    'mensagem' => 'Situação do usuário alterado com sucesso'
                )
            );
        } catch ( Zend_Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            echo json_encode(
                array(
                    'status' => false,
                    'mensagem' => 'Não foi possível alterar a situação.'
                )
            );
        }
    }

    /**
     * Método responsável para excluir o usuário
     *
     * @return void
     */
    public function excluirAction()
    {
        $objSession = new Zend_Session_Namespace('Data');
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        try {
            $post = $this->_getParam('chv_usuario');
            $chvUsuario = preg_split('/&/', preg_replace('/chv_usuario=/', '', $post));
            $nUsuario = new NDefault_UsuarioNegocio();
            foreach ( $chvUsuario as $usuario ) {

                if ( !empty($usuario) && $usuario != $this->_objDadosUsuario->chv_usuario ) {
                    $nUsuario->excluirUsuario($usuario);
                }
            }

            $objSession->__set('Mensagem', array(
                'status' => 'alert alert-block alert-success fade in',
                'mensagem' => 'Usuário excluído com sucesso'
            ));
        } catch ( Zend_Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            $objSession->__set('Mensagem', array(
                'status' => 'alert alert-block alert-error fade in',
                'mensagem' => 'Não foi possível excluir o usuário'
            ));
        }
        $this->_redirect('usuario');
    }

    /**
     * @return void
     */
    public function reenviarsenhaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        try {
            $nUsuario = new NDefault_UsuarioNegocio();
            $chvUsuario = $this->getParam('chv_usuario');
            $novaSenha = str_shuffle(str_shuffle('NoVaSENHA') . rand(1, 9999));
            $coUsuario = $nUsuario->listagemUsuario($chvUsuario);
            $mensagem = $this->view->partial('usuario/recuperacao-senha.phtml', array(
                'nomeUsuario' => $coUsuario[0]['nom_usuario'],
                'loginUsuario' => $coUsuario[0]['ds_login'],
                'senhaUsuario' => $novaSenha,
                'endAPP' => END_WEB
                )
            );

            NDefault_MensagemNegocio::enviarEmail($mensagem, 'Reenvio da nova senha de acesso', array($coUsuario[0]['ds_email']));
            $nUsuario->reenviarSenha($chvUsuario, $novaSenha);
            echo json_encode(array('status' => true, 'mensagem' => 'Foi encaminhado uma nova senha de acesso para o usuário.'));
        } catch ( Zend_Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            echo json_encode(array('status' => false, 'mensagem' => 'Não foi possível reenviar a senha para o usuário.'));
        }
    }

    /**
     *
     */
    public function novoAction()
    {
        try {
            $formUsuario = new Form_Usuario();
            $this->view->formUser = $formUsuario->novo();
            if ( $this->getRequest()->isPost() ) {
                $post = $this->_getAllParams();

                if ( $formUsuario->isValidNovo($post) ) {
                    $post = $formUsuario->novo()->getValidValues($post);
                    $post['cpf_usuario'] = str_replace('.', '', str_replace('-', '.', $post['cpf_usuario']));

                    $nUsuario = new NDefault_UsuarioNegocio();
                    $novaSenha = str_shuffle(str_shuffle('NoVaSENHA') . rand(1, 9999));

                    if ( $post['acesso_sis'] ) {
                        $post['ds_senha'] = md5($novaSenha);
                        $aEmail = explode('@', $post['ds_email']);
                        $post['ds_login'] = $aEmail[0];
                    }

                    if ( $nUsuario->salvarUsuario($post) ) {
                        if ( $post['acesso_sis'] ) {
                            $mensagem = $this->view->partial('usuario/email-cadastro.phtml', array(
                                'nomeUsuario' => $post['nom_usuario'],
                                'loginUsuario' => $post['ds_login'],
                                'senhaUsuario' => $novaSenha,
                                'endAPP' => END_WEB
                                )
                            );
                            $post['ds_senha'] = md5($post['ds_senha']);
                            NDefault_MensagemNegocio::enviarEmail($mensagem, 'Cadastro de sistema', array($post['ds_email']));
                        }
                        $objSession = new Zend_Session_Namespace('Data');
                        $objSession->__set('Mensagem', array(
                            'status' => 'alert alert-block alert-success fade in',
                            'mensagem' => 'Usuário cadastrado com sucesso.'
                        ));
                        $this->_redirect('usuario');
                    } else {
                        $this->view->error = "Não foi possível cadastrar";
                    }
                } else {
                    $this->view->formUser = $formUsuario->novo()->populate($post);
                }
            }
        } catch ( Zend_Exception $exc ) {
            $this->view->error = array('status' => false, 'mensagem' => $exc->getMessage());
            $this->view->formUser = $formUsuario->novo()->populate($post);
        } catch ( Exception $exec ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exec);
            $this->_redirect(END_WEB . '/error/index/id/' . $chvErro);
        }
    }

    /**
     *
     */
    public function alterarAction()
    {
        $chvUsuario = $this->getParam('id');
        $objSession = new Zend_Session_Namespace('Data');

        if ( !isset($chvUsuario) ) {
            throw new Exception('Não foi informado a chave do usuário');
        }
        $nUsuario = new NDefault_UsuarioNegocio();
        $coUsuario = $nUsuario->listagemUsuario($chvUsuario);
        $post = $coUsuario[0];
        $form = new Form_Usuario();

        if ( $this->getRequest()->isPost() ) {
            $post = $this->_getAllParams();

            if ( $form->isValidAlterar($post) ) {
                $post = $form->alterar($post)->getValidValues($post);

                if ( $post['acesso_sis'] == '1' && is_null($coUsuario[0]['ds_senha']) && is_null($coUsuario[0]['ds_login'])) {
                    $post['ds_senha'] = str_shuffle(str_shuffle('NoVaSENHA') . rand(1, 9999));
                    $mensagem = $this->view->partial('usuario/email-cadastro.phtml', array(
                        'nomeUsuario' => $post['nom_usuario'],
                        'loginUsuario' => $post['ds_login'],
                        'senhaUsuario' => $post['ds_senha'],
                        'endAPP' => END_WEB
                        )
                    );
                    $post['ds_senha'] = md5($post['ds_senha']);
                    NDefault_MensagemNegocio::enviarEmail($mensagem, 'Cadastro no sistema', array($post['ds_email']));
                }
                
                unset($post['acesso_sis']);
                $nUsuario->salvarUsuario($post);
                $objSession->__set('Mensagem', array(
                    'status' => 'alert alert-block alert-success fade in',
                    'mensagem' => 'Usuário alterado com sucesso.'
                ));
                $this->_redirect('usuario');
            } else {
                $post['cpf_usuario'] = $coUsuario[0]['cpf_usuario'];
            }
        }

        $this->view->formUser = $form->alterar($post);
        $this->view->desabilitaBotao = false;
        if ( $chvUsuario == $this->_objDadosUsuario->chv_usuario ) {
            $this->view->desabilitaBotao = true;
        }
    }

}