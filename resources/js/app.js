require('./bootstrap');

import { createApp } from 'vue'
import Calculator from './components/Calculator'
const app = createApp({})
app.component('calculator', Calculator)
app.mount('#app')