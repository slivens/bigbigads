import './../sass/extension.scss'
import track from './track.js'

track.storage()
$("#downloadExtension").click(function() {
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
    window.open('//chrome.google.com/webstore/detail/bigbigadsfacebook-ad-exam/aeicgjbjcnodlaomefmanfbkhpcdlcbk', '_self')
})
