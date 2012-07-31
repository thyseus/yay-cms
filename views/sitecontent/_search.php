<div class="wide form">

<? $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<? echo $form->label($model,'id'); ?>
		<? echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<? echo $form->label($model,'position'); ?>
		<? echo $form->textField($model,'position'); ?>
	</div>

	<div class="row">
		<? echo $form->label($model,'title'); ?>
		<? echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<? echo $form->label($model,'content'); ?>
		<? echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<? echo $form->label($model,'authorid'); ?>
		<? echo $form->textField($model,'authorid'); ?>
	</div>

	<div class="row">
		<? echo $form->label($model,'createtime'); ?>
		<? echo $form->textField($model,'createtime'); ?>
	</div>

	<div class="row">
		<? echo $form->label($model,'updatetime'); ?>
		<? echo $form->textField($model,'updatetime'); ?>
	</div>

	<div class="row buttons">
		<? echo CHtml::submitButton(Yii::t('App','Search')); ?>
	</div>

<? $this->endWidget(); ?>

</div><!-- search-form -->
