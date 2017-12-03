<?php
class RouterBuku
{
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

        $pencarian = cari_buku_by_judul($nama_buku);
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

        $pencarian = cari_buku_by_pengarang($nama_pengarang);
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

        $pencarian = cari_pengarang_dari_buku($nama_pengarang);
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

    public static function Baru($params)
    {
        check_redirect_admin_router();
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

        muat_layout('buku-ubah', $params);
    }

    public static function Hapus($params)
    {
        check_redirect_admin_router();
        $id = empty($params['id'])
            ? null
            : $params['id'];
//        print_r($params);exit;
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
//        print_r($params);exit;
    }

    public static function List($params)
    {
        check_redirect_login_router();
        muat_layout('buku', $params);
    }

    public static function Pinjaman($params)
    {
        check_redirect_login_router();
        muat_layout('pinjaman-'.(is_admin() ? 'admin' : 'anggota'));
    }
}