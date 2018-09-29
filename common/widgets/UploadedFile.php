<?php

namespace common\widgets;

use Yii;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;

/**
 * 加强 UploadedFile 的功能，增加公共的快捷方法
 * 
 * @author ChisWill
 */
class UploadedFile extends \yii\web\UploadedFile
{
    /**
     * @var string 上传文件保存的相对路径
     */
    protected $_uploadPath;
    /**
     * @var string 上传文件的保存物理路径
     */
    protected $_savePath;
    /**
     * @var string 上传文件的实际名称
     */
    protected $_realName;
    /**
     * @var string 上传文件保存的绝对路径
     */
    protected $_filePath;

    public function init()
    {
        parent::init();

        $this->_uploadPath = config('uploadPath') . '/' . date('Ymd') . '/';
    }

    /**
     * 获取不重复的随机文件名
     * 
     * @return string
     */
    protected function getRandName()
    {
        do {
            $newName = date('His') . rand(100000, 999999) . '.' . $this->extension;
        } while (file_exists($this->_savePath . $newName));

        return $newName;
    }

    /**
     * 获取文件保存的实际名称
     * 
     * @return string
     */
    public function getRealName()
    {   
        return $this->_realName;
    }

    /**
     * 获取文件保存的绝对路径
     * 
     * @return string
     */
    public function getFilePath()
    {
        return $this->_filePath;
    }

    /**
     * 获取文件原名
     * 
     * @return string
     */
    public function getOriginName()
    {
        return $this->baseName . '.' . $this->extension;
    }

    /**
     * 移动文件
     * 
     * @param  string $saveFile 保存的文件夹名
     * @return boolean
     */
    public function move($saveFile = '')
    {
        if ($saveFile) {
            $this->_uploadPath .= $saveFile . '/';
        }
        $this->_savePath = Yii::getAlias('@webroot' . $this->_uploadPath);
        FileHelper::mkdir($this->_savePath);

        $this->_realName = $this->getRandName();
        $this->_filePath = $this->_uploadPath . $this->_realName;

        return $this->saveAs($this->_savePath . $this->_realName);
    }
}
