<div>
    <v-container fluid>
        <h3>Laporan</h3>
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
                    <h2 class='titledatatable'>Laporan</h2>
                    <v-btn v-on:click='delete_report' color="menu" dark style='margin-left:15px'>
                        Backup dan Hapus Laporan
                    </v-btn>
                </div>
                
            </v-flex>
            <v-flex xs6 class="text-xs-right">
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
            
            
            :headers="headers"
            :items="data_table"
            :search="search_data"
            class="clean_datatable"
        >
        <template v-slot:items="props">
            
            <td>{{ props.item.no }}</td>
                
                
            <td>{{ props.item.date }}</td>

            <td>{{ props.item.time_start_end }}</td>
                

            <td>{{ props.item.room_name }}</td>

            <td>{{ props.item.user_name }}</td>


            
            <td>{{ props.item.total_histories }}</td>

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
                { text: 'Tanggal', value: 'date'},
                { text: 'Waktu', value: 'time_start_end'},
                { text: 'Ruangan', value: 'room_name'},
                { text: 'Satpam', value: 'user_name'},
                { text: 'Jumlah Riwayat', value: 'id'},
                { text: 'Riwayat', value: ''},
            ],


            data_table:[],
            search_data: null,
            
        }
    },
    methods: {
        delete_report()
        {
            
            swal({
                title: "Backup & Hapus Laporan Lama",
                text: "Yakin ingin melakukan backup & hapus laporan lama ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((runPython) => {
                this.showLoading(true);
                if (runPython) {
                    const formData = new FormData();
                    formData.append('token', localStorage.getItem('token'));
                    axios.post('/api/admin/shifts/removeAndBackup', formData,{
                        headers: {
                            'Accept': 'application/json',
                            'Content-type': 'application/json'
                        }
                    }).then(r=> {
                        console.log(r.data);
                        if(r.data.error == false)
                        {
                            this.get_data();
                            swal('Berhasil !', 'Backup & Hapus Laporan Lama Berhasil Dilakukan !', 'success');
                        }
                        else
                        {
                            swal('Gagal !', 'Backup & Hapus Laporan Lama Gagal Dilakukan !', 'error');
                        }
                        
                        this.showLoading(false);
                    });
                }
                else
                {
                    this.showLoading(false);
                }
            });
        
        },
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
        this.get_data();

    },
    mixins:[
        mxCrudBasic
    ],
}
</script>

