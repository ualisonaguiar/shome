<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelo envio das mensagens do sistema
 * E-mail
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */
class NDefault_MensagemNegocio extends PDefault_Models_Email
{

    /**
     * Método contendo os dados do envio do e-mail
     * @return \Zend_Mail_Transport_Smtp
     */
    public static function getMailInstance($config)
    {
        $servidor = $config['smtp'];
        unset($config['smtp']);
        return new Zend_Mail_Transport_Smtp($servidor, $config);
    }

    /**
     * Método responsável pelo envio e-mail
     *
     * @param type $mensagem
     * @param type $assunto
     * @param type $aEmails
     */
    static function enviarEmail($mensagem, $assunto, $aEmails)
    {
        try {
            $mail = new Zend_Mail('utf-8');
            $config = new Zend_Config_Ini(
                    APPLICATION_PATH . '/configs/application.ini',
                    APPLICATION_ENV
            );
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $sql = $db->select()
                ->from(
                    'admin.email', array(
                    'usuario',
                    'nome_usuario',
                    'senha_email',
                    'servidor'
                    )
                )
                ->where('status = true')
                ->query();

            if ( $sql->rowCount() != 0 ) {
                $config = $sql->fetchObject();
                foreach ( $aEmails as $email ) {
                    $mail->setFrom(
                            $config->usuario,
                            $config->nome_usuario
                    )
                    ->addTo($email)
                    ->setBodyHtml($mensagem)
                    ->setSubject($assunto)
                    ->send(
                        self::getMailInstance(
                            array(
                                'auth' => 'login',
                                'username' => $config->usuario,
                                'password' => $config->senha_email,
                                'smtp' => $config->servidor
                            )
                        )
                    );
                }
            }
        } catch ( Zend_Exception $exc ) {
            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     * Método responsável para listar as configurações de e-mail
     *
     * @param integer $chvEmail Chave de e-mail
     * @return ArrayIterator
     */
    public function getListagemConfiguracaoEmail($chvEmail = null)
    {
        $sql = $this->select()
            ->from(
                $this->_schema . '.' . $this->_name, array(
                'chv_email',
                'usuario',
                'nome_usuario',
                'senha_email',
                'servidor',
                'dat_cadastro' => new Zend_Db_Expr(
                    "to_char(dat_cadastro, 'DD/MM/YYYY HH24:MI:SS')"
                ),
                'status'
                )
            )
            ->order(array('dat_cadastro desc', 'status'));
        $sWhere = array();
        if ( !empty($chvEmail) ) {
            $sql->where('chv_email = ?', $chvEmail);
        }
        $result = $sql->query();
        return $result->fetchAll();
    }

    /**
     * Método responsável para salvar as configurações de e-mail
     *
     * @param type $post
     * @param $verificaConfig
     * @return boolean
     */
    public function salvar($post, $verificaConfig = true)
    {
        try {
            $objDb = $this->getDefaultAdapter();
            $objDb->beginTransaction();
            if ( !empty($post['chv_email']) ) {
                $this->update(
                    array(
                    'nome_usuario' => $post['usuario'],
                    'usuario' => $post['email'],
                    'senha_email' => $post['senha'],
                    'servidor' => $post['servidor']
                    ), array(
                    'chv_email = ?' => $post['chv_email']
                    )
                );
            } else {
                $row = $this->createRow();
                $this->update(
                    array(
                    'status' => 'false'
                    ), array()
                );
                $row->usuario = $post['email'];
                $row->nome_usuario = $post['usuario'];
                $row->senha_email = $post['senha'];
                $row->servidor = $post['servidor'];
                $row->save();
            }

            if ( $verificaConfig ) {
                self::enviarEmail(
                    'Teste de configuração de e-mail', 'Teste de configuração de e-mail', array(EMAIL_ADMINISTRADOR)
                );
            }
            $objDb->commit();
        } catch ( Zend_Db_Exception $exc ) {
            //$this->delete(array('chv_email' => $chvConfig));
            $objDb->rollBack();
            throw new Zend_Exception($exc->getMessage());
        }
    }

    public function alteraStatus($chvEmail, $status)
    {
        try {
            $coEmail = $this->getListagemConfiguracaoEmail($chvEmail);

            if ( count($coEmail) == 0 ) {
                throw new Zend_Exception(
                    'Não encontrado configuração de e-mail'
                );
            }
            $objDb = $this->getDefaultAdapter();
            $objDb->beginTransaction();

            if ( $status ) {
                $this->update(
                    array(
                    'status' => 'false'
                    ), array()
                );
            }

            $this->update(
                array(
                'status' => ($status) ? 'true' : 'false'
                ), array(
                'chv_email = ?' => $chvEmail
                )
            );

            if ( $status ) {
                self::enviarEmail(
                    'Teste de configuração de e-mail', 'Teste de configuração de e-mail',
                    array(EMAIL_ADMINISTRADOR)
                );
            }
            $objDb->commit();
        } catch ( Zend_Db_Exception $exc ) {
            $objDb->rollBack();
            throw new Zend_Exception($exc->getMessage());
        }
    }

}