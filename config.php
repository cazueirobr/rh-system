<?php
/*
 * RH Enterprise - laboratório de estudo de CSRF (ambiente controlado)
 *
 * Estado compartilhado em arquivos JSON (NÃO na sessão), para que uma
 * alteração valha para qualquer navegador/host - condição necessária
 * para que um ataque CSRF seja observável de fora da sessão da vítima.
 */

session_start();

// ------------------------------------------------------------------
// Caminhos dos "bancos" (arquivos) e uploads
// ------------------------------------------------------------------
$ARQ_USUARIOS   = __DIR__ . '/usuarios.json';
$ARQ_CANDIDATOS = __DIR__ . '/candidatos.json';
$DIR_UPLOADS    = __DIR__ . '/uploads';

// Usuário do RH (criado automaticamente na primeira execução)
$usuariosPadrao = [
    'rh' => [
        'nome'  => 'Recursos Humanos',
        'senha' => password_hash('rhsystem', PASSWORD_DEFAULT),
    ],
];

// ------------------------------------------------------------------
// Usuários
// ------------------------------------------------------------------
function carregarUsuarios(): array
{
    global $ARQ_USUARIOS, $usuariosPadrao;
    if (is_file($ARQ_USUARIOS)) {
        $dados = json_decode((string) file_get_contents($ARQ_USUARIOS), true);
        if (is_array($dados) && $dados !== []) {
            return $dados;
        }
    }
    return $usuariosPadrao;
}

function salvarUsuarios(array $usuarios): void
{
    global $ARQ_USUARIOS;
    file_put_contents($ARQ_USUARIOS, json_encode($usuarios, JSON_PRETTY_PRINT), LOCK_EX);
}

// ------------------------------------------------------------------
// Candidatos (currículos enviados)
// ------------------------------------------------------------------
function carregarCandidatos(): array
{
    global $ARQ_CANDIDATOS;
    if (is_file($ARQ_CANDIDATOS)) {
        $dados = json_decode((string) file_get_contents($ARQ_CANDIDATOS), true);
        if (is_array($dados)) {
            return $dados;
        }
    }
    return [];
}

function salvarCandidatos(array $candidatos): void
{
    global $ARQ_CANDIDATOS;
    file_put_contents($ARQ_CANDIDATOS, json_encode($candidatos, JSON_PRETTY_PRINT), LOCK_EX);
}

function proximoId(array $candidatos): int
{
    if ($candidatos === []) {
        return 1;
    }
    return max(array_map('intval', array_keys($candidatos))) + 1;
}

// ------------------------------------------------------------------
// Helpers gerais
// ------------------------------------------------------------------
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function usuarioLogado(): ?string
{
    return $_SESSION['logado'] ?? null;
}

function exigirLogin(): void
{
    if (usuarioLogado() === null) {
        header('Location: login.php');
        exit;
    }
}

function flash(?string $msg = null, string $tipo = 'sucesso'): ?array
{
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'tipo' => $tipo];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function rotuloStatus(string $status): string
{
    return [
        'aprovado' => 'Aprovado',
        'negado'   => 'Negado',
        'neutro'   => 'Em análise',
    ][$status] ?? 'Em análise';
}

$usuarios = carregarUsuarios();
