<?php

class CronConta
{

    /**
     *
     * @var type
     */
    private static $prazoData = 15;

    private static $qtdEnvioPermitido = 2;

    /**
     *
     * @var type
     */
    private static $aMensagem = null;

    /**
     * Inicio da execução
     */
    static function run()
    {
        $coLogContas = self::visualizeLogAtual();
        if ( count($coLogContas) < self::$qtdEnvioPermitido )
        {
            $nConta = new NDefault_ContaNegocio();
            $coContas = $nConta->listagemContaNaoPaga();

            if ( count($coContas) ) {
                foreach ( $coContas as $contas ) {
                    $dias = self::validaData($contas['dataVencimento']);
                    if ( $dias < 0 || $dias <= self::$prazoData ) {
                        self::preparaMensagem($contas, $dias);
                    }
                }

                if ( count(self::$aMensagem) != 0 ) {
                    self::disparaEmail();
                }
            }
        }
        self::registraLog();
    }

    /**
     * Método responsável para validar as datas
     *
     * @param type $dataVencimento
     * @return type
     */
    static protected function validaData($dataVencimento)
    {
        list($d, $m, $y) = explode('/', $dataVencimento);
        $dI = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $dF = mktime(0, 0, 0, $m, $d, $y);

        $diferenca = $dF - $dI;
        $dias = (int) floor($diferenca / (60 * 60 * 24));
        return $dias;
    }

    /**
     *
     * @throws Zend_Exception
     */
    static protected function disparaEmail()
    {
        $aEmaisl = self::$aMensagem;
        try {
            foreach ( $aEmaisl as $email => $mensagens ) {
                $sMensagem = '';
                foreach ( $mensagens as $msg ) {
                    $sMensagem .= $msg . '<br><br>';
                }

                NDefault_MensagemNegocio::enviarEmail(
                    $sMensagem,
                    'Contas vencidas ou à vencer',
                    array($email)
                );
            }
            self::$aMensagem = $sMensagem;
        } catch ( Zend_Exception $exc ) {
            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     *
     * @param type $contas
     * @param type $dias
     */
    static protected function preparaMensagem($contas, $dias)
    {
        $nomConta = $contas['nomeConta'];
        $nomEmpre = $contas['pj'];
        $datVenc = $contas['dataVencimento'];

        $nUsuario = new NDefault_UsuarioNegocio();
        $coUsuario = $nUsuario->listagemUsuario($contas['usuario']);
        $coUsuario = $coUsuario[0];

        if ( $dias < 0 ) {
            $strFraseEmail  = "Conta " . $nomConta . ', ' . $nomEmpre;
            $strFraseEmail .= ' - encontra-se vencida desde o dia: ' . $datVenc;
        } elseif ( $dias >= 0 && $dias <= self::$prazoData ) {
            $strFraseEmail  = "Conta " .$nomConta . ', ' . $nomEmpre;
            if ($dias == 0) {
                $strFraseEmail .= ' - vencerá daqui: ' . $dias . ' dias';
            } else {
                $strFraseEmail .= ' - vencendo hoje';
            }
        }
        self::$aMensagem[$coUsuario['ds_email']][] = $strFraseEmail;
    }

    /**
     *
     * @return type
     */
    static function visualizeLogAtual()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->select()
            ->from('admin.log_cron')
            ->where('tipo_cron = ?', 'conta')
            ->where('dat_log >= ?', date('Y-m-d 00:00:00'))
            ->where('dat_log <= ?', date('Y-m-d 23:59:59'))
            ->query();
        return $sql->fetchAll();
    }

    /**
     *
     */
    static function registraLog()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->insert(
            'admin.log_cron',
            array(
                'tipo_cron' => 'conta',
                'conteudo' => self::$aMensagem
            )
        );
    }

}

