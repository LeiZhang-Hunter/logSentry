<?php
/**
 * 图片处理类
 * The Flycorn cms
 * Author: flycorn
 * Email: yuming@flycorn.com
 * Date: 15/9/17
 */
class ImageLibrary extends Container
{
    protected $ci;
    function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('image_lib');
    }

    /**
     * CreateThumb 创建缩略图
     * @param $target 目标图片
     * @param $thumb_medium 中等缩略图路径
     * @param $thumb_small 小缩略图路径
     */
    function CreateThumb($target, $thumb_medium = '', $thumb_small = '', $m_w = 600, $m_h = 350, $s_w = 200, $s_h = 160)
    {
        $res = array();
        $res['thumb_medium'] = '';
        $res['thumb_small'] = '';

        if(!empty($thumb_medium)){
            $this -> checkdir($thumb_medium);

            $result1 = $this -> MakeThumb($target,$thumb_medium, $m_w,$m_h);
            if($result1['status']){
                $res['thumb_medium'] = $result1['msg'];
            }
        }

        if(!empty($thumb_small)){
            $this -> checkdir($thumb_small);

            $result2 = $this -> MakeThumb($target,$thumb_small,$s_w,$s_h);
            if($result2['status']){
                $res['thumb_small'] = $result2['msg'];
            }
        }
        return $res;
    }

    private function Checkdir($dir_name)
    {
        if(!empty($dir_name) && $dir_name != '.'){
            $dir_arr = array();
            $dir_arr = explode('/',$dir_name);
            //遍历
            $tmp_dir = '';
            foreach($dir_arr as $k => $v){
                $tmp_dir .= $v.'/';

                //判断文件夹是否存在
                if(!file_exists($tmp_dir)){
                    @mkdir($tmp_dir); //创建文件夹
                }
            }
        }
    }

    /**
     *
     * 生成缩略图
     * @param string $img 图片路径，不能使用URL
     * @param int $width 缩略图宽
     * @param int $height 缩略图高
     * @param string img_name 新图片名
     */
    function MakeThumb($img='', $new_path='./static/upload/images/thumb/', $width=575, $height=350, $img_name = '')
    {
        $result = array();
        $result['status'] = false;
        $result['msg'] = '';

        $info = pathinfo($img);
        //$new_name = 'thumb_'.$info['basename'];
        if(empty($img_name)) {
            $new_name = $info['basename'];
        } else {
            $new_name = $img_name;
        }
        //$new_name = $info['basename'];
        $config = array();
        $config['image_library'] = 'gd2'; // 设置图像库
        $config['source_image'] = $img; //设置原始图像名字
        $config['dynamic_output'] = FALSE;//决定新图像的生成是要写入硬盘还是动态的存在
        $config['quality'] = '90%';//设置图像的品质。品质越高，图像文件越大
        $config['new_image'] = $new_path.$new_name; //设置图片的目标名/路径
        $config['width'] = $width;//(必须)设置你想要得图像宽度。
        $config['height'] = $height;//(必须)设置你想要得图像高度
        //$config['create_thumb'] = TRUE;//让图像处理函数产生一个预览图像(将_thumb插入文件扩展名之前)
        //$config['thumb_marker'] = '_thumb'; //指定预览图像的标示。它将在被插入文件扩展名之前。  例如，mypic.jpg 将会变成 mypic_thumb.jpg
        $config['maintain_ratio'] = TRUE; //维持比例
        $config['master_dim'] = 'auto'; //auto, width, height 指定主轴线

        $this->ci->image_lib->initialize($config);

        $bool = $this->ci->image_lib->resize();

        if(!$bool){
            $result['msg'] = $this->ci->image_lib->display_errors();
            return $result;
        }
        $result['status'] = true;
        $result['msg'] = $new_name;

        return $result;
    }

    /**
     *
     * 文字水印
     * @param $img
     */
    function TextWater($img='')
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $img;
        $config['dynamic_output'] = FALSE;
        $config['quality'] = '90%';
        $config['wm_type'] = 'overlay';
        $config['wm_padding'] = '5';
        $config['wm_vrt_alignment'] = 'middle';
        $config['wm_hor_alignment'] = 'center';
        $config['wm_vrt_offset'] = '0';
        $config['wm_hor_offset'] = '0';
        $config['wm_text'] = 'showgrid';
        $config['wm_font_path'] = 'ptj_system/fonts/type-ra.ttf';
        $config['wm_font_size'] = '16';
        $config['wm_font_color'] = 'FF0000';
        $config['wm_shadow_color'] = 'FF0000';
        $config['wm_shadow_distance'] = '3';
        $config['new_image'] = './static/upload/images/thumbnail';
        $this->ci->image_lib->initialize($config);
        $this->ci->image_lib->watermark();
    }

    /**
     * 图片水印
     * @param string $img
     * @param string $new_image
     * @param string $mark
     */
    function ImgWater($img = '', $new_image = '', $mark = 'uploads/water/mark.png')
    {
        //验证图片与水印图是否存在
        if(!file_exists($img) || !file_exists($mark)){
            return false;
        }
        $this -> ci -> image_lib -> clear(); //清除
        //是否需要删除原图
        $del_original = 0;
        $config['image_library'] = 'gd2'; //(必须)设置图像库
        $config['source_image'] = $img; //(必须)设置原始图像的名字/路径
        $config['maintain_ratio'] = TRUE; //维持比例
        $config['dynamic_output'] = FALSE; //决定新图像的生成是要写入硬盘还是动态的存在
        $config['quality'] = '90%'; //设置图像的品质。品质越高，图像文件越大
        $config['wm_type'] = 'overlay'; //(必须)设置想要使用的水印处理类型(text, overlay)
        $config['wm_padding'] = '5'; //图像相对位置(单位像素)
        $config['wm_vrt_alignment'] = 'bottom'; //竖轴位置 top, middle, bottom
        $config['wm_hor_alignment'] = 'right'; //横轴位置 left, center, right
        $config['wm_vrt_offset'] = '20'; //指定一个垂直偏移量(以像素为单位)
        $config['wm_hor_offset'] = '20'; //指定一个横向偏移量(以像素为单位)
        $config['wm_overlay_path'] = $mark; //水印图
        $config['wm_opacity'] = '30';//水印图像的透明度
        $config['wm_x_transp'] = '4';//水印图像通道
        $config['wm_y_transp'] = '4';//水印图像通道
        if(!empty($new_image)){
            $del_original = 1;
            $config['new_image'] = $new_image; //设置图像的目标名/路径。
        }
        $this->ci->image_lib->initialize($config);
        //$this->ci->image_lib->resize();
        $bool = $this->ci->image_lib->watermark();
        if($bool && $del_original){
            @unlink($img);
        }
        return $bool;
    }

}