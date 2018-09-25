<?php

use App\Core\App;

class Mail extends PHPMailer{

    public function __construct(){
        $this->isSMTP();                                      // Set mailer to use SMTP
        $this->Host = App::get('config')['host'];  // Specify main and backup SMTP servers
        $this->SMTPAuth = App::get('config')['smtpAuth'];                               // Enable SMTP authentication
        $this->Username = App::get('config')['username'];                 // SMTP username
        $this->Password = App::get('config')['password'];                           // SMTP password
        $this->SMTPSecure = App::get('config')['smtpSecure'];                            // Enable TLS encryption, `ssl` also accepted
        $this->Port = App::get('config')['port'];
    }

}

?>