var Mensagem = {};

/**
 * mensagem de alerta
 */
Mensagem.alerta = function(msg) {
  $('#mensagem-alerta').attr('title', 'Atenção');
  var img = baseUrl + '/public/img/icon/Alarm-Burn-icon.png';
  $('#mensagem-alerta p').html('<img src="'+img+'">');
  $('#mensagem-alerta p').append('&nbsp;&nbsp;<strong>'+msg+'</strong>');

  $( "#mensagem-alerta" ).dialog({
    autoOpen: true,
    height: 200,
    width: 500,
    modal: true,
    buttons: {
      "OK": function() {
        $( this ).dialog( "close" );
        return true;
      }
    }
  });
}
/**
 * mensagem de sucesso
 */
Mensagem.sucesso = function(msg, reload) {
  if(reload == null || reload == '') {
    reload = false;
  }

  $('#mensagem-alerta').attr('title', 'Transação realizada com suscesso');
  var img = baseUrl + '/public/img/icon/success-icon.png';
  $('#mensagem-alerta p').html('<img src="'+img+'">');
  $('#mensagem-alerta p').append('&nbsp;&nbsp;<strong>'+msg+'</strong>');

  $( "#mensagem-alerta" ).dialog({
    autoOpen: true,
    height: 200,
    width: 500,
    modal: true,
    buttons: {
      "OK": function() {
        $( this ).dialog( "close" );
        if(reload) {
          setTimeout(function(){
            location.reload();
          },1000);
        }
      }
    }
  });
}

/**
 * mensagem de erro
 */
Mensagem.erro = function(msg) {
  $('#mensagem-alerta').attr('title', 'Erro na transação');
  var img = baseUrl + '/public/img/icon/Close-icon.png';
  $('#mensagem-alerta p').html('<img src="'+img+'">');
  $('#mensagem-alerta p').append('&nbsp;&nbsp;<strong>'+msg+'</strong>');

  $('#mensagem-alerta p').html('<img src="'+img+'">');
  $('#mensagem-alerta p').append('&nbsp;&nbsp;<strong>'+msg+'</strong>');

  $( "#mensagem-alerta" ).dialog({
    autoOpen: true,
    height: 200,
    width: 500,
    modal: true,
    buttons: {
      "OK": function() {
        $( this ).dialog( "close" );
        return true;
      }
    }
  });
}

/**
 * mensagem de erro
 */
Mensagem.confirmacao = function(msg, url) {
  $('#mensagem-alerta').attr('title', 'Confirmação');
  var img = baseUrl + '/public/img/icon/Sign-Info-icon.png';
  $('#mensagem-alerta p').html('<img src="'+img+'">');
  $('#mensagem-alerta p').append('&nbsp;&nbsp;<strong>'+msg+'</strong>');

  $('#mensagem-alerta p').html('<img src="'+img+'">');
  $('#mensagem-alerta p').append('&nbsp;&nbsp;<strong>'+msg+'</strong>');

  $( "#mensagem-alerta" ).dialog({
    autoOpen: true,
    height: 200,
    width: 500,
    modal: true,
    buttons: {
      "Ok": function() {
        $( this ).dialog( "close" );
        window.location = url;
      },
      Cancel: function() {
        $( this ).dialog( "close" );
      }
    }
  });
}

/**
 * mensagem de informação
 */
Mensagem.information = function(msg) {
  $('#mensagem-alerta').attr('title', 'Informação');
  var img = baseUrl + '/public/img/icon/Sign-Info-icon.png';
  $('#mensagem-alerta p').html('<img src="'+img+'">');
  $('#mensagem-alerta p').append('&nbsp;&nbsp;<strong>'+msg+'</strong>');

  $( "#mensagem-alerta" ).dialog({
    autoOpen: true,
    height: 200,
    width: 500,
    modal: true,
    buttons: {
      "OK": function() {
        $( this ).dialog( "close" );
        return true;
      }
    }
  });
}

var Entidade = {};

Entidade.Detalhes = function(chv_pessoa_juridica) {
  $.ajax({
    type: 'post',
    data: {
      chv_pessoa_juridica: chv_pessoa_juridica
    },
    url: baseUrl + '/entidade/detalhes',
    dataType: 'json',
    success: function(response){
      $('#modal_detalhesEntidade').modal('show');
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }

      $('#entidadeFantasia').html(response.data.nom_fantasia);
      $('#entidadeRazao').html(response.data.nom_pessoa);
      $('#entidadeCNPJ').html(response.data.cnpj_pessoa_juridica);
      $('#entidadeSite').html(response.data.ds_site);

      var Endereco = response.data.endereco;
      var tr = '';
      $.each(Endereco, function(i, item) {
        tr += '<tr>';
        tr += '<th>' + item.ds_logradouro +" " + item.ds_complemento +", " + item.ds_bairro;
        tr += ' - CEP: ' + item.ds_cep + '</th>';

        tr += '<th>' + item.ds_telefone1 + '</th>';
        tr += '<th>' + item.ds_telefone2 + '</th>';

        tr += '</tr>';
      });
      $('.entidadeEndereco').html(tr);
    }
  });
}

var WsLugar = {};

