<?php

class Default_AnexoController extends Default_SegurancaController
{

    /**
     *
     */
    public function downloadAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $chvFile = $this->_getParam('id');

        $nArquivo = new NDefault_AnexoNegocio();
        $coFiles = $nArquivo->listagem($chvFile);

        if ( count($coFiles) != 0 ) {
            $coFiles = $coFiles[0];

            if ( file_exists($coFiles['caminho_file']) && md5_file($coFiles['caminho_file']) == $coFiles['md5'] ) {
                header('Content-Description: File Transfer');
                header('Content-Type: ' . $coFiles['type_file']);
                header("Content-Length: " . filesize($coFiles['caminho_file']));
                header("Content-Disposition: attachment; filename=" . preg_replace('/\s/', '_', $coFiles['nm_file']));
                header('Expires: 0');
                readfile($coFiles['caminho_file']);
            }
        }
    }

}