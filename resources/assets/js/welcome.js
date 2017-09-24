$("#continue").click(function() {
    if (/iphone|nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|wap|mobile/i.test(navigator.userAgent.toLowerCase())) {
        // 移动端登录跳转至app
        window.open('/m/#/login', '_self')
    } else {
        window.open('/app', '_self')
    }
})
