<?php
class RouterBuku
{
    const MESSAGE_BUKU_SIMPAN_SUKSES = '<div class="alert alert-success alert-dismissable">Detail Buku Berhasil Diubah<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';
    const MESSAGE_BUKU_SIMPAN_GAGAL  = '<div class="alert alert-danger alert-dismissable">Gagal Menyimpan Data<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';

    public static function ApiCariBukuJudul($params)
    {
        if (!is_login()) {
            if (!headers_sent()) {
                header('Content-Type: application/json;charset=utf-8', 401);
            }
            echo json_encode([
                "code" => 401,
                "error" => "Tidak Punya Hak Akses",
            ],JSON_PRETTY_PRINT);
            exit(0);
        }

        if (!headers_sent()) {
            header('Content-Type: application/json;charset=utf-8', 200);
        }
        $nama_buku = !isset($params['nama'])
            ? ''
            : $params['nama'];

        $limit = get('limit', 100);
        $offset = get('offset', 0);
        $limit = is_numeric($limit) ? intval(abs($limit)) : 100;
        $limit = $limit > 0 ? $limit : 100;
        $offset = is_numeric($offset) ? intval(abs($offset)) : 0;
        $offset = $limit > 0 ? $offset : 0;

        $pencarian = cari_buku_by_judul($nama_buku, $offset, $limit);
        $pencarian = is_array($pencarian) ? $pencarian : [];
        $value = [
            "code" => 200,
            "total" => count($pencarian),
            "data" => $pencarian
        ];
        if (get('array') == 'true') {
            $value = $value['data'];
        }
        echo json_encode($value,JSON_PRETTY_PRINT);
        exit(0);
    }

    public static function ApiCariJudulBukuByPengarang($params)
    {
        if (!is_login()) {
            if (!headers_sent()) {
                header('Content-Type: application/json;charset=utf-8', 401);
            }
            echo json_encode([
                "code" => 401,
                "error" => "Tidak Punya Hak Akses",
            ],JSON_PRETTY_PRINT);
            exit(0);
        }

        if (!headers_sent()) {
            header('Content-Type: application/json;charset=utf-8', 200);
        }
        $nama_pengarang = !isset($params['nama'])
            ? ''
            : $params['nama'];

        $limit = get('limit', 100);
        $offset = get('offset', 0);
        $limit = is_numeric($limit) ? intval(abs($limit)) : 100;
        $limit = $limit > 0 ? $limit : 100;
        $offset = is_numeric($offset) ? intval(abs($offset)) : 0;
        $offset = $limit > 0 ? $offset : 0;

        $pencarian = cari_buku_by_pengarang($nama_pengarang, $offset, $limit);
        $pencarian = is_array($pencarian) ? $pencarian : [];
        $value = [
            "code" => 200,
            "total" => count($pencarian),
            "data" => $pencarian
        ];
        if (get('array') == 'true') {
            $value = $value['data'];
        }
        echo json_encode($value,JSON_PRETTY_PRINT);
        exit(0);
    }

    public static function ApiCariPengarang($params)
    {
        if (!is_login()) {
            if (!headers_sent()) {
                header('Content-Type: application/json;charset=utf-8', 401);
            }
            echo json_encode([
                "code" => 401,
                "error" => "Tidak Punya Hak Akses",
            ],JSON_PRETTY_PRINT);
            exit(0);
        }

        if (!headers_sent()) {
            header('Content-Type: application/json;charset=utf-8', 200);
        }
        $nama_pengarang = !isset($params['nama'])
            ? ''
            : $params['nama'];

        $limit = get('limit', 100);
        $offset = get('offset', 0);
        $limit = is_numeric($limit) ? intval(abs($limit)) : 100;
        $limit = $limit > 0 ? $limit : 100;
        $offset = is_numeric($offset) ? intval(abs($offset)) : 0;
        $offset = $limit > 0 ? $offset : 0;

        $pencarian = cari_pengarang_dari_buku($nama_pengarang, $offset, $limit);
        $pencarian = is_array($pencarian) ? $pencarian : [];

        $value = [
            "code" => 200,
            "total" => count($pencarian),
            "data" => $pencarian
        ];
        if (get('array') == 'true') {
            $value = $value['data'];
        }
        echo json_encode($value,JSON_PRETTY_PRINT);
        exit(0);
    }

