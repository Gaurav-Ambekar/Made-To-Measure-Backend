<?php
	if(!defined('BASEPATH')) exit('NO ACCESS'); //and in routes file defualt controller

	class Mypdf_class
	{
		protected $CI;
		public function __construct()
		{
			$this->CI=& get_instance();
		}
		function tcpdf()
		{
		    require_once('tcpdf/tcpdf.php');
		}
	}
?>