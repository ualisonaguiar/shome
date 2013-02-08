<?php

/**
 *
 */

/**
 *
 */
class PDefault_Models_Entidade extends Zend_Db_Table
{

    /**
     * SCHEMA do banco de dados.
     * @var string
     */
    protected $_schema = "pessoa";

    /**
     * Nome da tabela
     * @var string
     */
    protected $_name = "pessoa_juridica";

    /**
     * Chave primaria
     * @var string
     */
    protected $_primary = "chv_pessoa_juridica";

    /**
     * Define the logic for new values in the primary key.
     * May be a string, boolean true, or boolean false.
     *
     * @var mixed
     */
    protected $_sequence = "pessoa.pessoa_juridica_chv_pessoa_juridica_seq";

}