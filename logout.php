<?php
require __DIR__ . '/config.php';
unset($_SESSION['logado']);
session_regenerate_id(true);
flash('Você saiu da sua conta.');
header('Location: index.php');
exit;
