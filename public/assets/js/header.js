const inpTheme = document.getElementById('theme-switcher');
    
inpTheme.addEventListener('change',loadTheme);

function loadTheme() {
    const textos = document.querySelectorAll('.texto');
    const themeBackgrounds = document.querySelectorAll('.theme-background');

    let branco = '#F2FAFC';
    let escuro = '#151515';

    const theme = inpTheme.checked ? 'dark' : 'light';

    const xhr = new XMLHttpRequest();
    xhr.open("POST", window.location.pathname, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    toggleTheme(textos, 'color', escuro, branco, inpTheme.checked);
                    toggleTheme(themeBackgrounds, 'backgroundColor', branco, escuro, inpTheme.checked);
                } catch (error) {
                    console.error("Erro ao fazer o parse do JSON:", error);
                }
            } else {
                console.error("Erro na requisição. Status:", xhr.status);
            }
        }
    };

    xhr.send("theme=" + theme);
}

function toggleTheme(elements, property, colorLight, colorDark, isChecked) {
    elements.forEach(function(elementAtual) {
        if (isChecked) {
            elementAtual.style[property] = colorDark;
        } else {
            elementAtual.style[property] = colorLight;
        }
    });
}


document.addEventListener('DOMContentLoaded', function () {
    const menuBar = document.querySelector('.menu-bar');
    const optionsMenuOpen = document.querySelector('.options-menu-open');
    const options = document.querySelectorAll('.option'); // Seleciona todos os links <a>

    menuBar.addEventListener('click', function() {
        if (menuBar.classList.contains('open')) {
            // Fechar o menu - animação de saída
            options.forEach((option, index) => {
                option.style.animation = `slideOut 0.2s forwards ${index * 0.01}s`; /*atraso ao contrario : (options.length - index)  no ludar do index*/
            });

            menuBar.classList.remove('open');

            // Remover a classe 'open' após a animação
            setTimeout(() => {
                optionsMenuOpen.classList.remove('open');
            },options.length * 100); // Tempo total para a animação de saída
        } else {
            // Abrir o menu - adiciona classe 'open' e animação de entrada
            menuBar.classList.add('open');
            optionsMenuOpen.classList.add('open');

            // Adiciona a animação de entrada com atraso
            options.forEach((option, index) => {
                option.style.animation = `slideIn 0.2s forwards ${index * 0.01}s`;
            });
        }
    });
});

loadTheme();