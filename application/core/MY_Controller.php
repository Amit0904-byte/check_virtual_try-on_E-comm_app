<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    protected function render($view, $data = array()) {
        $data['content_view'] = $view;
        $this->load->view('layouts/app', $data);
    }
    protected function require_login() {
        if (!is_logged_in()) { $this->session->set_flashdata('error', 'Please sign in to continue.'); redirect('login?next='.rawurlencode(uri_string())); exit; }
    }
    protected function require_admin() {
        if (!is_admin()) { show_error('Administrator access required.', 403); exit; }
    }
}
