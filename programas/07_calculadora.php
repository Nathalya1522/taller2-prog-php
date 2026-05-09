<?php
require_once __DIR__ . '/../layout.php';

$resultado = null;
$expresion = '';
$errores   = [];
$numA      = $_POST['num_a']    ?? '';
$numB      = $_POST['num_b']    ?? '';
$opSel     = $_POST['operacion'] ?? '';

if (isset($_POST['calcular'])) {

    
    if (!is_numeric($numA) || !is_numeric($numB)) {
        $errores[] = 'Ambos campos deben ser números válidos.';
    } elseif ($opSel == '') {
        $errores[] = 'Selecciona una operación.';
    } else {

        $a = (float)$numA;
        $b = (float)$numB;

    
        if ($opSel == 'suma') {
            $resultado = $a + $b;
            $expresion = "$a + $b";

        } elseif ($opSel == 'resta') {
            $resultado = $a - $b;
            $expresion = "$a − $b";

        } elseif ($opSel == 'multiplicacion') {
            $resultado = $a * $b;
            $expresion = "$a × $b";

        } elseif ($opSel == 'division') {
            if ($b == 0) {
                $errores[] = 'Error: División por cero no está permitida.';
            } else {
                $resultado = $a / $b;
                $expresion = "$a ÷ $b";
            }

        } elseif ($opSel == 'porcentaje') {
            $resultado = ($a * $b) / 100;
            $expresion = "$a% de $b";
        }

        if ($resultado !== null) {
            $resultado = round($resultado, 10);
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
    <title>07 · Calculadora</title>
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
            <h2>Calculadora</h2>
            <span class="badge">App 07</span>
        </div>
        <main>

            <?php foreach ($errores as $e): ?>
                <div class="alerta alerta-error"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>

            <div class="panel">
                <p class="panel-titulo">Operación</p>
                <form method="POST" action="">
                    <div class="form-fila">
                        <div class="campo">
                            <label for="num_a">Número A</label>
                            <input type="number" id="num_a" name="num_a"
                                step="any" placeholder="Ej: 100"
                                value="<?= htmlspecialchars((string)$numA) ?>">
                        </div>
                        <div class="campo">
                            <label for="num_b">Número B</label>
                            <input type="number" id="num_b" name="num_b"
                                step="any" placeholder="Ej: 25"
                                value="<?= htmlspecialchars((string)$numB) ?>">
                        </div>
                    </div>
                    <div class="campo">
                        <label for="operacion">Operación</label>
                        <select id="operacion" name="operacion">
                            <option value="">— Selecciona —</option>
                            <option value="suma"           <?php if ($opSel == 'suma')           { echo 'selected'; } ?>>Suma (+)</option>
                            <option value="resta"          <?php if ($opSel == 'resta')          { echo 'selected'; } ?>>Resta (−)</option>
                            <option value="multiplicacion" <?php if ($opSel == 'multiplicacion') { echo 'selected'; } ?>>Multiplicación (×)</option>
                            <option value="division"       <?php if ($opSel == 'division')       { echo 'selected'; } ?>>División (÷)</option>
                            <option value="porcentaje"     <?php if ($opSel == 'porcentaje')     { echo 'selected'; } ?>>Porcentaje (A% de B)</option>
                        </select>
                    </div>
                    <button type="submit" name="calcular" class="btn btn-primary">= Calcular</button>
                </form>
            </div>

            <?php if ($resultado !== null): ?>
            <div class="resultado">
                <p class="resultado-titulo">Resultado</p>
                <p style="font-size:0.8rem;color:var(--suave);margin-bottom:8px;">
                    <?= htmlspecialchars($expresion) ?>
                </p>
                <div class="resultado-valor"><?= htmlspecialchars((string)$resultado) ?></div>
            </div>
            <?php endif; ?>

        </main>
    </div>
</div>
</body>
</html>