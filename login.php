<?php
require __DIR__ . '/config.php';
$titulo = 'Acesso RH';
$erro = '';
$usuario = '';

if (usuarioLogado()) {
    header('Location: painel.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    if (isset($usuarios[$usuario]) && password_verify($senha, $usuarios[$usuario]['senha'])) {
        session_regenerate_id(true);
        $_SESSION['logado'] = $usuario;
        flash('Bem-vindo(a), ' . $usuarios[$usuario]['nome'] . '!');
        header('Location: painel.php');
        exit;
    }
    $erro = 'Usuário ou senha inválidos.';
}

require __DIR__ . '/includes/header.php';
?>
<div class="card card-estreito">
    <h2>Acesso restrito - RH</h2>
    <p class="sub">Entre para avaliar os currículos recebidos.</p>
    <?php if ($erro): ?><div class="alerta alerta-erro"><?= e($erro) ?></div><?php endif; ?>
    <form method="post" class="form">
        <label>Usuário
            <input type="text" name="usuario" value="<?= e($usuario) ?>" required autofocus>
        </label>
        <label>Senha
            <input type="password" name="senha" required>
        </label>
        <button type="submit" class="btn btn-bloco">Entrar</button>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
