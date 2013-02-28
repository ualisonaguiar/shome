<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Entidade
 *
 * @author ualison
 */
class Form_Conta extends Zend_Form
{

    /**
     *
     * @return type
     */
    public function init()
    {
        $chvTipoConta = new Zend_Form_Element_Select('chv_tp_conta');
        $nTpConta = new NDefault_TipoContaNegocio();
        $aTpConta = array();
        $coTpContas = $nTpConta->listagem();

        foreach ( $coTpContas as $contaPai ) {
            if ( empty($contaPai['chv_tp_conta_pai']) ) {
                foreach ( $coTpContas as $contaFilho ) {
                    if ( $contaFilho['chv_tp_conta_pai'] == $contaPai['chv_tp_conta'] ) {
                        $aTpConta[$contaPai['nom_tipo']][$contaFilho['chv_tp_conta']] = $contaFilho['nom_tipo'];
                    }
                }
            }
        }

        $chvTipoConta->setLabel('Tipo de conta')
            ->addMultiOption('', 'Selecione')
            ->addMultiOptions($aTpConta)
            ->setErrorMessages(array('Informe o tipo da conta'))
            ->setRegisterInArrayValidator(true)
            ->addValidator('NotEmpty', false)
            ->setRequired(true);

        $chvEntidade = new Zend_Form_Element_Select('chv_entidade');
        $nEntidade = new NDefault_EntidadeNegocio();
        $coEntidades = $nEntidade->listagem(true);

        $coEntidade = array();
        if ( count($coEntidades) != 0 ) {
            foreach ( $coEntidades as $entidade ) {
                $coEntidade[$entidade['chv_pessoa_juridica']] = $entidade['nom_fantasia'];
            }
        }
        $chvEntidade->setLabel('Entidade jurídica')
            ->addMultiOption('', 'Selecione')
            ->addMultiOptions($coEntidade)
            ->setRegisterInArrayValidator(true)
            ->addValidator('NotEmpty', false)
            ->setErrorMessages(array('Informe a entidade responsável por esta conta'))
            ->setRequired(true);

        $nomConta = new Zend_Form_Element_Text('nom_conta');
        $nomConta->setLabel('Nome da conta')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge')
            ->setAttrib("maxlength", "400")
            ->addValidator('StringLength', false, array(3, 400, 'UTF-8'))
            ->setErrorMessages(array('Informe o nome da conta'));

        $dtVencimento = new Zend_Form_Element_Text('dat_vencimento');
        $dtVencimento->setLabel('Data de vencimento')
            ->setRequired(true)
            ->setAttrib('class', 'input-small data')
            ->addValidator(new Zend_Validate_Date('dd/MM/yyyy'))
            ->setErrorMessages(array('A data informada é inválida'))
            ->setAttrib("maxlength", "10");

        $geradorConta = new Zend_Form_Element_Select('gerar_conta');
        $geradorConta->setLabel('Gerar novas contas a partir desta?')
            ->addMultiOption('', 'Selecione')
            ->addMultiOptions(array(1 => 'Sim', 2 => 'Não'))
            ->setRegisterInArrayValidator(true)
            ->addValidator('NotEmpty', false)
            ->setValue(2)
            ->setRequired(true);

        $tipoVencimento = new Zend_Form_Element_Select('tipo_vencimento');
        $tipoVencimento->setLabel('Tipo de vencimento?')
            ->setAttrib('disabled', 'true')
            ->addMultiOption('', 'Selecione')
            ->addMultiOptions(
                array(
                    '1' => 'Semanal',
                    '2' => 'Mensal',
                    '3' => 'Bimestral',
                    '4' => 'Trimestral',
                    '5' => 'Semestral',
                    '6' => 'Anual'
                )
            )
            ->setErrorMessages(array('Tipo de vencimento inválido'))
            ->setRegisterInArrayValidator(true)
            ->setRequired(false);

        $numeroParcelas = new Zend_Form_Element_Text('nr_parcela');
        $numeroParcelas->setLabel('Número de parcelas')
            ->setRequired(false)
            ->setAttrib('class', 'input-small numero')
            ->addValidator('StringLength', false, array(1, 20, 'UTF-8'))
            ->addValidator(new Zend_Validate_Int())
            ->setErrorMessages(array('Número de parcelas inválida'))
            ->setAttrib("maxlength", "20");


        $vlrConta = new Zend_Form_Element_Text('vlr_conta');
        $vlrConta->setLabel('Valor da fatura')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge moeda')
            ->setErrorMessages(array('Valor da conta inválida'))
            ->setAttrib("maxlength", "255");

        $fileConta = new Zend_Form_Element_File('fatura');
        $fileConta->setLabel('Fatura')
            ->setRequired(false)
            ->addValidator('Size', false, 10485760)
            ->addValidator('NotEmpty')
            ->setErrorMessages(array('Tipo de conta inválida'))
            ->addValidator('Count', false, 1);

        $dtPagamento = new Zend_Form_Element_Text('dat_pagamento');
        $dtPagamento->setLabel('Data de pagamento')
            ->setRequired(false)
            ->addValidator(new Zend_Validate_Date('dd/MM/yyyy'))
            ->setAttrib('class', 'input-small data')
            ->setErrorMessages(array('Data de pagamento inválida'))
            ->setAttrib("maxlength", "10");

        $vlrPaga = new Zend_Form_Element_Text('vlr_pagamento');
        $vlrPaga->setLabel('Valor do pagamento')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge moeda')
            ->setErrorMessages(array('Valor pago inválido'))
            ->setAttrib("maxlength", "255");

        $filePaga = new Zend_Form_Element_File('recibo');
        $filePaga->setLabel('Recibo da conta')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->setErrorMessages(array('Recibo inválido'))
            ->addValidator('Size', false, array('min' => 0, 'max' => 10485760));

        $observacao = new Zend_Form_Element_Textarea('observacao');
        $observacao->setLabel('Observações/Informações sobre a conta')
            ->setRequired(false)
            ->setAttrib('class', 'ckeditor')
            ->setAttrib('row', 10);

        $this->addElements(
            array(
                $tipoVencimento, $numeroParcelas,
                $chvTipoConta, $chvEntidade, $nomConta,
                $dtVencimento, $vlrConta, $fileConta,
                $dtPagamento, $vlrPaga, $filePaga,
                $observacao, $geradorConta
            )
        );
        $this->addDisplayGroup(
            array(
            'chv_tp_conta',
            'chv_entidade',
            'nom_conta',
            ), 'informacao'
        );

        $this->addDisplayGroup(
            array('dat_vencimento', 'vlr_conta', 'fatura', 'gerar_conta'), 'vencimento'
        );

        $this->addDisplayGroup(
            array('tipo_vencimento', 'nr_parcela'), 'gerarParcelas', array('class' => 'hide')
        );

        $this->addDisplayGroup(
            array(
            'dat_pagamento',
            'vlr_pagamento',
            'recibo'
            ), 'pagamento'
        );

        $this->addDisplayGroup(
            array(
            'observacao'
            ), 'observacao'
        );
    }

    public function isValid($data)
    {
        if ( array_key_exists('gerar_conta', $data) ) {
            if ( $data['gerar_conta'] == 1 ) {
                if ( empty($data['tipo_vencimento']) || empty($data['nr_parcela']) ) {
                    return false;
                }
            }
        }

        if ( !empty($data['chv_conta']) ) {
            $data['tipo_vencimento'] = "1";
            $data['gerar_conta'] = "2";
        }
        return parent::isValid($data);
    }

}

?>
