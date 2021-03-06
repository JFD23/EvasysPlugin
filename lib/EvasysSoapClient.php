<?php

class EvasysSoapClient extends SoapClient {

    public function __soapCall ($function_name,  $arguments,  $options = null, $input_headers = null,  &$output_headers = null) {
        $starttime = microtime(true);
        $result = parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
        $soapcalltime = microtime(true) - $starttime;

        $soaplog = new EvasysSoapLog();
        $soaplog['function'] = $function_name;
        $soaplog['arguments'] = (array) $arguments;
        $soaplog['result'] = (array) $result;
        $soaplog['time'] = $soapcalltime;
        $soaplog['user_id'] = $GLOBALS['user']->id;
        $soaplog->store();

        return $result;
    }
}
