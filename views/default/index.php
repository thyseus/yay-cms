<?
$this->breadcrumbs=array(
	$this->module->id,
);

$this->menu = array(
		array(
			'label' => Cms::t('Manage Sitecontent'),
			'url' => array('sitecontent/admin')
			),
		array(
			'label' => Cms::t('Create new Sitecontent'),
			'url' => array('sitecontent/create')
			),
);

?>

<h1> <? echo Yii::t('CmsModule.cms', 'Welcome to your CMS'); ?> </h1> 


