const feed = document.querySelector('#feed'); // div onde será listado os post
const textos = document.querySelectorAll('.texto');
const themeBackgrounds = document.querySelectorAll('.theme-background');
const btnMaisCom = document.querySelector('#mais-com');
const sppinerLoad = document.querySelector('#sppiner')


maxCom = 1
paginacao = 1

var noExistPost = false; 


if (noExistPost === false) {
    console.log(noExistPost)
    document.addEventListener('scrollend', function () {
        btnMaisCom.style.display = 'none';
        sppinerLoad.style.opacity = 1

        loadPosts(paginacao, maxCom)

    })
}

document.addEventListener('DOMContentLoaded', loadPosts(paginacao, 4))

btnMaisCom.addEventListener('click', function () {
    btnMaisCom.style.display = 'none';
    sppinerLoad.style.opacity = 1;

    loadPosts(paginacao, maxCom)
    
})

// Consultar e paginar publicações
function loadPosts(page, maxCom) {

    $.ajax({
        url: '../acoes.php',
        method: 'post',
        data: {
            operacao: 'carregar-posts',
            page: page,
            max_com: maxCom
        }
    }).done(function (response) {
        if(noExistPost === false){
            try {
                var jsonResponse = JSON.parse(response) // convertendo resposta para json
                console.log(page)

                let i = 0;

                if (jsonResponse.sucess && jsonResponse.dados !== 'vazio') {
                    while (i < maxCom) {
                        var titulo = jsonResponse.dados[i].titulo != '' ? jsonResponse.dados[i].titulo : 'Sem titulo'
                        var descricao = jsonResponse.dados[i].descricao
                        var id = jsonResponse.dados[i].id
                        var data_publi = jsonResponse.dados[i].data_publi

                        feed.innerHTML += `
                        <div class='publi texto theme-background'>
                            <span class="data-publi">${data_publi}</span>
                            <h1 class='titulo-publi'>${titulo}</h1>
                            <p class='subtitulo'>${descricao}</p>
                            <a href='../publicacao?id=${id}' class='ver-mais texto'>Veja mais</a> 
                        </div>`


                        i++
                    }

                    loadTheme() // chamando função de ../assets/js/header.js

                    paginacao++;
                    btnMaisCom.style.display = 'block';
                    sppinerLoad.style.opacity = 0;

                } else{
                    btnMaisCom.style.display = 'none';
                    sppinerLoad.style.opacity = 0;
                    loadTheme()
                    noExistPost = true;
                }
            } catch (erro) {
                console.log('Erro: ' + erro)
            }
        } else {
            btnMaisCom.style.display = 'none';
            sppinerLoad.style.display = 'none';
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error('Error: ' + textStatus, errorThrown)
    })
}