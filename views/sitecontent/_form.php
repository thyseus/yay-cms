<?
	if(Cms::module()->rtepath != false)
		Yii::app()->clientScript-> registerScriptFile(Yii::app()->getModule('cms')->rtepath, CClientScript::POS_HEAD); 
	if(Cms::module()->rteadapter != false)
		Yii::app()->clientScript-> registerScriptFile(Yii::app()->getModule('cms')->rteadapter, CClientScript::POS_HEAD); 
	if(Cms::module()->rtescript != false)
		Yii::app()->clientScript->registerScript('rte_init', Cms::module()->rtescript);
?>
<div class="form">
	<? $form=$this->beginWidget('CActiveForm', array(
				'id'=>'sitecontent-form',
				'enableAjaxValidation'=>true,
				'enableClientValidation'=>true,
				'htmlOptions' => array(
					'enctype' => 'multipart/form-data'),
				'focus'=>array($model,'title'),

				));
	?>

	<? echo $form->errorSummary($model); ?>

	<div class="box-form-right">
        <fieldset>
            <legend><? echo Cms::t('Metatags'); ?></legend>
            <?
                $metatags = $model->metatags;
                
                if(!$metatags)
                    $metatags = array();
    
                foreach(Cms::module()->allowedMetaTags as $metatag) {
									echo '<div class="row">';
									echo $form->labelEx($model, $metatag);	
									$value = '';
									if(isset($metatags[$metatag]))
										$value = $metatags[$metatag];

									if($value == '' && isset(Cms::module()->defaultMetaTags[$metatag]))
										$value = Cms::module()->defaultMetaTags[$metatag];

									echo CHtml::textField("Sitecontent[metatags][$metatag]", 
											$value);
									echo $form->error($model, $metatag);
									echo '</div>';	
                }
            ?>
        </fieldset>

      <fieldset>
            <legend><? echo Cms::t('Images'); ?></legend>
            <?
                $images = $model->images;
                
                if(!$images)
                    $images = array();
    
										if($images && !$model->isNewRecord) {
											echo '<table>';
											foreach($images as $i => $image) {
												printf(
														'<tr><td>%s</td><td>%s</td><td>%s %s %s</td>',
														Yii::app()->baseUrl . '/' . Cms::module()->imagePath. $image,
														Cms::getImage($image, $image, true), 
														count($images) > 1 ? CHtml::link('Up', array(
																'//cms/sitecontent/moveImage',
																'id' => $model->id,
																'language' => $model->language,
																'image' => $image,
																'direction' => 'up')) : '',
														count($images) > 1 ? CHtml::link('Down', array(
																'//cms/sitecontent/moveImage',
																'id' => $model->id,
																'language' => $model->language,
																'image' => $image,
																'direction' => 'down')) : '',
														CHtml::link(
															CHtml::image(
																Yii::app()->getAssetManager()->publish(
																	Yii::getPathOfAlias(
																		'zii.widgets.assets.gridview').'/delete.png')),
															array(
																'//cms/sitecontent/deleteImage',
																'model_id' => $model->id,
																'language' => $model->language,
																'image' => $image),
															array(
																'confirm' => Cms::t(
																	'Are you sure you want to delete this image?')
																)
															)
														);

									}
									echo '</table>';
								}			
