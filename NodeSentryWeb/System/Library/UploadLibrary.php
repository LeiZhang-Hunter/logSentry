<?php
/**
 * 上传类
 * The Flycorn cms
 * Author: flycorn
 * Email: yuming@flycorn.com
 * Date: 15/9/17
 */
class UploadLibrary extends Container {
    protected $ci;
    var $saveName;// 保存名
    var $savePath;// 保存路径
    var $fileFormat = array('gif','jpg','doc','application/octet-stream');// 文件格式&MIME限定
    var $overwrite = 0;// 覆盖模式
    var $maxSize = 0;// 文件最大字节
    var $ext;// 文件扩展名
    var $errno;// 错误代号
    var $returnArray= array();// 所有文件的返回信息
    var $returninfo= array();// 每个文件返回信息
    function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->config('upload');
        $this->setSavepath($this->ci->config->item('upload_savePath'));
        $this->setFileformat($this->ci->config->item('upload_fileFormat'));
        $this->setMaxsize($this->ci->config->item('upload_maxSize'));
        $this->setOverwrite($this->ci->config->item('upload_overwrite'));
        $this->errno = 0;
    }
    /**
     *
     * 上传
     * @param $fileInput 网页Form(表单)中input的名称
     * @param $changeName 是否更改文件名
     */
    function run($fileInput,$changeName = 1){
        if(isset($_FILES[$fileInput])){
            $fileArr = $_FILES[$fileInput];
            if(is_array($fileArr['name'])){//上传同文件域名称多个文件
                for($i = 0; $i < count($fileArr['name']); $i++){
                    $ar['tmp_name'] = $fileArr['tmp_name'][$i];
                    $ar['name'] = $fileArr['name'][$i];
                    $ar['type'] = $fileArr['type'][$i];
                    $ar['size'] = $fileArr['size'][$i];
                    $ar['error'] = $fileArr['error'][$i];
                    $this->getExt($ar['name']);//取得扩展名，赋给$this->ext，下次循环会更新
                    $this->setSavename($changeName == 1 ? '' : $ar['name']);//设置保存文件名
                    if($this->copyfile($ar)){
                        $this->returnArray[] =  $this->returninfo;
                    }else{
                        $this->returninfo['error'] = $this->errmsg();
                        $this->returnArray[] =  $this->returninfo;
                    }
                }
                return $this->errno ?  false :  true;
            }else{
                //上传单个文件
                $this->getExt($fileArr['name']);//取得扩展名
                $this->setSavename($changeName == 1 ? '' : (!empty($this -> saveName) ? $this -> saveName : $fileArr['name']));//设置保存文件名
                if($this->copyfile($fileArr)){
                    $this->returnArray[0] =  $this->returninfo;
                }else{
                    $this->returninfo['error'] = $this->errmsg();
                    $this->returnArray[0] =  $this->returninfo;
                }
                return $this->errno ?  false :  true;
            }
            return false;
        }else{
            $this->errno = 10;
            return false;
        }
    }

    /**
     *
     *单个文件上传
     *
     * @param $fileArray 文件信息数组
     */
    function copyfile($fileArray){
        $this->returninfo = array();
        // 返回信息
        $this->returninfo['name'] = $fileArray['name'];
        $this->returninfo['md5'] = @md5_file($fileArray['tmp_name']);
        $this->returninfo['saveName'] = $this->saveName;
        $this->returninfo['size'] = number_format( ($fileArray['size'])/1024 , 0, '.', ' ');//以KB为单位
        $this->returninfo['type'] = $fileArray['type'];

        // 检查文件格式
        if (!$this->validateFormat()){
            $this->errno = 11;
            return false;
        }
        // 检查目录是否可写
        if(!@is_writable($this->savePath)){
            $this->errno = 12;
            return false;
        }
        // 如果有大小限制，检查文件是否超过限制
        if ($this->maxSize != 0 ){
            if ($fileArray["size"] > $this->maxSize){
                $this->errno = 14;
                return false;
            }
        }
        // 文件上传
        if(!@move_uploaded_file($fileArray["tmp_name"], $this->savePath.$this->saveName)){
            $this->errno = $fileArray["error"];
            return false;
        }
        return true;
    }

    // 文件格式检查,MIME检测
    function validateFormat(){
        if(!is_array($this->fileFormat)
            || in_array(strtolower($this->ext), $this->fileFormat)
            || in_array(strtolower($this->returninfo['type']), $this->fileFormat) )
            return true;
        else
            return false;
    }
    // 获取文件扩展名
    // @param $fileName 上传文件的原文件名
    function getExt($fileName){
        $ext = explode(".", $fileName);
        $ext = $ext[count($ext) - 1];
        $this->ext = strtolower($ext);
    }

    // 设置上传文件的最大字节限制
    // @param $maxSize 文件大小(bytes) 0:表示无限制
    function setMaxsize($maxSize){
        $this->maxSize = $maxSize;
    }
    // 设置文件格式限定
    // @param $fileFormat 文件格式数组
    function setFileformat($fileFormat){
        if(is_array($fileFormat)){$this->fileFormat = $fileFormat ;}
    }

    // 设置覆盖模式
    // @param overwrite 覆盖模式 1:允许覆盖 0:禁止覆盖
    function setOverwrite($overwrite){
        $this->overwrite = $overwrite;
    }


    // 设置保存路径
    // @param $savePath 文件保存路径：以 "/" 结尾，若没有 "/"，则补上
    function setSavepath($savePath){
        $this->savePath = substr( str_replace("\\","/", $savePath) , -1) == "/"
            ? $savePath : $savePath."/";
    }


    // 设置文件保存名
    // @param $saveName 保存名，如果为空，则系统自动生成一个随机的文件名
    function setSavename($saveName){
        if ($saveName == ''){  // 如果未设置文件名，则生成一个随机文件名
            $name = date('YmdHis')."_".rand(100,999).'.'.$this->ext;
            //判断文件是否存在,不允许重复文件
            if(file_exists($this->savePath . $name)){
                $name = setSavename($saveName);
            }
        } else {
            $name = $saveName;
        }
        $this->saveName = $name;
    }

    // 删除文件
    // @param $fileName 所要删除的文件名
    function del($fileName){
        if(!@unlink($fileName)){
            $this->errno = 15;
            return false;
        }
        return true;
    }

    // 返回上传文件的信息
    function getInfo(){
        return $this->returnArray;
    }

    // 得到错误信息
    function errmsg(){
        $uploadClassError = array(
            0	=>'没有错误，文件上传成功。',
            1	=>'上传的文件在php.ini中超过upload_max_filesize指令。',
            2	=>'上传的文件超过max_file_size那是在HTML表格中指定的。',
            3	=>'上传的文件只有部分上传。 ',
            4	=>'没有文件被上传。 ',
            6	=>'缺少一个临时文件夹。介绍了PHP和PHP 5.0.3 4.4.7。 ',
            7	=>'无法将文件写入到磁盘。介绍了PHP 5.1.0。',
            10	=>'输入的名称不可用！',
            11	=>'上传的文件是不允许的！',
            12	=>'目录不可写！',
            13	=>'文件已经存在！',
            14	=>'文件太大！',
            15	=>'删除文件失败！',
            16	=>'你的PHP版本似乎并不支持GIF缩略图。',
            17	=>'你的PHP版本不会出现JPEG缩略图支持。',
            18	=>'你的PHP版本似乎没有支持图片缩略图。',
            19	=>'在试图复制源图像时发生错误。',
            20	=>'在试图创建一个新图像时发生错误。',
            21	=>'复制的源图像的缩略图图像时出错。',
            22	=>'当保存缩略图图像文件时发生错误。'
        );
        if ($this->errno == 0)
            return false;
        else
            return $uploadClassError[$this->errno];
    }
}