<?php

namespace app\controller;

use app\BaseController;
use app\model\Login;
use app\model\User;
use rpc\contract\user\File;
use rpc\contract\user\Pay;

class Index extends BaseController
{

    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V' . \think\facade\App::version() . '<br/><span style="font-size:30px;">14载初心不改 - 你值得信赖的PHP框架</span></p><span style="font-size:25px;">[ V6.0 版本由 <a href="https://www.yisu.com/" target="yisu">亿速云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ee9b1aa918103c4fc"></think>';
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }

    public function test(Pay $pay, File $file)
    {
        echo "<pre>";

        $this->delimiter('测试日志格式');
        try {
            x_app()->common->log('test');
            x_app()->common->log('test12');
            x_app()->common->exception('haha');
        } catch (\Exception $e) {
            x_app()->common->log($e);
        }

        $this->delimiter('测试翻译');
        $this->varDump(x_app()->lang->run('apple'));

        $this->delimiter("测试rpc");
//        $this->varDump($user->get('haha'));
        $this->varDump($pay->run(1000));

        $this->delimiter("测试rpc文件");
        $this->varDump($file->upload(new \think\File(app()->getRuntimePath() . 'swoole.log')));

        $this->delimiter("测试model注入配置和分表查询");
        var_dump((new Login())->where([
            x_app()->xAdmin->login->flagOne => '15818',
            x_app()->xAdmin->login->flagTwo => '2',
            x_app()->xAdmin->login->type => '1'
        ])->find()->toArray());
//        (new Login())->exists(false)->save([
//            x_app()->xAdmin->login->flagOne => '15818',
//            x_app()->xAdmin->login->flagTwo => '2',
//            x_app()->xAdmin->login->type => '1'
//        ]);
        $xApp = x_app();

        $this->delimiter("测试model封装的列表查询");
        var_dump(x_app()
            ->change()
            ->commonRule(
                x_app()->xAdmin->user->getSchemaKeysExclude([x_app()->xAdmin->user->id]),
                function ($key) {
                    return x_app()->xAdmin->user->table . ".{$key}";
                }
            )
            ->fieldJoinRule("{1}", "user1.{$xApp->xAdmin->user->userId}")
            ->fieldRule(x_app()->xAdmin->user->userName, function ($key) {
                return x_app()->xAdmin->user->groupConcat(x_app()->xAdmin->user->getTableField(x_app()->xAdmin->user->userName), $key);
            })
            ->joinRule(
                x_app()->changeJoin()
                    ->leftJoin(x_app()->xAdmin->user)
                    ->as('user1')
                    ->on("{$xApp->xAdmin->user->userId}={1}")
                    ->trigger(function (array $fields) {
                        return array_intersect(x_app()->xAdmin->user->getSchemaKeysExclude(['id']), $fields);
                    })
            )
            ->whereRule(x_app()->xAdmin->user->getSchemaKeysExclude([
                x_app()->xAdmin->user->userId,
                x_app()->xAdmin->user->userName,
                x_app()->xAdmin->user->createAt
            ]))
            ->whereRule(x_app()->xAdmin->user->userId, 'in')
            ->whereRule(x_app()->xAdmin->user->userName, 'left_like')
            ->whereRule(x_app()->xAdmin->user->createAt, 'datetime')
            ->base(x_app()->xAdmin->user)
            ->front([
                x_app()->change->name->field => x_app()->xAdmin->user->getFields(),
                x_app()->change->name->where => [
                    x_app()->xAdmin->user->userId => [
                        1, 2, 3, 4
                    ]
                ],
                x_app()->change->name->group => [
                    x_app()->xAdmin->user->userId,
                ],
                x_app()->change->name->order => [
                    [
                        x_app()->change->name->sortField => x_app()->xAdmin->user->userId
                    ],
                ],
                x_app()->change->name->join => [
                    x_app()->xAdmin->user
                ],
                x_app()->change->name->page => [
                    x_app()->change->name->page => 10,
                    x_app()->change->name->pageSize => 100,
                ]
            ])
            ->checkLimit()
            ->filterWhere()
            ->run()
            ->query()
            ->lists()
        );

        echo "</pre>";
    }

    // 分隔符
    private function delimiter($msg)
    {
        echo "\n================={$msg}======================\n";
    }

    private function varDump($content)
    {
        echo x_app()->common->sprintf("------\n%s\n--------\n", $content);
    }
}
