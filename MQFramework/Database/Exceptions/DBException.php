<?php
namespace MQFramework\Database\Exceptions;

use Exception;

class DBException extends Exception
{
    protected $message;
    protected $code;
    protected $file;
    protected $line;

    public function __construct($message, $code, $file = '', $line = '')
    {
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
        $this->code = $code;
    }
    public function connException()
    {

    }

    public function sqlException()
    {

    }

    public function getResponse()
    {
        $msg = "<table border=1 cellspacing=0>";
        $msg.= "<tr><td>Message</td><td>$this->message</td></tr>";
        $msg.= "<tr><td>File</td><td>$this->file</td></tr>";
        $msg.= "<tr><td>Line</td><td>$this->line Line</td></tr>";
        $msg.= "<tr><td>Error Code</td><td>$this->code</td></tr>";
        $msg.= "</table>";
        return $msg;
    }
}
