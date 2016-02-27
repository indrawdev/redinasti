<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Menu Class
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Menu Controller
 * 
 */
class Menu extends CI_Controller
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
        $this->data['add_url'] = site_url('menu/add');
        $this->data['export_excel_url'] = site_url('menu/export_excel');
        $this->data['list_data'] = site_url('menu/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_menu'] = "a.menu";
        $alias['search_file'] = "a.name";
        $alias['search_parent'] = "b.menu";
        $query = "
            select 
                a.id_auth_menu as id, 
                a.id_auth_menu as idx, 
                a.*,
                b.menu as parent_menu
            from " . $this->db->dbprefix('auth_menu') . " a 
            left join " . $this->db->dbprefix('auth_menu') . " b on b.id_auth_menu = a.parent_auth_menu
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
        $this->load->model('Menu_model');
        $this->data['page_title'] = 'Tambah Menu';
        $this->data['cancel_url'] = site_url('menu');
        $this->data['form_action'] = site_url('menu/add');
        $post = array(
            'menu'=>'',
            'file'=>'',
            'parent_auth_menu'=>0,
            'position'=>($this->Menu_model->getMaxPosition()+1),
            'is_superadmin'=>false,
        );
        if ($this->input->post()) {
            $post = purify($this->input->post());
            if ($this->validateForm()) {
                $post['is_superadmin'] = (isset($post['is_superadmin'])) ? 1 : 0;
                $data_post = array(
                    'menu' => $post['menu'],
                    'parent_auth_menu' => $post['parent_auth_menu'],
                    'file' => strtolower($post['file']),
                    'position' => (int)$post['position'],
                    'is_superadmin' => $post['is_superadmin'],
                );

                // insert data
                $last_id = $this->Menu_model->InsertRecord($data_post);
                if ($last_id) {
                    $this->session->set_flashdata('success_msg', 'Berhasil.<br/>');
                } else {
                    $this->session->set_flashdata('tmp_msg', 'Gagal.<br/>');
                }

                redirect('menu');
            }
        }
        $this->data['parent_option'] = $this->Menu_model->getParentSelect(0,'',$post['parent_auth_menu']);
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
            redirect('menu');
        }
        $this->load->model('Menu_model');
        $this->data['page_title'] = 'Edit Menu';
        $this->data['cancel_url'] = site_url('menu');
        $this->data['form_action'] = site_url('menu/edit/'.$id);
        $detail = $this->Menu_model->getMenu($id);
        if (!$detail) {
            redirect('menu');
        }
        if ($detail['is_superadmin'] == 1) {
            if (!is_superadmin()) {
                $this->session->set_flashdata('tmp_msg', 'Anda tidak bisa merubah menu ini.<br/>');
                redirect('menu');
            }
        }
        $post = $detail;
        if ($this->input->post()) {
            $post = purify($this->input->post());
            if ($this->validateForm($id)) {
                $post['is_superadmin'] = (isset($post['is_superadmin'])) ? 1 : 0;
                $data_post = array(
                    'menu' => $post['menu'],
                    'parent_auth_menu' => $post['parent_auth_menu'],
                    'file' => strtolower($post['file']),
                    'position' => (int)$post['position'],
                    'is_superadmin' => $post['is_superadmin'],
                );

                // update data
                $this->Menu_model->UpdateRecord($id,$data_post);
                $this->session->set_flashdata('success_msg', 'Berhasil.<br/>');

                redirect('menu');
            }
        }
        $this->data['parent_option'] = $this->Menu_model->getParentSelect(0,'',$post['parent_auth_menu'],$detail['id_auth_menu']);
        $this->data['post'] = $post;
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
        $this->load->model('Menu_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Menu_model->getMenu($id);
                    if ($detail) {
                        $id_group = adm_sess_usergroupid();
                        if (!$this->Menu_model->checkAuthMenuGroup($id,$id_group)) {
                            $this->session->set_flashdata('message','Gagal. Anda tidak punya hak akses untuk menghapus data ini.');
                        } else {
                            if (is_superadmin()) {
                                $this->Menu_model->DeleteRecord($id);
                                $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                            } else {
                                $this->session->set_flashdata('message','Gagal. Anda tidak punya hak akses untuk menghapus data ini.');
                            }
                        }
                    } else {
                        $this->session->set_flashdata('message','Tidak ada data.');
                    }
                }
            }
        } else {
            redirect('menu');
        }
    }
    
    /**
     * validate form
     * @return boolean
     */
    private function validateForm($id=0)
    {
        $this->load->model('Menu_model');
        $post = purify($this->input->post());
        $err = '';

        if ($post['menu'] == '') {
            $err .= 'Mohon isi Menu.<br/>';
        }

        if ($post['file'] == '') {
            $err .= 'Mohon isi File.<br/>';
        } else {
            if (!$this->Menu_model->checkExistMenu($post['file'],$id)) {
                $err .= 'Nama Menu File telah dipergunakan. Mohon isi dengan nama lain.<br/>';
            }
        }

        if ($post['parent_auth_menu'] == '') {
            $err .= 'Mohon pilih Parent.<br/>';
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }
    
}
/* End of file menu.php */
/* Location: ./application/controllers/menu.php */


