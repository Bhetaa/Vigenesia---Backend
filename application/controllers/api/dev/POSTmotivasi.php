<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class POSTmotivasi extends REST_Controller
{
    function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->database();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        
        $this->load->model('motivasi');
    }

    public function index_post()
    {
        // Ambil data dari input POST
        $materiData = array(
            'isi_motivasi' => $this->input->post("isi_motivasi"),
            'iduser' => $this->input->post("iduser")
        );

        // Validasi input
        if (empty($materiData['isi_motivasi']) || empty($materiData['iduser'])) {
            // Jika input tidak lengkap, kembalikan respons error
            $this->response([
                'message' => 'Data tidak lengkap. Pastikan semua field terisi.',
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Coba untuk menyimpan data ke database
        $insert = $this->motivasi->insert($materiData);

        // Cek apakah data berhasil disimpan
        if ($insert) {
            // Jika berhasil, kembalikan respons sukses
            $this->response([
                'message' => 'Postingan berhasil ditambahkan.',
                'data' => $insert // Anda mungkin ingin mengembalikan ID yang baru disimpan
            ], REST_Controller::HTTP_CREATED); // HTTP 201 Created
        } else {
            // Jika gagal, kembalikan respons error
            $this->response([
                'message' => 'Gagal menambahkan postingan.',
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500 Internal Server Error
        }
    }
}
