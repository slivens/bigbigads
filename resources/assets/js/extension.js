import './../sass/extension.scss'
import 'js-url'
import moment from 'moment'

document.addEventListener('DOMContentLoaded', function(event) {
    /* eslint-disable no-undef */
    var track = url("?track")
    if (track) {
        var days = track.match(/\d\d$/)
        days = days ? Number(days[0]) : 90
        window.localStorage.setItem('track', JSON.stringify({
            "code": track,
            "expired": moment().add(days, 'days').format('YYYY-MM-DD')
        }))
    }
})
