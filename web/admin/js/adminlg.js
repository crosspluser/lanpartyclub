window.onload = function () {
  var oLogin = document.getElementById('login');
  var oUserAccount = document.getElementById('userAccount');
  var oUserPassword = document.getElementById('userPassword');
  oLogin.onclick = function () {
    if (oUserAccount.value === '15026777791' && oUserPassword.value === '15026777791') {
      setCookie('account_lpc', oUserAccount.value);
      window.location.href = 'lp-admin.html';
    } else {
      alert('请填写正确的账号！');
    }
  }


}

function setCookie(cookieName, cookieValue) {
  var Days = 1/24;
  var exp = new Date();
  exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000); //过期时间一个星期分钟  
  document.cookie = cookieName + "=" + escape(cookieValue) + ";expires=" + exp.toGMTString() + ";path=/";
}