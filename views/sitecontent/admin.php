<style>
#sitecontent-grid input { width: 50px !important; }
#sitecontent-grid select { width: 50px !important; }
</style>
<?php
$this->breadcrumbs=array(
		Cms::t('Sitecontent')=>array(
			Cms::module()->sitecontentAdminRoute),
		Cms::t('Manage'),
		);
$this->pageTitle = Cms::t('Manage Sitecontent'); 

$columns = array(
		array(
			'class'=>'CButtonColumn',
			'headerHtmlOptions' => array(
				'style' => 'width:25px;',
				),
			'viewButtonUrl' => 'Yii::app()->controller->createUrl(
				Cms::module()->sitecontentViewRoute, array( "id" => $data->id, "lang" => $data->language))',
			'updateButtonUrl' => 'Yii::app()->controller->createUrl(
				Cms::module()->sitecontentUpdateRoute, array( "id" => $data->id, "lang" => $data->language))',
			'deleteButtonUrl' => 'Yii::app()->controller->createUrl(
				Cms::module()->sitecontentDeleteRoute, array( "id" => $data->id, "lang" => $data->language))',
			),
		array(
			'name' => 'id',
			'headerHtmlOptions' => array(
				'style' => 'width:25px;',
				),
			),
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
				),
		array('name' => 'title'),
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
				'htmlOptions' => array(
					'rel' => 'tags',
					),
				),
		);

$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'sitecontent-grid',
			'dataProvider'=>$model->search(),
			'template' => '{summary} {pager} {items} {pager}',
			'htmlOptions' => array('class' => 'table table-condensed table-striped'),
			'filter'=>$model,
			'columns'=> $columns,
			)); ?>

<? echo CHtml::link(
		Cms::t('Create new Sitecontent'), array(
			Cms::module()->sitecontentCreateRoute), array(
			'tabindex' => 1, 'class' => 'btn')); ?>
