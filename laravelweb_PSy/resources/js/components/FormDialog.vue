<template>
    <div>

        

        <!-- POPUP CREATE EDIT -->
        <v-dialog v-model="dialog_createedit" width=750>
            <v-card>
                <v-toolbar dark color="menu">
                    <v-btn icon dark v-on:click="closedialog_createedit()">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title v-html='id_data_edit == -1 ?"Add " + name_table:"Edit " + name_table'></v-toolbar-title>

                </v-toolbar>
                <v-form v-model="valid" style='padding:30px' ref='formCreateEdit'>
                    <div v-for='item in listinput'>
                        <v-text-field v-if='item.typefield=="v-text-field"' :rules='item.validation' v-model='input[item["name_field"]]' label='item["label_field"]' required></v-text-field>

                        <v-text-area v-if='item.typefield=="v-text-area"' :rules='item.validation' v-model='input[item["name_field"]]' label='item["label_field"]' required></v-text-area>
                        
                        <v-select v-if='item.typefield=="v-select"' :rules="item.validation" v-model='input[item["name_field"]]' :items="ref_input.avgprice_status" item-text='name' item-value='value' label="Select Average Price Status"></v-select>

                    </div>
                    
                    <v-btn v-on:click='save_data()' >submit</v-btn>
                </v-form>
            </v-card>
        </v-dialog>

        
    </div>
</template>

<script>
import axios from 'axios'
import mxCrudBasic from '@/js/mixin/mxCrudBasic';

export default {
    props:['listinput','header_api','urlinsert','urlpatch', 'name_table']
    data () {
        return {


            
            header_api:{
                'Accept': 'application/json',
                'Content-type': 'application/json'
            },


            action_items: ['Edit', 'Delete'],
            on:false,

            valid:null,
            dialog_createedit:false,
            
            

            id_data_edit:-1,

            input:{
                name:'',    
            },
            input_before_edit:null, //variabel ini digunakan untuk menampung input sebelum di klik submit saat edit
            

            headers: [
                { text: 'No', value: 'no'},
                { text: 'Name', value: 'name'},
                { text: 'Action', align:'left',sortable:false, width:'15%'},

            ],


            data_table:[],
            search_data: null,
            
        }
    },
    methods: {

        action_change(id,idx_action)
        {
            if(idx_action == 0)
            {
                this.opendialog_createedit(id)
            }
            else if(idx_action == 1)
            {
                this.delete_data(id);
            }
        },



        convert_data_input(tempobject)
        {
            this.input.name = tempobject.name;
            this.input_before_edit = JSON.parse(JSON.stringify(this.input));
        },

        prepare_data_form()
        {
            const formData = new FormData();
            if(this.id_data_edit == -1) //jika add data
            {
                formData.append('name', this.input.name);
            }
            else //jika edit data
            {
                if(this.input.name != this.input_before_edit.name) 
                    formData.append('name', this.input.name);
                formData.append('_method','patch');
            }
            formData.append('token', localStorage.getItem('token'));
            return formData;
        },

        showTable(r) 
        {
            this.data_table = r.data.items.units;

        },
        

    },
    mounted(){
        this.get_data();

    },
    mixins:[
        mxCrudBasic
    ],
}
</script>
