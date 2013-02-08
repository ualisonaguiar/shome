<?php

class Default_WslugarController extends Default_SegurancaController
{

    /**
     * Método principal.
     *
     * @return void
     */
    public function buscacepAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        try {
            $nWsLugar = new NDefault_WslugarNegocio();
            $cep = $this->getParam('cep');
            $aResponse = $nWsLugar->buscaCep($cep);
            echo json_encode(array('status' => true, 'data' => $aResponse));
        } catch ( Zend_Exception $exc ) {
            echo json_encode(array('status' => false, 'mensagem' => $exc->getMessage()));
        }
    }

}