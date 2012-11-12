<?
Yii::setPathOfAlias('CmsAssets' , dirname(__FILE__) . '/assets/');   

class CmsModule extends CWebModule
{
	public $version = '0.6-dev';
	public $adminLayout = 'application.modules.cms.views.layouts.cms';
	public $layout = '//layouts/main';
	public $dateformat = 'd.m.Y G:i:s';
	public $enableHtmlPurifier = true;
	public $rtepath = false; // Don't use an Rich text Editor
	public $rteadapter = false; // Don't use an Adapter
	public $ckfinderpath = false; // do not use CKFinder

	/* Script snippet to be executed. Examples are:
		 tinyMCE.init({ mode : "textareas", theme : "simple" }); ',
		 or
		 $('#content').ckeditor();
	 */

	public $rtescript = false;

	// Which languages do your cms serve?
	public $languages = array('en' => 'English');
	public $defaultLanguage = 'en';

	// the 'language' metatag does not need to be listed here because it is
	// automatically inserted out of the language of the sitecontent
	public $defaultMetaTags = array();
	public $allowedMetaTags = array(
			'description', 'keywords', 'author', 'revised');

	// Which images are allowed and where to save them ?
	public $allowedImageMimeTypes = array(
			'image/png',
			'image/gif',
			'image/jpg',
			'image/jpeg');
	public $imagePath = 'images/';

	// pageCache is disabled by default because it depends on a configured
	// 'cache' component
	public $pageCache = false;
	public $httpCache = true;

	// If a page is requested by CMS::render and not found, should
	// a 404 be raised or the content simply not be delivered?
	public $strict404raising = false;

	public $sitecontentViewRoute = '//cms/sitecontent/view';
	public $sitecontentCreateRoute = '//cms/sitecontent/create';
	public $sitecontentUpdateRoute = '//cms/sitecontent/update';
	public $sitecontentDeleteRoute = '//cms/sitecontent/delete';
	public $sitecontentAdminRoute = '//cms/sitecontent/admin';
	public $imageAdminRoute = '//cms/sitecontent/adminImages';

	public $sitecontentViewFile = 'view'; 
	public $sitecontentUpdateFile = 'update'; 
	public $searchResultsViewFile = 'results';

	public $enableLiveEdit = true;
	public $enableTooltip = true;

	public function init()
	{
		$this->setImport(array(
			'cms.models.*',
			'cms.components.*',
			'cms.controllers.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			return true;
		}
		else
			return false;
	}
}
