<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelas regras de negócio do backup do sistema
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */
class NDefault_BackupNegocio extends PDefault_Models_Backup
{

    /**
     * Método responsável contendo a listagem das configurações
     * do backup
     *
     * @param type $aFilter
     * @return ArrayIterator
     */
    public function listagem($aFilter = null)
    {
        $sql = $this->select();
        //ativo
        if ( !empty($aFilter['status']) ) {
            $sql->where('status = ?', $aFilter['status']);
        }

        $rs = $sql->query();
        return $rs->fetchAll();
    }

}