<div class="view">

	<b><? echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<? echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><? echo CHtml::encode($data->getAttributeLabel('position')); ?>:</b>
	<? echo CHtml::encode($data->position); ?>
	<br />

	<b><? echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b>
	<? echo CHtml::encode($data->title); ?>
	<br />

	<b><? echo CHtml::encode($data->getAttributeLabel('content')); ?>:</b>
	<? echo CHtml::encode($data->content); ?>
	<br />

	<b><? echo CHtml::encode($data->getAttributeLabel('authorid')); ?>:</b>
	<? echo CHtml::encode($data->authorid); ?>
	<br />

	<b><? echo CHtml::encode($data->getAttributeLabel('createtime')); ?>:</b>
	<? echo CHtml::encode($data->createtime); ?>
	<br />

	<? /*
	<b><? echo CHtml::encode($data->getAttributeLabel('updatetime')); ?>:</b>
	<? echo CHtml::encode($data->updatetime); ?>
	<br />

	*/ ?>

</div>
