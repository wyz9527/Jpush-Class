<?php

require_once 'autoload.php';

use JPush\Model as M;
use JPush\JPushClient;
use JPush\JPushLog;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;

/**
 * jpush封装
 * @author yangzhiwu <578154898@qq.com>
 * @copyright Copyright (c) 2016-2017 shutung Team 
 * @version $ID$
 */
class jpush {
    
    protected $appKey;
    protected $secret;
    /**
     *
     * @var JPushClient
     */
    protected $client;
    
    /**
     * 
     * @return jpush
     */
    static function getInstance(){
        $var = __CLASS__;
        static $mJPUSH;
        if( isset( $mJPUSH ) ){
            return $mJPUSH;
        }
        return $mJPUSH = new $var;
    }    
    
    /**
     * 
     * 
     * @param type $appKey
     * @param type $secret
     */
    function initPush( $appKey, $secret ){
        $this->appKey = $appKey;
        $this->secret = $secret;
        $this->client = new JPushClient( $this->appKey, $this->secret );
    }
    
    
    /**
     * 点对点推送消息
     * 
     * @param string $platform
     *                  //平台 ios|android
     * @param string $registrationId
     *                  //接收者的注册id
     * @param string $alert
     *                  //推送的内容 锁屏时，会弹出到屏幕上
     * @param string $message
     *                  //推送的内部消息 ，app内部接收，不会弹出在屏幕上
     * @param string $apnsProduction
     *                  //false 推送到开发环境 true 推送到生产环境
     * 
     * @param array $ext
     *                  //附加字段, 视app具体业务定义  格式：array("key1" => "value1", "key2" => "value2")
     *              
     */
    function push2One($platform, $registrationId, $alert, $message, $apnsProduction = false, array $ext = array() ) {
        
        $notification = array();
        if( $alert ){
            $notification = M\notification( $alert, 
                                M\android( $alert, '', 1, $ext )
                        );
            if( $platform == 'ios' ){
                $notification = M\notification( $alert,
                                M\ios( $alert, "happy", "+1", true, $ext )
                        );            
            }            
        }

        try {
            
            $push = $this->client->push()
                    ->setPlatform( M\Platform( $platform ) )
                    ->setAudience( M\Audience( M\registration_id( array( $registrationId ) ) ) );
            if( $notification ){
                $push->setNotification( $notification );
            }
            if( $message ){
                $push->setMessage(M\message( $message, NULL, NULL, $ext ));
            }
            $rs = $push->setOptions(M\options( NULL, NULL, NULL, $apnsProduction ))
                       ->send();
            $result = array(
                'sendno'    => $rs->sendno,
                'msgId'     => $rs->msg_id,
                'response'  => json_decode( $rs->json, TRUE )
            );
            
        } catch ( APIRequestException $e ) {
            $result = array(
                'code' => $e->code,
                'msg'  => $e->message,
                'response'  => json_decode( $e->json , TRUE ),
                'rateLimitLimit' => $e->rateLimitLimit,
                'rateLimitRemaining' => $e->rateLimitRemaining,
                'rateLimitReset'    => $e->rateLimitReset,
            );
        } catch ( APIConnectionException $e ) {
            $result = array(
                'msg'   => $e->getMessage(),
                'IsResponseTimeout' => $e->isResponseTimeout
            );
        }
        
        return $result;
    }
    
    
    /**
     * 推送给多个
     * 
     * @param array $registrationIds
     *                  //接收者的注册ids
     * @param string $alert
     *                  //推送的内容 锁屏时，会弹出到屏幕上
     * @param string $message
     *                  //推送的内部消息 ，app内部接收，不会弹出在屏幕上
     * @param string $apnsProduction
     *                  //false 推送到开发环境 true 推送到生产环境
     * @param array $ext
     *                  //附加字段, 视app具体业务定义  格式：array("key1" => "value1", "key2" => "value2")
     */
    function push2More( array $registrationIds, $alert, $message, $apnsProduction = false, array $ext = array() ){
        try {
            $push = $this->client->push()
                    ->setPlatform(M\Platform('android', 'ios'))
                    ->setAudience(M\Audience(M\registration_id($registrationIds)));
            if( $alert ){
                $push->setNotification(M\notification( $alert, 
                            M\android( $alert, NULL, 1, $ext ), 
                            M\ios( $alert, "happy", "+1", true, $ext )
                    ));
            }
            if( $message ){
                $push->setMessage(M\message( $message, NULL, NULL, $ext ));
            }
            $rs = $push->setOptions(M\options( NULL, NULL, NULL, $apnsProduction ))
                       ->send();
            $result = array(
                'sendno'    => $rs->sendno,
                'msgId'     => $rs->msg_id,
                'response'  => json_decode( $rs->json, TRUE )
            );            
        } catch ( APIRequestException $e ) {
            $result = array(
                'code' => $e->code,
                'msg'  => $e->message,
                'response'  => json_decode( $e->json , TRUE ),
                'rateLimitLimit' => $e->rateLimitLimit,
                'rateLimitRemaining' => $e->rateLimitRemaining,
                'rateLimitReset'    => $e->rateLimitReset,
            );
        } catch ( APIConnectionException $e ) {
            $result = array(
                'msg'   => $e->getMessage(),
                'IsResponseTimeout' => $e->isResponseTimeout
            );
        }
        
        return $result;
    }
    
    /**
     * 群发
     * 
     * @param type $alert
     * @param type $message
     * @param type $apnsProduction
     * @param array $ext
     */
    function push2All( $alert, $message, $apnsProduction = false, array $ext = array() ){
        try {
            $push = $this->client->push()
                    ->setPlatform(M\all)
                    ->setAudience(M\all);
                if( $alert ){
                    $push->setNotification(M\notification( $alert ));
                }
                if( $message ){
                    $push->setMessage(M\message( $message, NULL, NULL, $ext ));
                }
                $rs = $push->setOptions(M\options( NULL, NULL, NULL, $apnsProduction ))
                           ->send();
            $result = array(
                'sendno'    => $rs->sendno,
                'msgId'     => $rs->msg_id,
                'response'  => json_decode( $rs->json, TRUE )
            );            
        } catch ( APIRequestException $e ) {
            $result = array(
                'code' => $e->code,
                'msg'  => $e->message,
                'response'  => json_decode( $e->json , TRUE ),
                'rateLimitLimit' => $e->rateLimitLimit,
                'rateLimitRemaining' => $e->rateLimitRemaining,
                'rateLimitReset'    => $e->rateLimitReset,
            );
        } catch ( APIConnectionException $e ) {
            $result = array(
                'msg'   => $e->getMessage(),
                'IsResponseTimeout' => $e->isResponseTimeout
            );
        }
        
        return $result;
    }
}
