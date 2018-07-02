<?php
namespace app\api\controller ;

/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/5/22
 * Time: 16:50
 */

use PHPMailer\PHPMailer\PHPMailer;
class Code extends Common
{
    /*
     * 获取验证码
     * */
    public function get_code(){

        $username = $this->params['username'];
        $exist = $this->params['is_exist'];
        $username_type = $this->check_username($username);
           switch ($username_type){
               case 'phone':
                   return $this->get_code_by_username($username,'phone',$exist);
               break;

               case 'email':
                   return $this->get_code_by_username($username,'email',$exist);
                break;
           }
    }
    /*
     * 获取验证码
     * */
    public function get_code_by_username($username,$username_type,$exist){
        if($username_type == 'phone'){
            $type_name = '手机';
        }else{
            $type_name = '邮箱';
        }
        //1检测手机|邮箱是否存在于数据库中
        $this->check_exist($username,$username_type,$exist);
        //2检测验证码提交频次30秒一次
        if('?'.session($username.'_code_lasttime')){
            if(time()-session($username.'_code_lasttime')<30){
               return $this->return_msg(400,$type_name.'验证码提交时间少于30秒，请稍后再试');
            }
        }
        //3生成验证码
        $code = $this->make_code(6);
        //4使用session存储验证码，方便对比 md5加密
        $md5_code = md5($username.'_'.md5($code));
        session($username.'_code',$md5_code);
        //5存入session 验证码提交时间
        session($username.'_code_lasttime',time());
        //6发送验证码
        if($username_type == 'phone'){
            $this->send_code_by_phone($username,$code);
        }else{
            $this->send_code_by_email($username,$code);
        }

    }

    /*
     * 生成验证码
     * */
    public function make_code($num){
        $max = pow(10,$num)-1;
        $min = pow(10,$num-1);
        $code = rand($min,$max);
        return $code;
    }

    /*
     * 发送验证码by手机
     * */
    public function send_code_by_phone($username,$code){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.mysubmail.com/message/xsend');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        $data = [
            'appid' => '23296',
            'signature' => 'e6d8550d76f1c00b7a906ecb00b69b49',
            'to' => $username,
            'project' => 'jORky',
            'vars' => '{"code":"'.$code.'","time":"60"}'
        ];
        curl_setopt($curl,CURLOPT_POSTFIELDS, $data);
        $res = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($res);
        if($res->status !== 'success'){
            return $this->return_msg(400,$res->msg);
        }
        return $this->return_msg(200,'发送验证码成功');


    }

    /*
     * 发送验证码by电子邮箱
     *
     */
    public function send_code_by_email($username,$code){
        //实例化
        $mail=new PHPMailer();
        try{
            //邮件调试模式
            $mail->SMTPDebug = 2;
            //设置邮件使用SMTP
            $mail->isSMTP();
            // 设置邮件程序以使用SMTP
            $mail->Host = 'smtp.163.com';
            // 设置邮件内容的编码
            $mail->CharSet='UTF-8';
            // 启用SMTP验证
            $mail->SMTPAuth = true;
            // SMTP username
            $mail->Username = '15603607237@163.com';
            // SMTP password
            $mail->Password = 'chen5212';
            // 启用TLS加密，`ssl`也被接受
//            $mail->SMTPSecure = 'tls';
            // 连接的TCP端口
//            $mail->Port = 587;
            //设置发件人
            $mail->setFrom('15603607237@163.com', '陈新伟');
            //  添加收件人1
            $mail->addAddress($username, $username);     // Add a recipient
//            $mail->addAddress('ellen@example.com');               // Name is optional
//            收件人回复的邮箱
            $mail->addReplyTo('15603607237@163.com', '陈新伟');
//            抄送
//            $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');
            //附件
//            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            //Content
            // 将电子邮件格式设置为HTML
            $mail->isHTML(true);
            $mail->Subject = '测试验证码';
            $mail->Body    = "您的验证码是".$code."，请注意激活";
//            $mail->AltBody = '这是非HTML邮件客户端的纯文本';
            if($mail->send()){
                return $this->return_msg(200,'发送邮件成功请注意查收');
            }
            $mail->isSMTP();
        }catch (Exception $e){
            return $this->return_msg(400,'发送邮件失败',$mail->ErrorInfo);
        }
    }



}