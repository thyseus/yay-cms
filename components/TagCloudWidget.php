<?
class TagCloudWidget extends CWidget
{
	public $cacheDuration = 3600;
	public $limit = 10;
	public $linkUrl = '//cms/sitecontent/search';

	public function init() {
		parent::init();
	}

	public function run() {

		if($this->beginCache('yay_cms_tag_cloud', array(
						'duration' => $this->cacheDuration ))) { 
			$tags = array();
			$result = Yii::app()->db->createCommand()
				->select('tags')
				->from('sitecontent')
				->where('visible = 3')
				->order('tags')
				->queryAll();

			foreach($result as $record) {
				$words = explode(',', strip_tags($record['tags']));	
				if($words) {
					foreach($words as $word) {
							$word = CHtml::encode($word);
							if(isset($tags[$word]))
								$tags[trim($word)]++;
							else
								$tags[trim($word)] = 1;
					}
				}
			}

			$i = 0;
			foreach($tags as $key => $tag) {
				if($i > $this->limit)
					unset($tags[$key]);
				$i++;
			}

			$this->render('tagcloud', array(
						'tags' => $tags,
						'linkUrl' => $this->linkUrl,
						));
			$this->endCache();
		}
	}
} 
?>
