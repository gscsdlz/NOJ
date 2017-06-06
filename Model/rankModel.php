<?php
class rankModel extends DB {
    private $users = array ();
    private $contestrank = array ();
    public function __construct() {
        parent::__construct ();
    }
    private function get_users() {
        $res = parent::query ( "SELECT user_id, username, motto, nickname FROM users WHERE activate = 1" );
        while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {

            $this->users [$row [0]] = array (
                $row [1],
                $row [2],
                0,
                0,
                0,
                $row[3]
            );
        }
    }
    public function getRank($page = 0, $user_id = 0) {
        $status = redisDB::get ( 'ranks' );

        if ($status) {
            $this->users = json_decode ( $status, true );
        } else {
            $this->get_users ();
            $this->getAcNum ();
            $this->getnAcNum ();
            function cmp($a, $b) {
                if ($a [2] == $b [2]) {
                    if ($a [3] == $b [3])
                        return $a [0] < $b [0] ? - 1 : 1;
                    if ($a [2] == 0)
                        return $a [3] > $b [3] ? - 1 : 1;
                    else
                        return $a [3] > $b [3] ? 1 : - 1;
                } else {
                    return $a [2] < $b [2] ? 1 : - 1;
                }
            }
            uasort ( $this->users, "cmp" );
            $i = 1;
            foreach ( $this->users as &$row ) { // 通过引用修改原数组的值
                $row [4] = $i ++;
            }
        }
        if ($user_id > 0) {
            /*
             * 可能存在性能BUG
             */
            if (isset ( $this->users [$user_id] ))
                $tmp = $this->users [$user_id];
            else
                $tmp = 1;
            reset ( $this->users );
            /**
             */
            for($i = 0; $i < $tmp [4] - 3; ++ $i) {
                next ( $this->users );
            }
            for($i = 0; $i < 4; $i ++) {
                $args [] = current ( $this->users );
                next ( $this->users );
            }
            unset ( $this->users );
            return $args;
        }
        $rps = RANKPAGEMAXSIZE;
        if (count ( $this->users ) / $rps < $page)
            $page = ( int ) (count ( $this->users ) / $rps + 1);
        else if ($page < 0)
            $page = 0;
        if ($status == false) {
            redisDB::setWithTimeOut( 'rank', json_encode ( $this->users ) , 10 * 60);
        }
        $args [] = array (
            RANKPAGEMAXSIZE,
            count ( $this->users )
        );
        $args [] = array_slice ( $this->users, $page * $rps, $rps );

        unset ( $this->users );
        return $args;
    }
    private function getAcNum() {
        $res = parent::query ( "SELECT COUNT(DISTINCT pro_id), users.user_id FROM users LEFT JOIN status ON (users.user_id = status.user_id) WHERE status=4 AND contest_id=0 AND activate=1 GROUP BY (status.user_id)" );
        while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
            $this->users [$row [1]] [2] += ( int ) $row [0];
        }
    }
    private function getnAcNum() {
        $res = parent::query ( "SELECT COUNT(pro_id), users.user_id FROM users LEFT JOIN status ON (users.user_id = status.user_id) WHERE contest_id = 0 AND activate=1 GROUP BY (status.user_id)" );
        while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
            $this->users [$row [1]] [3] += ( int ) $row [0];
        }
    }

    /**
     * 严重的性能问题，采用redis缓存减少查询
     *
     * @param int $cid
     */
    public function contest_rank($cid, $page, $group=-1) {
        $tmp = array();
        $cache = false;
        $options = redisDB::get('contest_options:'.$cid);
        if ($options !== false) {
            $status = redisDB::get ( 'contest_rank:' . $cid );
            if ($status !== false) {
                $tmp = json_decode ( $status, true );
                $cache = true;
                $medalNum = json_decode(redisDB::get('contest_medal_num:'.$cid), true );
                $oi_mode = json_decode(redisDB::get('contest_oi:'.$cid), true );
            }
        }
        if($cache == false) {
            $medalNum = $this->get_medal_num($cid);
            $this->get_all_status($cid);
            $c_stime = $this->get_contest_stime($cid);
            $options = $this->get_contest_option($cid);
            $oi_mode = $this->get_oi_mode($cid);
            if($oi_mode == 1)
                $tmp = $this->sort_with_oi($c_stime);
            else
                $tmp = $this->sort_without_oi($cid, $c_stime);

        }
        if(count($tmp) == 0) {
            return null;
        } else {
            if($cache == false) {
                redisDB::setWithTimeOut('contest_medal_num:' . $cid, json_encode($medalNum), REDISCACHETIME);
                redisDB::setWithTimeOut('contest_options:' . $cid, $options, REDISCACHETIME);
                redisDB::setWithTimeOut('contest_rank:' . $cid, json_encode($tmp), REDISCACHETIME);
                redisDB::setWithTimeOut('contest_oi:'.$cid, json_encode($oi_mode), REDISCACHETIME);
            }
            $args = array();
            $pstart = $page * RANKPAGEMAXSIZE + 1;
            $pend = ($page + 1) * RANKPAGEMAXSIZE;
            $p = 1;
            $rank = 1;
            $t = 0;
            $groups = array();
            foreach ($tmp as $key) {
                $groups[] = $key[3];
                if ($group == -1) {
                    if ($p >= $pstart && $p <= $pend) {
                        $args[$t] = $key;
                        $args[$t++][5] = $rank;
                    }
                    $p++;
                } else if ($group == $key[3]) {
                    if ($p >= $pstart && $p <= $pend) {
                        $args[$t] = $key;
                        $args[$t++][5] = $rank;
                    }
                    $p++;
                }
                $rank++;
            }
            if($group == -1)
                $group = "";


            $info = array(
                'pageN' => $page,
                'pageT' => (int)(($p + 1) / RANKPAGEMAXSIZE) + 1,
                'teams' => array_unique($groups),
                'oi_mode' => $oi_mode,
                'team' => $group,
                'auNum' => $medalNum['0'],
                'agNum' => $medalNum['1'],
                'cuNum' => $medalNum['2'],
                'feNum' => $medalNum['3'],
                'options' => $options

            );
            if($cache == true) {
                $info['ttl'] = redisDB::ttl('contest_rank:' . $cid);
            }

            return [$info, $args];
        }
    }

    /**
     * @param $cid
     * @return 返回金银铜铁牌（对应一，二，三，优秀奖）的数量
     * 数据约束如下:
     * 	0表示不设置奖牌，例如比赛只有一二三等奖，只需要将优秀奖数量设置为0即可
     *
     */
    public function get_medal_num($cid) {
        $res = parent::query_one("SELECT au, ag, cu, fe FROM contest WHERE contest_id = ?", array($cid));
        if($res) {
            return array($res[0], $res[1], $res[2], $res[3]);
        } else {
            return null;
        }
    }

    public function get_contest_option($cid) {
        $res = parent::query_one("SELECT options FROM contest WHERE contest_id = ?", array($cid));
        if($res)
            return $res[0];
        else
            return null;
    }

    private function get_contest_stime($cid) {
        $res = parent::query_one ( "SELECT c_stime FROM contest WHERE contest_id = ?", array (
            $cid
        ) );
        if ($res)
            return ( int ) $res [0];
        return null;
    }

    private function get_oi_mode($cid){
        $res = parent::query_one("SELECT oi FROM contest WHERE contest_id = ?", [$cid]);
        if($res)
            return $res[0];
        return null;
    }

    /**
     * 可能是严重性能问题
     *
     * @param unknown $cid
     */
    private function get_all_status($cid) {
        $res = parent::query ( "SELECT user_id, inner_id, status, submit_time, score FROM status LEFT JOIN contest_pro ON(status.contest_id = contest_pro.contest_id AND status.pro_id = contest_pro.pro_id) WHERE status.contest_id = ? ORDER BY submit_time", array (
            $cid
        ) );
        if ($res->rowCount () != 0) {
            while ( $row = $res->fetch ( PDO::FETCH_NUM ) ) {
                if (! isset ( $this->contestrank [$row [0]] )) {
                    $this->contestrank [$row [0]] = array ();
                }
                if (! isset ( $this->contestrank [$row [0]] [$row [1]] )) {
                    $this->contestrank [$row [0]] [$row [1]] = array ();
                }
                $count = count ( $this->contestrank [$row [0]] [$row [1]] );
                $this->contestrank [$row [0]] [$row [1]] [$count] = array (
                    $row [2],
                    $row [3],
                    $row[4]
                );
            }
        }
    }
    private function get_userinfo($user_id) {
        $res = parent::query_one ( "SELECT users.username, users.nickname, group_name FROM users LEFT JOIN `team` ON (team.group_id = users.group_id) WHERE users.user_id = ?", array (
            $user_id
        ) );
        return $res;
    }

    /**
     * 标准ACM比赛下，排名排序函数
     */
    private function sort_without_oi($cid, $c_stime){
        /**
         * $contestrank 层次说明
         * 第一层 用户ID
         * 第二层 当前用户所有题目
         * 第三层 该用户当前题目的提交情况 按时间排序
         * 第四层 该题目状态。该题目提交时间
         * 过程描述。遍历第三层 确定第一次正确提交前有多少次错误
         */
        $tmp = array();
        $res = parent::query("SELECT inner_id FROM contest_pro WHERE contest_id = ? ORDER BY inner_id ASC", array(
            $cid
        ));
        $fb = array();
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $fb [$row [0]] = array(
                0,
                0
            );
        }
        if ($c_stime && count($this->contestrank) != 0) {
            foreach ($this->contestrank as $key => $users) {
                $tmp [$key] = array();
                $total_time = 0; // 总秒数
                $total_ac = 0; // 总正确次数
                foreach ($users as $key2 => $pro) { // 需要修改
                    $tmp [$key] [$key2] = array();
                    $len = count($pro);
                    $pro_time = 0; // 单个题目总秒数
                    $pro_wa = 0; // 单个题目总错误次数
                    $pro_ac = false;
                    foreach ($pro as $status) {
                        if ($status [0] != 4) {
                            $pro_wa++;
                        } else { // 一旦通过 之后的提交都不在计算
                            if ($fb [$key2] [0] == 0 || $fb [$key2] [0] > ( int )$status [1]) {
                                $fb [$key2] [0] = ( int )$status [1];
                                $fb [$key2] [1] = $key;
                            }
                            $pro_ac = true;
                            $pro_time += ( int )$status [1] - $c_stime;
                            break;
                        }
                    }
                    /**
                     * 说明
                     * time = 0， wa = n 错误了n次 仍然为通过
                     * time = n，wa = m 再经过m次错误以后，通过题目 m可以为0
                     */
                    if (!$pro_ac)
                        $tmp [$key] [$key2] = array(
                            0,
                            $pro_wa
                        );
                    else {
                        $total_ac++;
                        $tmp [$key] [$key2] = array(
                            $pro_time,
                            $pro_wa
                        );
                        $total_time += $pro_time + $pro_wa * 20 * 60; // 加上罚时
                    }
                } // $pro
                $tmp [$key] [0] = $total_time;
                $tmp [$key] [1] = $total_ac;
                $userinfo = $this->get_userinfo($key);
                $tmp [$key] [2] = $userinfo [0];
                $tmp [$key] [3] = $userinfo [2];
                $tmp [$key] [4] = $userinfo [1];
            } // users
        }

        unset ($this->contestrank);
        function cmp($a, $b)
        {
            if ($a [1] == $b [1]) {
                if ($a [1] == 0)
                    return $a [0] > $b [0] ? -1 : 1;
                else
                    return $a [0] > $b [0] ? 1 : -1;
            } else {
                return $a [1] < $b [1] ? 1 : -1;
            }
        }

        uasort($tmp, "cmp");
        foreach ($fb as $key => $value) {
            if ($value [0] > 0)
                $tmp [$value [1]] [$key] [] = 1;//根据fb数组设置tmp数组中的状态值
        }
        output_csv($cid, $tmp, $fb);
        return $tmp;
    }

    /**
     * OI模式下，排名排序函数
     */
    private function sort_with_oi($c_stime){
        $tmp = array();
        /*
         * 没有罚时 按照分数排序
         */
        if ($c_stime && count($this->contestrank) != 0) {
            foreach ($this->contestrank as $key => $users) {
                $tmp [$key] = array();
                $total_score = 0; //总分数
                $total_submit = 0; // 总提交次数
                foreach ($users as $key2 => $pro) {
                    $tmp [$key] [$key2] = array();
                    $pro_score = 0; // 单个题目分数
                    $pro_submit = count($pro); // 单个题目总提交次数
                    $pro_ac = false;
                    foreach ($pro as $status) {
                        $pro_score = max($pro_score, $status[2]);
                        if($status[0] == 4)
                            $pro_ac = true;
                    }
                    $tmp[$key][$key2] = array(
                         $pro_score,
                         $pro_submit,
                         $pro_ac
                    );
                    $total_score += $pro_score;
                    $total_submit += $pro_submit;
                } // $pro
                $tmp [$key] [0] = $total_score;
                $tmp [$key] [1] = $total_submit;
                $userinfo = $this->get_userinfo($key);
                $tmp [$key] [2] = $userinfo [0];
                $tmp [$key] [3] = $userinfo [2];
                $tmp [$key] [4] = $userinfo [1];
            } // users
        }

        unset ($this->contestrank);
        function cmp($a, $b)
        {
            if ($a [0] == $b [0]) {
                    return $a [1] > $b [1] ? 1 : -1;
            } else {
                return $a [0] < $b [0] ? 1 : -1;
            }
        }

        uasort($tmp, "cmp");
        //output_csv($cid, $tmp, $fb);
        return $tmp;
    }
}
?>
