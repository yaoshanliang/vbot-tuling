<?php

namespace Vbot\TuLing;

use Vbot\Http\Http;
use Hanson\Vbot\Message\Text;
use Hanson\Vbot\Console\Console;
use Illuminate\Support\Collection;
use Hanson\Vbot\Extension\AbstractMessageHandler;

class TuLing extends AbstractMessageHandler
{
    private $api = 'http://www.tuling123.com/openapi/api';

    private $key = 'd5b73c4420bc4728b08e85a8a6cabb5b';

    private static $users = [];

    public function handler(Collection $message)
    {
        if ($message['type'] === 'text') {
            $username = $message['from']['UserName'];
            vbot('console')->log($this->generateId(), Console::INFO);

            if ($message['pure'] == '聊天') {
                Text::send($username, '你要聊什么呢?');
                self::$users[$username]['tuling_id'] = $this->generateId();
            } elseif (isset(self::$users[$username]['tuling_id'])) {
                $response = $this->reply($message['pure'], self::$users[$username]['tuling_id']);

                // 100000: 文本消息
                if (100000 == $response['code']) {
                    Text::send($username, $response['text']);
                }
            }
        }
    }

    private function generateId() {
        $time = explode(' ', microtime());
        return $time [1] . ($time[0] * 1000000);
    }

    // 图灵
    private function reply($content, $id)
    {
        try {
            return vbot('http')->post($this->api, [
                'key'    => $this->key,
                'info'   => $content,
                'userid' => $id,
            ], true);
        } catch (\Exception $e) {
            vbot('console')->log($e->getMessage(), Console::ERROR);

            return ['code' => 0];
        }
    }

    /**
     * 注册拓展时的操作.
     */
    public function register()
    {

    }
}