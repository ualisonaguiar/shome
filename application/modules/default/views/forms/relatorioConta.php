<?php

/**
 *
 */

/**
 *
 */
class Form_relatorioConta extends Zend_Form
{

    /**
     *
     */
    public function init()
    {
        $chvTipoConta = new Zend_Form_Element_Select('chv_tp_conta');
        $nTpConta = new NDefault_TipoContaNegocio();
        $aTpConta = array('' => 'Selecione');
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
            ->addMultiOptions($aTpConta)
            ->setRegisterInArrayValidator(true)
            ->addValidator('NotEmpty', true)
            ->setRequired(false);

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
            ->setRegisterInArrayValidator(true)
            ->addValidator('NotEmpty', true)
            ->setRequired(false);

        $datIniciaVenc = new Zend_Form_Element_Text('data_venc_inicial');
        $datIniciaVenc->setLabel('Data de início do vencimento')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-small data')
            ->setAttrib("maxlength", "10");

        $datFinalVenc = new Zend_Form_Element_Text('data_venc_final');
        $datFinalVenc->setLabel('Data de fim do vencimento')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-small data')
            ->setAttrib("maxlength", "10");

        $datIniciaPag = new Zend_Form_Element_Text('data_pag_inicial');
        $datIniciaPag->setLabel('Data de início do pagamento')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-small data')
            ->setAttrib("maxlength", "10");

        $datFinalPag = new Zend_Form_Element_Text('data_pag_final');
        $datFinalPag->setLabel('Data de final do pagamento')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-small data')
            ->setAttrib("maxlength", "10");

        $this->addElements(
            array(
                $chvTipoConta, $chvEntidade, $datIniciaVenc, $datFinalVenc,
                $datIniciaPag, $datFinalPag
            )
        );

        $this->addDisplayGroup(
            array('chv_tp_conta', 'chv_entidade'), 'informacaoConta'
        );

        $this->addDisplayGroup(
            array('data_venc_inicial', 'data_venc_final'), 'dataVencimento'
        );

        $this->addDisplayGroup(
            array('data_pag_inicial', 'data_pag_final'), 'dataPagamento'
        );
    }

}
