<?php
require_once __DIR__ . '/../layout.php';

$errores    = [];
$inputA     = $_POST['conjunto_a'] ?? '';
$inputB     = $_POST['conjunto_b'] ?? '';

$conjA        = [];
$conjB        = [];
$union        = [];
$interseccion = [];
$difAB        = [];
$difBA        = [];
$calculado    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tokensA = preg_split('/[\s,;{}]+/', trim($inputA), -1, PREG_SPLIT_NO_EMPTY);
    $tokensB = preg_split('/[\s,;{}]+/', trim($inputB), -1, PREG_SPLIT_NO_EMPTY);

    foreach ($tokensA as $t) {
        if (is_numeric($t)) {
            $conjA[] = (int)$t;
        }
    }

    foreach ($tokensB as $t) {
        if (is_numeric($t)) {
            $conjB[] = (int)$t;
        }
    }

    $conjA = array_values(array_unique($conjA));
    $conjB = array_values(array_unique($conjB));
    sort($conjA);
    sort($conjB);

    if (empty($conjA)) {
        $errores[] = 'El Conjunto A no contiene enteros válidos.';
    }
    if (empty($conjB)) {
        $errores[] = 'El Conjunto B no contiene enteros válidos.';
    }

    if (empty($errores)) {

        foreach ($conjA as $num) {
            $union[] = $num;
        }
        foreach ($conjB as $num) {
            if (!in_array($num, $union)) {
                $union[] = $num;
            }
        }
        sort($union);

        foreach ($conjA as $num) {
            if (in_array($num, $conjB)) {
                $interseccion[] = $num;
            }
        }
        sort($interseccion);

        foreach ($conjA as $num) {
            if (!in_array($num, $conjB)) {
                $difAB[] = $num;
            }
        }
        sort($difAB);

        foreach ($conjB as $num) {
            if (!in_array($num, $conjA)) {
                $difBA[] = $num;
            }
        }
        sort($difBA);

        $calculado = true;
    }
}

function mostrarConjunto(array $arr): string
{
    if (empty($arr)) {
        return '∅';
    }
    return '{ ' . implode(', ', $arr) . ' }';
}

$base = '../';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>04 · Conjuntos</title>
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
            <h2>Operaciones de Conjuntos</h2>
            <span class="badge">App 04</span>
        </div>
        <main>

            <?php foreach ($errores as $e): ?>
                <div class="alerta alerta-error"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>

            <div class="panel">
                <p class="panel-titulo">Ingresa los conjuntos</p>
                <form method="POST" action="">
                    <div class="form-fila">
                        <div class="campo">
                            <label for="conjunto_a">Conjunto A (enteros)</label>
                            <input type="text" id="conjunto_a" name="conjunto_a"
                                placeholder="Ej: 1, 2, 3, 4, 5"
                                value="<?= htmlspecialchars($inputA) ?>">
                        </div>
                        <div class="campo">
                            <label for="conjunto_b">Conjunto B (enteros)</label>
                            <input type="text" id="conjunto_b" name="conjunto_b"
                                placeholder="Ej: 3, 4, 5, 6, 7"
                                value="<?= htmlspecialchars($inputB) ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Calcular operaciones →</button>
                </form>
            </div>

            <?php if ($calculado): ?>
            <div class="resultado">
                <p class="resultado-titulo">Resultados de las operaciones</p>

                <div class="fila-dato">
                    <span class="etiqueta">A</span>
                    <span class="valor-dato"><?= htmlspecialchars(mostrarConjunto($conjA)) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">B</span>
                    <span class="valor-dato"><?= htmlspecialchars(mostrarConjunto($conjB)) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">A ∪ B <span style="font-size:0.68rem;color:var(--suave)">(Unión)</span></span>
                    <span class="valor-dato"><?= htmlspecialchars(mostrarConjunto($union)) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">A ∩ B <span style="font-size:0.68rem;color:var(--suave)">(Intersección)</span></span>
                    <span class="valor-dato"><?= htmlspecialchars(mostrarConjunto($interseccion)) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">A − B <span style="font-size:0.68rem;color:var(--suave)">(Diferencia)</span></span>
                    <span class="valor-dato"><?= htmlspecialchars(mostrarConjunto($difAB)) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">B − A <span style="font-size:0.68rem;color:var(--suave)">(Diferencia)</span></span>
                    <span class="valor-dato"><?= htmlspecialchars(mostrarConjunto($difBA)) ?></span>
                </div>

            </div>
            <?php endif; ?>

        </main>
    </div>
</div>
</body>
</html>