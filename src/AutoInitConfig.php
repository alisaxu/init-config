<?php

namespace pack;

class AutoInitConfig
{
    protected $createDir = '';//配置文件所在目录
    protected $configContent = [];
    protected $errorMsg = [];
    protected $token = '';//token
    protected $algo = 'sha256';
    protected $signature = '';

    CONST SUCCESS = 200;
    CONST ERROR = 999;
    CONST DIR_NOT_EXISTS = '目录不存在';

    public function __construct($configContent, $createDir, $token, $signature)
    {
        $this->configContent = $configContent;
        $this->createDir = $createDir;
        $this->token = $token;
        $this->signature = $signature;
    }

    /**
     * 初始化配置文件
     * @return array
     */
    public function initConfig(){
        if(!$this->checkSignature()){//检测
            return $this->toResponse();
        }

        if(!$this->checkDirExists()){//检测目录是否存在
            return $this->toResponse();
        }

        if(empty($this->configContent)){
            return $this->toResponse();
        }

        foreach ($this->configContent as $fileName => $config){
            $file = $this->getFile($fileName);//文件名
            $bakFile = $this->getBakFile($fileName);//拷贝后文件名
            if (!$this->copyConfig($file, $bakFile))//备份配置文件
            {
                continue;
            }
            $content = $this->getContent($config);//获取写入文件的内容
            $this->createConfig($content, $file);//创建文件并写入文件
        }

        return $this->toResponse();
    }

    /**
     * 检测签名是否正确
     */
    protected function checkSignature(){
        if($this->signature == $this->createSignature()){
            return true;
        }else{
            $this->errorMsg[] = '签名验证不合格';
            return false;
        }
    }

    protected function createSignature()
    {
        $dataString = json_encode($this->configContent);
        return hash_hmac($this->algo, $dataString, $this->token);
    }


    protected function createConfig($content, $file){
        if(!file_put_contents($file, implode('', $content))){
            $this->errorMsg[] = $file . '生成配置文件错误';
            return false;
        }
        return true;
    }

    protected function getContent($config){
        $content = [];
        foreach ($config as $keyConfig => $valueConfig){
            $content[] = $keyConfig . ' = ' . $valueConfig .PHP_EOL;
        }
        return $content;
    }

    protected function copyConfig($file, $bakFile){
        if(!copy($file, $bakFile)){
            $this->errorMsg[] = $file . ' 备份操作错误';
            return false;
        }
        return true;
    }

    protected function getFile($fileName){
        return $this->createDir.'/'.$fileName;
    }

    protected function getBakFile($fileName){
        return $this->createDir.'/'.implode('_bak.', explode('.', $fileName));
    }

    protected function checkDirExists(){
        if(!is_dir($this->createDir))
        {
            $this->errorMsg[] = self::DIR_NOT_EXISTS;
            return false;
        }
        return true;
    }

    protected function toResponse(){
        return [
            'code' => empty($this->errorMsg) ? self::SUCCESS : self::ERROR,
            'msg' => $this->errorMsg,
        ];
    }
}
