$(function(){
  var usuario = {};

  /**
     *
     */
  usuario.alteraStatus = function(chv_usuario, status) {
    $.ajax({
      type: 'post',
      data: {
        chv_usuario: chv_usuario,
        status: status
      },
      url:'usuario/situacao',
      dataType: 'json',
      beforeSend: function(){
        $('#loading').modal('show');
      },
      success: function(response){
        $('#loading').modal('hide');
        if(response.status != true) {
          Mensagem.erro(response.mensagem);
          return false;
        }
        Mensagem.sucesso(response.mensagem, true);
      }
    });
  }

  /**
     *
     */
  usuario.excluir = function(chv_usuario) {
    var url = baseUrl + '/usuario/excluir/chv_usuario/' + chv_usuario;
    Mensagem.confirmacao("Deseja excluir este usuário?", url);
  }

  /**
     *
     */
  usuario.reEnviaSenha = function (chv_usuario) {
    $.ajax({
      type: 'post',
      data: {
        chv_usuario: chv_usuario
      },
      url:'usuario/reenviarsenha',
      dataType: 'json',
      beforeSend: function(){
        $('#loading').modal('show');
      },
      success: function(response){
        $('#loading').modal('hide');
        if(response.status != true) {
          Mensagem.erro(response.mensagem);
          return false;
        }
        Mensagem.sucesso(response.mensagem, true);
      }
    });
  }

  $('body').on('click','.altera-status', function() {
    var chv_usuario = $(this).attr('ref');
    usuario.alteraStatus(chv_usuario, $(this).attr('value'));
  }).on('click','#excluir_usuario', function() {
    if($('#chv_usuario:checked').length == 0) {
      Mensagem.alerta('Para excluir algum usuário, selecione pelo menos.');
      return false;
    }
    usuario.excluir($('#chv_usuario:checked').serialize());
  }).on('click','.btRefresh', function() {
    var chv_usuario = $(this).attr('values');
    usuario.reEnviaSenha(chv_usuario);
  });
});