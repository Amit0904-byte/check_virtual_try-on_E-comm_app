<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    public function find($id) { return $this->db->get_where('users', array('id' => (int) $id))->row_array(); }
    public function find_by_email($email) { return $this->db->get_where('users', array('email' => strtolower(trim($email))))->row_array(); }
    public function create($data) { $data['email'] = strtolower(trim($data['email'])); $this->db->insert('users', $data); return $this->db->insert_id(); }
    public function count_all() { return $this->db->count_all('users'); }
}
