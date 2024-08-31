/**
 * Código do comentários.js reestruturado
 * Modularizando o código
 * 
 */
let pagination = 1;
let exstComentario = false;

document.addEventListener('DOMContentLoaded', loadCometarios(pagination, 10));

const elements = {
    listCom: document.querySelector('.list-comentario'),
    btnOpenComentarios: document.querySelector('#open-comentarios'),
    comentariosArea: document.querySelector('.comentarios-area'),
    btnComentar: document.querySelector('.btn-comentar'),
    com: document.querySelector('.comentario'),
    listComentarios: document.querySelector('#list-comentario'),
    btnMaisCom: document.querySelector('#mais-com'),
    sppinerLoad: document.querySelector('#sppiner'),
    btnConfirmeExcluir: document.querySelector('#btn-confirme-excluir'),
    btnCancelar: document.querySelector('#btn-cancelar'),
    backgroundTela: document.querySelector('.background-tela-confirme'),
    formComent: document.querySelector('#form-coment')
};

elements.btnMaisCom.addEventListener('click', function () {
    elements.btnMaisCom.style.display = 'none';
    elements.sppinerLoad.style.display = 'block';

    loadCometarios(pagination, 10);
});

elements.btnOpenComentarios.addEventListener('click', toggleComentarios);

elements.formComent.addEventListener('submit', function (event) {
    addComentario(event);
});

/* ----FUNÇÕES DE REQUISIÇÕES COM SERVIDOR E MANIPULAÇÕ DOS DADOS ------*/ 

function addComentario(event) {
    event.preventDefault(); // Impede o envio do formulário tradicional

    var formData = serialize(elements.formComent);
    console.log(formData)

    $.ajax({
        type: 'POST',
        url: '../acoes.php',
        data: formData  
    }).done(function (response) {
        console.log(response.dados)
        // Verifica se a resposta é um JSON válido
        try {
            var jsonResponse = JSON.parse(response);
            console.log(jsonResponse)

            if (jsonResponse.success) {

                // Atualiza a lista de comentários
                var numComElement = document.getElementById('num-com');
                numComElement.innerHTML = Number(numComElement.innerHTML) + 1;

                document.getElementById('comentario').value = ''; // Limpa o campo de comentário
                // window.location.href = '../publicacao/?id=' + idPubli;

            } else if (jsonResponse.action == 'login') {
                window.location.href = '../login/?redirect=publicacao?id=' + idPubli;
            } else{
                console.log(jsonResponse.error)
            }
        } catch (e) {
            console.error("Erro ao analisar a resposta JSON:", e);
            alert("Erro ao enviar o comentário.");
        }
    });
}