else echo Cms::t('No images yet');
									
									echo '<hr /><div class="row">';
									echo $form->labelEx($model, 'image_new');	
									echo CHtml::fileField('image_new', '');
									echo $form->error($model, 'image_new');
									echo '</div>';	

            ?>
        </fieldset>

    </div>

	<div class="box-form-left">
        <fieldset>
            <legend ><? echo Cms::t('Site options'); ?></legend>
            
            <div class="row">
                <? echo $form->labelEx($model,'id'); ?>
                <? echo $form->textField($model,'id',array('size'=>5,'maxlength'=>11)); ?>
                <? echo $form->error($model,'id'); ?>
            </div>
    

            <div class="row">
                <? echo $form->labelEx($model,'parent'); ?>
                <? echo CHtml::activeDropDownList($model,
                        'parent',
												Sitecontent::listData(),
                        array(
                            'empty' => array(
                                '0' => ' - ')));
                ?>
                <? echo $form->error($model,'header'); ?>
            </div>
    
            <div class="row">
                <? echo $form->labelEx($model,'visible'); ?>
                <? echo $form->dropDownList($model, 'visible', $model->itemAlias('visible')); ?>
                <? echo $form->error($model,'visible'); ?>
            </div>
    
    
            <div class="row redirect" style="display: none;">
						<? echo $form->labelEx($model,'redirect'); ?>
						<? echo $form->dropDownList($model,'redirect',
								CHtml::listData(Sitecontent::model()->findAll(), 'id', 'title'),
								array(
									'empty' => Cms::t('Absolute url')
									)); ?>

            <div class="row redirect_absolute" style="display: none;">
						<? echo $form->labelEx($model,'redirect'); ?>
						<? echo $form->textField($model,'redirect',array(
									'id' => 'Sitecontent_redirect_absolute',
									'size'=>40,
									'maxlength'=>255)); ?>
						</div>
						<? echo $form->error($model,'redirect'); ?>
						</div>

            <div class="row password" style="display: none;">
                <? echo $form->labelEx($model,'password'); ?>
                <? echo $form->passwordField($model, 'password'); ?>
            </div>    
           	<div class="row password" style="display: none;">
            	<? echo $form->labelEx($model,'password_repeat'); ?>
                <? echo $form->passwordField($model, 'password_repeat'); ?>
                <div class="hint">
                    <? echo Cms::t('Enter a password to allow access by password'); ?>
                </div>
                
                <div class="hint">
                    <? echo Cms::t('Leave it empty to only allow access by non-guest users'); ?>
                </div>
                
                <? echo $form->error($model,'password'); ?>
            </div>
    
            <? Yii::app()->clientScript->registerScript('dropdown_visible', "
                if($('#Sitecontent_visible').val() == 2)
                    $('.password').show();
                if($('#Sitecontent_visible').val() == 4)
                    $('.redirect').show();

										if($('#Sitecontent_redirect').val() == 0)
										$('.redirect_absolute').show();


            $('#Sitecontent_redirect').change(function() {
								$('#Sitecontent_redirect_absolute').val($(this).val());

	                if($(this).val() == 0)
                    $('.redirect_absolute').show(500);
                else
                    $('.redirect_absolute').hide(500);
						});
            $('#Sitecontent_visible').change(function() {
								$('#Sitecontent_redirect_absolute').val('');
                if($(this).val() == 2)
                    $('.password').show(500);
                else
                    $('.password').hide(500);
                if($(this).val() == 4)
                    $('.redirect').show(500);
                else
                    $('.redirect').hide(500);

            });
            ");
	if($model->isNewRecord)
            Yii::app()->clientScript->registerScript('typeahead', "
	$('#Sitecontent_title').keyup(function() {
		sitecontent_title_browser = $('#Sitecontent_title_browser').val();
		value1 = $(this).val().substr(0, sitecontent_title_browser.length);
		sitecontent_title_url = $('#Sitecontent_title_url').val();
		value2 = $(this).val().substr(0, sitecontent_title_url.length);

		if(sitecontent_title_browser == '' || sitecontent_title_browser == value1)
			$('#Sitecontent_title_browser').val($(this).val());
if(sitecontent_title_url == '' || sitecontent_title_url == value2)
			$('#Sitecontent_title_url').val($(this).val());


});
");
            ?>
    
            <div class="row">
                <? echo $form->labelEx($model,'position'); ?>
                <? for($i = 0; $i <= 99; $i++) $position[] = $i; ?>
                <? echo CHtml::dropDownList('Sitecontent[position]',
                        $model->position,
                        $position); ?>
                <? echo $form->error($model,'position'); ?>
            </div>
    
            <div class="row">
                <? echo $form->labelEx($model,'title'); ?>
                <? echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
                <? echo $form->error($model,'title'); ?>
            </div>
    
            <div class="row">
                <? echo $form->labelEx($model,'title_browser'); ?>
                <? echo $form->textField($model,'title_browser',array('size'=>60,'maxlength'=>80)); ?>
                <? echo $form->error($model,'title_browser'); ?>
            </div>
    
            <div class="row">
                <? echo $form->labelEx($model,'title_url'); ?>
                <? echo $form->textField($model,'title_url',array('size'=>60,'maxlength'=>80)); ?>
                <? echo $form->error($model,'title_url'); ?>
            </div>
    
            <div class="row">
                <? echo $form->labelEx($model,'language'); ?>
                <? echo $form->dropDownList($model,'language',Cms::module()->languages); ?>
                <? echo $form->error($model,'language'); ?>
            </div>
    
            <div class="row">
                <? echo $form->labelEx($model,'content'); ?>
                <? echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
                <? echo $form->error($model,'content'); ?>
            </div>

            <div class="row">
                <? echo $form->labelEx($model,'tags'); ?>
                <? echo $form->textField($model,'tags',array('size'=>60,'maxlength'=>255)); ?>
                <? echo $form->error($model,'tags'); ?>
            </div>

    
        </fieldset>
    </div>
    
    <div class="clear"></div>
     <div class="row buttons">
		 <? echo CHtml::submitButton(Yii::t('CmsModule.cms', 'Save'), array(
					 'id' => 'submit-save')); ?> 
		 <? echo CHtml::submitButton(Yii::t('CmsModule.cms', 'Save and view'), array(
					 'id' => 'submit-view')); ?> 
		 <? echo CHtml::submitButton(Yii::t('CmsModule.cms', 'Save and close'), array(
					 'id' => 'submit-close')); ?> 

            </div>
    <? $this->endWidget(); ?>
</div><!-- form -->
