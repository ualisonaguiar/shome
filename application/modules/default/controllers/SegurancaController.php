<?php

class Default_SegurancaController extends Zend_Controller_Action
{

    /**
     * Dados em objeto do usuário logado.
     *
     * @var object
     */
    protected $_objDadosUsuario;

    /**
     *
     */
    final public function init()
    {
        parent::init();

        switch ( $this->_request->getActionName() ) {
            case "login":
            case "logout":
            case "nao-autorizado":
            case "recuperarsenha":
            case "cronjob":
                continue;
                break;
            default:
                if ( !Zend_Auth::getInstance()->hasIdentity() ) {
                    $this->_redirect('/seguranca/login');
                } else {
                    $this->_objDadosUsuario = Zend_Auth::getInstance()->getIdentity();
                    $this->view->objDadosUsuario = $this->_objDadosUsuario;
                    $form = new Form_Seguranca();
                    $this->view->formAlterarSenha = $form->alteraSenhaUsuario();
                }
                break;
        }

        $this->mensagem();
    }

    /**
     * Método principal.
     *
     * @return void
     */
    public function loginAction()
    {
        try {
            $form = new Form_Seguranca();
            if ( $this->getRequest()->isPost() ) {
                $post = $this->_getAllParams();
                if ( $form->login()->isValid($post) ) {
                    $post = $form->login()->getValidValues($post);
                    $nSeguracan = new NDefault_SegurancaNegocio();
                    $result = $nSeguracan->loginUsuario($post['usuario'], $post['senha']);
                    $msg = '';
                    switch ( $result ) {
                        case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                            $msg = "Falha na identificação";
                            break;
                        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                            $msg = 'Dados incorretos';
                            break;
                        case Zend_Auth_Result::SUCCESS:
                            /** autenticado com sucesso * */
                            $this->_redirect('index');
                            break;
                        default:
                            $msg = 'Falha no login';
                            break;
                    }

                    if ( !empty($msg) ) {
                        $objSession = new Zend_Session_Namespace('Data');
                        $objSession->__set('Mensagem', array(
                            'status' => 'alert alert-block alert-error fade in',
                            'mensagem' => $msg
                        ));
                        $this->_redirect('seguranca/login');
                    }
                } else {
                    $form->login()->populate($post);
                }
            }
            $this->view->form = $form->login();
            $this->view->formRecuperar = $form->recuperarSenha();
        } catch ( Zend_Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $chvErro = $nErro->registraErro($exc);
            $this->_redirect(END_WEB . '/error/index/id/' . $chvErro);
        }
    }

    public function recuperarsenhaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $formSeguranca = new Form_Seguranca();
        $post = $this->_getAllParams();
        if ( $formSeguranca->isValidRecuperarSenha($post) ) {
            $post = $formSeguranca->recuperarSenha()->getValidValues($post);

            $post['cpf_usuario'] = str_replace('.', '', str_replace('-', '', $post['cpf_usuario']));

            $nUsuario = new NDefault_UsuarioNegocio();
            $coUsuario = $nUsuario->listagemUsuario();
            $aDadosUsuario = array();

            if ( count($coUsuario) != 0 ) {
                foreach ( $coUsuario as $usuario ) {
                    if ( $usuario['cpf_usuario'] == $post['cpf_usuario'] ) {
                        $aDadosUsuario = $usuario;
                    }
                }

                if ( count($aDadosUsuario) == 0 ) {
                    echo json_encode(array('status' => false, 'mensagem' => 'Usuário não encontrado no sistema'));
                } elseif ( $aDadosUsuario['ds_status'] == false ) {
                    echo json_encode(array('status' => false, 'mensagem' => 'Usuário inativo no sistema'));
                } else {
                    $aSenha = NDefault_SegurancaNegocio::geradorSenha();

                    $aDadosUsuario['ds_senha'] = $aSenha['cript'];
                    $aDadosUsuario['ds_status'] = 1;
                    $mensagem = $this->view->partial('usuario/recuperacao-senha.phtml', array(
                        'nomeUsuario' => $aDadosUsuario['nom_usuario'],
                        'loginUsuario' => $aDadosUsuario['ds_login'],
                        'senhaUsuario' => $aSenha['senha'],
                        'endAPP' => END_WEB
                        )
                    );

                    NDefault_MensagemNegocio::enviarEmail($mensagem, 'Recuperação de senha', array($aDadosUsuario['ds_email']));
                    $nUsuario->salvarUsuario($aDadosUsuario);
                    echo json_encode(array('status' => true, 'mensagem' => 'Foi encaminhado uma nova senha para o e-mail do usuário cadastrado.'));
                }
            } else {
                echo json_encode(array('status' => false, 'mensagem' => 'Não existe usuário cadastrado no sistama'));
            }
        } else {
            echo json_encode(array('status' => false, 'mensagem' => 'CPF inválido.'));
        }
    }

    /**
     *
     */
    final public function logoutAction()
    {
        /** desabilitando renderização do layout e view * */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        /** removendo dados de acesso * */
        Zend_Auth::getInstance()->clearIdentity();
        /** redirecionando para login * */
        $this->_redirect('seguranca/login');
    }

    public function alteracaosenhaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $formSeguranca = new Form_Seguranca();
        $post = $this->_getAllParams();

        if ( $formSeguranca->alteraSenhaUsuario()->isValid($post) ) {
            $post = $formSeguranca->alteraSenhaUsuario()->getValidValues($post);
            if ( $post['nova_senha'] != $post['conf_senha'] ) {
                echo json_encode(array('status' => false, 'mensagem' => 'A nova senha esta diferente da confirmação'));
            } else {
                $chvUsuario = $this->_objDadosUsuario->chv_usuario;
                $nUsuario = new NDefault_UsuarioNegocio();
                $coUsuario = $nUsuario->listagemUsuario($chvUsuario);

                if ( $coUsuario[0]['ds_senha'] != md5($post['ds_senha']) ) {
                    echo json_encode(array('status' => false, 'mensagem' => 'Senha do usuário incorreto'));
                } else {
                    $coUsuario[0]['ds_senha'] = md5($post['conf_senha']);
                    $nUsuario->salvarUsuario($coUsuario[0]);
                    echo json_encode(array('status' => true, 'mensagem' => 'Alteração de senha realizada com sucesso'));
                }
            }
        } else {
            echo json_encode(array('status' => false, 'mensagem' => 'Os dados informados estão incorretos'));
        }
    }

    protected function mensagem()
    {
        $objSession = new Zend_Session_Namespace('Data');
        if ( $objSession->__isset('Mensagem') ) {
            $this->view->retorno = $objSession->__get('Mensagem');
            $objSession->__unset('Mensagem');
        }
    }

}