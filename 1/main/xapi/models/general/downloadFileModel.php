<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

use sinacloud\sae\Storage as Storage;

class downloadFileModel extends AjaxModel {

  protected $need_login = true;

	public function GetResponse_ () {

    if (!array_key_exists('path', $_GET)) {
      header('HTTP/1.1 404 Not Found');
      exit;
    }

    $file_path = $_GET['path'];
    $prefixLen = strlen(PRIVATE_STORAGE_URL);
    $uri = substr(urldecode($file_path), $prefixLen);

    $instance = new Storage();

    $instance->setExceptions(true);

    $file_info = $instance->getObjectInfo(PRIVATE_STORAGE, $uri);
    if (!$file_info) {
      header('HTTP/1.1 404 Not Found');
      exit;
    }

    header(sprintf('Content-Type: %s', $file_info['type']));
    header(sprintf('Content-Length: %d', $file_info['size']));

    header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $file_info['time']) . ' GMT'); //last modified time

    $header_info = $instance->getBucketInfo(PRIVATE_STORAGE);
    if (!$file_info) {
      header('HTTP/1.1 404 Not Found');
      exit;
    }

    $expire = $header_info['x-sws-container-meta-expires']; //expire time
    $date = $header_info['date']; //last visit time

    $max_age = 120;

    header("Cache-Control: max-age=" . $max_age);

    header("Expires: " . gmdate("D, d M Y H:i:s", $date + $max_age) . " GMT");

    $tmp = tempnam(SAE_TMP_PATH, 'tmpfile');

    $file = $instance->getObject(PRIVATE_STORAGE, $uri, $tmp);
    if (!$file) {
      header('HTTP/1.1 404 Not Found');
      exit;
    }

    $content = file_get_contents($tmp);

    echo($content);
    exit;
	}
}
