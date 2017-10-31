import I18n from 'i18n-js'
import en from '../lang/en.json'
import cn from '../lang/zh_cn.json'

/* global DEFAULT_LOCALE:false */
I18n.defaultLocale = DEFAULT_LOCALE
/* global LOCALE:false */
I18n.locale = LOCALE
I18n.fallbacks = true

I18n.translations['en'] = en
I18n.translations['zh_cn'] = cn

const tr = (...args) => I18n.t(...args)

export default tr
