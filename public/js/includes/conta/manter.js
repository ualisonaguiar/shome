var Conta = {};

Conta.AdicionarBotaoTipoConta = function() {
  $('#chv_tp_conta-element').append('&nbsp;&nbsp;<a title="Adicionar tipo de conta" '+
    'class="btn btn-small btn-primary add-tp-conta" hreaf="#">'+
    '<i class="icon-plus-sign icon-white"></i></a>');
}

Conta.incluirTipoConta = function(nom_tipo_conta) {
  $.ajax({
    type: 'post',
    data: {
      nom_tipo_conta : nom_tipo_conta
    },
    url:baseUrl+'/conta/tipoconta/',
    dataType: 'json',
    success: function(response){
      $('#modal_add_tp_conta').modal('hide');
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }
      Mensagem.sucesso(response.mensagem);
      $('#chv_tp_conta').append(
        '<option value="'+response.data.chv_tp_conta+'" selected="selected">'+response.data.nom_tipo+'</option>'
        );
    }
  });
}

Conta.excluirArquivo = function(chv_file) {
  $.ajax({
    type: 'post',
    data: {
      chv_file : chv_file,
      chv_conta: $('#chv_conta').val()
    },
    url:baseUrl+'/conta/excluirfile/',
    dataType: 'json',
    success: function(response){
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }
      Mensagem.sucesso(response.mensagem);
      $("a.btExcluir[value='"+chv_file+"']").parent().parent().remove();
    }
  });
}

function vinculaTipoConta() {
  if($('#nom_conta').val() == '') {
    if($('#chv_tp_conta').val() != '' && $('#chv_entidade').val() != '') {
      var nome = $('#chv_tp_conta option:selected').html();
      nome += " - ";
      nome += $('#chv_entidade option:selected').html();
      $('#nom_conta').val(nome);
    }
  }
}

$(function(){
  Conta.AdicionarBotaoTipoConta();
  $('#gerarParcelas-label').remove();
  $('body').on('click','.add-tp-conta', function() {
    $('#nom_tipo').val('');
    $('#modal_add_tp_conta').modal('show');
  }).on('change', '#chv_tp_conta', function(){
    vinculaTipoConta();
  }).on('change', '#chv_entidade', function(){
    vinculaTipoConta();
  }).on('click','.btExcluir', function() {
    Conta.excluirArquivo($(this).attr('value'));
  }).on('change', '#gerar_conta', function() {
    if($(this).val() == '' || $(this).val() == '2') {
      $('#tipo_vencimento').val(0);
      $('#nr_parcela').val('');
      $('#fieldset-gerarParcelas').addClass('hide')
      $('#tipo_vencimento').attr('disabled', true);
      $('#tipo_vencimento').removeAttr('required');

      $('#nr_parcela').attr('disabled', true);
      $('#nr_parcela').removeAttr('required');
      return false;
    }
    $('#fieldset-gerarParcelas').removeClass('hide');
    $('#tipo_vencimento').attr('disabled', false);
    $('#tipo_vencimento').attr('required', 'true');

    $('#nr_parcela').attr('disabled', false);
    $('#nr_parcela').attr('required', 'true');
  });

  $('#formAddTipoConta').submit(function() {
    Conta.incluirTipoConta($('#nom_tipo').val());
    return false;
  });
});