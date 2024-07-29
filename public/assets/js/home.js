const publi = document.querySelectorAll('.publi'); // seleciona todos os posts
const paginationContainer = document.querySelector('#pagination'); 

const quantElementsForPage = 5; //quantidadae de elementos por paginação
const quantPaginations = Math.ceil(publi.length / quantElementsForPage);


function createPagination(){
    for(let i = 1; i <= quantPaginations; i++ ){
        const newButton = document.createElement('p'); //criando novo botao
        newButton.id = `button-pagination${i}`; //adicionando id único ao botão de acordo com sua numeração
        newButton.classList.add('button-pagination'); 
        newButton.textContent = i; //adicionando valor de acordo com a numeração da pagina
        newButton.addEventListener('click',()=>{ //adicionando evento no botão
            showPages(i);
        });

        paginationContainer.appendChild(newButton);//adicionando botão ao container
    }   
}

function showPages(pagina){
    let start = (pagina - 1) * quantElementsForPage;
    let end = pagina * quantElementsForPage - 1;
    
    publi.forEach((element)=>{
        element.style.display = 'none';
    })
    
    publi.forEach((element, idx)=>{
        if(idx >= start && idx <=end){
            element.style.display = 'block'
        }
    })
    
    document.querySelectorAll('.button-pagination').forEach((element)=>{
        if(element.textContent == pagina){
            element.style.backgroundColor = "#d4d4d4c9";
        }else{
            element.style.backgroundColor = "#F2FAFC";
        }

    })
}

createPagination();
showPages(1);


