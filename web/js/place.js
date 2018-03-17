var dtImgSize;
var widthaaa;

$(document).ready(function () {
  // 设置首屏高度
  var height = $(window).height();
  $('.topBox').css('height', height);


  // 每日推荐轮播
  var dailyIndex = 0;
  var dailySize;
  var dailyPicWidth;
  $('.daily-slideBtn .next').on('click', function () {
    dailyNext();
  })
  $('.daily-slideBtn .prev').on('click', function () {
    dailyPrev();
  })

  var ttimer = setInterval(dailyNext, 2000);
  $('.daily-slideBtn').hover(function () {
    clearInterval(ttimer);
  }, function () {
    ttimer = setInterval(dailyNext, 2000);
  })

  function dailyNext() {
    dailyIndex += 1;
    if (dailyIndex == dailySize) {
      $('#dailySlidePic').css({
        'left': 0
      })
      dailyIndex = 1;
    }
    $('#dailySlidePic').stop().animate({
      'left': -dailyIndex * dailyPicWidth + 'px'
    }, 300)
  }

  function dailyPrev() {
    dailyIndex -= 1;
    if (dailyIndex == -1) {
      $('#dailySlidePic').css({
        'left': -((dailySize - 1) * dailyPicWidth) + 'px'
      })
      dailyIndex = dailySize - 2;
    }
    $('#dailySlidePic').stop().animate({
      'left': -dailyIndex * dailyPicWidth + 'px'
    }, 300)
  }
  $('.daily-slideBtn span').each(function () {
    $(this).hover(function () {
      $(this).addClass('active')
    }, function () {
      $(this).removeClass('active')
    })
  })

  // 滚动事件
  $(window).scroll(function () {
    var windowHeight = $(this).height();
    var scrollHeight = $(this).scrollTop();

    // 回到顶部按钮出现
    if (scrollHeight >= 200) {
      $('.stick .backtop').slideDown();
      $('.detailBackTop').fadeIn();
    } else {
      $('.stick .backtop').slideUp();
      $('.detailBackTop').fadeOut();
    }

    // 页面滚动内容飞入
    function flyLeftAnimate(ele, hhh) {
      var eleHeight1 = $(ele).offset().top;
      if (eleHeight1 - scrollHeight < windowHeight - hhh) {
        $(ele).css({
          '-webkit-animation': 'flyLeftAnimate .5s ease-in-out 1 forwards',
          '-moz-animation': 'flyLeftAnimate .5s ease-in-out 1 forwards',
          'animation': 'flyLeftAnimate .5s ease-in-out 1 forwards'
        })
      }
    }

    function flyRightAnimate(ele, hhh) {
      var eleHeight2 = $(ele).offset().top;
      if (eleHeight2 - scrollHeight < windowHeight - hhh) {
        $(ele).css({
          '-webkit-animation': 'flyRightAnimate .5s ease-in-out 1 forwards',
          '-moz-animation': 'flyRightAnimate .5s ease-in-out 1 forwards',
          'animation': 'flyRightAnimate .5s ease-in-out 1 forwards'
        })
      }
    }

    $('.container .flyLeftAnimate').each(function () {
      flyLeftAnimate(this, 30);
    })

    $('.container .flyRightAnimate').each(function () {
      flyRightAnimate(this, 30);
    })

    $('.about-container .flyLeftAnimate').each(function () {
      flyLeftAnimate(this, 20);
    })

    $('.about-container .flyRightAnimate').each(function () {
      flyRightAnimate(this, 20);
    })


    // 设置详情弹出框在当前位置在窗口顶部
    $('.detailCover').css('top', scrollHeight + 'px');

  })

  $('.detailCover').scroll(function () {
    var windowHeight = $(this).height();
    var scrollHeight = $(this).scrollTop();

    // 回到顶部按钮出现
    if (scrollHeight >= 200) {
      $('.detailBackTop').fadeIn();
    } else {
      $('.detailBackTop').fadeOut();
    }
  })

  // 回到顶部
  $('.stick .backtop').on('click', function () {
    $('body,html').animate({
      scrollTop: 0
    }, 300);
    return false;
  })

  $('.detailBackTop').on('click', function () {
    $('.detailCover').animate({
      scrollTop: 0
    }, 300);
    return false;
  })

  // 显示二维码
  var stickH = $('.stick').outerHeight();
  var erweimaH = $('.erweima').outerHeight();
  var erweimaH1 = erweimaH / 2 - stickH / 2;
  $('.erweima').css('bottom', 80 - erweimaH1 + 'px');
  $('.stick .wechat').hover(function () {
    $('.erweima').show();
  }, function () {
    $('.erweima').hide();
  })

  // =================
  // 详情部分
  // =================


  // 每日推荐详情弹出
  $('.daily').on('click', '.detailBtn', function () {
    var itemNum = $(this).attr('id').split('-');
    var itemIdNum = itemNum[1];
    $('.detailCover').stop().animate({
      left: 0
    }, 400).show();
    $('body').css('overflow-y', 'hidden');
    $('#itemDetail').children().remove();
    itemDetails(itemIdNum);
  })

  // 品项列表详情弹出
  $('.container').on('click', '.detailBtn', function () {
    var itemNum = $(this).parent('.product').attr('id').split('-');
    var itemIdNum = itemNum[1];
    $('.detailBackTop').hide();
    $('.detailCover').stop().animate({
      left: 0
    }, 400).show();
    $('body').css('overflow-y', 'hidden');
    $('#itemDetail').children().remove();
    itemDetails(itemIdNum);
  })

  $('#allItems').on('click', '.detailBtnTouch', function () {
    var itemNum = $(this).attr('id').split('-');
    var itemIdNum = itemNum[1];
    $('.detailCover').stop().animate({
      left: 0
    }, 400).show();
    $('body').css('overflow-y', 'hidden');
    $('#itemDetail').children().remove();
    itemDetails(itemIdNum);
  })

  // 设置图片宽度


  // 关闭按钮
  $('.detailCover').on('click', '#closeBtn', function () {
    $(this).parents('.detailCover').stop().animate({
      left: '100%'
    }, 300).hide(300);
    $('body').css('overflow-y', 'auto');
  })

  $('.detailCover').on('click', '.backList', function () {
    $(this).parents('.detailCover').stop().animate({
      left: '100%'
    }, 300).hide(300);
    $('body').css('overflow-y', 'auto');
  })

  // 详情轮播
  var dtIndex = 0;

  $('#itemDetail').on('click', '#picSlideBtnNext', function () {
    dtIndex += 1;
    if (dtIndex == dtImgSize) {
      $('.picSlideBig').css('left', 0);
      dtIndex = 1;
    }
    $('.curNum').text(dtIndex + 1);
    $('.picSlideBig').stop().animate({
      'left': -dtIndex * widthaaa + 'px'
    }, 300);
    if (dtIndex == dtImgSize - 1) {
      $('.curNum').text('1');
    }
  })

  $('#itemDetail').on('click', '#picSlideBtnPrev', function () {
    dtIndex -= 1;
    if (dtIndex == -1) {
      $('.picSlideBig').css('left', -(dtImgSize - 1) * widthaaa);
      dtIndex = dtImgSize - 2;
    }
    $('.curNum').text(dtIndex + 1);
    $('.picSlideBig').stop().animate({
      'left': -dtIndex * widthaaa + 'px'
    }, 300);
  })


  // =================
  // 响应菜单弹出
  // =================
  var height = $(window).height();
  $('.responsiveNav-list').css('height', height);
  var responsiveNav = document.getElementById('responsiveNav');
  responsiveNav.ontouchstart = function () {
    // $('.responsiveNav').on('click', function () {
    if ($('.responsiveNav-list').css('left') != '0px') {
      $('.responsiveNav-list').animate({
        left: 0
      }, 500);
      $('#responsiveNav .line-1').removeClass('hamburgerAa').addClass('hamburgerA');
      $('#responsiveNav .line-2').removeClass('hamburgerBb').addClass('hamburgerB');
      $('#responsiveNav .line-3').removeClass('hamburgerCc').addClass('hamburgerC');
    } else {
      $('.responsiveNav-list').animate({
        left: '-40rem'
      }, 500);
      $('#responsiveNav .line-1').removeClass('hamburgerA').addClass('hamburgerAa');
      $('#responsiveNav .line-2').removeClass('hamburgerB').addClass('hamburgerBb');
      $('#responsiveNav .line-3').removeClass('hamburgerC').addClass('hamburgerCc');
    }
  }


  // =================
  // 页面数据获取部分
  // =================

  // 场地每日推荐
  if (document.getElementById('placeDaily')) {
    $.ajax({
      type: 'GET',
      url: 'http://www.lanpartyclub.com/lanpartyclub/item/get/class/random?id=1',
      dataType: 'JSONP',
      jsonp: 'callback',
      success: function (data) {
        var dailyitem = data.data.item;
        var dailyPhoto = data.data.photo;
        if (dailyPhoto.length == 0) {
          $('#placeDaily').find('#dailySlidePic').append('<li><img src="img/default_img.jpg" alt="默认图片"></li>');
        } else {
          $.each(dailyPhoto, function (i, cur) {
            $('#placeDaily').find('#dailySlidePic').append('<li><img src="http://www.lanpartyclub.com/upload/lanpartyclub/images/album/' + cur.url + '" alt="图片"></li>');
          })
        }
        $('#placeDaily').find('.dailySlidePic-info').attr('id', 'dailyItem-' + dailyitem.id + '').text(dailyitem.title);
        $('#placeDaily').find('.daily-right').append('<h1>' + dailyitem.title + '<br>顶级别墅</h1><div class="dailyStrip"></div><p class="dailyContent introduce">别墅介绍：' + dailyitem.brief + '</p><div class="detailBtn dailyDetailBtn" id="dailyItem-' + dailyitem.id + '">VIEW</div>')
          // 每日推荐轮播
        var clone = $('#dailySlidePic li').first().clone();
        $('#dailySlidePic').append(clone);
        dailySize = $('#dailySlidePic li').size();
        dailyPicWidth = $('.dailyPic li').outerWidth();

        // 设置介绍字段长度 
        var maxTextWidth = 120;
        $('.introduce').each(function () {
          var curTextWidth = $(this).text().length;
          if (curTextWidth > maxTextWidth) {
            $(this).text($(this).text().substring(0, maxTextWidth));
            $(this).html($(this).text() + '...');
          }
        })
      }
    })
  }

  // 餐饮每日推荐
  if (document.getElementById('foodDaily')) {
    $.ajax({
      type: 'GET',
      url: 'http://www.lanpartyclub.com/lanpartyclub/item/get/class/random?id=2',
      dataType: 'JSONP',
      jsonp: 'callback',
      success: function (data) {
        var dailyitem = data.data.item;
        var dailyPhoto = data.data.photo;
        if (dailyPhoto.length == 0) {
          $('#foodDaily').find('#dailySlidePic').append('<li><img src="img/default_img.jpg" alt="默认图片"></li>');
        } else {
          $.each(dailyPhoto, function (i, cur) {
            $('#foodDaily').find('#dailySlidePic').append('<li><img src="http://www.lanpartyclub.com/upload/lanpartyclub/images/album/' + cur.url + '" alt="图片"></li>');
          })
        }
        $('#foodDaily').find('.dailySlidePic-info').attr('id', 'dailyItem-' + dailyitem.id + '').text(dailyitem.title);
        $('#foodDaily').find('.daily-right').append('<h1>' + dailyitem.title + '<br>精品美食</h1><div class="dailyStrip"></div><p class="dailyContent introduce">美食介绍：' + dailyitem.brief + '</p><div class="detailBtn dailyDetailBtn" id="dailyItem-' + dailyitem.id + '">VIEW</div>')
          // 每日推荐轮播
        var clone = $('#dailySlidePic li').first().clone();
        $('#dailySlidePic').append(clone);
        dailySize = $('#dailySlidePic li').size();
        dailyPicWidth = $('.dailyPic li').outerWidth();

        // 设置介绍字段长度 
        var maxTextWidth = 120;
        $('.introduce').each(function () {
          var curTextWidth = $(this).text().length;
          if (curTextWidth > maxTextWidth) {
            $(this).text($(this).text().substring(0, maxTextWidth));
            $(this).html($(this).text() + '...');
          }
        })
      }
    })
  }

  // 增值服务每日推荐
  if (document.getElementById('serviceDaily')) {
    $.ajax({
      type: 'GET',
      url: 'http://www.lanpartyclub.com/lanpartyclub/item/get/class/random?id=3',
      dataType: 'JSONP',
      jsonp: 'callback',
      success: function (data) {
        var dailyitem = data.data.item;
        var dailyPhoto = data.data.photo;
        if (dailyPhoto.length == 0) {
          $('#serviceDaily').find('#dailySlidePic').append('<li><img src="img/default_img.jpg" alt="默认图片"></li>');
        } else {
          $.each(dailyPhoto, function (i, cur) {
            $('#serviceDaily').find('#dailySlidePic').append('<li><img src="http://www.lanpartyclub.com/upload/lanpartyclub/images/album/' + cur.url + '" alt="图片"></li>');
          })
        }
        $('#serviceDaily').find('.dailySlidePic-info').attr('id', 'dailyItem-' + dailyitem.id + '').text(dailyitem.title);
        $('#serviceDaily').find('.daily-right').append('<h1>' + dailyitem.title + '<br>专业服务</h1><div class="dailyStrip"></div><p class="dailyContent introduce">服务介绍：' + dailyitem.brief + '</p><div class="detailBtn dailyDetailBtn" id="dailyItem-' + dailyitem.id + '">VIEW</div>')
          // 每日推荐轮播
        var clone = $('#dailySlidePic li').first().clone();
        $('#dailySlidePic').append(clone);
        dailySize = $('#dailySlidePic li').size();
        dailyPicWidth = $('.dailyPic li').outerWidth();

        // 设置介绍字段长度 
        var maxTextWidth = 120;
        $('.introduce').each(function () {
          var curTextWidth = $(this).text().length;
          if (curTextWidth > maxTextWidth) {
            $(this).text($(this).text().substring(0, maxTextWidth));
            $(this).html($(this).text() + '...');
          }
        })
      }
    })
  }

  // 场地风格列表
  if (document.getElementById('placeList')) {
    $.ajax({
      type: 'GET',
      url: 'http://www.lanpartyclub.com/lanpartyclub/class/get/child?id=1',
      dataType: 'JSONP',
      jsonp: "callback",
      success: function (data) {
        var classTitle = data.data.class;
        $.each(classTitle, function (i, cur) {
          var thisClassIdNum = cur.id;
          $('#placeList').append('<section class="typeList clearfix" id="itemStyle-' + thisClassIdNum + '"><div class="typeList-title flyLeftAnimate"><h2>' + cur.name + '</h2><span><i></i><i></i><i></i></span></div><div class="colList clearfix flyRightAnimate"></div></section>')
          itemLists(thisClassIdNum);
        });
      }
    });
  }

  // 餐饮风格列表
  if (document.getElementById('foodList')) {
    $.ajax({
      type: 'GET',
      url: 'http://www.lanpartyclub.com/lanpartyclub/class/get/child?id=2',
      dataType: 'JSONP',
      jsonp: 'callback',
      success: function (data) {
        var classTitle = data.data.class;
        $.each(classTitle, function (i, cur) {
          var thisClassIdNum = cur.id;
          $('#foodList').append('<section class="typeList clearfix" id="itemStyle-' + thisClassIdNum + '"><div class="typeList-title flyLeftAnimate"><h2>' + cur.name + '</h2><span><i></i><i></i><i></i></span></div><div class="colList clearfix flyRightAnimate"></div></section>')
          itemLists(thisClassIdNum);
        });
      }
    });
  }

  // 增值服务列表
  if (document.getElementById('serviceList')) {
    $.ajax({
      type: 'GET',
      url: 'http://www.lanpartyclub.com/lanpartyclub/class/get/child?id=3',
      dataType: 'JSONP',
      jsonp: "callback",
      success: function (data) {
        var classTitle = data.data.class;
        console.log(classTitle)
        $.each(classTitle, function (i, cur) {
          var thisClassIdNum = cur.id;
          $('#serviceList').append('<section class="typeList clearfix" id="itemStyle-' + thisClassIdNum + '"><div class="typeList-title flyLeftAnimate"><h2>' + cur.name + '</h2><span><i></i><i></i><i></i></span></div><div class="colList clearfix flyRightAnimate"></div></section>')
          itemLists(thisClassIdNum);
        });
      }
    });
  }

})


