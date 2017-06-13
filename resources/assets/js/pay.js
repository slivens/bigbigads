import Vue from 'vue'
import axios from 'axios'
import moment from 'moment'
import { SweetModal} from 'sweet-modal-vue'
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
		errorMessage:""
    },
	components: {
		SweetModal
	},
    methods: {
        applyCoupon: function() {
            const that = this;
            that.loading = true;
            axios.get(`/rest/coupon?where={"code":${this.coupon}}`)
                .then(response => {
                    that.loading = false;
                    if (response.status === 200 && response.data.length > 0) {
                        that.couponObject = response.data[0];
                        if (!that.checkDiscount())
                            return;
                        that.discount = that.getDiscount();
                    } else {
                        that.couponObject = null;
                        that.discount = 0;
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
    }
})
