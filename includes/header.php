<?php
$logado = usuarioLogado();
$f = flash();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titulo) ? e($titulo) : 'RH Enterprise' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topo">
    <div class="container topo-inner">
        <a href="index.php" class="marca">RH<span>Enterprise</span></a>
        <nav class="nav">
            <?php if ($logado): ?>
                <a href="painel.php">Painel do RH</a>
                <a href="logout.php" class="btn-link">Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn-link">Acesso RH</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container conteudo">
    <?php if ($f): ?>
        <div class="alerta alerta-<?= e($f['tipo']) ?>"><?= e($f['msg']) ?></div>
    <?php endif; ?>
