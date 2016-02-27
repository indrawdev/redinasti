<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Site Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc Site Model
 */
class Site_model extends CI_Model
{
    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * get site setting detail
     * @return array data
     */
    function getSite($id) {
        $data = $this->db
                ->where('is_delete',0)
                ->where('id_site',$id)
                ->get('sites')
                ->row_array();
        
        return $data;
    }
    
    /**
     * get setting site
     * @param int $id
     * @return array $return
     */
    function getSetting($id) {
        $return = array();
        $query = $this->db
                ->select('type,value')
                ->where('id_site', $id)
                ->order_by('type', 'asc')
                ->get('setting')->result_array();
        if ($query) {
            $q = array();
            foreach ($query as $row => $val) {
                $q[$val['type']] = $val['value'];
            }
            $return = $q;
        }
        return $return;
    }
    
    /**
     * update data
     * @param int $id
     * @param array $param
     */
    function UpdateData($id,$param) {
        $settings = $param['setting'];
        unset($param['setting']);
        $this->db->where('id_site',$id);
        $this->db->update('sites',$param);

        // delete setting before update
        $this->db->where('id_site',$id);
        $this->db->delete('setting');
        $ins = array();
        foreach ($settings as $setting => $val) {
            $ins[] = "('{$id}','{$setting}','{$val}')";
        }
        // now we update the setting
        if (count($ins)>0) {
            $imp = implode(', ', $ins);
            $this->db->query("insert into ".$this->db->dbprefix('setting')." (id_site,type,value) values {$imp}");
        }
    }
    
    /**
     * update data/record
     * @param int $id
     * @param array $param
     */
    function UpdateRecord($id,$param) {
        $this->db->where('id_site',$id);
        $this->db->update('sites',$param);
    }
}

/* End of file site_model.php */
/* Location: ./application/model/webcontrol/site_model.php */

