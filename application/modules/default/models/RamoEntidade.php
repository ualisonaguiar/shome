<?php

/**
 *
 */

/**
 *
 */
class PDefault_Models_RamoEntidade extends Zend_Db_Table
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
    protected $_name = "tipo_pessoa";

    /**
     * Chave primaria
     * @var string
     */
    protected $_primary = "chv_tp_pessoa";

    /**
     * Define the logic for new values in the primary key.
     * May be a string, boolean true, or boolean false.
     *
     * @var mixed
     */
    protected $_sequence = "pessoa.tipo_pessoa_chv_tp_pessoa_seq";

}