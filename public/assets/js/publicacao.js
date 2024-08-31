const btnLike = document.querySelector('#like')

document.querySelector('#like').addEventListener('click', function () {
    if (btnLike.innerHTML.includes('❤️')) {
        var likeStatus = true
    } else {
        var likeStatus = false
    }

    $.ajax({
        url: '../acoes.php',
        method: 'post',
        data: {
            operacao: 'curtir-publi', 
            id_publi: idPubli,
        }
    }).done(function (resposta) {
        if (resposta != "login") {
            if (!likeStatus) {
                btnLike.innerHTML = `<p>❤️${resposta}</p>`
            } else {
                btnLike.innerHTML = `<p>🤍${resposta}</p>`
            }
            console.log(resposta)
        } else {
            window.location.href = `../login/?redirect=publicacao/?id=${idPubli}`;
        }
    })
})
