<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Menu Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc Menu Model
 */
class Menu_model extends CI_Model
{
    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * get menu admin detail by id
     * @param int $id
     * @return array data
     */
    function getMenu($id) {
        $data = $this->db
                ->where('id_auth_menu',$id)
                ->limit(1)
                ->get('auth_menu')
                ->row_array();
        
        return $data;
    }
    
    /**
     * insert new record
     * @param array $param
     * @return int last inserted id
     */
    function InsertRecord($param) {
        $this->db->insert('auth_menu',$param);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update record menu user
     * @param int $id
     * @param array $param
     */
    function UpdateRecord($id,$param) {
        $this->db->where('id_auth_menu',$id);
        $this->db->update('auth_menu',$param);
    }
    
    /**
     * delete record
     * @param int $id_menu
     */
    function DeleteRecord($id_menu) {
        $this->db->where('id_auth_menu',$id_menu);
        $this->db->delete(array('auth_menu','auth_menu_group'));
    }
    
    /**
     * get parent auth pages
     * @param int $id_parent
     * @return array data
     */
    function getParentMenu($id_parent=0) {
        if (is_superadmin()) {
            $data = $this->db
                    ->where('parent_auth_menu',$id_parent)
                    ->order_by('id_auth_menu','asc')
                    ->get('auth_menu')
                    ->result_array();
        } else {
            $data = $this->db
                    ->where('parent_auth_menu',$id_parent)
                    ->where('is_superadmin',0)
                    ->order_by('id_auth_menu','asc')
                    ->get('auth_menu')
                    ->result_array();
        }
        return $data;
    }
    
    /**
     * get max position
     * @return int maximum value
     */
    function getMaxPosition() {
        $data = $this->db
                ->select('max(position) as max_pos')
                ->get('auth_menu')
                ->row_array();
        
        return (int)$data['max_pos'];
    }
    
    /**
     * exception children by parent
     * @param int $id_page
     * @return array return list of exception id
     */
    function pageParentExeption($id_page) {
        $return = array();
        $data = $this->db
                ->where('parent_auth_menu',$id_page)
                ->get('auth_menu')
                ->result_array();
        foreach($data as $row) {
            $return[] = $row['id_auth_menu'];
            $return = array_merge($return,$this->pageParentExeption($row['id_auth_menu']));
        }
        return $return;
    }
    
    /**
     * get parent for select option tag
     * @param int $parent
     * @param string $prefix
     * @param int $selectitem
     * @param int $disable
     * @return string html option tag with data
     */
    function getParentSelect ($parent=0, $prefix='', $selectitem='',$disable='')
    {
        $tmp_menu = '';
        $parent = $this->getParentMenu($parent);
        foreach ($parent as $row) 
        {  
            if ($disable && ($row["id_auth_menu"] == $disable)) {
                $tmp_menu .= '';
            } elseif($row["parent_auth_menu"] == $disable) {
                $tmp_menu .= '';
            } else {
                if ($row["parent_auth_menu"] == $disable) {
                    $tmp_menu .= '';
                } else {
                    $exeption = FALSE;
                    if ($disable) {
                        $get_exeption = $this->pageParentExeption($disable);
                        $exeption = array_search($row['id_auth_menu'], $get_exeption,TRUE);
                    }
                    if ($exeption !== FALSE) {
                        $tmp_menu .= '';
                    } else {
                        if ($selectitem == $row["id_auth_menu"]) { 
                            $tmp_menu .=  '<option value="'.$row["id_auth_menu"].'" selected="selected">'.$prefix.' '.$row["menu"].'</option>';
                        } else { 
                            $tmp_menu .=  '<option value="'.$row["id_auth_menu"].'">'.$prefix.' '.$row["menu"].'</option>';
                        }
                    }
                }
            }
            $tmp_menu .=  $this->getParentSelect($row["id_auth_menu"], $prefix."--",$selectitem,$disable);
        }
        $tmp_menu .= 'a';
        return $tmp_menu;
    }
    
    /**
     * check exists menu
     * @param string $menu
     * @param int $id
     * @return boolean
     */
    function checkExistMenu($menu,$id=0) {
        if ($menu == '#' || $menu == '/') {
            return true;
        }
        $this->db->from('auth_menu');
        if ($id) {
            $this->db->where('id_auth_menu !=',$id);
        }
        $this->db->where('LCASE(file)',strtolower($menu));
        $count = $this->db->count_all_results();
        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * check if user have access to this menu
     * @param int $id_menu
     * @param int $id_group
     * @return boolean
     */
    function checkAuthMenuGroup($id_menu,$id_group) {
        $data = $this->db
                ->from('auth_menu_group')
                ->where('id_auth_group',$id_group)
                ->where('id_auth_menu',$id_menu)
                ->count_all_results();
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }


}

/* End of file menu_model.php */
/* Location: ./application/model/webcontrol/menu_model.php */

