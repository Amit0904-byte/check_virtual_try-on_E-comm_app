<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tryon_model extends CI_Model {
    public function create($data) { $this->db->insert('tryon_history', $data); return $this->db->insert_id(); }
    public function history($user_id = NULL, $limit = 50) {
        $this->db->select('tryon_history.*, products.name AS product_name, products.product_image, users.name AS user_name, users.email')
            ->from('tryon_history')->join('products', 'products.id = tryon_history.product_id')
            ->join('users', 'users.id = tryon_history.user_id', 'left');
        if ($user_id !== NULL) $this->db->where('tryon_history.user_id', (int) $user_id);
        return $this->db->order_by('tryon_history.created_at', 'DESC')->limit((int) $limit)->get()->result_array();
    }
    public function recent_products($user_id, $limit = 6) {
        return $this->db->select('products.*, MAX(tryon_history.created_at) AS last_tried')->from('tryon_history')
            ->join('products', 'products.id = tryon_history.product_id')->where('tryon_history.user_id', (int) $user_id)
            ->group_by('products.id')->order_by('last_tried', 'DESC')->limit((int) $limit)->get()->result_array();
    }
    public function count_all() { return $this->db->count_all('tryon_history'); }
}
