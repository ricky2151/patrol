<div>
    <v-container fluid>
        <h3>Room</h3>
    </v-container>
</div>

<template>
    <div>

        

        <!-- POPUP CREATE EDIT -->
        <v-dialog v-model="dialog_createedit" width=750>
            <v-card>
                <v-toolbar dark color="menu">
                    <v-btn icon dark v-on:click="closedialog_createedit()">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title v-html='id_data_edit == -1 ?"Add Room":"Edit Room"'></v-toolbar-title>

                </v-toolbar>
                <v-form v-model="valid" style='padding:30px' ref='formCreateEdit'>
                    <v-text-field :rules="this.$list_validation.max_req" v-model='input.name' label="Name" required></v-text-field>
                    <v-select class='pa-2' :rules="this.$list_validation.selectdata_req"  v-model='input.building_id' :items="ref_input.building" item-text='name' item-value='id' label="Select Building"></v-select>
                    <v-select class='pa-2' :rules="this.$list_validation.selectdata_req"  v-model='input.floor_id' :items="ref_input.floor" item-text='name' item-value='id' label="Select Floor"></v-select>
                    <v-btn v-on:click='save_data()' >submit</v-btn>
                    
                </v-form>
            </v-card>
        </v-dialog>

        <v-layout row class='bgwhite margintop10'>
            <v-flex xs6>
                <div class='marginleft30 margintop10'>
                    <v-icon class='icontitledatatable'>meeting_room</v-icon>
                    <h2 class='titledatatable'>Rooms Data</h2>
                    <v-btn v-on:click='opendialog_createedit(-1)' color="menu" dark class='btnadddata'>
                    Add Data
                </v-btn>
                </div>
                
            </v-flex>
            <v-flex xs12 class="text-xs-right">
                <v-text-field
                    class='d-inline-block searchdatatable'
                    v-model="search_data"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
            </v-flex>
        </v-layout>
        <v-data-table
            disable-initial-sort
            :headers="headers"
            :items="data_table"
            :search="search_data"
            class="datatable"
            :rows-per-page-items='[{"text" : "10", "value" : 10}]'
        >
        <template v-slot:items="props">
            <td>{{ props.item.no }}</td>
            <td>{{ props.item.floor_name }}</td>
            <td>{{ props.item.building_name }}</td>
            <td>{{ props.item.name }}</td>

            <td>
                <div class="text-xs-left">
                    <v-menu offset-y>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          class='btnaction'
                          color="menu"
                          dark
                          v-on="on"
                        >
                          Action
                        </v-btn>
                      </template>
                      <v-list>
                        <v-list-tile
                          v-for="(item, index) in action_items"
                          :key="index"
                          v-on:click="action_change(props.item.id,index)"
                          
                        >
                          <v-list-tile-title>{{ item }}</v-list-tile-title>
                        </v-list-tile>
                      </v-list>
                    </v-menu>
                </div>
            </td>
        </template>
        </v-data-table>
    </div>
</template>

<script>
import axios from 'axios'
import mxCrudBasic from './../mixin/mxCrudBasic';

export default {
    data () {
        return {

            name_table:'rooms',
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
            
            ref_input:
            {
                building:[],
                floor:[],
            },

            headers: [
                { text: 'No', value: 'no'},
                { text: 'Floor', value: 'floors'},
                { text: 'Building', value: 'buildings'},
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
                this.get_data_before_edit(id);
            }
            else if(idx_action == 1)
            {
                this.delete_data(id);
            }
        },


        fill_select_master_data(r)
        {
            //console.log(r.data.items[0].units);
            
            this.ref_input.floor = r.data.floors;
            this.ref_input.building = r.data.buildings;
            
        },
        convert_data_input(r)
        {
            //console.log(r);
            var temp_r = r;
            
            this.input.name = temp_r.name;
            this.input.building_id = parseInt(temp_r.building_id);
            this.input.floor_id = parseInt(temp_r.floor_id);

            this.input_before_edit = JSON.parse(JSON.stringify(this.input));
        },

        prepare_data_form()
        {
            const formData = new FormData();
            if(this.id_data_edit == -1) //jika add data
            {
                formData.append('name', this.input.name);
                formData.append('building_id', this.input.building_id);
                formData.append('floor_id', this.input.floor_id);
            }
            else //jika edit data
            {
                if(this.input.name != this.input_before_edit.name) 
                    formData.append('name', this.input.name);
                if(this.input.building_id != this.input_before_edit.building_id) 
                    formData.append('building_id', this.input.building_id);
                if(this.input.floor_id != this.input_before_edit.floor_id) 
                    formData.append('floor_id', this.input.floor_id);
                formData.append('_method','patch');
            }
            formData.append('token', localStorage.getItem('token'));
            return formData;
        },

        showTable(r) 
        {

            this.data_table = r.data;


        },
        

    },
    mounted(){
        this.get_data();
        this.get_master_data();

    },
    mixins:[
        mxCrudBasic
    ],
}
</script>

