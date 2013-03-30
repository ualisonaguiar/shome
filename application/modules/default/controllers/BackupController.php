<?php

/**
 *
 */

/**
 * Classe responsável pelo agendamento dos backups do sistema
 */
class Default_BackupController extends Default_SegurancaController
{

    /**
     *
     */
    public function indexAction()
    {
        $nBackup = new NDefault_BackupNegocio();
        $coBackup = $nBackup->listagem(
            array(
                'status' => true
            )
        );
        $this->view->coBackup = $coBackup;
    }

}