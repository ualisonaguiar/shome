$(function () {
  $('body').on('click','#recuperarSenha',function() {
    $('#modal_recuperar_senha').modal('show');
    $('#cpf_usuario').val('');
  }).on('click','#recuperar', function() {
    if($('#cpf_usuario').val() == '') {
      Mensagem.alerta('Informe o seu CPF no campo indicado.');
      $('#cpf_usuario').focus();
      return false;
    }

    $.ajax({
      type: 'post',
      data: {
        cpf_usuario: $('#cpf_usuario').val()
      },
      url: baseUrl + '/seguranca/recuperarsenha',
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
        Mensagem.sucesso(response.mensagem);
        $('#modal_recuperar_senha').modal('hide');
      }
    });
  });
});