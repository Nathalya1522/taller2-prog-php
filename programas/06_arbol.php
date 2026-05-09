<?php
require_once __DIR__ . '/../layout.php';


class Nodo
{
    public string $valor;
    public ?Nodo  $izq = null;
    public ?Nodo  $der = null;

    public function __construct(string $valor)
    {
        $this->valor = $valor;
    }
}

class ArbolBinario
{
    private ?Nodo $raiz = null;

    // Reconstruye desde Preorden + Inorden
    public function desdePreIn(array $pre, array $ino): void
    {
        $this->raiz = $this->construirPreIn($pre, $ino);
    }

    // Reconstruye desde Postorden + Inorden
    public function desdePostIn(array $post, array $ino): void
    {
        $this->raiz = $this->construirPostIn($post, $ino);
    }

    private function construirPreIn(array $pre, array $ino): ?Nodo
    {
        if (empty($pre)) return null;

        $raizVal = $pre[0];
        $nodo    = new Nodo($raizVal);
        $pos     = array_search($raizVal, $ino, true);

        if ($pos === false) return $nodo;

        $nodo->izq = $this->construirPreIn(
            array_slice($pre, 1, $pos),
            array_slice($ino, 0, $pos)
        );

        $nodo->der = $this->construirPreIn(
            array_slice($pre, $pos + 1),
            array_slice($ino, $pos + 1)
        );

        return $nodo;
    }

    private function construirPostIn(array $post, array $ino): ?Nodo
    {
        if (empty($post)) return null;

        $raizVal = $post[array_key_last($post)];
        $nodo    = new Nodo($raizVal);
        $pos     = array_search($raizVal, $ino, true);

        if ($pos === false) return $nodo;

        $nodo->izq = $this->construirPostIn(
            array_slice($post, 0, $pos),
            array_slice($ino, 0, $pos)
        );

        $nodo->der = $this->construirPostIn(
            array_slice($post, $pos, count($post) - $pos - 1),
            array_slice($ino, $pos + 1)
        );

        return $nodo;
    }

    public function preorden(): array  { $r = []; $this->_pre($this->raiz, $r);  return $r; }
    public function inorden(): array   { $r = []; $this->_in($this->raiz, $r);   return $r; }
    public function postorden(): array { $r = []; $this->_post($this->raiz, $r); return $r; }

    private function _pre(?Nodo $n, array &$r): void
    {
        if (!$n) return;
        $r[] = $n->valor;
        $this->_pre($n->izq, $r);
        $this->_pre($n->der, $r);
    }

    private function _in(?Nodo $n, array &$r): void
    {
        if (!$n) return;
        $this->_in($n->izq, $r);
        $r[] = $n->valor;
        $this->_in($n->der, $r);
    }

    private function _post(?Nodo $n, array &$r): void
    {
        if (!$n) return;
        $this->_post($n->izq, $r);
        $this->_post($n->der, $r);
        $r[] = $n->valor;
    }

    // Visualización ASCII
    public function dibujar(): string
    {
        if (!$this->raiz) return '(árbol vacío)';
        $lineas = [];
        $this->dibujarNodo($this->raiz, '', true, $lineas);
        return implode("\n", $lineas);
    }

    private function dibujarNodo(?Nodo $n, string $pref, bool $esUltimo, array &$lines): void
    {
        if (!$n) return;
        $conector = $esUltimo ? '└── ' : '├── ';
        $lines[]  = $pref . $conector . '[' . $n->valor . ']';
        $nuevoPref = $pref . ($esUltimo ? '    ' : '│   ');
        $hijos     = array_filter([$n->izq, $n->der]);
        $total     = count($hijos);
        $idx       = 0;
        if ($n->izq) { $this->dibujarNodo($n->izq, $nuevoPref, (++$idx === $total), $lines); }
        if ($n->der) { $this->dibujarNodo($n->der, $nuevoPref, (++$idx === $total), $lines); }
    }
}

