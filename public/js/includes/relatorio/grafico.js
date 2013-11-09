var Avaliador = {};

Avaliador.pesquisarRelatorio = function() {
    $.ajax({
        type: 'post',
        data: {
            periodicidade: $('#periodicidadeConta').val(),
            datInicio: $('#dataInicio').val(),
            datFim: $('#dataFim').val()
        },
        url: baseUrl + '/relatorio/pesquisar-resultado-grafico/',
        dataType: 'json',
        success: function(response) {

        }
    });
};

$(function() {
    $('#pesquisar').click(function() {
        if ($('#periodicidadeConta').val() === '') {
            Mensagem.alerta('Informa a periodicidade da conta.');
            return false;
        }
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

    $.jqplot.config.enablePlugins = true;
    var s1 = [2, 6, 7, 10];
    var ticks = ['a', 'b', 'c', 'd'];

    plot1 = $.jqplot('resultadoPesquisa', [s1], {
        // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
        animate: !$.jqplot.use_excanvas,
        seriesDefaults: {
            renderer: $.jqplot.BarRenderer,
            pointLabels: {show: true}
        },
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks
            }
        },
        highlighter: {show: false}
    });

});