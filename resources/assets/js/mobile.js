import moment from 'moment'
import 'js-url'
import 'animate.css/animate.css'
import 'bootstrap/dist/css/bootstrap.css'
import 'font-awesome/css/font-awesome.min.css'
import './../sass/demo.scss'
import './../sass/mobile.scss'
import { linkToUp, changeWord } from './dom-common'

function palyVideo() {
    $('#youtubeFrame').attr('src', $('#youtubeFrame').data('url'))
    $('#youtubeFrame').removeClass('hidden')
    $(this).addClass('hidden')
}

(function() {
    changeWord(0, $('#change-word'), ['Ad Creatives', 'Audience Targeting', 'Ad Run Time', 'Tracking Tool', 'Eshop Platform'])
    $('a[href="#ads-register"]').on('click', linkToUp)

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
    $('#youtubeImage').on('click', palyVideo)
})()
