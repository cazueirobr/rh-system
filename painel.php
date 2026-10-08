<?php
require __DIR__ . '/config.php';
exigirLogin();
$titulo = 'Painel do RH';

$candidatos = carregarCandidatos();
// mais recentes primeiro
krsort($candidatos);

require __DIR__ . '/includes/header.php';
?>
<div class="card">
    <h2>Currículos recebidos</h2>
    <p class="sub">Analise cada candidato e defina o status.</p>

    <?php if ($candidatos === []): ?>
        <p class="vazio">Nenhum currículo recebido ainda.</p>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Candidato</th>
                    <th>Enviado em</th>
                    <th>Currículo</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($candidatos as $c): ?>
                <tr>
                    <td>#<?= (int) $c['id'] ?></td>
                    <td><?= e($c['nome']) ?></td>
                    <td><?= e($c['enviado_em'] ?? '-') ?></td>
                    <td><a href="<?= e($c['arquivo']) ?>" target="_blank" rel="noopener">Abrir PDF</a></td>
                    <td>
                        <span class="status status-<?= e($c['status']) ?>">
                            <?= e(rotuloStatus($c['status'])) ?>
                        </span>
                    </td>
                    <td>
                        <!-- Formulário SEM token anti-CSRF: é o ponto de estudo do laboratório -->
                        <form action="avaliar.php" method="post" class="form-acao">
                            <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                            <button type="submit" name="aprovado" value="1" class="btn btn-mini btn-ok">Aprovar</button>
                            <button type="submit" name="negado" value="1" class="btn btn-mini btn-no">Negar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
