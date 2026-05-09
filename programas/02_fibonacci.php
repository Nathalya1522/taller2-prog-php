<?php
require_once __DIR__ . '/../layout.php';


abstract class Secuencia
{
    protected int $limite;

    public function __construct(int $limite)
    {
        $this->limite = $limite;
    }

    abstract public function calcular(): array;

    public function getLimite(): int { return $this->limite; }
}

class SecuenciaFibonacci extends Secuencia
{
    public function calcular(): array
    {
        if ($this->limite <= 0) return [];

        $serie = array_fill(0, max($this->limite, 2), 0);
        $serie[0] = 0;
        if ($this->limite > 1) $serie[1] = 1;

        for ($i = 2; $i < $this->limite; $i++) {
            $serie[$i] = $serie[$i - 1] + $serie[$i - 2];
        }

        return array_slice($serie, 0, $this->limite);
    }
}

class SecuenciaFactorial extends Secuencia
{
    public function calcular(): array
    {
        $pasos = [];
        $acum  = 1;

        foreach (range(1, max($this->limite, 1)) as $i) {
            $acum    *= $i;
            $pasos[]  = ['k' => $i, 'valor' => $acum];
            if ($i >= $this->limite) break;
        }

        return $pasos;
    }
}

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
    }

    if (empty($errores)) {
        $n = (int)$numero;

        $secuencia = match($operacion) {
            'fibonacci' => new SecuenciaFibonacci($n),
            'factorial' => new SecuenciaFactorial($n),
            default     => null,
        };

        if ($secuencia !== null) {
            $serie     = $secuencia->calcular();
            $procesado = true;
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
    <title>02 · Fibonacci / Factorial</title>
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
                                    <?= $operacion === 'fibonacci' ? 'selected' : '' ?>>
                                    Sucesión de Fibonacci (n términos)
                                </option>
                                <option value="factorial"
                                    <?= $operacion === 'factorial' ? 'selected' : '' ?>>
                                    Factorial de n (pasos acumulados)
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
                    <?= $operacion === 'fibonacci'
                        ? "Fibonacci · primeros {$numero} términos"
                        : "Factorial · pasos de 1! a {$numero}!" ?>
                </p>
                <div class="resultado-serie">
                    <?php if ($operacion === 'fibonacci'): ?>
                        <?php foreach ($serie as $idx => $val): ?>
                            <span style="color:var(--suave);font-size:0.7rem;">F(<?= $idx ?>)=</span><strong><?= $val ?></strong>
                            <?= $idx < count($serie) - 1 ? ' <span style="color:var(--borde)">›</span> ' : '' ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($serie as $paso): ?>
                            <span style="color:var(--suave);font-size:0.7rem;"><?= $paso['k'] ?>!=</span><strong><?= $paso['valor'] ?></strong>
                            <?= $paso['k'] < count($serie) ? ' <span style="color:var(--borde)">›</span> ' : '' ?>
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
