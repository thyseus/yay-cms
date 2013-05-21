<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo Yii::app()->language; ?>" lang="<?php echo Yii::app()->language; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><? echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body id="cms">
		<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">

	<div class="container">
	<?php echo CHtml::link(Yii::app()->name, array(
				'//cms/sitecontent/admin'), array(
	'class' => 'brand')); ?>

	<?php 
	$this->widget('zii.widgets.CMenu',array(
				'htmlOptions' => array('class' => 'nav'),
				'items'=>
				array(
					array(
						'label'=>Cms::t('Manage Sitecontent'), 
						'url'=>array(Cms::module()->sitecontentAdminRoute),
						),
					array(
						'label'=>Cms::t('Create new Sitecontent'),
						'url'=>array(Cms::module()->sitecontentCreateRoute)),
					),
				)
			);
	?>
	<form method="POST" action="<?php echo Yii::app()->createUrl('//cms/sitecontent/search'); ?>" class="navbar-search pull-right">
	<input name="search" type="text" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" class="search-query" placeholder="<?php echo Cms::t('Search'); ?>">
</form>
			</div>	
		</div>	
	</div>	

	<div class="container" style="padding-top: 50px;">
		<div class="row">
			<div class="span12">
       
			<?php echo Cms::renderFlash(); ?> 
        
     	<?php echo $content; ?>
		</div>
	</div>
         
    	<div class="clearfix"> </div>
    
			<footer>	
            <p> <?php echo CHtml::link('CMS Module', 'http://github.com/thyseus/yay-cms'); ?> by thyseus@gmail.com | <?php echo Yii::powered(); ?> </p>
			</footer>
    </div><!-- container -->

</body>
</html>

