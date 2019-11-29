<?php

function smarty_function_log($params, &$smarty)
{
    $from = $smarty->_plugins['function']['log'][1] . ':' . $smarty->_plugins['function']['log'][2];
    $message = "log call at: $from - " . $params['msg'];
    if (isset($params['level'])) {
        $level = $params['level'];
    } else {
        LoggerManager::getLogger()->fatal($message);
    }
    LoggerManager::getLogger()->$level($message);
}
