<?php
require_once __DIR__ . '/../layout.php';

class ConvertidorBinario
{
    private int $entero;

    public function __construct(int $entero)
    {
        $this->entero = $entero;
    }

    public function aBinario(): string
    {
        if ($this->entero === 0) return '0';

        $numero = abs($this->entero);
        $bits   = [];

        while ($numero > 0) {
            array_unshift($bits, $numero % 2);
            $numero = intdiv($numero, 2);
        }

        $resultado = implode('', $bits);
        return ($this->entero < 0 ? '-' : '') . $resultado;
    }

    public function getEntero(): int { return $this->entero; }
}


$binario  = null;
$errores  = [];
$inputNum = $_POST['numero'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numLimpio = trim($inputNum);

    if (!preg_match('/^-?\d+$/', $numLimpio)) {
        $errores[] = 'Ingresa un número entero válido (positivo o negativo).';
    } elseif (abs((int)$numLimpio) > PHP_INT_MAX) {
        $errores[] = 'El número es demasiado grande.';
    }

    if (empty($errores)) {
        $conv    = new ConvertidorBinario((int)$numLimpio);
        $binario = $conv->aBinario();
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