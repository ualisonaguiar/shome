<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelas regras de negócio da segurança
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */

class NDefault_SegurancaNegocio extends PDefault_Models_Seguranca
{

    /**
     * Método responsável que valida o login
     *
     * @param type $usuario
     * @param type $senha
     * @return ArrayIterator
     */
    public function loginUsuario($usuario, $senha)
    {
        try {
            if ( $usuario == null ) {
                throw new Zend_Exception('Não foi informado o usuário');
            }

            if ( $senha == null ) {
                throw new Zend_Exception('Não foi informado a senha');
            }
            $pUsuario = new PDefault_Models_Usuario();
            return $pUsuario->autenticar($usuario, $senha);
        } catch ( Zend_Exception $exec ) {
            throw new Zend_Exception($exec);
        }
    }

    /**
     * Método responsável pela geração da senha
     *
     * @return string
     */
    static function geradorSenha()
    {
        $aSenha['senha'] = str_shuffle(
            str_shuffle('NoVaSENHA') . rand(1, 9999)
        );
        $aSenha['cript'] = md5($aSenha['senha']);
        return $aSenha;
    }

}