    public static function ApiCariPenerbit($params)
    {
        if (!is_login()) {
            if (!headers_sent()) {
                header('Content-Type: application/json;charset=utf-8', 401);
            }
            echo json_encode([
                "code" => 401,
                "error" => "Tidak Punya Hak Akses",
            ],JSON_PRETTY_PRINT);
            exit(0);
        }

        if (!headers_sent()) {
            header('Content-Type: application/json;charset=utf-8', 200);
        }

        $nama_penerbit = !isset($params['nama'])
            ? ''
            : $params['nama'];

        $limit = get('limit', 100);
        $offset = get('offset', 0);
        $limit = is_numeric($limit) ? intval(abs($limit)) : 100;
        $limit = $limit > 0 ? $limit : 100;
        $offset = is_numeric($offset) ? intval(abs($offset)) : 0;
        $offset = $limit > 0 ? $offset : 0;

        $pencarian = cari_penerbit_dari_buku($nama_penerbit, $offset, $limit);
        $pencarian = is_array($pencarian) ? $pencarian : [];

        $value = [
            "code" => 200,
            "total" => count($pencarian),
            "data" => $pencarian
        ];
        if (get('array') == 'true') {
            $value = $value['data'];
        }
        echo json_encode($value,JSON_PRETTY_PRINT);
        exit(0);
    }

