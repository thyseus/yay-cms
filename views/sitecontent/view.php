<?                                                                           
if(isset($sc))
	$this->pageTitle = $sc->title_browser; 
else if(isset($menu))
	$this->pageTitle = $menu->title_browser;
if(isset($this->breadcrumbs))
	$this->breadcrumbs = $sitecontent->getBreadcrumbs();

if(Yii::app()->user->id == 1) // is admin {
if(is_object($sitecontent) && $sitecontent instanceof Sitecontent) 
	$this->renderPartial('draw_editable', array('sitecontent' => $sitecontent));
	else if ($sitecontent == array()) {
		echo CHtml::link(Cms::t('Create new sitecontent here'),
				array(Cms::module()->sitecontentCreateRoute, 'position' => $menu->id));
	} else if (is_array($sitecontent))  {
		foreach($sitecontent as $sc) {
			$this->renderPartial('draw_editable', array('sitecontent' => $sc));
		}
	}
else
{
	if(!is_null($sitecontent))
		if(is_object($sitecontent))
			$this->renderPartial('draw', array('sitecontent' => $sitecontent));
		else
			foreach($sitecontent as $sc)
			{
				$this->renderPartial('draw', array('sitecontent' => $sc));
			}

}

if(isset($menu))
	$this->breadcrumbs = array($menu->title);

