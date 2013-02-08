<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Usuario
 *
 * @author Ualison
 */
class PDefault_Models_Gerenciamento extends Zend_Db_Table
{

    /**
     * tamanho do banco de dados
     */
    public function tamanhoDatabase()
    {
        $objDB = $this->getAdapter();
        $coInfo = $objDB->getConfig();
        $sql = "select * from pg_database_size('{$coInfo['dbname']}')";
        return $objDB->query($sql)->fetchObject();
    }

    public function tamanhoFile()
    {
        $objDB = $this->getAdapter();
        $sql = "
            select
                (select sum(size_file) from file as file1 where file1.extensao_file = file2.extensao_file) as sum_file,
                extensao_file
            from
                file as file2
                group by extensao_file, sum_file order by extensao_file asc
        ";

        $result = $objDB->query($sql);
        return $result->fetchAll();
    }

}