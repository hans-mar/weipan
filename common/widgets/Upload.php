<?php

namespace common\widgets;

use Yii;
use yii\web\UploadedFile;
use common\helpers\Html;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;

/**
 * 简易通用型文件上传组件
 * ps1.上传按钮的name属性应为 name="Upload[file]"，其中`Upload`是固定的，`file`是可选的
 * ps2.上传按钮的name属性，不能和该类的属性重名!
 *
 * @author ChisWill
 */
class Upload extends \yii\base\Model
{
    /**
     * 上传按钮的name属性
     * @var string
     */
    public $uploadName = 'uploadFile';
    /**
     * 是否必须上传图片
     * @var boolean
     */
    public $required = true;
    /**
     * 上传文件格式的限制
     * @var string
     */
    public $extensions = 'png, jpg, gif';
    /**
     * 上传文件大小，单位为KB
     * @var integer
     */
    public $maxSize = 2048;
    /**
     * 未上传文件的错误提示信息
     * @var string
     */
    public $message = '必须上传图片!';
    /**
     * 上传文件过大时的错误提示信息
     * @var string
     */
    public $tooBig = '上传的文件最大不能超过2M！';
    /**
     * 上传文件格式不正确时的错误提示信息
     * @var string
     */
    public $wrongExtension = '只支持上传 {ext} 的文件!';
    /**
     * 批量上传的数量限制
     * @var integer
     */
    public $maxFiles = 10;
    /**
     * 上传文件过多时的错误提示信息
     * @var string
     */
    public $tooMany = '最多上传 {num} 个文件！';
    /**
     * 上传目录
     * @var string
     */
    public $uploadPath = null;
    
    /**
     * @var string 上传规则
     */
    protected $_rules;
    /**
     * @var string 上传文件的实际名称
     */
    protected $_realName = [];
    /**
     * @var string 上传文件的原始名称
     */
    protected $_originName = [];
    /**
     * @var string 上传文件的保存物理路径
     */
    protected $_savePath;
    /**
     * @var string 上传文件保存的相对路径
     */
    protected $_uploadPath;
    /**
     * @var string 是否为批量上传
     */
    protected $_isMultiple;
    /**
     * @var string 上传文件的实例存储容器
     */
    protected static $_files = [];

