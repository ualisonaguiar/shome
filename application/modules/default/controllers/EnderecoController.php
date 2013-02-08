<?php

class Default_EnderecoController extends Default_SegurancaController
{

    /**
     * Método principal.
     *
     * @return void
     */
    public function cadastroAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $post = $this->_getAllParams();

        try {
            $nEndereco = new NDefault_EnderecoNegocio();
            $chvEndereco = $nEndereco->gravar($post);
            echo json_encode(array('status' => true, 'chvEndereco' => $chvEndereco));
        } catch ( Zend_Exception $exc ) {
            echo json_encode(array('status' => false, 'mensagem' => $exc->getMessage()));
        }
    }

    public function buscaenderecoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $chvEndereco = $chvUsuario = $this->getParam('chv_endereco');

        try {
            $nEndereco = new NDefault_EnderecoNegocio();
            $aEnderecos = $nEndereco->listagem($chvEndereco);
            echo json_encode(array('status' => true, 'dados' => $aEnderecos));
        } catch ( Exception $exc ) {
            echo json_encode(array('status' => false, 'mensagem' => $exc->getMessage()));
        }
    }

    /**
     * Método responsável pela exclusão do endereço
     */
    public function excluirAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $chvEndereco = $chvUsuario = $this->getParam('chv_endereco');

        try {
            $nEndereco = new NDefault_EnderecoNegocio();
            $aEnderecos = $nEndereco->excluir($chvEndereco);
            echo json_encode(array('status' => true, 'mensagem' => 'Endereço excluído com sucesso'));
        } catch ( Exception $exc ) {
            echo json_encode(array('status' => false, 'mensagem' => $exc->getMessage()));
        }
    }

}