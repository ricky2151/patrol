<div>
    <v-container fluid>
        <h3>Laporan</h3>
    </v-container>
</div>

<script src="vendor/raphael.js"></script>
<script src="Treant.min.js"></script>
<template>
    <div style='padding:20px'>
        <center>
            <img style='margin:40px 0px' src="/assets/images/logo.png" width=300 height=300 alt="">
            <br>
            <p style='margin: 0px;font-size:16px'>Apabila ada tambahan node atau perubahan gateway, silahkan tekan tombol dibawah ini </p>
            <br>
            <v-btn depressed color="light-blue darken-4" dark @click='refresh_config'>
                <label>Refresh Pengaturan LoRa</label>
            </v-btn>
            <br>
            <br>
            <div v-if='data_config != null' style='text-align:left;width:300px;overflow:auto'>
                <p>Pengaturan LoRa : </p>
                <pre>{{ data_config | pretty }}</pre>
            </div>
        </center>
    </div>
</template>

<script>
import axios from 'axios'

export default {

    data () {
        return {

           data_config : null,
            
        }
    },
    filters: {
        pretty: function(value) {
        return JSON.stringify(JSON.parse(value), null, 2);
        }
    },
    methods: {
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
        refresh_config()
        {
            this.showLoading(true);
            axios.get('/api/admin/iot/configGateway', {
                    params:{
                        token: localStorage.getItem('token')
                    }
            },{
                headers: {
                    'Accept': 'application/json',
                    'Content-type': 'application/json'
                }
            }).then(r=> {
                console.log(r.data);
                swal('Berhasil !', 'Pengaturan LoRa Berhasil Direfresh !', 'success');
                this.data_config = JSON.stringify(r.data.information);
                this.showLoading(false);
            })
            .catch((error) =>
            {
                this.showLoading(false);
                console.log("error : ")
                console.log(error)
                if(error.response.status == 422)
                {
                    swal('Request Gagal', 'Cek koneksi internet Anda !', 'error');
                }
                else
                {
                    swal('Unkown Error', error.response.data , 'error');
                }
            });
            
            
        }
        

    },
    mounted(){
        

    },
    
}
</script>

