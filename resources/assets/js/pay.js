import Vue from 'vue'
import axios from 'axios'
import moment from 'moment'
import { SweetModal } from 'sweet-modal-vue'
import 'bootstrap/dist/css/bootstrap.css';
import './../sass/demo.scss';
import './../sass/pay.scss';
import 'font-awesome/css/font-awesome.min.css';
// Vue.component('coupon', require('./components/Coupon.vue'));

const app = new Vue({
    el: '#app',
    data: {
        couponObject:null,
        coupon:'', 
        loading:false,
        amount:0,
        discount:0,
        inited:true,
		errorMessage:"",
        email:'',
        emailErr:false,
        emailMessage:"Required",
        ipAddr:"0.0.0.0",
        showLoading:true,//showLoding 控制的是hidden的样式，当为true的时候，为隐藏！
    },
	components: {
		SweetModal
	},
    methods: {
        applyCoupon: function() {
            const that = this;
            that.loading = true;
            axios.get(`/rest/coupon?where={"code":"${this.coupon}"}`)
                .then(response => {
                    that.loading = false;
                    if (response.status === 200 && response.data.length > 0) {
                        that.couponObject = response.data[0];
                        if (!that.checkDiscount()) {
                            that.couponObject = null;
                            that.coupon = "";
                            return;
                        }
                        that.discount = that.getDiscount();
                    } else {
                        that.couponObject = null;
                        that.discount = 0;
                        that.coupon = "";
                        that.errorMessage = "The coupon is not found";
						that.$refs.modal.open();
                    }
                        
                    // console.log(response);
                }).catch(error => {
                    // console.log(error);
                    that.loading = false;
                    that.couponObject = null;
                });
        },
        toRegister:function(){
            const that = this;
            var userEmail = that.email;
            var userName = userEmail.split("@")[0];
            that.showLoading = false;
            if(isEmail(userEmail)){
                //如果符合需求，进行注册
                axios.post('/quickRegister',{'email':userEmail,'password':userEmail, 'name':userName}).then(
                    response =>{
                        if(response.status === 200){
                            //该邮箱已经被注册
                            if(response.data.code === -1){
                                that.showLoading = true;
                                that.emailErr = true;
                                //that.emailMessage = response.data.desc.email[0];
                                that.emailMessage =  "Already Registered";
                            }
                            else if(response.data.code === 0)
                            {
                                //注册成功，提交表单
                                document.getElementById("checkout").submit();
                            }
                            else{
                                that.showLoading = true;
                                that.errorMessage = "There was an error, please check it";
                                that.$refs.modal.open();
                            }
                        }
                        else
                        {
                            alert("error");
                        }
                    }).catch(error => {
                    // console.log(error);
                    that.loading = false;
                    that.couponObject = null;
                });
            }
            else{
                that.showLoading = true;
                that.errorMessage = "Invalid";
                that.$refs.modal.open();
            }
        },
        //输入事件，判断邮箱格式
        emailEnter:function(){
            const that = this;
            var userEmail = that.email;
            if(!isEmail(userEmail)){
                that.emailErr=true;
                that.emailMessage="Invalid"
                return false;
            }
            else{
                that.emailErr=false;
                return true;
            }
        },
        //支付按钮点击
        toPay:function(){
            this.showLoading = false;
            document.getElementById("checkout").submit();
        },
        initAmount:function(amount) {
            this.amount = amount
        },
        checkDiscount:function() {
            let obj = this.couponObject;
            if (obj.status === 0) {
                this.errorMessage = "The coupon is invalid";
                this.$refs.modal.open();
                return false;
            }
            if (this.amount < obj.total) {
                this.errorMessage = `Your order price should be more than ${obj.total}`;
                this.$refs.modal.open();
                return false;
            }
            if (obj.used >= obj.uses) {
                this.errorMessage = `The coupon is exhauted`;
                this.$refs.modal.open();
                return false;
            }
            if (!obj.start || !obj.end) {
                this.errorMessage = `The coupon is invalid`;
                this.$refs.modal.open();
                return false;
            }
            if (moment().isBefore(obj.start) || moment().isAfter(obj.end)) {
                this.errorMessage = `The coupon is expired`;
                this.$refs.modal.open();
                return false;
            }
            return true;
        },
        getDiscount: function() {
            let obj = this.couponObject;
            let amount = this.amount;
            if (!obj)
                return 0;
            if (obj.type === 0 && obj.discount <= 100)
                return Math.floor(amount * obj.discount / 100);
            if (obj.type === 1 && amount >= obj.discount)
                return obj.discount;
            return 0;
        }
    },
    mounted () {
        try{
            this.ipAddr = returnCitySN.cip;
        }
        catch(e){
            this.ipAddr = '0.0.0.0';
        }
        
    }
})

/*判断邮箱*/
function isEmail(szMail){ 
var szReg=/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/; 
var bChk=szReg.test(szMail); 
return bChk; 
} 

