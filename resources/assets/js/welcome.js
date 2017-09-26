$("#continue").click(function() {
    $.get("/record-continue/")
    if (/iphone|nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|wap|mobile/i.test(navigator.userAgent.toLowerCase())) {
        window.open('/m/#/login', '_self')
    } else {
        window.open('/app', '_self')
    }
})
