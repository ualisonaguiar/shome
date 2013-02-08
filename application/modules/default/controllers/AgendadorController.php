<?php

include_once 'Cron/CronConta.php';

class Default_AgendadorController extends Default_SegurancaController
{

    public function preDispatch()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        header('Content-Type: text/html; charset=utf-8');
    }

    public function cronjobAction()
    {
        try {
            CronConta::run();

        } catch ( Exception $exc ) {
            $nErro = new NDefault_ErroNegocio();
            $nErro->registraErro($exc);
            echo $exc->getMessage();
        }
    }
}