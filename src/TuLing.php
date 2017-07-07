<?php

namespace Vbot\TuLing;

use Vbot\Http\Http;
use Hanson\Vbot\Message\Text;
use Hanson\Vbot\Message\Emoticon;
use Hanson\Vbot\Message\Image;
use Hanson\Vbot\Message\Video;
use Hanson\Vbot\Message\Voice;
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
        $username = $message['from']['UserName'];

        if ($message['fromType'] === 'Friend') {
            return $this->autoReplyFriend($username, $message);
        } elseif ($message['fromType'] === 'Group') {
            return $this->autoReplyGroup($username, $message);
        }
    }


    // 回复好友
    private function autoReplyFriend($username, $message)
    {
        try {
            if ($message['type'] === 'text') {
                if ($message['pure'] == '聊天') {
                    Text::send($username, '恭喜你解锁聊天功能, 你要聊什么呢?');
                    self::$users[$username]['tuling_id'] = $this->generateId();
                } elseif($message['pure'] == '不聊了') {
                    unset(self::$users[$username]['tuling_id']);
                    Text::send($username, '拜拜哦~');
                } elseif (isset(self::$users[$username]['tuling_id'])) {
                    Text::send($username, $this->tulingReply($message['pure'], self::$users[$username]['tuling_id']));
                }
            }
            if ($message['type'] === 'emoticon') {
                Emoticon::sendRandom($username);
            }
            if ($message['type'] === 'recall') {
                Text::send($username, $message['content'].' : '.$message['origin']['content']);
                if ($message['origin']['type'] === 'image') {
                    Image::send($username, $message['origin']);
                } elseif ($message['origin']['type'] === 'emoticon') {
                    Emoticon::send($username, $message['origin']);
                } elseif ($message['origin']['type'] === 'video') {
                    Video::send($username, $message['origin']);
                } elseif ($message['origin']['type'] === 'voice') {
                    Voice::send($username, $message['origin']);
                }
            }
        } catch (\Exception $e) {
            vbot('console')->log($e->getMessage(), Console::ERROR);
        }
    }

    // 回复群
    private function autoReplyGroup($username, $message)
    {
        try {
            if (($message['type'] === 'text') && $message['isAt']) {
                Text::send($username, $this->tulingReply($message['pure']));
            }
            if ($message['type'] === 'red_packet') {
                $myself = vbot('myself');
                $groupNickname = $message['from']['NickName'];
                $senderNickname = $message['sender']['NickName'];
                Text::send($myself->username, '[' . $groupNickname . ': '. $senderNickname . ']' . $message['content']);
            }
        } catch (\Exception $e) {
            vbot('console')->log($e->getMessage(), Console::ERROR);
        }
    }

    // 生成ID,用户图灵上下文（微信username为64位）
    private function generateId() {
        $time = explode(' ', microtime());
        return $time [1] . ($time[0] * 1000000);
    }

    // 图灵回复
    private function tulingReply($content, $id = 0)
    {
        try {
            $response = vbot('http')->post($this->api, [
                'key' => $this->key,
                'info' => $content,
                'userid' => $id,
            ], true);

            switch ($response['code']) {

                // 文本类
                case 100000:
                    return $response['text'];
                    break;

                // 链接类
                case 200000:
                    return $response['text'] . ' ' . $response['url'];
                    break;

                default:
                    return '';
                    break;
            }
        } catch (\Exception $e) {
            vbot('console')->log($e->getMessage(), Console::ERROR);
        }
    }

    // 注册拓展时的操作
    public function register()
    {

    }
}