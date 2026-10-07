import DefaultTheme from 'vitepress/theme'
import ComponentPreview from './ComponentPreview.vue'
import './custom.css'

export default {
  extends: DefaultTheme,
  enhanceApp({ app }) {
    app.component('ComponentPreview', ComponentPreview)
  },
}
