<?php
/**
 * Description:调试管理
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2019/1/18
 * Time: 11:27
 */
class Debug extends BaseController{

    //下载页面
    public function downloadPage()
    {
        //渲染展示页面
        $this->loadView->display("Admin/Debug/downloadPage");
    }

    //下载插件
    public function download()
    {
        //清除页面缓存
        ob_clean();

        $chrome_dir = realpath(__ROOT__."/Public/download/google_extension1.0.zip");
        if(!is_file($chrome_dir))
        {
            ob_clean();
            header("Location:/");
            exit();
        }

        $ext = pathinfo($chrome_dir,PATHINFO_EXTENSION);

        $base_name = pathinfo($chrome_dir,  PATHINFO_BASENAME );

        header('Content-type: application/'.$ext);

        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.$base_name.'"');

        // The PDF source is in original.pdf
        readfile($chrome_dir);
        exit();
    }

    /**
     * Description:插件使用说明
     */
    public function explain()
    {
        $this->loadView->display("Admin/Debug/explain");
    }
}