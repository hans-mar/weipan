<?php use common\helpers\Html; ?>

<?php $form = self::beginForm(['id' => 'settingForm', 'enctype' => 'multipart/form-data', 'action' => self::createUrl(['saveSetting'])])?>
<table class="setting-table table table-border table-bg table-hover">
    <tr class="showMode">
        <th colspan="3">
            <span class="add-parent-span">添加二级菜单</span>
            <span id="addParentSpan" class="Hui-iconfont icon add-icon add-link-img" href="<?= self::createUrl('addSetting') ?>">&#xe600;</span>
            <input type="hidden" value="<?= $nowTopId ?>" id="topParentId">
        </th>
    </tr>
    <?php
    foreach ($settings as $parent) {
        if ($parent['level'] == 2 && $parent['pid'] == $nowTopId) {
            echo '
    <tr>
        <th colspan="3" class="text-c">
            <span>' . $parent['name'] . '&nbsp;&nbsp;
                <span class="deleteItemLink Hui-iconfont icon delete-icon delete-link-img showMode" data-id="' . $parent['id'] . '" data-parent="1" data-name="' . $parent['name'] . '" href="' . self::createUrl(['deleteSetting']) . '">&#xe6a6;</span>
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
                                $input .= '<input name="' . $name . '" ' . $checked . ' type="' . $inputType . '" id="r' . $child['id'] . $i . '" value="' . $k . '"><label for="r' . $child['id'] . $i . '">' . $v . '</label>';
                                $i++;
                            }
                            break;
                        case 'select':
                            $input = Html::dropDownList($name, $value, unserialize($child['alter']));
                            break;
                        case 'file':
                            //$input = '<input data-filename="' . $child['id'] . '" type="file">';
                            if ($value) {
                               // $previewHtml = Html::a('预览', $value, array('class' => 'fancybox', 'data-id' => $child['id']));
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
        <td width="12%">
            <span class="showMode">' . $child['var'] . '</span>
            <h5><span class="config-name">' . $child['name'] . '：</span></h5>
        </td>
        <td>
            ' . $input . '&nbsp;&nbsp;
            <span class="deleteItemLink Hui-iconfont icon delete-icon delete-link-img showMode" data-id="' . $child['id'] . '" data-name="' . $child['name'] . '" href="' . self::createUrl(['deleteSetting']) . '">&#xe6a6;</span>
            ' . $previewHtml . '
        </td>
        <td width="35%">' . (empty($child['comment']) ? '' : $child['comment']) . '</td>
    </tr>
                    ';
                }
            }
            echo '
    <tr class="showMode add-bar">
        <td colspan="3">
            <span class="add-child-span">添加配置项</span>
            <span id="addChildSpan' . $parent['id'] . '" class="addChildLink Hui-iconfont icon add-icon add-link-img" href="' . self::createUrl(['addSetting']) . '" pid="' . $parent['id'] . '">&#xe600;</span>
        </td>
    </tr>
    ';
        }
    }
    ?>
    <tr class="submit-bar">
        <td colspan="3" class="text-c"><input type="button" value="确认并保存" id="settingSubmit"></td>
    </tr>
</table>
<?php self::endForm() ?>