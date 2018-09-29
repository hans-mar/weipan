<?php

namespace common\modules\wizard\generators\migrate;

use Yii;
use common\helpers\Html;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

/**
 * This generator will generate migrate script at current application.
 *
 * @author ChisWill
 */
class Generator extends \common\modules\wizard\Generator
{
    use \common\traits\dbTrait;
    // 虚拟字段
    public $commitUser;
    public $description;
    public $inputSql;
    // 配置
    public $saveFile = 'migrations';
    public $tableName = '{{%migration}}';
    public $appName = null;
    // 内部变量
    private $_cache = null;
    private $_syncErrors = [];
    private $_cacheName = 'wizard-migration-fileinfo';

    public function rules()
    {
        return [
            [['commitUser', 'description', 'inputSql'], 'required'],
            [['commitUser', 'description', 'inputSql'], 'filter', 'filter' => 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'commitUser' => '提交人',
            'inputSql' => 'SQL语句',
            'description' => '描述'
        ];
    }

    public function getSaveFilePath()
    {
        $path = $this->appName ?: Yii::$app->basePath;
        $path .= '/' . $this->saveFile;
        if (!file_exists($path)) {
            FileHelper::mkdir($path);
        }
        return $path;
    }

    public function sync($fileName)
    {
        $data = $this->getFileByName($fileName);

        return $this->syncSql($data['sql'], $fileName);
    }

    protected function syncSql($sql, $fileName)
    {
        try {
            self::db($sql)->execute();
            $this->record($fileName);
            return true;
        } catch (\Exception $e) {
            $this->_syncErrors[] = $e->errorInfo[2];
            return false;
        }
    }

    public function getSyncErrors()
    {
        return $this->_syncErrors;
    }

    public function save()
    {
        $todayFile = FileHelper::findFiles($this->getSaveFilePath(), ['only' => ['prefix' => gmdate('Ymd') . '*']]);
        
        $fileName = gmdate('Ymd_His') . '_' . (count($todayFile) + 1) . '_' . StringHelper::random(6, 'w') . '.data';

        if ($this->write($fileName)) {
            $this->record($fileName);
            return true;
        } else {
            return false;
        }
    }

    protected function write($fileName)
    {
        $currentYearFilePath = $this->getSaveFilePath() . '/' . gmdate('Y');
        if (!file_exists($currentYearFilePath)) {
            FileHelper::mkdir($currentYearFilePath);
        }
        $filePath = $currentYearFilePath . '/' . $fileName;
        $data = [
            'user' => $this->commitUser,
            'sql' => $this->inputSql,
            'desc' => $this->description
        ];
        preg_match_all('/drop|delete/i', $this->inputSql, $warning);
        if (isset($warning[0])) {
            $data['warning'] = array_unique($warning[0]);
        }
        $data = serialize($data);
        // 写入文件
        if (@file_put_contents($filePath, $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function update($fileName)
    {
        return $this->write($fileName);
    }

    protected function prepare()
    {
        // 创建记录表，如果该表不存在的话
        self::db('CREATE TABLE if not exists ' . $this->tableName . ' (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `version` varchar(80) NOT NULL,
                    `apply_time` int(11) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;')->execute();
    }

    public function syncAll()
    {
        set_time_limit(0);
        // 获取所有文件并按时间正序
        $files = $this->getFiles();
        sort($files);
        // 获取历史同步记录
        $history = $this->getHistory();
        $successNum = 0;
        $err = [];
        foreach ($files as $file) {
            $fileName = basename($file);
            if (!array_key_exists($fileName, $history)) {
                $data = $this->getFileByName($fileName);
                if (isset($data['delete'])) {
                    continue;
                }
                if ($this->syncSql($data['sql'], $fileName)) {
                    $successNum++;
                } else {
                    $err['user'] = '提交者：' . $data['user'];
                    $err['desc'] = '描述：' . $data['desc'];
                    $err['reason'] = '原因：' . current($this->_syncErrors);
                    break;
                }
            }
        }
        return [$successNum, $err];
    }

    protected function record($version)
    {
        $this->prepare();

        self::dbInsert($this->tableName, ['version' => $version, 'apply_time' => time()]);
    }

    public function getFileByName($fileName, $getPath = false)
    {
        $path = $this->getSaveFilePath() . '/' . substr($fileName, 0, 4) . '/' . $fileName;

        if ($getPath === true) {
            return $path;
        }

        return unserialize(file_get_contents($path));
    }

    public function delete($fileName)
    {
        $file = $this->getFileByName($fileName, true);
        $data = $this->getFileByName($fileName);
        $data['delete'] = true;
        $data = serialize($data);

        if (@file_put_contents($file, $data)) {
            $this->setCache($file);
            return true;
        } else {
            return false;
        }
    }

    public function getFiles()
    {
        return FileHelper::findFiles($this->getSaveFilePath(), ['only' => ['suffix' => '*.data']]);
    }

    public function getHistory()
    {
        $this->prepare();

        return self::dbQuery()
            ->select(['version', 'apply_time'])
            ->from($this->tableName)
            ->orderBy('id DESC')
            ->map('version', 'apply_time');
    }

    public function getCache()
    {
        if ($this->_cache === null) {
            $this->_cache = cache()->get($this->_cacheName) ?: [];
        }
        return $this->_cache;
    }

    public function deleteCache()
    {
        cache($this->_cacheName, null);
    }

    protected function setCache($file)
    {
        $cache = $this->getCache();

        $fileName = basename($file);

        $data = unserialize(file_get_contents($file));

        $cache[$fileName] = $data;
        cache($this->_cacheName, $cache);
        // 同步当前已经获取的缓存变量
        $this->_cache = $cache;

        return $cache;
    }

    public function dumpInfo($file, $field)
    {
        $cache = $this->getCache();

        $fileName = basename($file);
        if (!isset($cache[$fileName])) {
            $cache = $this->setCache($file);
        }
        if ($field === 'warning' && isset($cache[$fileName]['warning'])) {
            $string = '';
            foreach ($cache[$fileName]['warning'] as $warning) {
                $string .= str_repeat('&nbsp;', 2) . Html::errorSpan(strtoupper($warning));
            }
            return $string;
        }

        return ArrayHelper::getValue($cache[$fileName], $field, '');;
    }
}
