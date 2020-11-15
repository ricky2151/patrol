<template>
    <div class='bg-login'>
        <v-content>
            <v-container fluid fill-height>
                <v-layout align-center justify-center>
                    <v-flex xs12 sm8 md4>
                        <v-form @submit.prevent="req_login">
                            <v-card class="elevation-12">
                                <v-toolbar dark color="primary">
                                    <v-toolbar-title>Patrolee Admin Dashboard</v-toolbar-title>

                                </v-toolbar>
                                <v-card-text>
                                    <v-form>
                                        
                                        <v-text-field v-model="in_username" prepend-icon="person" name="login" label="Username" type="text"></v-text-field>
                                        <v-text-field v-model="in_password" prepend-icon="lock" name="password" label="Password" id="password" type="password"></v-text-field>
                                        <label style='color:red' v-if='message_error.length > 0'>{{message_error}}</label>
                                    </v-form>
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="primary" v-on:click="req_login" >Masuk</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-form>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </div>
</template>

<script>
import api from '../global_function/api';

export default {
    data()
    {
        return {
            message_error:'',
            in_password:'',
            in_username:'',
        }
    },
    methods:
    {
        async req_login(){
            console.log('request login');
            var body = {
                username : this.in_username,
                password : this.in_password,
                isAdmin : true,
            }
            var result = await api.requestApi('login', body);
            console.log('loh cke result');
            console.log(result);
            if(result['success'])
            {
                
                localStorage.setItem('token', result['result']['access_token'])
                localStorage.setItem('user', JSON.stringify(result['result']['user']))
                this.in_password = '';
                this.in_username = '';
                this.message_error = '';
                this.$router.replace('/');
            }
            else
            {
                this.in_password = '';
                this.in_username = '';
                if(result['message'] != api.constValue.message.connection)
                {
                    this.message_error = 'Wrong username/password !';
                }
            }



            
        }
    }
}
</script>


