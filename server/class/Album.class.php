<?php
class Album
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
        //if ($this->result) {
        // mysqli_free_result($this->result);
        //}
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function getAlbum($id)
    {
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确');
        }
        //查询：如果不为空则放入返回数组
        if (empty($arrResponse['album'] = $this->select_album($id))) {
            return ResponseModel($arrResponse, 402, 'ERROR: 数据库没有对应数据');
        }
        //获取照片
        $arrResponse['photo'] = $this->select_album_photo($id);
        //输出
        if (!empty($arrResponse)) {
            $arrResponse = ResponseModel($arrResponse, 200, 'Success: 成功');
            //print_r($arrResult);
        } else {
            return ResponseModel($arrResponse, 402, 'ERROR: 没有合法对应数据');
        }
        return $arrResponse;
    }

    public function getAlbumAll()
    {
        $arrResponse = array();
        //查询：如果不为空则放入返回数组
        if (!empty($arrResponse['album'] = $this->select('SELECT album_id AS id, album_name AS title FROM lanpartyclub_album ORDER BY album_id'))) {
            foreach ($arrResponse['album'] as &$album) {
                if (!empty($album['cover'] = $this->select('SELECT photo_url AS url FROM lanpartyclub_photo WHERE album_id = ' . $album['id'] . ' LIMIT 1;'))) {
                    $filename  = $album['cover'][0]['url'];
                    $file_path = "../upload/lanpartyclub/images/album/";
                    if (file_exists($file_path . $filename)) {
                        if (!file_exists($file_path . "thumb/" . $filename)) {
                            $im        = imagecreatefromjpeg($file_path . $filename); //参数是图片的存方路径
                            $maxwidth  = "600"; //设置图片的最大宽度
                            $maxheight = "400"; //设置图片的最大高度
                            $name      = $file_path . "thumb/" . $filename; //图片的名称，随便取吧
                            $filetype  = ""; //图片类型
                            resizeImage($im, $maxwidth, $maxheight, $name, $filetype);
                        }
                    }
                    $album['cover'] = 'thumb/' . $filename;
                } else {
                    $album['cover'] = "";
                }
            }
            $arrResponse = ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 402, 'ERROR: 没有合法对应数据');
        }
        return $arrResponse;
    }

    public function getAlbumRandom()
    {
        $arrResponse = array();
        //获取所有id
        $array_album_id  = $this->select('SELECT album_id AS id FROM lanpartyclub_album ORDER BY album_id');
        $string_album_id = "";
        if (!empty($array_album_id)) {
            //随机数发生
            $numbers = range(0, count($array_album_id) - 1);
            shuffle($numbers); //shuffle 将数组顺序随即打乱
            if (count($array_album_id) >= 5) {
                $size = 5; //array_slice 取该数组中的某一段
            } else {
                $size = count($array_album_id); //有多少取多少
            }
            $numbers_select = array_slice($numbers, 0, $size);
            for ($i = 0; $i < count($numbers_select); $i++) {
                $string_album_id .= $array_album_id[$numbers_select[$i]]['id'];
                if ($i != count($numbers_select) - 1) {
                    $string_album_id .= ',';
                }
            }
            //获取 封面
            $arrResponse['album'] = $this->select('SELECT album_id AS id, album_name AS title FROM lanpartyclub_album WHERE album_id IN (' . $string_album_id . ')');
            shuffle($arrResponse['album']); //shuffle 将数组顺序随即打乱
            foreach ($arrResponse['album'] as &$album) {
                if (!empty($album['cover'] = $this->select('SELECT photo_url AS url FROM lanpartyclub_photo WHERE album_id = ' . $album['id'] . ' LIMIT 1;'))) {
                    $filename  = $album['cover'][0]['url'];
                    $file_path = "../upload/lanpartyclub/images/album/";
                    if (file_exists($file_path . $filename)) {
                        if (!file_exists($file_path . "thumb/" . $filename)) {
                            $im        = imagecreatefromjpeg($file_path . $filename); //参数是图片的存方路径
                            $maxwidth  = "600"; //设置图片的最大宽度
                            $maxheight = "400"; //设置图片的最大高度
                            $name      = $file_path . "thumb/" . $filename; //图片的名称，随便取吧
                            $filetype  = ""; //图片类型
                            resizeImage($im, $maxwidth, $maxheight, $name, $filetype);
                        }
                    }
                    $album['cover'] = 'thumb/' . $filename;
                } else {
                    $album['cover'] = "";
                }
            }
            //输出
            return ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            $arrResponse['album'] = [];
            $arrResponse          = ResponseModel($arrResponse, 200, 'Success: 没有相册，请上传相册');
        }
        return ResponseModel($arrResponse, 402, 'ERROR: 未知错误');
    }

    public function postAlbum($title)
    {
        //参数设置
        $arrResponse = array();
        //检测参数
        $title = dowith_sql($title);
        if (empty($title)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确');
        }
        //检测参数 title
        if ($this->select('SELECT album_id FROM lanpartyclub_album WHERE album_name ="' . $title . '"')) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，标题重名，请检查 title = ' . $title);
        }
        //插入
        if ($this->insert('INSERT INTO lanpartyclub_album ( album_name ) VALUES ("' . $title . '")') > 0) {
            $array_album_id = $this->select('SELECT album_id FROM lanpartyclub_album WHERE album_name ="' . $title . '"');
            if (!empty($array_album_id)) {
                $arrResponse['id'] = $array_album_id[0]['album_id'];
                return ResponseModel($arrResponse, 200, 'Success: 成功');
            }
        }
        return ResponseModel($arrResponse, 204, 'ERROR: 执行失败，未知错误');
    }

    public function putAlbum($id, $title)
    {
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 id = ' . $id);
        }
        $title = dowith_sql($title);
        if ($title == "") {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 title = ' . $title);
        }
        //检测id
        if (empty($this->select_album($id))) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，不存在对应相册，请检查参数 id = ' . $id);
        }
        //检测参数 title
        if ($temp = $this->select('SELECT album_id FROM lanpartyclub_album WHERE album_name ="' . $title . '"')) {
            if ($temp[0]['album_id'] == $id) {
                return ResponseModel($arrResponse, 204, 'Success: 成功，相册名无改变');
            } else {
                return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，标题重名，请检查 title = ' . $title);
            }
        }
        //更新
        if ($this->update('UPDATE lanpartyclub_album SET album_name ="' . $title . '" WHERE album_id="' . $id . '"') > 0) {
            return ResponseModel($arrResponse, 200, '成功');
        }
        return ResponseModel($arrResponse, 400, 'Error: 未知错误');
    }

    public function deleteAlbum($id)
    {
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确');
        }
        //查询
        $sql          = 'INSERT INTO lanpartyclub_task ( task_command,task_parameter,task_status ) SELECT "DeletePhoto",photo_url,0 FROM lanpartyclub_photo WHERE album_id=' . $id;
        $this->result = mysqli_query($this->conn, $sql);
        //删除
        $sql          = 'DELETE FROM lanpartyclub_album WHERE album_id=' . $id;
        $this->result = mysqli_query($this->conn, $sql);
        if (mysqli_affected_rows($this->conn)) {
            $arrResponse = ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 204, 'ERROR: 不存在对应数据');
        }
        return $arrResponse;
    }

    public function postAlbumPhoto($id, $file)
    {
        //参数设置
        $arrResponse = array();
        //参数检测
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确');
        }
        //上传图片
        $filename = guid();
        $fileType = getExtension($file["tmp_name"]);
        $data     = saveImage($file, $filename);
        if ($data['status']) {
            //插入
            $sql          = 'INSERT INTO lanpartyclub_photo ( photo_url,album_id ) VALUES ("' . $filename . '.' . $fileType . '","' . $id . '")';
            $this->result = mysqli_query($this->conn, $sql);
            //输出
            if (mysqli_affected_rows($this->conn) != -1) {
                return ResponseModel($arrResponse, 200, 'Success: ' . $data['message']);
                //print_r($arrResult);
            } else {
                return ResponseModel($arrResponse, 204, 'ERROR: 数据库插入失败');
            }
        } else {
            return ResponseModel($arrResponse, 500, 'ERROR: 文件上传失败');
        }
    }

    public function deleteAlbumPhoto($id)
    {
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确');
        }
        //查询
        $arrResult = $this->select('SELECT photo_url FROM lanpartyclub_photo WHERE photo_id=' . $id . ' LIMIT 1');
        if (!empty($arrResult)) {
            //插入任务队列
            $sql          = 'INSERT INTO lanpartyclub_task ( task_command,task_parameter,task_status ) VALUES ("DeletePhoto","' . $arrResult[0]['photo_url'] . '", 0 )';
            $this->result = mysqli_query($this->conn, $sql);
        } else {
            return ResponseModel($arrResponse, 204, 'ERROR: 不存在对应数据');
        }
        //删除
        if ($this->delete('DELETE FROM lanpartyclub_photo WHERE photo_id=' . $id) > 0) {
            $arrResponse = ResponseModel($arrResponse, 200, 'Success: 成功');
        } else {
            return ResponseModel($arrResponse, 204, 'ERROR: 不存在对应数据');
        }
        return $arrResponse;
    }

    protected function select_album($id)
    {
        $arrResult = array();
        //品项查询：如果不为空则放入返回数组
        $sql          = 'SELECT album_id AS id, album_name AS title FROM lanpartyclub_album WHERE album_id = ' . $id;
        $this->result = mysqli_query($this->conn, $sql);
        if ($this->result) {
            if (mysqli_num_rows($this->result) > 0) {
                $arrResult = $this->result->fetch_assoc();
            }
        }
        return $arrResult;
    }

    protected function select_album_photo($id)
    {
        $arrResult = array();
        //品项查询：如果不为空则放入返回数组
        $sql          = 'SELECT photo_id AS id, photo_url AS url FROM lanpartyclub_photo WHERE album_id = ' . $id;
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
}
