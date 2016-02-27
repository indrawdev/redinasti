<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Admin Class
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Admin Controller
 * 
 */
class Admin extends CI_Controller
{
    //private $error = array();
    private $error = '';

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Index Page for this controller.
     */
    public function index()
    {
        /**
         * let this function empty just for generating layout
         */
        $this->data['add_url'] = site_url('admin/add');
        $this->data['export_excel_url'] = site_url('admin/export_excel');
        $this->data['list_data'] = site_url('admin/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_create_date'] = "DATE_FORMAT(CURRENT_TIMESTAMP, '%d  %b  %Y %H:%i:%S')";
        $alias['search_username'] = "a.username";
        $alias['search_name'] = "a.name";
        $alias['search_email'] = "a.email";
        $alias['search_auth_group'] = "b.auth_group";
        $query = "
            select 
                a.id_auth_user as id, 
                a.id_auth_user as idx, 
                DATE_FORMAT(CURRENT_TIMESTAMP, '%d  %b  %Y %H:%i:%S') as create_date,
                a.*,
                b.auth_group
            from " . $this->db->dbprefix('auth_user') . " a 
            left join " . $this->db->dbprefix('auth_group') . " b on b.id_auth_group = a.id_auth_group
            ".((!is_superadmin()) ? " where a.is_superadmin='0' " : "where 1")."
        ";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add page
     */
    public function add()
    {
        $this->load->model('Admin_model');
        $this->data['form_action'] = site_url('admin/add');
        $this->data['page_title'] = 'Tambah Admin';
        $this->data['empty_msg'] = '';
        $this->data['cancel_url'] = site_url('admin');
        $this->data['groups'] = $this->Admin_model->getAdminGroup();
        $this->data['divisions'] = $this->Admin_model->getDivisions();
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $post['status'] = (isset($post['status'])) ? 1 : 0;
                $post['is_superadmin'] = (isset($post['is_superadmin'])) ? 1 : 0;
                $post['email'] = strtolower($post['email']);
                $post['userpass'] = md5plus($post['password']);
                unset($post['password']);
                unset($post['conf_password']);

                // insert data
                $last_id = $this->Admin_model->InsertRecord($post);
                if ($last_id) {
                    $post_image = $_FILES;
                    if ($post_image['image']['tmp_name']) {
                        $filename = 'adm_'.url_title($post['name'],'_',true).md5plus($last_id);
                        $picture_db = file_copy_to_folder($post_image['image'], IMG_UPLOAD_DIR.'admin/', $filename);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'admin/'.$picture_db, IMG_UPLOAD_DIR.'admin/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'admin/'.$picture_db, IMG_UPLOAD_DIR.'admin/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);

                        $this->Admin_model->UpdateRecord($last_id,array('image'=>$picture_db));
                    }
                    $this->session->set_flashdata('success_msg', 'Tambah data berhasil.<br/>');
                } else {
                    $this->session->set_flashdata('tmp_msg', 'Gagal.<br/>');
                }
                redirect('admin');
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
        $this->data['content_layout'] = 'form';
    }
    
