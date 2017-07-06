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

            if ($message['pure'] == '聊天') {
                Text::send($username, '恭喜你解锁聊天功能, 你要聊什么呢?');
                self::$users[$username]['tuling_id'] = $this->generateId();
            } elseif($message['pure'] == '不聊了') {
                unset(self::$users[$username]['tuling_id']);
            } elseif (isset(self::$users[$username]['tuling_id'])) {
                $response = $this->reply($message['pure'], self::$users[$username]['tuling_id']);

                switch ($response['code']) {

                    // 文本类
                    case 100000:
                        Text::send($username, $response['text']);
                        break;

                    // 链接类
                    case 200000:
                        Text::send($username, $response['text'] . ' ' . $response['url']);
                        break;

                    default:
                        var_dump($response);
                        break;
                }
            }
        }
    }

    // 生成ID,用户图灵上下文（微信username为64位）
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


    // 注册拓展时的操作
    public function register()
    {

    }
}