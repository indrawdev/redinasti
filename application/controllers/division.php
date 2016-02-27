<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Division Class
 * this class is for division management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Division Controller
 */
class Division extends CI_Controller
{
    //private $error = array();
    private $error = '';

    /**
     * Index Division for this controller.
     */
    public function index()
    {
        /**
         * let this function empty just for generating layout
         */
        $this->data['add_url'] = site_url('division/add');
        $this->data['export_excel_url'] = site_url('division/export_excel');
        $this->data['list_data'] = site_url('division/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_division'] = "division";
        $alias['search_pic'] = "division_pic";
        $alias['search_address'] = "division_address";
        $query = "
            select 
                id_division as id, 
                id_division as idx, 
                " . $this->db->dbprefix('division') . ".*
            from " . $this->db->dbprefix('division') . "
            where is_delete=0";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Division_model');
        $this->data['form_action'] = site_url('division/add');
        $this->data['cancel_url'] = site_url('division');
        $this->data['page_title'] = 'Tambah Divisi';
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                
                $last_id = $this->Division_model->InsertNewRecord($post);
                if ($last_id) {
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect('division');
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
        $this->data['content_layout'] = 'form';
    }
    
    /**
     * edit new record
     */
    public function edit($id=0)
    {
        $this->load->model('Division_model');
        $this->data['form_action'] = site_url('division/edit/'.$id);
        $this->data['cancel_url'] = site_url('division');
        $this->data['page_title'] = 'Edit Divisi';
        $id = (int)$id;
        if (!$id) {
            redirect('division');
        }
        $detail = $this->Division_model->getDivision($id);
        $this->data['products'] = $this->Division_model->getDivisionProduct($detail['id_division']);
        $this->data['post'] = $detail;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm($id)) {
                $this->Division_model->UpdateRecord($id,$post);
                $this->session->set_flashdata('success_msg','Edit data berhasil.');
                redirect('division');
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
     */
    public function delete() {
        $this->load->model('Division_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Division_model->getDivision($id);
                    if ($detail) {
                        $this->Division_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                    } else {
                        $this->session->set_flashdata('message','Data tidak ditemukan.');
                    }
                }
            }
        } else {
            redirect('division');
        }
    }

    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Division_model');
        $post = $this->input->post();
        $err = '';
        if ($post['division'] == '') {
            $err .= 'Mohon isi Nama Divisi.<br/>';
        }
        if ($post['division_code'] == '') {
            $err .= 'Mohon isi Kode Divisi.<br/>';
        } else {
            if ($this->Division_model->check_exists_code_pref($post['division_code'],'code')) {
                $err .= 'Kode Divisi telah digunakan. Mohon isi yang lain.<br/>';
            }
        }
        if ($post['division_pref'] == '') {
            $err .= 'Mohon isi Prefix Divisi.<br/>';
        } else {
            if ($this->Division_model->check_exists_code_pref($post['division_pref'],'pref')) {
                $err .= 'Prefix Divisi telah digunakan. Mohon isi yang lain.<br/>';
            }
        }
        if ($post['division_pic'] == '') {
            $err .= 'Mohon isi Nama PIC Divisi.<br/>';
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }

}
/* End of file division.php */
/* Location: ./application/controllers/division.php */