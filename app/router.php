<?php
/**
 * @var \FastRoute\RouteCollector $this
 */
if (!isset($this) || !$this instanceof \FastRoute\RouteCollector) {
    if (!headers_sent()) {
        header('Location: ../', 301);
    }
    exit;
}

// -----------------------------------------------------------------
// MULAI ROUTER
// -----------------------------------------------------------------

/**
 * HTTP Method tersedia
 *
 * @var array
 */
$allMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'TRACE', 'HEAD',];
$standardMethod = ['POST', 'GET'];
$this->addRoute($allMethods, '/', 'RouterBase::Home'); // homepage
$this->addRoute($allMethods, '/{match: (?i)keluar}[/]', 'RouterBase::Keluar'); // KELUAR
$this->addRoute($allMethods, '/{match: (?i)masuk}[/]', 'RouterBase::Masuk');   // MASUK / LOGIN
// PROFILE
$this->addRoute($standardMethod, '/profile[/]', 'RouterAnggota::Profile');
// ANGGOTA
$this->addGroup('/anggota', function (\FastRoute\RouteCollector $r) use ($standardMethod) {
    $r->addRoute($standardMethod, '[/]', 'RouterAnggota::List');
    $r->addRoute($standardMethod, '/baru[/]', 'RouterAnggota::Baru');
    $r->addRoute($standardMethod, '/{action: (?i)ubah}[/[{id: (?i)(?:[0-9]+)}[/]]]', 'RouterAnggota::Ubah');
    $r->addRoute($standardMethod, '/{action: (?i)hapus}[/[{id: (?i)(?:[0-9]+)}[/]]]', 'RouterAnggota::Hapus');
});
// BUKU
$this->addGroup('/buku', function (\FastRoute\RouteCollector $r) use ($standardMethod) {
    $r->addRoute($standardMethod, '[/]', 'RouterBuku::List');

    $r->addRoute($standardMethod, '/baru[/]', 'RouterBuku::Baru');
    $r->addRoute($standardMethod, '/pinjaman[/]', 'RouterBuku::Pinjaman');
    $r->addRoute($standardMethod, '/{action: (?i)ubah}[/[{id: \d+}[/]]]', 'RouterBuku::Ubah');
    $r->addRoute($standardMethod, '/{action: (?i)hapus}[/[{id: \d+}[/]]]', 'RouterBuku::Hapus');
    $r->addRoute($standardMethod, '/{action: (?i)detail}[/[{id: [^/]+}[/]]]', 'RouterBuku::Detail');
});

// API
$this->addGroup('/api', function (\FastRoute\RouteCollector $r) use ($standardMethod) {
    $r->addRoute($standardMethod, '[/]', function () {
        if (!headers_sent()) {
            header('Content-Type: application/json', 404);
        }
        echo json_encode([
            'code' => 404,
            'error' => 'Target API Salah'
        ],JSON_PRETTY_PRINT);
        exit(0);
    });
    // anggota
    $r->addRoute($standardMethod, '/anggota[/[{username: .*}[{slash: [/]+}]]]', 'RouterAnggota::ApiCari');
    // buku
    $r->addRoute($standardMethod, '/buku[/[{nama: .*}[{slash: [/]+}]]]', 'RouterBuku::ApiCariBukuJudul');
    $r->addRoute($standardMethod, '/buku-pengarang[/[{nama: .*}[{slash: [/]+}]]]', 'RouterBuku::ApiCariJudulBukuByPengarang');
    $r->addRoute($standardMethod, '/pengarang[/[{nama: .*}[{slash: [/]+}]]]', 'RouterBuku::ApiCariPengarang');

});