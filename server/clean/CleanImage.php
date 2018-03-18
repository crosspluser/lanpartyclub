<?php
delete_images();
function delete_images()
{
    $conn = new mysqli('127.0.0.1:3306', 'root', 'ckb_sh0135dS@Rn', 'lanpartyclub_database');
    mysqli_options($conn, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
    if (!$conn) {
        echo 'ERROR: 数据库连接错误';
    }
    //获取图片列表
    $sql       = 'SELECT task_id,task_parameter FROM lanpartyclub_task WHERE task_command in ("DeletePhoto","DeleteItemPhoto") AND task_status = 0';
    $result    = $conn->query($sql);
    $arrResult = array();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $arrResult[] = $row;
        }
    } else {
        echo 'ERROR: 数据库没有对应数据';
    }
    if (!empty($arrResult)) {
        //print_r($arrResult);
    } else {
        echo 'ERROR: 数据库没有对应数据';
    }
    //移动图片
    $array_status_1 = [];
    $array_status_2 = [];
    foreach ($arrResult as $photo) {
        print_r($photo);
        print_r('<br />');
        $id   = $photo['task_id'];
        $name = $photo['task_parameter'];
        if (!empty($name)) {
            $status = rename("../../upload/lanpartyclub/images/album/" . $name, "../../recycle/lanpartyclub/images/album/" . $name);
            if ($status) {
                $array_status_1[] = $id;
            } else {
                $array_status_2[] = $id;
            }
        }
    }
    //刷新状态
    print_r('status_1');
    print_r('<br />');
    foreach ($array_status_1 as $status_1) {
        $sql    = 'UPDATE lanpartyclub_task SET task_status = 1 WHERE task_id =' . $status_1;
        $result = mysqli_query($conn, $sql);
        if (mysqli_affected_rows($conn) > 0) {
            print_r($status_1);
            print_r('执行成功');
            print_r('<br />');
            //print_r($arrResult);
        } else {
            print_r($status_1);
            print_r('执行失败');
            print_r('<br />');
        }
    }
    //刷新状态
    print_r('status_2');
    print_r('<br />');
    foreach ($array_status_2 as $status_2) {
        $sql    = 'UPDATE lanpartyclub_task SET task_status = 2 WHERE task_id =' . $status_2;
        $result = mysqli_query($conn, $sql);
        if (mysqli_affected_rows($conn) > 0) {
            print_r($status_2);
            print_r('执行成功');
            print_r('<br />');
        } else {
            print_r($status_2);
            print_r('执行失败');
            print_r('<br />');
        }
    }
    //rename("cs/temporaryPicture/".$picturename[i],"cs/picture/".$picturename[i]);
    //
}
