/******************************************
 * 公用方法
 * 这里的方法在其他页面可能也会用到，所以有两个页面使用的方法写在这里
 * 用法： 
 *   1）这里设置方法，export function fun1 () {...}
 *   2）如果某个页面需要fun1，则在顶部写： import {fun1} from dom-utils即可
 * 
 * 创建、初版编程：余清红
 * 版本：1.0.1
 * 函数：
 *   1) linkToUp
 * 
 * 修改历史： 
 *   2017.10.28 创建文件，添加 linkToUp方法
 * 
 ******************************************/
// 页面内锚点连接上滑
export function linkToUp() {
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
