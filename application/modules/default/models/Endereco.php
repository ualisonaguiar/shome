<?php

/**
 *
 */

/**
 *
 */
class PDefault_Models_Endereco extends Zend_Db_Table
{

    /**
     * SCHEMA do banco de dados.
     * @var string
     */
    protected $_schema = "public";

    /**
     * Nome da tabela
     * @var string
     */
    protected $_name = "endereco";

    /**
     * Chave primaria
     * @var string
     */
    protected $_primary = "chv_endereco";

    /**
     * Define the logic for new values in the primary key.
     * May be a string, boolean true, or boolean false.
     *
     * @var mixed
     */
    protected $_sequence = "endereco_chv_endereco_seq";

}