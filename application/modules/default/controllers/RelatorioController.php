<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelos relatórios
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */
class Default_RelatorioController extends Default_SegurancaController
{
    public function graficoAction() 
    {
        
    }    
    
    public function pesquisarResultadoGraficoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        if ($this->getRequest()->isPost()) {
            $arrPost = $this->getAllParams();

            $nRelatorio = new NDefault_RelatorioNegocio();
            $arrValorConta = $nRelatorio->relatorioMensal($arrPost['datInicio'], $arrPost['datFim']);

            $arrCores = array( 'AFD8F8', 'F6BD0F', '8BBA00', 'FF8E46', '008E8E', 'D64646', '8E468E',
                '588526', 'B3AA00', '008ED6', '9D080D', 'A186BE'
            );

            $strXml = '<graph '
                . 'caption="Conta / Mes" '
                . 'subcaption="Relatorio de Contas Pagas" '
                . 'xAxisName="Mes"'
                . 'yAxisName="Valores das contas"'
                . 'decimalPrecision="2"'
                . 'formatNumberScale="2"'
                . 'numberPrefix="R$ "'
                . 'showValues="0">';

            foreach ($arrValorConta as $arrConta) {
                $strCor = $arrCores[array_rand($arrCores)];
                $strXml .= "<set name='{$arrConta['datVencimento']}' value='{$arrConta['totalConta']}' color='{$strCor}' />";
            }
            $strXml .= '</graph>';

            $strNmFileXml = md5(date('Ymd_His')) . '.xml';
            file_put_contents(__APP__ . '/data/xml/' . $strNmFileXml, $strXml);
            echo Zend_Json_Encoder::encode( array('pathXml' => $strNmFileXml));
        }
    }

    public function excluirArquivoXmlAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $arrPost = $this->getAllParams();

        if (!empty($arrPost['strPathXml'])) {
            unlink(__APP__ . '/data/xml/'.$arrPost['strPathXml']);
        }
    }
}
