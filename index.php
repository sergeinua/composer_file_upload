<?php
use Application\FileUpload;

require_once 'vendor\autoload.php';

$html = '<form action="/" method="post">';
$html .= '<input name="url" placeholder="url"><br>';
$html .= '<input name="path" placeholder="path" value="uploads"><br>';
$html .= '<input name="file_size" placeholder="file size in bytes" value="1024"><br>';
$html .= '<input type="submit" value="upload file">';
$html .= '</form>';

if ($_POST) {
    $url = strip_tags($_POST['url']);
    $path = strip_tags($_POST['path']);
    $file_size = strip_tags($_POST['file_size']);
    $file = new FileUpload();
    $headers = $file->getHeaders($url, $file_size);
    if ($file->download($url, $path)){
        echo 'Download complete!';
    }
} else {
    echo $html;
}
