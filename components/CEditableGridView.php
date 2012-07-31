<?

/**
 * CEditableGridView represents a grid view which contains editable rows
 * and an optional 'Quickbar' which fires an action that quickly adds
 * entries to the table.
 *
 * To make a Column editable you have to assign it to the class 'CEditableColumn'
 *
 * Use it like the CGridView:
 *
 * $this->widget('zii.widgets.grid.CEditableGridView', array(
 *     'dataProvider'=>$dataProvider,
 *     'showQuickBar'=>'true',
 *     'quickCreateAction'=>'QuickCreate', // will be actionQuickCreate()
 *     'columns'=>array(
 *           'title',          // display the 'title' attribute
 *            array('header' => 'editMe', 'name' => 'editable_row', 'class' => 'CEditableColumn')
 *     ));
 *
 * With this Config, the column "editable_row" gets rendered with
 * inputfields. The Table-header will be called "editMe".
 *
 * You have to define a action that receives $_POST data like this:
 *   public function actionQuickCreate() {
 *	   $model=new Model;
 *      if(isset($_POST['Model']))
 *       {
 * 	      $model->attributes=$_POST['Model'];
 * 	      if($model->save())
 * 	      $this->redirect(array('admin')); //<-- assuming the Grid was used unter view admin/
 *       }
 *     }
 *
 * @author Herbert Maschke <thyseus@gmail.com>
 * @package zii.widgets.grid
 * @since 1.1
 */

Yii::import('zii.widgets.grid.CGridView');

class CEditableGridView extends CGridView {
	public $ajaxUpdate = false;
	public $updateUrl='QuickUpdate';

	public function renderTableBody() {
		parent::renderTableBody();

		Yii::app()->clientScript->registerScript('editable', "
				function updateSitecontent(pk, language, column, value) {

				$.ajax({url: '".Yii::app()->controller->createUrl('//cms/sitecontent/updateValue')."',
					type: 'POST',
					success: function() {
					$('input.quickedit').each(function(i) {
						$(this).addClass('success');
						});
					},
error: function() {
$('input.quickedit').each(function(i) {
	$(this).addClass('failure');
	});
},

data: { 'id': pk,
'language' : language,
'column': column,
'value': value,
'YII_CSRF_TOKEN': '".Yii::app()->request->csrfToken."'}});
}", CClientScript::POS_HEAD);

Yii::app()->clientScript->registerScript('editable', "
		$('.grid-view td.editable').click(function() {
			value = $(this).html();
			pk = $(this).parent('tr').children('td:first').html();
			language = $(this).parent('tr').children('td:first').next().next().html();
			column = $(this).attr('rel');
			element = '<input class=\"quickedit\" style=\"width:100%\" value=\"'+value+'\" type=text name=\"'+column+'_'+pk+'\" onblur=\"updateSitecontent(pk, language, column, value);\"></input>';

			$(this).replaceWith(element);
			});
		", CClientScript::POS_END);
}

}
