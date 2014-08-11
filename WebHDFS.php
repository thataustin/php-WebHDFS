<?php

/**
 * @class WebHdfs
 * @thanks to https://github.com/simpleenergy/php-WebHDFS/ for the starter code
 */
class WebHdfsService
{
    protected $curl;
    protected $host;
    protected $port;
    protected $user;

    public function __construct($curl, $host, $port, $user)
    {
        $this->curl = $curl;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
    }

    // File and Directory Operations

    public function create($path, $filename)
    {
        if (!file_exists($filename)) {
            return false;
        }

        $url = $this->buildUrl($path, array('op'=>'CREATE'));
        $redirectUrl = $this->curl->putLocation($url);
        return $this->curl->putFile($redirectUrl, $filename);
    }

    public function append($path, $string, $bufferSize='')
    {
        $url = $this->buildUrl($path, array('op'=>'APPEND', 'buffersize'=>$bufferSize));
        $redirectUrl = $this->curl->postLocation($url);
        return $this->curl->postString($redirectUrl, $string);
    }

    public function concat($path, $sources)
    {
        $url = $this->buildUrl($path, array('op'=>'CONCAT', 'sources'=>$sources));
        return $this->curl->post($url); 
    }

    public function open($path, $offset='', $length='', $bufferSize='')
    {
        $url = $this->buildUrl($path, array('op'=>'OPEN', 'offset'=>$offset, 'length'=>$length, 'buffersize'=>$bufferSize));
        return $this->curl->getWithRedirect($url);
    }

    public function mkdirs($path, $permission='')
    {
        $url = $this->buildUrl($path, array('op'=>'MKDIRS', 'permission'=>$permission));
        return $this->curl->put($url);
    }

    public function createSymLink($path, $destination, $createParent='')
    {
        $url = $this->buildUrl($destination, array('op'=>'CREATESYMLINK', 'destination'=>$path, 'createParent'=>$createParent));
        return $this->curl->put($url);
    }

    public function rename($path, $destination)
    {
        $url = $this->buildUrl($path, array('op'=>'RENAME', 'destination'=>$destination));
        return $this->curl->put($url);
    }

    public function delete($path, $recursive='')
    {
        $url = $this->buildUrl($path, array('op'=>'DELETE', 'recursive'=>$recursive));
        return $this->curl->delete($url);
    }

    public function getFileStatus($path)
    {
        $url = $this->buildUrl($path, array('op'=>'GETFILESTATUS'));
        return $this->curl->get($url);
    }

    public function listStatus($path)
    {
        $url = $this->buildUrl($path, array('op'=>'LISTSTATUS'));
        return $this->curl->get($url);
    }

    // Other File System Operations

    public function getContentSummary($path)
    {
        $url = $this->buildUrl($path, array('op'=>'GETCONTENTSUMMARY'));
        return $this->curl->get($url);
    }

    public function getFileChecksum($path)
    {
        $url = $this->buildUrl($path, array('op'=>'GETFILECHECKSUM'));
        return $this->curl->getWithRedirect($url);
    }

    public function getHomeDirectory()
    {
        $url = $this->buildUrl('', array('op'=>'GETHOMEDIRECTORY'));
        return $this->curl->get($url);
    }

    public function setPermission($path, $permission)
    {
        $url = $this->buildUrl($path, array('op'=>'SETPERMISSION', 'permission'=>$permission));
        return $this->curl->put($url);
    }

    public function setOwner($path, $owner='', $group='')
    {
        $url = $this->buildUrl($path, array('op'=>'SETOWNER', 'owner'=>$owner, 'group'=>$group));
        return $this->curl->put($url);
    }

    public function setReplication($path, $replication)
    {
        $url = $this->buildUrl($path, array('op'=>'SETREPLICATION', 'replication'=>$replication));
        return $this->curl->put($url);
    }

    public function setTimes($path, $modificationTime='', $accessTime='')
    {
        $url = $this->buildUrl($path, array('op'=>'SETTIMES', 'modificationtime'=>$modificationTime, 'accesstime'=>$accessTime));
        return $this->curl->put($url);
    }

    protected function buildUrl($path, $queryData = array())
    {
        if ( !empty($path) && $path[0] === '/') {
            $path = substr($path, 1);
        }

        $host = 'http://' . $this->host . ':' . $this->port;
        $path = '/webhdfs/v1/' . $path;

        $queryData['user.name'] = $this->user;
        $queryData = http_build_query(array_filter($queryData));

        return  $host . $path . '?' . $queryData;
    }
}
