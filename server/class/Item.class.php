<?php
//类别查询
class Item
{

    protected $conn;
    protected $result;

    public function __construct()
    {
        //数据库初始化
        $this->conn = new mysqli('127.0.0.1:3306', 'root', 'ejX5*aJlQ', 'lanpartyclub_database');
        if (!mysqli_connect_errno()) {
            mysqli_options($this->conn, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
        } else {
            $this->conn = false;
            echo '<h2>数据库连接错误，请检查服务器:' . mysqli_connect_error() . '</h2>';
            die();
        }
    }

    //析构函数：主要用来释放结果集和关闭数据库连接
    public function __destruct()
    {
/*
if ($this->result) {
mysqli_free_result($this->result);
}*/
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function get_item($id)
    {
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 1400, 'ERROR: 请求参数不正确，请检查参数 id = ' . $id);
        }
        //品项查询：如果不为空则放入返回数组
        if (!empty($temp = $this->select_item($id))) {
            $arrResponse['item'] = $temp;
            //品项类别查询：如果不为空则放入返回数组
            $arrItemClass = $this->select_item_class($id);
            if (!empty($arrItemClass)) {
                $arrResponse['class']  = $arrItemClass[1]; //结果集合为多，但只需要一组
                $arrResponse['pclass'] = $arrItemClass[0]; //结果集合为多，但只需要一组
            }
            //品项详情查询：如果不为空则放入返回数组
            $arrResponse['detail'] = $this->select_item_detail_all($id);
            //品项相册查询：如果不为空则放入返回数组
            $arrResponse['photo'] = $this->select_item_photo_all($id); //品项唯一
            //输出
            return ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 1402, 'ERROR: 没有对应数据，请检查参数 id = ' . $id);
        }
        return ResponseModel($arrResponse, 1500, 'ERROR: 未知错误');
    }

    public function get_item_all()
    {
        $arrResponse = array();
        //品项查询：如果不为空则放入返回数组
        $arrResponse['item'] = $this->select('SELECT item_id AS id , item_name AS title , item_brief AS brief FROM lanpartyclub_item');
        if (!empty($arrResponse['item'])) {
            foreach ($arrResponse['item'] as &$item) {
                $id = $item['id'];
                //增加品项封面图片
                if (!empty($array_item_photo = $this->select('SELECT item_photo_url AS cover FROM lanpartyclub_item_photo WHERE item_id =' . $id . ' LIMIT 1;'))) {
                    $filename      = $array_item_photo[0]['cover'];
                    $item['cover'] = $this->getThumb($filename);
                } else {
                    $item['cover'] = "";
                }
                //查询品项类别
                $arrItemClass = $this->select_item_class($id);
                if (!empty($arrItemClass)) {
                    $item['class']  = $arrItemClass[1]; //结果集合为多，但只需要一组
                    $item['pclass'] = $arrItemClass[0]; //结果集合为多，但只需要一组
                }
            }
            //输出
            return ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 402, 'ERROR: 没有对应数据，请添加数据');
        }
        return $arrResponse;
    }

    public function get_item_class($id)
    {
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 id = ' . $id);
        }
        //品项搜索
        $arrResponse['item'] = $this->select('SELECT item_id AS id , item_name AS title , item_brief AS brief FROM lanpartyclub_item WHERE item_id IN ( SELECT item_id FROM lanpartyclub_re_item_class WHERE class_id =' . $id . ')');
        if (!empty($arrResponse['item'])) {
            foreach ($arrResponse['item'] as &$item) {
                $id = $item['id'];
                //增加品项封面图片
                if (!empty($array_item_photo = $this->select('SELECT item_photo_url AS cover FROM lanpartyclub_item_photo WHERE item_id =' . $id . ' LIMIT 1;'))) {
                    $filename      = $array_item_photo[0]['cover'];
                    $item['cover'] = $this->getThumb($filename);
                } else {
                    $item['cover'] = "";
                }
                //查询品项类别
                $arrItemClass = $this->select_item_class($id);
                if (!empty($arrItemClass)) {
                    $item['class']  = $arrItemClass[1]; //结果集合为多，但只需要一组
                    $item['pclass'] = $arrItemClass[0]; //结果集合为多，但只需要一组
                }
            }
            //输出
            return ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 402, 'ERROR: 没有对应数据，请添加数据');
        }
        return $arrResponse;
    }

    public function get_item_class_random($id)
    {
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确');
        }
        //品项按类别搜索
        $arrItemResult = $this->select('SELECT item_id AS id , item_name AS title , item_brief AS brief FROM lanpartyclub_item WHERE item_random = 1 AND item_id IN ( SELECT item_id FROM lanpartyclub_re_item_class WHERE class_id =' . $id . ' ) '); //过滤黑名单品项

        if (!empty($arrItemResult)) {
            $arrResponse['item'] = $arrItemResult[rand(0, count($arrItemResult) - 1)];
            $id                  = $arrResponse['item']['id'];
            //增加品项照片集合
            //$arrResponse['photo'] = $this->select_item_photo_all($id);
            $arrResponse['photo'] = $this->select('SELECT item_photo_id AS id,item_photo_url AS url FROM lanpartyclub_item_photo WHERE item_id =' . $id . ' limit 3'); //限制三张，减少延迟
            foreach ($arrResponse['photo'] as &$photo) {
                $filename     = $photo['url'];
                $photo['url'] = $this->getThumb($filename);
                //增加品项封面图片
            }
            return ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 402, 'ERROR: 没有对应数据，请检查参数 id=' . $id);
        }
        return $arrResponse;
    }

    public function post_item($title, $brief, $class)
    {
        //参数设置
        $arrResponse = array();
        //参数检测
        if (!preg_match('/^[0-9]{1,8}$/', $class) || preg_match('/^[1-3]$/', $class)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 class = ' . $class);
        }
        $title = dowith_sql($title);
        if (empty($title)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 title = ' . $title);
        }
        $brief = dowith_sql($brief);
        if (empty($brief)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 brief = ' . $brief);
        }
        //参数检测 标题
        $array_title = $this->select('SELECT item_name AS title FROM lanpartyclub_item WHERE item_name="' . $title . '"');
        if (!empty($array_title)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，标题重名，请检查 title = ' . $title);
        }
        //参数检测 类别与父类别
        $array_pid = $this->select('SELECT class_pid AS pid FROM lanpartyclub_class WHERE class_id=' . $class);
        if (!empty($array_pid)) {
            $pclass = $array_pid[0]['pid'];
            if (!preg_match('/^[1-3]$/', $pclass)) {
                return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，类别级别错误，请检查 class = ' . $class);
            }
        } else {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查 class = ' . $class);
        }
        //新增
        if ($this->insert('INSERT INTO lanpartyclub_item ( item_name , item_brief ) VALUES ("' . $title . '","' . $brief . '")') > 0) {
            $array_item = $this->select('SELECT item_id AS id FROM lanpartyclub_item WHERE item_name="' . $title . '"');
            if (empty($array_item)) {
                return ResponseModel($arrResponse, 500, 'ERROR: 操作过快，数据库品项尚未刷新');
            }
        } else {
            return ResponseModel($arrResponse, 500, 'ERROR: 向数据库插入品项失败');
        }
        //获得id
        $id = $array_item[0]['id'];
        //插入类别
        //插入子类
        $number_insert_item_class = $this->insert('INSERT INTO lanpartyclub_re_item_class ( item_id , class_id ,level) VALUES ("' . $id . '","' . $class . '",2)');
        if ($number_insert_item_class > 0) {
            //插入父类
            $number_insert_item_pclass = $this->insert('INSERT INTO lanpartyclub_re_item_class ( item_id , class_id  ,level) VALUES ("' . $id . '","' . $pclass . '",1)');
            if ($number_insert_item_pclass > 0) {
                $arrResponse['id'] = $id;
                return ResponseModel($arrResponse, 200, 'Success: 成功');
            } else {
                $this->delete('DELETE FROM lanpartyclub_item WHERE item_id=' . $id); //回退插入操作
                return ResponseModel($arrResponse, 500, 'ERROR: 插入父类别失败');
            }
        } else {
            $this->delete('DELETE FROM lanpartyclub_item WHERE item_id=' . $id); //回退插入操作
            return ResponseModel($arrResponse, 500, 'ERROR: 插入子类别失败');
        }
        return $arrResponse;
    }

    public function put_item($id, $title, $brief, $class)
    {
        //参数设置
        $arrResponse = array();
        $message     = "";
        //检测参数
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 id = ' . $id);
        }
        if (!preg_match('/^[0-9]{1,8}$/', $class) || preg_match('/^[1-3]$/', $class)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 class = ' . $class);
        }
        $title = dowith_sql($title);
        if (empty($title)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 title = ' . $title);
        }
        $brief = dowith_sql($brief);
        if (empty($brief)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 brief = ' . $brief);
        }
        //检测参数 ID
        $array_item = $this->select('SELECT item_id FROM lanpartyclub_item WHERE item_id = ' . $id);
        if (empty($array_item)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查 id = ' . $id);
        }
        //检测参数 标题
        $array_title = $this->select('SELECT item_id FROM lanpartyclub_item WHERE item_name = "' . $title . '" AND item_id <> ' . $id);
        if (!empty($array_title)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，标题重名，请检查 title = ' . $title);
        }
        //检测参数 父类别
        $array_pid = $this->select('SELECT class_pid FROM lanpartyclub_class WHERE class_id = ' . $class);
        if (!empty($array_pid)) {
            $pclass = $array_pid[0]['class_pid'];
            if (!preg_match('/^[1-3]$/', $pclass)) {
                return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查class = ' . $class);
            }
        } else {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查class = ' . $class);
        }
        //执行修改
        if ($this->update('UPDATE lanpartyclub_item SET item_name = "' . $title . '", item_brief="' . $brief . '" WHERE item_id=' . $id) <= 0) {
            $message .= ' 品项基本信息无变化';
        }
        //插入子类
        if ($this->update('UPDATE lanpartyclub_re_item_class SET class_id = "' . $class . '" WHERE item_id = ' . $id . ' AND level=2') <= 0) {
            $message .= ' 品项子类别无变化';
        }
        //插入父类
        if ($this->update('UPDATE lanpartyclub_re_item_class SET class_id = "' . $pclass . '" WHERE item_id = ' . $id . ' AND level = 1') <= 0) {
            $message .= ' 品项父类别无变化';
        }
        $arrResponse['id'] = $id;
        return ResponseModel($arrResponse, 200, 'Success: 成功' . $message);
    }

    public function delete_item($id)
    {
        //参数初始化
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查 id');
        }
        //记录图片删除操作
        $this->insert('INSERT INTO lanpartyclub_task ( task_command,task_parameter,task_status ) SELECT "DeleteItemPhoto", item_photo_url, 0 FROM lanpartyclub_item_photo WHERE item_id=' . $id);
        //删除
        if ($this->delete('DELETE FROM lanpartyclub_item WHERE item_id=' . $id) > 0) {
            $arrResponse = ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 204, 'ERROR: 不存在对应数据');
        }
        return $arrResponse;
    }

    public function post_item_detail($id, $title, $content)
    {
        //参数设置
        $arrResponse = array();
        //参数检测
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 id = ' . $id);
        }
        $title = dowith_sql($title);
        if (empty($title)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 title = ' . $title);
        }
        $content = dowith_sql($content);
        if (empty($content)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 content = ' . $content);
        }
        //检测参数 ID
        $array_item = $this->select('SELECT item_id FROM lanpartyclub_item WHERE item_id = ' . $id);
        if (empty($array_item)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，数据不存在，请检查 id = ' . $id);
        }
        //检测参数 标题
        $array_title = $this->select('SELECT item_id FROM lanpartyclub_item_detail WHERE item_detail_name = "' . $title . '" AND item_id = ' . $id);
        if (!empty($array_title)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，标题重名，请检查 title = ' . $title);
        }
        //新增
        if ($this->insert('INSERT INTO lanpartyclub_item_detail ( item_detail_name , item_detail_content,item_id ) VALUES ("' . $title . '","' . $content . '",' . $id . ')') != -1) {
            return ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 500, 'ERROR: 向数据库插入品项详情失败，未知原因');
        }
        return $arrResponse;
    }

    public function put_item_detail($id, $title, $content)
    {
        //参数设置
        $arrResponse = array();
        //参数检测
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 id = ' . $id);
        }
        $title = dowith_sql($title);
        if (empty($title)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 title = ' . $title);
        }
        $content = dowith_sql($content);
        if (empty($content)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 content = ' . $content);
        }
        //检测参数 ID
        $array_item_detail = $this->select('SELECT item_id FROM lanpartyclub_item_detail WHERE item_detail_id = ' . $id);
        if (empty($array_item_detail)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，数据不存在，请检查 id = ' . $id);
        }
        $item_id = $array_item_detail[0]['item_id'];
        //检测参数 标题
        //$array_item_detail = $this->select('SELECT * FROM lanpartyclub_item_detail WHERE item_detail_name = "' . $title . '" AND item_id = ' . $item_id . ' AND item_detail_id <> ' . $id);
        //if (!empty($array_item_detail)) {
        //return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，标题重名，请检查 title = ' . $title);
        //}
        //修改
        if ($this->update('UPDATE lanpartyclub_item_detail SET item_detail_name="' . $title . '" , item_detail_content="' . $content . '" WHERE item_detail_id=' . $id) > 0) {
            return ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 204, 'Success: 数据无变化');
        }
    }

    public function delete_item_detail($id)
    {
        //参数初始化
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查 id');
        }
        //删除
        $sql          = 'DELETE FROM lanpartyclub_item_detail WHERE item_detail_id=' . $id;
        $this->result = mysqli_query($this->conn, $sql);
        //输出
        if (mysqli_affected_rows($this->conn)) {
            $arrResponse = ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 204, 'ERROR: 不存在对应数据');
        }
        return $arrResponse;
    }

    public function post_item_photo($id, $file)
    {
        //参数初始化
        $arrResponse = array();
        //检测参数
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确');
        }
        //保存文件
        $filename = guid();
        $fileType = getExtension($file["tmp_name"]);
        $data     = saveImage($file, $filename);
        if (!$data['status']) {
            return ResponseModel($arrResponse, 500, 'ERROR: 文件上传失败' . $data['message']);
        }
        //插入数据库
        if ($this->insert('INSERT INTO lanpartyclub_item_photo ( item_photo_url , item_id ) VALUES ("' . $filename . '.' . $fileType . '","' . $id . '")') > 0) {
            return ResponseModel($arrResponse, 200, 'Success: ' . $data['message']);
        } else {
            return ResponseModel($arrResponse, 500, 'ERROR: 数据库插入失败');
        }
        return ResponseModel($arrResponse, 204, 'ERROR: 未知状态');
    }

    public function delete_item_photo($id)
    {
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确');
        }
        //查询
        $arrResult = $this->select('SELECT item_photo_url FROM lanpartyclub_item_photo WHERE item_photo_id=' . $id);
        if (!empty($arrResult)) {
            //插入任务队列
            $this->insert('INSERT INTO lanpartyclub_task ( task_command,task_parameter,task_status ) VALUES ("DeleteItemPhoto","' . $arrResult[0]['item_photo_url'] . '", 0 )');
        } else {
            return ResponseModel($arrResponse, 204, 'ERROR: 不存在对应数据，请检查参数 id = ' . $id);
        }
        //删除
        if ($this->delete('DELETE FROM lanpartyclub_item_photo WHERE item_photo_id=' . $id) > 0) {
            return ResponseModel($arrResponse, 200, 'Success: 成功');
        }
        return ResponseModel($arrResponse, 400, 'ERROR: 未知错误');
    }

    public function item_class_query($id)
    {
        $arrItemClass = array();
        //查询类别
        $sql          = 'SELECT class_id AS id,class_name AS name,class_pid AS pid FROM lanpartyclub_class WHERE class_id IN ( SELECT class_id FROM lanpartyclub_re_item_class WHERE item_id =' . $id . ') ORDER by class_id'; //类别查询 //sql查询类别id
        $this->result = mysqli_query($this->conn, $sql);
        $arrResult    = array();
        if ($this->result) {
            $row         = $this->result->fetch_assoc();
            $arrResult[] = $row;
            $row         = $this->result->fetch_assoc();
            $arrResult[] = $row;
            //print_r($arrResult);
        } else {
            $arrResult[] = 'ERROR: 没有对应数据';
        }
        //var_dump($this->result);
        if (!empty($arrResult)) {
            $arrResultClassQuery = $arrResult;
            $arrItemClass[]      = $arrResultClassQuery;
        }
        return $arrItemClass;
    }

    protected function select_item($id)
    {
        $arrResult = array();
        //品项查询：如果不为空则放入返回数组
        $sql          = 'SELECT item_id AS id , item_name AS title , item_brief AS brief FROM lanpartyclub_item WHERE item_id =' . $id;
        $this->result = mysqli_query($this->conn, $sql);
        if ($this->result) {
            if (mysqli_num_rows($this->result) > 0) {
                $arrResult = $this->result->fetch_assoc();
            }
        }
        return $arrResult;
    }

    protected function select_item_class($id)
    {
        $arrResult = array();
        //查询类别
        $sql          = 'SELECT class_id AS id,class_name AS name,class_pid AS pid FROM lanpartyclub_class WHERE class_id IN ( SELECT class_id FROM lanpartyclub_re_item_class WHERE item_id =' . $id . ') ORDER by class_id'; //类别查询 //sql查询类别id
        $this->result = mysqli_query($this->conn, $sql);
        if ($this->result) {
            if (mysqli_num_rows($this->result) > 0) {
                for ($i = 0; $i < 2 && $row = $this->result->fetch_assoc(); $i++) {
                    $arrResult[] = $row;
                }
            }
        }
        return $arrResult;
    }

    protected function select_item_detail_all($id)
    {
        $arrResult = array();
        //品项详情查询：如果不为空则放入返回数组
        $sql          = 'SELECT item_detail_id AS id, item_detail_name AS title, item_detail_content AS content FROM lanpartyclub_item_detail WHERE item_id = ' . $id; //sql查询详情id
        $this->result = mysqli_query($this->conn, $sql);
        if ($this->result) {
            if (mysqli_num_rows($this->result) > 0) {
                while ($row = $this->result->fetch_assoc()) {
                    $arrResult[] = $row;
                }
            }
        }
        return $arrResult;
    }

    protected function select_item_photo_all($id)
    {
        $arrResult = array();
        //品项相册查询：如果不为空则放入返回数组
        $sql          = 'SELECT item_photo_id AS id,item_photo_url AS url FROM lanpartyclub_item_photo WHERE item_id =' . $id; //sql查询详情id
        $this->result = mysqli_query($this->conn, $sql);
        if ($this->result) {
            if (mysqli_num_rows($this->result) > 0) {
                while ($row = $this->result->fetch_assoc()) {
                    $arrResult[] = $row;
                }
            }
        }
        return $arrResult;
    }

    protected function select($sql)
    {
        $arrResult    = array();
        $this->result = mysqli_query($this->conn, $sql);
        if ($this->result) {
            if (mysqli_num_rows($this->result) > 0) {
                while ($row = $this->result->fetch_assoc()) {
                    $arrResult[] = $row;
                }
            }
        }
        return $arrResult;
    }

    protected function insert($sql)
    {
        $this->result = mysqli_query($this->conn, $sql);
        return mysqli_affected_rows($this->conn);
    }

    protected function update($sql)
    {
        $this->result = mysqli_query($this->conn, $sql);
        return mysqli_affected_rows($this->conn);
    }

    protected function delete($sql)
    {
        $this->result = mysqli_query($this->conn, $sql);
        return mysqli_affected_rows($this->conn);
    }

    protected function getThumb($filename)
    {
        if (empty($filename)) {
            return "";
        }

        $file_path = "../upload/lanpartyclub/images/album/";
        if (!file_exists($file_path . "thumb/" . $filename)) {
            if (file_exists($file_path . $filename)) {
                $file_type = $this->getFileType($filename);
                if ($file_type == "jpg" || $file_type == "jpeg") {
                    $im = imagecreatefromjpeg($file_path . $filename);
                } else if ($file_type == "png") {
                    $im = imagecreatefrompng($file_path . $filename);
                } else if ($file_type == "bmp") {
                    $im = imagecreatefrombmp($file_path . $filename);
                } else if ($file_type == "gif") {
                    $im = imagecreatefromgif($file_path . $filename);
                } else {
                    $im = imagecreatefromstring($file_path . $filename);
                }
                //参数是图片的存方路径
                $maxwidth  = "480"; //设置图片的最大宽度
                $maxheight = "320"; //设置图片的最大高度
                $name      = $file_path . "thumb/" . $filename; //图片的名称，随便取吧
                $filetype  = ""; //图片类型
                resizeImage($im, $maxwidth, $maxheight, $name, $filetype);
            }
        }
        return 'thumb/' . $filename;
    }

    /**
     * 获取文件类型
     * @param string $filename 文件名称
     * @return string 文件类型
     */
    protected function getFileType($filename)
    {
        return substr($filename, strrpos($filename, '.') + 1);
    }

}
