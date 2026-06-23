<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller {
    public function __construct() { parent::__construct(); $this->load->model('Product_model'); }
    public function index() { $this->render('product_list', array('title' => 'The Collection', 'products' => $this->Product_model->all())); }
    public function toggle_favorite($id) {
        $this->require_login();
        if (!$this->Product_model->find($id)) show_404();
        $saved = $this->Product_model->toggle_favorite(current_user()['id'], $id);
        $this->session->set_flashdata('success', $saved ? 'Added to your atelier.' : 'Removed from favorites.');
        redirect($this->input->server('HTTP_REFERER') ?: 'products');
    }
}