WsLugar.buscaLocalizacao = function(cep) {
  $.ajax({
    type: 'post',
    data: {
      cep: cep
    },
    url:baseUrl+'/wslugar/buscacep',
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
      $('#resultPesquisaCep').removeClass('hide');
      $('#salvarEndereco').removeClass('hide');

      $('#tipo_logradouro').val(response.data.tipo_logradouro);
      $('#logradouro').val(response.data.logradouro);
      $('#bairro').val(response.data.bairro);
      $('#cidade label').html(response.data.cidade+'/'+response.data.uf);
      $('#chv_cidade').val(response.data.chv_cidade);
    }
  });
}

function buscaEnderecoEntidade(chvEndereco) {
  $.ajax({
    type: 'post',
    data: {
      chv_endereco: chvEndereco
    },
    url:baseUrl+'/endereco/buscaendereco/',
    dataType: 'json',
    success: function(response){
      if(response.status != true) {
        Mensagem.erro(response.mensagem);
        return false;
      }

      var Listagem = response.dados;
      var tr = '';

      if(chvEndereco.search(',') != -1) {
        achvEndereco = chvEndereco.split(",");
        $.each(Listagem, function(i, item) {

          tr += '<tr><input type="hidden" name="chv_endereco[]" value='+achvEndereco[i]+'>';
          tr += '<th>' + item.ds_logradouro  +'</th>';
          tr += '<th>' + item.ds_complemento +'</th>';
          tr += '<th>' + item.ds_bairro +'</th>';
          tr += '<th>' + item.nom_cidade+'/'+item.nom_estado+'</th>';
          tr += '<th><input type="text" class="fone input-medium" name="telefone[]" value="'+item.ds_telefone1+'"></th>';
          tr += '<th><input type="text" class="fone input-medium" name="outro[]" value="'+item.ds_telefone2+'"></th>';
          tr += '<th>'+
          '<i title="Editar" class="btEditar"></i>'+
          '<i title="Excluir" class="btExcluir" ref='+achvEndereco[i]+'></i>'+
          '</th>';
          tr += '</tr>';
        });
      } else {
        $.each(Listagem, function(i, item) {
          tr += '<tr><input type="hidden" name="chv_endereco[]" value='+chvEndereco+'>';
          tr += '<th>' + item.ds_logradouro  +'</th>';
          tr += '<th>' + item.ds_complemento +'</th>';
          tr += '<th>' + item.ds_bairro +'</th>';
          tr += '<th>' + item.nom_cidade+'/'+item.nom_estado+'</th>';
          tr += '<th><input type="tel" class="fone input-medium" name="telefone[]" value="'+item.ds_telefone1+'"></th>';
          tr += '<th><input type="tel" class="fone input-medium" name="outro[]" value="'+item.ds_telefone2+'"></th>';
          tr += '<th>'+
          '<i title="Editar" class="btEditar"></i>'+
          '<i title="Excluir" class="btExcluir" ref='+chvEndereco+'></i>'+
          '</th>';
          tr += '</tr>';
        });
      }

      $('table:first').append(tr);
      $('#loading').modal('hide');
      $('#modal_addEndereco').modal('hide');
      $('.fone').mask("(99) 9999-9999");
      $('#listagemEndereco h2').children('p').remove();
    }
  });
}

function verificaNumero(e) {
  if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    return false;
  }
}

$(document).ready(function() {
  $('.cpf').mask('999.999.999-99');
  $('.cnpj').mask("99.999.999/9999-99");
  $('.cep').mask("99999-999");
  $('.fone').mask("(99) 9999-9999");

  $('.data').datepicker({
    numberOfMonths: 3,
    showButtonPanel: true,
    showOn: "button",
    buttonImage: baseUrl + "/public/img/calendar.gif",
    buttonImageOnly: true
  });
  $('.data').datepicker( $.datepicker.regional[ "pt-BR" ] );
  $('.data').mask("99/99/9999");
  $('.numero').keypress(verificaNumero);

  $('.moeda').priceFormat({
    prefix: 'R$ ',
    centsSeparator: ',',
    thousandsSeparator: '.'
  });

  $('body').on('change','.cpf',function() {
    }).on('click','#alterar_senha_usuario', function() {
    $('#modal_alterar_senha').modal('show');
  }).on('click','.alterar-senha-usuario', function() {
    if($('#ds_senha').val() == '') {
      Mensagem.alerta('Informe a sua senha atual');
      $('#ds_senha').focus();
      return false;
    } else if($('#nova_senha').val() == '') {
      Mensagem.alerta('Informe a nova senha');
      $('#nova_senha').focus();
      return false;
    } else if($('#conf_senha').val() == '') {
      Mensagem.alerta('Informe a confirmação da senha');
      $('#conf_senha').focus();
      return false;
    }

    if($('#nova_senha').val() != $('#conf_senha').val()) {
      Mensagem.alerta('A nova senha esta diferente da confirmação');
      $('#nova_senha').focus();
      return false;
    }

    $.ajax({
      type: 'post',
      data: {
        ds_senha: $('#ds_senha').val(),
        nova_senha: $('#nova_senha').val(),
        conf_senha: $('#conf_senha').val()
      },
      url: baseUrl + '/seguranca/alteracaosenha/',
      dataType: 'json',
      success: function(response){
        if(response.status != true) {
          Mensagem.erro(response.mensagem);
          return false;
        }
        $('#modal_alterar_senha').modal('hide');
        Mensagem.sucesso(response.mensagem);
      }
    });
  }).on('click','#detalhes_entidade', function() {
    var i = $('#chv_pessoa_juridica').index(this);
    var chv_pessoa_juridica = $(this).attr('value');
    Entidade.Detalhes(chv_pessoa_juridica);
  });
});