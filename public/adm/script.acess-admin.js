const textarea = document.getElementById("conteudo");
const botaoNegrito = document.getElementById('negrito');
const botaoItalico = document.getElementById('italico');
const botaoSublinhar = document.getElementById('sublinhar');
const colorFont = document.getElementById('color-font');
const previl = document.getElementById('previl');
const divListPubli = document.getElementById('list-posts');
const titulos = document.querySelector('#titulos');
const alinharEsquerda = document.getElementById('a-esquerda');
const alinharDireita = document.getElementById('a-direita');
const centralizar = document.getElementById('centralizar');

var selectionSave;

divListPubli.addEventListener('click', () => {
    //console.log(getComputedStyle(divListPubli).left);

    if (getComputedStyle(divListPubli).left != '0px') {
        divListPubli.style.left = '0px';
    } else if (getComputedStyle(divListPubli).left === '0px') {
        divListPubli.style.left = '-390px';
    }
});


function verificarEnter(event) {
    if (event.key === "Enter") {
      //alert("Você pressionou a tecla Enter!");
        textarea.value = textarea.value + "<br>";
        event.preventDefault();
    }
  }

  textarea.addEventListener('input',function() {
      previl.innerHTML = textarea.value
  });

    previl.addEventListener('click', function () {
        previl.contentEditable = true
    });

    previl.addEventListener('input', function () {
        textarea.value = previl.innerHTML
    });


    botaoNegrito.addEventListener('click', function () {
        saveSelection();  
        var startTag = '<strong>';
        var endTag = '</strong>';
        
            formatText(startTag, endTag, selectionSave);
    });
    
    botaoItalico.addEventListener('click', function() {
        saveSelection();  
        var startTag = '<em>';
        var endTag = '</em>';
        
            formatText(startTag, endTag, selectionSave);
    });

    botaoSublinhar.addEventListener('click', function () {
        saveSelection();  
        var startTag = '<span style="text-decoration:underline">';
        var endTag = '</span>';
        
            formatText(startTag, endTag, selectionSave);
    });

    alinharEsquerda.addEventListener('click', function () {
        saveSelection();  
        var startTag = '<div style="text-align:left">';
        var endTag = '</div>';
        
            formatText(startTag, endTag, selectionSave);
    });

    centralizar.addEventListener('click', function () {
        saveSelection();  
        var startTag = '<div style="text-align:center">';
        var endTag = '</div>';
        
            formatText(startTag, endTag, selectionSave);
    });

    alinharDireita.addEventListener('click', function () {
        saveSelection();  
        var startTag = '<div style="text-align:right">';
        var endTag = '</div>';
        
            formatText(startTag, endTag, selectionSave);
    });

    titulos.addEventListener('click', function () {
        saveSelection();
        var selectedOption = this.options[this.selectedIndex];
        var selectedValue = selectedOption.value;

        var startTag = '';
        var endTag = '';

        switch (selectedValue) {
            case 'h1': startTag = '<h1>'; endTag = '</h1>';       
                break;
            case 'h2': startTag = '<h2>'; endTag = '</h2>';       
                break;
            case 'h3': startTag = '<h3>'; endTag = '</h3>';     
                break;
            case 'h4': startTag = '<h4>'; endTag = '</h4>';     
                break;
            case 'h5': startTag = '<h5>'; endTag = '</h5>';      
                break;
            case 'h6': startTag = '<h6>'; endTag = '</h6>';      
                break;
        }

        this.selectedIndex = 0;
        
            formatText(startTag, endTag, selectionSave);

    });

    colorFont.addEventListener('click', function() {
        saveSelection();      
        var color = colorFont.value
            
            var startTag = `<span style="color:${color}">`;
            var endTag = '</span>';
            
            if (selectionSave) {
                formatText(startTag, endTag, selectionSave);
            }
    });

    function formatText(startTag, endTag,selection) {
        console.log(selection)  
        //selection.selecaoText.substring(selection.posInicial - startTag.length, selection.posInicial) === startTag &&selection.selecaoText.substring(selection.posFinal, selection.posFinal + endTag.length) === endTag

        if (selection!=undefined && startTag!='' && endTag!='') {
            textarea.value = '';
            textarea.value = `${selection.textoAntes}${startTag}${selection.selecaoText}${endTag}${selection.textoDepois}`;
        } else if(startTag!='' && endTag!='' && selection==undefined){
            textarea.value += startTag + endTag;
        }
        selectionSave = undefined;
        console.log(selection)
    }
        
    function isFormatApplied(command) {
        return document.queryCommandState(command);
    }
    
    function saveSelection() {
        var selecao = document.getSelection();
        var selecaoText = selecao.toString();        
        
        if (selecaoText !== '') {
            var anchorOffset = selecao.anchorOffset;
            var focusOffset = selecao.focusOffset;

            var ancInicial = previl.innerHTML.indexOf(selecao.anchorNode.textContent) + anchorOffset;
            var ancFinal = previl.innerHTML.indexOf(selecao.focusNode.textContent) + focusOffset;
            
            posInicial = ancInicial < ancFinal ? ancInicial : ancFinal;
            posFinal = ancInicial > ancFinal ? ancInicial : ancFinal;

            var textoAntes = previl.innerHTML.substring(0, posInicial);
            var textoDepois = previl.innerHTML.substring(posFinal);

            selectionSave = {
                selecaoText: selecaoText,
                posInicial: posInicial,
                posFinal: posFinal,
                textoAntes: textoAntes,
                textoDepois: textoDepois
            }
        } else {
            
        }
    }


document.getElementById("list-uploads").addEventListener("change", function() {
    var selectedOption = this.options[this.selectedIndex];
    var selectedValue = selectedOption.value;
    var extensaoArquivo = selectedValue.substring(selectedValue.length - 3)

    console.log(extensaoArquivo)

    switch (extensaoArquivo) {
        case 'pdf': textarea.value += `<a type="application/pdf" href="/projetos-pessoais/blog-pessoal/uploads/${selectedValue}">Veja o pdf aqui</a>`; break;

        case 'jpg' || 'png': textarea.value += `<img src="/projetos-pessoais/blog-pessoal/uploads/${selectedValue}">`; break;

        case 'mp4': textarea.value += `<video src="/projetos-pessoais/blog-pessoal/uploads/${selectedValue}" width="500" controls></video>`; break;
        
        case 'mp3': textarea.value += ` <audio src="/projetos-pessoais/blog-pessoal/uploads/${selectedValue}" controls></audio>`; break;

    }
});

//console.log("Seleção atual", selecionado.toString());
        //console.log("Posição de início:", startPos);
        //console.log("Posição de fim:", endPos);
        //console.log("Nó de início:", startNode);
        //console.log("Nó de fim:", endNode);