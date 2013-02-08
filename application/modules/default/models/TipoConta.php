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
class PDefault_Models_TipoConta extends Zend_Db_Table
{

    /**
     * SCHEMA do banco de dados.
     * @var string
     */
    protected $_schema = "conta";

    /**
     * Nome da tabela
     * @var string
     */
    protected $_name = "tipo_conta";

    /**
     * Chave primaria
     * @var string
     */
    protected $_primary = "chv_tp_conta";

    /**
     * Define the logic for new values in the primary key.
     * May be a string, boolean true, or boolean false.
     *
     * @var mixed
     */
    protected $_sequence = "conta.tipo_conta_chv_tp_conta_seq";

}