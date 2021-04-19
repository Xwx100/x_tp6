<?php
// +----------------------------------------------------------------------
// | xu: 2021/4/6 0006
// +----------------------------------------------------------------------

return [
    'lang' => [
        'default' => env('x_tp6_lang.drive', 'baidu'),
        'drives' => [
            'baidu' => [
                'type' => 'Baidu',
                'api' => 'http://api.fanyi.baidu.com/api/trans/vip/translate',
                'api_params' => [
                    'app_id' => '20210406000764674',
                    'app_secret' => 'iEXNb8vSmGuD33ijO4C9',
                    // 翻译源语言
                    'from' => 'en',
                    // 翻译目标语言
                    'to' => 'zh'
                ]
            ]
        ]
    ]
];