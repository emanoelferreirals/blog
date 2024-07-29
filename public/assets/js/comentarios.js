const listCom = document.querySelector('.list-comentario')

const btnExcluir = document.querySelectorAll('.btn-excluir-com')
const btnOpenComentarios = document.querySelector('#open-comentarios')
const comentariosArea = document.querySelector('.comentarios-area')
const btnComentar = document.querySelector('.btn-comentar')
const com = document.querySelector('.comentario')

const listComentarios = document.querySelector('#list-comentario');
const btnMaisCom = document.querySelector('#mais-com');
const sppinerLoad = document.querySelector('#sppiner')


const btnConfirmeExcluir = document.querySelector('#btn-confirme-excluir')
const btnCancelar = document.querySelector('#btn-cancelar')
const backgroundTela = document.querySelector('.background-tela-confirme')
    

var pagination = 1
var exstComentario = false

document.addEventListener('DOMContentLoaded', loadCometarios(pagination,10))

    btnMaisCom.addEventListener('click', function () {
        btnMaisCom.style.display = 'none';
        sppinerLoad.style.display = 'block'
        
        loadCometarios(pagination, 10)
    })

btnOpenComentarios.addEventListener('click',()=>{
    if(comentariosArea.style.display == 'block'){
        comentariosArea.style.display = 'none';
        btnOpenComentarios.style.backgroundColor = 'transparent';
    } else {
        comentariosArea.style.display = 'block';
        btnOpenComentarios.style.backgroundColor = '#e9e9e9c9';
    }
})

index_com = 0


    
function ativarConfirmExcluir(){
    backgroundTela.style.display = 'flex';
    document.body.classList.add('modal-open')
}

function desativarConfirmExcluir(){
    backgroundTela.style.display = 'none';
    document.body.classList.remove('modal-open')
}

$(document).ready(function() {
    $('#form-coment').submit(function(event) {
        event.preventDefault(); // Impede o envio do formulário tradicional

        var formData = $(this).serialize(); // Serializa os dados do formulário
     // console.log(formData)

        $.ajax({
            type: 'POST',
            url: '../acoes.php',
            data: formData,
            success: function(response) {
                // Verifica se a resposta é um JSON válido
                try {
                    var jsonResponse = JSON.parse(response);
                    
                    if (jsonResponse.success) {
                        // Atualiza a lista de comentários
                        $('#num-com').html(Number($('#num-com').html()) + 1);
                        
                        $('#comentario').val(''); // Limpa o campo de comentário
                        window.location.href = '../publicacao/?id='+idPubli;
                    } else  if(jsonResponse.action == 'login'){
                        window.location.href = '../login/?redirect=publicacao?id='+idPubli
                    }
                } catch (e) {
                 // console.error("Erro ao analisar a resposta JSON:", e);
                    alert("Erro ao enviar o comentário.");
                }
            },
            error: function() {
                alert('Erro ao enviar o comentário.');
            }
        });
    });
});


