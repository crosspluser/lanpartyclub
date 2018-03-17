$(function () {

  // tab标签
  $('#adminAll').on('click', '.myTab', function (e) {
    e.preventDefault()
    $(this).tab('show');
    var hrefName = $(this).attr('href');
    $('#myTab a').each(function () {
      if (hrefName == $(this).attr('href')) {
        $(this).parent().addClass('active').siblings().removeClass('active');
      }
    })
  })


  // =================================
  // 品项管理
  // =================================

  // 修改品项
  var thisItemId;
  var thisItemTitle;
  var thisItemPclassId;
  var thisItemclassId;
  var thisDetailId;
  $('#pxgl-lists').on('click', '.PXchangeInfo', function () {
    $('#styleLists2').empty();
    $('#xgpx-info-list tbody').empty();
    $('#xgpx-imgPreview').empty();
    thisItemId = $(this).parent().siblings('.thisItemId').text();
    $.ajax({
      type: 'GET',
      url: 'http://www.lanpartyclub.com/lanpartyclub/item/get?id=' + thisItemId,
      dataType: 'JSONP',
      jsonp: 'callback',
      success: function (data) {
        var datas = data.data;
        thisItemPclassId = datas.pclass.id;
        thisItemclassId = datas.class.id;
        if (data.status == 200) {
          $('#xgpxLevelOneTitle').val('' + datas.pclass.name + '');
          $.ajax({
            type: 'GET',
            url: 'http://www.lanpartyclub.com/lanpartyclub/class/get/child?id=' + thisItemPclassId,
            dataType: 'JSONP',
            jsonp: 'callback',
            success: function (data) {
              var iclass = data.data.class;
              $.each(iclass, function (i, cur) {
                $('#styleLists2').append('<option value="' + cur.id + '">' + cur.name + '</option>');
                $('#styleLists2 option[value="' + thisItemclassId + '"]').attr('selected', true);
              });
            }
          })
          $('#itemTitle').val('' + datas.item.title + '');
          $('#itemTitle').attr('data-thisItemId', thisItemId);
          $('#itemBrief').text(datas.item.brief);
          if (datas.detail == undefined || datas.detail.length == 0) {
            $('#xgpx-info-list tbody').append('<tr><td colspan="4" class="text-center text-danger">暂无数据!</td></tr>')
          } else {
            $.each(datas.detail, function (i, cur) {
              thisDetailId = cur.id;
              $('#xgpx-info-list tbody').append('<tr>' +
                '<td><input type="checkbox" class="checkboxStyle" name="xgpx-info-list"></td>' +
                '<td data-cid="' + thisDetailId + '" class="thisDetailTitle"><input type="text" class="form-control detailsTitle" value="' + cur.title + '"></td>' +
                '<td><input type="text" class="form-control detailsInfo" value="' + cur.content + '"></td>' +
                '<td><button type="button" class="btn btn-warning xgpx-info-delete1">删除</button></td>' +
                '</tr>')
            })
          }
          if (datas.photo == undefined || datas.photo.length == 0) {
            $('#xgpx-imgPreview').append('<li class="text-center text-danger">暂无照片！</li>')
          } else {
            $.each(datas.photo, function (i, cur) {
              $('#xgpx-imgPreview').append('<li class="col-md-3"><img src="http://www.lanpartyclub.com/upload/lanpartyclub/images/album/' + cur.url + '" alt="活动照片" name="' + cur.id + '" ><button type="button" class="btn btn-warning btn-sm deleteImg">删除</button></li>')
            })
          }
        } else {
          alert('数据获取失败，请刷新页面');
        }
      }
    });
  })

  // 删除品项
  $('#pxgl-lists').on('click', '.PXdelete', function () {
    var that = $(this);
    thisItemId = that.parent().siblings('.thisItemId').text();
    thisItemTitle = that.parent().siblings('.thisItemTitle').text();
    $('#deleteConfirm .modal-body').html('您确认删除 <span class="text-danger "><b>' + thisItemTitle + '</b></span> 吗？');
    $('#deleteConfirm .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">返回</button><button type="button" class="btn btn-primary" id="delItemConfirmBtn-' + thisItemId + '" data-dismiss="modal">确认</button>');
    $('#deleteConfirm').on('click', '#delItemConfirmBtn-' + thisItemId + '', function () {
      $.ajax({
        type: 'POST',
        url: 'http://www.lanpartyclub.com/lanpartyclub/item/delete?id=' + thisItemId,
        dataType: 'JSONP',
        jsonp: 'callback',
        success: function (response) {
          if (response.status == 200) {
            that.parents('tr').remove();
          } else {
            console.log(response);
          }
        },
        error: function (response) {
          console.log(response);
        }
      })
    })
  })

  // =================================
  // 发布品项
  // =================================

  // 品项发布-详情新增一条信息
  $('#fbpx-info-add').on('click', function () {
    $('#fbpx-info-list tbody').append('<tr><td><input type="checkbox" class="checkboxStyle" name="fbpx-info-list"></td><td><input type="text" class="form-control detailsTitle"></td><td><input type="text" class="form-control detailsInfo"></td><td><button type="button" class="btn btn-warning fbpx-info-delete">删除</button></td></tr>')
  })

  // 品项发布-详情删除一条信息
  $('#fbpx-info-list tbody').on('click', 'button.fbpx-info-delete', function () {
    $(this).parents('tr').remove();
  })

  // 品项发布-详情删除选中
  $('#fbpx-info-delete-all').on('click', function () {
    $('#fbpx-info-list input[type="checkbox"]:checked').each(function () {
      $(this).parents('tr').remove();
    })
  })


  // =================================
  // 修改品项
  // =================================

  // 修改品项-详情新增一条信息
  $('#xgpx-info-add').on('click', function () {
    $('#xgpx-info-list1 tbody').append('<tr><td><input type="checkbox" class="checkboxStyle" name="xgpx-info-list"></td><td><input type="text" class="form-control detailsTitle"></td><td><input type="text" class="form-control detailsInfo"></td><td><button type="button" class="btn btn-warning xgpx-info-delete">删除</button></td></tr>')
  })

  // 修改品项-详情删除一条信息
  $('#xgpx-info-list tbody').on('click', '.xgpx-info-delete1', function () {
    var that = $(this);
    var id = that.parent().siblings('.thisDetailTitle').attr('data-cid');
    deleteDetail(id, that)
  })
  $('#xgpx-info-list1 tbody').on('click', '.xgpx-info-delete', function () {
    $(this).parents('tr').remove();
  })

  // 修改品项-详情删除选中
  $('#xgpx-info-delete-all').on('click', function () {
    $('#xgpx-info-list1 input[type="checkbox"]:checked').each(function () {
      $(this).parents('tr').remove();
    })
  })

  // 修改品项-删除已有图片
  $('#xgpx-imgPreview').on('click', '.deleteImg', function () {
    that = $(this);
    var id = that.siblings('img').attr('name');
    deleteImg(id, 'item', that);
  })


  // =================================
  // 照片管理
  // =================================

  // 修改相册照片
  var thisAlbumId;
  var thisAlbumTitle;
  var thisAlbumPhotoId;
  $('#zpqLists tbody').on('click', '.changealbum', function () {
    $('#xgzp-imgPreview').empty();
    thisAlbumId = $(this).parent().siblings('.thisAlbumId').text();
    $.ajax({
      type: 'GET',
      url: 'http://www.lanpartyclub.com/lanpartyclub/album/get?id=' + thisAlbumId,
      dataType: 'JSONP',
      jsonp: 'callback',
      success: function (data) {
        if (data.status == 200) {
          var datas = data.data;
          $('#albumTitle').val('' + datas.album.title + '');
          if (datas.photo == undefined || datas.photo.length == 0) {
            $('#xgzp-imgPreview').append('<li class="text-center text-danger">暂无照片！</li>')
          } else {
            $.each(datas.photo, function (i, cur) {
              thisAlbumPhotoId = cur.id;
              $('#xgzp-imgPreview').append('<li class="col-md-3"><img src="http://www.lanpartyclub.com/upload/lanpartyclub/images/album/' + cur.url + '" alt="活动照片" name="' + thisAlbumPhotoId + '"><button type="button" class="btn btn-warning btn-sm deleteImg">删除</button></li>')
            })
          }
        } else {
          $('#xgzp-imgPreview').append('<li class="text-center text-danger">暂无照片！</li>')
        }
      }
    });
  })

  // 删除相册
  $('#zpqLists tbody').on('click', '.deletealbum', function () {
    var that = $(this);
    thisAlbumId = that.parent().siblings('.thisAlbumId').text();
    thisAlbumTitle = that.parent().siblings('.thisAlbumTitle').text();
    $('#deleteConfirm .modal-body').html('您确认删除 <span class="text-danger "><b>' + thisAlbumTitle + '</b></span> 吗？');
    $('#deleteConfirm .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">返回</button><button type="button" class="btn btn-primary" id="delAlbumConfirmBtn-' + thisAlbumId + '" data-dismiss="modal">确认</button>');
    $('#deleteConfirm').on('click', '#delAlbumConfirmBtn-' + thisAlbumId + '', function () {
      $.ajax({
        type: 'POST',
        url: 'http://www.lanpartyclub.com/lanpartyclub/album/delete?id=' + thisAlbumId,
        dataType: 'JSONP',
        jsonp: 'callback',
        success: function (response) {
          if (response.status == 200) {
            that.parents('tr').remove();
          } else {
            console.log(response);
          }
        },
        error: function (response) {
          console.log(response);
        }
      })
    })
  })

  // 删除照片-删除已有图片
  $('#xgzp-imgPreview').on('click', '.deleteImg', function () {
    that = $(this);
    var id = that.siblings('img').attr('name');
    deleteImg(id, 'album', that);
  })


  // =================================
  // 上传文件部分
  // =================================  

  // 上传文件
  // upLoadImg($("#file-1"), 'item', 'post', fbpxId);
  // upLoadImg($("#file-2"), 'item', 'post', 10);
  // upLoadImg($("#file-3"), 'album', 'post', newXCid);
  // upLoadImg($("#file-4"), 'album', 'post', xgxcId);

  // 上传文件
  upLoadImg($("#file-1"));
  upLoadImg($("#file-2"));
  upLoadImg($("#file-3"));
  upLoadImg($("#file-4"));  
  // 上传文件监听
  fileOnChange($("#file-1"));
  fileOnChange($("#file-2"));
  fileOnChange($("#file-3"));
  fileOnChange($("#file-4"));


  // =================================
  // 数据获取部分
  // =================================

  // 管理品项--载入时获取--分类2
  var para = $('#styleLists');
  getLevelTwo(1, para);

  // 管理品项--载入时获取--列表
  getItemLists(1);

  // 管理品项--分类1改变时获取分类2
  var nummm;
  $('#lanClass').on('change', function () {
    nummm = $('#lanClass option:selected').attr('value')
    console.log('1:' + nummm)
    var leveloneCurVal = $('#lanClass option:selected').attr('value');
    $('#styleLists option[value="0"]').nextAll().remove();
    $('#pxgl-lists tbody').empty();
    // 获取分类2
    // var para = $('#styleLists');
    getLevelTwo(leveloneCurVal, para);
    // 获取新列表
    getItemLists(leveloneCurVal);
  })

  // 管理品项--风格选择改变时列表拉取
  $('#styleLists').on('change', function () {
    var leveltwoCurVal = $('#styleLists option:selected').attr('value');
    if (leveltwoCurVal == 0) {
      leveltwoCurVal = $('#lanClass option:selected').attr('value');
    }
    $('#pxgl-lists tbody').empty();
    // 根据风格获取列表
    getItemLists(leveltwoCurVal)
  })

  // 发布品项--载入时获取分类
  $('#fbpxTab').on('click', function () {
    console.log('2:' + nummm)
    var opts = $('#lanClass1 option');
    $.each(opts, function (i, cur) {
      // console.log($(cur).attr('value'))
      if ($(cur).attr('value') === nummm) {
        $(cur).attr('selected', 'selected').siblings().removeAttr('selected');
      }
    })
    var leveloneCurVal = $('#lanClass1 option:selected').attr('value');
    classifyMenu(leveloneCurVal);
  })

  // 发布品项--载入时获取分类
  $('#addNewPX').on('click', function () {
    console.log('3:' + nummm)
    var opts = $('#lanClass1 option');
    $.each(opts, function (i, cur) {
      if ($(cur).attr('value') === nummm) {
        $(cur).attr('selected', 'selected').siblings().removeAttr('selected');
      }
    })
    var leveloneCurVal = $('#lanClass1 option:selected').attr('value');
    classifyMenu(leveloneCurVal);
  })

  // 发布品项--分类1改变时获取分类2
  $('#lanClass1').on('change', function () {
    var leveloneCurVal = $('#lanClass1 option:selected').attr('value');
    classifyMenu(leveloneCurVal);
  })

  // 提交发布品项
  $('#fbpxBtn').on('click', function () {
    // 判断品项标题
    if ($('#itemTitle1').val().length <= 0) {
      alert('品项名不能为空，请修改！')
      return false;
    }
    if ($('#itemTitle1').val().length > 50) {
      alert('品项名过长，请修改！')
      return false;
    }
    // 判断品项简介
    if ($('#itemBrief1').val().length <= 0) {
      alert('品项简介不能为空，请修改！')
      return false;
    }
    if ($('#itemBrief1').val().length > 200) {
      alert('品项简介过长，请修改！')
      return false;
    }
    // 照片判断
    if (pxPhoto == undefined) {
      alert('请上传照片！');
      return false;
    }
    var itemInfo = $('#fbpx-title').serialize();
    $.ajax({
      type: 'POST',
      url: 'http://www.lanpartyclub.com/lanpartyclub/item/post?' + itemInfo,
      dataType: 'JSONP',
      jsonp: 'callback',
      beforeSend: function () {
        $('#fbpxBtn').html('<span class="sendLoading"></span>');
      },
      success: function (response) {
        if (response.status == 200) {
          var id = response.data.id;
          fbpxDetails(id, 'post', 'fbpx-info-list');
          fbpxPhotos(id, 'item', 'post');
          alert('品项发布成功！');
          window.location.reload();
        } else {
          alert('品项名重复，请修改品项名！');
          $('#fbpxBtn').html('<span class="glyphicon glyphicon-ok"></span> 确认提交品项 ');
        }
      },
      error: function () {
        alert('页面出错了，请尝试刷新！');
        $('#fbpxBtn').html('<span class="glyphicon glyphicon-ok"></span> 确认提交品项 ');
      }
    })
  })

  // 提交修改品项
  $('#xgpxBtn').on('click', function () {
    // 判断品项标题
    if ($('#itemTitle').val().length <= 0) {
      alert('品项名不能为空，请修改！')
      return false;
    }
    if ($('#itemTitle').val().length > 50) {
      alert('品项名过长，请修改！')
      return false;
    }
    // 判断品项简介
    if ($('#itemBrief').val().length <= 0) {
      alert('品项简介不能为空，请修改！')
      return false;
    }
    if ($('#itemBrief').val().length > 200) {
      alert('品项简介过长，请修改！')
      return false;
    }
    var itemInfo = $('#xgpx-title').serialize();
    var id = thisItemId;
    updateSome(id, 'item', itemInfo);
    xgpxDetails('put', 'xgpx-info-list');
    fbpxDetails(id, 'post', 'xgpx-info-list1');
    fbpxPhotos(id, 'item', 'post');
    alert('品项修改成功！');
    window.location.reload();

  })
  
  // 照片墙管理--拉取列表
  $('#zpqTab').on('click', function () {
    $('#zpqLists tbody').empty();
    getAlbumLists();
  })

  // 提交新相册
  $('#fbzpBtn').on('click', function () {
    // 判断品项标题
    if ($('#albumTitle1').val().length <= 0) {
      alert('相册名不能为空，请修改！')
      return false;
    }
    if ($('#albumTitle1').val().length > 15) {
      alert('相册名过长，请修改！')
      return false;
    }
    if (pxPhoto == undefined) {
      alert('请上传照片！');
      return false;
    }
    var albumInfo = $('#fbzp-title').serialize();
    $.ajax({
      type: 'POST',
      url: 'http://www.lanpartyclub.com/lanpartyclub/album/post?' + albumInfo,
      dataType: 'JSONP',
      jsonp: 'callback',
      beforeSend: function () {
        $('#fbzpBtn').html('<span class="sendLoading"></span>');
      },
      success: function (response) {
        console.log(response);
        if (response.status == 200) {
          var id = response.data.id;
          fbpxPhotos(id, 'album', 'post');
          alert('相册发布成功！');
          window.location.reload();
        } else {
          alert('相册重名，请修改相册名！');
          $('#fbzpBtn').html('<span class="glyphicon glyphicon-ok"></span> 确认提交照片 ');
        }
      },
      error: function () {
        alert('页面出错了，请尝试刷新！');
        $('#fbzpBtn').html('<span class="glyphicon glyphicon-ok"></span> 确认提交照片 ');
      }
    })
  })

  // 提交修改相册
  $('#xgzpBtn').on('click', function () {
    // 判断品项标题
    if ($('#albumTitle').val().length <= 0) {
      alert('相册名不能为空，请修改！')
      return false;
    }
    if ($('#albumTitle').val().length > 15) {
      alert('相册名过长，请修改！')
      return false;
    }
    var albumInfo = $('#xgzp-title').serialize();
    var id = thisAlbumId;
    updateSome(id, 'album', albumInfo);
    fbpxPhotos(id, 'album', 'post');
    alert('相册修改成功！');
    window.location.reload();
  })

  // 删除cookie
  $('#outPage').on('click', function () {
    DeleteCookie('account_lpc');
    window.location.href = 'index.html';
  })



});

