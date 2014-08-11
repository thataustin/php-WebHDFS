<?php

class CurlService {

    public function getWithRedirect($url)
    {
        return $this->get($url, array(CURLOPT_FOLLOWLOCATION => true));
    }

    public function get($url, $options=array())
    {
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_RETURNTRANSFER] = true;
        return $this->exec($options);
    }

    public function putLocation($url)
    {
        return $this->_findRedirectUrl($url, array(CURLOPT_PUT=>true));
    }

    public function postLocation($url)
    {
        return $this->_findRedirectUrl($url, array(CURLOPT_POST=>true));
    }

    private function _findRedirectUrl($url, $options)
    {
        $options[CURLOPT_URL] = $url;
        $info = $this->exec($options, true);
        return $info['redirect_url'];
    }

    public function putFile($url, $filename)
    {
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_PUT] = true;
        $handle = fopen($filename, "r");
        $options[CURLOPT_INFILE] = $handle;
        $options[CURLOPT_INFILESIZE] = filesize($filename);

        $info = $this->exec($options, true);

        return ('201' == $info['http_code']);
    }

    public function postString($url, $string)
    {
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $string;

        $info = $this->exec($options, true);

        return ('200' == $info['http_code']);
    }

    public function put($url) {
        $options = array();
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_PUT] = true;
        $options[CURLOPT_RETURNTRANSFER] = true;

        return $this->exec($options);
    }

    public function post($url)
    {
        $options = array();
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_RETURNTRANSFER] = true;

        return $this->exec($options);
    }

    public function delete($url)
    {
        $options = array();
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_CUSTOMREQUEST] = "DELETE";
        $options[CURLOPT_RETURNTRANSFER] = true;

        return $this->exec($options);
    }

    protected function exec($options, $returnInfo=false)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        if ($returnInfo) {
            $result = curl_getinfo($ch);
        }

        curl_close($ch);
        return $result;
    }

}
