<?
$this->breadcrumbs=array(
	Yii::t('CmsModule.cms', 'Sitecontent')=>array('admin'),
	$model->id=>array('view','id'=>$model->id),
	Yii::t('CmsModule.cms', 'Update'),
);

$this->menu=array(
		array(
			'label'=>Yii::t('CmsModule.cms', 'Manage Sitecontent'), 
			'url'=>array('sitecontent/admin')
			),
);
?>

<h2><? echo Cms::t('Update');?> <? echo $model->title; ?></h2>

<? echo $this->renderPartial('_form', array('model'=>$model)); ?>
