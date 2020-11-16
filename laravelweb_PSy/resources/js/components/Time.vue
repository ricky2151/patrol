<div>
    <v-container fluid>
        <h3>Waktu Jaga </h3>
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
                    <v-toolbar-title v-html='id_data_edit == -1 ?"Tambah Waktu Jaga":"Edit Waktu Jaga"'></v-toolbar-title>

                </v-toolbar>
                <v-form v-model="valid" style='padding:30px' ref='formCreateEdit'>
                    <v-text-field :rules="this.$list_validation.time_shift" v-model='input.start' label="Mulai" required></v-text-field>
                    <v-text-field :rules="this.$list_validation.time_shift" v-model='input.end' label="Berakhir" required></v-text-field>
                    <v-btn v-on:click='confirmationForm()' >Simpan</v-btn>
                </v-form>
            </v-card>
        </v-dialog>

        <v-layout row class='bgwhite margintop10'>
            <v-flex xs6>
                <div class='marginleft30 margintop10'>
                    <v-icon class='icontitledatatable'>access_time</v-icon>
                    <h2 class='titledatatable'>Waktu Jaga</h2>
                    <v-btn v-on:click='opendialog_createedit(-1)' color="menu" dark class='btnadddata'>
                    Tambah
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
            :rowsPerPageItems="[10, 20, 30, 40, 50]"
        >
        <template v-slot:items="props">
            <td>{{ props.item.no }}</td>
            <td>{{ props.item.start }}</td>
            <td>{{ props.item.end }}</td>

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
                          Pilih
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

            name_table:'times',
            header_api:{
                'Accept': 'application/json',
                'Content-type': 'application/json'
            },


            action_items: ['Edit', 'Hapus'],
            on:false,

            valid:null,
            dialog_createedit:false,
            
            

            id_data_edit:-1,

            input:{
                start:'',    
                end : '',
            },
            input_before_edit:null, //variabel ini digunakan untuk menampung input sebelum di klik submit saat edit
            

            headers: [
                { text: 'No', value: 'no'},
                { text: 'Mulai', value: 'start'},
                { text: 'Berakhir', value: 'end'},
                { text: 'Pilihan', align:'left',sortable:false, width:'15%'},

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
                this.delete_data(id, ['Shift', 'Riwayat Scan', 'Foto']);
            }
        },

        confirmationForm()
        {
            if((this.input.start) > (this.input.end))
            {
                swal({
                    title: "Apakah Anda yakin waktu melewati jam 12 malam ?",
                    text: "Apabila yakin, silahkan klik yes",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((yes) => {
                    
                    if (yes) {
                        this.save_data();
                    }
                });
            }
            else{
                this.save_data();
            }
            
        },

        convert_data_input(tempobject)
        {
            this.input.start = tempobject.start;
            this.input.end = tempobject.end;
            this.input_before_edit = JSON.parse(JSON.stringify(this.input));
        },

        prepare_data_form()
        {
            const formData = new FormData();
            if(this.id_data_edit == -1) //jika add data
            {
                formData.append('start', this.input.start);
                formData.append('end', this.input.end);
            }
            else //jika edit data
            {
                if(this.input.start != this.input_before_edit.start) 
                    formData.append('start', this.input.start);
                 if(this.input.end != this.input_before_edit.end) 
                    formData.append('end', this.input.end);
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

    },
    mixins:[
        mxCrudBasic
    ],
}
</script>

