<?php
if (defined ( 'APPPATH' )) {
    require APPPATH . '/Model/problemModel.php';
    require APPPATH . '/Include/smarty/core/Smarty.class.php';
} else {
    die ();
}
class problemControl extends Smarty {
    private static $model = null;
    public function __construct() {
        parent::__construct();
        if (self::$model == null) {
            self::$model = new problemModel ();
        }
    }
    public function index() {
        $this->page ();
    }
    public function __call($method, $args) {
        parent::assign('errorInfo', 'Invalid Action');
        parent::display('error.html');
    }
    public function show() {
        $problemId = get ( 'id' );
        if (! $problemId)
            $problemId = '1000';
        $body = self::$model->get_problem ( $problemId ); // 获取页面主体
        $submits = self::$model->get_submits ( $problemId );
        /**
         * 管理员添加题目以后 允许查看隐藏题目 @/admin/aView/problem_list.php
         */
        if ($body ['visible'] == 0)
            if (! (isset ( $_SESSION ['user_id'] ) && $_SESSION ['privilege'] [0] == 1))
                $body = null;
        if ($body) {
            $body ['aSubmit'] = $submits [0];
            $body ['tSubmit'] = $submits [1];
            global $langArr;
            $body ['langArr'] = $langArr;
            $body ['contest'] = 0;
            parent::assign('title', $body['pro_title']);
            parent::assign($body);
            parent::display('problem.html');
        } else {
            parent::assign('errorInfo', 'Invalid Id');
            parent::display('error.html');
        }
    }
    public function page() {
        $pageId = get ( 'id' );
        if (! $pageId)
            $pageId = 0;
        $_GET ['pageid'] = $pageId; // 这里重新设置ID的意义在于 @problem_list:4 需要通过读取GET数组确定分页单元的显示
        $lists = self::$model->get_list ( $pageId );
        if ($lists) {

            parent::assign( 'lists', $lists );
            parent::display('problem_list.html');
        } else {
            parent::assign('errorInfo', 'Invalid Id');
            parent::display('error.html');
        }

    }
    public function search() {
        $key = get ( 'key' );
        $lists = self::$model->get_search_result ( $key );
        parent::assign( 'lists', $lists );
        parent::display('problem_list.html');
    }
    public function get_statistics() {
        if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
            $cid = ( int ) post ( 'cid' );
            $pro_id = ( int ) post ( 'pro_id' );
            if($cid > 0) {
                $pro_id = self::$model->get_real_id($pro_id, $cid);
            }
            if ($pro_id >= 1000) {
                $args [] = self::$model->get_stat ( $pro_id, $cid );
                echo json_encode(array(
                    'status' => true,
                    'info' => $args
                ));
            }
        }
    }
}
?>
