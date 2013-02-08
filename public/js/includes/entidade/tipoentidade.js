var Entidade = {};

Entidade.incluirTipoEntidade = function(nom_tipo) {
  $.ajax({
    type: 'post',
    data: {
      nom_tipo : nom_tipo
    },
    url:baseUrl+'/entidade/novotipo',
    dataType: 'json',
    success: function(response){
      $('#modal_add_tp_entidade').modal('hide');
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }
      Mensagem.sucesso(response.mensagem, true);
    }
  });
}

Entidade.exluirTipo = function() {
  if($('input:checked').length == 0) {
    Mensagem.information('Escolhe pelo menos um tipo de entidade para excluir.');
    return false;
  }
  var url = baseUrl+'/entidade/excluirtipo/chv_tp_pessoa/'+$('input:checked').serialize();
  Mensagem.confirmacao("Você deseja excluir esta tipo de entidade?", url);
  return false;
}

Entidade.editarTipo = function(chv_tp_conta) {
  $.ajax({
    type: 'get',
    data: {
      chv_tp_conta : chv_tp_conta
    },
    url:baseUrl+'/entidade/editartipo/',
    dataType: 'json',
    success: function(response){
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }
      $('#modal_add_tp_entidade').modal('show');
      $('#ds_tipo_pessoa').val(response.data.ds_tipo_pessoa);
      $('#chv_tp_entidade').val(response.data.chv_tp_pessoa);
    }
  });
}

$(function(){
  $('body').on('click','.add-tp-entidade', function() {
    $('#ds_tipo_pessoa').val('');
    $('#chv_tp_entidade').val('');
    $('#modal_add_tp_entidade').modal('show');
  }).on('click', '.btn-danger', function() {
    Entidade.exluirTipo();
  }).on('click', '.btEditar', function() {
    Entidade.editarTipo($(this).attr('value'));
  }).on('click','#edit_tp_entidade', function(){
    var chvTpEntidade = $(this).attr('values');
    Entidade.editarTipo(chvTpEntidade);
  });
  //  }).on('click', '.ui-dialog-buttonset button', function() {
  //    window.location.reload();
  //  });

  $('#formAddTipoEntidade').submit(function() {
    if($('#chv_tp_entidade').val() == '') {
      Entidade.incluirTipoEntidade($('#ds_tipo_pessoa').val());
    } else {
      $.ajax({
        type: 'post',
        data: {
          chv_tp_pessoa : $('#chv_tp_entidade').val(),
          ds_tipo_pessoa: $('#ds_tipo_pessoa').val()
        },
        url:baseUrl+'/entidade/editartipo/',
        dataType: 'json',
        success: function(response){
          window.location.reload();
        }
      });
    }
    return false;
  });
});