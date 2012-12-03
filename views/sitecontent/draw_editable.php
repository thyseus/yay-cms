<?
echo CHtml::link(Cms::t('Edit this sitecontent'), array(
			Cms::module()->sitecontentUpdateRoute,
			'id' => $sitecontent->id,
			'lang' => $sitecontent->language));
echo '<div style="clear: both;"></div>';

if(Cms::module()->enableHalloJs) 
	echo '<div class="editable">';
echo $sitecontent->content; 
if(Cms::module()->enableHalloJs) 
	echo '</div';
echo '<div style="clear: both;"></div>';
echo CHtml::link(Cms::t('Edit this sitecontent'), array(
			Cms::module()->sitecontentUpdateRoute,
			'id' => $sitecontent->id,
			'lang' => $sitecontent->language));

if(Cms::module()->enableHalloJs) {
	Yii::app()->clientScript->registerCoreScript('jquery.ui');
	Yii::app()->clientScript->registerCssFile(
			Yii::app()->getAssetManager()->publish(
				Yii::getPathOfAlias(
					'application.modules.cms.assets').'/hallo.css'));

	Yii::app()->clientScript->registerScriptFile(
			Yii::app()->getAssetManager()->publish(
				Yii::getPathOfAlias(
					'application.modules.cms.assets').'/hallo.js'));

			Yii::app()->controller->renderPartial(
					'application.modules.cms.components.views.toastView',array(
						'message'=>Cms::t('Live editing is activated'),
						'type'=>'notice',
						'options'=>array(
							'sticky'=>false,
							'position'=>'top-right',
							'stayTime'=>5000)),false,true);	

Yii::app()->clientScript->registerScript('hallo.js', "
function saveContent() {
	$.ajax({
type: 'POST',
url: '".$this->createUrl('//cms/sitecontent/updateValue')."',
data: {
	'id': ".$sitecontent->id.",
	'language': '".$sitecontent->language."',
	'column': 'content',
	'value': $('.editable').html(),
}, 
success: function() {
	$().toastmessage('showSuccessToast', 'Content has been saved', {
'stayTime': 1000,
'inEffectDuration': 100,
'sticky': false,
});

},
error: function() {
	$().toastmessage('showErrorToast', 'Error while saving content !', {
	'sticky': true,
});
}
});

}
jQuery('.editable').hallo({plugins: {
	'halloformat': {},
	'halloheadings': {},
	'hallojustify': {},
	'hallolists': {},
	'halloreundo': {}, 
	'hallolink': {},
	'halloimage': {},
}});

lastsave = 0;
jQuery('.editable').bind('hallodeactivated', function() {saveContent();});

// auto-save all 10 seconds
jQuery('.editable').bind('hallomodified', function(event, data) {
var d = new Date();
var currenttime = d.getTime();
var interval = currenttime - lastsave;
if(interval >= 10000) {
	lastsave = currenttime;
	saveContent();
}
}); 
");
}
?>