// 上传文件监听赋值变量
var pxPhoto;


/*
 *
 * 
 * 函数调用部分
 */

// 分类2获取
function getLevelTwo(id, ele) {
  $.ajax({
    type: 'GET',
    url: 'http://www.lanpartyclub.com/lanpartyclub/class/get/child?id=' + id,
    dataType: 'JSONP',
    jsonp: 'callback',
    success: function (data) {
      var iclass = data.data.class;
      $.each(iclass, function (i, cur) {
        ele.append('<option value="' + cur.id + '">' + cur.name + '</option>');
      });
    }
  })
}

// 管理品项获取列表
function getItemLists(url) {
  $.ajax({
    type: 'GET',
    url: 'http://www.lanpartyclub.com/lanpartyclub/item/get/class?id=' + url,
    dataType: 'JSONP',
    jsonp: 'callback',
    success: function (data) {
      if (data.status == 200) {
        var item = data.data.item;
        var q = 0;
        $.each(item, function (i, cur) {
          q++;
          $('#pxgl-lists tbody').append('<tr>' +
            '<td class="text-primary">' + q + '</td>' +
            '<td class="thisItemId">' + cur.id + '</td>' +
            '<td>' + cur.pclass.name + '</td>' +
            '<td>' + cur.class.name + '</td>' +
            '<td class="thisItemTitle">' + cur.title + '</td>' +
            '<td><button type="button" class="btn btn-sm btn-warning PXdelete" data-toggle="modal" data-target="#deleteConfirm">删除</button><button type="button" class="btn btn-sm btn-primary myTab ml10 PXchangeInfo" href="#xgpx">修改</button></td>' +
            '</tr>')
        })
      } else {
        $('#pxgl-lists tbody').append('<tr><td colspan="6" class="text-center text-danger">该分类暂无数据!</td></tr>')
      }
    }
  })
}

