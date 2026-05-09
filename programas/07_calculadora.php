<?php
require_once __DIR__ . '/../layout.php';

class Calculadora
{
    private float  $a;
    private float  $b;
    private string $op;

    private const SIMBOLOS = [
        'suma'           => '+',
        'resta'          => '−',
        'multiplicacion' => '×',
        'division'       => '÷',
        'porcentaje'     => '%',
    ];

    public function __construct(float $a, float $b, string $op)
    {
        $this->a  = $a;
        $this->b  = $b;
        $this->op = $op;
    }

    public function ejecutar(): float|false
    {
        return match($this->op) {
            'suma'           => $this->a + $this->b,
            'resta'          => $this->a - $this->b,
            'multiplicacion' => $this->a * $this->b,
            'division'       => $this->b != 0 ? $this->a / $this->b : false,
            'porcentaje'     => ($this->a * $this->b) / 100,
            default          => false,
        };
    }

    public function expresion(): string
    {
        $sim = self::SIMBOLOS[$this->op] ?? '?';
        return "{$this->a} {$sim} {$this->b}";
    }
}


$resultado = null;
$expresion = '';
$errores   = [];
$numA      = $_POST['num_a'] ?? '';
$numB      = $_POST['num_b'] ?? '';
$opSel     = $_POST['operacion'] ?? '';


if (isset($_POST['calcular'])) {
    $aLimpio = trim($numA);
    $bLimpio = trim($numB);

    if (!is_numeric($aLimpio) || !is_numeric($bLimpio)) {
        $errores[] = 'Ambos campos deben ser números válidos.';
    } elseif ($opSel === '') {
        $errores[] = 'Selecciona una operación.';
    }

    if (empty($errores)) {
        $calc      = new Calculadora((float)$aLimpio, (float)$bLimpio, $opSel);
        $resultado = $calc->ejecutar();
        $expresion = $calc->expresion();

        if ($resultado === false) {
            $errores[] = 'Error: División por cero no está permitida.';
            $resultado = null;
        } else {
            $resultado = round($resultado, 10);
        }
    }
}

$opciones = [
    'suma'           => 'Suma  (+)',
    'resta'          => 'Resta  (−)',
    'multiplicacion' => 'Multiplicación  (×)',
    'division'       => 'División  (÷)',
    'porcentaje'     => 'Porcentaje  (A% de B)',
];

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
                            <?php foreach ($opciones as $val => $etiq): ?>
                            <option value="<?= $val ?>"
                                <?= $opSel === $val ? 'selected' : '' ?>>
                                <?= htmlspecialchars($etiq) ?>
                            </option>
                            <?php endforeach; ?>
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