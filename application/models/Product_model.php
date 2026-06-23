<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {
    private $table = 'products';

    public function all($active_only = TRUE) {
        if ($active_only) $this->db->where('status', 1);
        return $this->db->order_by('created_at', 'DESC')->get($this->table)->result_array();
    }

    public function find($id, $active_only = TRUE) {
        $this->db->where('id', (int) $id);
        if ($active_only) $this->db->where('status', 1);
        return $this->db->get($this->table)->row_array();
    }

    public function create($data) { $this->db->insert($this->table, $data); return $this->db->insert_id(); }
    public function update($id, $data) { return $this->db->where('id', (int) $id)->update($this->table, $data); }
    public function delete($id) { return $this->db->where('id', (int) $id)->delete($this->table); }
    public function count_active() { return $this->db->where('status', 1)->count_all_results($this->table); }

    public function favorites($user_id) {
        return $this->db->select('products.*')->from('favorites')->join('products', 'products.id = favorites.product_id')
            ->where(array('favorites.user_id' => (int) $user_id, 'products.status' => 1))->order_by('favorites.created_at', 'DESC')->get()->result_array();
    }

    public function toggle_favorite($user_id, $product_id) {
        $where = array('user_id' => (int) $user_id, 'product_id' => (int) $product_id);
        $exists = $this->db->get_where('favorites', $where)->row_array();
        if ($exists) { $this->db->delete('favorites', $where); return FALSE; }
        $this->db->insert('favorites', $where); return TRUE;
    }
}
