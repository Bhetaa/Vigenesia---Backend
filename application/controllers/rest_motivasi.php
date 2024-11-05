<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Rest_motivasi extends REST_Controller
{
    function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->database();
    }

    // Menampilkan semua motivasi atau satu berdasarkan ID
    function index_get()
    {
        $id = $this->get('id');

        if ($id === null) {
            $motivasi = $this->db->get('motivasi')->result();
        } else {
            $this->db->where('id', $id);
            $motivasi = $this->db->get('motivasi')->row();
        }

        // Cek apakah data ditemukan
        if ($motivasi) {
            $this->response($motivasi, REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'not_found'], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    // Menambah motivasi baru
    function index_post()
    {
        $data = [
            'isi_motivasi' => $this->post('isi_motivasi'),
            'iduser' => $this->post('iduser'),
            'tanggal_input' => date('Y-m-d H:i:s'),
        ];

        // Validasi input
        if (empty($data['isi_motivasi']) || empty($data['iduser'])) {
            return $this->response(['status' => 'fail', 'message' => 'Required fields are missing'], REST_Controller::HTTP_BAD_REQUEST);
        }

        $insert = $this->db->insert('motivasi', $data);
        if ($insert) {
            $this->response(['status' => 'success', 'data' => $data], REST_Controller::HTTP_CREATED);
        } else {
            $this->response(['status' => 'fail', 'message' => 'Gagal menambah data'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Memperbarui motivasi yang ada
    function index_put()
    {
        $id = $this->put('id');
        if (empty($id)) {
            return $this->response(['status' => 'fail', 'message' => 'ID is required'], REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = [
            'isi_motivasi' => $this->put('isi_motivasi'),
            'iduser' => $this->put('iduser'),
            'tanggal_update' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('id', $id);
        $update = $this->db->update('motivasi', $data);
        if ($update) {
            $this->response(['status' => 'success', 'data' => $data], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'fail', 'message' => 'Update failed'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Menghapus motivasi berdasarkan ID
    function index_delete()
    {
        $id = $this->delete('id');
        if (empty($id)) {
            return $this->response(['status' => 'fail', 'message' => 'ID is required'], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->db->where('id', $id);
        $delete = $this->db->delete('motivasi');
        if ($delete) {
            $this->response(['status' => 'success'], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => 'fail', 'message' => 'Delete failed'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
