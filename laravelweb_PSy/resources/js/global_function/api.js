import axios from 'axios'

const listApi = {
    "login" : {
        method : 'POST',
        url : "/api/auth/login",
        withToken : false,
        attributeError : ['username', 'password'] //if error happen (ex : username is null), then server will give response with this attribute
    }
};

const flagNeedAuthentication = "Your are not admin";

const message = 
{
    "connection" : "Check your internet connection !",
    "shouldLogin" : "You are not authenticated ! You should login !"
}

export default {
    constValue : 
    {
        listApi : listApi,
        flagNeedAuthentication : flagNeedAuthentication,
        message, message
    },
    async backToLogin()
    {
        localStorage.setItem("token", null);
        localStorage.setItem("user", null);
        this.$router.push({ name: "home" }).catch(()=>{});
    },

    processMessage(message, nameApi) //this function is process message to readable message (not object)
    {
        var result = "";
        var listMessage = [];
        var attribute = listApi[nameApi]['attributeError'];
        for(var i = 0;i<attribute.length;i++)
        {
            if(message[attribute[i]])
            {
                listMessage.push(message[attribute[i]].toString());
            }
        }
        if(listMessage.length > 0)
        {
            result = listMessage.toString();
        }
        else
        {
            result = message.toString();
        }
        
        if(result.includes("[object Object]"))
        {
            result = "Please try again";
        }
        return result;
    },
    processResponse(r, ignoreAlert, nameApi)
    {
        var result = 
        {
            "success" : false,
            "result" : null,
            "is_json" : false,
            "message" : "",
            "already_display_alert" : false,
        };
        if(r.data)
        {
            if(r.data.error == false)
            {
                //if request success, then return success information + data
                result['is_json'] = true;
                result['success'] = true;
                result['result'] = r.data;
            }
            else
            {
                //if request failed, then return failed information
                result['is_json'] = true;
                result['success'] = false;
                if(r.data.message)
                {
                    if(r.data.message.toString() == flagNeedAuthentication)
                    {
                        result['message'] = message.shouldLogin; 
                        this.backToLogin();
                    }
                    else
                    {
                        result['message'] = this.processMessage(r.data.message, nameApi);
                        if(!ignoreAlert)
                        {
                            swal('Failed', result['message'], 'error');
                            result['already_display_alert'] = true;
                        }
                    }
                }
                else
                {
                    result['is_json'] = false;
                    result['success'] = false;
                    result['message'] = message.connection;
                    if(!ignoreAlert)
                    {
                        swal('Failed', message.connection, 'error');
                        result['already_display_alert'] = true;
                    }
                }
            }
        }
        else
        {
            result['is_json'] = false;
            result['success'] = false;
            result['message'] = message.connection;
            if(!ignoreAlert)
            {
                swal('Failed', message.connection, 'error');
                result['already_display_alert'] = true;
            }
        }
        if(result['message'].includes("[object Object]"))
        {
            result['message'] = "Please try again";
        }
        return result;
    },

    processErrorResponse(error, ignoreAlert, nameApi)
    {
        var result = 
        {
            "success" : false,
            "result" : null,
            "is_json" : false,
            "message" : "",
            "already_display_alert" : false,
        };
        if(error.response)
        {
            if(error.response.data)
            {
                if(error.response.data.message)
                {
                    //if request failed, then return failed information
                    result['is_json'] = true;
                    if(error.response.data.message.toString() == flagNeedAuthentication)
                    {
                        result['message'] = message.shouldLogin; 
                        this.backToLogin();
                    }
                    else
                    {
                        result['message'] = this.processMessage(error.response.data.message, nameApi); 
                        if(!ignoreAlert)
                        {
                            swal('Failed', result['message'], 'error');
                            result['already_display_alert'] = true;
                        }
                    }
                    
                }
                else
                {
                    result['is_json'] = false;
                    result['message'] = message.connection;
                    if(!ignoreAlert)
                    {
                        swal('Failed', message.connection, 'error');
                        result['already_display_alert'] = true;
                    }
                }
            }
            else
            {
                result['is_json'] = false;
                result['message'] = message.connection;
                if(!ignoreAlert)
                {
                    swal('Failed', message.connection, 'error');
                    result['already_display_alert'] = true;
                }
            }
        }
        else
        {
            result['is_json'] = false;
            result['message'] = message.connection;
            if(!ignoreAlert)
            {
                swal('Failed', message.connection, 'error');
                result['already_display_alert'] = true;
            }
        }
        return result;
        
    },
    async requestApi(nameApi, bodyRequest = {}, ignoreAlert = false, paramsRequest = {}, customUrl = null, customMethod = null,customWithToken = null) {
        var resultReturn = {
            "success": false, //default value for success attribute. if API request is failed, then it will false, else true.
            "result": null, //default value for result
            "already_display_alert": false, //default value for already_display_alert. if this function show an alert, then it will true, else false.
            "is_json": false, //default value for is_json. if this result is json, then is_json will true, else false.
            "message" : "", //default value for message. If response error and return a message, then that value will store to message attribute
            "list_errors" : [], //default value for list_errors. If response error and return array of error (string), then that value will store to list_errors
        };

        var url = "";
        var method = "";
        var withToken = false;
        if(nameApi != null)
        {
            url = listApi[nameApi]['url'];
            method = listApi[nameApi]['method'];
            withToken = listApi[nameApi]['withToken'];
        }
        else
        {
            if(customUrl != null && customMethod != null && customWithToken != null)
            {
                url = customUrl;
                method = customMethod;
                withToken = customWithToken;
            }
            else
            {
                if(!ignoreAlert)
                {
                    swal('Failed', "Error Api.js (1)", 'error');
                    resultReturn['already_display_alert'] = true;
                }
                return resultReturn;
            }

        }

        //1. prepare token
        var token = "";
        if(withToken) //if request requires a token
        {
            //get token from localStorage. If localStorage doesn't have token, then redirect to login page (home)
            var tokenRequest = localStorage.getItem('token');
            if(tokenRequest != null)
            {
                token = tokenRequest;
            }
            else
            {
                this.$router.push({ name: "login" }).catch(()=>{}); //back to login
            }
        }


        //2. prepare params and formData
        var params = null;
        var formData = null;
        if(method == "GET")
        {
            params = paramsRequest;
            if(withToken)
            {
                params['token'] = token;
            }
        }
        else if (method == "POST")
        {
            formData = new FormData();
            for (var key in bodyRequest) {
                // check if the property/key is defined in the object itself, not in parent
                if (bodyRequest.hasOwnProperty(key)) {     
                    formData.append(key, bodyRequest[key]);
                }
            }
        }
        else
        {
            if(!ignoreAlert)
            {
                swal('Failed', "Error Api.js (2)", 'error');
                resultReturn['already_display_alert'] = true;
            }
            return resultReturn;
        }
        
        //3. request GET / POST
        if(method == "GET")
        {
            await axios
            .get(url, {params:params})
            .then(r => {
                var tempResult = this.processResponse(r, ignoreAlert, nameApi);
                resultReturn['success'] = tempResult['success'];
                resultReturn['is_json'] = tempResult['is_json'];
                resultReturn['result'] = tempResult['result'];
                resultReturn['message'] = tempResult['message'];
                resultReturn['already_display_alert'] = tempResult['already_display_alert'];
                return resultReturn;
            }).catch((error) => {
                //if request failed, then return failed information
                var tempResult = this.processErrorResponse(error, ignoreAlert, nameApi);
                resultReturn['success'] = tempResult['success'];
                resultReturn['is_json'] = tempResult['is_json'];
                resultReturn['result'] = tempResult['result'];
                resultReturn['message'] = tempResult['message'];
                resultReturn['already_display_alert'] = tempResult['already_display_alert'];
                return resultReturn;
            });
        }
        else if(method == "POST")
        {
            //request post with axios
            await axios
            .post(url, formData, {params:params})
            .then(r => {
                var tempResult = this.processResponse(r, ignoreAlert, nameApi);
                resultReturn['success'] = tempResult['success'];
                resultReturn['is_json'] = tempResult['is_json'];
                resultReturn['result'] = tempResult['result'];
                resultReturn['message'] = tempResult['message'];
                resultReturn['already_display_alert'] = tempResult['already_display_alert'];
                return resultReturn;
            }).catch((error) => {
                //if request failed, then return failed information
                var tempResult = this.processErrorResponse(error, ignoreAlert, nameApi);
                resultReturn['success'] = tempResult['success'];
                resultReturn['is_json'] = tempResult['is_json'];
                resultReturn['result'] = tempResult['result'];
                resultReturn['message'] = tempResult['message'];
                resultReturn['already_display_alert'] = tempResult['already_display_alert'];
                return resultReturn;
            });
        }
        else
        {
            if(!ignoreAlert)
            {
                swal('Failed', "Error Api.js (3)", 'error');
                resultReturn['already_display_alert'] = true;
            }
            return resultReturn;
        }
        return resultReturn;
    }
}
