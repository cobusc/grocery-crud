<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

error_reporting(E_ALL);
ini_set('display_errors', True);

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

            //$sql = "select b.table_name, b.column_name from information_schema.table_constraints a, information_schema.constraint_column_usage b where a.constraint_type = 'FOREIGN KEY' and a.table_name = '$table_name' and a.constraint_name=b.constraint_name;";
            $sql = "SELECT tc.constraint_name,
                           tc.table_name,
                           kcu.column_name,
                           ccu.table_name AS foreign_table_name,
                           ccu.column_name AS foreign_column_name
                      FROM information_schema.table_constraints AS tc 
                      JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name
                      JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name
                     WHERE constraint_type = 'FOREIGN KEY'
                       AND tc.table_name='$table_name'";
            foreach($this->db->query($sql)->result() as $relation)
            {
                $crud->set_relation($relation->column_name,$relation->foreign_table_name,$relation->foreign_column_name); 
                //break;
            }
            $output = $crud->render();

            $this->_example_output($output);
        }

	
/*        function spm_instance()
        {
            $output = $this->grocery_crud->render();

            $this->_example_output($output);    
        }*/

        function spm_service()
        {
            $crud = new grocery_CRUD();

            $crud->set_relation('instance_id','spm_instance','short_hostname');
            $crud->set_relation('svc_id','agg_svc_table','name');

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

            $output = $crud->render();

            $this->_example_output($output);
        }

}