    // ------------------------------------------
    private static function upload_gambar()
    {
        if (get_method() !== 'POST') {
            return false;
        }
        $file = isset($_FILES['buku']) && is_array($_FILES['buku'])
            ? $_FILES['buku']
            : [];
        if (empty($file['tmp_name']['path_gambar'])
            || $file['error']['path_gambar']
            || stripos($file['type']['path_gambar'], 'image/') === false
        ) {
            $file = null;
        }
        $return = false;
        if ($file !== null) {
            $image_extension  = explode('/', $file['type']['path_gambar']);
            $image_extension   = $image_extension[1];
            $image_name   = sha1($file['name']['path_gambar'] . microtime());
            $name = "{$image_name}.{$image_extension}";
            $image_target = get_path_upload_gambar() . DIRECTORY_SEPARATOR. $name;
            $return = [
                'source' => $file['tmp_name']['path_gambar'],
                'original_name' => $file['name']['path_gambar'],
                'extension' => $image_extension,
                'size' => $file['size']['path_gambar'],
                'base_name' => $image_name,
                'name' => $name,
                'target' => $image_target,
            ];

            if (!file_exists($return['source']) || !@move_uploaded_file($return['source'], $return['target'])) {
                $return = false;
            }
        }

        return $return;
    }
    public static function Baru($params)
    {
        check_redirect_admin_router();
        if (get_method() === 'POST') {
            $data = post('buku');
            if (!is_array($data)) {
                redirect(get_base_url_index('buku/baru?status=error'));
            }
            $toSave = [
                'judul'     => '',
                'pengarang' => '',
                'penerbit'  => '',
                'tahun'     => 2017,
                'keterangan' => '',
                'path_gambar' => null,
            ];
            $image_detail = self::upload_gambar();
            $session =& get_all_sessions();
            $session['buku_baru'] = $data;
            if (!isset($data['judul']) || !is_string($data['judul']) || trim($data['judul']) == '') {
                unset($session['buku_baru']['judul']);
                if ($image_detail) {
                    unlink($image_detail['target']);
                    if (file_exists($image_detail['source'])) {
                        unlink($image_detail['source']);
                    }
                }
                $message = 'Judul tidak boleh kosong';
                redirect(get_base_url_index('buku/baru?status=error&message='.rawurlencode($message)));
            }
            if (!isset($data['pengarang']) || !is_string($data['pengarang']) || trim($data['judul']) == '') {
                unset($session['buku_baru']['pengarang']);
                if ($image_detail) {
                    unlink($image_detail['target']);
                    if (file_exists($image_detail['source'])) {
                        unlink($image_detail['source']);
                    }
                }
                $message = 'Pengarang tidak boleh kosong';
                redirect(get_base_url_index('buku/baru?status=error&message='.rawurlencode($message)));
            }
            if (!isset($data['penerbit']) || !is_string($data['penerbit']) || trim($data['penerbit']) == '') {
                unset($session['buku_baru']['penerbit']);
                if ($image_detail) {
                    unlink($image_detail['target']);
                    if (file_exists($image_detail['source'])) {
                        unlink($image_detail['source']);
                    }
                }
                $message = 'Penerbit tidak boleh kosong';
                redirect(get_base_url_index('buku/baru?status=error&message='.rawurlencode($message)));
            }

            if (!isset($data['tahun']) || !is_numeric($data['tahun']) || strlen($data['tahun']) <> 4) {
                unset($session['buku_baru']['tahun']);
                if ($image_detail) {
                    unlink($image_detail['target']);
                    if (file_exists($image_detail['source'])) {
                        unlink($image_detail['source']);
                    }
                }
                $message = 'Tahun Penerbitan Salah';
                redirect(get_base_url_index('buku/baru?status=error&message='.rawurlencode($message)));
            }

            if (isset($data['keterangan']) && is_string($data['keterangan'])) {
                $toSave['keterangan'] = trim($data['keterangan']);
                $session['buku_baru']['keterangan'] = $toSave['keterangan'];
            } else {
                unset($session['buku_baru']['keterangan']);
            }

            if (is_array($image_detail)) {
                // unlink($image_detail['target']);
                $toSave['path_gambar'] = $image_detail['name'];
                if (!class_exists('Pentagonal\ImageResizer')) {
                    require_once __DIR__ .'/../ImageResizer.php';
                }
                if (file_exists($image_detail['target'])) {
                    try {
                        $imageResize = Pentagonal\ImageResizer::create($image_detail['target']);
                        if ($imageResize->crop(200, 200)) {
                            $fileName = dirname($image_detail['target']);
                            $fileName .= DIRECTORY_SEPARATOR . "thumb-{$image_detail['name']}";
                            $imageResize->saveTo($fileName);
                        }
                        $imageResize->clear();
                    } catch (\Exception $e) {
                        // by pass
                    }
                }
            }

            $toSave['judul'] = trim($data['judul']);
            $toSave['pengarang'] = trim($data['pengarang']);
            $toSave['penerbit'] = trim($data['penerbit']);
            $status = database_create_buku($toSave);
            if (is_object($status)) {
                delete_session('buku_baru');
                $id_buku = database_execute(
                    "SELECT id FROM buku
                        WHERE judul=?
                          AND pengarang=?
                          AND penerbit=?
                          AND tahun=?
                          ORDER BY id DESC
                          LIMIT 1
                    ",
                    [
                        $toSave['judul'],
                        $toSave['pengarang'],
                        $toSave['penerbit'],
                        $toSave['tahun'],
                    ]
                );

                $id_buku = $id_buku->fields['id'];
                redirect(get_base_url_index("buku/ubah/{$id_buku}?status=tambah"));
                return;
            }

            $message = rawurlencode(base64_encode(database()->_errormsg));
            redirect(get_base_url_index("buku/baru/?status=error&message=".$message));
            return;
        }

        $status = get('status');
        if ($status && is_string($status) && in_array(strtolower($status), ['sukses', 'error'])) {
            $params['message'] = strtolower($status) == 'sukses'
                ? self::MESSAGE_BUKU_SIMPAN_SUKSES
                : self::MESSAGE_BUKU_SIMPAN_GAGAL;
            if (strtolower($status) === 'error') {
                $message = get('message');
                $message = is_string($message) ? base64_decode(rawurldecode($message)) : null;
                if ($message) {
                    $params['message'] = "<div class=\"alert alert-danger alert-dismissable\">{$message}<div class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</div></div>";
                }
            }
        }

        muat_layout('buku-baru', $params);
    }

