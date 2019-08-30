<template>
    <div class='bg-login'>
        <v-content>
            <v-container fluid fill-height>
                <v-layout align-center justify-center>
                    <v-flex xs12 sm8 md4>
                        <v-form @submit.prevent="req_login">
                            <v-card class="elevation-12">
                                <v-toolbar dark color="primary">
                                    <v-toolbar-title>Login form</v-toolbar-title>

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
                                    <v-btn type="submit" color="primary" v-on:click="req_login" >Login</v-btn>
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
    		req_login(){

    			axios.post('/api/auth/login',{
    				username:this.in_username,
    				password:this.in_password,
                    isAdmin:true,
    			}).then(r => {
                    //console.log(response);
                    console.log(r.data.message);
                    if(r.data.message == "Your are not admin" || r.data.message == 'Login Failed')
                    {
                        this.in_password = '';
                        this.in_username = '';
                        this.message_error = 'Wrong username/password !';


                    }
                    else 
                    {
                        localStorage.setItem('token', r.data.access_token)
                        localStorage.setItem('user', JSON.stringify(r.data.user))
                        this.in_password = '';
                        this.in_username = '';
                        this.message_error = '';
                        this.$router.replace('/');
                        
                    }
                });
    		}
    	}
    }
</script>


