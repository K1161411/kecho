<?php
    date_default_timezone_set('Asia/Shanghai');

    $config = array(

        // 配置七牛或 github 地址（二选一），没用到的请留空（保留''）。
        // 'github_info' => 'solarhell/blog',
        'github_info' => '',

        // 'qiniu_info' => 'https://dn-solarhell.qbox.me',
        'qiniu_info' => 'https://dn-solarhell.qbox.me',

        // 附件目录
        'attachment_dir' => '_attachment',

        // 输入目录
        'input_dir' => '_markdown',

        // 输出目录
        'output_dir' => 'article',

        // 格式化时间（这个暂时没做）
        'date_format' => 'Y-m-d',

        // 主题
        'theme' => 'ryu',

        // 文章排序（根据目录，默认倒叙，留空为正序）
        'sort' => 'desc',
    );