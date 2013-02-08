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
        $aTpConta = array('' => 'Selecione');
        $coTpContas = $nTpConta->listagem();

        if ( count($coTpContas) != 0 ) {
            foreach ( $coTpContas as $tpConta ) {
                $aTpConta[$tpConta['chv_tp_conta']] = $tpConta['nom_tipo'];
            }
        }

        $chvTipoConta->setLabel('Tipo de conta')
            ->addMultiOptions($aTpConta)
            ->setAttrib('required', 'required')
            ->setRegisterInArrayValidator(true)
            ->addValidator('NotEmpty', false)
            ->setRequired(true);

        $chvEntidade = new Zend_Form_Element_Select('chv_entidade');
        $nEntidade = new NDefault_EntidadeNegocio();
        $coEntidades = $nEntidade->listagem(true);

        $coEntidade = array('' => 'Selecione');
        if ( count($coEntidades) != 0 ) {
            foreach ( $coEntidades as $entidade ) {
                $coEntidade[$entidade['chv_pessoa_juridica']] = $entidade['nom_fantasia'];
            }
        }
        $chvEntidade->setLabel('Entidade jurídica')
            ->addMultiOptions($coEntidade)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setRegisterInArrayValidator(true)
            ->addValidator('NotEmpty', false)
            ->setRequired(true);

        $nomConta = new Zend_Form_Element_Text('nom_conta');
        $nomConta->setLabel('Nome da conta')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->setAttrib('required', 'required')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge')
            ->setAttrib("maxlength", "400")
            ->setErrorMessages(array('Valor informado errado'))
            ->setValue('');

        $dtVencimento = new Zend_Form_Element_Text('dat_vencimento');
        $dtVencimento->setLabel('Data de vencimento')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->setAttrib('required', 'true')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-small data')
            ->setAttrib("maxlength", "10");

        $geradorConta = new Zend_Form_Element_Select('gerar_conta');
        $geradorConta->setLabel('Gerar novas contas a partir desta?')
            ->addMultiOptions(array('' => 'Selecione', '1' => 'Sim', '2' => 'Não'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setRegisterInArrayValidator(true)
            ->addValidator('NotEmpty', false)
            ->setRequired(true);

        $tipoVencimento = new Zend_Form_Element_Select('tipo_vencimento');
        $tipoVencimento->setLabel('Tipo de vencimento?')
            ->setAttrib('disabled', 'true')
            ->addMultiOptions(
                array(
                    '' => 'Selecione',
                    '1' => 'Semanal',
                    '2' => 'Mensal',
                    '3' => 'Bimestral',
                    '4' => 'Trimestral',
                    '5' => 'Semestral',
                    '6' => 'Anual'
                )
            )
            ->setRequired(false);

        $numeroParcelas = new Zend_Form_Element_Text('nr_parcela');
        $numeroParcelas->setLabel('Quantidade de parcelas')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-small numero')
            ->setAttrib("maxlength", "20");


        $vlrConta = new Zend_Form_Element_Text('vlr_conta');
        $vlrConta->setLabel('Valor da fatura')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->setAttrib('required', 'true')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge moeda')
            ->setAttrib("maxlength", "255");

        $fileConta = new Zend_Form_Element_File('fatura');
        $fileConta->setLabel('Fatura')
            ->setRequired(false)
            ->addValidator('Size', false, 10485760)
            ->addValidator('NotEmpty')
            ->addValidator('Count', false, 1);

        $dtPagamento = new Zend_Form_Element_Text('dat_pagamento');
        $dtPagamento->setLabel('Data de pagamento')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-small data')
            ->setAttrib("maxlength", "10");

        $vlrPaga = new Zend_Form_Element_Text('vlr_pagamento');
        $vlrPaga->setLabel('Valor do pagamento')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge moeda')
            ->setAttrib("maxlength", "255");

        $filePaga = new Zend_Form_Element_File('recibo');
        $filePaga->setLabel('Recibo da conta')
            ->setRequired(false)
            ->addFilter('StripTags')
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
            if ( $data['gerar_conta'] == 1 && (!empty($data['tipo_vencimento']) || !empty($data['nr_parcela'])) ) {
                return false;
            }
        }
        if ( !empty($data['chv_conta']) ) {
            $data['tipo_vencimento'] = "1";
            $data['gerar_conta'] = "2";
        }
        return parent::isValid($data);
    }

    public function populate(array $values)
    {
        $this->getElement('nr_parcela')
            ->setLabel('Número da parcela');
        return parent::populate($values);
    }

}

?>