// 发布品项--分类菜单获取
function classifyMenu(url) {
  var para = $('#styleLists1');
  para.empty();
  getLevelTwo(url, para);
}

// 发布品项--详细信息
function fbpxDetails(id, url, p) {
  var detailsTitle = [];
  var detailsInfo = [];
  // 详情标题
  $('#' + p + ' .detailsTitle').each(function () {
      detailsTitle.push($(this).val());
    })
    // 详情内容
  $('#' + p + ' .detailsInfo').each(function () {
    detailsInfo.push($(this).val());
  })

  $.each(detailsTitle, function (i, cur) {
    $.ajax({
      type: 'GET',
      url: '/lanpartyclub/item/detail/' + url,
      data: 'id=' + id + '&title=' + detailsTitle[i] + '&content=' + detailsInfo[i],
      async: false,
      success: function (response) {
        console.log('成功');
      },
      error: function (response) {
        console.log(response);
      }
    })
  })
}

// 修改品项--详细信息
function xgpxDetails(url, p) {
  var detailsTitle = [];
  var detailsInfo = [];
  var curDetailId = [];
  // 详情标题
  $('#' + p + ' .detailsTitle').each(function () {
      detailsTitle.push($(this).val());
    })
    // 详情内容
  $('#' + p + ' .detailsInfo').each(function () {
      detailsInfo.push($(this).val());
    })
    // 当前详情ID
  $('#' + p + ' .thisDetailTitle').each(function () {
    curDetailId.push($(this).attr('data-cid'));
  })

  $.each(detailsTitle, function (i, cur) {
    $.ajax({
      type: 'GET',
      url: '/lanpartyclub/item/detail/' + url,
      data: 'id=' + curDetailId[i] + '&title=' + detailsTitle[i] + '&content=' + detailsInfo[i],
      async: false,
      success: function (response) {
        console.log('成功');
      },
      error: function (response) {
        console.log(response);
      }
    })
  })
}

