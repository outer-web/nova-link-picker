import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
    app.component('index-nova-link-picker', IndexField)
    app.component('detail-nova-link-picker', DetailField)
    app.component('form-nova-link-picker', FormField)
})