    public static function Ubah($params)
    {
        check_redirect_admin_router();
        $id = empty($params['id'])
            ? null
            : $params['id'];
        if (!$id) {
            redirect(get_base_url_index('buku'));
        }

        $toSave = database_get_buku_by_id($id);
        if (!$toSave) {
            redirect(get_base_url_index('buku'));
        }
        unset($toSave['tanggal_pinjam']);
        unset($toSave['anggota_id_pinjam']);

        if (get_method() === 'POST') {
            $data = post('buku');
            if (!is_array($data)) {
                redirect(get_base_url_index('buku/ubah/'.$id.'?status=error'));
            }

            $image_detail = self::upload_gambar();
            if (!isset($data['judul']) || !is_string($data['judul']) || trim($data['judul']) == '') {
                if ($image_detail) {
                    unlink($image_detail['target']);
                    if (file_exists($image_detail['source'])) {
                        unlink($image_detail['source']);
                    }
                }
                $message = 'Judul tidak boleh kosong';
                redirect(get_base_url_index('buku/ubah/'.$id.'?status=error&message='.rawurlencode($message)));
            }
            if (!isset($data['pengarang']) || !is_string($data['pengarang']) || trim($data['judul']) == '') {
                if ($image_detail) {
                    unlink($image_detail['target']);
                    if (file_exists($image_detail['source'])) {
                        unlink($image_detail['source']);
                    }
                }
                $message = 'Pengarang tidak boleh kosong';
                redirect(get_base_url_index('buku/ubah/'.$id.'?status=error&message='.rawurlencode($message)));
            }
            if (!isset($data['penerbit']) || !is_string($data['penerbit']) || trim($data['penerbit']) == '') {
                if ($image_detail) {
                    unlink($image_detail['target']);
                    if (file_exists($image_detail['source'])) {
                        unlink($image_detail['source']);
                    }
                }
                $message = 'Penerbit tidak boleh kosong';
                redirect(get_base_url_index('buku/ubah/'.$id.'?status=error&message='.rawurlencode($message)));
            }

            if (!isset($data['tahun']) || !is_numeric($data['tahun']) || strlen($data['tahun']) <> 4) {
                if ($image_detail) {
                    unlink($image_detail['target']);
                    if (file_exists($image_detail['source'])) {
                        unlink($image_detail['source']);
                    }
                }
                $message = 'Tahun Penerbitan Salah';
                redirect(get_base_url_index('buku/ubah/'.$id.'?status=error&message='.rawurlencode($message)));
            }

            if (isset($data['anggota_id_pinjam']) && is_numeric($data['anggota_id_pinjam'])) {
                if (database_get_anggota_by_id($data['anggota_id_pinjam'])) {
                    $toSave['anggota_id_pinjam'] = (int) $data['anggota_id_pinjam'];
                } elseif ($toSave['anggota_id_pinjam'] < 1) {
                    $toSave['anggota_id_pinjam'] = null;
                }
            }

            if (isset($toSave['anggota_id_pinjam'])
                && isset($data['tanggal_pinjam']) && is_array($data['tanggal_pinjam'])
            ) {
                if (is_numeric($toSave['anggota_id_pinjam']) && $toSave['anggota_id_pinjam'] > 0) {
                    $tanggal_pinjam           = $data['tanggal_pinjam'];
                    $tahun                    = isset($tanggal_pinjam['tahun']) ? $tanggal_pinjam['tahun'] : date('Y');
                    $bulan                    = isset($tanggal_pinjam['bulan']) ? $tanggal_pinjam['bulan'] : date('m');
                    $tanggal                  = isset($tanggal_pinjam['tanggal']) ? $tanggal_pinjam['tanggal'] : date('d');
                    $jam                      = isset($tanggal_pinjam['jam']) ? $tanggal_pinjam['jam'] : date('H');
                    $jam                      = is_numeric($jam) && is_int(abs($jam)) && in_array(abs($jam),
                        range(1, 24)) ? abs($jam) : 12 ;
                    $jam                      = $jam < 10 ? "0{$jam}" : $jam;
                    $menit                    = isset($tanggal_pinjam['menit']) ? $tanggal_pinjam['menit'] : date('i');
                    $menit                    = is_numeric($menit) && is_int(abs($menit))
                                                && in_array(abs($menit), range(0, 59)) ? abs($menit) : 30;
                    $menit                    = $menit < 10 ? "0{$menit}" : $menit;
                    $time                     = strtotime("{$tahun}-{$bulan}-{$tanggal} {$jam}:{$menit}:00");
                    $toSave['tanggal_pinjam'] = date('Y-m-d H:i:s', $time);
//                    print_r('<pre>');
//                    print_r($toSave);
//                    print_r($data);exit;
                } else {
                    $toSave['tanggal_pinjam'] = null;
                }
            } else {
                $toSave['tanggal_pinjam'] = null;
            }

            if (isset($data['keterangan']) && is_string($data['keterangan'])) {
                $toSave['keterangan'] = trim($data['keterangan']);
                $session['buku_baru']['keterangan'] = $toSave['keterangan'];
            }

            if (is_array($image_detail)) {
                $path_gambar = $image_detail['name'];
                if (is_string($toSave['path_gambar'])) {
                    $targetDir = dirname($image_detail['target']);
                    if (file_exists($targetDir . DIRECTORY_SEPARATOR . $path_gambar)) {
                        unlink($targetDir . DIRECTORY_SEPARATOR . $path_gambar);
                    }
                    if (file_exists($targetDir . DIRECTORY_SEPARATOR . 'thumb-'.$path_gambar)) {
                        unlink($targetDir . DIRECTORY_SEPARATOR . 'thumb-'.$path_gambar);
                    }
                }

                // unlink($image_detail['target']);
                $toSave['path_gambar'] = $path_gambar;
                if (!class_exists('Pentagonal\ImageResizer')) {
                    require_once __DIR__ .'/../ImageResizer.php';
                }
                if (file_exists($image_detail['target'])) {
                    try {
                        $imageResize = Pentagonal\ImageResizer::create($image_detail['target']);
                        if ($imageResize->crop(200, 200)) {
                            $fileName = dirname($image_detail['target']);
                            $fileName .= DIRECTORY_SEPARATOR . "thumb-{$image_detail['name']}";
                            $imageResize->saveTo($fileName);
                        }
                        $imageResize->clear();
                    } catch (\Exception $e) {
                        // by pass
                    }
                }
            }

            $toSave['judul'] = trim($data['judul']);
            $toSave['pengarang'] = trim($data['pengarang']);
            $toSave['penerbit'] = trim($data['penerbit']);
            $status = database_update_buku($id, $toSave);
            if (is_object($status)) {
                redirect(get_base_url_index("buku/ubah/{$id}?status=sukses"));
                return;
            }

            $message = rawurlencode(base64_encode(database()->_errormsg));
            redirect(get_base_url_index("buku/baru/?status=error&message=".$message));
            return;
        }

        $params['id'] = $id;
        $status = get('status');
        if ($status && is_string($status) && in_array(strtolower($status), ['tambah','sukses', 'error'])) {
            $params['message'] = strtolower($status) == 'sukses'
                ? self::MESSAGE_BUKU_SIMPAN_SUKSES
                : self::MESSAGE_BUKU_SIMPAN_GAGAL;
            if (strtolower($status) == 'tambah') {
                $params['message'] = '<div class="alert alert-success alert-dismissable">Buku Berhasil Ditambahkan<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';
            }
            if (strtolower($status) === 'error') {
                $message = get('message');
                $message = is_string($message) ? base64_decode(rawurldecode($message)) : null;
                if ($message) {
                    $params['message'] = "<div class=\"alert alert-danger alert-dismissable\">{$message}<div class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</div></div>";
                }
            }
        }

        muat_layout('buku-ubah', $params);
    }

