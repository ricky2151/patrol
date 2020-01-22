<div>
    <v-container fluid>
        <h3>Jadwal Hari Ini</h3>
    </v-container>
</div>

<template>
    <div>

        

       

        <v-layout row class='bgwhite margintop10'>
            <v-flex xs6>
                <div class='marginleft30 margintop10'>
                    <v-icon class='icontitledatatable'>list</v-icon>
                    <h2 class='titledatatable'>Jadwal Hari Ini</h2>
                   
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
            :rowsPerPageItems="[10, 20, 30, 40, 50]"
            
            :sort-by="['room_name', 'user_name']"
            :sort-asc="[true, true]"
            multi-sort
            :headers="headers"
            :items="data_table"
            :search="search_data"
            class="clean_datatable"
        >
        <template v-slot:items="props">
            
                <td v-bind:class='
            {
            rowRed: props.item.status_node_id == 3,
            rowOrange : props.item.status_node_id == 2,
            rowGreen : props.item.status_node_id == 1,
            }'>{{ props.item.no }}</td>
                <td v-bind:class='
            {
            rowRed: props.item.status_node_id == 3,
            rowOrange : props.item.status_node_id == 2,
            rowGreen : props.item.status_node_id == 1,
            }'>{{ props.item.room_name }}</td>
                <td v-bind:class='
            {
            rowRed: props.item.status_node_id == 3,
            rowOrange : props.item.status_node_id == 2,
            rowGreen : props.item.status_node_id == 1,
            }'>{{ props.item.time_start_end }}</td>
                <td v-bind:class='
            {
            rowRed: props.item.status_node_id == 3,
            rowOrange : props.item.status_node_id == 2,
            rowGreen : props.item.status_node_id == 1,
            }'>{{ props.item.user_name }}</td>
                
                <td v-bind:class='
            {
            rowRed: props.item.status_node_id == 3,
            rowOrange : props.item.status_node_id == 2,
            rowGreen : props.item.status_node_id == 1,
            }'>{{ props.item.status_node_name }}</td>
                <td v-bind:class='
            {
            rowRed: props.item.status_node_id == 3,
            rowOrange : props.item.status_node_id == 2,
            rowGreen : props.item.status_node_id == 1,
            }' style='line-break:anywhere'>{{ props.item.message }}</td>
                <td v-bind:class='
            {
            rowRed: props.item.status_node_id == 3,
            rowOrange : props.item.status_node_id == 2,
            rowGreen : props.item.status_node_id == 1,
            }'>{{ props.item.scan_time }}</td>

            <td v-bind:class='
            {
            rowRed: props.item.status_node_id == 3,
            rowOrange : props.item.status_node_id == 2,
            rowGreen : props.item.status_node_id == 1,
            }'>
                <v-btn small depressed color="light-blue darken-4" dark>
                    <label>Lihat Foto</label>
                </v-btn>
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

            name_table:'shifts',
            header_api:{
                'Accept': 'application/json',
                'Content-type': 'application/json'
            },

            

            headers: [
                { text: 'No', value: 'no'},
                { text: 'Ruangan', value: 'room_name', sort:0},
                { text: 'Waktu', value: 'time_start_end',sort:2},
                { text: 'Satpam', value: 'user_name',sort:3},
                { text: 'Kondisi', value: 'status_node_name',sort:4},
                { text: 'Pesan', value: 'message', width:'30%',sort:5},
                { text: 'Waktu Scan', value: 'scan_time',sort:6},
                { text: 'Foto', value: '',sort:6},
               

            ],


            data_table:[],
            search_data: null,
            
        }
    },
    methods: {

        action_change(id,idx_action)
        {
            
        },

        showTable(r) 
        {

            this.data_table = r.data;


        },
        

    },
    mounted(){
        this.get_data('api/admin/shifts/shifttoday');

    },
    mixins:[
        mxCrudBasic
    ],
}
</script>

