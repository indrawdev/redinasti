<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Localization Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc Localization model
 * 
 */
class Localization_model extends CI_Model
{
    
    /**
     * Constructor 
     * @desc to load extends
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * get all localization list
     * @return array data of localization
     */
    function getLocale() {
        $get_locale = $this->db
                ->order_by('locale_status','desc')
                ->order_by('id_localization','asc')
                ->get('localization')
                ->result_array();
        return $get_locale;
    }
    
    /**
     * inser new record
     * @param array $param
     * @return boolean last id or false
     */
    function InsertNewRecord($param) {
        $this->db->insert('localization',$param);
        $last_id = $this->db->insert_id();
        if ($last_id) {
            return $last_id;
        }
        return false;
    }
    
    /**
     * update record
     * @param int $id
     * @param array $param
     */
    function UpdateRecord($id,$param) {
        $this->db->where('id_localization',$id);
        $this->db->update('localization',$param);
    }
    
    /**
     * get detail record
     * @param int $id
     * @return array $data
     */
    function getPage($id) {
        $data = $this->db
            ->where('id_localization',$id)
            ->limit(1)
            ->get('localization')
            ->row_array();
        return $data;
    }
    
    /**
     * check exists path
     * @param string $path
     * @param int $id
     * @return boolean
     */
    function check_exists_path($path,$id=0) {
        $this->db->where('LCASE(locale_path)',strtolower($path));
        if ($id) {
            $this->db->where('id_localization !=',$id);
        }
        $this->db->from('localization');
        $total = $this->db->count_all_results();
        if ($total > 0) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * check default
     * @param int $id
     * @return boolean true/false
     */
    function check_default($id=0) {
        $this->db->where('locale_status',1);
        if ($id) {
            $this->db->where('id_localization !=',$id);
        }
        $this->db->from('localization');
        $total = $this->db->count_all_results();
        if ($total > 0) {
            return false;
        } else {
            return true;
        }
    }

}

/* End of file localization_model.php */
/* Location: ./application/model/localization_model.php */

