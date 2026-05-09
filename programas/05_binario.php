<?php
require_once __DIR__ . '/../layout.php';

$binario  = null;
$errores  = [];
$inputNum = $_POST['numero'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $numero = trim($inputNum);

    if ($numero == '') {
        $errores[] = 'Escribe un número antes de convertir.';
    } elseif (!is_numeric($numero)) {
        $errores[] = 'Ingresa un número entero válido.';
    } else {

        $numero = (int)$numero;

        if ($numero == 0) {
            $binario = '0';
        } else {

            $esNegativo = false;
            if ($numero < 0) {
                $esNegativo = true;
                $numero     = $numero * -1; 
            }

            $binario = '';

            while ($numero > 0) {
                $residuo = $numero % 2;  
                $binario = $residuo . $binario; 
                $numero  = (int)($numero / 2);  
            }

            if ($esNegativo) {
                $binario = '-' . $binario;
            }
        }
    }
}

$base = '../';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>05 · Conversor Binario</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">

    <aside class="sidebar">
        <div class="sidebar-logo"><h1>PHP · POO</h1><p>Portafolio de ejercicios</p></div>
        <nav>
            <?php foreach ($menu as $clave => $item):
                $url = urlMenu($clave, $base);
                if (esActivo($clave, $base)) {
                    $activo = 'activo';
                } else {
                    $activo = '';
                }
            ?>
            <a href="<?= htmlspecialchars($url) ?>" class="<?= $activo ?>">
                <span class="num"><?= $item['num'] ?></span>
                <?= htmlspecialchars($item['label']) ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="contenido">
        <div class="topbar">
            <h2>Entero a Binario</h2>
            <span class="badge">App 05</span>
        </div>
        <main>

            <?php foreach ($errores as $e): ?>
                <div class="alerta alerta-error"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>

            <div class="panel">
                <p class="panel-titulo">Número a convertir</p>
                <form method="POST" action="">
                    <div class="campo">
                        <label for="numero">Número entero (positivo o negativo)</label>
                        <input type="number" id="numero" name="numero"
                            placeholder="Ej: 42"
                            value="<?= htmlspecialchars($inputNum) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Convertir a binario →</button>
                </form>
            </div>

            <?php if ($binario !== null): ?>
            <div class="resultado">
                <p class="resultado-titulo"><?= htmlspecialchars($inputNum) ?> en sistema binario</p>
                <div class="resultado-valor"><?= htmlspecialchars($binario) ?></div>
            </div>
            <?php endif; ?>

        </main>
    </div>
</div>
</body>
</html>