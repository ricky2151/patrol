<div>
    <v-container fluid>
        <h3>Laporan</h3>
    </v-container>
</div>

<script src="vendor/raphael.js"></script>
<script src="Treant.min.js"></script>
<template>
    <div style='padding:20px'>

        <!-- POPUP PANDUAN -->
        <v-dialog v-model="dialog_panduan" width=750>
            <v-card>
                <v-toolbar dark color="menu">
                    <v-btn icon dark v-on:click="closedialog_panduan()">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title v-html='"Panduan Instalasi Alat Lora/nRF"'></v-toolbar-title>

                </v-toolbar>
                <div style='padding:30px'>
                    <label>1. Pasang semua alat LoRa/nRF pada tempatnya</label>
                    <br>
                    <label>2. Pastikan LoRa/nRF Gateway sudah connect WiFi dan connect MQTT</label>
                    <br>
                    <label>3. Buka halaman admin, atur room, gateway, jadwal shift, dll</label>
                    <br>
                    <label>4. Di halaman admin, masuk ke menu IoT, klik refresh pengaturan LoRa/nRF</label>
                    <br>
                    <label>5. Setelah itu, pastikan LoRa/nRF Gateway sudah subscribe ke id LoRa/nRF node dengan benar</label>
                    <br>
                    <label>6. Jalankan python dengan mengklik tombol "jalankan server python" atau menjalankannya secara manual melalui terminal</label>
                    <br>
                    <label>7. Buka aplikasi patrolee, dan sistem sudah siap digunakan !</label>
                    <br>
                </div>
            </v-card>
        </v-dialog>


        <center>
            <img style='margin:40px 0px' src="/assets/images/logo.png" width=300 height=300 alt="">
            <br>
            <p style='padding:30px 70px; margin: 0px;font-size:16px'>Instruksi instalasi alat dapat dilihat dengan cara klik tombol "Panduan Instalasi". Apabila ada tambahan node atau perubahan gateway, klik tombol "Refresh Pengaturan LoRa/nRF", dan untuk menjalankan server python klik tombol "Jalankan Server Python" </p>
            
            <v-btn depressed color="light-blue darken-4" dark @click='opendialog_panduan'>
                <label><v-icon left>menu_book</v-icon>Panduan Instalasi Lora/nRF</label>
            </v-btn>
            <br>
            <v-btn depressed color="light-blue darken-4" dark @click='refresh_config'>
                <label><v-icon left>router</v-icon>Refresh Pengaturan LoRa/nRF</label>
            </v-btn>
            <br>
            <v-btn depressed color="light-blue darken-4" dark @click='run_python'>
                <label><v-icon left>dns</v-icon>Jalankan Server Python</label>
            </v-btn>
            <br>
            <br>
            <div v-if='data_config != null' style='text-align:left;width:300px;overflow:auto'>
                <p>Pengaturan LoRa/nRF : </p>
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
           dialog_panduan : false,
            
        }
    },
    filters: {
        pretty: function(value) {
        return JSON.stringify(JSON.parse(value), null, 2);
        }
    },
    methods: {
        closedialog_panduan()
        {
            this.dialog_panduan = false;
        },
        opendialog_panduan()
        {
            this.dialog_panduan = true;
        },
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
        run_python()
        {
            swal({
                title: "Pastikan tidak ada server python yang sedang berjalan",
                text: "Apabila ada, maka akan terestart. Klik OK untuk melanjutkan",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((runPython) => {
                this.showLoading(true);
                if (runPython) {

                    axios.get('/api/admin/iot/runPython', {
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
                        if(r.data.error == false)
                        {
                            swal('Berhasil !', 'Server Python Berhasil Dijalankan !', 'success');
                        }
                        else
                        {
                            swal('Gagal !', 'Pastikan Anda menjalankan server degnan benar !', 'error');
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
                if(r.data.error == false)
                {
                    swal('Berhasil !', 'Pengaturan LoRa/nRF Berhasil Direfresh !', 'success');
                    this.data_config = JSON.stringify(r.data.information);
                }
                else
                {
                    swal('Gagal !', 'Pastikan Anda menjalankan server degnan benar !', 'error');
                }
                
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

