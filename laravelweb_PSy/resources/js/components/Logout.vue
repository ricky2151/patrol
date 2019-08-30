<template>

</template>
<script>
export default {
    created(){
        axios.post('/api/auth/logout', {
            token: localStorage.getItem('token'),
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-type': 'application/json'
            }
        }).then(() => {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            this.$router.replace('login');
        })
    },
}
</script>