// 发布品项--照片
function fbpxPhotos(id, urlA, urlB) {
  if (pxPhoto == undefined) return false;
  if (pxPhoto.length == 0) return false;
  $.each(pxPhoto, function (i, cur) {
    var iformData = new FormData();
    iformData.append('id', id)
    iformData.append('file', pxPhoto[i])
    $.ajax({
      type: 'POST',
      url: '/lanpartyclub/' + urlA + '/photo/' + urlB + '',
      // dataType: 'JSON',
      data: iformData,
      processData: false,
      contentType: false,
      async: false,
      success: function (response) {
        console.log('成功');
      },
      error: function (response) {
        console.log(response);
        alert('页面出错了，请尝试刷新！');
      }
    })
  })
}

// 数据修改
function updateSome(id, url, odata) {
  $.ajax({
    type: 'GET',
    url: '/lanpartyclub/' + url + '/put',
    // dataType: 'JSON',
    data: 'id=' + id + '&' + odata,
    async: false,
    success: function (response) {
      console.log(response.status);
      if (response.status == 200 || response.status == 204) {
        console.log('成功');
      } else {
        alert('品项名与其他品项重复，请修改！')
      }
    },
    error: function (response) {
      alert('页面出错了，请尝试刷新！')
    }
  })
}

// 获取相册列表
function getAlbumLists() {
  $.ajax({
    type: 'GET',
    url: 'http://www.lanpartyclub.com/lanpartyclub/album/get/all',
    dataType: 'JSONP',
    jsonp: 'callback',
    success: function (data) {
      var album = data.data.album;
      var q = 0;
      $.each(album, function (i, cur) {
        var thistitle;
        if (cur.cover == "") {
          thistitle = cur.title + ' <span class="text-danger"> ( 该相册无照片请检查！ ) </span>';
        } else {
          thistitle = cur.title;
        }
        q++;
        $('#zpqLists tbody').append('<tr>' +
          '<td>' + q + '</td>' +
          '<td class="thisAlbumId">' + cur.id + '</td>' +
          '<td class="thisAlbumTitle">' + thistitle + '</td>' +
          '<td>' +
          '<button type="button" class="btn btn-sm btn-warning deletealbum" data-toggle="modal" data-target="#deleteConfirm">删除</button><button type="button" class="btn btn-sm btn-primary myTab ml10 changealbum" href="#xgzp">修改</button>' +
          '</td></tr>')
      })
    }
  })
}

