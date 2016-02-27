<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $breadcrumbs[] = array(
            'href'=>'#',
            'menu'=>'Dashboard',
            'class'=>'active',
        );
        $this->data['breadcrumbs'] = $breadcrumbs;
    }
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */