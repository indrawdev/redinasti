<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Login page
 * @author : ivan lubis 
 * 
 */

class Login extends CI_Controller
{

    /**
     * Index page for this controller
     */
    public function index()
    {
        $post = $this->input->post();
        if (!$post) {
            $this->layout = 'blank';
            $this->data['tmp_msg'] = $this->session->flashdata('tmp_msg');
        } else {
            $this->load->model('Auth_model');
            $this->layout = 'none';
            $email = $post['uid'];
            $pwd = $post['pas'];
            
            // auth logic goes here
            $this->Auth_model->check_login($email,$pwd);
        }
    }

    public function logout()
    {
        $this->layout = 'none';
        $this->session->sess_destroy();
        redirect('login');
    }

}

/* End of file login.php */
/* Location: ./application/controllers/login.php */

