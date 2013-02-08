var Entidade = {};

Entidade.cadastroEndereco = function(form) {
  $.ajax({
    type: 'post',
    data: form,
    url:baseUrl+'/endereco/cadastro/',
    dataType: 'json',
    beforeSend: function(){
      $('#loading').modal('show');
    },
    success: function(response){
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }
      $('#listagemEndereco').removeClass('hide');
      buscaEnderecoEntidade(response.chvEndereco);
    }
  });
}

Entidade.excluirEndereco = function(chv_endereco) {
  $.ajax({
    type: 'post',
    data: {
      chv_endereco : chv_endereco
    },
    url:baseUrl+'/endereco/excluir/',
    dataType: 'json',
    beforeSend: function(){
      $('#loading').modal('show');
    },
    success: function(response){
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }
      $('#loading').modal('hide');
      Mensagem.sucesso('Endereço excluído com sucsso.');
    }
  });
}

Entidade.incluirTipo = function(nom_tipo) {
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
      $('#chv_tp_pessoa').append(
        '<option value="'+response.data.chv_tp_pessoa+'" selected="selected">'+response.data.ds_tipo_pessoa+'</option>'
        );
      Mensagem.sucesso(response.mensagem);
    }
  });
}


$(function(){
  $('#Cadastrar-label').html('<br><div id="listagemEndereco" class="hide"><h2>'+
    'Endereço das entidades</h2>'+
    '<table class="table table-striped">'+
    '<thead><th>Logradouro</th><th>Complemento</th><th>Bairro</th><th>Cidade</th><th>1 Telefone</th><th>2 Telefone</th><th>Ação</th></thead><tbory id="result"></tbory>'
    +'</table></div><br>');

  $('#chv_tp_pessoa-element').append('&nbsp;&nbsp;<a title="Adicionar tipo de entidade" '+
    'class="btn btn-small btn-primary add-tp-entidade" hreaf="#">'+
    '<i class="icon-plus-sign icon-white"></i></a>');


  $('#Cadastrar').html('<i class="icon-plus-sign icon-white"></i>&nbsp;Cadastrar');
  var botaoCadastro = $('#Cadastrar-element ').html();
  $('#Cadastrar').remove();
  $('#btn').append(botaoCadastro);

  $('#addEndereco').html('<i class="icon-ok-sign icon-white"></i>&nbsp;Adicionar endereço');
  var botaoEndereco = $('#addEndereco-element ').html();
  $('#addEndereco').remove();
  $('#btn').append(botaoEndereco);

  $('#chv_pessoa_juridica-label').append($('#btn').html());
  $('#btn').remove();
  $('#chv_pessoa_juridica-label').addClass('pull pull-right')

  if($('#chv_pessoa_juridica').val() != '') {
    if($('#chv_enderecoEntidade').val() != '') {
      $('#listagemEndereco').removeClass('hide');
      $('#listagemEndereco h2').append('<br>'+$('#loading').html());
      buscaEnderecoEntidade($('#chv_enderecoEntidade').val());
    }
    $('#Cadastrar').html('<i class="icon-ok-sign icon-white"></i>Alterar');
  }

  $('body').on('click','#addEndereco', function() {
    $('#resultPesquisaCep').addClass('hide');
    $('#salvarEndereco').addClass('hide');
    $('#dsCep').val('');
    $('#modal_addEndereco').modal('show');
  }).on('click','#addEnd', function(){
    WsLugar.buscaLocalizacao($('#dsCep').val());
  }).on('click','#salvarEndereco', function(){
    Entidade.cadastroEndereco($('#adicionarEndereco').serialize());
    return false;
  }).on('click','.btExcluir', function() {
    Entidade.excluirEndereco($(this).attr('ref'));
    $(this).parent().parent().remove();
  }).on('click','.add-tp-entidade', function() {
    $('#ds_tipo_pessoa').val('');
    $('#modal_add_tp_entidade').modal('show');
  });


  $('#formAddTipoEntidade').submit(function() {
    Entidade.incluirTipo($('#ds_tipo_pessoa').val());
    return false;
  });
});