<?php
require_once __DIR__ . '/../layout.php';

class CalculadoraEstadistica
{
    private array $datos;

    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    public function promedio(): float
    {
        return array_sum($this->datos) / count($this->datos);
    }

    public function mediana(): float
    {
        $ordenados = $this->datos;
        sort($ordenados);
        $total = count($ordenados);
        $mitad = intdiv($total, 2);

        if ($total % 2 !== 0) {
            return (float) $ordenados[$mitad];
        } else {
            return ($ordenados[$mitad - 1] + $ordenados[$mitad]) / 2.0;
}
    }

    public function moda(): array
    {
        $conteo = [];
        foreach ($this->datos as $n) {
            $clave = (string)$n;
            $conteo[$clave] = ($conteo[$clave] ?? 0) + 1;
        }

        $maxFrecuencia = max($conteo);
        if ($maxFrecuencia <= 1) return [];

        return array_keys(
            array_filter($conteo, fn($f) => $f === $maxFrecuencia)
        );
    }

    public function total(): int { return count($this->datos); }
}

$promedio  = null;
$mediana   = null;
$moda      = null;
$total     = 0;
$errores   = [];
$inputRaw  = $_POST['numeros'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partes  = preg_split('/[\s,;]+/', trim($inputRaw), -1, PREG_SPLIT_NO_EMPTY);
    $validos = [];

    foreach ($partes as $p) {
        is_numeric($p)
            ? ($validos[] = (float)$p)
            : ($errores[]  = "Valor inválido: «{$p}» no es un número.");
    }

    if (empty($errores)) {
        if (count($validos) < 2) {
            $errores[] = 'Ingresa al menos 2 números.';
        } else {
            $est       = new CalculadoraEstadistica($validos);
            $promedio  = $est->promedio();
            $mediana   = $est->mediana();
            $moda      = $est->moda();
            $total     = $est->total();
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
                        <?= empty($moda)
                            ? 'Sin moda (valores únicos)'
                            : implode(', ', $moda) ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>