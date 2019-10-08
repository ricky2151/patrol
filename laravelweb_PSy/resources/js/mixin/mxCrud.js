import mxStringProcessing from './mxStringProcessing'

export default {
    
	methods:{
        showLoading(state)
        {
            if(state)
            {
                document.getElementById('myLoading').style.display = 'block';
            }
            else
            {
                document.getElementById('myLoading').style.display = 'none';
            }
        },
		findDataById(id)
		{
			
			for(var i = 0;i<this.data_table.length;i++)
			{
				if(this.data_table[i].id == id)
				{
                    
                    return this.data_table[i];
                    
				}
			}
		},

        opendialog_createedit(id_data_edit,r){
            if(id_data_edit != -1)
            {
                this.id_data_edit = id_data_edit;
                if(r)
                {
                    console.log('masuk pilihan 1');
                    this.convert_data_input(r);
                }
                else
                {
                    console.log('masuk pilihan2');
                    this.convert_data_input(this.findDataById(id_data_edit));
                }
                
            }
            else
            {
                this.clear_input();
            }

            this.dialog_createedit = true;
        },

        get_data_before_edit(id_edit)
        {
            
            axios.get('/api/admin/' + this.name_table +'/' + id_edit + '/edit', {
                    params:{
                        token: localStorage.getItem('token')
                    }
            },{
                headers: {
                    'Accept': 'application/json',
                    'Content-type': 'application/json'
                }
            }).then(r=> {

                this.opendialog_createedit(id_edit,r.data);
            })
            .catch((error) =>
            {
                console.log("error : ")
                console.log(error)
                if(error.response.status == 422)
                {
                    swal('Request Failed', 'Check your internet connection !', 'error');
                }
                else
                {
                    swal('Unkown Error', error.response.data , 'error');
                }
            });
        },
        
		closedialog_createedit(){ 
            this.id_data_edit = -1;
            this.dialog_createedit = false;
        },

        clear_input(){
            
            this.$refs.formCreateEdit.resetValidation();
            
            for (var key in this.input)
            {
                if(this.input[key])
                {
                    if(Array.isArray(this.input[key]))
                    {
                        this.input[key] = [];     
                    }
                    else
                    {
                        this.input[key] = "";
                    }   
                }
            }
        },

        save_data() 
        {
            if(this.valid)
            {


                if(this.id_data_edit != -1) //jika sedang diedit
                {
                    this.showLoading(true);
                    axios.post(
                    	'api/admin/' + this.name_table + '/' + this.findDataById(this.id_data_edit).id,
                    	this.prepare_data_form(),
                    	this.header_api).then((r) => {
                    	this.clear_input();
                        this.get_data();
                        this.closedialog_createedit();
                        swal("Good job!", "Data saved !", "success");
                        this.id_data_edit = -1;
                        
                    }).catch((error) =>
                    {
                        console.log('terjadi error guys 1');
                        this.showLoading(false);
                        if(error.response.status == 422)
                        {
                            swal('Request Failed', 'Check your internet connection !', 'error');
                        }
                        else
                        {
                            swal('Unkown Error', error.response.data , 'error');
                        }
                    }).then((r) => {
                        this.showLoading(false);
                        r = r.data;
                        if(r.message == "Your are not admin")
                        {
                            this.$router.replace('/login');
                        }
                    });
                    
                }
                else //jika sedang tambah data
                {
                    this.showLoading(true);
                    
                    axios.post(
                    	'api/admin/' + this.name_table,
                    	this.prepare_data_form(),
                    	this.header_api).then((r)=> {
                        this.clear_input();
                        this.get_data();
                        this.closedialog_createedit();
                        swal("Good job!", "Data saved !", "success");
                    }).catch((error) =>
                    {
                        console.log('terjadi error guys 2');
                        this.showLoading(false);
                        console.log('adoh');
                        if(error.response.status == 422)
                        {
                            swal('Request Failed', 'Check your internet connection !', 'error');
                        }
                        else
                        {
                            swal('Unkown Error', error.response.data , 'error');
                        }
                    }).then((r) => {
                        this.showLoading(false);
                        r = r.data;
                        if(r.message == "Your are not admin")
                        {
                            this.$router.replace('/login');
                        }
                    });
                }
            }
            else
            {
                swal('Cannot Submit !', 'Please fill input correctly !', 'error');
            }
        },

        get_data(str) {
            var url = '';
            if(str)
            {
                url = str;

            }
            else
            {
                url = '/api/admin/' + this.name_table;
            }
            this.showLoading(true);
            axios.get(url, {
                    params:{
                        token: localStorage.getItem('token')
                    }
            },this.header_api).then((r) => {
                this.showLoading(false);
                r = r.data;
                if(r.message == "Your are not admin")
                {
                    this.$router.replace('/login');
                }
            	else
                {

                	this.showTable(r);
                	for(var i = 0;i<this.data_table.length;i++)
                	{
                		this.data_table[i].no = this.data_table.length - i;
                	}

                }
            });
        },

        delete_data(id_data_delete){
            
            swal({
                    title: "Are you sure want to delete this item?",
                    text: "Once deleted, it can't be undone",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    this.showLoading(true);
                    if (willDelete) {

                        axios.delete('api/admin/' + this.name_table + '/' + this.findDataById(id_data_delete).id,{
                            params:{
                        token: localStorage.getItem('token')
                    }
                            
                        }).then((r)=>{
                            this.showLoading(false);
                            r = r.data;
                            if(r.message == "Your are not admin")
                            {
                                this.$router.replace('/login');
                            }
                            else
                            {
                                this.get_data();
                                swal("Good job!", "Data Deleted !", "success");
                                

                            }

                            
                        });
                    }
            });
        },

        get_master_data()
        {
            axios.get('/api/admin/' + this.name_table +'/create', {
                params:{
                    token: localStorage.getItem('token')
                }
            },{
                headers: {
                    'Accept': 'application/json',
                    'Content-type': 'application/json'
                }
            }).then((r)=>{
                r = r.data;
                
                if(r.message == "Your are not admin")
                {
                    this.$router.replace('/login');
                }
                else
                {
                    this.fill_select_master_data(r)
                    
                    

                }
                
            }).catch((error) =>
            {
                console.log("error : ")
                console.log(error)
                if(error.response.status == 422)
                {
                    swal('Request Failed', 'Check your internet connection !', 'error');
                }
                else
                {
                    swal('Unkown Error', error.response.data , 'error');
                }
            });
        },

        get_popup_detail(id_edit, path)
        {
        	
        	axios.get('api/admin/' + this.name_table + '/' + this.findDataById(id_edit).id + '/' + path,{
                    params:{
                        token: localStorage.getItem('token')
                    }
            }).then((r) => {
                r = r.data;
                if(r.message == "Your are not admin")
                {
                    this.$router.replace('/login');
                }
                else
                {
                    return r.data.items;
                    

                }
                
            })
        },


        
	},
	mixins:[
		mxStringProcessing,
	],
    mounted() {

        
    },
}