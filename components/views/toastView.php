<?php $widget = $this->beginWidget(
		'application.modules.cms.components.toastMessageWidget',array(
			'message'=>$message,
			'type'=>$type,
			'options'=>$options
			)); ?>
<?php $this->endWidget(); ?>
