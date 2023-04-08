<script setup>
    import {onMounted, ref} from 'vue'
    import {useRouter} from 'vue-router';
    const router = useRouter();
    
    let requisitions = ref([]);
    let items = ref([]);
    let items_in_bucket = ref([]);
    let singleRequisition = ref({});
    let add_requisition_error = ref("");
    let bucket_form = ref({
        bucket_item_id : "",
        bucket_item_quantity : 0,
    });
    let isEditModalShow = ref(false);
    let isDetailModalShow = ref(false);
    let singleRequisitionDetail = ref({})

    const logout = () => {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        localStorage.removeItem('user_info');
        router.push('/');
    }

    const add_items_in_bucket = () => {
        let found_item = items.value.find(item => item.id == bucket_form.value.bucket_item_id);
        found_item.quantity = Number(bucket_form.value.bucket_item_quantity);
        items_in_bucket.value.push(found_item);

        bucket_form.value.bucket_item_id = ""
        bucket_form.value.bucket_item_quantity = 0;
    }
    const add_requisition = () => {
        add_requisition_error.value = "";
        if(items_in_bucket.value.length == 0){
            add_requisition_error.value = "There is no item!";
            return false;
        }
        var data = new FormData();
        items_in_bucket.value.map((item) => {
            data.append('item_id[]', item.id);
            data.append('quantity[]', item.quantity);
        });
        var config = {
            method: 'post',
            url: '/api/requisitions',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            data : data
        };

        axios(config)
        .then(function (response) {
            items_in_bucket.value = {};
            add_requisition_error.value = "";
            isEditModalShow.value = !isEditModalShow.value;
            getRequisitions();
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const remove_from_bucket = (bucket_item) => {
        let new_items_in_bucket = items_in_bucket.value.filter((item) => item.id != bucket_item.id);
        items_in_bucket.value = new_items_in_bucket;
    }
    const addEditModalShow = () => {
        isEditModalShow.value = !isEditModalShow.value;
    }
    const showDetailModal = () => {
        isDetailModalShow.value = !isDetailModalShow.value;
    }
    onMounted(async () => {
        getRequisitions()
        getItems()
        
    })

    const getRequisitions = async () => {

        var config = {
            method: 'get',
            url: '/api/requisitions',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            requisitions.value = response.data;

        })
        .catch(function (error) {
            console.log(error);
        });
    }
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
    const getRequisition = async (id) => {

        var config = {
            method: 'get',
            url: `/api/requisitions/${id}`,
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            singleRequisition.value = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const showDetailRequisition = (id) => {
        showDetailModal();
        singleRequisitionDetail.value = requisitions.value.find((requisition) => requisition.id === id);
    }
    const showStatus = (status) => {
        if(status == "new"){
            return "New";
        }else if(status == "approved"){
            return "Approved";
        }else if(status == "rejected"){
            return "Rejected";
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
				<li><router-link to="/employee/home">Requisitions</router-link></li>
			</ul>
		</div>

		<div class="content">
            <div class="main_content_header">
			    <h2>Requisitions</h2>
                <button class="smart-button" @click="addEditModalShow()">Add</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Status</th>
                        <th>Item Types Number</th>
                        <th>Requisition Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <tr v-for="requisition in requisitions" :key="requisition.id" v-if="requisitions.length > 0">
                        <td>{{requisition.id}}</td>
                        <td>{{showStatus(requisition.status)}}</td>
                        <td>{{requisition.details.length}}</td>
                        <td>{{new Date(requisition.created_at).toISOString().slice(0, 10)}}</td>
                        <td>
                            <i class="fas fa-info-circle cursor-pointer" @click="showDetailRequisition(requisition.id)"></i>
                            <!-- <button to="/employee/home" class="btn-edit"><i class="fas fa-edit"></i></button> -->
                        </td>
                    </tr>
                    
                </tbody>
            </table>
		</div>
	</div>

    <div id="modal" class="modal" :class="[isEditModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="isEditModalShow = false">&times;</span>
            <h2>Add</h2>
            <p class="error_text" :class="[add_requisition_error != '' ? 'show' : 'hide']">{{add_requisition_error}}</p>
            <div>
                <div class="container_item">
                    <div class="row header">
                        <div class="column item">Item</div>
                        <div class="column quantity">Quantity</div>
                        <div class="column action">Action</div>
                    </div>
                    <div class="row">
                        <div class="column select">
                        <select v-model="bucket_form.bucket_item_id">
                            <option value="">Select Item</option>
                            <option v-for="(item, index) in items" :value="item.id" :key="index">{{ item.name }}</option>
                        </select>
                        </div>
                        <div class="column input">
                        <input v-model="bucket_form.bucket_item_quantity" type="number" placeholder="Quantity">
                        </div>
                        <div class="column button">
                        <button @click="add_items_in_bucket()">Add to List</button>
                        </div>
                    </div>
                </div>

                <div class="added_items">
                    <table class="small_table">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(bucket_item, index, key) in items_in_bucket" :key="key" v-if="items_in_bucket.length > 0">
                                <td>{{index + 1}}</td>
                                <td>{{bucket_item.name}}</td>
                                <td>{{bucket_item.quantity}}</td>
                                <td><i class="fa fa-trash-alt cursor-pointer" @click="remove_from_bucket(bucket_item)"></i></td>
                            </tr>
                        </tbody>
                    </table>
                    <button @click="add_requisition()">Add Requisition</button>
                </div>

            </div>
        </div>
    </div>


    <div id="modal" class="modal" :class="[isDetailModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="isDetailModalShow = false">&times;</span>
            <h2>Requisition Details</h2>
            <div>
                
                <div class="added_items">
                    <table class="small_table">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Name</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(requisition_detail, index, key) in singleRequisitionDetail.details" :key="key" v-if="singleRequisitionDetail?.details?.length > 0">
                                <td>{{index + 1}}</td>
                                <td>{{requisition_detail.item.name}}</td>
                                <td>{{requisition_detail.quantity}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    

</template>
<style>
    @import '../../layouts/base.css'
</style>