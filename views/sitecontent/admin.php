<?
$this->breadcrumbs=array(
	Cms::t('Sitecontent')=>array('index'),
	Cms::t('Manage'),
);

$this->menu=array(
		array(
			'label'=>Cms::t('Manage Sitecontent'), 
			'url'=>array('sitecontent/admin')
			),
		array(
			'label'=>Cms::t('Create new Sitecontent'),
			'url'=>array('create')),
		);


?>

<h2><? echo Cms::t('Manage Sitecontent'); ?></h2>

<? 
$this->widget('application.modules.cms.components.CEditableGridView', array(
			'id'=>'sitecontent-grid',
			'dataProvider'=>$model->search(),
			'template' => '{summary} {pager} <br /> {items} {pager}',
			'filter'=>$model,
			'columns'=>array(
				array(
					'name' => 'id',
					'headerHtmlOptions' => array(
						'style' => 'width:25px;',
						),
						'htmlOptions' => array(
							'class' => 'editable',
							'rel' => 'id')),
				array(
					'name' => 'parent',
					'value' => '$data->Parent ? $data->Parent->title_url : "-"',
					'filter' => Sitecontent::listData(),
					'headerHtmlOptions' => array(
						'style' => 'width:100px;',
						),
					),
				array(
					'name' => 'language',
					'filter' => Cms::module()->languages,
					'headerHtmlOptions' => array(
						'style' => 'width:25px;',
						),
						'htmlOptions' => array(
							'class' => 'editable',
							'rel' => 'language')),
				array('name' => 'title',
						'htmlOptions' => array(
							'class' => 'editable',
							'rel' => 'title')),
				array(
						'name' => 'title_url',
						'type' => 'raw',
						'value' => '\'<p class="tooltip">\'.$data->title_url.\'</p><p class="tooltip-content">\'.CHtml::encode(substr($data->content, 0, 500)).\'</p>\'',
						),

				array(
						'name' => 'position',
						'headerHtmlOptions' => array(
							'style' => 'width:5px;',
							),
						),
				array(
					'name'=>'createtime',
					'value'=>'date(Cms::module()->dateformat, $data->createtime)',
					'filter' => false,
					'headerHtmlOptions' => array(
						'style' => 'width:110px;',
						),
				),
				array(
					'name'=>'updatetime',
					'value'=>'date(Cms::module()->dateformat, $data->updatetime)',
					'filter' => false,
					'headerHtmlOptions' => array(
						'style' => 'width:110px;',
						),
				),
				array(
					'name' => 'visible',
					'value' => '$data->itemAlias("visible", $data->visible)',
					'filter' => Sitecontent::itemAlias('visible'),
					'headerHtmlOptions' => array(
						'style' => 'width:50px;',
						),

					),
			array(
					'name' => 'tags',
					'headerHtmlOptions' => array(
						'style' => 'width:100px;',
						),
					),
				array(
					'class'=>'CButtonColumn',
					'viewButtonUrl' => 'Yii::app()->controller->createUrl(
						"//cms/sitecontent/view", array( "page" => $data->title_url))',
					),
				),
			)); ?>

			<? echo CHtml::link(
					Cms::t('Create new Sitecontent'), array(
						'//cms/sitecontent/create'), array('tabindex' => 1)); ?>

<?
	if(Cms::module()->enableTooltip) {
		Yii::app()->clientScript->registerScriptFile(
				Yii::app()->getAssetManager()->publish(
					Yii::getPathOfAlias(
						'application.modules.cms.assets').'/jquery.tooltip.js'));

		Yii::app()->clientScript->registerScript(
				'tooltip', "$('.tooltip').tooltip({
			position: 'center left',
			offset: [10, 2]});");
	}
?>
