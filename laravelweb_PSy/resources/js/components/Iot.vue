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
        

        <!-- POPUP ACKNOWLEDGE NODE -->
        <v-dialog v-model="dialog_acknowledge" fullscreen>
            <v-card>
                <v-toolbar dark color="menu">
                    <v-btn icon dark v-on:click="closedialog_acknowledge()">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title v-html='"Log Acknowledge Pada Node/NrF"'></v-toolbar-title>

                </v-toolbar>
                <div style='padding:30px'>
                    <v-btn v-on:click='get_acknowledges' color="menu" dark class='btnadddata'>
                        Reload
                    </v-btn>
                    <v-data-table
                        disable-initial-sort
                        :headers="headers_acknowledges"
                        :items="data_acknowledges"
                        :search="search_data_acknowledges"
                        class="datatable"
                        :rowsPerPageItems="[10, 20, 30, 40, 50]"
                    >
                    <template v-slot:items="props">
                        <td>{{ props.item.no }}</td>
                        <td>{{ props.item.room_id }}</td>
                        <td>{{ props.item.room_name }}</td>
                        <td>{{ props.item.sent }}</td>
                        <td>{{ props.item.time }}</td>
                        <td>{{ props.item.created_at }}</td>
                    </template>
                    </v-data-table>
                </div>
            </v-card>
        </v-dialog>


        <center>
            <img style='margin:40px 0px' src="/assets/images/logo.png" width=300 height=300 alt="">
            <br>
            <p style='padding:30px 70px; margin: 0px;font-size:16px'>Instruksi instalasi alat dapat dilihat dengan cara klik tombol "Panduan Instalasi". Apabila ada tambahan node atau perubahan gateway, klik tombol "Refresh Pengaturan LoRa/nRF", dan untuk melihat log pengiriman data LoRa/nRF dari gateway ke node  klik tombol "Log Acknowledges"</p>
            
            <v-btn depressed color="light-blue darken-4" dark @click='opendialog_panduan'>
                <label><v-icon left>menu_book</v-icon>Panduan Instalasi Lora/nRF</label>
            </v-btn>
            <br>
            <v-btn depressed color="light-blue darken-4" dark @click='refresh_config'>
                <label><v-icon left>router</v-icon>Refresh Pengaturan LoRa/nRF</label>
            </v-btn>
            <br>
            <!-- <v-btn depressed color="light-blue darken-4" dark @click='opendialog_acknowledge'>
                <label><v-icon left>dns</v-icon>Log Acknowledges</label>
            </v-btn> -->
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
           dialog_acknowledge : false,


           headers_acknowledges: [
                { text: 'No', value: 'no'},
                { text: 'ID Ruangan / ID Node', value: 'room_id'},
                { text: 'Ruangan', value: 'room_name'},
                { text: 'Berhasil / Tidak', value: 'sent'},
                { text: 'Selisih Waktu (mikrodetik)', value: 'time'},
                { text: 'Waktu Terkirim', value: 'created_at'},

            ],


            data_acknowledges:[],
            search_data_acknowledges: null,
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
        closedialog_acknowledge()
        {
            this.dialog_acknowledge = false;
        },
        get_acknowledges()
        {
            axios.get('/api/acknowledges', {
                    params:{
                        token: localStorage.getItem('token')
                    }
            },this.header_api).then((r) => {
                this.showLoading(false);
                r = r.data;
                if(r.message == "Your are not admin")
                {
                    this.$router.replace('/login');
                }
            	else
                {
                    this.data_acknowledges = r.data;
                	for(var i = 0;i<this.data_acknowledges.length;i++)
                	{
                        this.data_acknowledges[i].no = this.data_acknowledges.length - i;
                        if(this.data_acknowledges[i].sent == "0")
                        {
                            this.data_acknowledges[i].sent = "Gagal";
                        }
                        else
                        {
                            this.data_acknowledges[i].sent = "Berhasil";
                        }
                    }
                    console.log('cek data_acknowleges');
                    console.log(this.data_acknowledges);
                    

                }
            });
        },
        opendialog_acknowledge()
        {
            this.dialog_acknowledge = true;
            this.showLoading(true);
            this.get_acknowledges();
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
                console.log("error : ");
                console.log(error.response.data);
                if(error.response.status == 422)
                {
                    swal('Request Gagal', 'Cek koneksi internet Anda !', 'error');
                }
                else if(error.response.status == 503)
                {
                    swal('Unkown Error', error.response.data.message[0] , 'error');
                }
                else {
                    swal('Unkown Error', error.response.data , 'error');
                }
            });
            
            
        }
        

    },
    mounted(){
        

    },
    
}
</script>

