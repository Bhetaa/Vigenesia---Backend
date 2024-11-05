<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class User extends REST_Controller
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
    }

    // Menampilkan hasil looping login akun user
    function index_get()
    {
        $iduser = $this->get('iduser'); // Menggunakan iduser bukan id
        if ($iduser == '') {
            $api = $this->db->get('user')->result();
        } else {
            $this->db->where('iduser', $iduser);
            $api = $this->db->get('user')->result();
        }
        $this->response($api, 200);
    }

    function index_post()
    {
        $data = array(
            'iduser' => $this->post('iduser'),
            'nama' => $this->post('nama'),
            'profesi' => $this->post('profesi'),
            'email' => $this->post('email'),
            'password' => password_hash($this->post('password'), PASSWORD_DEFAULT),
            'role_id' => $this->post('role_id'),
            'is_active' => $this->post('is_active'),
            'tanggal_input' => date('Y-m-d'), 
            'modified' => date('Y-m-d') 
        );

        // Validasi input
        if (empty($data['nama']) || empty($data['email']) || empty($data['password'])) {
            $this->response(array('status' => 'fail', 'message' => 'Nama, email, dan password harus diisi'), 400);
            return;
        }

        $insert = $this->db->insert('user', $data);

        if ($insert) {
            $this->response(array('status' => 'sukses', 'data' => $data), 201);
        } else {
            $this->response(array('status' => 'fail', 'message' => 'Gagal menambah data'), 502);
        }
    }

    function index_put()
    {
        $iduser = $this->put('iduser');
        $data = array(
            'nama' => $this->put('nama'),
            'profesi' => $this->put('profesi'),
            'email' => $this->put('email'),
            'password' => password_hash($this->put('password'), PASSWORD_DEFAULT),
            'role_id' => $this->put('role_id'),
            'is_active' => $this->put('is_active'),
            'modified' => date('Y-m-d H:i:s') 
        );

        $this->db->where('iduser', $iduser);
        $update = $this->db->update('user', $data);

        if ($update) {
            $this->response(array('status' => 'sukses', 'data' => $data), 200);
        } else {
            $this->response(array('status' => 'fail', 'message' => 'Gagal memperbarui data'), 502);
        }
    }

    function index_delete()
    {
        $iduser = $this->delete('iduser');

        if (!$iduser) {
            return $this->response(['status' => 'FAILED', 'message' => 'ID USER NOT FOUND'], 400);
        }

        // Cek apakah pengguna ada
        $this->db->where('iduser', $iduser);
        $user = $this->db->get('user')->row();

        if (!$user) {
            return $this->response(['status' => 'FAILED', 'message' => 'USER NOT FOUND'], 404);
        }

        // Hapus pengguna
        $delete = $this->db->delete('user', ['iduser' => $iduser]);

        if ($delete) {
            return $this->response(['status' => 'SUCCESS', 'message' => 'USER SUCCESSFULLY DELETED'], 200);
        }

        return $this->response(['status' => 'FAILED', 'message' => 'FAILED TO DELETE USER'], 502);
    }
}
