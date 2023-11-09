import Vue from 'vue'
import Vuetify from 'vuetify'

// Скопированы стили библиотеки и изменен шрифт
//import '../../../public/css/vuetify.css'
// Импорт организован в компоненте, т.к. стили конфликтуют со стилями bootstrap

import colors from 'vuetify/lib/util/colors';

Vue.use(Vuetify)

const vuetify = new Vuetify({
  theme: {
    themes: {
      light: {
	
		background: '#FFFFFF',
		surface: '#FFFFFF',
		primary: '#01579B',
		'primary-darken-1': '#3700B3',
		secondary: '#03DAC6',
		'secondary-darken-1': '#018786',
		error: '#B00020',
		info: '#2196F3',
		success: '#4CAF50',
		warning: '#FB8C00',
		menuicons: '#414961',
		black: '#000000',
		subtitlepromo: '#0277BD',
		indigo: '#3F51B5',		
		
      },
    },
  },
})

export default vuetify

