<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelas regras de negócio das contas
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */
class NDefault_RelatorioNegocio extends PDefault_Models_Conta
{
    public function relatorioSemestral($strDatInicio, $strDatFim)
    {
        $zendData = new Zend_Date($strDatInicio);
        $strDatInicio = $zendData->get('YYYY-MM-dd');
        $zendData = new Zend_Date($strDatFim);
        $strDatFim = $zendData->get('YYYY-MM-dd');

        $objDb = $this->getDefaultAdapter();
        $sql = $objDb->select()
            ->from(
                array ('contaDetalhes' => $this->_schema . '.detalhes_conta'),
                array(
                    'totalConta' => 'sum(vlr_conta)',
                    'datVencimento' => "to_char(dat_vencimento, 'MM/YYYY')"
                )
            )
            ->group(array ('datVencimento'))
            ->order('datVencimento')
            ->query();
        return $sql->fetchAll();
    }
}