/*
 *
 *
 *需调用函数部分
 */

// 品项列表
function itemLists(classId) {
  $.ajax({
    type: 'GET',
    url: 'http://www.lanpartyclub.com/lanpartyclub/item/get/class?id=' + classId,
    dataType: 'JSONP',
    jsonp: 'callback',
    success: function (data) {
      if (data.status == 200) {
        var items = data.data.item;
        $.each(items, function (i, cur) {
          var curUrl;
          if (cur.cover == "") {
            curUrl = 'img/default_img2.jpg';
          } else {
            curUrl = 'http://www.lanpartyclub.com/upload/lanpartyclub/images/album/' + cur.cover + '';
          }
          $('#itemStyle-' + classId).find('.typeList-title').siblings('.colList').append('<div class="product left" id="item-' + cur.id + '"><div class="productPic"><img src="' + curUrl + '" alt="图片"></div><div class="productPic-info detailBtnTouch" id="sitem-' + cur.id + '">' + cur.title + '</div><div class="product-content-bg"><div class="product-content"><h3>' + cur.title + '</h3><p class="introduce">' + cur.brief + '</p><span class="productStrip"></span></div></div><div class="detailBtn productBtn">VIEW</div></div>')
            // 设置分类标题高度
          $('.typeList-title').height($('.colList .product').height());

          // 设置介绍字段长度 
          var maxTextWidth = 60;
          $('.introduce').each(function () {
            var curTextWidth = $(this).text().length;
            if (curTextWidth > maxTextWidth) {
              $(this).text($(this).text().substring(0, maxTextWidth));
              $(this).html($(this).text() + '...');
            }
          })
        })
      } else {
        $('#itemStyle-' + classId).remove();
      }
    }
  });
}

