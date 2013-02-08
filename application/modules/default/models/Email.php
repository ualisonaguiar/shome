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
class PDefault_Models_Email extends Zend_Db_Table
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
    protected $_name = "email";

    /**
     * Chave primaria
     * @var string
     */
    protected $_primary = "chv_email";

    /**
     * Define the logic for new values in the primary key.
     * May be a string, boolean true, or boolean false.
     *
     * @var mixed
     */
    protected $_sequence = "admin.email_chv_email_seq";

}