<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {
    public function __construct() { parent::__construct(); $this->load->model('User_model'); }
    public function login() {
        if (is_logged_in()) redirect('try-on');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');
        if ($this->form_validation->run()) {
            $user = $this->User_model->find_by_email($this->input->post('email', TRUE));
            if ($user && password_verify($this->input->post('password'), $user['password'])) {
                $this->session->sess_regenerate(TRUE);
                $this->session->set_userdata('user', array('id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'is_admin' => (bool) $user['is_admin']));
                redirect($this->input->get('next', TRUE) ?: ($user['is_admin'] ? 'admin' : 'try-on'));
            }
            $data['error'] = 'Email or password is incorrect.';
        }
        $data['title'] = 'Welcome Back'; $data['mode'] = 'login'; $this->render('auth/form', $data);
    }
    public function register() {
        if (is_logged_in()) redirect('try-on');
        $this->form_validation->set_rules('name', 'Name', 'required|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        if ($this->form_validation->run()) {
            $id = $this->User_model->create(array('name' => $this->input->post('name', TRUE), 'email' => $this->input->post('email', TRUE), 'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)));
            $this->session->sess_regenerate(TRUE);
            $this->session->set_userdata('user', array('id' => $id, 'name' => $this->input->post('name', TRUE), 'email' => $this->input->post('email', TRUE), 'is_admin' => FALSE));
            redirect('try-on');
        }
        $data = array('title' => 'Create Account', 'mode' => 'register'); $this->render('auth/form', $data);
    }
    public function logout() { $this->session->sess_destroy(); redirect('try-on'); }
}