    public static function Hapus($params)
    {
        check_redirect_admin_router();
        $buku = empty($params['id'])
            ? null
            : $params['id'];
        if (!$buku) {
            redirect(get_base_url_index('buku'));
        }
        if (abs($buku) === get_current_user_id()) {
            $referer = isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], get_host()) !== false
                ? $_SERVER['HTTP_REFERER']
                : get_base_url_index('buku');
            redirect($referer);
        }

        $buku_exist = database_get_buku_by_id($buku);
        $url = get_base_url_index('buku/hapus/'. $buku);
        if (get_method() === 'POST') {
            $data = post('user');
            if (empty($data['id']) || abs($data['id']) !== abs($buku)) {
                redirect($url.'?status=error');
            }
            $deleted = delete_buku_by_id($data['id']);
            if ($deleted) {
                if ($buku_exist && !empty($buku_exist['path_gambar']) && strpos($buku_exist['path_gambar'], '..') === false) {
                    $path = get_path_upload_gambar();
                    unlink($path . DIRECTORY_SEPARATOR . $buku_exist['path_gambar']);
                    unlink($path . DIRECTORY_SEPARATOR . 'thumb-' . $buku_exist['path_gambar']);
                }

                redirect(get_base_url_index('buku?status=deleted'));
            }
            redirect($url.'?status=error');
        }

        if ($buku_exist && get('status') === 'error') {
            $params['message'] = '<div class="alert alert-danger alert-dismissable">Gagal Menghapus Buku<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';
        }

        $params['buku_id'] = $buku;
        muat_layout('buku-hapus', $params);
    }

    public static function Detail($params)
    {
        check_redirect_login_router();
        $id = empty($params['id'])
            ? null
            : $params['id'];
        if (!is_numeric($id)) {
            redirect(get_base_url_index('buku'));
        }
        muat_layout('buku-detail', $params);
    }

    public static function Listing($params)
    {
        check_redirect_login_router();
        muat_layout('buku', $params);
    }
}
