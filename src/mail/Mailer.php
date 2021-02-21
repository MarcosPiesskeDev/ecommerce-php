<?php

namespace Hcode\mail;

require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../global.php';

use PHPMailer;
use Rain\Tpl;

class Mailer{

    private $mail;

    public function __construct($toAddress, $toName, $subject, $tplName, $data = [])
    {

        $config = [
            "tpl_dir"     => $_SERVER['DOCUMENT_ROOT'].'/views/email/',
            "cache_dir"   => $_SERVER['DOCUMENT_ROOT']."/views-cache/",
            "auto_escape" => false,
            "debug"       => false
        ];

        Tpl::configure($config);

        $tpl = new Tpl;

        foreach($data as $key => $value){
            $tpl->assign($key, $value);
        }

        $html = $tpl->draw($tplName, true);

       $this->mail = new PHPMailer();
       
       $this->mail->isSMTP();

       $this->mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

       $this->mail->SMTPDebug = 0;
       $this->mail->Debugoutput = 'html';
       $this->mail->SMTPAuth = true;
       $this->mail->Host = 'smtp.gmail.com';

       $this->mail->Port = 587;

       $this->mail->SMTPSecure = 'tls';

       $this->mail->Username = getenv('EMAIL');
      
       $this->mail->Password = getenv('MAIL_PASSWORD');

       $this->mail->setFrom(getenv('EMAIL'), 'Curso PHP 7');

       $this->mail->addAddress($toAddress, $toName);

       $this->mail->Subject = $subject;

       $this->mail->msgHTML(utf8_decode($html));

       $this->mail->AltBody = 'This is a plain-text message body';

       if(!$this->mail->send()){
           echo 'Mailer error:'. $this->mail->ErrorInfo;
           exit();
       }

       echo 'Message sent!';
    }

    public function getMailer()
    {
        return $this->mail;
    }
}