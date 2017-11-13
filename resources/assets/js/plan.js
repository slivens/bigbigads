/*************************************************************
* 创建、初版编程：余清红
* 版本：1.0.1
* 需求：
*   1）卡片翻转
*   2）连接锚点上滑
*   3）向下图标
*   4）用户信息征集提交
* 依赖插件：axios、 bootstrap-select
* 修改历史：
* 2017.10.23： 添加用户信息征集
* 2017.10.28： 将linkToUp剪切到common.js
*
**************************************************************/
import './../sass/plan.scss'
import axios from 'axios'
import 'bootstrap-select/dist/css/bootstrap-select.min.css'
import 'font-awesome/css/font-awesome.min.css'
import 'bootstrap-select'
import {linkToUp} from './dom-common' // 从common中导该方法

// Ie 不兼容promise
if (!window.Promise) {
    window.Promise = Promise
}

// 定义价格数组
var princeArr = {
    'lite': [{
        'id': 101,
        'price': '25,35',
        'value': '25-35($)'
    }, {
        'id': 102,
        'price': '35,45',
        'value': '35-45($)'
    }, {
        'id': 103,
        'price': '45,60',
        'value': '45-60($)'
    }, {
        'id': 104,
        'price': 'other',
        'value': 'Other'
    }],
    'plus': [{
        'id': 201,
        'price': '149,199',
        'value': '149-199($)'
    }, {
        'id': 202,
        'price': '199,249',
        'value': '199-249($)'
    }, {
        'id': 203,
        'price': '249,399',
        'value': '249-399($)'
    }, {
        'id': 204,
        'price': 'other',
        'value': 'Other'
    }]
}
// 初始化定义国家数组
var countryData

// 翻转到背面
function turnToBack() {
    var cardName = $(this).attr("turn-back") // 获取要翻转卡片的名称
    $('[card-name=' + cardName + ']').addClass("transform-turn")
}
// 翻转到正面
function turnToFront() {
    var cardName = $(this).attr("turn-front")
    $('[card-name=' + cardName + ']').removeClass("transform-turn")
}

// 当上滑到某种程度的时候，向下的图标隐藏
function hideTheDownIcon() {
    var windowHight = $(window).height()
    var marginTop = $("#plan-table")[0].getBoundingClientRect().top // 获取不到下边距
    var marginBottom = windowHight - marginTop
    if (marginBottom > 50) {
        $("#click-to-down").addClass("hidden")
    } else {
        $("#click-to-down").removeClass("hidden")
    }
}

// 判断邮箱格式
function isEmail(szMail) {
    var szReg = /^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/
    var bChk = szReg.test(szMail)
    return bChk
}

/*
* 判断输入的内容
* 在dom元素上加个属性，用于判断
* dom元素属性：
* 1）request： 必填项目，不能为空；值为true
* 2）request-type: 数据的类型；值有：email、phone
* 3) request-maxlen: 长度限制
* 4) blur: 失去焦点判断（默认为：blur='ads-input-check'）
*/
function checkValue(eml) {
    let dom = eml.type ? $(this) : $(eml)
    let val = dom.val()
    let type = dom.attr("request-type") || ''
    let maxLen = parseInt(dom.attr("request-maxlen")) || false // 长度限制

    let inputRes = function(res, resText) {
        if (!res) {
            dom.parent("div").addClass("has-error")
            dom.parent("div").find(".ads-control-label").html(resText)
        } else {
            dom.parent("div").removeClass("has-error")
        }
        return res
    }
    // 优先判断必填项目
    if (dom.attr("request") && !val) {
        return inputRes(false, "Request")
    }

    // 邮箱类型判断
    if (type == "email") {
        if (val.length > 0 && !isEmail(val)) {
            return inputRes(false, "Invalid Email")
        }
    }

    // 手机号只能数字输入
    if (val && type == "phone") {
        dom.val(val.replace(/\D/g, '')) // 其值只能是数字
        // 手机号因为各国的格式不一致，不好判断，暂时判断条件为长度范围在5~20
        if (val.length < 5 || val.length > 20) {
            return inputRes(false, "Invalid phone")
        }
    }

    // 长度限制
    if (val && maxLen && val.length > maxLen) {
        return inputRes(false, "Invalid Length")
    }
    // 其他的不做要求的都认为是对的
    return inputRes(true)
}

