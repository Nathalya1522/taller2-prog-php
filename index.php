<?php
require_once __DIR__ . '/layout.php';
$base = '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicaciones PHP · POO</title>
    <link rel="stylesheet" href="css/style.css">
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
                $activo = esActivo($clave, $base) ? 'activo' : '';
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
            <h2>Panel Principal</h2>
            <span class="badge">7 Aplicaciones</span>
        </div>

        <main>
            <div class="card-grid">
                <?php
                $descripciones = [
                    'programas/01_acronimo'   => 'Convierte frases largas en su acrónimo. Soporta guiones y signos de puntuación.',
                    'programas/02_fibonacci'  => 'Genera la serie de Fibonacci o los pasos del factorial de un número.',
                    'programas/03_estadistica'=> 'Calcula promedio, mediana y moda de una serie de números reales.',
                    'programas/04_conjuntos'  => 'Unión, intersección, A−B y B−A dados dos conjuntos de enteros.',
                    'programas/05_binario'    => 'Convierte un entero a binario mostrando el proceso de divisiones.',
                    'programas/06_arbol'      => 'Reconstruye un árbol binario desde preorden, inorden o postorden.',
                    'programas/07_calculadora'=> 'Calculadora con suma, resta, ×, ÷ y porcentaje. Guarda historial.',
                ];

                foreach ($menu as $clave => $item):
                    if ($clave === '') continue;
                    $url  = urlMenu($clave, $base);
                    $desc = $descripciones[$clave] ?? '';
                ?>
                <a href="<?= htmlspecialchars($url) ?>" class="card-app">
                    <div class="icono"><?= $item['num'] ?></div>
                    <h3><?= htmlspecialchars($item['label']) ?></h3>
                    <p><?= htmlspecialchars($desc) ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

</div>
</body>
</html>
