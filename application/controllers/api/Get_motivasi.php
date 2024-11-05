<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Get_motivasi extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Konfigurasi CORS untuk mengizinkan akses dari semua domain
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');

        $this->load->database();  // Memuat database
    }

    // Mengambil data motivasi dengan metode GET
    function index_get()
    {
        $id = $this->get('id');  // Ambil parameter id dari request GET

        // Jika ID tidak diberikan, ambil semua data motivasi
        if ($id === null) {
            $motivasi = $this->db->get('motivasi')->result();
        } else {
            $this->db->where('id', $id);  // Ambil data motivasi berdasarkan ID
            $motivasi = $this->db->get('motivasi')->row();
        }

        // Cek apakah data motivasi ditemukan
        if ($motivasi) {
            $this->response($motivasi, REST_Controller::HTTP_OK);  // Kirim respon sukses
        } else {
            $this->response(['status' => 'not_found'], REST_Controller::HTTP_NOT_FOUND);  // Kirim respon data tidak ditemukan
        }
    }

    // Menambah motivasi baru dengan metode POST
    function index_post()
    {
        // Ambil data dari body request POST
        $data = [
            'isi_motivasi' => $this->post('isi_motivasi'),
            'iduser' => $this->post('iduser'),
            'tanggal_input' => date('Y-m-d H:i:s'),  // Set tanggal saat motivasi ditambahkan
        ];

        // Validasi: pastikan 'isi_motivasi' dan 'iduser' tidak kosong
        if (empty($data['isi_motivasi']) || empty($data['iduser'])) {
            return $this->response(['status' => 'fail', 'message' => 'Required fields are missing'], REST_Controller::HTTP_BAD_REQUEST);
        }

        // Lakukan insert data ke database
        $insert = $this->db->insert('motivasi', $data);
        if ($insert) {
            $this->response(['status' => 'success', 'data' => $data], REST_Controller::HTTP_CREATED);  // Kirim respon sukses
        } else {
            $this->response(['status' => 'fail', 'message' => 'Gagal menambah data'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);  // Kirim respon gagal
        }
    }

    // Memperbarui data motivasi dengan metode PUT
    function index_put()
    {
        $id = $this->put('id');  // Ambil ID dari request PUT
        if (empty($id)) {
            return $this->response(['status' => 'fail', 'message' => 'ID is required'], REST_Controller::HTTP_BAD_REQUEST);  // ID harus disertakan
        }

        // Ambil data yang akan diperbarui dari body request PUT
        $data = [
            'isi_motivasi' => $this->put('isi_motivasi'),
            'iduser' => $this->put('iduser'),
            'tanggal_update' => date('Y-m-d H:i:s'),  // Set tanggal saat motivasi diperbarui
        ];

        // Lakukan update data ke database
        $this->db->where('id', $id);
        $update = $this->db->update('motivasi', $data);
        if ($update) {
            $this->response(['status' => 'success', 'data' => $data], REST_Controller::HTTP_OK);  // Kirim respon sukses
        } else {
            $this->response(['status' => 'fail', 'message' => 'Update failed'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);  // Kirim respon gagal
        }
    }

    // Menghapus data motivasi berdasarkan ID dengan metode DELETE
    function index_delete()
    {
        $id = $this->delete('id');  // Ambil ID dari request DELETE
        if (empty($id)) {
            return $this->response(['status' => 'fail', 'message' => 'ID is required'], REST_Controller::HTTP_BAD_REQUEST);  // ID harus disertakan
        }

        // Hapus data motivasi berdasarkan ID
        $this->db->where('id', $id);
        $delete = $this->db->delete('motivasi');
        if ($delete) {
            $this->response(['status' => 'success'], REST_Controller::HTTP_OK);  // Kirim respon sukses
        } else {
            $this->response(['status' => 'fail', 'message' => 'Delete failed'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);  // Kirim respon gagal
        }
    }
}
