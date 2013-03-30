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

class Default_ErrorController extends Zend_Controller_Action
{

    /**
     * Método principal.
     *
     * @return void
     */
    public function indexAction()
    {
        $id = $this->_getParam('id');

        if ( !empty($id) ) {
            $nErro = new NDefault_ErroNegocio();
            $coErro = $nErro->listagemErro($id, true);
            $mensagem = $this->view->partial(
                'error/email.phtml', array(
                'ds_file' => $coErro[0]['erro']['ds_file'],
                'ds_line' => $coErro[0]['erro']['ds_line'],
                'ds_message' => $coErro[0]['erro']['ds_message'],
                'dat_log' => $coErro[0]['erro']['dat_log'],
                'aTracert' => $coErro[0]['tracert']
                )
            );
            $this->view->mensagemErro = $mensagem;
            NDefault_MensagemNegocio::enviarEmail(
                $mensagem, 'Erro no sistema',
                array(
                    EMAIL_ADMINISTRADOR
                )
            );
        }
    }

    /**
     * Método da ação do erro para ser tratada
     *
     * @return void
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $nErro = new NDefault_ErroNegocio();
        $chvErro = $nErro->registraErro($errors->exception);
        $this->_redirect(END_WEB . '/error/index/id/' . $chvErro);
    }

}
