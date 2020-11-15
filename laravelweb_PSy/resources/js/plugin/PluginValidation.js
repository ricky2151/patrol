// This exports the plugin object.
export default {
  // The install method will be called with the Vue constructor as
  // the first argument, along with possible options
  install (Vue, options) {


  	
  	Vue.prototype.$list_validation = {
  		email_req:[
  			v => v.length > 0 || 'Please fill the blank !',
  			v => v.includes('@') || 'Please fill with email !',
  		],

  		email:[
  			v => v.includes('@') || 'Please fill with email !'
  		],

  		combodata_req:[
  			v => v.length > 0 || 'Please fill the blank !',
  		],

  		selectdata_req : [
  			v => v > 0 || 'Please fill the blank !',
  		],

  		selecttf_req : [
  			v => (v == 1 || v == 0) || 'Please fill the blank !',	
  		],

  		max_req : [
	  		
	  		v => !!v || 'Please fill the blank !',
	  		v => (v || '').length <= 191 || 'A maximum of 191 characters is allowed'

  		],

  		max : [
  			v => (v || '').length <= 191 || 'A maximum of 191 characters is allowed'
  		],

  		numeric_req : [
  			v => !!v || 'Please fill the blank !',
  			v =>  !isNaN(v) || 'Input must number !',

  		],

  		numeric : [
  			v => !isNaN(v) || 'Input must number !',
  		],

      time_shift : [
        v => !!v || 'Please fill the blank !',
        v => (v.length == 5 && !isNaN(v[0]) && !isNaN(v[1]) && !isNaN(v[3]) && !isNaN(v[4]) && v[2] == ":")  || 'Input format must hh:mm !',

      ],


  	};


  },
  methods :
  {
   
  }
}