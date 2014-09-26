<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Profile Class
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Profile Controller
 * 
 */
class Profile extends CI_Controller
{
    //private $error = array();
    private $error = '';

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * index page for this controller
     */
    public function index()
    {
        $id = id_auth_user();
        if (!$id) {
            redirect();
        }
        $this->load->model('Admin_model');
        $this->data['form_action'] = site_url('profile');
        $this->data['changepass_form'] = site_url('profile/change_pass');
        $detail = $this->Admin_model->getAdmin($id);
        $post = $detail;
        
        if ($this->input->post()) {
            if ($this->validateForm()) {
                $post = purify($this->input->post());
                $now = date('Y-m-d H:i:s');
                $data_post = array(
                    'name' => $post['name'],
                    'email' => strtolower($post['email']),
                    'phone' => $post['phone'],
                    'alamat' => $post['alamat'],
                    'modify_date' => $now,
                );

                // update data
                $this->Admin_model->UpdateRecord($id, $data_post);
                $post_image = $_FILES;
                if ($post_image['image']['tmp_name']) {
                    if ($detail['image'] != '' && file_exists(IMG_UPLOAD_DIR.'admin/'.$detail['image'])) {
                        unlink(IMG_UPLOAD_DIR.'admin/'.$detail['image']);
                        unlink(IMG_UPLOAD_DIR.'admin/tmb_'.$detail['image']);
                        unlink(IMG_UPLOAD_DIR.'admin/sml_'.$detail['image']);
                    }
                    $filename = 'adm_'.url_title($post['name'],'_',true).md5plus($id);
                    $picture_db = file_copy_to_folder($post_image['image'], IMG_UPLOAD_DIR.'admin/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'admin/'.$picture_db, IMG_UPLOAD_DIR.'admin/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'admin/'.$picture_db, IMG_UPLOAD_DIR.'admin/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);

                    $this->Admin_model->UpdateRecord($id,array('image'=>$picture_db));
                }

                $user_session = array($this->session->userdata('ADM_SESS'));
                foreach ($user_session as $key => $val) {
                    $user_session[$key]['admin_name'] = $post['name'];
                    $user_session[$key]['admin_email'] = strtolower($post['email']);
                }
                foreach ($user_session as $key => $val) {
                    $user_sess[$val] = $key[$val];
                }
                $new_session = $val;
                $this->session->set_userdata('ADM_SESS', $new_session);

                $this->session->set_flashdata('success_msg', 'Your Profile has been updated.<br/>');

                redirect('profile');
            }
        }
        $this->data['post'] = $post;
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * change user password
     */
    public function change_pass() {
        $this->layout = 'none';
        if (is_ajax_requested()) {
            if ($this->input->post()) {
                $json = array();
                $post = $this->input->post();
                $id = id_auth_user();
                $this->load->model('Admin_model');
                $detail = $this->Admin_model->getAdmin($id);
                if (!$id || !$detail) {
                    $json['location'] = site_url('home');
                }
                if (!$this->validatePassword()) {
                    $json['error'] = $this->error;
                }
                if (!$json) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'userpass'=>md5plus($post['new_password']),
                        'modify_date'=>$now
                    );
                    $this->Admin_model->UpdateRecord($id,$data);
                    $json['success'] = 'Your Password has been changed.<br/>';
                    $this->session->set_flashdata('success_msg',$json['success']);
                    $json['redirect'] = site_url('profile');
                }
                echo json_encode($json);
            }
        }
    }
    
    /**
     * validate form
     * @return boolean
     */
    private function validateForm()
    {
        $this->load->model('Admin_model');
        $id = id_auth_user();
        $post = purify($this->input->post());
        $err = '';

        if ($post['name'] == '') {
            $err .= 'Please insert Name.<br/>';
        } else {
            if ((utf8_strlen($post['name']) < 1) || (utf8_strlen($post['name']) > 32)) {
                $err .= 'Please insert Name.<br/>';
            }
        }

        if ($post['email'] == '') {
            $err .= 'Please insert Email.<br/>';
        } else {
            if (!mycheck_email($post['email'])) {
                $err .= 'Please insert correct Email.<br/>';
            } else {
                if (!$this->Admin_model->checkExistsEmail($post['email'], $id)) {
                    $err .= 'Email already exists, please input different Email.<br/>';
                }
            }
        }

        if (($post['phone'] != '') && (!ctype_digit($post['phone']))) {
            $err .= 'Please insert correct Phone.<br/>';
        }
        
        $post_image = $_FILES;
        if (!empty($post_image['image']['tmp_name'])) {
            $check_picture = validatePicture('image');
            if (!empty($check_picture)) {
                $err .= $check_picture.'<br/>';
            }
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * validate change password form
     * @return boolean
     */
    private function validatePassword()
    {
        $this->load->model('Admin_model');
        $id = id_auth_user();
        $post = purify($this->input->post());
        $err = '';
        $detail = $this->Admin_model->getAdmin($id);
        $pass = md5plus($post['old_password']);
        if ($post['old_password'] == '') {
            $err .= 'Please insert Old Password.<br/>';
        } else {
            if ($pass != $detail['userpass']) {
                $err .= 'Your Old Password is incorrect.<br/>';
            }
        }
        if ($post['new_password'] == '') {
            $err .= 'Please input your New Password.<br/>';
        } else {
            if (utf8_strlen($post['new_password']) <= 6) {
                $err .= 'Please input New Password more than 6 characters.<br/>';
            } else {
                if ($post['conf_password'] != $post['new_password']) {
                    $err .= 'Your Confirmation Password is not same with Your New Password.<br/>';
                }
            }
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }

}
/* End of file profile.php */
/* Location: ./application/controllers/profile.php */


