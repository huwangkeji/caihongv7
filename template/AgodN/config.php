<?php

// AgodN模板配置文件

// 模板信息
$template_info = array(
    'name' => 'AgodN',
    'version' => '1.0',
    'description' => '现代化简洁模板'
);

// 模板设置
$template_settings = array(
    'agodn_stock_display' => array(
        'name' => '库存显示方式',
        'type' => 'select',
        'options' => array(
            '0' => '准确库存显示',
            '1' => '模糊库存范围显示'
        ),
        'note' => '选择库存的显示方式'
    ),
    'agodn_show_sales' => array(
        'name' => '是否显示销量',
        'type' => 'select',
        'options' => array(
            '1' => '显示销量',
            '0' => '不显示销量'
        ),
        'note' => '选择是否在商品列表中显示销量信息'
    )
);

// 模板路由
$template_route = array(
    'index' => 'index.php',
    'shop' => 'shop.php',
    'query' => 'query.php'
);

// 移动端路由
$template_route_m = $template_route;
