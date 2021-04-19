<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

namespace app\rpc\services;



use Co\WaitGroup;

class User implements \app\rpc\interfaces\User
{

    public function get($name)
    {
        echo 'start...';
        $wg = new WaitGroup();

        $c = 4;
        while ($c) {
            $wg->add();
            go(function () use ($wg, $c) {
                sleep(1);
                echo 'do...'. $c;
                $wg->done();
            });
            $c--;
        }

        var_dump(array_keys(iterator_to_array(app()->getIterator())));

        $wg->wait();
        echo 'end....';
        return ['user' => $name];
    }
}