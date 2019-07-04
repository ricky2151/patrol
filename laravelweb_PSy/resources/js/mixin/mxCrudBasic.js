import mxCrud from '@/js/mixin/mxCrud'
export default {
	methods:{
        

        opendialog_createedit(id_data_edit){
            if(id_data_edit != -1)
            {
                this.id_data_edit = id_data_edit;
                this.convert_data_input(this.findDataById(id_data_edit));
                
            }
            else
            {
                this.clear_input();
            }

            this.dialog_createedit = true;
        },


        
        
	},
	mounted(){
		//this.get_data();
	},
    mixins:[
        mxCrud
    ],
}