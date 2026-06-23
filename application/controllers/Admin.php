<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {
    public function __construct() { parent::__construct(); $this->require_admin(); $this->load->model(array('Product_model', 'Tryon_model', 'User_model')); }
    public function dashboard() {
        $this->render('admin/dashboard', array('title' => 'Admin Dashboard', 'stats' => array('products' => $this->Product_model->count_active(), 'tryons' => $this->Tryon_model->count_all(), 'users' => $this->User_model->count_all()), 'products' => $this->Product_model->all(FALSE), 'history' => $this->Tryon_model->history(NULL, 6)));
    }
    public function create() { $this->product_form(); }
    public function edit($id) { $product = $this->Product_model->find($id, FALSE); if (!$product) show_404(); $this->product_form($product); }
    private function product_form($product = NULL) {
        $this->form_validation->set_rules('name', 'Product name', 'required|max_length[150]');
        $this->form_validation->set_rules('category', 'Category', 'required|max_length[80]');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric|greater_than_equal_to[0]');
        if ($this->form_validation->run()) {
            $data = array('name' => $this->input->post('name', TRUE), 'category' => $this->input->post('category', TRUE), 'price' => $this->input->post('price'), 'description' => $this->input->post('description', TRUE), 'status' => $this->input->post('status') ? 1 : 0);
            $product_upload = $this->upload_image('product_image', 'products', FALSE);
            $tryon_upload = $this->upload_image('virtual_tryon_image', 'virtual_tryon', TRUE);
            if ($product_upload) $data['product_image'] = $product_upload;
            if ($tryon_upload) $data['virtual_tryon_image'] = $tryon_upload;
            if (empty($this->upload_error)) {
                $product ? $this->Product_model->update($product['id'], $data) : $this->Product_model->create($data);
                $this->session->set_flashdata('success', 'Product saved successfully.'); redirect('admin');
            }
        }
        $this->render('admin/product_form', array('title' => $product ? 'Edit Product' : 'Add Product', 'product' => $product, 'upload_error' => isset($this->upload_error) ? $this->upload_error : ''));
    }
    private function upload_image($field, $folder, $png_only) {
        if (empty($_FILES[$field]['name'])) return NULL;
        $config = array('upload_path' => FCPATH.'assets/uploads/'.$folder.'/', 'allowed_types' => $png_only ? 'png' : 'jpg|jpeg|png|webp', 'max_size' => 5120, 'encrypt_name' => TRUE, 'file_ext_tolower' => TRUE);
        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0755, TRUE);
        $this->load->library('upload', $config, $field.'_upload'); $uploader = $field.'_upload';
        if (!$this->{$uploader}->do_upload($field)) { $this->upload_error = strip_tags($this->{$uploader}->display_errors('', '')); return NULL; }
        $data = $this->{$uploader}->data();
        if ($png_only && @getimagesize($data['full_path'])['mime'] !== 'image/png') { @unlink($data['full_path']); $this->upload_error = 'The try-on asset must be a valid PNG.'; return NULL; }
        return 'uploads/'.$folder.'/'.$data['file_name'];
    }
    public function delete($id) {
        $product = $this->Product_model->find($id, FALSE); if (!$product) show_404();
        $this->Product_model->delete($id); $this->session->set_flashdata('success', 'Product deleted.'); redirect('admin');
    }
    public function history() { $this->render('admin/history', array('title' => 'Try-On History', 'history' => $this->Tryon_model->history(NULL, 200))); }
}
