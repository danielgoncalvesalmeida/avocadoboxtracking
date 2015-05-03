<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends My_Controller {
    
    public function __construct()
    {
        $this->layout = 'front';
        parent::__construct();
    }
    
	public function index()
	{
        if($this->id_lang == 1)
            $this->setTitle('BUDERUS | Économiser en remplacent votre chaudière');
        elseif ($this->id_lang == 2) 
            $this->setTitle('BUDERUS | Intelligent sparen durch Kesselaustausch!');
        
        $dview = array(
            'id_lang' => $this->id_lang,
            'dont_show_language_switch_on_header' => true,
         );
        $this->display('home', $dview);
    }
}

