import Vue from 'vue'
import axios from 'axios'
import moment from 'moment'
import {
    SweetModal
} from 'sweet-modal-vue'
import 'bootstrap/dist/css/bootstrap.css'
import './../sass/demo.scss'
import './../sass/pay.scss'
import 'font-awesome/css/font-awesome.min.css'
import { Card, createToken } from 'vue-stripe-elements'

// Vue.component('coupon', require('./components/Coupon.vue'))

/* 判断邮箱 */
function isEmail(szMail) {
    var szReg = /^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/
    var bChk = szReg.test(szMail)
    return bChk
}

/* eslint-disable no-new */
new Vue({
    el: '#app',
    data: {
        couponObject: null,
        coupon: '',
        loading: false,
        amount: 0,
        discount: 0,
        inited: true,
        errorMessage: "",
        email: '',
        emailErr: false,
        emailMessage: "Required",
        showLoading: true, // showLoding 控制的是hidden的样式，当为true的时候，为隐藏！
        complete: false, // complete为true时，表示信用卡卡号是OK的
        stripeOptions: {
            elements: {
                locale: 'en'
            }
        },
        token: "" // 信用卡的token
    },
    components: {
        SweetModal,
        Card
    },
    methods: {
        applyCoupon: function() {
            const that = this
            that.loading = true
            axios.get(`/rest/coupon?where={"code":"${this.coupon}"}`)
                .then(response => {
                    that.loading = false
                    if (response.status === 200 && response.data.length > 0) {
                        that.couponObject = response.data[0]
                        if (!that.checkDiscount()) {
                            that.couponObject = null
                            that.coupon = ""
                            return
                        }
                        that.discount = that.getDiscount()
                    } else {
                        that.couponObject = null
                        that.discount = 0
                        that.coupon = ""
                        that.errorMessage = "The coupon is not found"
                        that.$refs.modal.open()
                    }

                    // console.log(response)
                }).catch(() => {
                    // console.log(error)
                    that.loading = false
                    that.couponObject = null
                })
        },
        toRegister: function() {
            const that = this
            var userEmail = that.email
            var userName = userEmail.split("@")[0]
            var regTrack = ""
            var track = JSON.parse(window.localStorage.getItem('track')) // 获取联盟会员的track码
            if (track) {
                if (Date.parse(new Date()) < Date.parse(track.expired)) {
                    regTrack = track.code
                }
            }
            that.showLoading = false
            if (isEmail(userEmail)) {
                // 如果符合需求，进行注册
                axios.post('/quick_register', {
                    'email': userEmail,
                    'password': userEmail,
                    'name': userName,
                    'track': regTrack
                }).then(
                    response => {
                        if (response.status === 200) {
                            // 该邮箱已经被注册
                            if (response.data.code === -1) {
                                that.showLoading = true
                                that.emailErr = true
                                // that.emailMessage = response.data.desc.email[0]
                                that.emailMessage = "Already Registered"
                            } else if (response.data.code === 0) {
                                // 注册成功，提交表单
                                that.toCheckout()
                            } else {
                                that.showLoading = true
                                that.errorMessage = "There was an error, please check it"
                                that.$refs.modal.open()
                            }
                        } else {
                            that.showLoading = true
                            that.errorMessage = "There was an error, please try again later!"
                            that.$refs.modal.open()
                            that.email = ""
                        }
                    }).catch(() => {
                    // console.log(error)
                    that.showLoading = true
                    that.errorMessage = "There was an error, please try again later!"
                    that.$refs.modal.open()
                    that.email = ""
                })
            } else {
                that.showLoading = true
                that.errorMessage = "Invalid"
                that.$refs.modal.open()
            }
        },
        // 输入事件，判断邮箱格式
        onEmailEnter: function() {
            const that = this
            var userEmail = that.email
            if (!isEmail(userEmail)) {
                that.emailErr = true
                that.emailMessage = "Invalid"
                return false
            } else {
                that.emailErr = false
                return true
            }
        },
        // 支付按钮点击
        toCheckout: function() {
            const that = this
            // 支付按钮加上有效订阅判断
            axios.get('/userinfo').then(response => {
                const sub = response.data.effective_sub
                if (!sub) {
                    this.showLoading = false
                    this.$refs.checkout.submit()
                } else {
                    that.errorMessage = "You have a subscription already. Contact help@bigbigads.com"
                    that.$refs.modal.open()
                }
            })
        },
        initAmount: function(amount) {
            this.amount = amount
        },
        checkDiscount: function() {
            let obj = this.couponObject
            if (obj.status === 0) {
                this.errorMessage = "The coupon is invalid"
                this.$refs.modal.open()
                return false
            }
            if (this.amount < obj.total) {
                this.errorMessage = `Your order price should be more than ${obj.total}`
                this.$refs.modal.open()
                return false
            }
            if (obj.used >= obj.uses) {
                this.errorMessage = `The coupon is exhauted`
                this.$refs.modal.open()
                return false
            }
            if (!obj.start || !obj.end) {
                this.errorMessage = `The coupon is invalid`
                this.$refs.modal.open()
                return false
            }
            if (moment().isBefore(obj.start) || moment().isAfter(obj.end)) {
                this.errorMessage = `The coupon is expired`
                this.$refs.modal.open()
                return false
            }
            return true
        },
        getDiscount: function() {
            let obj = this.couponObject
            let amount = this.amount
            if (!obj)
                return 0
            if (obj.type === 0 && obj.discount <= 100)
                return Math.floor(amount * obj.discount / 100)
            if (obj.type === 1 && amount >= obj.discount)
                return obj.discount
            return 0
        },
        pay: async function() {
            let data = await createToken()
            console.log(data.token)
            this.token = data.token.id
            // 设置this.token时，vue不能立刻反映到v-model上，所以要等它反应完成后才能提交表单
            setTimeout(() => {
                this.$refs.payform.submit()
            }, 100)
        }
    }
})
