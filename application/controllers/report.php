<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Report Class
 * this class is for report management
 * @author ivan lubis
 * @version 2.1
 * @report Controller
 * @desc Report Controller
 */
class Report extends CI_Controller {

    //private $error = array();
    private $controller = 'report';

    /**
     * Index Report for this controller.
     */
    public function index() {
        $this->data['sales_product_url'] = site_url($this->controller.'/sales_product');
        $this->data['purchase_product_url'] = site_url($this->controller.'/purchase_product');
        $this->data['store_sales_url'] = site_url($this->controller.'/store_sales');
        $this->data['item_sales_url'] = site_url($this->controller.'/item_sales');
        $this->data['giro_cash_url'] = site_url($this->controller.'/giro_cash');
    }
    
    /**
     * report sales product
     */
    public function sales_product() {
        $this->load->model('Report_model');
        $this->data['controller'] = $this->controller;
        $this->data['form_action'] = site_url($this->controller.'/sales_product');
        $data = $this->Report_model->getSalesProduct();
        $this->data['data'] = $data;
        if ($this->input->post() && is_ajax_requested()) {
            $this->layout = 'none';
            $json = array();
            $from = $this->input->post('from_date');
            $to = $this->input->post('to_date');
            $data = $this->Report_model->getSalesProduct($from,$to);
            if (!$data) {
                $json['error'] = alert_box('Data tidak ditemukan. Mohon coba lagi.<br/>','error');
            }
            if (!$json) {
                $this->data['data'] = $data;
                $json['return'] = $this->load->view($this->controller.'/sales_product',$this->data,true);
            }
            echo json_encode($json);
            exit;
        }
    }
    
    /**
     * report sales product detail
     * @param int $id
     */
    public function sales_product_detail($id=0) {
        $this->load->model('Report_model');
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $data = $this->Report_model->getSalesProductDetail($id);
        if (!$data) {
            redirect($this->controller);
        }
        $this->data['data'] = $data;
    }
    
    /**
     * report purchase product
     */
    public function purchase_product() {
        $this->load->model('Report_model');
        $this->data['controller'] = $this->controller;
        $this->data['form_action'] = site_url($this->controller.'/purchase_product');
        $data = $this->Report_model->getSupplierPurchase();
        $this->data['data'] = $data;
        if ($this->input->post() && is_ajax_requested()) {
            $this->layout = 'none';
            $json = array();
            $from = $this->input->post('from_date');
            $to = $this->input->post('to_date');
            $data = $this->Report_model->getSupplierPurchase($from,$to);
            if (!$data) {
                $json['error'] = alert_box('Data tidak ditemukan. Mohon coba lagi.<br/>','error');
            }
            if (!$json) {
                $this->data['data'] = $data;
                $json['return'] = $this->load->view($this->controller.'/purchase_product',$this->data,true);
            }
            echo json_encode($json);
            exit;
        }
    }
    
    /**
     * store sales
     */
    public function store_sales() {
        $this->load->model('Report_model');
        $this->data['controller'] = $this->controller;
        $this->data['form_action'] = site_url($this->controller.'/store_sales');
        $data = $this->Report_model->getStoreSales();
        $this->data['data'] = $data;
        if ($this->input->post() && is_ajax_requested()) {
            $this->layout = 'none';
            $json = array();
            $from = $this->input->post('from_date');
            $to = $this->input->post('to_date');
            $data = $this->Report_model->getStoreSales($from,$to);
            if (!$data) {
                $json['error'] = alert_box('Data tidak ditemukan. Mohon coba lagi.<br/>','error');
            }
            if (!$json) {
                $this->data['data'] = $data;
                $json['return'] = $this->load->view($this->controller.'/store_sales',$this->data,true);
            }
            echo json_encode($json);
            exit;
        }
    }
    
    /**
     * store sales detail
     * @param int $id
     */
    public function store_sales_detail($id=0) {
        $this->load->model('Report_model');
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $data = $this->Report_model->getStoreSalesDetail($id);
        if (!$data) {
            redirect($this->controller);
        }
        $this->data['data'] = $data;
    }
    
    /**
     * item sales report
     */
    public function item_sales() {
        $this->load->model('Report_model');
        $this->data['controller'] = $this->controller;
        $this->data['form_action'] = site_url($this->controller.'/item_sales');
        $data = $this->Report_model->getSalesItem();
        $this->data['data'] = $data;
        if ($this->input->post() && is_ajax_requested()) {
            $this->layout = 'none';
            $json = array();
            $from = $this->input->post('from_date');
            $to = $this->input->post('to_date');
            $data = $this->Report_model->getSalesItem($from,$to);
            if (!$data) {
                $json['error'] = alert_box('Data tidak ditemukan. Mohon coba lagi.<br/>','error');
            }
            if (!$json) {
                $this->data['data'] = $data;
                $json['return'] = $this->load->view($this->controller.'/sales_product',$this->data,true);
            }
            echo json_encode($json);
            exit;
        }
    }
    
    public function giro_cash() {
        $this->load->model('Report_model');
        $this->data['controller'] = $this->controller;
        $this->data['form_action'] = site_url($this->controller.'/item_sales');
        $data_giro = $this->Report_model->getAllGiro();
        $this->data['data_giro'] = $data_giro;
        if ($this->input->post() && is_ajax_requested()) {
            $this->layout = 'none';
            $json = array();
            $from = $this->input->post('from_date');
            $to = $this->input->post('to_date');
            $data = $this->Report_model->getSalesItem($from,$to);
            if (!$data) {
                $json['error'] = alert_box('Data tidak ditemukan. Mohon coba lagi.<br/>','error');
            }
            if (!$json) {
                $this->data['data'] = $data;
                $json['return'] = $this->load->view($this->controller.'/sales_product',$this->data,true);
            }
            echo json_encode($json);
            exit;
        }
    }
    
}

/* End of file report.php */
/* Location: ./application/controllers/report.php */