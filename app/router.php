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
    $r->addRoute($standardMethod, '/{action: (?i)ubah}[/[{username: (?i)(?:[^\/\s]+)}[/]]]', 'RouterAnggota::Ubah');
    $r->addRoute($standardMethod, '/{action: (?i)hapus}[/[{username: (?i)(?:[^\/\s]+)}[/]]]', 'RouterAnggota::Hapus');
});
// BUKU
$this->addGroup('/buku', function (\FastRoute\RouteCollector $r) use ($standardMethod) {
    $r->addRoute($standardMethod, '[/]', 'RouterBuku::List');
    $r->addRoute($standardMethod, '/baru[/]', 'RouterBuku::Baru');
    $r->addRoute($standardMethod, '/pinjaman[/]', 'RouterBuku::Pinjaman');
    $r->addRoute($standardMethod, '/{action: (?i)ubah}[/[{id: \d+}[/]]]', 'RouterBuku::Ubah');
    $r->addRoute($standardMethod, '/{action: (?i)hapus}[/[{id: \d+}[/]]]', 'RouterBuku::Hapus');
});
