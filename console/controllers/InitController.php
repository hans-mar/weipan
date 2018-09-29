<?php

namespace console\controllers;

use common\helpers\System;
use console\models\GatherHuobi;
use console\models\GatherSina;
use console\models\GatherSinaStock;
use console\models\GatherXinfu;
use console\models\GatherYiyuan;

class InitController extends \common\components\ConsoleController {
	public function actionUser() {
		echo 'Input User Info' . "\n";

		$username = $this->prompt('Input Username:');
		$password = $this->prompt('Input password:');

		$user = new \frontend\models\User;

		$user->username = $username;
		$user->password = $password;
		$user->setPassword();

		if (!$user->save()) {
			foreach ($user->getErrors() as $field => $errors) {
				array_walk($errors, function ($error) {
					echo "$error\n";
				});
			}
		}
	}

	public function actionHq() {
		$cnt = 0;
		$path = System::isWindowsOs() ? '' : './';
		while (true) {
			$cnt++;
			// echo exec('yii init/gather2');
			echo exec('yii init/gather');
			echo exec('yii init/gather6');
			sleep(1);
			// echo cnt;
			// echo date('h:i:sa').'\n';
			if ($cnt > 9223372036854775807) {
				break;
			}

		}
	}

	public function actionGather() {
		$gather = new GatherSina;
		$gather->run();
	}

	public function actionGather2() {
		$gather = new GatherXinfu;
		$gather->run();
	}

	public function actionGather3() {
		$gather = new GatherSinaStock;
		$gather->run();
	}

	public function actionGather4() {
		$gather = new GatherYiyuan;
		$gather->run();
	}

	public function actionGather6() {

		$gather = new GatherHuobi;
		$gather->run();
	}
}
