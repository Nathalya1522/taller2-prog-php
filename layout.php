<?php

$enApps  = (strpos(__DIR__, 'programas') !== false);
$base    = $enApps ? '../' : '';


$menu = [
    ''                  => ['ico' => '', 'label' => 'Inicio',              'num' => '00'],
    'programas/01_acronimo'  => ['ico' => '', 'label' => 'Acrónimo',           'num' => '01'],
    'programas/02_fibonacci' => ['ico' => '',  'label' => 'Fibonacci/Factorial', 'num' => '02'],
    'programas/03_estadistica'=> ['ico' => '', 'label' => 'Estadística',        'num' => '03'],
    'programas/04_conjuntos' => ['ico' => '',  'label' => 'Conjuntos',           'num' => '04'],
    'programas/05_binario'   => ['ico' => '', 'label' => 'Binario',             'num' => '05'],
    'programas/06_arbol'     => ['ico' => '',  'label' => 'Árbol Binario',      'num' => '06'],
    'programas/07_calculadora'=> ['ico' => '', 'label' => 'Calculadora',        'num' => '07'],
];


$uriActual = strtok($_SERVER['REQUEST_URI'], '?');

function esActivo(string $clave, string $base): bool {
    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    if ($clave === '') {
        return (bool) preg_match('#/index\.php$#', $uri);
    }
    return (bool) strpos($uri, basename($clave)) !== false;
}

function urlMenu(string $clave, string $base): string {
    if ($clave === '') return $base . 'index.php';
    return $base . $clave . '.php';
}
?>
