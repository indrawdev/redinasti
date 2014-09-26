<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Giro Model Class
 * @author ivan lubis
 * @version 2.1
 * @giro Model
 * @desc giro model
 * 
 */
class Giro_model extends CI_Model {

    /**
     * Constructor 
     * @desc to load extends
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * inser new record giro
     * @param array $post
     * @return boolean last id or false
     */
    function InsertNewRecord($post) {
        $this->db->insert('giro', $post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }

    /**
     * update giro record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id, $post) {
        $this->db->where('id_giro', $id);
        $this->db->update('giro', $post);
    }

    /**
     * get giro detail
     * @param int $id
     * @return array $data
     */
    function getGiro($id) {
        $data = $this->db
                ->where('id_giro', $id)
                ->limit(1)
                ->get('giro')
                ->row_array();
        return $data;
    }
    
    /**
     * delete record
     * @param int $id
     */
    function DeleteRecord($id) {
        $this->db->where('id_giro', $id);
        $this->db->delete('giro');
    }
    
    /**
     * get giro history
     * @param int $id_giro
     * @return array
     */
    function GiroHistory($id_giro) {
        $data = array();
        $data['from'] = $this->db
                ->query(
                    "
                        (
                            select 
                                id_division_purchase_payment AS id,
                                purchase_invoice AS invoice,
                                payment_note AS note,
                                payment_date AS dated,
                                payment_total AS total,
                                concat(_utf8 'division_purchase') AS path
                            from {$this->db->dbprefix('division_purchase_payment')}
                            where id_giro = '{$id_giro}' and payment_type = '2'
                        )
                        union 
                        (
                            select 
                                id_sales AS id,
                                sales_invoice AS invoice,
                                payment_note AS note,
                                payment_date AS dated,
                                payment_total AS total,
                                concat(_utf8 'sales') AS path
                            from {$this->db->dbprefix('sales_payment')}
                            where id_giro = '{$id_giro}' and payment_type = '2'
                        )
                    "
                )
                ->result_array();
                            
        $data['payment'] = $this->db
                ->query(
                    "
                        (
                            select 
                                id_supplier_purchase AS id,
                                purchase_invoice AS invoice,
                                payment_note AS note,
                                payment_date AS dated,
                                payment_total AS total,
                                concat(_utf8 'supplier_purchase') AS path
                            from {$this->db->dbprefix('supplier_purchase_payment')}
                            where id_giro = '{$id_giro}' and payment_type = '2'
                        )
                        union 
                        (
                            select 
                                id_purchase AS id,
                                purchase_invoice AS invoice,
                                payment_note AS note,
                                payment_date AS dated,
                                payment_total AS total,
                                concat(_utf8 'purchase') AS path
                            from {$this->db->dbprefix('purchase_payment')}
                            where id_giro = '{$id_giro}' and payment_type = '2'
                        )
                    "
                )
                ->result_array();
        
        $data['cashed'] = $this->db
                ->where('id_giro',$id_giro)
                ->order_by('id_giro_cashed','desc')
                ->get('giro_cashed')
                ->result_array();
        
        
        return $data;
    }
    
    /**
     * cashed in giro
     * @param array $param
     * @return int last inserted id
     */
    function CashedInGiro($param) {
        $this->db->insert('giro_cashed',$param);
        
        return $this->db->insert_id();
    }
        

}

/* End of file giro_model.php */
/* Location: ./application/model/giro_model.php */