// 删除图片
function deleteImg(id, url, odom) {
  $.ajax({
    type: 'POST',
    url: 'http://www.lanpartyclub.com/lanpartyclub/' + url + '/photo/delete?id=' + id,
    dataType: 'JSONP',
    jsonp: 'callback',
    success: function (response) {
      if (response.status == 200) {
        odom.parent().remove();
      } else {
        console.log(response);
      }
    },
    error: function (response) {
      console.log(response);
    }
  })
}

// 删除详情
function deleteDetail(id, odom) {
  $.ajax({
    type: 'POST',
    url: 'http://www.lanpartyclub.com/lanpartyclub/item/detail/delete?id=' + id,
    dataType: 'JSONP',
    jsonp: 'callback',
    success: function (response) {
      if (response.status == 200) {
        odom.parents('tr').remove();
      } else {
        console.log(response);
      }
    },
    error: function (response) {
      console.log(response);
    }
  })
}

// 上传图片
function upLoadImg(d) {
  d.fileinput({
    uploadUrl: '#',
    allowedFileExtensions: ['jpg', 'png', 'gif'],
    overwriteInitial: false,
    maxFileSize: 4500,
    maxFilesNum: 15,
    showUpload: false,
    slugCallback: function (filename) {
      return filename.replace('(', '_').replace(']', '_');
    }
  });
}

// 上传图片
// function upLoadImg(d, urlA, urlB) {
//   var id = $('#itemTitle').attr('data-thisItemId');
//   d.fileinput({
//     uploadUrl: '/lanpartyclub/' + urlA + '/photo/' + urlB,
//     uploadExtraData: {
//       id: id
//     },
//     allowedFileExtensions: ['jpg', 'png', 'gif'],
//     overwriteInitial: false,
//     maxFileSize: 4500,
//     maxFilesNum: 20,
//     showUpload: false,
//     slugCallback: function (filename) {
//       return filename.replace('(', '_').replace(']', '_');
//     }
//   })
// }


// 上传文件监听
function fileOnChange(z) {
  z.on('change', function (e) {
   // $('.btn-file').html('<i class="glyphicon glyphicon-folder-open"></i> &nbsp;重新选择 … <input id="'+ id +'" type="file" multiple="" class="file">')
    $('.file-preview-thumbnails').empty();
    pxPhoto = e.currentTarget.files;
  });
}

function DeleteCookie(name) {
  var exp = new Date();
  exp.setTime(exp.getTime() - 100);
  var cval = GetCookie(name);
  window.document.cookie = name + "=" + cval + "; expires=" + exp.toGMTString() + ";path=/";
}