<?php
class PhpMongoSession implements SessionHandlerInterface
{
    private $savePath;

    public function open($savePath, $sessionName)
    {
        var_dump($savePath);
        var_dump($sessionName);

        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    public function close()
    {
        var_dump("close");
        return true;
    }

    public function read($id)
    {
        var_dump("read: " . $id);
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    public function write($id, $data)
    {
        var_dump("write: " . $id);
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    public function destroy($id)
    {
        var_dump( "destroy: " . $id);
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc($maxlifetime)
    {
        var_dump("running gc...");
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}

$handler = new PhpMongoSession();
session_set_save_handler($handler, true);
session_start();
$_SESSION["test"]="hello";
