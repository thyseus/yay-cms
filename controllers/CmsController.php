<?php

/**
 * CmsController
 **/
class CmsController extends Controller
{
	public function beforeAction($action)
	{
		$this->layout = Cms::module()->layout;

		if(Cms::module()->enableBootstrap) 
			Cms::register('bootstrap.min.css'); 

		Cms::register('cms.css'); 
		return parent::beforeAction($action);
	}
}

?>	