function loadCometarios(page,max_com) {
    $.ajax({
        url: '../getComentarios.php',
        method: 'GET',
        data: {
            id: idPubli,
            page: page,
            max_com:max_com,
            usuario_id: idUser
        }
    }).done(function (response) {
        try {
         // console.log(response)

            if (response.status === 'sucess') {
                let i = 0

                while (i < max_com) {
                    const dados = response.dados[i];
                    
                    var newCom = templateCom(
                       dados.id_comentario,
                       dados.id_publi,
                       dados.id_usuario,
                       dados.foto_perfil,
                       dados.nome,
                       dados.email,
                       dados.tempo_publi,
                       dados.texto,
                       dados.com_usuario_log,
                    )

                    const comentario = document.createElement('div')
                    comentario.classList.add('comentario')
                    comentario.innerHTML = newCom

                 // console.log(dados.com_usuario_log)

                    if (dados.com_usuario_log) {
                        const formExcluirCom = document.createElement('form')
                        formExcluirCom.classList.add('form-excluir-com')
                        formExcluirCom.innerHTML = `
                                <input type="hidden" name="operacao" value="delete-comentario">
                                <input type="hidden" name="redirecionamento" value="${dados.id_publi}">
                                <input type="hidden" name="id-comentario" value="${dados.id_comentario}">
                            `
                        const btnExcluir = document.createElement('button')
                        btnExcluir.classList.add('btn-excluir-com', 'button')
                        btnExcluir.type = 'button'
                        btnExcluir.innerText = 'Excluir'

                        formExcluirCom.appendChild(btnExcluir)

                        btnExcluir.addEventListener('click', () => { 
                            ativarConfirmExcluir();

                            btnConfirmeExcluir.addEventListener('click', function () {                
                                var data = serialize(formExcluirCom)
                
                                $.ajax({
                                    url: '../acoes.php',
                                    method: 'post',
                                    data: data  
                                }).done(function (response) {
                                    var jsonResponse = JSON.parse(response);
                
                                    if (jsonResponse.sucess) {
                                     // console.log(comentario)
                                        document.querySelector('#num-com').innerHTML = Number(document.querySelector('#num-com').innerHTML) - 1
                                        comentario.style.display = 'none'
                                        //https://developer.mozilla.org/en-US/docs/Web/API/Node/removeChild
                                        // https://scancode.com.br/como-remover-elementos-html-com-javascript/


                                         // Remover o evento para evitar múltiplos anexos
                                         // btnConfirmeExcluir.removeEventListener('click', handleConfirmClick);
                                          desativarConfirmExcluir();
                                    } else {
                                        alert(jsonResponse.dados)
                                    }
                                })
                            })
                            
                            btnCancelar.addEventListener('click', ativarConfirmExcluir)
                            backgroundTela.addEventListener('click', desativarConfirmExcluir)
                        })
                     // console.log(formExcluirCom)
                        
                        // Encontrar a div 'insights-com' dentro do novo elemento 'comentario'
                        const insightsCom = comentario.querySelector('.insights-com');
                        if (insightsCom) {
                            insightsCom.appendChild(formExcluirCom);
                        } else {
                            // Se não encontrar 'insights-com', adicionar diretamente ao comentário
                            comentario.appendChild(formExcluirCom);
                        }
                    }


                    //console.log(newCom)
                    listComentarios.appendChild(comentario)
                    i++;
                }

                btnMaisCom.style.display = 'block'
                sppinerLoad.style.display = 'none'

                pagination++
                exstComentario = true

            } else if (response.status === 'vazio') { 
                btnMaisCom.style.display = 'none'
                sppinerLoad.style.display = 'none'

                //console.log(response.status + "   "+ exstComentario)
                
                if (!exstComentario) {
                 // console.log("porque não funciona??")
                    listComentarios.innerHTML = `<p style="text-align:center;font-size:1.2em" class="texto">${response.dados}</p>  <p style="text-align:center; font-size: 1em"> (Seja o primeiro a comentar)</p>`;
                    listComentarios.style.paddingBottom = '50px';
                }
            } else {
             // console.log(response.status)
            }
            
        } catch (e) {
            btnMaisCom.style.display = 'none'
            sppinerLoad.style.display = 'none'
            
            //console.error('Failed to parse JSON response:', response);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
     // console.error('Error: ' + textStatus, errorThrown);
    });
}


function templateCom(id_comentario,id_publi,id_usuario,foto_perfil,nome,email,tempo_publi,texto,com_usuario_log) {
    comFormatado = `
    <div class="usuario-com">
            <div class="foto-perfil" style="background-image: url(${foto_perfil})"></div>
            
            <div>
                <p class="email-usuario-com">
                ${nome} <span style="color:  blue;">(${email})</span>
                </p>
                <p class="tempo-publicacao">
                    ${tempo_publi}
                    </p>
                    </div>
                    </div>
                    
                    <p class="texto-comentario">
            ${texto}
        </p>
        <div class="insights-com">
        <p>🤍 0</p>
        <p>🗨️ 0</p>
        `
    return comFormatado
}

//serializar dados sem ajax OBS: não entendo este código. Fonte: https://pt.stackoverflow.com/questions/287393/serialize-com-javascript-puro

function serialize(form){if(!form||form.nodeName!=="FORM"){return }var i,j,q=[];for(i=form.elements.length-1;i>=0;i=i-1){if(form.elements[i].name===""){continue}switch(form.elements[i].nodeName){case"INPUT":switch(form.elements[i].type){case"text":case"hidden":case"password":case"button":case"reset":case"submit":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"checkbox":case"radio":if(form.elements[i].checked){q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value))}break;case"file":break}break;case"TEXTAREA":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"SELECT":switch(form.elements[i].type){case"select-one":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"select-multiple":for(j=form.elements[i].options.length-1;j>=0;j=j-1){if(form.elements[i].options[j].selected){q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].options[j].value))}}break}break;case"BUTTON":switch(form.elements[i].type){case"reset":case"submit":case"button":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break}break}}return q.join("&")};