// ── HELPERS ──────────────────────────────────────────────
function separarRecorrido(string $input): array
{
    $limpio = preg_replace('/[-–→>]+/', ',', $input);
    $tokens = preg_split('/[\s,;]+/', trim($limpio), -1, PREG_SPLIT_NO_EMPTY);
    return array_map('strtoupper', $tokens);
}


$arbol   = null;
$dibujo  = '';
$errores = [];
$preStr  = $_POST['preorden']  ?? '';
$inoStr  = $_POST['inorden']   ?? '';
$postStr = $_POST['postorden'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pre  = !empty(trim($preStr))  ? separarRecorrido($preStr)  : [];
    $ino  = !empty(trim($inoStr))  ? separarRecorrido($inoStr)  : [];
    $post = !empty(trim($postStr)) ? separarRecorrido($postStr) : [];

    $cantidad = (int)(!empty($pre)) + (int)(!empty($ino)) + (int)(!empty($post));

    if ($cantidad < 2) {
        $errores[] = 'Debes ingresar al menos dos recorridos.';
    } elseif (empty($ino)) {
        $errores[] = 'El recorrido INORDEN es obligatorio para reconstruir el árbol.';
    } else {
        $arbol = new ArbolBinario();
        !empty($pre)
            ? $arbol->desdePreIn($pre, $ino)
            : $arbol->desdePostIn($post, $ino);

        $dibujo = $arbol->dibujar();
    }
}

$base = '../';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>06 · Árbol Binario</title>
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
            <h2>Árbol Binario</h2>
            <span class="badge">App 06</span>
        </div>
        <main>
            <?php foreach ($errores as $e): ?>
                <div class="alerta alerta-error"><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>

            <div class="alerta alerta-info">
                Ingresa al menos dos recorridos. El <strong>inorden es obligatorio</strong>.
                Usa → , comas o espacios como separadores.
            </div>

            <div class="panel">
                <p class="panel-titulo">Recorridos del árbol</p>
                <form method="POST" action="">
                    <div class="campo">
                        <label for="preorden">Preorden (Raíz → Izq → Der)</label>
                        <input type="text" id="preorden" name="preorden"
                            placeholder="Ej: A → B → D → E → C"
                            value="<?= htmlspecialchars($preStr) ?>">
                    </div>
                    <div class="campo">
                        <label for="inorden">Inorden · Izq → Raíz → Der (requerido)</label>
                        <input type="text" id="inorden" name="inorden"
                            placeholder="Ej: D → B → E → A → C"
                            value="<?= htmlspecialchars($inoStr) ?>">
                    </div>
                    <div class="campo">
                        <label for="postorden">Postorden (Izq → Der → Raíz)</label>
                        <input type="text" id="postorden" name="postorden"
                            placeholder="Ej: D → E → B → C → A"
                            value="<?= htmlspecialchars($postStr) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Construir árbol →</button>
                </form>
            </div>

            <?php if ($arbol && empty($errores)): ?>
            <div class="arbol-vista">
                <p style="font-size:0.7rem;letter-spacing:1px;text-transform:uppercase;color:var(--suave);margin-bottom:12px;">Estructura del árbol</p>
                <pre><?= htmlspecialchars($dibujo) ?></pre>
            </div>

            <div class="resultado" style="margin-top:18px;">
                <p class="resultado-titulo">Recorridos generados</p>
                <div class="fila-dato">
                    <span class="etiqueta">Preorden</span>
                    <span style="color:var(--verde);font-size:0.85rem;"><?= implode(' → ', $arbol->preorden()) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">Inorden</span>
                    <span style="color:var(--verde);font-size:0.85rem;"><?= implode(' → ', $arbol->inorden()) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">Postorden</span>
                    <span style="color:var(--verde);font-size:0.85rem;"><?= implode(' → ', $arbol->postorden()) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
</body>
</html>