/*
* 打开模态框
* 点击不同的按钮，都会打开模态框，显示不停的内容
* 点击的按钮绑定属性 “open-modal”,与其要打开的模态框的ID值
*/
function openModal() {
    let modalData = $(this).attr("modal-data") // 获取要传值给模态框的数据
    $("[value = 'level']").html(modalData)
    $("#submit-info").attr('info-level', modalData) // 将level等级的数据赋值到提交按钮

    // 判断是否为空，用于判断第一次加载是加载数据，之后就不用重复加载了
    if (!countryData) {
        // 获取国家数据,并填充到select中，这里采用的是boorstrap-select插件
        let locationOption = ''
        let countryJson = import('../data/world-country.json')
        countryJson.then(function(res) {
            countryData = res
            let item = 1
            countryData.forEach(function(items) {
                locationOption += `<option locationid="${item}" value="${items[1]}">${items[1]}</option>`
                item++
            })
            $('[option = "locationOption"]').html(locationOption)
            $('[option = "locationOption"]').selectpicker('refresh')
        }).catch(function() {
            locationOption = `<option locationid="0" value="">NULL</option>`
            $('[option = "locationOption"]').html(locationOption)
            $('[option = "locationOption"]').selectpicker('refresh')
        })
    }

    // 价格option，lite个plus的价格option不一致
    let priceOption = ''
    princeArr[modalData].forEach(function(item) {
        priceOption += `<option id="${item.id}" price="${item.price}">${item.value}</option>`
    })
    $('[option = "priceOption"]').html(priceOption)
    $('#info-modal').modal('show')
}

/*
* 提交数据
* 对所要提交的数据进行判断
* 成功后关闭当前模态框，并清空输入框数据
* 打开结果模态框，显示结果
*/
function submitInfo() {
    $(this).attr("disabled", "disabled") // 点击后屏蔽按钮，避免重复点击
    if (checkValue("#info-firstname") && checkValue("#info-lastname") && checkValue("#info-email") && checkValue("#info-company") && checkValue("#info-website") && checkValue("#info-page") && checkValue("#info-phone") && checkValue("#info-skype") && checkValue("#info-location")) {
        // 获取用户填写的数据
        let firstName = $("#info-firstname").val()
        let lastName = $("#info-lastname").val()
        let email = $("#info-email").val()
        let company = $("#info-company").val()
        let website = $("#info-website").val()
        let page = $("#info-page").val()
        let phone = $("#info-phone").val()
        let skype = $("#info-skype").val()
        let location = $("#info-location").val()
        let price = $("#info-price").find("option:selected").attr("price")
        let feedback = $("#info-feedback").val()
        let level = $(this).attr("info-level")
        let data = {
            'firstName': firstName,
            'lastName': lastName,
            'email': email,
            'company': company,
            'website': website,
            'page': page,
            'phone': phone,
            'skype': skype,
            'location': location,
            'price': price,
            'feedback': feedback,
            'level': level
        }
        axios({
            method: 'post',
            url: "/feedback/plan",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data
        }).then(function(res) {
            // 信息提交成功
            if (res.data.code == 0) {
                openResultModal('success')
            } else openResultModal('error')
        }).catch(function(res) {
            openResultModal('error')
        })
    } else $(this).removeAttr("disabled")
}

/*
* 打开结果模态框
* 执行表当提交的时候，显示结果
* 传入的参数为“success”和“error”
*/
function openResultModal(result) {
    let data = {
        'success': {
            'title': 'Submit successfully',
            'emoji': '😃',
            'text': 'Thank you for your participation.',
            'class': 'result-success'
        },
        'error': {
            'title': 'Submit failure',
            'emoji': '😐',
            'text': 'Please enter the correct information to continue.',
            'class': 'result-error'
        }
    }
    $("#submit-info").removeAttr("disabled") // 解禁按钮
    $("#info-modal").modal("hide")
    $("#result-title").html(data[result].title)
    $("#result-emoji").html(data[result].emoji)
    $("#result-text").html(data[result].text)
    $("#result-modal").modal("show").addClass(data[result].class)
    $("#info-modal input").val("")
}

(function() {
    $("[turn-back]").on("click", turnToBack)
    $("[turn-front]").on("click", turnToFront)
    $("#app-to-top").on("click", linkToUp)
    $("#click-to-down").on("click", linkToUp)
    hideTheDownIcon()
    $(window).on("scroll", hideTheDownIcon)
    // 失去焦点判断输入框
    $("input[blur='ads-input-check']").on("blur", checkValue)
    // 输入时动态验证
    $("input[blur='ads-input-check']").on("keyup", checkValue)
    // 提交数据
    $("#submit-info").on("click", submitInfo)
    // 点击打开模态框
    $("[open-modal]").on('click', openModal)
})()
