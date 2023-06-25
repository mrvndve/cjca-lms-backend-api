<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_handler_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }

    public function upload_file($base64 = '', $type ='', $file_name = '')
    {
        $file_not_uploaded = false;

        $data = str_replace('data:' . $type . ';base64,', '', $base64);

        file_put_contents("uploads/$file_name", base64_decode($data));

        if (!file_exists("uploads/$file_name"))
        {
            $file_not_uploaded = true;
        }

        if ($file_not_uploaded)
        {   
            return array('status' => 0, 'message' => 'File/s upload failed.');
        }
        else
        {
            return array('status' => 1, 'message' => 'File uploaded.');
        }
    }

    public function delete_file($file_name)
    {
        if (file_exists("uploads/$file_name"))
        {
            unlink("uploads/$file_name");
        }

        return array('status' => 1, 'message' => 'File deleted.');
    }
}