    public function init()
    {
        parent::init();

        $this->setUploadPath();
        // 图片最终存储的物理路径
        $this->_savePath = Yii::getAlias('@webroot' . $this->_uploadPath);
        // 错误后缀的错误提示描述
        $this->wrongExtension = str_replace('{ext}', $this->extensions, $this->wrongExtension);
        $this->_rules = [
            [
                [$this->uploadName],
                'file',
                'skipOnEmpty' => !$this->required, //如果没有上传，是否忽略所有规则
                'uploadRequired' => $this->message,
                'extensions' => $this->extensions,
                'wrongExtension' => $this->wrongExtension,
                'maxSize' => $this->maxSize * 1000,
                'tooBig' => $this->tooBig
            ],
        ];
        // 批量上传时，增加额外的规则
        if ($this->isMultiple()) {
            $this->_rules[0]['maxFiles'] = $this->maxFiles;
            $this->tooMany = str_replace('{num}', $this->maxFiles, $this->tooMany);
            $this->_rules[0]['tooMany'] = $this->tooMany;
        }
    }

    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (\yii\base\UnknownPropertyException $e) {
            if (array_key_exists($name, self::$_files)) {
                return self::$_files[$name];
            } else {
                return '';
            }
        }
    }

    public function __set($name, $value)
    {
        try {
            parent::__set($name, $value);
        } catch (\yii\base\UnknownPropertyException $e) {
            self::$_files[$name] = $value;
        }
    }

    /**
     * 验证上传文件，参数只是为了适配与父类同名方法，实际无意义
     * 
     * @return boolean
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        if ($this->isMultiple()) {
            return $this->checkMultiple();
        } else {
            return $this->checkSingle();
        }
    }

    /**
     * 保存上传文件
     * 
     * @param  boolean $validate 是否启用验证
     * @return boolean
     */
    public function move($validate = true)
    {
        if ($this->isMultiple()) {
            return $this->uploadMultiple($validate);
        } else {
            return $this->uploadSingle($validate);
        }
    }

    /**
     * 获取上传文件的实际保存名称
     * 
     * @return string|array
     */
    public function getRealName()
    {
        if ($this->isMultiple()) {
            return $this->_realName;
        } else {
            return current($this->_realName);
        }
    }

    /**
     * 获取上传文件的原始名称
     * 
     * @return string|array
     */
    public function getOriginName()
    {
        if ($this->isMultiple()) {
            return $this->_originName;
        } else {
            return current($this->_originName);
        }
    }

    /**
     * 获取文件保存的绝对路径
     * 
     * @return string
     */
    public function getFilePath()
    {
        if ($this->isMultiple()) {
            return array_map(function ($name) {
                return $this->_uploadPath . $name;
            }, $this->_realName);
        } else {
            return $this->_uploadPath . $this->realName;
        }
    }

    /**
     * 验证规则
     * 
     * @return array
     */
    public function rules()
    {
        return $this->_rules;
    }

    /**
     * 设置上传路径
     * 
     * @return string
     */
    protected function setUploadPath()
    {
        if ($this->_uploadPath === null)  {
            $uploadPath = $this->uploadPath ? '/' . trim($this->uploadPath, '/') . '/' : '/';
            $this->_uploadPath = config('uploadPath') .  $uploadPath . date('Ymd') . '/';
        }
        
        return $this;
    }

    /**
     * 验证是否是批量上传
     * 
     * @return boolean
     */
    protected function isMultiple()
    {
        if ($this->_isMultiple === null) {
            $file = ArrayHelper::getValue($_FILES, 'Upload.name', []);
            $this->_isMultiple = $file && isset($file[$this->uploadName]) && is_array($file[$this->uploadName]);
        }

        return $this->_isMultiple;
    }

    /**
     * 设置上传文件的实例
     * 
     * @return void
     */
    protected function setInstance()
    {
        $name = $this->uploadName;
        $this->$name = UploadedFile::getInstance($this, $name);
    }

    /**
     * 获取批量上传文件的实例
     * 
     * @return void       
     */
    protected function setInstances()
    {
        $name = $this->uploadName;
        $this->$name = UploadedFile::getInstances($this, $name);
    }

    /**
     * 设置上传文件的实际保存名称
     * 
     * @param  object $file 上传文件实例
     * @return string
     */
    protected function setRandName($file = null)
    {
        $name = $this->uploadName;
        $file or $file = $this->$name;
        do {
            $newName = date('His') . rand(100000, 999999) . '.' . $file->extension;
        } while (file_exists($this->_savePath . $newName));

        $this->_realName[] = $newName;
        $this->_originName[] = $file->name;

        return $newName;
    }

    /**
     * 单个上传文件的验证
     * 
     * @return boolean
     */
    protected function checkSingle()
    {
        $this->setInstance();

        return parent::validate();
    }

    /**
     * 批量上传文件的验证
     * 
     * @return boolean
     */
    protected function checkMultiple()
    {
        $this->setInstances();

        return parent::validate();
    }

    /**
     * 单个上传文件的保存
     * 
     * @param  boolean $validate 是否启用验证
     * @return boolean
     */
    protected function uploadSingle($validate = true)
    {
        $name = $this->uploadName;

        $this->setInstance();

        $check = true;
        if ($validate === true) {
            $check = $this->checkSingle();
        }

        if ($this->$name && $check) {
            FileHelper::mkdir($this->_savePath);
            $realName = $this->setRandName();
            $this->$name->saveAs($this->_savePath . $realName);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 批量上传文件的保存
     * 
     * @param  boolean $validate 是否启用验证
     * @return boolean
     */
    protected function uploadMultiple($validate = true)
    {
        $name = $this->uploadName;

        $this->setInstances();

        $check = true;
        if ($validate === true) {
            $check = $this->checkMultiple();
        }

        if ($this->$name && $check) {
            FileHelper::mkdir($this->_savePath);
            foreach ($this->$name as $key => $file) {
                $realName = $this->setRandName($file);
                $file->saveAs($this->_savePath . $realName);
            }
            return true;
        } else {
            return false;
        }
    }
}
