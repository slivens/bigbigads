import Vue from 'vue'
// import axios from 'axios'
// import moment from 'moment'
// import Stripe from 'stripe'
import { Card, createToken } from 'vue-stripe-elements'

// Stripe.setPublishableKey(window.laravel.stripeKey)

/* eslint-disable no-new */
new Vue({
    el: '#app',
    data: {
        complete: false,
        stripeOptions: {},
        token: ""
    },
    components: {
        Card
    },
    methods: {
        pay: async function() {
            let data = await createToken()
            console.log(data.token)
            this.token = data.token.id
            // .then(data => console.log(data.token))
            // this.$refs.payform.submit()
        },
        submit: function() {
            this.$refs.payform.submit()
        }

    }
})
