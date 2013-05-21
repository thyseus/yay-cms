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

<h3><? echo Cms::t('Update {languages} version of {title}', array(
			'{languages}' => CHtml::dropDownList(
				'languages', $model->language, Cms::module()->languages),
			'{title}' => $model->title)); ?></h3>

<? Yii::app()->clientScript->registerScript('dropdown_language', "
		$('#languages').change(function() {
			window.location='"
			.$this->createUrl(
				Cms::module()->sitecontentUpdateRoute, array(
					'id' => $model->id))."' + '?lang=' + $(this).val();});
		");
?>

<? echo $this->renderPartial('_form', array('model'=>$model)); ?>
