<?php
/**
 * 用户头像处理
 * The Flycorn cms
 * Author: flycorn
 * Email: yuming@flycorn.com
 * Date: 15/12/7
 */
class AvatarDealLibrary extends Container {

    private $ci;

    public function __construct()
    {
        //获取CI实例对象
        $this -> ci = &get_instance();
    }

    /**
     * 上传用户头像
     * @param $uid
     * @param String $field
     */
    public function Upload($uid = 0, $field = '')
    {
        $result = array();
        $result['status'] = 0;
        $result['msg'] = '上传失败!';
        $result['avatar'] = '';

        $uid = intval($uid);

        if(!empty($field)){
            $config['upload_path'] = './uploads/avatar';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['encrypt_name'] = TRUE;
            $config['max_size'] = '512000';
            $this -> ci -> load -> library('upload', $config);
            if(!$this-> ci -> upload -> do_upload($field))
            {
                //上传失败
                $result['msg'] = $this -> ci -> upload -> display_errors();
                return $result;
            } else {
                //上传成功
                $img_array = $this -> ci -> upload -> data();
                $this -> ci -> load -> library('AvatarResize', array('uid' => $uid));

                if ($this -> ci -> avatarresize -> resize($img_array['full_path'], 150,150 ,'big') && $this -> ci -> avatarresize -> resize($img_array['full_path'], 100,100 ,'normal') && $this -> ci -> avatarresize -> resize($img_array['full_path'], 50,50 ,'small')) {

                    $img = array(
                        'avatar' => $this -> ci -> avatarresize ->get_dir()
                    );

                    //删除tmp下的原图
                    unlink($img_array['full_path']);

                    $result['status'] = 1;
                    $result['msg'] = '上传成功!';
                    $result['avatar'] = $img['avatar'];
                }
            }
        }
        return $result;
    }

    /**
     * 上传用户头像
     * @param $uid
     * @param $path
     */
    public function ImgUpload($uid, $path)
    {
        $result = array();
        $result['status'] = 0;
        $result['msg'] = '上传失败!';
        $result['avatar'] = '';

        /*
        if(!is_file($path)){
            $result['msg'] = '图片不存在';
            return $result;
        }
        */

        $this -> ci -> load -> library('AvatarResize', array('uid' => $uid));

        if ($this -> ci -> avatarresize -> resize($path, 150,150 ,'big') && $this -> ci -> avatarresize -> resize($path, 100,100 ,'normal') && $this -> ci -> avatarresize -> resize($path, 50,50 ,'small')) {

            $img = array(
                'avatar' => $this -> ci -> avatarresize ->get_dir()
            );

            //删除tmp下的原图
            unlink($path);

            $result['status'] = 1;
            $result['msg'] = '上传成功!';
            $result['avatar'] = $img['avatar'];
        }

        return $result;
    }
}