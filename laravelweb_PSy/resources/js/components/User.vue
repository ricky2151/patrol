<div>
    <v-container fluid>
        <h3>user</h3>
    </v-container>
</div>

<template>
    <div>
        
        <!-- POPUP DETAIL SHIFT -->
        <v-dialog v-model="dialog_detailshifts" width=750>
            <v-card>
                <v-toolbar dark color="menu">
                    <v-btn icon dark v-on:click="closedialog_detailshifts()">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Detail Shifts</v-toolbar-title>

                </v-toolbar>
                <div style='padding:30px'>

                    <v-text-field
                        v-model="popup_search_detailshifts"
                        append-icon="search"
                        label="Search"
                        single-line
                        hide-details
                      ></v-text-field>
                    <v-data-table
                    disable-initial-sort
                    :headers="headers_popup_detailshifts"
                    :items="popup_detailshifts"
                    :search="popup_search_detailshifts"
                    class=""
                    >
                    <template v-slot:items="props">
                        <td>{{ props.index + 1 }}</td>
                        <td>{{ props.item.room }}</td>
                        <td>{{ props.item.time_start }}</td>
                        <td>{{ props.item.time_end }}</td>
                        <td>{{ props.item.date }}</td>
                        <td>{{ props.item.status_node }}</td>
                        <td>{{ props.item.message }}</td>
                        <td>{{ props.item.token_shift }}</td>
                    </template>
                    </v-data-table>
                </div>
            </v-card>
        </v-dialog>

        

        <!-- POPUP CREATE EDIT -->


        <v-dialog v-model="dialog_createedit">
            <v-form v-model="valid" ref='formCreateEdit'>
                <v-card>
                    <v-toolbar dark color="menu">
                        <v-btn icon dark v-on:click="closedialog_createedit()">
                            <v-icon>close</v-icon>
                        </v-btn>
                        <v-toolbar-title v-html='id_data_edit == -1 ?"Add Guard":"Edit Guard"'></v-toolbar-title>

                    </v-toolbar>
                    {{id_data_edit}}
                    <v-stepper v-model="e6" vertical non-linear >

                        <!-- ==== STEPPER 1 ==== -->

                        <v-stepper-step :complete="e6 > 1" step="1" editable>
                            <h3>User Data</h3>
                        </v-stepper-step>

                        <v-stepper-content step="1" editable='id_data_edit != -1'>
                            
                            
                                    <v-text-field class="pa-2" :rules="this.$list_validation.max_req" v-model='input.name'  label="Name" counter=191></v-text-field>
                                

                            
                                    <v-text-field class="pa-2" :rules="this.$list_validation.numeric_req" v-model='input.age'  label="Age" counter=191></v-text-field>
                                


                            
                                    <v-select class='pa-2' :rules="this.$list_validation.selecttf_req" v-model='input.role_id' :items="ref_input.role" item-text='name' item-value='id' label="Select Role"></v-select>
                                

                            
                                    <v-text-field class="pa-2" :rules="this.$list_validation.max_req" v-model='input.username'  label="Username" counter=191></v-text-field>
                                

                            
                                    <v-text-field 
                                    v-if='id_data_edit == -1' 
                                    class="pa-2" 
                                    :rules="this.$list_validation.max_req" 
                                    v-model='input.password'  
                                    label="Password" 
                                    type="password"
                                    counter=191>
                                    </v-text-field>

                                    <v-text-field 
                                    v-if='id_data_edit != -1' 
                                    class="pa-2" 
                                    :rules="this.$list_validation.max_req" 
                                    v-model='input.password'  
                                    label="New Password" 
                                    type="password"
                                    counter=191>
                                    </v-text-field>
                                

                            
                                    <v-text-field class="pa-2" :rules="this.$list_validation.max_req" v-model='input.phone'  label="Phone" counter=191></v-text-field>
                                

                            
                                    <v-text-field class="pa-2" :rules="this.$list_validation.email_req" v-model='input.email'  label="Email" counter=191></v-text-field>
                               

                            
                                    
                                
                            
                            
                            

                            <v-btn color='menu' v-on:click='e6=2'>Continue</v-btn>
                              
                                
                            
                        </v-stepper-content>



                        <!-- ==== STEPPER 2 ==== -->

                        <v-stepper-step :complete="e6 > 2" step="2" editable><h3>Shifts Schedule</h3></v-stepper-step>

                        <v-stepper-content step="2">


                            <v-select v-model='temp_input.shifts.room' :items="ref_input.room" item-text='name' return-object label="Select Room"></v-select>

                             <v-select v-model='temp_input.shifts.time' :items="ref_input.time" item-text='name' return-object label="Select Time"></v-select>

                             <v-menu
                              ref="menu_date"
                              v-model="menu_date"
                              :close-on-content-click="false"
                              :nudge-right="40"
                              lazy
                              transition="scale-transition"
                              offset-y
                              full-width
                              max-width="290px"
                              min-width="290px"
                            >
                              <template v-slot:activator="{ on }">
                                <v-text-field
                                  v-model="temp_input.shifts.date"
                                  label="Date"
                                  hint="MM/DD/YYYY format"
                                  persistent-hint
                                  prepend-icon="event"
                                  @blur="date = temp_input.shifts.date"
                                  v-on="on"
                                ></v-text-field>
                              </template>
                              <v-date-picker v-model="temp_input.shifts.date" no-title @input="menu_date = false"></v-date-picker>
                            </v-menu>
                            
                            <v-text-field v-if='temp_input.id_edit_shifts==-1' style='margin-top:15px' :rules="this.$list_validation.numeric_req" v-model='repeat_time' label="How many days is it repeated?" required></v-text-field>


                            <v-toolbar flat color="white" >
                                
                                <v-spacer></v-spacer>
                                <v-btn v-if='temp_input.id_edit_shifts != -1' color="red" dark v-on:click='table_shift().canceledit()'>
                                    Cancel
                                </v-btn>
                                
                                <v-btn color="menu" dark v-on:click='table_shift().save()' v-html='temp_input.id_edit_shifts == -1?"Add to Table":"Save Changes"'>
                                </v-btn>
                            </v-toolbar>
                            


                            <v-toolbar flat color="white" >
                                <v-toolbar-title>Shifts Data</v-toolbar-title>
                                
                            </v-toolbar>
                            

                            <v-data-table
                                disable-initial-sort
                                :headers="[
                                {text:'No', value:'no'},
                                {text:'Room',value:'room'},
                                {text:'Time',value:'time'},
                                {text:'Date',value:'date'},
                                {text:'Action',align:'left',width:'15%',sortable:false}
                                ]"
                                :items="input.shifts"
                                class=""
                            >

                                <template v-slot:items="props">
                                    <td>{{ props.index + 1 }}</td>
                                    <td>{{ props.item.room.name }}</td>
                                    <td>{{ props.item.time.name }}</td>
                                    <td>{{ props.item.date }}</td>
                                    <td>
                                        <v-btn class='button-action' v-on:click='table_shift().showData(props.index)' color="primary" fab depressed small dark v-on="on">
                                            <v-icon small>edit</v-icon>
                                        </v-btn>
                                        <v-btn class='button-action' v-on:click='table_shift().delete(props.index)' color="red" fab small dark depressed>
                                            <v-icon small>delete</v-icon>
                                        </v-btn>

                                    </td>
                                </template>
                            </v-data-table>
                            
                            
                            

                        </v-stepper-content>



                        
                        
                        <v-btn v-on:click='save_data()' >submit</v-btn>

                        
                        
                        
                    </v-stepper>
                </v-card>
            </v-form>
        </v-dialog>

        <v-layout row class='bgwhite margintop10'>
            <v-flex xs6>
                <div class='marginleft30 margintop10'>
                    <v-icon class='icontitledatatable'>widgets</v-icon>
                    <h2 class='titledatatable'>Users Data</h2>
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
        >
        <template v-slot:items="props">
            <td>{{ props.item.no }}</td>
            <td>{{ props.item.name }}</td>
            <td class="text-xs-right">{{ props.item.age }}</td>
            <td class="text-xs-right">{{ props.item.role_name }}</td>
            <td class="text-xs-right">{{ props.item.username }}</td>
            <td class="text-xs-right">{{ props.item.phone }}</td>
            <td class="text-xs-right">{{ props.item.email }}</td>
            <td class="text-xs-right">{{ props.item.master_key }}</td>
            <td>
                <div class="text-xs-left">
                    <v-menu offset-y>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          color="menu"
                          dark
                          v-on="on"
                          class='btnaction'
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
import mxCrudChildForm from '../mixin/mxCrudChildForm';
export default {
    errorCaptured (err, vm, info) {
    this.error = `${err.stack}\n\nfound in ${info} of component`
    return false
  },
    data () {
        return {
            name_table:'users',
            header_api:{
                'Accept': 'application/json',
                'Content-type': 'multipart/form-data'
            },

            action_items: ['Edit', 'Shifts', 'Delete'],
            
            repeat_time : 1,
            


            valid:false,
            on:false,

            dialog_createedit:false,
            dialog_detailshifts:false,
            

            e6:1,

            id_data_edit:-1,

            
            input_before_edit:{ //variabel ini digunakan untuk menampung input sebelum di klik submit saat edit
                
            },
            input:{
                name:'',
                age:'',
                role_id:'',
                username:'',
                password:'',
                phone:'',
                master_key:'',
                email:'',
                
                shifts:[],
                
            },
            menu_date:null,

            temp_input:{
                id_edit_shifts:-1, //artinya add data,
                
                shifts:
                {
                    room:
                    {
                        id:null,
                        name:null,
                    },
                    time:
                    {
                        id:null,
                        name:null,
                    },
                    date:null,
                },
                
            },


            ref_input:
            {
                

            },
            

            

            headers: [
                { text: 'No', value: 'no'},
                { text: 'Name', value: 'name'},
                { text: 'Age', value: 'age', align:'right' },
                { text: 'Role', value: 'role', align:'right' },
                { text: 'Username', value: 'username', align:'right' },
                { text: 'Phone', value: 'phone', align:'right' },
                { text: 'Email', value: 'email', align:'right' },
                { text: 'Master Key', value: 'master_key', align:'right' },
                { text: 'Action', align:'left',sortable:false, width:'15%'},
            ],

            headers_popup_detailshifts : [
                { text: 'No', value:'no'},
                { text: 'user', value:'user'},
                { text: 'Room', value:'room'},
                { text: 'Time', value:'time'},
                { text: 'Date', value:'date'},
                { text: 'Status Node', value:'status_node'},
                { text: 'Message', value:'message'},
                { text: 'Token Shift', value:'token_shift'},

            ],

            


            data_table:[],
            search_data: null,

            popup_detailshifts :
            [
                {
                    room:null,
                    time:null,
                    date:null,
                    status_node:null,
                    message:null,
                    token_shift:null,
                },
               
            ],
            popup_search_detailshifts:null,

            
        }
    },
    methods: {
        add_zero_time(num)
        {
            var str = num.toString();
            if(str.length == 1)
            {
                return "0" + str;
            }
            else
            {
                return str;
            }
        },
        action_change(id_datatable,idx_action)
        {
            
            //console.log('action_change');
            //console.log(this.action_selected);
            // console.log(this.action_selected == 'Rack');
            if(idx_action == 0)
            {
                this.get_data_before_edit(id_datatable);
            }
            else if(idx_action == 1)
            {
                
                this.opendialog_detailshifts(id_datatable);
            }
            else if(idx_action == 2)
            {

                this.delete_data(id_datatable);
            }
            //this.action_selected[id_datatable] = null;
        },
        table_shift()
        {
            var self = this;
            return{
                showData(idx){ 
                    self.temp_input.shifts = JSON.parse(JSON.stringify(self.input.shifts[idx]));
                    self.temp_input.id_edit_shifts = idx;
                },
                clearTempInput(){
                    self.repeat_time = 1;
                    for (var key in self.temp_input.shifts)
                    {
                        if(self.temp_input.shifts[key])
                            self.temp_input.shifts[key] = null;
                    }
                },
                save(){ //bisa edit / add

                    var id_edit = JSON.parse(JSON.stringify(self.temp_input.id_edit_shifts));
                    console.log(id_edit);
                    if(!self.input.shifts)
                    {
                        self.input.shifts = [];
                    }
                    if(id_edit == -1)
                    {
                        for(var i = 0;i<self.repeat_time;i++)
                        {
                            var temp = JSON.parse(JSON.stringify(self.temp_input.shifts));
                            self.input.shifts.push(temp);
                            var temp_date = new Date(self.temp_input.shifts.date);
                            // add a day
                            temp_date.setDate(temp_date.getDate() + 1);
                            var str_temp_date = temp_date.getFullYear() + "-" + self.add_zero_time(temp_date.getMonth() + 1) + "-" + self.add_zero_time(temp_date.getDate());
                            
                            self.temp_input.shifts.date = str_temp_date;

                           // console.log(str_temp_date);
                        }
                        
                    }
                    else
                    {
                        self.input.shifts[id_edit] = JSON.parse(JSON.stringify(self.temp_input.shifts));
                        self.temp_input.id_edit_shifts = -1;

                    }
                    this.clearTempInput();
                },
                canceledit(){
                    this.clearTempInput();
                    self.temp_input.id_edit_shifts = -1;
                },
                delete(idx)
                {
                    self.input.shifts.splice(idx,1);
                }



            }
        },
        

        
        closedialog_detailshifts(){
            this.dialog_detailshifts = false;
        },
        opendialog_detailshifts(id_edit_popup_detailshifts)
        {

            this.dialog_detailshifts = true;
            this.get_popup_detailshifts(id_edit_popup_detailshifts);

        },

        


        
        
        showTable(r)
        {
            //console.log(r.data.items.goods[0]);

            this.data_table = r.data;

            
            
        },
        
        fill_select_master_data(r)
        {
            
            
            this.ref_input.role = r.data.roles;
            this.ref_input.room = r.data.rooms;
            this.ref_input.time = r.data.times;
            this.ref_input.status_node = r.data.status_nodes;
            
        },
        convert_data_input(r)
        {
            var temp_r = r.data.user;
            console.log(temp_r);
            this.input.name = temp_r.name;
            this.input.age = temp_r.age;
            this.input.role_id = temp_r.role.id;
            this.input.username = temp_r.username;
            this.input.password = temp_r.password;
            this.input.phone = temp_r.phone;
            this.input.email = temp_r.email;
            this.input.master_key = temp_r.master_key;
            //console.log('sampe conver data input');
            this.input.shifts = r.data.shifts;
            //console.log(this.input.shifts);
            

            
            this.input_before_edit = JSON.parse(JSON.stringify(this.input));
            
        },
        prepare_data_form()
        {
            //prepare data selalu dari this.input, tapi bandingkan dulu dengan this.input_before_edit
            
            
            const formData = new FormData();
            formData.append('token', localStorage.getItem('token'));




            if(this.id_data_edit != -1) //jika sedang diedit
            {

                //data yang harus dikirim saat update :
                //1. data goods yang BERUBAH SAJA
                //2. data goods_attribute dan goods_category AKHIR 
                //3. data material yang BERUBAH, DITAMBAH, & DIHAPUS
                //4. jika thumbnail awal ada, lalu thumbnail akhir kosong (user menghapus gambar thumbnail), maka tambahin data is_image_deleted = true, jika tidak is_image_deleted = false

                //step-step :
                //1. kirim data goods yang berubah
                

                if(this.input.name != this.input_before_edit.name) formData.append('name', this.input.name);

                if(this.input.age != this.input_before_edit.age) formData.append('age', this.input.age);

                if(this.input.role_id != this.input_before_edit.role_id) formData.append('role_id', this.input.role_id);

                if(this.input.username != this.input_before_edit.username) formData.append('username', this.input.username);

                if(this.input.password != this.input_before_edit.password) formData.append('password', this.input.password);

                if(this.input.phone != this.input_before_edit.phone) formData.append('phone', this.input.phone);

                if(this.input.email != this.input_before_edit.email) formData.append('email', this.input.email);

                

                

                //3. kirim data shift yang berubah, ditambah, dan dihapus
                
                //cek di input cocokin dengan input_before_edit
                //1. cek apakah ada id nya atau tidak, jika tidak memiliki id, pasti itu tambah baru
                //2. jika punya id, cocokan dengan input_before_edit, jika sama berarti tidak diedit, jika beda berarti diedit

                //temp adalah data dari input
                //temp2 adalah data dari input_before_edit
                var counteridx = 0;
                if(this.input.shifts)
                {

                    for(var i = 0;i<this.input.shifts.length;i++)
                    {

                        var temp = this.input.shifts[i];
                        if(temp.id == null)
                        {
                            
                            formData.append('shifts[' + counteridx + '][room_id]', temp.room.id);
                            formData.append('shifts[' + counteridx + '][time_id]', temp.time.id);
                            formData.append('shifts[' + counteridx + '][date]', temp.date);
                            formData.append('shifts[' + counteridx + '][type]', '1');
                            counteridx++;
                        }
                        else
                        {

                            //cocokan dengan input_before_edit
                            var edittrue = false;
                            for(var j = 0;j<this.input_before_edit.shifts.length;j++)
                            {
                                var temp2 = this.input_before_edit.shifts[i];
                                if(temp.id == temp2.id)
                                {
                                    if(temp.room.id != temp2.room.id || temp.time.id != temp2.time.id || temp.date != temp2.date) //jika ada salah satu saja yang berbeda, maka ini pasti diedit
                                    {
                                        edittrue = true;
                                    }
                                    break;
                                }
                            }

                            if(edittrue)
                            {
                                formData.append('shifts[' + counteridx + '][id]', temp.id);
                                formData.append('shifts[' + counteridx + '][room_id]', temp.room.id);
                                formData.append('shifts[' + counteridx + '][time_id]', temp.time.id);
                                formData.append('shifts[' + counteridx + '][date]', temp.date);
                                formData.append('shifts[' + counteridx + '][type]', '0');
                                counteridx++;
                            }

                        }
                    }
                }

                //cek di input_before_edit cocokin dengan input
                //1. jika ada data dengan id yang tidak ada di data input, berarti data tersebut pasti dihapus
                if(this.input_before_edit.shifts)
                {
                    for(var i = 0;i<this.input_before_edit.shifts.length;i++)
                    {
                        var deletetrue = true;
                        for(var j=0;j<this.input.shifts.length;j++)
                        {
                            if(this.input.shifts[j].id == this.input_before_edit.shifts[i].id)
                            {
                                deletetrue = false;
                                break;
                            }
                        }

                        if(deletetrue)
                        {
                            formData.append('shifts[' + counteridx + '][id]', this.input_before_edit.shifts[i].id);
                            formData.append('shifts[' + counteridx + '][type]', '-1');
                            counteridx++;
                        }
                    }

                }

                

                
                
               
                formData.append('_method', 'patch');

            }
            else //jika sedang add
            {

                //data-data yang harus dikirim : 
                //1. semua data goods
                //2. semua data attribute,category, dan material

                //step-step : 
                //1. kirim data goods
                formData.append('name', this.input.name);
                formData.append('age', this.input.age);
                formData.append('role_id', this.input.role_id);
                formData.append('username', this.input.username);
                formData.append('password', this.input.password);
                formData.append('phone', this.input.phone);
                formData.append('master_key', this.input.master_key);
                formData.append('email', this.input.email);
               

                //2. kirim data attribute,category, dan material

                for(var i = 0;i<this.input.shifts.length;i++)
                {
                    formData.append('shifts[' + i + '][room_id]',this.input.shifts[i].room.id);
                    formData.append('shifts[' + i + '][time_id]',this.input.shifts[i].time.id);
                    formData.append('shifts[' + i + '][date]',this.input.shifts[i].date);

                }

                

            }

            
            //cek dulu
            // for (var pair of formData.entries()) {
            //     console.log(pair[0]+ ', ' + pair[1]); 
            // }

            //4. masukan token
            
            return formData;
        },
        
        
        


        get_popup_detailshifts(id_edit_popup_detailshifts){
            axios.get('api/admin/users/' + id_edit_popup_detailshifts + '/shifts',{
                    params:{
                        token: localStorage.getItem('token')
                    }
            }).then((r) => {
                this.popup_detailshifts = r.data.data;
            })
        },


       


    },
    mounted(){
        
        this.get_data();
        this.get_master_data();
       // console.log(this.add_zero_time('1'))
       // console.log(this.add_zero_time('10'))
        
        //this.testing_input();

    },
    mixins:[
        mxCrudChildForm,
    ],
}
</script>

