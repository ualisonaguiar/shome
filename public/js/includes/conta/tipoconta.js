var Conta = {};

Conta.incluirTipoConta = function(nom_tipo_conta) {
  $.ajax({
    type: 'post',
    data: {
      nom_tipo_conta : nom_tipo_conta,
      chv_tp_conta : $('#chv_tp_conta').val()
    },
    url:baseUrl+'/conta/tipoconta/',
    dataType: 'json',
    success: function(response){
      $('#modal_add_tp_conta').modal('hide');
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }
      Mensagem.sucesso(response.mensagem, true);
      window.location();
    }
  });
}

Conta.exluirTipoConta = function() {
  $.ajax({
    type: 'post',
    data: {
      chv_contas : $('input:checked').serialize()
    },
    url:baseUrl+'/conta/excluirtipoconta/',
    dataType: 'json',
    success: function(response){
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }
      Mensagem.sucesso(response.mensagem, true);
    }
  });
}

Conta.editarTipo = function(chv_tp_conta) {
  $.ajax({
    type: 'post',
    data: {
      chv_tp_conta : chv_tp_conta
    },
    url:baseUrl+'/conta/editartipoconta/',
    dataType: 'json',
    success: function(response){
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }
      $('#modal_add_tp_conta').modal('show');
      $('#nom_tipo').val(response.data.nom_tipo);
      $('#chv_tp_conta').val(response.data.chv_tp_conta);
    }
  });
}

$(function(){
  $('body').on('click','.add-tp-conta', function() {
    $('#nom_tipo').val('');
    $('#modal_add_tp_conta').modal('show');
  }).on('click', '.btn-danger', function() {
    Conta.exluirTipoConta();
  }).on('click', '.btEditar', function() {
    Conta.editarTipo($(this).attr('value'));
  }).on('click','.ver-detalhes', function(){
    var detalhes = $(this).parent().children('table');

    if(detalhes.hasClass('hide')) {
      $(this).children('i').removeClass('icon-folder-close');
      $(this).children('i').addClass('icon-folder-open');
      detalhes.removeClass('hide');
    } else {
      $(this).children('i').addClass('icon-folder-close');
      $(this).children('i').removeClass('icon-folder-open');
      detalhes.addClass('hide');
    }
  });

  $('#formAddTipoConta').submit(function() {
    Conta.incluirTipoConta($('#nom_tipo').val());
    return false;
  });
});