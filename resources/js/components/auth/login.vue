<script setup>
    import {reactive, ref} from 'vue';
    import {useRouter} from 'vue-router';

    const router = useRouter();
    let form = reactive({
        email : '',
        password : ''
    })
    let error = ref('');
    const login = async() => {
        return axios.post('/api/auth/login', form)
        .then(response => {
            if(response.status === 200){
                localStorage.setItem('token', response.data.token);
                localStorage.setItem('role', response.data.logged_in_user_type);
                localStorage.setItem('user_info', JSON.stringify(response.data.user_info));

                if(response.data.logged_in_user_type == 'admin'){
                  router.push('/admin/home');
                }else if(response.data.logged_in_user_type == 'employee'){
                  router.push('/employee/home');
                }else if(response.data.logged_in_user_type == 'store-executive'){
                  router.push('/store_executive/home');
                }
            }else{

            }
        })
        .catch(function (error) {
          // handle error
          error.value = error?.response?.data?.message;
        });
    }
</script>
<template>
    <div class="login-container">
      <p class="error" v-if="error">{{error}}</p>
      <h1>Login</h1>
      <form @submit.prevent="login">
        <p class="error" v-if="error">{{error}}</p>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" v-model="form.email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" v-model="form.password" required>

        <button type="submit">Login</button>
      </form>
    </div>
</template>
<style>
body {
  background-color: #f1f1f1;
}
.error {
  color:#ff8e8e;
}
.login-container {
  width: 400px;
  margin: auto;
  margin-top: 100px;
  background-color: #fff;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3);
}

h1 {
  text-align: center;
  color: #555;
}

form {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: bold;
  margin-bottom: 5px;
  color: #555;
}

input {
  padding: 10px;
  border-radius: 3px;
  border: 1px solid #ccc;
  margin-bottom: 20px;
}

button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 3px;
  font-size: 16px;
  cursor: pointer;
}

button:hover {
  background-color: #3e8e41;
}

</style>