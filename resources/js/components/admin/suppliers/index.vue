<script setup>
    import {onMounted, ref} from 'vue'
    import {useRouter} from 'vue-router';
    const router = useRouter();
    
    let suppliers = ref([]);
    let form = ref({
        id : 0,
        name : '',
        pohone : '',
        email : '',
        address : '',
        status : 'active'
    })
    let isEditMode = ref(false);
    let isDeleteMode = ref(false);
    let singleItem = ref({});
    let add_supplier_error = ref("");
    let isEditModalShow = ref(false);
    let isDetailModalShow = ref(false);
    let isDeleteModalShow = ref(false);
    let singleItemDetail = ref({})

    const logout = () => {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        localStorage.removeItem('user_info');
        router.push('/');
    }

    
    const addEditModalShow = () => {
        isEditModalShow.value = !isEditModalShow.value;
    }
    const showDetailModal = () => {
        isDetailModalShow.value = !isDetailModalShow.value;
    }
    const showDeleteModal = () => {
        isDeleteModalShow.value = !isDeleteModalShow.value;
    }
    const showEditItem = (id) => {
        isEditModalShow.value = !isEditModalShow.value;
        isEditMode.value = true;
        console.log(suppliers.value);
        let single_supplier = suppliers.value.find(supplier => supplier.id == id);
        form.value = {
            id : single_supplier.id,
            name : single_supplier.name,
            phone : single_supplier.phone,
            email : single_supplier.email,
            address : single_supplier.address,
            status : single_supplier.status
        }
    }
    const closeAddEditModal = () => {
        form.value = {
            id : 0,
            name : '',
            phone : '',
            email : '',
            address : '',
            status : 'active'
        };
        isEditModalShow.value = false;
        isEditMode.value = false;
    }
    onMounted(async () => {
        getItems()
    })

    const getItems = async () => {

        var config = {
            method: 'get',
            url: '/api/suppliers',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            suppliers.value = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const getItem = async (id) => {

        var config = {
            method: 'get',
            url: `/api/suppliers/${id}`,
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            singleItem.value = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const add_edit_supplier = () => {
        add_supplier_error.value = "";
        
        if(isEditMode.value){
            var data = {
                'name': form.value.name,
                'phone': form.value.phone,
                'email': form.value.email,
                'address': form.value.address,
                'status': form.value.status 
            };
        }else{
            var data = new FormData();
            data.append('name', form.value.name);
            data.append('phone', form.value.phone);
            data.append('email', form.value.email);
            data.append('address', form.value.address);
            data.append('status', form.value.status);

        }


        var config = {
            method: (isEditMode.value) ? 'put' : 'post',
            url: (isEditMode.value) ? `/api/suppliers/${form.value.id}` : '/api/suppliers',
            headers: (isEditMode.value) ? { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            } : { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data : data
        };

        axios(config)
        .then(function (response) {
            form.value = {
                id : 0,
                name : '',
                phone : '',
                email : '',
                address : '',
                status : 'active'
            };
            add_supplier_error.value = "";
            isEditModalShow.value = !isEditModalShow.value;
            getItems();
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const confirmDelete = () => {
        if(isDeleteMode.value){
            var config = {
                method: 'delete',
                url: `/api/suppliers/${singleItemDetail.value.id}`,
                headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            };

            axios(config)
            .then(function (response) {
                // console.log(JSON.stringify(response.data));
                getItems();
                deleteCancel();
            })
            .catch(function (error) {
                console.log(error);
            });
        }
    }
    const showDetailItem = (id) => {
        showDetailModal();
        singleItemDetail.value = suppliers.value.find((supplier) => supplier.id === id);
    }
    const deleteItem = (id) => {
        showDeleteModal();
        singleItemDetail.value = suppliers.value.find((supplier) => supplier.id === id);
        isDeleteMode.value = true;
    }
    const deleteCancel = () => {
        singleItemDetail.value = {};
        isDeleteMode.value = false;
        isDeleteModalShow.value = false;
    }
    const showStatus = (status) => {
        if(status == "active"){
            return "Active";
        }else if(status == "inactive"){
            return "Inactive";
        }
    }
</script>
<template>
    <header>
		<h1>MBM</h1>
		<div id="user-dropdown" class="dropdown">
			
			<p class="cursor-pointer" @click="logout()">Logout</p>
			
		</div>
	</header>

	<div class="container">
		<div class="sidebar">
			<ul>
				<li><router-link to="/admin/home">Requisitions</router-link></li>
				<li><router-link to="/admin/items">Items</router-link></li>
				<li><router-link to="/admin/suppliers">Suppliers</router-link></li>
			</ul>
		</div>

		<div class="content">
            <div class="main_content_header">
			    <h2>Suppliers</h2>
                <button class="smart-button" @click="addEditModalShow()">Add</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <tr v-for="(supplier, index, key) in suppliers" :key="supplier.id" v-if="suppliers.length > 0">
                        <td>{{index + 1}}</td>
                        <td>{{supplier.name}}</td>
                        <td>{{showStatus(supplier.status)}}</td>
                        <td>
                            <i class="fas fa-trash-alt cursor-pointer" @click="deleteItem(supplier.id)"></i>
                            <i class="fas fa-info-circle cursor-pointer ml_10" @click="showDetailItem(supplier.id)"></i>
                            <i class="fas fa-edit cursor-pointer ml_10" @click="showEditItem(supplier.id)"></i>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
		</div>
	</div>

    <div id="modal" class="modal" :class="[isEditModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="closeAddEditModal">&times;</span>
            <h2>Add</h2>
            <p class="error_text" :class="[add_supplier_error != '' ? 'show' : 'hide']">{{add_supplier_error}}</p>
            <div>
                <form @submit.prevent="add_edit_supplier">
                    <label for="name">Name</label>
                    <input v-model="form.name" type="text" id="name" name="name"><br>
                    <label for="phone">Phone</label>
                    <input v-model="form.phone" type="text" id="phone" name="phone"><br>
                    <label for="email">Email</label>
                    <input v-model="form.email" type="text" id="email" name="email"><br>
                    <label for="address">Address</label>
                    <input v-model="form.address" type="text" id="address" name="address"><br>
                    <label for="status">Status</label>
                    <select v-model="form.status" id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select><br><br>
                    <button type="submit">Submit</button>
                </form>

            </div>
        </div>
    </div>


    <div id="modal" class="modal" :class="[isDetailModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="isDetailModalShow = false">&times;</span>
            <h2>Item Details</h2>
            <div>
                
                <div class="added_suppliers">
                    <p class="mt_10 mb_10"><span class="bold">Name :</span> {{singleItemDetail.name}}</p>
                    <p class="mt_10 mb_10"><span class="bold">Phone :</span> {{singleItemDetail.phone}}</p>
                    <p class="mt_10 mb_10"><span class="bold">Email :</span> {{singleItemDetail.email}}</p>
                    <p class="mt_10 mb_10"><span class="bold">Address :</span> {{singleItemDetail.address}}</p>
                    <p class="mt_10 mb_10"><span class="bold">Status :</span> {{showStatus(singleItemDetail.status)}}</p>
                </div>

            </div>
        </div>
    </div>

    <div id="modal" class="modal" :class="[isDeleteModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="deleteCancel">&times;</span>
            <h2>Confirmation</h2>
            <div>
                <h3 class="mt_10 mb_10">Are you sure to delete this supplier?</h3>
                <p class="mt_10 mb_10"><span class="bold">Name :</span> {{singleItemDetail.name}}</p>
                <p class="mt_10 mb_10"><span class="bold">Phone :</span> {{singleItemDetail.phone}}</p>
                <p class="mt_10 mb_10"><span class="bold">Email :</span> {{singleItemDetail.email}}</p>
                <p class="mt_10 mb_10"><span class="bold">Address :</span> {{singleItemDetail.address}}</p>
                <p class="mt_10 mb_10"><span class="bold">Status :</span> {{showStatus(singleItemDetail.status)}}</p>
                <button class="smart-button" @click="confirmDelete()">Confirm</button>

            </div>
        </div>
    </div>
    

</template>
<style>
    @import '../../layouts/base.css'
</style>