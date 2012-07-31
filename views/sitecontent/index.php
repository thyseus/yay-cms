<?
$this->breadcrumbs=array(
	'Sitecontents',
);

$this->menu=array(
		array(
			'label'=>Yii::t('CmsModule.cms', 'Manage Menustructure'), 
			'url'=>array('menustructure/admin')
			),
		array(
			'label'=>Yii::t('CmsModule.cms', 'Manage Sitecontent'), 
			'url'=>array('sitecontent/admin')
			),
	array('label'=>Yii::t('App', 'Create').' Sitecontent', 'url'=>array('create')),
	array('label'=>Yii::t('App', 'Manage').' Sitecontent', 'url'=>array('admin')),
);
?>

<h2>Sitecontents</h2>

<? $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
