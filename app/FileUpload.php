<?php
namespace Application;

Class FileUpload
{
    /**
     * Get Headers function
     * @param str $url
     * @param str $file_size
     * @return array
     */
    public function getHeaders($url, $file_size)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_exec($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);

        //if the file type is allowed
        $allowed_file_types = ['image/gif', 'image/png', 'image/jpg'];
        if (! in_array($headers['content_type'], $allowed_file_types)) {
            throw new \Exception('Incorrect file type');
        }

        //if the file file size is not exceeded
        if ($headers['http_code'] === 200) {
            if ($headers['download_content_length'] < $file_size) {
                throw new \Exception('File size is exceeded');
            }
            return $headers;
        } else {
            throw new \Exception('File not found');
        }

        return $headers;
    }

    /**
     * Download
     * @param str $url, $path
     * @return bool || void
     */
    public function download($url, $path)
    {
        if (strpos($url, '.jpg')) {
            $file_type = '.jpg';
        } elseif (strpos($url, '.png')) {
            $file_type = '.png';
        } elseif (strpos($url, '.gif')) {
            $file_type = '.gif';
        }
        $file_path = $path . '/' . time() . $file_type;
        // open file to write
        $fp = fopen ($file_path, 'w+');
        // start curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // set return transfer to false
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // increase timeout to download big file
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        // write data to local file
        curl_setopt($ch, CURLOPT_FILE, $fp);
        // execute curl
        curl_exec($ch);
        // close curl
        curl_close($ch);
        // close local file
        fclose($fp);

        if ($this->checkPath($file_path)) {
            return true;
        }
    }

    /**
     * Checks if directory exists
     * @param $path
     * @return bool
     */
    public function checkPath($path)
    {
        if (filesize($path) > 0) {
            return true;
        } else {
            throw new \Exception("Directory doesn't exist");
        }
    }
}