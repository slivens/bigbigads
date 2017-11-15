import './../sass/extension.scss'
import track from './track.js'

track.setTrack()
$('#downloadExtension').click(function() {
    // 发送bing统计事件
    window.uetq = window.uetq || []
    window.uetq.push({
        'ec': 'conversion',
        'ea': 'bba_download_extension',
        'el': 'click',
        'ev': 12
    })
    /* eslint-disable no-undef */
    // 发送google统计事件
    ga('send', 'event', 'conversion', 'click', 'download_extension', 12)

    if (document.getElementById('bigbigads-extension-is-installed')) {
        window.location.href = '/plan'
    } else {
        var url = 'https://chrome.google.com/webstore/detail/aeicgjbjcnodlaomefmanfbkhpcdlcbk'
        try {
            chrome.webstore.install(url, function() {
                window.open('https://www.facebook.com', '_blank')
            })
        } catch (err) {
            window.open(url, '_blank')
        }
    }
})
