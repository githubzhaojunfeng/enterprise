<?php
/*
$type bool true/false
$msg string ajax message
 */
function sendmsg($type, $msg)
{
    $ret['ok'] = $type;
    $ret['msg'] = $msg;
    echo json_encode($ret);
    exit();
}

?>