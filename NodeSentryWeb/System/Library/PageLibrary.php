<?php
/**
 * Created by PhpStorm.
 * User: Abel
 * Date: 2018-5-23 0023
 * Time: 13:48
 */
class PageLibrary{

    private $totalRows;
    private $listRows;
    private $parameter;
    private $nowPage = 1;
    public $page_name = "page";
    private $firstRow;
    private $totalPages;//总分页数
    public $lastSuffix = true; // 最后一页是否显示总页数
    private $rollPage = 5;
    public $url_;

    // 分页显示定制
    private $config  = array(
        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        'prev'   => '<',
        'next'   => '>',
        'first'  => '1...',
        'last'   => '...%TOTAL_PAGE%',
        'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    );


    /**
     * PageLibrary constructor.
     * @param array $totalRow   总的条数
     * @param int $listRow 每一页
     * @param array $param  分页跳转的参数
     */
    public function __construct($totalRow,$listRow,$pageNmae="page")
    {
        $this->page_name = $pageNmae;
        $this->totalRows = $totalRow;
        $this->listRows = $listRow;
        $this->nowPage = empty($_GET[$this->page_name]) ? 1 : (int)$_GET[$this->page_name];
        $this->nowPage = $this->nowPage > 0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);
    }

    //组装url形成新的url
    private function installUrl()
    {
        //将改链接中生成page页
        if(!$this->url_) {
            $url = $_SERVER["REQUEST_URI"];
        }else{
            $url = $this->url_;
        }
        $return_url = "";
        $urlArr  = explode("?",$url);
        if(count($urlArr) > 1)
        {//存在get参数
            //存在页码参数
            if(isset($_GET[$this->page_name]))
            {
                if(preg_match("/\?".$this->page_name."=(.*?)/",$url,$begin))
                {
                    $return_url = preg_replace("/(\?)".$this->page_name."=([^&]*)/","?".$this->page_name."=".urlencode("[PAGE]"),$url);
                }else{
                    $return_url = preg_replace("/(\&)".$this->page_name."=([^&]*)/","&".$this->page_name."=".urlencode("[PAGE]"),$url);
                }
            }else{
                //不存在页码参数
                $return_url = base_url($url."&".$this->page_name."=".urlencode("[PAGE]"));
            }
        }else{
            //不存在get参数
            $return_url = base_url($url."?".$this->page_name."=".urlencode("[PAGE]"));
        }
        return $return_url;
    }

    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    private function makeUrl($page){
        return str_replace(urlencode('[PAGE]'), $page, $this->url);
    }

    /**
     * Description:展示分页链接
     */
    public function show()
    {
        if($this->totalRows == 0) return "";
        $this->parameter[$this->page_name] = "[PAGE]";

        $this->url = $this->installUrl();

        //计算总的分页数
        $this->totalPages = ceil($this->totalRows/$this->listRows);
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }

        /* 计算分页临时变量 */
        $now_cool_page      = $this->rollPage/2;
        $now_cool_page_ceil = ceil($now_cool_page);

        $this->lastSuffix && $this->config['last'] = $this->totalPages;

        //上一页
        $up_row  = $this->nowPage - 1;
        $up_page = $up_row > 0 ?
            '<li><a aria-label="Previous" class="prev" href="' . $this->makeUrl($up_row) . '"><span aria-hidden="true">' . $this->config['prev'] . '</span></a></li>'
            :
            '<li class="disabled"><a aria-label="Previous" class="prev" href="javascript:;"><span aria-hidden="true">' . $this->config['prev'] . '</span></a></li>';

        //下一页
        $next_row  = $this->nowPage + 1;
        $down_page = ($next_row <= $this->totalPages) ?
            '<li><a aria-label="Next" class="prev" href="' . $this->makeUrl($next_row) . '"><span aria-hidden="true">' . $this->config['next'] . '</span></a></li>'
            :
            '<li class="disabled"><a aria-label="Next" class="prev" href="javascript:;"><span aria-hidden="true">' . $this->config['next'] . '</span></a></li>';

        //第一页
        $the_first = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1){
            $the_first =  '<li class="first"><a href="'.$this->makeUrl(1).'">'.$this->config['first'].'</a></li>';
        }

        //最后一页
        $the_end = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages){
            $the_end = '<li><a class="end" href="'.$this->makeUrl($this->totalPages).'">'.$this->config['last'] .'</a></li>';
        }

        //数字连接
        $link_page = "";
        for($i = 1; $i <= $this->rollPage; $i++){
            if(($this->nowPage - $now_cool_page) <= 0 ){
                $page = $i;
            }elseif(($this->nowPage + $now_cool_page - 1) >= $this->totalPages){
                $page = $this->totalPages - $this->rollPage + $i;
            }else{
                $page = $this->nowPage - $now_cool_page_ceil + $i;
            }
            if($page > 0 && $page != $this->nowPage){

                if($page <= $this->totalPages){
                    $link_page .= '<li><a href="' . $this->makeUrl($page) . '">'.$page.'</a></li>';
                }else{
                    break;
                }
            }else{
                if($page > 0 && $this->totalPages != 1){
                    $link_page .= '<li class="active"><a href="' . $this->makeUrl($page) . '">'.$page.'</a></li>';
                }
            }
        }
        //替换分页内容
        $page_str = str_replace(
            array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%'),
            array($this->config['header'], $this->nowPage, $up_page, $down_page, $the_first, $link_page, $the_end, $this->totalRows, $this->totalPages),
            $this->config['theme']);

        return "<ul class='pagination'>{$page_str}</ul>";
    }
}