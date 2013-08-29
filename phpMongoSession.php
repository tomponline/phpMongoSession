<?php
class PhpMongoSession implements SessionHandlerInterface
{
    private $_c;

    public function open( $savePath, $sessionName )
    {
        if( $m = new MongoClient() )
        {
            $db = $m->phpMongoSession;
            $this->_c = $db->sessions;
            return TRUE;
        }
    }

    public function close()
    {
        return true;
    }

    public function read( $id )
    {
        if( $doc = $this->_c->findOne( array( '_id' => $id ) ) )
        {
            return $doc[ 'd' ];
        }
    }

    public function write( $id, $data )
    {
        if( $this->_c->update(
            array( '_id' => $id ),
            array( '_id' => $id, 'd' => $data, 't' => time() ),
            array( 'upsert' => TRUE )
        ) )
        {
            return TRUE;
        }
    }

    public function destroy( $id )
    {
        if( $this->_c->remove(
            array( '_id' => $id ),
            array( 'justOne' => TRUE )
        ) )
        {
            return TRUE;
        }
    }

    public function gc( $maxLifeTime )
    {
        if( $this->_c->remove(
            array( 't' => array( '$lt' => ( time() - $maxLifeTime ) ) )
        ) )
        {
            return TRUE;
        }
    }
}

$handler = new PhpMongoSession();
session_set_save_handler($handler, true);
session_start();
$_SESSION["test"]="hello";

if( !empty( $_SESSION[ 'count' ] ) )
{
        $_SESSION['count']++;
}
else
{
         $_SESSION['count'] = 1;
}

echo $_SESSION['count'];