// 弹出详情
function itemDetails(itemId) {
  $.ajax({
    type: 'GET',
    url: 'http://www.lanpartyclub.com/lanpartyclub/item/get?id=' + itemId,
    dataType: 'JSONP',
    jsonp: 'callback',
    success: function (data) {
      if (data.status == 200) {
        var item = data.data.item;
        var photo = data.data.photo;
        var detail = data.data.detail;
        $('#itemDetail').append('<h2 id="detailTitle-' + itemId + '">' + item.title + '<span class="closeBtn closeBtnAnimate" id="closeBtn"><<返回</span></h2><div class="detailPic picSlideBox clearfix"><ul class="picSlideBig"></ul><div class="picSlideSmallBox"><span class="curNum"></span>/<span class="totalNum"></span></div><span class="picSlideBtn prev" id="picSlideBtnPrev"></span><span class="picSlideBtn next" id="picSlideBtnNext"></span></div><div class="detailInfo"><h3>详细信息</h3><ul class="detailInfoText"></ul></div>');
        if (photo.length == 0) {
          $('.picSlideBig').append('<li><img src="img/default_img.jpg" alt="详情照片"></li>')
        } else {
          $.each(photo, function (i, cur) {
            $('.picSlideBig').append('<li><img src="http://www.lanpartyclub.com/upload/lanpartyclub/images/album/' + cur.url + '" alt="详情照片"></li>')
          });
        }
        $.each(detail, function (j, ocur) {
          $('.detailInfoText').append('<li class="clearfix"><span>' + ocur.title + '</span><i>' + ocur.content + '</i></li>')
        });


        $('.picSlideSmall li').eq(0).addClass('active').siblings().removeClass('active');
        if ($('.picSlideBig li').size() == 1) {
          $('.picSlideBtn').hide();
          $('.totalNum').text('1');
        } else {
          var dtClone = $('.picSlideBig li').first().clone();
          $('.picSlideBig').append(dtClone);
          dtImgSize = $('.picSlideBig li').size();
          $('.totalNum').text(dtImgSize - 1);
        }
        $('.curNum').text('1');

        widthaaa = $('.picSlideBox').outerWidth();
        $('.picSlideBig li').css('width', widthaaa + 'px');

      } else {
        alert('暂无详情！');
      }
    }
  });
}

//显示加载数据
function ShowDiv() {
  $("#loading").show();
}

//隐藏加载数据
function HiddenDiv() {
  $("#loading").hide();
}