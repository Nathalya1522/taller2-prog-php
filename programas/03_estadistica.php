<?php
require_once __DIR__ . '/../layout.php';

$promedio = null;
$mediana  = null;
$moda     = null;
$total    = 0;
$errores  = [];
$inputRaw = $_POST['numeros'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $partes  = preg_split('/[\s,;]+/', trim($inputRaw), -1, PREG_SPLIT_NO_EMPTY);
    $validos = [];

    foreach ($partes as $p) {
        if (is_numeric($p)) {
            $validos[] = (float)$p;
        } else {
            $errores[] = "Valor inválido: «{$p}» no es un número.";
        }
    }

    if (empty($errores)) {
        if (count($validos) < 2) {
            $errores[] = 'Ingresa al menos 2 números.';
        } else {
            $total = count($validos);

            
            $suma     = 0;
            foreach ($validos as $num) {
                $suma = $suma + $num;
            }
            $promedio = $suma / $total;

            
            $ordenados = $validos;
            sort($ordenados);
            $mitad = intdiv($total, 2);

            if ($total % 2 !== 0) {
                
                $mediana = (float) $ordenados[$mitad];
            } else {
                
                $mediana = ($ordenados[$mitad - 1] + $ordenados[$mitad]) / 2.0;
            }

            
            $conteo = [];
            foreach ($validos as $num) {
                $clave = (string)$num;
                if (isset($conteo[$clave])) {
                    $conteo[$clave] = $conteo[$clave] + 1;
                } else {
                    $conteo[$clave] = 1;
                }
            }

            
            $maxFrecuencia = 0;
            foreach ($conteo as $frecuencia) {
                if ($frecuencia > $maxFrecuencia) {
                    $maxFrecuencia = $frecuencia;
                }
            }

           
            $moda = [];
            if ($maxFrecuencia > 1) {
                foreach ($conteo as $numero => $frecuencia) {
                    if ($frecuencia === $maxFrecuencia) {
                        $moda[] = $numero;
                    }
                }
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
    <title>03 · Estadística</title>
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
            <h2>Estadística Básica</h2>
            <span class="badge">App 03</span>
        </div>
        <main>

            <?php foreach ($errores as $e): ?>
                <div class="alerta alerta-error"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>

            <div class="panel">
                <p class="panel-titulo">Serie de números</p>
                <form method="POST" action="">
                    <div class="campo">
                        <label for="numeros">Números reales (separados por comas, espacios o punto y coma)</label>
                        <textarea id="numeros" name="numeros"
                            placeholder="Ej: 4, 7.5, 2, 9, 4, 3.2, 7.5"
                        ><?= htmlspecialchars($inputRaw) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Calcular estadísticas →</button>
                </form>
            </div>

            <?php if ($promedio !== null): ?>
            <div class="resultado">
                <p class="resultado-titulo">Resultados · <?= $total ?> valores</p>
                <div class="fila-dato">
                    <span class="etiqueta">Promedio (Media aritmética)</span>
                    <span class="valor-dato"><?= round($promedio, 6) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">Mediana</span>
                    <span class="valor-dato"><?= round($mediana, 6) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">Moda</span>
                    <span class="valor-dato">
                        <?php if (empty($moda)): ?>
                            Sin moda (valores únicos)
                        <?php else: ?>
                            <?= implode(', ', $moda) ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>

        </main>
    </div>
</div>
</body>
</html>