<?php
//类别查询
class ItemClass
{

    protected $conn;
    protected $result;

    public function __construct()
    {
        //数据库初始化
        $this->conn = new mysqli('139.224.72.243:13306', 'root', 'Ecs1014lPx@riXi_aly', 'lanpartyclub_database');
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
        if ($this->result) {
            mysqli_free_result($this->result);
        }
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function getClass($id)
    {
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 1400, 'ERROR: 请求参数不正确，请检查 id 的参数格式');
        }
        //类别查询：获得类数组，如果不为空则放入返回结果
        $sql          = 'SELECT class_id AS id,class_name AS name,class_pid AS pid FROM lanpartyclub_class WHERE class_id =' . $id;
        $this->result = mysqli_query($this->conn, $sql);
        if ($this->result) {
            if (mysqli_num_rows($this->result) > 0) {
                $row                  = $this->result->fetch_assoc();
                $arrResponse['class'] = $row;
                //输出
                return ResponseModel($arrResponse, 200, 'Success: 成功');
            } else {
                //输出
                return ResponseModel($arrResponse, 2204, 'ERROR: id = ' . $id . ' 不存在对应数据');
            }
        } else {
            //输出
            return ResponseModel($arrResponse, 3500, 'ERROR: 操作数据库失败，请检查服务器');
        }
        //默认输出
        return $arrResponse;
    }

    public function getClassChild($id)
    {
        $arrResponse = array();
        if (!preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 1400, 'ERROR: 请求参数不正确，请检查 id 的参数格式');
        }
        //类别查询：获得类数组，如果不为空则放入返回结果
        $sql          = 'SELECT class_id AS id,class_name AS name,class_pid AS pid FROM lanpartyclub_class WHERE class_pid =' . $id;
        $this->result = mysqli_query($this->conn, $sql);
        if ($this->result) {
            if (mysqli_num_rows($this->result) > 0) {
                while ($row = $this->result->fetch_assoc()) {
                    $arrResponse['class'][] = $row;
                }
                //输出
                return ResponseModel($arrResponse, 200, 'Success: 成功');
            } else {
                //输出
                return ResponseModel($arrResponse, 2204, 'ERROR: 不存在对应数据，请检查参数 id = ' . $id);
            }
        } else {
            //输出
            return ResponseModel($arrResponse, 3500, 'ERROR: 操作数据库失败，请检查服务器');
        }
        //默认输出
        return $arrResponse;
    }

    public function getClassAll()
    {
        $arrResponse = array();
        //类别查询：获得类数组，如果不为空则放入返回结果
        $sql          = 'SELECT class_id AS id,class_name AS name,class_pid AS pid FROM lanpartyclub_class';
        $this->result = mysqli_query($this->conn, $sql);
        if ($this->result) {
            if (mysqli_num_rows($this->result) > 0) {
                while ($row = $this->result->fetch_assoc()) {
                    $arrResponse['class'][] = $row;
                }
                //输出
                return ResponseModel($arrResponse, 200, 'Success: 成功');
            } else {
                //输出
                return ResponseModel($arrResponse, 2204, 'ERROR: 不存在对应数据，请检查参数 id = ' . $id);
            }
        } else {
            //输出
            return ResponseModel($arrResponse, 3500, 'ERROR: 操作数据库失败，请检查服务器');
        }
        //默认输出
        return $arrResponse;
    }

    public function postClass($name, $pid)
    {
        //参数设置
        $arrResponse = array();
        //参数检测
        if (!preg_match('/^[1-3]$/', $pid)) {
            return ResponseModel($arrResponse, 1400, 'ERROR: 请求参数不正确，请检查参数 pid = ' . $pid);
        }
        $name = dowith_sql($name);
        if (empty($name)) {
            return ResponseModel($arrResponse, 1400, 'ERROR: 请求参数不正确，请检查参数 name = ' . $name);
        }
        //数据库设置
        if (!$this->conn) {
            return ResponseModel($arrResponse, 3500, 'ERROR: 数据库连接错误，请检查网络');
        }
        //新增
        $sql    = 'INSERT INTO lanpartyclub_class ( class_name , class_pid ) VALUES ("' . $name . '","' . $pid . '")';
        $result = mysqli_query($this->conn, $sql);
        //输出
        if ($result) {
            if (mysqli_affected_rows($this->conn) > 0) {
                return ResponseModel($arrResponse, 200, 'Success: 成功');
            } else {
                return ResponseModel($arrResponse, 1400, 'ERROR: 请求参数不正确，请检查参数 name = ' . $name);
            }
        } else {
            return ResponseModel($arrResponse, 1400, 'ERROR: 请求参数不正确，请检查参数 name = ' . $name);
        }
        return $arrResponse;
    }

    public function putClass($id, $name, $pid)
    {
        //参数设置
        $arrResponse = array();
        //参数检测
        if (!preg_match('/^[1-3]$/', $pid)) {
            return ResponseModel($arrResponse, 1400, 'ERROR: 请求参数不正确，请检查参数 pid = ' . $pid);
        }
        if (preg_match('/^[1-3]$/', $id) || !preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 1400, 'ERROR: 请求参数不正确，请检查参数 id = ' . $id);
        }
        $name = dowith_sql($name);
        if (empty($name)) {
            return ResponseModel($arrResponse, 1400, 'ERROR: 请求参数不正确，请检查参数 name = ' . $name);
        }
        //修改
        $sql    = 'UPDATE lanpartyclub_class SET class_name ="' . $name . '", class_pid="' . $pid . '" WHERE class_id="' . $id . '"';
        $result = mysqli_query($this->conn, $sql);
        //输出
        if ($result) {
            if (mysqli_affected_rows($this->conn) > 0) {
                $arrResponse = ResponseModel($arrResponse, 200, 'Success: 成功');
            } else {
                return ResponseModel($arrResponse, 2204, 'ERROR: 不存在对应数据 或 数据无变化，请检查参数 id = ' . $id . ' 或 name = ' . $name);
            }
            //print_r($arrResult);
        } else {
            return ResponseModel($arrResponse, 2204, 'ERROR: 不存在对应数据 或 数据无变化，请检查参数 id = ' . $id . ' 或 name = ' . $name);
        }
        return $arrResponse;
    }

    public function deleteClass($id)
    {
        //参数设置
        $arrResponse = array();
        if (preg_match('/^[1-3]$/', $id) || !preg_match('/^[0-9]{1,8}$/', $id)) {
            return ResponseModel($arrResponse, 400, 'ERROR: 请求参数不正确，请检查参数 id = ' . $id);
        }
        //修改
        $sql    = 'DELETE FROM lanpartyclub_class WHERE class_id=' . $id;
        $result = mysqli_query($this->conn, $sql);
        //输出
        if ($result) {
            if (mysqli_affected_rows($this->conn) > 0) {
                $arrResponse = ResponseModel($arrResponse, 200, 'Success: 成功');
            } else {
                return ResponseModel($arrResponse, 2204, 'ERROR: 不存在对应数据，请检查参数 id = ' . $id);
            }
        } else {
            return ResponseModel($arrResponse, 2204, 'ERROR: 不存在对应数据，请检查参数 id = ' . $id);
        }
        return $arrResponse;
    }
}
