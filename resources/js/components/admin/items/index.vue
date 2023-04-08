<script setup>
    import {onMounted, ref} from 'vue'
    import {useRouter} from 'vue-router';
    const router = useRouter();
    
    let items = ref([]);
    let form = ref({
        id : 0,
        name : '',
        description : '',
        status : 'active'
    })
    let isEditMode = ref(false);
    let isDeleteMode = ref(false);
    let singleItem = ref({});
    let add_item_error = ref("");
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
        console.log(items.value);
        let single_item = items.value.find(item => item.id == id);
        form.value = {
            id : single_item.id,
            name : single_item.name,
            description : single_item.description,
            status : single_item.status
        }
    }
    const closeAddEditModal = () => {
        form.value = {
            id : 0,
            name : '',
            description : '',
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
            url: '/api/items',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            items.value = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const getItem = async (id) => {

        var config = {
            method: 'get',
            url: `/api/items/${id}`,
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
    const add_edit_item = () => {
        add_item_error.value = "";
        
        if(isEditMode.value){
            var data = {
                'name': form.value.name,
                'description': form.value.description,
                'status': form.value.status 
            };
        }else{
            var data = new FormData();
            data.append('name', form.value.name);
            data.append('description', form.value.description);
            data.append('status', form.value.status);

        }


        var config = {
            method: (isEditMode.value) ? 'put' : 'post',
            url: (isEditMode.value) ? `/api/items/${form.value.id}` : '/api/items',
            headers: (isEditMode.value) ? { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Content-Type': 'application/x-www-form-urlencoded'
            } : { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            data : data
        };

        axios(config)
        .then(function (response) {
            form.value = {
                id : 0,
                name : '',
                description : '',
                status : 'active'
            };
            add_item_error.value = "";
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
                url: `/api/items/${singleItemDetail.value.id}`,
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
        singleItemDetail.value = items.value.find((item) => item.id === id);
    }
    const deleteItem = (id) => {
        showDeleteModal();
        singleItemDetail.value = items.value.find((item) => item.id === id);
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
			    <h2>Items</h2>
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

                    <tr v-for="(item, index, key) in items" :key="item.id" v-if="items.length > 0">
                        <td>{{index + 1}}</td>
                        <td>{{item.name}}</td>
                        <td>{{showStatus(item.status)}}</td>
                        <td>
                            <i class="fas fa-trash-alt cursor-pointer" @click="deleteItem(item.id)"></i>
                            <i class="fas fa-info-circle cursor-pointer ml_10" @click="showDetailItem(item.id)"></i>
                            <i class="fas fa-edit cursor-pointer ml_10" @click="showEditItem(item.id)"></i>
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
            <p class="error_text" :class="[add_item_error != '' ? 'show' : 'hide']">{{add_item_error}}</p>
            <div>
                <form @submit.prevent="add_edit_item">
                    <label for="name">Name</label>
                    <input v-model="form.name" type="text" id="name" name="name"><br>
                    <label for="description">Description</label>
                    <textarea v-model="form.description" id="description" name="description"></textarea><br>
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
                
                <div class="added_items">
                    <p class="mt_10 mb_10"><span class="bold">Name :</span> {{singleItemDetail.name}}</p>
                    <p class="mt_10 mb_10"><span class="bold">Description :</span> {{singleItemDetail.description}}</p>
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
                <h3 class="mt_10 mb_10">Are you sure to delete this item?</h3>
                <p class="mt_10 mb_10"><span class="bold">Name :</span> {{singleItemDetail.name}}</p>
                <p class="mt_10 mb_10"><span class="bold">Description :</span> {{singleItemDetail.description}}</p>
                <p class="mt_10 mb_10"><span class="bold">Status :</span> {{showStatus(singleItemDetail.status)}}</p>
                <button class="smart-button" @click="confirmDelete()">Confirm</button>

            </div>
        </div>
    </div>
    

</template>
<style>
    @import '../../layouts/base.css'
</style>