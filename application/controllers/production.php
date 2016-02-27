<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Production Class
 * this class is for production management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Production Controller
 */
class Production extends CI_Controller 
{
    //private $error = array();
    private $error = '';
    private $controller = 'production';
    
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
        $alias['search_code'] = "production_code";
        $alias['search_name'] = "production_name";
        if (!is_superadmin()) {
            $inc_where = " and " . $this->db->dbprefix('production') . ".id_division = '".getSessionAdmin('admin_id_division')."'";
        }
        $query = "
            select 
                id_production as id, 
                id_production as idx, 
                " . $this->db->dbprefix('production') . ".*,
                " . $this->db->dbprefix('division') . ".*
            from " . $this->db->dbprefix('production') . "
            left join ".$this->db->dbprefix('division')." on ".$this->db->dbprefix('division').".id_division=".$this->db->dbprefix('production').".id_division
            where " . $this->db->dbprefix('production') . ".is_delete=0".$inc_where;
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add page
     */
    public function add() {
        $this->load->model('Production_model');
        $this->data['form_action'] = site_url($this->controller.'/add');
        $this->data['cancel_url'] = site_url($this->controller);
        $this->data['category_url'] = site_url($this->controller.'/add_category');
        $this->data['getproduct_url'] = site_url($this->controller.'/ajax_get_product');
        $this->data['getproduction_url'] = site_url($this->controller.'/ajax_get_production');
        $this->data['productinfo_url'] = site_url($this->controller.'/ajax_product_info');
        $this->data['productioninfo_url'] = site_url($this->controller.'/ajax_production_info');
        $this->data['page_title'] = 'Tambah Kode Produksi Barang';
        $this->data['product_count'] = 0;
        $this->data['cost_count'] = 0;
        $this->data['production_count'] = 0;
        $this->data['categories'] = $this->Production_model->getProductionCategory();
        if (is_superadmin()) {
            $id_division = 0;
            $this->data['divisions'] = $this->Production_model->getAllDivision();
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
                $post_product = false;
                $post_production = false;
                $post_cost = false;
                if (isset($post['post_product']) && count($post['post_product'])>0) {
                    $post_product = $post['post_product'];
                    unset($post['post_product']);
                }
                if (isset($post['post_production']) && count($post['post_production'])>0) {
                    $post_production = $post['post_production'];
                    unset($post['post_production']);
                }
                if (isset($post['post_cost']) && count($post['post_cost'])>0) {
                    $post_cost = $post['post_cost'];
                    unset($post['post_cost']);
                }
                $div_info = $this->Production_model->getDivisionByID($id_division);
                $last_id = $this->Production_model->InsertRecord($post);
                $total = 0;
                if ($last_id) {
                    $post['production_code'] = $div_info['division_pref'].$div_info['id_division'].$post['production_type'].date('Ymd').$post['id_production_category'].$last_id;
                    if ($post_product) {
                        $product = array();
                        foreach ($post_product as $prow) {
                            $product[$prow['id_product']]['id_product'] = $prow['id_product'];
                            $product[$prow['id_product']]['purchase_price'] = $prow['purchase_price'];
                            $product[$prow['id_product']]['id_production'] = $last_id;
                            $product[$prow['id_product']]['id_division'] = $post['id_division'];
                            $product[$prow['id_product']]['production_code'] = $post['production_code'];
                            if (!empty($product[$prow['id_product']]['purchase_qty'])) {
                                $product[$prow['id_product']]['purchase_qty'] += $prow['purchase_qty'];
                            } else {
                                $product[$prow['id_product']]['purchase_qty'] = $prow['purchase_qty'];
                            }
                        }
                        $products = array_values($product);
                        foreach ($products as $prd) {
                            $produ['id_production'] = $last_id;
                            $produ['id_division'] = $post['id_division'];
                            $produ['id_product'] = $prd['id_product'];
                            $produ['production_code'] = $post['production_code'];
                            $produ['production_product_qty'] = $prd['purchase_qty'];
                            $produ['production_product_price'] = $prd['purchase_price'];
                            $total += $prd['purchase_price'];
                            $this->Production_model->InsertProductionProduct($produ);
                            
                            // update product division record
                            $this->Production_model->UpdateProductStock($prd['id_product'],$prd['purchase_qty'],$prd['purchase_price'],$post['id_division']);
                        }
                    }
                    if ($post_production) {
                        $production = array();
                        foreach ($post_production as $prd) {
                            $production['id_production'] = $last_id;
                            $production['id_division'] = $post['id_division'];
                            $production['production_id'] = $prd['id_production'];
                            $production['code'] = $post['production_code'];
                            $production['name'] = $post['production_name'];
                            $production['price'] = $prd['production_price'];
                            $total += $prd['production_price'];
                            
                            $this->Production_model->InsertProductionProduction($production);
                        }
                    }
                    if ($post_cost) {
                        $cost = array();
                        foreach ($post_cost as $cst) {
                            $cost['id_production'] = $last_id;
                            $cost['id_division'] = $post['id_division'];
                            $cost['production_code'] = $post['production_code'];
                            $cost['production_cost_note'] = $cst['note'];
                            $cost['production_cost'] = $cst['cost'];
                            $total += ($cst['cost']);
                            
                            $this->Production_model->InsertProductionCost($cost);
                        }
                    }
                    // update total hpp
                    $post_total = array('production_hpp_price'=>$total,'production_code'=>$post['production_code']);
                    $this->Production_model->UpdateRecord($last_id,$post_total);
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect('production');
            }
            $this->data['post'] = $post;
        }
        $this->data['productions'] = $this->Production_model->getDivisionProductions($id_division);
        $this->data['products'] = $this->Production_model->getDivisionProducts($id_division);
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * detail page
     * @param int $id
     */
    public function detail($id=0) {
        $this->load->model('Production_model');
        $this->data['back_url'] = site_url('production');
        $this->data['print_url'] = site_url('production/print/'.$id);
        $this->data['page_title'] = 'Detail Kode Produksi Barang';
        $id = (int)$id;
        if (!$id) {
            redirect('production');
        }
        $detail = $this->Production_model->getProduction($id);
        if (!$detail) {
            redirect('production');
        }
        if (!is_superadmin()) {
            $id_division = getSessionAdmin('admin_id_division');
            if ($id_division != $detail['id_division']) {
                redirect('production');
            }
        }
        $this->data['record'] = $detail;
    }
    
    /**
     * add new category production
     */
    public function add_category() {
        $this->load->model('Production_model');
        $this->data['form_action'] = site_url($this->controller.'/add_category');
        $this->data['page_title'] = 'Tambah Kategori Produksi';
        $this->data['cancel_url'] = site_url($this->controller);
        $json = array();
        if (is_ajax_requested()) {
            $this->layout = 'none';
        }
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateAddCategoryForm()) {
                $last_id = $this->Production_model->InsertProductCategory($post);
                if ($last_id) {
                    $json['success'] = alert_box('Berhasil tambah data.','success');
                    $json['return'] = $this->Production_model->getProductionCategory();
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
        $this->data['content_layout'] = 'add_category';
        if (is_ajax_requested()) {
            $json['html'] = $this->load->view($this->controller.'/'.$this->data['content_layout'],$this->data,true);
            echo json_encode($json);
        }
    }
    
    /**
     * request ajax product info
     */
    public function ajax_product_info() {
        $this->layout = 'none';
        if (is_ajax_requested() && $this->input->post()) {
            $post = $this->input->post();
            $this->load->model('Production_model');
            $json = array();
            if (!isset($post['product_id'])) {
                $json['error'] = alert_box('Mohon pilih Produk.','error');
            }
            if (!isset($post['division_id'])) {
                $json['error'] = alert_box('Mohon pilih Divisi terlebih dahulu.','error');
            }
            if (is_superadmin()) {
                $id_division = $post['division_id'];
            } else {
                $id_division = getSessionAdmin('admin_id_division');
            }
            if (!$id_division) {
                $json['error'] = alert_box('Mohon pilih Divisi terlebih dahulu.<br/>','error');
            }
            if (!$json) {
                $data = $this->Production_model->getProductInfo($post['product_id'],$id_division);
                if (!$data) {
                    $json['error'] = alert_box('Produk tidak ada. Mohon pilih Produk yang lain<br/>.');
                }
                if (!$json) {
                    $json['value'] = $data;
                }
            }
            echo json_encode($json);
        }
    }
    
    /**
     * request ajax production info
     */
    public function ajax_production_info() {
        $this->layout = 'none';
        if (is_ajax_requested() && $this->input->post()) {
            $post = $this->input->post();
            $this->load->model('Production_model');
            $json = array();
            if (!isset($post['production_id'])) {
                $json['error'] = alert_box('Mohon pilih Kode Produksi.','error');
            }
            if (!isset($post['division_id'])) {
                $json['error'] = alert_box('Mohon pilih Divisi terlebih dahulu.','error');
            }
            if (is_superadmin()) {
                $id_division = $post['division_id'];
            } else {
                $id_division = getSessionAdmin('admin_id_division');
            }
            if (!$id_division) {
                $json['error'] = alert_box('Mohon pilih Divisi terlebih dahulu.<br/>','error');
            }
            if (!$json) {
                $data = $this->Production_model->getProductionInfo($post['production_id'],$id_division);
                if (!$data) {
                    $json['error'] = alert_box('Kode Produksi tidak ada. Mohon pilih Kode Produksi yang lain<br/>.');
                }
                if (!$json) {
                    $json['value'] = $data;
                }
            }
            echo json_encode($json);
        }
    }
    
    /**
     * ajax get product list
     */
    public function ajax_get_product() {
        $this->layout = 'none';
        $json = array();
        if (is_ajax_requested() && $this->input->post()) {
            $this->load->model('Production_model');
            $post = $this->input->post();
            if (is_superadmin()) {
                $id_division = $post['division_id'];
            } else {
                $id_division = getSessionAdmin('admin_id_division');
            }
            if (!$id_division) {
                $json['error'] = alert_box('Mohon pilih Divisi terlebih dahulu.<br/>','error');
            }
            $data = $this->Production_model->getDivisionProducts($id_division);
            if ($data) {
                $return = '';
                foreach ($data as $row) {
                    $return .= '<option value="'.$row['id_product'].'">'.$row['product_name'].'</option>';
                }
                $json['return'] = $return;
            }
            echo json_encode($json);
        }
    }
    
    /**
     * ajax get production list
     */
    public function ajax_get_production() {
        $this->layout = 'none';
        $json = array();
        if (is_ajax_requested() && $this->input->post()) {
            $this->load->model('Production_model');
            $post = $this->input->post();
            if (is_superadmin()) {
                $id_division = $post['division_id'];
            } else {
                $id_division = getSessionAdmin('admin_id_division');
            }
            if (!$id_division) {
                $json['error'] = alert_box('Mohon pilih Divisi terlebih dahulu.<br/>','error');
            }
            $data = $this->Production_model->getDivisionProductions($id_division);
            if ($data) {
                $return = '';
                foreach ($data as $row) {
                    $return .= '<option value="'.$row['id_production'].'">'.$row['production_code'].' - '.$row['production_name'].'</option>';
                }
                $json['return'] = $return;
            } else {
                $json['error'] = alert_box('Kode Produksi tidak ada.<br/>','error');
            }
            echo json_encode($json);
        }
    }
    
    /**
     * validate form
     * @return boolean
     */
    private function validateForm() {
        $this->load->model('Production_model');
        $post = $this->input->post();
        $err = '';
        if (is_superadmin()) {
            if ($post['id_division'] == '') {
                $err .= 'Mohon pilih Divisi.<br/>';
            }
        }
        if ($post['id_production_category'] == '' || $post['id_production_category']== 0) {
            $err .= 'Mohon pilih Kategori Produksi.<br/>';
        }
        if ($post['production_name'] == '') {
            $err .= 'Mohon isi Nama Produksi/Barang.<br/>';
        }
        if ($post['production_type'] == '') {
            $err .= 'Mohon pilih Tipe Produksi.</br>';
        }
        
        if (isset($post['post_product']) && count($post['post_product'])>0) {
            $post_product = $post['post_product'];
            $product = array();
            foreach ($post_product as $prow) {
                $product[$prow['id_product']]['id_product'] = $prow['id_product'];
                $product[$prow['id_product']]['purchase_price'] = $prow['purchase_price'];
                if (!empty($product[$prow['id_product']]['purchase_qty'])) {
                    $product[$prow['id_product']]['purchase_qty'] += $prow['purchase_qty'];
                } else {
                    $product[$prow['id_product']]['purchase_qty'] = $prow['purchase_qty'];
                }
            }
            $products = array_values($product);
            foreach ($products as $prow2) {
                $id_product = $prow2['id_product'];
                $prod_qty = $prow2['purchase_qty'];
                /*$check = $this->Production_model->checkQty($post['id_division'],$id_product,$prod_qty);
                if (!$check) {
                    $err .= 'Jumlah Produk melebihi batas stok.<br/>';
                    break;
                }*/
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
     * validate category form
     * @return boolean
     */
    private function validateAddCategoryForm() {
        $post = $this->input->post();
        $err = '';
        if (!isset($post['production_category'])) {
            $err .= 'Mohon isi Nama Kategori.<br/>';
        }
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }
    
}

/* End of file production.php */
/* Location: ./application/controllers/production.php */
