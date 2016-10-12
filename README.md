# Jpush-Class

使用方法
----------------
```php
$mJpush = new jpush();
/**
* 初始化参数
* $appKey jpush分配的key
* $secret jpush分配的密钥字符串
**/
$mJpush->initPush( $appKey, $secret );

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
$mJpush->push2One($platform, $registrationId, $alert, $message, $apnsProduction, $ext );

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
$mJpush->push2More( array $registrationIds, $alert, $message, $apnsProduction, array $ext );

/**
 * 群发
 * 
 * @param string $alert
 *                //推送的内容 锁屏时，会弹出到屏幕上
 * @param string $message
 *                //推送的内部消息 ，app内部接收，不会弹出在屏幕上
 * @param bool $apnsProduction
 *                //alse 推送到开发环境 true 推送到生产环境
 * @param array $ext
 *                //附加字段, 视app具体业务定义  格式：array("key1" => "value1", "key2" => "value2")
 */
$mJpush->push2All( $alert, $message, $apnsProduction, $ext );
```
#####`OVER`

