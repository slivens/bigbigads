<?php

class LocalValetDriver extends LaravelValetDriver
{
    /**
     * 判断驱动服务请求
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri)
    {
        return true;
    }

    /**
    * 判断请求内容是否是静态文件。
    *
    * @param  string  $sitePath
    * @param  string  $siteName
    * @param  string  $uri
    * @return string|false
    */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        // 访问 /app 目录强制认为文件或文件夹不存在
        if ($uri === '/app' || $uri === '/app/') {
            return false;
        }

        // 如果文件存在，返回磁盘上的绝对路径
        if (file_exists($staticFilePath = $sitePath.'/public/'.$uri)) {
            return $staticFilePath;
        }

        return false;
    }

    /**
     * 获取应用前端控制器绝对路径
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {
        if (strpos($uri, '/app') === 0) {
            return $sitePath.'/public/app/index.html';
        }
        
        return $sitePath.'/public/index.php';
    }
}