/**
 * FUNÇÃO DE CARREGAMENTO DE PREPARAÇÃO DOS COMENTÁRIOS
 */
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

            if (response.status === 'sucess') {
                let i = 0;

                while (i < max_com) {
                    const dados = response.dados[i];
                    
                    var newCom = templateCom(
                       dados.foto_perfil,
                       dados.nome,
                       dados.email,
                       dados.tempo_publi,
                       dados.texto
                    );

                    const comentario = document.createElement('div');
                    comentario.classList.add('comentario');
                    comentario.innerHTML = newCom;

                    if (dados.com_usuario_log) {
                        const formExcluirCom = document.createElement('form');
                        formExcluirCom.classList.add('form-excluir-com');
                        formExcluirCom.innerHTML = `
                                <input type="hidden" name="operacao" value="delete-comentario">
                                <input type="hidden" name="redirecionamento" value="${dados.id_publi}">
                                <input type="hidden" name="id-comentario" value="${dados.id_comentario}">
                            `;
                        const btnExcluir = document.createElement('button');
                        btnExcluir.classList.add('btn-excluir-com', 'button');
                        btnExcluir.type = 'button';
                        btnExcluir.innerText = 'Excluir';

                        formExcluirCom.appendChild(btnExcluir);

                        btnExcluir.addEventListener('click', () => { 
                            ativarConfirmExcluir();

                            elements.btnConfirmeExcluir.addEventListener('click', function () {                
                                var data = serialize(formExcluirCom);
                
                                $.ajax({
                                    url: '../acoes.php',
                                    method: 'post',
                                    data: data  
                                }).done(function (response) {
                                    var jsonResponse = JSON.parse(response);
                
                                    if (jsonResponse.sucess) {
                                        document.querySelector('#num-com').innerHTML = Number(document.querySelector('#num-com').innerHTML) - 1;
                                        comentario.style.display = 'none';
                                        desativarConfirmExcluir();
                                    } else {
                                        alert(jsonResponse.dados);
                                    }
                                });
                            });
                            
                            elements.btnCancelar.addEventListener('click', ativarConfirmExcluir);
                            elements.backgroundTela.addEventListener('click', desativarConfirmExcluir);
                        });
                        
                        const insightsCom = comentario.querySelector('.insights-com');
                        if (insightsCom) {
                            insightsCom.appendChild(formExcluirCom);
                        } else {
                            comentario.appendChild(formExcluirCom);
                        }
                    }

                    elements.listComentarios.appendChild(comentario);
                    i++;
                }

                elements.btnMaisCom.style.display = 'block';
                elements.sppinerLoad.style.display = 'none';

                pagination++;
                exstComentario = true;

            } else if (response.status === 'vazio') { 
                elements.btnMaisCom.style.display = 'none';
                elements.sppinerLoad.style.display = 'none';
                
                if (!exstComentario) {
                    elements.listComentarios.innerHTML = `<p style="text-align:center;font-size:1.2em" class="texto">${response.dados}</p>  <p style="text-align:center; font-size: 1em"> (Seja o primeiro a comentar)</p>`;
                    elements.listComentarios.style.paddingBottom = '50px';
                }
            } else {
                console.log(`STATUS: ${response.status}; => ${response.dados}`);
            }
            
        } catch (e) {
            elements.btnMaisCom.style.display = 'none';
            elements.sppinerLoad.style.display = 'none';            
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error('Error: ' + textStatus, errorThrown);
    });
}


function templateCom(foto_perfil,nome,email,tempo_publi,texto) {
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
        `;
    return comFormatado;
}


/* ----------------------FUNÇÕES DE LAYOUT ---------------------------*/

function toggleComentarios() {
    if (elements.comentariosArea.style.display == 'block') {
        elements.comentariosArea.style.display = 'none';
        elements.btnOpenComentarios.style.backgroundColor = 'transparent';
    } else {
        elements.comentariosArea.style.display = 'block';
        elements.btnOpenComentarios.style.backgroundColor = '#e9e9e9c9';
    }
}

function ativarConfirmExcluir() {
    elements.backgroundTela.style.display = 'flex';
    document.body.classList.add('modal-open');
}

function desativarConfirmExcluir() {
    elements.backgroundTela.style.display = 'none';
    document.body.classList.remove('modal-open');
}

/* --------------------------------------------------------------------- */


//serializar dados sem ajax. Fonte: https://pt.stackoverflow.com/questions/287393/serialize-com-javascript-puro

function serialize(form){if(!form||form.nodeName!=="FORM"){return }var i,j,q=[];for(i=form.elements.length-1;i>=0;i=i-1){if(form.elements[i].name===""){continue}switch(form.elements[i].nodeName){case"INPUT":switch(form.elements[i].type){case"text":case"hidden":case"password":case"button":case"reset":case"submit":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"checkbox":case"radio":if(form.elements[i].checked){q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value))}break;case"file":break}break;case"TEXTAREA":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"SELECT":switch(form.elements[i].type){case"select-one":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break;case"select-multiple":for(j=form.elements[i].options.length-1;j>=0;j=j-1){if(form.elements[i].options[j].selected){q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].options[j].value))}}break}break;case"BUTTON":switch(form.elements[i].type){case"reset":case"submit":case"button":q.push(form.elements[i].name+"="+encodeURIComponent(form.elements[i].value));break}break}}return q.join("&")};
