<?php
require_once __DIR__ . '/../layout.php';

$serie     = [];
$operacion = $_POST['operacion'] ?? '';
$numero    = $_POST['numero']    ?? '';
$errores   = [];
$procesado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = trim($numero);

    if (!ctype_digit($numero) || (int)$numero < 0) {
        $errores[] = 'Ingresa un número entero positivo (mínimo 0).';
    } elseif ($operacion === '') {
        $errores[] = 'Selecciona una operación.';
    } else {
        $n = (int)$numero;

        if ($operacion === 'fibonacci') {

            // serie vacía
            if ($n <= 0) {
                $serie = [];
            } else {
                // Primer y segundo término
                $serie[0] = 0;
                if ($n > 1) {
                    $serie[1] = 1;
                }
                // Calcular los siguientes sumando los dos anteriores
                for ($i = 2; $i < $n; $i++) {
                    $serie[$i] = $serie[$i - 1] + $serie[$i - 2];
                }
            }

        } elseif ($operacion === 'factorial') {

            $acum = 1;
            for ($i = 1; $i <= $n; $i++) {
                $acum   = $acum * $i;
                $serie[] = ['k' => $i, 'valor' => $acum];
            }

        }

        $procesado = true;
    }
}

$base = '../';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>02 · Fibonacci / Factorial</title>
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
            <h2>Fibonacci / Factorial</h2>
            <span class="badge">App 02</span>
        </div>
        <main>

            <?php foreach ($errores as $e): ?>
                <div class="alerta alerta-error"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>

            <div class="panel">
                <p class="panel-titulo">Parámetros</p>
                <form method="POST" action="">
                    <div class="form-fila">
                        <div class="campo">
                            <label for="numero">Número (n)</label>
                            <input type="number" id="numero" name="numero"
                                   min="0" placeholder="Ej: 10"
                                   value="<?= htmlspecialchars($numero) ?>">
                        </div>
                        <div class="campo">
                            <label for="operacion">Operación</label>
                            <select id="operacion" name="operacion">
                                <option value="">— Selecciona —</option>
                                <option value="fibonacci"
                                    <?php if ($operacion === 'fibonacci') { echo 'selected'; } ?>>
                                    Fibonacci
                                </option>
                                <option value="factorial"
                                    <?php if ($operacion === 'factorial') { echo 'selected'; } ?>>
                                    Factorial
                                </option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Calcular →</button>
                </form>
            </div>

            <?php if ($procesado && !empty($serie)): ?>
            <div class="resultado">
                <p class="resultado-titulo">
                    <?php
                    if ($operacion === 'fibonacci') {
                        echo "Fibonacci · primeros {$numero} términos";
                    } else {
                        echo "Factorial · pasos de 1! a {$numero}!";
                    }
                    ?>
                </p>

                <div class="resultado-serie">
                    <?php if ($operacion === 'fibonacci'): ?>

                        <?php foreach ($serie as $idx => $val): ?>
                            <span style="color:var(--suave);font-size:0.7rem;">F(<?= $idx ?>)=</span>
                            <strong><?= $val ?></strong>
                            <?php if ($idx < count($serie) - 1): ?>
                                <span style="color:var(--borde)">›</span>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <?php foreach ($serie as $paso): ?>
                            <span style="color:var(--suave);font-size:0.7rem;"><?= $paso['k'] ?>!=</span>
                            <strong><?= $paso['valor'] ?></strong>
                            <?php if ($paso['k'] < count($serie)): ?>
                                <span style="color:var(--borde)">›</span>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </div>

                <?php if ($operacion === 'factorial' && !empty($serie)): ?>
                <p style="margin-top:16px;font-size:0.82rem;color:var(--suave);">
                    Resultado final de <?= $numero ?>! =
                    <strong style="color:var(--verde);"><?= end($serie)['valor'] ?></strong>
                </p>
                <?php endif; ?>

            </div>

            <?php elseif ($procesado): ?>
                <div class="alerta alerta-info">El número ingresado produce una serie vacía (n=0).</div>
            <?php endif; ?>

        </main>
    </div>
</div>
</body>
</html>
