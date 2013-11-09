var Avaliador = {};

Avaliador.pesquisarRelatorio = function() {
    $.ajax({
        type: 'post',
        data: {
            datInicio: $('#dataInicio').val(),
            datFim: $('#dataFim').val()
        },
        url: baseUrl + '/relatorio/pesquisar-resultado-grafico/',
        dataType: 'json',
        success: function(response) {
            Avaliador.plotaGrafico(response.pathXml);
        }
    });
};

Avaliador.plotaGrafico = function(strPathXml) {
    var chart = new FusionCharts(baseUrl + "/public/js/library/FusionCharts/Charts/FCF_Line.swf", "ChartId", "1300", "350");
    chart.setDataURL(baseUrl + '/data/xml/' + strPathXml);
    chart.render("graficoBarra");
    setTimeout(function() {Avaliador.excluirArquivoXml(strPathXml);},3000);
};

Avaliador.excluirArquivoXml = function(strPathXml) {
    $.ajax({
        type: 'post',
        data: {
            strPathXml: strPathXml
        },
        url: baseUrl + '/relatorio/excluir-arquivo-xml',
        dataType: 'json',
        success: function(response) {
            
        }
    });
};

$(function() {
    $('#pesquisar').click(function() {
        if ($('#dataInicio').val() === '') {
            Mensagem.alerta('Informa a data de início da periodicidade.');
            return false;
        }
        if ($('#dataFim').val() === '') {
            Mensagem.alerta('Informa a data de fim da periodicidade.');
            return false;
        }
        Avaliador.pesquisarRelatorio();
    });
});