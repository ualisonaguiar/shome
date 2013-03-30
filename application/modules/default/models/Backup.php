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
class PDefault_Models_Backup extends Zend_Db_Table
{

    /**
     * SCHEMA do banco de dados.
     * @var string
     */
    protected $_schema = "admin";

    /**
     * Nome da tabela
     * @var string
     */
    protected $_name = "backup";

    /**
     * Chave primaria
     * @var string
     */
    protected $_primary = "chv_backup";

    /**
     * Define the logic for new values in the primary key.
     * May be a string, boolean true, or boolean false.
     *
     * @var mixed
     */
    protected $_sequence = "admin.backup_chv_backup_seq";

    /**
     * Método responsável para gravar o backup no banco de dados
     *
     * @param binario $arquivo arquivo contendo o bakcup
     * @return int Código da inserção da tabela
     */
    public function gravaArquivoBackup($arquivo)
    {
        $config = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini',
                APPLICATION_ENV
        );

        Zend_Registry::set('dbbckp', $config->dbbckp);
        $this->objConexao = Zend_Registry::get('dbbckp');
    }
}