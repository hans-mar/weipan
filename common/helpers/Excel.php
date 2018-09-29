<?php

namespace common\helpers;

class Excel
{
    /**
     * 设置Excel列标题
     * 
     * @param object $sheet  PHPExcel的sheet对象
     * @param array  $titles 列标题数组
     */
    public static function setTitles($sheet, $titles)
    {
        foreach ($titles as $col => $title) {
            $cell = self::getCell($col, 1);
            $sheet->setCellValue($cell, $title);
            // 字体颜色
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FF5511');
            // 字体加粗
            $sheet->getStyle($cell)->getFont()->setBold(true);
            // 垂直居中
            $sheet->getStyle($cell)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            // 水平居中
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
    }

    /**
     * 设置Excel列内容
     * 
     * @param object $sheet    PHPExcel的sheet对象
     * @param array  $contents 列内容数组
     * @param int    $row      行数
     */
    public static function setContents($sheet, $contents, $row)
    {
        foreach ($contents as $col => $content) {
            $sheet->setCellValue(self::getCell($col, $row), strip_tags($content));
        }
    }

    /**
     * 获得Excel单元格坐标
     * 
     * @param  int $x 从0开始计数的横向坐标
     * @param  int $y 从1开始计数的行数
     * @return string
     */
    protected static function getCell($x, $y)
    {
        do {
            $mod = $x % 26;
            $x = (int) ($x / 26);
            $ret = chr($mod + 65) . $ret;
        } while ($x-- > 0);
        return $ret . $y;
    }
}
