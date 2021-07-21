<?php
namespace App\Traits;

trait reply
{

    public function success($msg, $info)
    {
        $data['status_code'] = 1;
        $data['status_text'] = 'success';
        $data['message'] = $msg;

        if ($info != null) {
            $data['data'] = $info;
        }
        return $data;
    }

    public function failed($msg)
    {
        $data['status_code'] = 0;
        $data['status_text'] = 'Failed';
        $data['message'] = $msg;

        return $data;
    }

}
