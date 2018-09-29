<?php use common\helpers\Html; ?>
<?php use common\helpers\FileHelper; ?>
<?php common\modules\wizard\assets\WizardAsset::register($this) ?>

<?php $items = array_combine($apps, $apps) ?>

<?php $form = self::beginForm(['id' => 'wizardForm', 'action' => self::createUrl(['generate'])]) ?>
<div id="wizard-toolbar">
    <input type="hidden" name="action" id="submitType">

    <div class="wizard-area">
        <div>
            <span class="wizard-title">模型：</span>
            <span class="wizard-question"></span>
            <span class="wizard-hint">
                <b>主要功能：生成数据库表对应的 Yii2 模型</b><br>
                表名：要生成的模型对应的数据库表名（不包含表前缀）<br>
                项目：必须选择以下中的一项<br>
                -common：表示生成的是公共模型，将会在公共目录和当前项目中一起创建，当前项目中的模型将会继承自公共目录中的模型<br>
                -<?= FileHelper::getCurrentApp() ?>：表示生成的模型仅在当前项目中使用，故仅在当前项目中创建模型<br>
                -其他：输入要创建模型的完整命名空间名称，仅在指定处创建<br>
                ps.反复创建将会覆盖主模型中的 `rules()`、`attributeLabels()`和`search()` 方法
            </span>
        </div>
        <div><input type="text" class="wizard-input" name="Wizard[tableName]" placeholder="表名"></div>
        <div>
            <?= Html::dropDownList('Wizard[isExtend]', common\components\ARModel::STATE_VALID, $extendItems, ['class' => 'applicationSelect', 'prompt' => '指定位置']) ?>
            <input type="text" class="wizard-input wizard-hidden" name="Wizard[modelNamespace]" placeholder="命名空间">
        </div>
        <div><input type="button" value="生成模型" class="wizard-submit" data-action="generateModel"></div>
    </div>

    <div class="wizard-area">
        <div>
            <span class="wizard-title">字段：</span>
            <span class="wizard-question"></span>
            <span class="wizard-hint">
                <b>主要功能：生成模型中字段对应的 `getValue()` 方法和 `getMap()` 方法</b><br>
                模型名：仅写类名，将会在公共模型出创建，否则需指定完整类名<br>
                字段名：要生成方法的字段名称
            </span>
        </div>
        <div><input type="text" class="wizard-input" name="Wizard[modelName]" placeholder="模型名"></div>
        <div><input type="text" class="wizard-input" name="Wizard[fieldName]" placeholder="字段名"></div>
        <div><input type="button" value="生成方法" class="wizard-submit" data-action="generateField"></div>
    </div>

    <div class="wizard-area">
        <div>
            <span class="wizard-title">迁移：</span>
            <span class="wizard-question"></span>
            <span class="wizard-hint">
                <b>主要功能：以规范化的方式记录数据库结构的变动，并提供一键式同步数据库表的结构</b><br>
                创建：点击后将在弹窗中，按照表单内容填写信息即可，系统会自动创建同步记录文件，代码提交时必须一并提交！<br>
                同步迁移：代码更新至最新后，即可点击该按钮一键同步别人创建的数据迁移记录
            </span>
        </div>
        <div>
            <?php if (YII_ENV === 'dev'): ?>
            <?= Html::likeSpan('创建', ['id' => 'create-migration', 'class' => 'iframe-fancybox fancybox.iframe', 'href' => self::createUrl(['/wizard/migrate/create-migration'])]) ?>
            <?php else: ?>
            <?= Html::likeSpan('查看', ['id' => 'create-migration', 'class' => 'iframe-fancybox fancybox.iframe', 'href' => self::createUrl(['/wizard/migrate/history-list'])]) ?>
            <?php endif ?>
        </div>
        <div><input type="button" value="同步迁移" class="wizard-submit" data-action="generateMigrate"></div>
    </div>

    <div class="wizard-area wizard-area-more">
        <a href="javascript:;" class="wizard-more" id="wizardMoreBtn">更多</a>
    </div>

    <div class="wizard-area wizard-area-hidden">
        <div>
            <span class="wizard-title">模块：</span>
            <span class="wizard-question"></span>
            <span class="wizard-hint">
                <b>主要功能：快速生成新模块模板</b><br>
                模块名：不能和PHP关键字重名<br>
                项目：将会在指定项目的modules目录下创建模块，或可以自定义命名空间进行创建
            </span>
        </div>
        <div><input type="text" class="wizard-input" name="Wizard[moduleName]" placeholder="模块名"></div>
        <div>
            <?= Html::dropDownList('Wizard[moduleApp]', FileHelper::getCurrentApp(), $items, ['class' => 'applicationSelect', 'prompt' => '指定位置']) ?>
            <input type="text" class="wizard-input wizard-hidden" name="Wizard[moduleNamespace]" placeholder="命名空间">
        </div>
        <div><input type="button" value="生成模块" class="wizard-submit" data-action="generateModule"></div>
    </div>

    <div class="wizard-area wizard-area-hidden">
        <div>
            <span class="wizard-title">项目：</span>
            <span class="wizard-question"></span>
            <span class="wizard-hint">
                <b>主要功能：快速生成新项目模板</b><br>
                项目名：不能和PHP关键字重名
            </span>
        </div>
        <div><input type="text" class="wizard-input" name="Wizard[appName]" placeholder="项目名"></div>
        <div><input type="button" value="生成项目" class="wizard-submit" data-action="generateApp"></div>
    </div>
</div>
<?php self::endForm() ?>