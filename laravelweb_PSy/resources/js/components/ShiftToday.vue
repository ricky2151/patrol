<div>
    <v-container fluid>
        <h3>Jadwal Hari Ini</h3>
    </v-container>
</div>

<template>
    <div>

        <!-- POPUP HISTORY SHIFT -->
        <cp-history ref='cpHistory'></cp-history>
        
       

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
            <td>{{ props.item.no }}</td>
            <td>{{ props.item.room_name }}</td>
            <td>{{ props.item.time_start_end }}</td>
            <td>{{ props.item.user_name }}</td>
            <td>
                <v-btn small depressed color="light-blue darken-4" dark @click='open_history(props.item.id)'>
                    <label>Riwayat Scan</label>
                </v-btn>
            </td>
        </template>
        </v-data-table>
    </div>
</template>

<script>
import axios from 'axios'
import mxCrudBasic from './../mixin/mxCrudBasic';
import cpHistory from './cpHistory.vue';

export default {
    components:{
        cpHistory,
    },
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
                { text: 'Riwayat', value: '',sort:6},
            ],


            data_table:[],
            search_data: null,
            
        }
    },
    methods: {
        open_history(id)
        {
            this.$refs['cpHistory'].show_dialog_histories(id);
        },
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

