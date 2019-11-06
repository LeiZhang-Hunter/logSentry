<?php
/**
 * 文件上传类(系统封装上传类)
 * The Flycorn cms
 * Author: flycorn
 * Email: yuming@flycorn.com
 * Date: 15/11/3
 */
class PicUploadLibrary extends Container
{
    protected $ci;
    private $error_msg = ''; //错误信息
    private $upload_info = array(); //上传信息
    private $allow_upload_ext = array('jpg', 'jpeg', 'png', 'gif'); //允许的文件类型
    private $upload_max_size = 1024000; //上传文件最大限制
    private $upload_is_watermark = false; //是否添加水印
    private $upload_watermark = 'uploads/water/mark.png'; //上传水印

    public function __construct()
    {
        $this -> ci = get_instance();
        //载入站点配置服务
        $this -> ci -> load -> service('admin/Web_service');
        //载入文件处理方法
        $this -> ci -> load -> helper('flycorn');
        //载入上传类
        $this -> ci -> load -> library('Flycorn_upload');
        //载入图片处理类
        $this -> ci -> load -> library('Flycorn_pic');

        $this -> Init();  //初始化
    }

    //初始化
    private function Init()
    {
        //获取配置
        $file_config = $this -> ci -> web_service -> getUploadConf();

        foreach($file_config as $k => $v){

            switch($k){

                case 'site_upload_ext':
                    $this -> allow_upload_ext = $file_config['site_upload_ext_arr'];
                    break;

                case 'site_upload_max_size':
                    $this -> upload_max_size = $v*1024;
                    break;

                case 'site_upload_is_watermark':
                    if($v > 0) {
                        $this -> upload_is_watermark = true;
                    }
                    break;

                case 'site_upload_watermark':
                    if(!empty($v)){
                        $this -> upload_watermark = $v;
                    }
                    break;

            }

        }
    }

    /**
     * 设置上传文件类型
     * @param string $ext
     */
    public function SetExt($ext = array())
    {
        $this -> allow_upload_ext = $ext;
    }

    /**
     * 获取错误信息
     */
    public function GetError()
    {
        return $this -> error_msg;
    }

    /**
     * 获取图片信息
     */
    public function GetInfo()
    {
        return $this -> upload_info;
    }

    /**
     * 上传文件
     * @param string $path 保存路径
     * @param string $field 上传字段
     * @param int $name_revise 文件名是否修改
     */
    public function Upload($path = '', $field = '', $name_revise = 1)
    {
        if(!empty($field)){

            //检测目录是否存在
            check_dir($path);

            //上传.......
            //设置上传目录
            $this -> ci -> flycorn_upload -> savePath = $path;
            $this -> ci -> flycorn_upload -> fileFormat = $this -> allow_upload_ext;
            $this -> ci -> flycorn_upload -> maxSize = $this -> upload_max_size;
            //do.......
            $upload = $this -> ci -> flycorn_upload -> run($field, $name_revise);
            if(!$upload){
                //获取错误信息
                $this -> error_msg = $this -> ci -> flycorn_upload -> errmsg();
                return false;
            }
            //获取上传的文件
            $upload_file = $this -> ci -> flycorn_upload -> getInfo();
            $upload_file = @array_pop($upload_file);
            $file = rtrim($path, '/').'/'.$upload_file['saveName'];

            //判断是否是图片类型
            $img_ext = '.gif|.jpg|.jpeg|.png|.bmp';//定义检查的图片类型
            $foo = @getimagesize($file);
            $ext = @image_type_to_extension($foo['2']);

            if(stripos($img_ext, strtolower($ext))){

                //判断是否开启图片水印
                if ($this->upload_is_watermark && !empty($this->upload_watermark)) {

                    //图片水印
                    $mark = $this->upload_watermark;
                    //获取水印图规格
                    $sy_info = getimagesize($mark);
                    $img_info = getimagesize($file);
                    if (!empty($img_info) && ($img_info[0] < $sy_info[0] || $img_info[1] < $sy_info[1])) {
                        $width = $img_info[0];
                        $height = $img_info[1];
                        $mark = 'uploads/water/mark_' . $width . '.png';
                        //判断该图片是否存在
                        if (!file_exists('uploads/water/mark_' . $width . '.png')) {
                            //不存在生成新水印
                            $height = ($sy_info[0] / $sy_info[1]) * $img_info[1];
                            $this->ci->flycorn_pic->makeThumb('uploads/water/mark.png', 'uploads/water/', $width, $height, 'mark_' . $width . '.png');
                        }
                    }
                    $this->ci->flycorn_pic->imgWater($file, '', $mark);
                }

            }

            $this -> upload_info = array('path' => $file);
            return true;
        }
        $this -> error_msg = '上传有误!';
        return false;
    }

    /**
     * 下载文件
     * @param string $url  资源地址
     * @param string $path 下载路径
     * @param string $file_name 保存文件名
     */
    public function download($url = '', $path = '', $file_name = '')
    {
        $result = [];
        $result['status'] = 0;
        $result['msg'] = '下载失败!';

        if(!empty($url)){
            $info = pathinfo($url,PATHINFO_BASENAME);
            $ext = strrchr($info,'.'); //后缀

            //验证该文件是否允许下载
            if(!empty($ext) && !in_array(trim($ext, '.'), $this -> allow_upload_ext)){
                $result['msg'] = '该文件格式不允许';
                return $result;
            }

            if(!function_exists('curl_init')){
                $result['msg'] = '请开启curl模块';
                return $result;
            }

            //检测目录是否存在
            check_dir($path);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $temp = curl_exec($ch);
            //写入成功
            $file = '';
            if(!empty($file_name)){
                $file = $ext ? $path.$file_name.$ext : $path.$file_name.'.jpg';
            } else {
                $file = $ext ? $path.$info : $path.uniqid().'.jpg';
            }
            $res = file_put_contents($file, $temp);
            if($res){
                $result['status'] = 1;
                $result['msg'] = $file;
            }
        }

        return $result;
    }

}