    /**
     * edit page
     */
    public function edit($id=0)
    {
        $id = (int)$id;
        if (!$id) {
            redirect('admin');
        }
        $this->load->model('Admin_model');
        $this->data['page_title'] = 'Edit Admin';
        $this->data['form_action'] = site_url('admin/edit/'.$id);
        $this->data['empty_msg'] = '(Kosongkan jika Anda tidak ingin merubah Password)';
        $this->data['cancel_url'] = site_url('admin');
        $detail = $this->Admin_model->getAdmin($id);
        if (!$detail) {
            redirect('admin');
        }
        if ($detail['is_superadmin'] == 1) {
            if (!is_superadmin()) {
                $this->session->set_flashdata('tmp_msg', 'Anda tidak punya hak akses untuk merubah data ini.<br/>');
                redirect('admin');
            }
        }
        $this->data['post'] = $detail;
        $this->data['groups'] = $this->Admin_model->getAdminGroup();
        $this->data['divisions'] = $this->Admin_model->getDivisions();
        if ($this->input->post()) {
            $post = purify($this->input->post());
            if ($this->validateForm($id)) {
                $post['modify_date'] = date('Y-m-d H:i:s');
                $post['status'] = (isset($post['status'])) ? 1 : 0;
                $post['is_superadmin'] = (isset($post['is_superadmin'])) ? 1 : 0;
                $post['email'] = strtolower($post['email']);

                if ($post['password'] != '') {
                    $post['userpass'] = md5plus($post['password']);
                }
                unset($post['password']);
                unset($post['conf_password']);
                
                // update data
                $this->Admin_model->UpdateRecord($id,$post);
                
                // now change session if user is edit themselve
                if (id_auth_user() == $id) {
                    $user_session = array($this->session->userdata('ADM_SESS'));
                    foreach ($user_session as $key => $val) {
                        $user_session[$key]['admin_name'] = $post['name'];
                        $user_session[$key]['admin_id_auth_group'] = $post['id_auth_group'];
                        $user_session[$key]['admin_id_division'] = $post['id_division'];
                        $user_session[$key]['admin_type'] = (isset($post['is_superadmin'])) ? 'superadmin' : 'admin';
                        $user_session[$key]['admin_email'] = strtolower($post['email']);
                    }
                    foreach ($user_session as $key => $val) {
                        $user_sess[$val] = $key[$val];
                    }
                    $new_session = $val;
                    $this->session->set_userdata('ADM_SESS', $new_session);
                }
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
                $this->session->set_flashdata('success_msg', 'Edit data berhasil.<br/>');

                redirect('admin');
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
        $this->data['content_layout'] = 'form';
    }
    
    /**
     * delete record
     * @param int $id
     */
    public function delete($id=0) {
        $this->load->model('Admin_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Admin_model->getAdmin($id);
                    if ($detail) {
                        if ($id == id_auth_user()) {
                            $this->session->set_flashdata('message','Gagal. Anda tidak bisa menghapus data Anda sendiri.');
                        } else {
                            if (is_superadmin()) {
                                $this->Admin_model->DeleteRecord($id);
                                $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                            } else {
                                $this->session->set_flashdata('message','Gagal. Anda tidak punya hak akses untuk menhapus data ini, silakan hubungi Administrator.');
                            }
                        }
                    } else {
                        $this->session->set_flashdata('message','Tidak ada data.');
                    }
                }
            }
        } else {
            redirect('admin');
        }
    }
    
    /**
     * validate form
     * @return boolean
     */
    private function validateForm($id=0)
    {
        $this->load->model('Admin_model');
        $post = purify($this->input->post());
        $err = '';

        if ($post['username'] == '') {
            $err .= 'Mohon isi Username.<br/>';
        } else {
            if ((utf8_strlen($post['username']) < 3) || (utf8_strlen($post['username']) > 32)) {
                $err .= 'Mohon isi Username dengan benar (min. 3, max. 32 karakter).<br/>';
            } else {
                if (!$this->Admin_model->checkExistsUsername($post['username'], $id)) {
                    $err .= 'Username <strong>'.$post['username'].'</strong> telah digunakan. Mohon gunakan Username yang lain.<br/>';
                }
            }
        }
        
        if ($post['id_auth_group'] == '' || $post['id_auth_group'] == 0) {
            $err .= 'Mohon isi Grup.<br/>';
        }

        if ($post['name'] == '') {
            $err .= 'Mohon isi Nama.<br/>';
        } else {
            if ((utf8_strlen($post['name']) < 1) || (utf8_strlen($post['name']) > 32)) {
                $err .= 'Mohon isi Nama dengan benar.<br/>';
            }
        }

        if ($post['email'] == '') {
            $err .= 'Mohon isi Email.<br/>';
        } else {
            if (!mycheck_email($post['email'])) {
                $err .= 'Mohon isi Email dengan benar.<br/>';
            } else {
                if (!$this->Admin_model->checkExistsEmail($post['email'], $id)) {
                    $err .= 'Email <strong>'.$post['email'].'</strong> telah digunakan. Mohon gunakan Email lain.<br/>';
                }
            }
        }
        
        if (!$id) {
            if ($post['password'] == '') {
                $err .= 'Mohon isi Password.<br/>';
            } else {
                if (utf8_strlen($post['password']) <= 6) {
                    $err .= 'Mohon isi Password lebih dari 6 karakter.<br/>';
                } else {
                    if ($post['conf_password'] != $post['password']) {
                        $err .= 'Konfirmasi Password yang Anda masukan tidak sama.<br/>';
                    }
                }
            }
        } else {
            if (utf8_strlen($post['password']) > 0) {
                if (utf8_strlen($post['password']) <= 6) {
                    $err .= 'Mohon isi Password lebih dari 6 karakter.<br/>';
                } else {
                    if ($post['conf_password'] != $post['password']) {
                        $err .= 'Konfirmasi Password yang Anda masukan tidak sama.<br/>';
                    }
                }
            }
        }

        if (($post['phone'] != '') && (!ctype_digit($post['phone']))) {
            $err .= 'Mohon isi Telepon dengan benar.<br/>';
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
    
}
/* End of file admin.php */
/* Location: ./application/controllers/admin.php */


