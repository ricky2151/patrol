

<template>
    <div>

        

        <!-- POPUP HISTORIES -->
        
        <v-dialog v-model="dialog_histories" width=900 v-if='data != null'>
            <v-card>
                <v-toolbar dark color="menu">
                    <v-btn icon dark v-on:click="closedialog_histories()">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title v-html='"Riwayat"'></v-toolbar-title>

                </v-toolbar>

                <!-- photos -->
                <div style='border: 4px solid #919191;margin:20px'>
                    <div style='width:10px;height:30px'></div>
                    <v-img v-if="data['histories'][index_history]['photos'].length > 0" 
                        :src="'/storage/' + data['histories'][index_history]['photos'][index_photo]['url']" max-height=500 contain>
                    </v-img>
                    <div style='margin-bottom:30px'></div>
                    <div><center>
                        <v-btn small color='menu' dark @click='prev_index_photo'>Prev</v-btn>
                        <label style='margin-top:10px;font-size:17px;font-weight:bold'>Gambar {{data["histories"][index_history]['photos'].length == 0 ? 0 : index_photo + 1}}/{{data["histories"][index_history]['photos'].length}}</label>
                        <v-btn small color='menu' dark @click='next_index_photo'>Next</v-btn>
                    </center></div>
                    <div><center>
                        <v-btn v-if='data["histories"][index_history]["photos"].length != 0' small color='menu' dark @click="open_url('/storage/' + data['histories'][index_history]['photos'][index_photo]['url'])">Detail Gambar</v-btn>
                    </center></div>
                </div>
                <div style='width:20px;height:20px'></div>
                <!-- ====== -->
                <div style='padding:20px'>
                    <!-- information -->
                    <p class='information_history title'>Ruangan </p><p class='information_history'> :  {{data['room_name']}}</p><br>
                    <p class='information_history title'>Waktu </p><p class='information_history'> : {{data['time_name']}}</p><br>
                    <p class='information_history title'>Date </p><p class='information_history'> : {{data['date']}}</p><br>
                    <p class='information_history title'>Waktu Scan </p><p class='information_history'> : {{data['histories'][index_history]['scan_time']}}</p><br>
                    <p class='information_history title'>Kondisi/Status </p><p class='information_history'> : {{data['histories'][index_history]['status_node_name']}}</p><br>
                    <p class='information_history title'>Pesan </p><p class='information_history'> : {{data['histories'][index_history]['message']}}</p><br>
                    <!-- ======= -->
                    <!-- control index history -->
                    <table style='width:100%'>
                        <tr>
                            <td>
                                <v-btn small color='menu' dark @click='prev_index_history'>Prev</v-btn>
                            </td>
                            <td align='center'>
                                <label style='margin-top:10px;font-size:19px;font-weight:bold'>Riwayat {{index_history + 1}}/{{data["histories"].length}}</label>
                            </td>
                            <td align='right'>
                                <v-btn small color='menu' dark @click='next_index_history'>Next</v-btn>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- ========= -->
            </v-card>
        </v-dialog>

        
    </div>
</template>
<style>
    .information_history
    {
        font-weight:bold;
        font-size:17px;
        display:inline-block;
    }
    .information_history.title
    {
        width:200px;
    }
</style>
<script>
import axios from 'axios'
import mxCrudBasic from './../mixin/mxCrudBasic';

export default {
    data () {
        return {

            
            header_api:{
                'Accept': 'application/json',
                'Content-type': 'application/json'
            },

            on:false,
            dialog_histories:false,
            index_photo:0,
            index_history:0,
            data:null,
            
        }
    },
    methods: {
        open_url(url)
        {
            window.location.href = url;
        },
        prev_index_history()
        {
            if(this.index_history > 0)
            {
                this.index_history -= 1;
            }
        },
        next_index_history()
        {
            if(this.index_history < this.data["histories"].length - 1)
            {
                this.index_history += 1;
            }
        },
        prev_index_photo()
        {
            if(this.index_photo > 0)
            {
                this.index_photo -= 1;
            }
        },
        next_index_photo()
        {
            if(this.index_photo < this.data["histories"][this.index_history]['photos'].length - 1)
            {
                this.index_photo += 1;
            }
        },
        show_dialog_histories(id)
        {
            axios.get('/api/admin/shifts/' + id + '/getHistories', {
                    params:{
                        token: localStorage.getItem('token')
                    }
            },this.header_api).then((r) => {
                
                if(r.data.data.histories.length > 0)
                {
                    this.data = r.data.data;
                    this.dialog_histories = true;
                    console.log('cek datanya dulu');
                    console.log(this.data);
                }
                else
                {
                    swal('Riwayat Tidak Ditemukan !', 'Shift ini belum di scan sama sekali!', 'error');
                }
                

            });
            
        },

        closedialog_histories()
        {
            this.dialog_histories = false;
        },
        

    },
    mounted(){

    },
    mixins:[
        mxCrudBasic
    ],
}
</script>

