<?php defined('BASEPATH') OR exit('No direct script access allowed');

class VirtualTryOn extends MY_Controller {
    public function __construct() { parent::__construct(); $this->load->model(array('Product_model', 'Tryon_model')); }
    public function index() {
        $products = $this->Product_model->all();
        $data = array('title' => 'Virtual Atelier', 'products' => $products, 'selected' => !empty($products) ? $products[0] : NULL);
        if (is_logged_in()) {
            $data['recent'] = $this->Tryon_model->recent_products(current_user()['id']);
            $data['favorites'] = $this->Product_model->favorites(current_user()['id']);
        }
        $this->render('virtual_tryon', $data);
    }
}
