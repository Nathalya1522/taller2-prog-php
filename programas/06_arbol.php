<?php
require_once __DIR__ . '/../layout.php';

class Nodo {
    public string $valor;
    public $izq = null;
    public $der = null;
    public function __construct(string $v) { $this->valor = $v; }
}

function construir(array $pre, array $ino) {
    if (count($pre) == 0) return null;

    $raiz = $pre[0];
    $nodo = new Nodo($raiz);

    $pos = 0;
    for ($i = 0; $i < count($ino); $i++) {
        if ($ino[$i] == $raiz) { $pos = $i; break; }
    }

    $inoIzq = array_slice($ino, 0, $pos);
    $inoDer = array_slice($ino, $pos + 1);
    $preIzq = array_slice($pre, 1, count($inoIzq));
    $preDer = array_slice($pre, 1 + count($inoIzq));

    
    $nodo->izq = construir($preIzq, $inoIzq);
    $nodo->der = construir($preDer, $inoDer);

    return $nodo;
}


function preorden($n, &$r)  { if (!$n) return; $r[] = $n->valor; preorden($n->izq, $r);  preorden($n->der, $r); }
function inorden($n, &$r)   { if (!$n) return; inorden($n->izq, $r);  $r[] = $n->valor; inorden($n->der, $r); }
function postorden($n, &$r) { if (!$n) return; postorden($n->izq, $r); postorden($n->der, $r); $r[] = $n->valor; }


function dibujar($nodo, $pref, $esUltimo, &$lineas) {
    if ($nodo == null) return;
    $lineas[] = $pref . ($esUltimo ? '└── ' : '├── ') . '[' . $nodo->valor . ']';
    $nuevoPref = $pref . ($esUltimo ? '    ' : '│   ');
    if ($nodo->izq) dibujar($nodo->izq, $nuevoPref, $nodo->der == null, $lineas);
    if ($nodo->der) dibujar($nodo->der, $nuevoPref, true, $lineas);
}


function separar(string $texto): array {
    $limpio = preg_replace('/[-→>]+/', ',', $texto);
    $tokens = preg_split('/[\s,;]+/', trim($limpio), -1, PREG_SPLIT_NO_EMPTY);
    $resultado = [];
    for ($i = 0; $i < count($tokens); $i++) {
        $resultado[] = strtoupper($tokens[$i]);
    }
    return $resultado;
}


$errores   = [];
$dibujo    = '';
$preorden  = [];
$inorden   = [];
$postorden = [];
$raiz      = null;
$preStr    = $_POST['preorden']  ?? '';
$inoStr    = $_POST['inorden']   ?? '';
$postStr   = $_POST['postorden'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pre  = trim($preStr)  != '' ? separar($preStr)  : [];
    $ino  = trim($inoStr)  != '' ? separar($inoStr)  : [];
    $post = trim($postStr) != '' ? separar($postStr) : [];

    $cantidad = (count($pre) > 0 ? 1 : 0)
              + (count($ino) > 0 ? 1 : 0)
              + (count($post) > 0 ? 1 : 0);

    if ($cantidad < 2) {
        $errores[] = 'Debes ingresar al menos dos recorridos.';
    } elseif (count($ino) == 0) {
        $errores[] = 'El recorrido INORDEN es obligatorio.';
    } else {
        $raiz = construir($pre, $ino);

        $lineas = [];
        dibujar($raiz, '', true, $lineas);
        $dibujo = implode("\n", $lineas);

        preorden($raiz,  $preorden);
        inorden($raiz,   $inorden);
        postorden($raiz, $postorden);
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
                $url = urlMenu($clave, $base);
                if (esActivo($clave, $base)) { $activo = 'activo'; } else { $activo = ''; }
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

            <?php if ($raiz != null && count($errores) == 0): ?>
            <div class="arbol-vista">
                <p style="font-size:0.7rem;letter-spacing:1px;text-transform:uppercase;color:var(--suave);margin-bottom:12px;">Estructura del árbol</p>
                <pre><?= htmlspecialchars($dibujo) ?></pre>
            </div>

            <div class="resultado" style="margin-top:18px;">
                <p class="resultado-titulo">Recorridos generados</p>
                <div class="fila-dato">
                    <span class="etiqueta">Preorden</span>
                    <span style="color:var(--verde);font-size:0.85rem;"><?= implode(' → ', $preorden) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">Inorden</span>
                    <span style="color:var(--verde);font-size:0.85rem;"><?= implode(' → ', $inorden) ?></span>
                </div>
                <div class="fila-dato">
                    <span class="etiqueta">Postorden</span>
                    <span style="color:var(--verde);font-size:0.85rem;"><?= implode(' → ', $postorden) ?></span>
                </div>
            </div>
            <?php endif; ?>

        </main>
    </div>
</div>
</body>
</html>
