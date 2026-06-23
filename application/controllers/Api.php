<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
    public function __construct() { parent::__construct(); $this->load->model(array('Product_model', 'Tryon_model')); }
    private function json($data, $status = 200) { return $this->output->set_status_header($status)->set_content_type('application/json')->set_output(json_encode($data)); }
    public function products() { return $this->json(array('success' => TRUE, 'data' => $this->Product_model->all())); }
    public function product($id) { $product = $this->Product_model->find($id); return $product ? $this->json(array('success' => TRUE, 'data' => $product)) : $this->json(array('success' => FALSE, 'message' => 'Product not found.'), 404); }
    public function save_tryon() {
        if (!is_logged_in()) return $this->json(array('success' => FALSE, 'message' => 'Sign in to save your look.'), 401);
        $product_id = (int) $this->input->post('product_id');
        $image = $this->input->post('image');
        if (!$this->Product_model->find($product_id) || !is_string($image) || strpos($image, 'data:image/png;base64,') !== 0) return $this->json(array('success' => FALSE, 'message' => 'Invalid try-on data.'), 422);
        $binary = base64_decode(substr($image, 22), TRUE);
        if ($binary === FALSE || strlen($binary) > 8 * 1024 * 1024 || substr($binary, 0, 8) !== "\x89PNG\r\n\x1a\n") return $this->json(array('success' => FALSE, 'message' => 'Invalid or oversized PNG.'), 422);
        $dir = FCPATH.'assets/uploads/screenshots/'; if (!is_dir($dir)) mkdir($dir, 0755, TRUE);
        $name = 'tryon_'.current_user()['id'].'_'.bin2hex(random_bytes(8)).'.png';
        if (file_put_contents($dir.$name, $binary, LOCK_EX) === FALSE) return $this->json(array('success' => FALSE, 'message' => 'Could not save screenshot.'), 500);
        $path = 'uploads/screenshots/'.$name;
        $id = $this->Tryon_model->create(array('user_id' => current_user()['id'], 'product_id' => $product_id, 'screenshot_path' => $path));
        return $this->json(array('success' => TRUE, 'message' => 'Look saved to your history.', 'data' => array('id' => $id, 'url' => asset_url($path)), 'csrf' => $this->security->get_csrf_hash()), 201);
    }
    public function tryon_history($user_id) {
        if (!is_logged_in()) return $this->json(array('success' => FALSE, 'message' => 'Authentication required.'), 401);
        if (!is_admin() && (int) current_user()['id'] !== (int) $user_id) return $this->json(array('success' => FALSE, 'message' => 'Forbidden.'), 403);
        return $this->json(array('success' => TRUE, 'data' => $this->Tryon_model->history($user_id)));
    }
    public function size_recommendation() {
        $height = (float) $this->input->post('height'); $chest = (float) $this->input->post('chest'); $waist = (float) $this->input->post('waist');
        if ($height < 120 || $height > 220 || $chest < 60 || $chest > 150 || $waist < 45 || $waist > 150) return $this->json(array('success' => FALSE, 'message' => 'Enter realistic measurements in centimetres.'), 422);
        $score = max($chest, $waist + 18); $size = $score < 84 ? 'XS' : ($score < 92 ? 'S' : ($score < 100 ? 'M' : ($score < 110 ? 'L' : ($score < 122 ? 'XL' : 'XXL'))));
        return $this->json(array('success' => TRUE, 'data' => array('size' => $size, 'confidence' => 'Fit estimate based on chest and waist measurements.')));
    }
}
