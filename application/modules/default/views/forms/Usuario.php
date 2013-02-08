<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MaterialInicio
 *
 * @author Uálison
 */
class Form_Usuario extends Zend_Form
{

    /**
     * método para cadastrar novo usuário
     *
     * $return Zend_Form
     */
    public function novo()
    {
        $form = new Zend_Form();

        $nome = new Zend_Form_Element_Text('nom_usuario');
        $nome->setLabel('Nome do usuário')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setAttrib('class', 'input-xlarge')
            ->setAttrib("maxlength", "100");

        $cpf = new Zend_Form_Element_Text('cpf_usuario');
        $cpf->setLabel('CPF do usuário')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setAttrib('class', 'input-medium cpf')
            ->setAttrib("maxlength", "14");

        $email = new Zend_Form_Element_Text('ds_email');
        $email->setLabel('E-mail do usuário')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setAttrib('class', 'input-xlarge')
            ->addValidator('EmailAddress');

        $acessoSis = new Zend_Form_Element_Checkbox('acesso_sis');
        $acessoSis->setLabel('Este usuário terá acesso ao sistema');


        $dsLogin = new Zend_Form_Element_Text('ds_login');
        $dsLogin->setLabel('Login do usuário')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge')
            ->setAttrib('disabled', true)
            ->setAttrib("maxlength", "50");

        $submit = new Zend_Form_Element_Button('Cadastrar');
        $submit->setAttrib('type', 'submit')
            ->setAttrib('class', 'btn btn-success')
            ->setValue('Cadastrar');


        $form->setAttrib('class', 'form-signin')
            ->setMethod('post')
            ->setName('form_novo_usuario')
            ->addElement($nome)
            ->addElement($cpf)
            ->addElement($email)
            ->addElement($acessoSis)
            ->addElement($dsLogin)
            ->addElement($submit);

        return $form;
    }

    /**
     * método para cadastrar novo usuário
     *
     * $return Zend_Form
     */
    public function alterar($values)
    {
        $form = new Zend_Form();

        $chvUsuario = new Zend_Form_Element_Hidden('chv_usuario');
        $chvUsuario->setValue($values['chv_usuario']);

        $nome = new Zend_Form_Element_Text('nom_usuario');
        $nome->setLabel('Nome do usuário')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setAttrib('class', 'input-xlarge')
            ->setAttrib("maxlength", "100");

        $cpf = new Zend_Form_Element_Text('cpf_usuario');
        $cpf->setLabel('CPF do usuário')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setAttrib('class', 'input-medium cpf')
            ->setAttrib("maxlength", "14")
            ->setAttrib('disabled', 'disabled');

        $email = new Zend_Form_Element_Text('ds_email');
        $email->setLabel('E-mail do usuário')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setAttrib('class', 'input-xlarge')
            ->addValidator('EmailAddress');

        $dsLogin = new Zend_Form_Element_Text('ds_login');
        $dsLogin->setLabel('Login do usuário')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'input-xlarge')
            ->setAttrib("maxlength", "50");

        $acessoSis = new Zend_Form_Element_Checkbox('acesso_sis');
        $acessoSis->setLabel('Este usuário terá acesso ao sistema');

        if ( !empty($values['ds_login']) ) {
            $acessoSis->setAttrib('checked', 'checked');
            $dsLogin->setRequired(true);
        } else {
            $dsLogin->setAttrib('disabled', 'disabled');
        }

        $submit = new Zend_Form_Element_Button('Alterar');
        $submit->setAttrib('type', 'submit')
            ->setAttrib('class', 'btn btn-success')
            ->setValue('Altarar');

        $form->setAttrib('class', 'form-signin')
            ->setMethod('post')
            ->setName('form_alterar_usuario')
            ->addElement($chvUsuario)
            ->addElement($nome)
            ->addElement($cpf)
            ->addElement($email)
            ->addElement($acessoSis)
            ->addElement($dsLogin)
            ->addElement($submit);

        return $form->populate($values);
    }

    /**
     * Método responsável para validar dados elementares
     *
     * @param type $data
     * @return type
     */
    public function isValidNovo($data)
    {
        if ( !empty($data['cpf_usuario']) ) {
            if ( !preg_match(RECPF, $data['cpf_usuario']) ) {
                $cpf = $this->novo()->getElement('cpf_usuario');
                $cpf->setErrorMessages(
                    array('CPF inválido')
                );
                return false;
            }
        }

        return $this->novo()->isValid($data);
    }

    public function isValidAlterar($data)
    {
        return $this->alterar($data)->isValid($data);
    }

}