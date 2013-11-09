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
            var arrValor = new Array();
            var arrTicks = new Array();

            $.each(response.data, function(i) {
                arrValor[i] = this.totalConta;
                arrTicks[i] = this.datVencimento;
            });
            Avaliador.plotaGrafico(arrValor, arrTicks);
        }
    });
};

Avaliador.plotaGrafico = function(arrValor, arrTicks) {
    $.jqplot.config.enablePlugins = true;
    plot1 = $.jqplot('chart1', [arrValor], {
        animate: !$.jqplot.use_excanvas,
        seriesDefaults: {
            renderer: $.jqplot.BarRenderer,
            pointLabels: {show: true}
        },
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: arrTicks
            },
            yaxis: {
                tickOptions: {
                    formatString: "R$ %'d"
                },
                rendererOptions: {
                    forceTickAt0: true
                }
            }
        },
        highlighter: {show: true}
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


});