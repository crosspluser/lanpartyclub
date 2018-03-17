$(document).ready(function () {

  var windowWidth = $(window).width();

  // =============
  // 首页轮播
  // =============
  var curIndex = 1;
  var timer;
  // 初始状态
  $('#slideAd li').eq(0).show();
  $('#slidePoint li').eq(0).addClass('active');

  // 按钮切换 
  $('#nextBtn').on('click', function () {
    if (curIndex == 3) {
      curIndex = 1;
    } else {
      curIndex++;
    }
    $('#slideAd li').eq(curIndex - 1).stop().fadeIn(800).siblings().stop().fadeOut(800);
    $('#slidePoint li').eq(curIndex - 1).addClass('active').siblings().removeClass('active');
  })

  $('#prevBtn').on('click', function () {
    if (curIndex == 1) {
      curIndex = 3;
    } else {
      curIndex--;
    }
    $('#slideAd li').eq(curIndex - 1).stop().fadeIn(800).siblings().stop().fadeOut(800);
    $('#slidePoint li').eq(curIndex - 1).addClass('active').siblings().removeClass('active');
  })

  $('#slidePoint li').each(function () {
    $(this).on('click', function () {
      var myIndex = $(this).index();
      curIndex = myIndex + 1;
      $('#slideAd li').eq(myIndex).stop().fadeIn(800).siblings().stop().fadeOut(800);
      $(this).addClass('active').siblings().removeClass('active');
    })
  })

  timer = setInterval(autoGo, 4000);
  $('.slideBtn').hover(function () {
    clearInterval(timer);
  }, function () {
    timer = setInterval(autoGo, 4000);
  })

  // 自动播放
  function autoGo() {
    if (curIndex == 3) {
      curIndex = 1;
    } else {
      curIndex++;
    }
    $('#slideAd li').eq(curIndex - 1).stop().fadeIn(800).siblings().stop().fadeOut(800);
    $('#slidePoint li').eq(curIndex - 1).addClass('active').siblings().removeClass('active');
  }

  // ============================
  // 显示二维码
  // ============================
  var stickH = $('.stick').outerHeight();
  var erweimaH = $('.erweima').outerHeight();
  var erweimaH1 = erweimaH / 2 - stickH / 2;
  $('.erweima').css('bottom', 80 - erweimaH1 + 'px');
  $('.stick .wechat').hover(function () {
    $('.erweima').show();
  }, function () {
    $('.erweima').fadeOut(150);
  })


  // =================
  // 响应菜单弹出
  // =================
  var windowHeight = $(window).height();
  $('.responsiveNav-list').css('height', windowHeight);
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

  // 照片墙
  var albumId;
  function photoAjax() {
    $.ajax({
      type: 'GET',
      url: 'http://www.lanpartyclub.com/lanpartyclub/album/get/random',
      dataType: 'JSONP',
      jsonp: 'callback',
      success: function (data) {
        var photos = data.data.album;
        var numZ = 0;
        $.each(photos, function (i, cur) {
          var thisCover;
          albumId = cur.id;
          numZ++;
          if (cur.cover == "") {
            thisCover = 'img/default_img1.jpg';
          } else {
            thisCover = 'http://www.lanpartyclub.com/upload/lanpartyclub/images/album/' + cur.cover;
          }
          $('.photoList ul').append('<li class="viewPhotos ani-' + numZ + '"  name="' + albumId + '">' +
            '<i class="photostyle" style="background: url('+ thisCover +') no-repeat center center;" data-aname="' + cur.id + '"></i>' +
            '<i class="line topLine"></i><i class="line rightLine"></i>' +
            '<i class="line bottomLine"></i>' +
            '<i class="line leftLine"></i>' +
            '<span class="teamName">' + cur.title + '活动<em>观看相册</em></span></li>')
        });
        // 相册页面样式
        $('.photoList li').hover(function () {
          $(this).find('span').stop().fadeIn(300);
        }, function () {
          $(this).find('span').stop().fadeOut(300);
        })
        var eleWidth = $('.photoList .viewPhotos').outerWidth(true);
        var numaaa = parseInt(windowWidth / eleWidth);
        $('.photoList').width(numaaa * eleWidth + 'px');
      }
    })
  }
  photoAjax();

  // 换一换按钮 
  $('#changePhoto').on('click', function () {
    $('.photoList ul').children().remove();
    photoAjax();
  });

  // 弹出相册
  $('.photoList ul').on('click', '.viewPhotos', function () {
    // $('.viewPhotos').on('click', function () {
    var thisAlbumId = $(this).attr('name')
    $('.detailCover').animate({
      left: 0
    }, 400).show();
    $('body').css('overflow-y', 'hidden');
    // 拉取数据
    $.ajax({
      type: 'GET',
      url: 'http://www.lanpartyclub.com/lanpartyclub/album/get?id=' + thisAlbumId,
      dataType: 'JSONP',
      jsonp: 'callback',
      success: function (data) {
        var albumT = data.data.album;
        var photos = data.data.photo;
        $('.detailCover').append('<h2>' + albumT.title + '活动纪念照片<span class="closeBtn closeBtnAnimate" id="closeBtn"><<返回</span></h2><div class="detailPic picSlideBox clearfix"><ul class="picSlideBig"></ul><div class="picSlideSmallBox"><span class="curNum"></span>/<span class="totalNum"></span></div><span class="picSlideBtn prev" id="picSlideBtnPrev"></span><span class="picSlideBtn next" id="picSlideBtnNext"></span></div>');
        if (photos.length == 0) {
          $('.picSlideBig').append('<li><img src="img/default_img.jpg" alt="活动照片"></li>')
        } else {
          $.each(photos, function (i, cur) {
            $('.picSlideBig').append('<li><img src="http://www.lanpartyclub.com/upload/lanpartyclub/images/album/' + cur.url + '" alt="活动照片"></li>')
          })
        }

        // 设置宽度弹出层照片宽度
        $('.picSlideBox .picSlideBig li').css('width', .9 * windowWidth + 'px');
        // 轮播
        var dtIndex = 1;
        var dtImgSize = $('.picSlideBig li').size();
        $('.picSlideBig li').eq(0).show();
        $('.totalNum').text(dtImgSize);
        $('.curNum').text('1');
        if (dtImgSize == 1) {
          $('.picSlideBtn').hide();
        }

        $('#picSlideBtnNext').on('click', function () {
          if (dtIndex == dtImgSize) {
            dtIndex = 1;
            $('.curNum').text('1');
          } else {
            dtIndex++;
          }
          $('.curNum').text(dtIndex);
          $('.picSlideBig li').eq(dtIndex - 1).stop().fadeIn().siblings().fadeOut();
        })

        $('#picSlideBtnPrev').on('click', function () {
          if (dtIndex == 1) {
            dtIndex = dtImgSize;
            $('.curNum').text(dtImgSize);
          } else {
            dtIndex--;
          }
          $('.curNum').text(dtIndex);
          $('.picSlideBig li').eq(dtIndex - 1).fadeIn().siblings().fadeOut();
        })
      }
    })
  })

  // 关闭按钮
  $('.detailCover').on('click', '#closeBtn', function () {
    $(this).parents('.detailCover').animate({
      left: '100%'
    }, 300).hide(300);
    $('body').css('overflow-y', 'auto');
    $('.detailCover').children().remove();
  })

})