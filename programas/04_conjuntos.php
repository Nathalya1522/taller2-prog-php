<?php
require_once __DIR__ . '/../layout.php';

class OperadorConjuntos
{
    private array $conjA;
    private array $conjB;

    public function __construct(array $a, array $b)
    {
        $this->conjA = array_values(array_unique($a));
        $this->conjB = array_values(array_unique($b));
    }

    public function union(): array
    {
        $union = array_unique(array_merge($this->conjA, $this->conjB));
        sort($union);
        return array_values($union);
    }

    public function interseccion(): array
    {
        $inter = array_intersect($this->conjA, $this->conjB);
        sort($inter);
        return array_values($inter);
    }

    public function diferenciaAB(): array
    {
        $diff = array_diff($this->conjA, $this->conjB);
        sort($diff);
        return array_values($diff);
    }

    public function diferenciaBA(): array
    {
        $diff = array_diff($this->conjB, $this->conjA);
        sort($diff);
        return array_values($diff);
    }

    public function getA(): array
    {
        $a = $this->conjA;
        sort($a);
        return $a;
    }

    public function getB(): array
    {
        $b = $this->conjB;
        sort($b);
        return $b;
    }
}


function parsear(string $entrada): array
{
    $tokens = preg_split('/[\s,;{}]+/', trim($entrada), -1, PREG_SPLIT_NO_EMPTY);
    return array_values(array_filter(
        array_map(fn($t) => is_numeric($t) ? (int)$t : null, $tokens),
        fn($v) => $v !== null
    ));
}

function mostrarConjunto(array $arr): string
{
    return empty($arr) ? '∅' : '{ ' . implode(', ', $arr) . ' }';
}


$resultados = null;
$errores    = [];
$inputA     = $_POST['conjunto_a'] ?? '';
$inputB     = $_POST['conjunto_b'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $arrA = parsear($inputA);
    $arrB = parsear($inputB);

    if (empty($arrA)) $errores[] = 'El Conjunto A no contiene enteros válidos.';
    if (empty($arrB)) $errores[] = 'El Conjunto B no contiene enteros válidos.';

    if (empty($errores)) {
        $op = new OperadorConjuntos($arrA, $arrB);

        $resultados = [
            ['etiqueta' => 'A',        'simbolo' => '',     'valor' => $op->getA()],
            ['etiqueta' => 'B',        'simbolo' => '',     'valor' => $op->getB()],
            ['etiqueta' => 'A ∪ B',   'simbolo' => 'Unión',        'valor' => $op->union()],
            ['etiqueta' => 'A ∩ B',   'simbolo' => 'Intersección', 'valor' => $op->interseccion()],
            ['etiqueta' => 'A − B',   'simbolo' => 'Diferencia',   'valor' => $op->diferenciaAB()],
            ['etiqueta' => 'B − A',   'simbolo' => 'Diferencia',   'valor' => $op->diferenciaBA()],
        ];
    }
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

            <?php if ($resultados !== null): ?>
            <div class="resultado">
                <p class="resultado-titulo">Resultados de las operaciones</p>
                <?php foreach ($resultados as $fila): ?>
                <div class="fila-dato">
                    <div>
                        <span class="etiqueta"><?= htmlspecialchars($fila['etiqueta']) ?></span>
                        <?php if ($fila['simbolo']): ?>
                            <span style="font-size:0.68rem;color:var(--suave);margin-left:6px;">
                                (<?= htmlspecialchars($fila['simbolo']) ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                    <span class="valor-dato"><?= htmlspecialchars(mostrarConjunto($fila['valor'])) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>
