var email = {}
email.Adicionar = function() {
  $('#modalHeader').html('Novo cadastro de configuração de e-mail');
  $('#chv_email').attr('value','');
  $('#modal_manter_email').modal('show');
}

email.Enviar = function() {

  }

email.Editar = function(chv_email) {
  $('#modalHeader').html('Alteração da configuração de e-mail');
  $('#chv_email').val(chv_email);
  $('#modal_manter_email').modal('show');

  email.Informacao(chv_email);
}

email.Salvar = function() {
  }

email.AlterarStatus = function(chv_email, value) {

  }

email.Informacao = function(chv_email) {
  $.ajax({
    type: 'post',
    data: {
      chv_email: chv_email
    },
    url: baseUrl + '/gerenciamento/informacaoemail',
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
      $('#servidor').val(response.dados.servidor);
      $('#usuario').val(response.dados.nome_usuario);
      $('#email').val(response.dados.usuario);
      $('#senha').val(response.dados.senha_email);
    }
  });
}

$(document).ready(function() {
  $('#novaConfig').click(function(){
    email.Adicionar();
  });

  $('#editarEmail').click(function(){
    email.Editar($(this).attr('value'));
  });

  $('#status').click(function(){
    var chv_email = $(this).attr('value');
    var value = $(this).attr('ref');
    var url = baseUrl + '/gerenciamento/statusemail/';

    url += 'chv_email/' + chv_email + '/status/'+value;
    Mensagem.confirmacao('Deseja realmente inativar esta configuração?', url);
  });

  $('#enviarEmail').click(function(){
    email.Enviar($(this).attr('value'));
  });

  $('#formManterConfi').submit(function() {
    $('#modal_manter_email').modal('hide');
    $('#loading').modal('show');
    email.Salvar();
  });

});