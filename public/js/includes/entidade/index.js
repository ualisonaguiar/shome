$(function(){

  Entidade.situacao = function(chv_pessoa_juridica, status) {
    $.ajax({
      type: 'post',
      data: {
        chv_pessoa_juridica: chv_pessoa_juridica,
        status: status
      },
      url:'entidade/situacao',
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

  $('body').on('click','#detalhes_entidade', function() {
    var i = $('#chv_pessoa_juridica').index(this);
    var chv_pessoa_juridica = $(this).attr('value');
    Entidade.Detalhes(chv_pessoa_juridica);
  }).on('click','.altera-status', function() {
    var situacao = $(this).attr('value');
    chv_pessoa_juridica = $(this).attr('rel');
    Entidade.situacao(chv_pessoa_juridica, situacao);
  }).on('click','#excluir_entidade', function() {

    if($(':checked').length == 0) {
      Mensagem.information('Selecione alguma entidade a ser excluída');
      return false;
    }

    var url = baseUrl+'/entidade/excluir/chv_entidade/'+$('input:checked').serialize()
    Mensagem.confirmacao("Você deseja excluir?", url);
    
  });

});