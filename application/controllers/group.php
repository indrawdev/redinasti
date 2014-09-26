<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Group Class
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Group Controller
 * 
 */
class Group extends CI_Controller
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
        $this->data['add_url'] = site_url('group/add');
        $this->data['export_excel_url'] = site_url('group/export_excel');
        $this->data['list_data'] = site_url('group/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_group'] = "auth_group";
        $query = "
            select 
                id_auth_group as id, 
                id_auth_group as idx, 
                " . $this->db->dbprefix('auth_group') . ".*
            from " . $this->db->dbprefix('auth_group') . " 
            ".((!is_superadmin()) ? " where is_superadmin='0' " : "where 1")."
        ";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add page
     */
    public function add()
    {
        $this->load->model('Group_model');
        $this->data['form_action'] = site_url('group/add');
        $post = array(
            'auth_group'=>'',
            'is_superadmin'=>false,
        );
        $this->data['page_title'] = 'Tambah Grup';
        $this->data['cancel_url'] = site_url('group');
        if ($this->input->post()) {
            $post = purify($this->input->post());
            if ($this->validateForm()) {
                $post['is_superadmin'] = (isset($post['is_superadmin'])) ? 1 : 0;
                $data_post = array(
                    'auth_group' => $post['auth_group'],
                    'is_superadmin' => $post['is_superadmin'],
                );

                // insert data
                $last_id = $this->Group_model->InsertRecord($data_post);
                if ($last_id) {
                    $this->session->set_flashdata('success_msg', 'Tambah Data berhasil.<br/>');
                } else {
                    $this->session->set_flashdata('tmp_msg', 'Gagal.<br/>');
                }

                redirect('group');
            }
        }
        $this->data['post'] = $post;
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
            redirect('group');
        }
        $this->load->model('Group_model');
        $this->data['page_title'] = 'Edit Grup';
        $this->data['cancel_url'] = site_url('group');
        $this->data['form_action'] = site_url('group/edit/'.$id);
        $detail = $this->Group_model->getGroup($id);
        if (!$detail) {
            redirect('group');
        }
        if ($detail['is_superadmin'] == 1) {
            if (!is_superadmin()) {
                $this->session->set_flashdata('tmp_msg', 'Anda tidak memiliki hak akes untuk merubah data ini.<br/>');
                redirect('group');
            }
        }
        $post = $detail;
        if ($this->input->post()) {
            $post = purify($this->input->post());
            if ($this->validateForm($id)) {
                $post['is_superadmin'] = (isset($post['is_superadmin'])) ? 1 : 0;
                $data_post = array(
                    'auth_group' => $post['auth_group'],
                    'is_superadmin' => $post['is_superadmin'],
                );

                // update data
                $this->Group_model->UpdateRecord($id,$data_post);
                $this->session->set_flashdata('success_msg', 'Edit data berhasil.<br/>');

                redirect('group');
            }
        }
        $this->data['post'] = $post;
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
        $this->data['content_layout'] = 'form';
    }
    
    /**
     * auth page
     * @param type $id
     */
    public function authorization($id=0) {
        $id = (int)$id;
        if (!$id) {
            redirect('group');
        }
        $this->load->model('Group_model');
        $this->data['form_action'] = site_url('group/authorization/'.$id);
        $this->data['cancel_url'] = site_url('group');
        $detail = $this->Group_model->getGroup($id);
        if (!$detail) {
            redirect('group');
        }
        $post = $detail;
        
        if ($this->input->post()) {
            $post = purify($this->input->post());
            if ($this->validateFormAuth($id)) {
                
                // update data
                $this->Group_model->UpdateAuth($id,$post['auth_menu_group']);
                $this->session->set_flashdata('success_msg', 'Berhasil.<br/>');
                
                redirect('group');
            }
        }
        $this->data['auth_menu_group'] = $this->Group_model->printAuthMenuGroup($id);
        $this->data['post'] = $post;
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * delete record
     * @param int $id
     */
    public function delete($id=0) {
        $this->load->model('Group_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Group_model->getGroup($id);
                    if ($detail) {
                        if ($id == adm_sess_usergroupid()) {
                            $this->session->set_flashdata('message','Gagal. Anda tidak bisa menghapus Grup ini.');
                        } else {
                            if (is_superadmin()) {
                                $this->Group_model->DeleteRecord($id);
                                $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                            } else {
                                $this->session->set_flashdata('message','Gagal. Anda tidak bisa menghapus Grup ini.');
                            }
                        }
                    } else {
                        $this->session->set_flashdata('message','Tidak ada data.');
                    }
                }
            }
        } else {
            redirect('group');
        }
    }
    
    /**
     * validate form
     * @return boolean
     */
    private function validateForm($id=0)
    {
        $this->load->model('Group_model');
        $post = purify($this->input->post());
        $err = '';

        if ($post['auth_group'] == '') {
            $err .= 'Mohon isi Nama Grup.<br/>';
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * validate authorization form
     * @param int $id
     * @return boolean
     */
    private function validateFormAuth($id=0) {
        $post = $this->input->post();
        $err = '';
        if ($post['auth_menu_group'] == '')
        {
            $err .= 'Mohon pilih otorisasi untuk Grup ini.<br/>';
        }
        else
        {
            if (count($post['auth_menu_group']) == 0)
            {
                $err .= 'Mohon pilih otorisasi untuk Grup ini.<br/>';
            }
            else
            {
                foreach($post['auth_menu_group'] as $row => $val)
                {
                    if (!ctype_digit($val))
                    {
                        $err .= 'Mohon pilih otorisasi untuk Grup ini dengan benar.<br/>';
                    }
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
/* End of file group.php */
/* Location: ./application/controllers/group.php */


