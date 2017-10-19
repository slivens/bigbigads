import moment from 'moment'
import 'js-url'

import 'animate.css/animate.css'
import 'bootstrap/dist/css/bootstrap.css'
import 'font-awesome/css/font-awesome.min.css'

import './../sass/demo.scss'
import './../sass/mobile.scss'

(function() {
    changeWord(0)
    getAdsCount() // 获取广告数和广告主数量
    $('a[href="#ads-register"]').on("click", linkToUp)

    /* 动态换词 */
    function changeWord(item) {
        var word = ["Ad Creatives", " Audience Targeting", "Ad Run Time", "Tracking Tool", "Eshop Platform"]
        if (word.length <= item) {
            item = 0
        }
        $("#changeWord").html(word[item])
        $("#changeWord").addClass("fadeIn")
        setTimeout(function() {
            $("#changeWord").removeClass("fadeIn")
            setTimeout(function() {
                changeWord(item + 1)
            }, 500)
        }, 1500)
    }

    /* 页面内锚点连接上滑 */
    function linkToUp() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var $target = $(this.hash)
            $target = ($target.length && $target) || $('[name=' + this.hash.slice(1) + ']')
            if ($target.length) {
                var targetOffset = $target.offset().top
                $('html,body').animate({
                    scrollTop: targetOffset
                },
                300)
                return false
            }
        }
    }

    /* 异步获取广告数 */
    function getAdsCount() {
        /* 采用fetch获取数据 */
        // 需要跨域请求，有待解决
        var data = {
            method: "get"
        }
        fetch('/get_total_count', data).then(function(response) {
            return response.json()
        }).then(function(json) {
            try {
                var adscount = estimationThousand(json.total_ads_count)
                var adsercount = estimationThousand(json.total_adser_count)
                $("#adsnumber").html(adscount + "<sup>+</sup>")
                $("#adsernumber").html(adsercount + "<sup>+</sup>")
            } catch (e) {
                $("#adsnumber").html("8,460,000" + "<sup>+</sup>")
                $("#adsernumber").html("2,054,000" + "<sup>+</sup>")
            }
        }).catch(function(ex) {
            $("#adsnumber").html("8,460,000" + "<sup>+</sup>")
            $("#adsernumber").html("2,054,000" + "<sup>+</sup>")
        })
    }
    // 估算到千位
    function estimationThousand(num) {
        var count = parseInt(Math.round(num / 1000))
        count = count.toString().replace(/(^|\s)\d+/g, (m) => m.replace(/(?=(?!\b)(\d{3})+$)/g, ','))
        count = count + ",000"
        return count
    }

    /* eslint-disable no-undef */
    const trackCode = url('?track')
    if (trackCode) {
        let days = trackCode.match(/\d\d$/)
        days = days ? Number(days[0]) : 90
        window.localStorage.setItem('track', JSON.stringify({
            code: trackCode,
            expired: moment().add(days, 'days').format('YYYY-MM-DD')
        }))
    }

    let track
    if (window.localStorage.getItem('track')) {
        track = JSON.parse(window.localStorage.getItem('track'))
        if (Date.parse(new Date()) < Date.parse(track.expired)) {
            (document.querySelectorAll('[name=track]') || []).forEach(ele => { ele.value = track.code })
        }
    }

    if (track) {
        (document.querySelectorAll('.socialite') || []).forEach((ele) => {
            ele.href = `${ele.href}?track=${track.code}`
            ele.classList.remove('disabled')
        })
    }
})()
