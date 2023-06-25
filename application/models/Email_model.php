<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_model extends CI_Model {
    function __construct()
    {
        // 'smtp_user' => 'chavezfritzsti@gmail.com',
        // 'smtp_pass' => 'Stichavez2022',
        parent::__construct();
    }

    public function send($to = null, $subject = null, $message = null)
    {
        $this->load->library('email');

        $mail_config['smtp_host'] = 'smtp.gmail.com';
        $mail_config['smtp_port'] = 587;
        $mail_config['smtp_user'] = 'chavezfritzsti@gmail.com';
        $mail_config['_smtp_auth'] = TRUE;
        $mail_config['smtp_pass'] = 'txngjaagwmuuffvb';
        $mail_config['smtp_crypto'] = 'tls';
        $mail_config['protocol'] = 'smtp';
        $mail_config['mailtype'] = 'html';
        $mail_config['send_multipart'] = FALSE;
        $mail_config['charset'] = 'utf-8';
        $mail_config['wordwrap'] = TRUE;

        $this->email->initialize($mail_config);

        $this->email->set_newline("\r\n");

        $this->email->to($to);

        $this->email->from(EMAIL);

        $this->email->subject($subject);

        $this->email->message($message);

        return $this->email->send();
    }
}