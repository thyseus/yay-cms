<?
$this->breadcrumbs=array(
	Yii::t('CmsModule.cms', 'Sitecontent')=>array('admin'),
	Yii::t('CmsModule.cms', 'Create'),
);

$this->menu=array(
		array(
			'label'=>Cms::t('Manage Sitecontent'), 
			'url'=>array('admin')
			),
);
?>

<h2><? echo Cms::t('Create new Sitecontent'); ?></h2>

<? echo $this->renderPartial('_form', array('model'=>$model)); ?>
