import '../sass/login.scss'
import 'jquery-validation'
import 'jquery-validation/dist/additional-methods'
import tr from '../lib/intl'

var Login = (function() {
    var handleLogin = function() {
        $('.login-form').validate({
            errorElement: 'span', // default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                email: {
                    required: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                }
            },

            messages: {
                email: {
                    required: tr('auth.validation.email_required'),
                    email: tr("validation.email")
                },
                password: {
                    required: tr('auth.validation.password_required')
                }
            },

            invalidHandler: function(event, validator) { // display error alert on form submit
                $('.alert-danger', $('.login-form')).show()
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error') // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error')
                label.remove()
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'))
            },

            submitHandler: function(form) {
                $("#submit-background").removeClass("hidden")
                form.submit() // form validation success, call ajax form submit
            }
        })

        $('.login-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.login-form').validate().form()) {
                    $("#submit-background").removeClass("hidden")
                    $('.login-form').submit() // form validation success, call ajax form submit
                }
                return false
            }
        })
    }

    var handleForgetPassword = function() {
        $('.forget-form').validate({
            errorElement: 'span', // default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },

            messages: {
                email: {
                    required: "Email is required."
                }
            },

            invalidHandler: function(event, validator) { // display error alert on form submit

            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error') // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error')
                label.remove()
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'))
            },

            submitHandler: function(form) {
                $("#submit-background").removeClass("hidden")
                form.submit()
            }
        })

        $('.forget-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.forget-form').validate().form()) {
                    $('.forget-form').submit()
                }
                return false
            }
        })

        jQuery('#forget-password').click(function() {
            jQuery('.login-form').hide()
            jQuery('.forget-form').show()
            history.pushState({}, "", '/forget')
            // window.location.hash = '#forget'
        })

        jQuery('#back-btn').click(function() {
            jQuery('.login-form').show()
            jQuery('.forget-form').hide()
            history.pushState({}, "", '/login')
            // window.location.hash = ""
        })
    }

    var handleRegister = function() {
        $('.register-form').validate({
            errorElement: 'span', // default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {

                fullname: {
                    required: false
                },
                email: {
                    required: true,
                    email: true,
                    checkEmail: true
                },
                address: {
                    required: false
                },
                city: {
                    required: false
                },
                username: {
                    required: true
                },
                password: {
                    required: true
                },
                rpassword: {
                    equalTo: "#register_password"
                },

                tnc: {
                    required: true
                }
            },

            messages: { // custom messages for radio buttons and checkboxes
                tnc: {
                    required: "Please accept Agreement first."
                }
            },

            invalidHandler: function(event, validator) { // display error alert on form submit

            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error') // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error')
                label.remove()
            },

            errorPlacement: function(error, element) {
                if (element.attr("name") == "tnc") { // insert checkbox errors after the container
                    error.insertAfter($('#register_tnc_error'))
                } else if (element.closest('.input-icon').length === 1) {
                    error.insertAfter(element.closest('.input-icon'))
                } else {
                    error.insertAfter(element)
                }
            },

            submitHandler: function(form) {
                $("#submit-background").removeClass("hidden")
                // 这个会报错，去掉[0],对功能不影响
                // form[0].submit();
                form.submit()
            }
        })
        // 自动填充用户名，用户名为邮箱前缀
        $("#register-email").keyup(function(e) {
            $("#register-username").val($(this).val().split('@')[0])
        })
        $('.register-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.register-form').validate().form()) {
                    $("#submit-background").removeClass("hidden")
                    $('.register-form').submit()
                }
                return false
            }
        })
    }

    // 由于mailgun对hotmail,outlook,live邮箱基本不可达，暂时过滤，提示不支持
    jQuery.validator.addMethod("checkEmail", function(value, element) {
        /* eslint-disable no-useless-escape */
        return this.optional(element) || !/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@(hotmail|outlook|live)\.com$/.test(value)
    }, "Sorry, we don't support hotmail/outlook/live now.")

    return {
        // main function to initiate the module
        init: function() {
            handleLogin()
            handleForgetPassword()
            handleRegister()

            // if (window.localStorage.getItem('track')) {
            //     var track = JSON.parse(window.localStorage.getItem('track'));
            //     if (Date.parse(new Date()) < Date.parse(track.expired)) {
            //         $('[name=track]').each(function() {
            //             $(this).val(track.code);
            //         });
            //     }
            //     $('.socialite').each(function() {
            //         var href = $(this).attr('href');
            //         href += "?track=" + track.code;
            //         $(this).attr("href", href);
            //     });
            // }

            // 生成验证码
            $('.captcha').click(function() {
                var ts = Date.parse(new Date())
                $(this).attr('src', '/captcha?t=' + ts)
            })
            switch (window.location.pathname) {
            case '/login':
                jQuery('.login-form').show()
                break
            case '/forget':
                jQuery('.login-form').hide()
                jQuery('.forget-form').show()
                break
            case '/register':
                jQuery('.login-form').hide()
                jQuery('.register-form').show()
                break
            }
            jQuery('.content').show()

            jQuery('#register-btn').click(function() {
                jQuery('.login-form').hide()
                jQuery('.register-form').show()
                history.pushState({}, "", '/register')
                // window.location.hash = '#register'
            })

            jQuery('#register-back-btn').click(function() {
                jQuery('.login-form').show()
                jQuery('.register-form').hide()
                history.pushState({}, "", '/login')
                // window.location.hash = ''
            })
        }

    }
}())
jQuery(document).ready(function() {
    Login.init()
})
