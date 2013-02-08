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
class Form_Entidade extends Zend_Form
{

    public function novo()
    {
        $form = new Zend_Form();

        $chvPessoaJuridica = new Zend_Form_Element_Hidden('chv_pessoa_juridica');

        $cnpj = new Zend_Form_Element_Text('cnpj_pessoa_juridica');
        $cnpj->setLabel('CNPJ da entidade')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge cnpj')
            ->setAttrib("maxlength", "15");

        $dscFantasia = new Zend_Form_Element_Text('nom_fantasia');
        $dscFantasia->setLabel('Nome fantasia')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->setAttrib('required', 'required')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge')
            ->setAttrib("maxlength", "200");

        $dscRazao = new Zend_Form_Element_Text('nom_pessoa');
        $dscRazao->setLabel('Razão social')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge')
            ->setAttrib("maxlength", "200");

        $dscSite = new Zend_Form_Element_Text('ds_site');
        $dscSite->setAttrib("type", "email")
            ->setLabel('Site da entidade')
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge')
            ->setAttrib("maxlength", "200");

        $ramoEntidade = new Zend_Form_Element_Select('chv_tp_pessoa');
        $nRamoEntidade = new NDefault_RamoEntidadeNegocio();
        $ramoEntidade->setLabel('Ramo que a entidade pertencem')
            ->addMultiOptions($nRamoEntidade->listagem())
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setRequired(false);

        $btnAdicionarEndereco = new Zend_Form_Element_Button('addEndereco');
        $btnAdicionarEndereco->setLabel('Adicionar endereço')
            ->setAttrib('class', 'btn btn-primary');

        $submit = new Zend_Form_Element_Button('Cadastrar');
        $submit->setAttrib('type', 'submit')
            ->setAttrib('class', 'btn btn-success');

        $form->setAttrib('class', 'form-signin')
            ->setMethod('post')
            ->setName('form_nova_entidade')
            ->addElement($chvPessoaJuridica)
            ->addElement($cnpj)
            ->addElement($dscFantasia)
            ->addElement($ramoEntidade)
            ->addElement($dscRazao)
            ->addElement($dscSite)
            ->addElement($btnAdicionarEndereco)
            ->addElement($submit);

        return $form;
    }

}

?>
