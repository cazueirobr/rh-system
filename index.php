<?php
require __DIR__ . '/config.php';
$titulo  = 'RH Enterprise - Envie seu currículo';
$erro    = '';
$idGerado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');

    if ($nome === '') {
        $erro = 'Informe o seu nome.';
    } elseif (!isset($_FILES['curriculo']) || $_FILES['curriculo']['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Selecione um arquivo PDF para enviar.';
    } else {
        $arquivo = $_FILES['curriculo'];

        // ---- Validação do PDF no BACKEND (não confiar no navegador) ----
        $ext    = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $finfo  = new finfo(FILEINFO_MIME_TYPE);
        $mime   = $finfo->file($arquivo['tmp_name']);
        $inicio = (string) file_get_contents($arquivo['tmp_name'], false, null, 0, 5);
        $maxBytes = 5 * 1024 * 1024; // 5 MB

        if ($arquivo['size'] <= 0 || $arquivo['size'] > $maxBytes) {
            $erro = 'O arquivo deve ter entre 1 byte e 5 MB.';
        } elseif ($ext !== 'pdf') {
            $erro = 'Somente arquivos .pdf são aceitos.';
        } elseif ($mime !== 'application/pdf') {
            $erro = 'O conteúdo do arquivo não é um PDF válido.';
        } elseif (strncmp($inicio, '%PDF-', 5) !== 0) {
            $erro = 'O arquivo não começa com a assinatura de um PDF (%PDF-).';
        } else {
            // ---- Arquivo válido: registra o candidato ----
            $candidatos = carregarCandidatos();
            $id = proximoId($candidatos);

            $nomeArquivo = 'cv_' . $id . '.pdf';
            $destino     = $DIR_UPLOADS . '/' . $nomeArquivo;

            if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
                $erro = 'Não foi possível salvar o arquivo. Tente novamente.';
            } else {
                $candidatos[$id] = [
                    'id'         => $id,
                    'nome'       => $nome,
                    'arquivo'    => 'uploads/' . $nomeArquivo,
                    'status'     => 'neutro',
                    'enviado_em' => date('d/m/Y H:i'),
                ];
                salvarCandidatos($candidatos);
                $idGerado = $id;
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <h1>Faça parte do nosso time</h1>
    <p class="sub">Envie o seu currículo em PDF e participe do nosso processo seletivo.</p>
</section>

<?php if ($idGerado !== null): ?>
    <div class="card card-sucesso">
        <h2>🎉 Parabéns, seu currículo foi enviado!</h2>
        <p>Você é o candidato número <strong>#<?= (int) $idGerado ?></strong> no sistema.</p>
        <p class="sub">Guarde esse número para acompanhar o seu processo.</p>
        <a href="index.php" class="btn">Enviar outro currículo</a>
    </div>
<?php else: ?>
    <div class="card">
        <h2>Envie seu currículo</h2>
        <?php if ($erro): ?><div class="alerta alerta-erro"><?= e($erro) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="form">
            <label>Seu nome completo
                <input type="text" name="nome" required placeholder="Ex.: Maria Silva">
            </label>
            <label>Currículo (somente PDF)
                <input type="file" name="curriculo" accept="application/pdf" required>
            </label>
            <button type="submit" class="btn btn-bloco">Enviar currículo</button>
        </form>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
