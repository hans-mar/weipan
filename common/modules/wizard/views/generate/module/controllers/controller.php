<?php
echo "<?php\n";
?>

namespace <?= $namespace ?>;

use Yii;

class SiteController extends \common\components\WebController
{
    public $layout = 'main';

    public function actionIndex()
    {
        $this->view->title = '首页 - <?= $moduleName ?>';

        return $this->render('index');
    }
}
