<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Item Class
 * this class is for item management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Item Controller
 */
class Item extends CI_Controller 
{
    //private $error = array();
    private $error = '';
    private $controller = 'item';
    
    /**
     * index page for this controller
     */
    public function index() {
        /**
         * let this function empty just for generating layout
         */
        $this->data['add_url'] = site_url($this->controller.'/add');
        $this->data['export_excel_url'] = site_url($this->controller.'/export_excel');
        $this->data['list_data'] = site_url($this->controller.'/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data() {
        $inc_where = '';
        if (is_superadmin()) {
            $alias['search_division'] = "division";
        }
        $alias['search_category'] = "item_category";
        $alias['search_name'] = "item_name";
        if (!is_superadmin()) {
            $inc_where = " and " . $this->db->dbprefix('item') . ".id_division = '".getSessionAdmin('admin_id_division')."'";
        }
        $query = "
            select 
                id_item as id, 
                id_item as idx, 
                " . $this->db->dbprefix('item') . ".*,
                " . $this->db->dbprefix('division') . ".*,
                " . $this->db->dbprefix('item_category') . ".*
            from " . $this->db->dbprefix('item') . "
            left join ".$this->db->dbprefix('division')." on ".$this->db->dbprefix('division').".id_division=".$this->db->dbprefix('item').".id_division
            left join ".$this->db->dbprefix('item_category')." on ".$this->db->dbprefix('item_category').".id_item_category=".$this->db->dbprefix('item').".id_item_category
            where " . $this->db->dbprefix('item') . ".is_delete=0".$inc_where;
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add page
     */
    public function add() {
        $this->load->model('Item_model');
        $this->data['form_action'] = site_url($this->controller.'/add');
        $this->data['cancel_url'] = site_url($this->controller);
        $this->data['category_url'] = site_url($this->controller.'/add_item_category');
        $this->data['page_title'] = 'Tambah Barang';
        $this->data['categories'] = $this->Item_model->getItemCategory();
        if (is_superadmin()) {
            $id_division = 0;
            $this->data['divisions'] = $this->Item_model->getAllDivision();
        } else {
            $id_division = getSessionAdmin('admin_id_division');
        }
        if ($this->input->post()) {
            $post = $this->input->post();
            if (!is_superadmin()) {
                $post['id_division'] = getSessionAdmin('admin_id_division');
            } else {
                $id_division = $post['id_division'];
            }
            if ($this->validateForm()) {
                $div_info = $this->Item_model->getDivisionByID($id_division);
                $category_info = $this->Item_model->getCategoryByID($post['id_item_category']);
                $last_id = $this->Item_model->InsertRecord($post);
                if ($last_id) {
                    $post_update['item_code'] = $div_info['division_pref'].$div_info['id_division'].'-'.$post['item_type'].'-'.$last_id.'-'.url_title($category_info['item_category']);
                    $post_update['item_name'] = $post_update['item_code'];
                    $this->Item_model->UpdateRecord($last_id,$post_update);
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect($this->controller);
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
     * @param int $id
     */
    public function edit($id=0) {
        $this->load->model('Item_model');
        $this->data['form_action'] = site_url($this->controller.'/edit/' . $id);
        $this->data['page_title'] = 'Edit Barang';
        $this->data['cancel_url'] = site_url($this->controller);
        $this->data['category_url'] = site_url($this->controller.'/add_item_category');
        $this->data['categories'] = $this->Item_model->getItemCategory();
        $id = (int) $id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Item_model->getItem($id);
        $this->data['post'] = $detail;
        if (is_superadmin()) {
            $id_division = 0;
            $this->data['divisions'] = $this->Item_model->getAllDivision();
        } else {
            $id_division = getSessionAdmin('admin_id_division');
        }
        if ($this->input->post()) {
            $post = $this->input->post();
            if (!is_superadmin()) {
                $post['id_division'] = getSessionAdmin('admin_id_division');
            } else {
                $id_division = $post['id_division'];
            }
            if ($this->validateForm($id)) {
                $div_info = $this->Item_model->getDivisionByID($id_division);
                $category_info = $this->Item_model->getCategoryByID($post['id_item_category']);
                $post['item_code'] = $div_info['division_pref'].$div_info['id_division'].'-'.$post['item_type'].'-'.$id.'-'.url_title($category_info['item_category']);
                $post['item_name'] = $post['item_code'];
                $this->Item_model->UpdateRecord($id, $post);
                $this->session->set_flashdata('success_msg', 'Berhasil edit data.');

                redirect($this->controller);
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error, 'error');
        }
        $this->data['content_layout'] = 'form';
    }
    
    /**
     * add new category item
     */
    public function add_item_category() {
        $this->load->model('Item_model');
        $this->data['form_action'] = site_url($this->controller.'/add_item_category');
        $this->data['page_title'] = 'Tambah Nama Barang';
        $this->data['cancel_url'] = site_url($this->controller);
        $json = array();
        if (is_ajax_requested()) {
            $this->layout = 'none';
        }
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateAddCategoryForm()) {
                $last_id = $this->Item_model->InsertItemCategory($post);
                if ($last_id) {
                    $json['success'] = alert_box('Berhasil tambah data.','success');
                    $json['return'] = $this->Item_model->getItemCategory();
                    $this->session->set_flashdata('success_msg', 'Berhasil tambah data.');
                } else {
                    $json['error'] = alert_box('Gagal.','error');
                    $this->session->set_flashdata('tmp_msg', 'Gagal.');
                }
                if (!is_ajax_requested()) {
                    redirect($this->controller);
                }
            }
            if (is_ajax_requested()) {
                if ($this->error) {
                    $json['error'] = alert_box($this->error, 'error');
                }
                echo json_encode($json);
                exit;
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['err_message'] = alert_box($this->error, 'error');
        }
        $this->data['content_layout'] = 'add_item_category';
        if (is_ajax_requested()) {
            $json['html'] = $this->load->view($this->controller.'/'.$this->data['content_layout'],$this->data,true);
            echo json_encode($json);
        }
    }
    
    /**
     * validate form
     * @return boolean
     */
    private function validateForm() {
        $this->load->model('Item_model');
        $post = $this->input->post();
        $err = '';
        if (is_superadmin()) {
            if ($post['id_division'] == '') {
                $err .= 'Mohon pilih Divisi.<br/>';
            }
        }
        if ($post['id_item_category'] == '' || $post['id_item_category']== 0) {
            $err .= 'Mohon pilih Nama Barang.<br/>';
        }
        if ($post['item_type'] == '') {
            $err .= 'Mohon pilih Tipe Barang.</br>';
        }
        if ($post['item_hpp_price'] == '') {
            $err .= 'Mohon isi HPP.<br/>';
        }
        if ($post['item_sell_price'] == '') {
            $err .= 'Mohon isi Harga Jual.<br/>';
        }
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * validate category form
     * @return boolean
     */
    private function validateAddCategoryForm() {
        $post = $this->input->post();
        $err = '';
        if (!isset($post['item_category'])) {
            $err .= 'Mohon isi Nama Kategori Barang.<br/>';
        }
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }
    
}

/* End of file item.php */
/* Location: ./application/controllers/item.php */
