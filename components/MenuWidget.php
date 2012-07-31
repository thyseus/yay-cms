<?
Yii::import('application.modules.cms.models.Sitecontent');

class MenuWidget extends CWidget
{
	public $point;
	protected $menu;

	public function init()
	{
		if($this->point == 0)
			throw new CException("Please provide a menu to render");

		parent::init();
		$this->menu = Sitecontent::model()->findAll(array(
					'condition' => 'parent = :point',
					'params' => array(':point' => $this->point),
					'order' => 'position',
					)
				);

		$items = array();
		if($this->menu)
			foreach($this->menu as $point) {
					$items[] = array('label' => $point->title,
							'active' => stripos(Yii::app()->request->url, $point->title_url) !== false,
							'url' => array('/site/view', 'page' => $point->title_url));
			}

		$this->widget('zii.widgets.CMenu',array(
					'items'=>$items
					));  
	}

} 
?>
