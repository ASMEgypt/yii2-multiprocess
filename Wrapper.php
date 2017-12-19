<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 10/5/16
 * Time: 4:25 PM
 */

namespace execut\multiprocess;


use yii\base\Component;

class Wrapper extends Component
{
    public $callback = null;
    public $threadsCount = 1;
    public $data = [];
    public function run() {
        $childrensCount = 0;
        foreach ($this->data as $key => $value) {
            if ($this->threadsCount == 1) {
                $this->executeCallback($value, $key);
            } else {
                $pid = pcntl_fork();
                if ($pid == -1) {
                    die("could not fork");
                } else if ($pid) {
                    $childrensCount++;
                    if ($childrensCount >= $this->threadsCount){
                        pcntl_wait($status);
                        $childrensCount--;
                    }
                } else {
                    $this->executeCallback($value, $key, $pid);
                    exit;
                }
            }
        }

        while ($childrensCount > 0){
            pcntl_wait($status);
            $childrensCount--;
        }
    }

    protected function executeCallback($value, $key, $pid = false) {
        $callback = $this->callback;

        return $callback($value, $key, $pid);
    }
}