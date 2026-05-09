<?php
require_once __DIR__ . '/../layout.php';

$acronimo    = null;
$normalizada = '';
$errores     = [];
$frase       = $_POST['frase'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $frase = trim($frase);

    if ($frase === '') {
        $errores[] = 'Escribe una frase antes de convertir.';
    } elseif (strlen($frase) < 3) {
        $errores[] = 'La frase es demasiado corta.';
    } else {
        // 1. Reemplazar guiones por espacios
        $normalizada = str_replace(['-', '_'], ' ', $frase);

        // 2. Quitar signos de puntuacion
        $normalizada = preg_replace('/[^\p{L}\s]/u', '', $normalizada);

        // 3. Quitar espacios al inicio y al final
        $normalizada = trim($normalizada);

        // 4. Dividir en palabras
        $palabras = explode(' ', $normalizada);

        // 5. Recorrer cada palabra y tomar la primera letra
        $acronimo = '';
        foreach ($palabras as $palabra) {
            if ($palabra !== '') {
                $acronimo .= mb_strtoupper(mb_substr($palabra, 0, 1, 'UTF-8'), 'UTF-8');
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
    <title>01 · Acrónimo</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="layout">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <h1>PHP · POO</h1>
            <p>Portafolio de ejercicios</p>
        </div>
        <nav>
            <?php foreach ($menu as $clave => $item):
                $url    = urlMenu($clave, $base);
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
            <h2>Conversor de Acrónimo</h2>
            <span class="badge">App 01</span>
        </div>

        <main>
            <?php foreach ($errores as $e): ?>
                <div class="alerta alerta-error"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>

            <div class="panel">
                <p class="panel-titulo">Ingresar frase</p>
                <form method="POST" action="">
                    <div class="campo">
                        <label for="frase">Nombre o frase completa</label>
                        <input
                            type="text"
                            id="frase"
                            name="frase"
                            placeholder="Ej: Liquid-crystal display"
                            value="<?= htmlspecialchars($frase) ?>"
                        >
                    </div>
                    <button type="submit" class="btn btn-primary">Generar acrónimo →</button>
                </form>
            </div>

            <?php if ($acronimo !== null): ?>
            <div class="resultado">
                <p class="resultado-titulo">Acrónimo generado</p>
                <div class="resultado-valor"><?= htmlspecialchars($acronimo) ?></div>
                <p style="margin-top:12px;font-size:0.78rem;color:var(--suave);">
                    Frase procesada: <em><?= htmlspecialchars($normalizada) ?></em>
                </p>
            </div>
            <?php endif; ?>

        </main>
    </div>

</div>
</body>
</html>
