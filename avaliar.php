<?php
/*
 * avaliar.php - define o status de um candidato (aprovado / negado).
 *
 * *** PONTO DE ESTUDO DO LABORATÓRIO ***
 * Este endpoint é intencionalmente VULNERÁVEL a CSRF:
 *   - aceita uma requisição POST que muda o estado do servidor;
 *   - NÃO exige nenhum token anti-CSRF (nem valida a origem);
 *   - confia apenas no cookie de sessão do RH.
 * Logo, se o RH estiver logado e abrir uma página maliciosa que
 * dispare este POST, o currículo será aprovado/negado sem a intenção dele.
 *
 * A decisão vem de:
 *   - botão "aprovado"  -> aprovado
 *   - botão "negado"    -> negado
 *   - checkbox "aprovado" marcado (usado pelo csrf_rh.html) -> aprovado
 */

require __DIR__ . '/config.php';
exigirLogin(); // só age se houver uma sessão de RH ativa (a "vítima" do CSRF)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: painel.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if (isset($_POST['negado'])) {
    $novoStatus = 'negado';
} elseif (isset($_POST['aprovado'])) {
    $novoStatus = 'aprovado';
} else {
    $novoStatus = null;
}

$candidatos = carregarCandidatos();

if ($id > 0 && isset($candidatos[$id]) && $novoStatus !== null) {
    $candidatos[$id]['status'] = $novoStatus;
    salvarCandidatos($candidatos);
    flash('Candidato #' . $id . ' marcado como "' . rotuloStatus($novoStatus) . '".');
} else {
    flash('Não foi possível avaliar o candidato.', 'erro');
}

header('Location: painel.php');
exit;
