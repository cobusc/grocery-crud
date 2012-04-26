<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AdminIf extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		/* Standard Libraries */
		$this->load->database();
		$this->load->helper('url');
		/* ------------------ */	
		
		$this->load->library('grocery_CRUD');	
	}
	
	function _example_output($output = null)
	{
		$this->load->view('adminif.php',$output);	
	}

	function index()
	{
		$this->_example_output((object)array('output' => '' , 
                                                     'js_files' => array() , 
                                                     'css_files' => array(), 
                                                     'tables' => $this->db->list_tables()
                                                    )
                                      );
	}	

        public function _remap($method, $params = array())
        {
            if (method_exists($this, $method))
                return call_user_func_array(array($this, $method), $params);
            else
                return call_user_func_array(array($this, "auto_table"), array_merge((array)$method, $params));
            //show_404();
        }

        public function auto_table($table_name)
        {
            $crud = new grocery_CRUD();

            $crud->set_table($table_name);

            $output = $crud->render();

            $this->_example_output($output);
        }

	
        function spm_instance()
        {
            $output = $this->grocery_crud->render();

            $this->_example_output($output);    
        }

        function spm_service()
        {
            $crud = new grocery_CRUD();

            $crud->set_relation('instance_id','spm_instance','short_hostname');
            $crud->set_relation('svc_id','agg_svc_table','{name}');

            $output = $crud->render();

            $this->_example_output($output);            
        }

        function spm_url()
        {
            $crud = new grocery_CRUD();

            $crud->set_relation('service_id','spm_service','name');
            $crud->set_relation('bearer_id','spm_bearer','bearer');
            $crud->set_relation('url_type_id','spm_url_type','name');          

            $output = $crud->render();

            $this->_example_output($output);
        }

        function spm_sms_bearer_config()
        {
            $crud = new grocery_CRUD();

            $crud->set_relation('service_id','spm_service','name');
            $crud->set_relation('behaviour_id','spm_sms_behaviour','{name} {description}');

            $output = $this->grocery_crud->render();

            $this->_example_output($output);
        }

}
