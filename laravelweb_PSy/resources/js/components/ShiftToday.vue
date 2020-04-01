<div>
    <v-container fluid>
        <h3>Jadwal Hari Ini</h3>
    </v-container>
</div>

<template>
    <div>

        
        <!-- POPUP PHOTOS -->
        <v-dialog v-model="dialog_photos" width=900>
            <v-card>
                <v-toolbar dark color="menu">
                    <v-btn icon dark v-on:click="closedialog_createedit()">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title v-html='"Detail Foto"'></v-toolbar-title>

                </v-toolbar>
                <div style='width:10px;height:30px'></div>
                <v-img v-if='photos.length > 0' :src="'/storage/' + photos[index_photos]['url']" max-height=500 contain></v-img>
                <div style='margin-bottom:30px'></div>
                <v-layout row style='padding-left:10px;'>
                    
                    <v-layout row
                        align="center"
                        justify="end"
                    >
                        
                        <label style='margin-top:10px'>Gambar {{index_photos + 1}}/{{photos.length}}</label>
                        <v-btn small color='menu' dark @click='prev_index_photos'>Prev</v-btn>
                        <v-btn small color='menu' dark @click='next_index_photos'>Next</v-btn>
                        <v-spacer>
                        </v-spacer>
                        <v-btn small color='menu' dark @click="open_url('/storage/' + photos[index_photos]['url'])">View Detail</v-btn>
                        <div style='width:10px;height:60px'></div>
                    </v-layout>
                    
                </v-layout>

            </v-card>
        </v-dialog>
       

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
                <v-btn small depressed color="light-blue darken-4" dark @click='show_dialog_photos(props.item.id)'>
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

            dialog_photos: false,
            photos:[],
            index_photos:0,

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
        show_dialog_photos(id)
        {
            console.log('masuk sin');
            axios.get('/api/admin/shifts/' + id + '/getPhotos', {
                    params:{
                        token: localStorage.getItem('token')
                    }
            },this.header_api).then((r) => {
                if(r.data.data.length > 0)
                {
                    this.photos = r.data.data;
                    this.dialog_photos = true;
                }
                else
                {
                    swal('Foto tidak ditemukan !', 'Shift ini tidak memiliki foto sama sekali!', 'error');
                }
                

            });
            
        },
        next_index_photos()
        {
            var temp_length = this.photos.length;
            if(!(this.index_photos + 1 >= temp_length))
            {
                this.index_photos += 1;
            }
            else
            {
                this.index_photos = 0;
            }
        },
        prev_index_photos()
        {
            var temp_length = this.photos.length;
            if(!(this.index_photos - 1 < 0))
            {
                this.index_photos -= 1;
            }
            else
            {
                this.index_photos = temp_length - 1;
            }
        }
        

    },
    mounted(){
        this.get_data('api/admin/shifts/shifttoday');

    },
    mixins:[
        mxCrudBasic
    ],
}
</script>

