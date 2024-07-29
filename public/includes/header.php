<!--Importar 'header.js' em assets/js-->
<!--Iportar 'header.css' em assets/css-->

<?php
    // Função para verificar se a requisição é AJAX
    function is_ajax_request() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    // Se a requisição for AJAX, processamos e respondemos com JSON
    if (is_ajax_request() && isset($_POST['theme'])) {
        $theme = $_POST['theme'] === 'dark' ? 'true' : 'false';
        setcookie('dark-theme', $theme, time() + 60 * 60 * 24 * 30, "/");
        echo json_encode(['theme' => $theme === 'true' ? 'dark' : 'light']);
        exit;
    }

    // Inicializa o cookie se não estiver definido
    if (!isset($_COOKIE['dark-theme'])) {
        setcookie('dark-theme', 'true', time() + 60 * 60 * 24 * 30, "/");
    }
?>

<header>
    <div class="logo"></div>
    <div class="menu-bar">
        <div class="menu-t"></div>
        <div class="menu-t"></div>
        <div class="menu-t"></div>
    </div>
</header>
<div class="options-menu-open">
    <?php 
        if(isset($_SESSION['login-admin']) && $_SESSION['login-admin'] === true){ ?>
            <a href="../adm/editor-admin.php" class="option">
                Painel Administrador
                
            </a>
    <?php } else if(isset($_SESSION['login']) && $_SESSION['login'] === true){  ?>
            <a href="../account" class="option">
                Minha Conta
                
            </a>
    <?php } else { ?>
            <a href="../login" class="option">
                Login | Cadastrar-se
            </a>
    <?php } ?>

    <a href="../home" class="option">Home</a>
    <a href="" class="option">Explore</a>
    <a href="" class="option">Sobre nós</a>
    <a href="" class="option">Pesquise 🔎</a>

    <div class="option" id="toggle-theme">
        <div class="theme-switcher-area">
            <form action="" method="post" id="form-theme">
                <input type="checkbox" id="theme-switcher" name="theme" value="dark" <?= isset($_COOKIE['dark-theme']) && $_COOKIE['dark-theme'] == 'true' ? 'checked' : '' ?>>
                <label for="theme-switcher" class="theme-switcher-button"></label>
            </form>
        </div>
    </div>
</div>
