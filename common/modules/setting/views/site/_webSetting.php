<?php use common\helpers\Html; ?>
<?php $baseUrl = $this->getAssetUrl('common\modules\setting\assets\SettingAsset') ?>

<?php $form = self::beginForm(['id' => 'settingForm', 'enctype' => 'multipart/form-data', 'action' => self::createUrl(['saveSetting'])])?>
<table class="setting-table">
    <tr class="showMode">
        <th colspan="2">
            <span class="add-parent-span">添加二级菜单</span>
            <span id="addParentSpan">
                <img src="<?= $baseUrl ?>/add.png" id="addParentLink" href="<?= self::createUrl(['addSetting']) ?>" class="add-link-img">
            </span>
            <input type="hidden" value="<?= $nowTopId ?>" id="topParentId">
        </th>
    </tr>
    <?php
    echo '
    ';
    foreach ($settings as $parent) {
        if ($parent['level'] == 2 && $parent['pid'] == $nowTopId) {
            echo '
    <tr>
        <th colspan="2">
            <span>' . $parent['name'] . '&nbsp;&nbsp;
                <img src="' . $baseUrl . '/remove.png" class="delete-link-img deleteItemLink showMode" data-id="' . $parent['id'] . '" data-parent="1" data-name="' . $parent['name'] . '" href="' . self::createUrl(['deleteSetting']) . '">
            </span>
        </th>
    </tr>
            ';
            foreach ($settings as $child) {
                if ($child['pid'] == $parent['id']) {
                    $name = $child['id'];
                    $previewHtml = '';
                    $value = empty($child['value']) ? '' : $child['value'];
                    switch ($child['type']) {
                        case 'text':
                            $input = Html::textInput($name, $value);
                            break;
                        case 'textarea':
                            $input = Html::textarea($name, $value);
                            break;
                        case 'radio':
                        case 'checkbox':
                            $inputType = $child['type'];
                            if ($inputType == 'checkbox') {
                                $input = '<input type="hidden" name="' . $name . '" value="">';
                                $name .= '[]';
                            } else {
                                $input = '';
                            }
                            $exValue = explode(',', $value);
                            $i = 0;
                            $alter = unserialize($child['alter']);
                            foreach ($alter as $k => $v) {
                                if (in_array($k, $exValue)) {
                                    $checked = 'checked="checked"';
                                } else {
                                    $checked = '';
                                }
                                $input .= '<input name="' . $name . '" ' . $checked . ' type="' . $inputType . '" id="r' . $child['id'] . $i . '" value="' . $k . '"><label for="r' . $child['id'] . $i . '">' . $v . '</label>
                                ';
                                $i++;
                            }
                            break;
                        case 'select':
                            $input = Html::dropDownList($name, $value, unserialize($child['alter']));
                            break;
                        case 'file':
                            $input = '<input data-filename="' . $child['id'] . '" type="file">';
                            if ($value) {
                                $previewHtml = Html::a('预览', $value, array('class' => 'fancybox', 'data-id' => $child['id']));
                            }
                            break;
                        case 'custom':
                            if (strpos($child['alter'], ':') !== false) {
                                $pieces = explode(':', $child['alter']);
                                $callback = [$pieces[0], $pieces[1]];
                            } else {
                                $callback = $this->alter;
                            }
                            if (is_callable($callback)) {
                                $input = call_user_func($callback, $name, $value);
                            } else {
                                $input = Html::errorSpan("该回调方法不存在！");
                            }
                            break;
                    }
                    echo '
    <tr>
        <td class="web-padding">
            <h4>' . $child['name'] . '<span class="showMode">（' . $child['var'] . '）</span>：</h4>
        </td>
    </tr>
    <tr>
        <td>
            ' . $input . '&nbsp;&nbsp;
            <img src="' . $baseUrl . '/remove.png" class="delete-link-img deleteItemLink showMode" data-id="' . $child['id'] . '" data-name="' . $child['name'] . '" href="' . self::createUrl(['deleteSetting']) . '">&nbsp;&nbsp;
            ' . $previewHtml . '
        </td>
        <td>' . (empty($child['comment']) ? '' : $child['comment']) . '</td>
    </tr>
                    ';
                }
            }
            echo '
    <tr class="showMode add-bar">
        <td colspan="2">
            <span class="add-child-span">添加配置项</span>
            <span id="addChildSpan' . $parent['id'] . '">
                <img src="' . $baseUrl . '/add.png" class="addChildLink add-link-img" href="' . self::createUrl(['addSetting']) . '" pid="' . $parent['id'] . '" />
            </span>
        </td>
    </tr>
    ';
        }
    }
    ?>
    <tr class="submit-bar">
        <td><input type="button" value="提交" id="settingSubmit"></td>
    </tr>
</table>
<?php self::endForm() ?>