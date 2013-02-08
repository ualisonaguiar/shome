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
class Form_Seguranca extends Zend_Form
{

    /**
     *
     */
    public function login()
    {
        $form = new Zend_Form();

        $usuario = new Zend_Form_Element_Text('usuario');
        $usuario->setAttrib('placeholder', 'usuário')
            ->setAttrib('class', 'input-medium')
            ->setLabel('Usuário do sistema')
            ->setAttrib('required', 'required')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setRequired(true);

        $senha = new Zend_Form_Element_Password('senha');
        $senha->setAttrib('placeholder', 'Senha')
            ->setLabel('Senha do usuário')
            ->setAttrib('class', 'input-medium')
            ->setAttrib('required', 'required')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('Logar');
        $submit->setAttrib('class', 'btn btn-primary');

        $esqueceSenha = new Zend_Form_Element_Button('recuperarSenha');
        $esqueceSenha->setAttrib('class', 'btn btn-success')
            ->setLabel('Recuperar senha');

        $form->setAttrib('class', 'form-signin')
            ->setMethod('post')
            ->setName('form-login')
            ->addElement($usuario)
            ->addElement($senha)
            ->addElement($submit)
            ->addElement($esqueceSenha);

        return $form;
    }

    /**
     *
     */
    public function recuperarSenha()
    {
        $cpf = new Zend_Form_Element_Text('cpf_usuario');
        $cpf->setLabel('CPF do usuário')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setAttrib('class', 'input-medium cpf')
            ->setAttrib("maxlength", "14");

        $recuperarSenha = new Zend_Form_Element_Button('recuperar');
        $recuperarSenha->setAttrib('class', 'btn btn-success')
            ->setLabel('Recuperar senha');

        $btnCancelar = new Zend_Form_Element_Button('voltar-recuperar');
        $btnCancelar->setAttrib('class', 'btn')
            ->setAttrib('data-dismiss', 'modal')
            ->setAttrib('aria-hidden', true)
            ->setLabel('Cancelar');

        return $this->addElement($cpf)
                ->addElement($recuperarSenha)
                ->addElement($btnCancelar);
    }

    /**
     * Método responsável para validar dados elementares
     *
     * @param type $data
     * @return type
     */
    public function isValidRecuperarSenha($data)
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

        return $this->recuperarSenha()->isValid($data);
    }

    /**
     * Chave do usuário
     * @param type $chvUsuario
     */
    public function alteraSenhaUsuario()
    {
        $senhaAtual = new Zend_Form_Element_Password('ds_senha');
        $senhaAtual->setLabel('Senha atual')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setAttrib('class', 'input-medium')
            ->setAttrib("maxlength", "32");

        $novaSenha = new Zend_Form_Element_Password('nova_senha');
        $novaSenha->setLabel('Nova senha')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setAttrib('class', 'input-medium')
            ->setAttrib("maxlength", "32");

        $confirmSenha = new Zend_Form_Element_Password('conf_senha');
        $confirmSenha->setLabel('Confirmação de senha')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('required', 'required')
            ->setAttrib('class', 'input-medium')
            ->setAttrib("maxlength", "32");

        return $this->addElement($senhaAtual)
                ->addElement($novaSenha)
                ->addElement($confirmSenha);
    }

}