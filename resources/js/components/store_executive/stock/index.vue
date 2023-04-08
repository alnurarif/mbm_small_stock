<script setup>
    import {onMounted, ref} from 'vue'
    import {useRouter} from 'vue-router';
    const router = useRouter();
    
    let stock = ref([]);
    let suppliers = ref([]);
    let items = ref([]);
    let form = ref({
        supplier_id : "",
        item_id : "",
        quantity : 0,
        price : 0
    })
    let isEditMode = ref(false);
    let isDeleteMode = ref(false);
    let singleReceive = ref({});
    let add_receive_error = ref("");
    let isEditModalShow = ref(false);
    let isDetailModalShow = ref(false);
    let isDeleteModalShow = ref(false);
    let singleReceiveDetail = ref({})

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
    const showEditReceive = (id) => {
        isEditModalShow.value = !isEditModalShow.value;
        isEditMode.value = true;
        console.log(stock.value);
        let single_receive = stock.value.find(receive => receive.id == id);
        form.value = {
            id : single_receive.id,
            name : single_receive.name,
            description : single_receive.description,
            status : single_receive.status
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
        getStock();
        getSuppliers();
        getItems();
    })

    const getStock = async () => {

        var config = {
            method: 'get',
            url: '/api/receives/stock',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            stock.value = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const getSuppliers = async () => {

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
    const getReceive = async (id) => {

        var config = {
            method: 'get',
            url: `/api/receives/${id}`,
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        };

        axios(config)
        .then(function (response) {
            singleReceive.value = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const add_edit_receive = () => {
        add_receive_error.value = "";
        
        var data = new FormData();
        data.append('supplier_id', form.value.supplier_id);
        data.append('item_id', form.value.item_id);
        data.append('quantity', form.value.quantity);
        data.append('price', form.value.price);



        var config = {
            method: 'post',
            url: '/api/receives',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            data : data
        };

        axios(config)
        .then(function (response) {
            form.value = {
                supplier_id : "",
                item_id : "",
                quantity : 0,
                price : 0
            };
            add_receive_error.value = "";
            isEditModalShow.value = !isEditModalShow.value;
            getStock();
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    
    const showDetailReceive = (id) => {
        showDetailModal();
        singleReceiveDetail.value = stock.value.find((receive) => receive.id === id);
    }
    const deleteReceive = (id) => {
        showDeleteModal();
        singleReceiveDetail.value = stock.value.find((receive) => receive.id === id);
        isDeleteMode.value = true;
    }
    const deleteCancel = () => {
        singleReceiveDetail.value = {};
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
				<li><router-link to="/store_executive/home">Receives</router-link></li>
				<li><router-link to="/store_executive/requisitions">Requisitions</router-link></li>
                <li><router-link to="/store_executive/stock">Stock</router-link></li>
			</ul>
		</div>

		<div class="content">
            <div class="main_content_header">
			    <h2>Stock</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Item Name</th>
                        <th>Remaining Stock</th>
                        <!-- <th>Actions</th> -->
                    </tr>
                </thead>
                <tbody>

                    <tr v-for="(single_item, index, key) in stock" :key="single_item.id" v-if="stock.length > 0">
                        <td>{{index + 1}}</td>
                        <td>{{single_item.item.name}}</td>
                        <td>{{single_item.quantity_total - single_item.consumed_total}}</td>
                        <!-- <td> -->
                            <!-- <i class="fas fa-trash-alt cursor-pointer" @click="deleteReceive(receive.id)"></i> -->
                            <!-- <i class="fas fa-info-circle cursor-pointer ml_10" @click="showDetailReceive(receive.id)"></i> -->
                            <!-- <i class="fas fa-edit cursor-pointer ml_10" @click="showEditReceive(receive.id)"></i> -->
                        <!-- </td> -->
                    </tr>
                    
                </tbody>
            </table>
		</div>
	</div>

    <div id="modal" class="modal" :class="[isEditModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="closeAddEditModal">&times;</span>
            <h2>Add</h2>
            <p class="error_text" :class="[add_receive_error != '' ? 'show' : 'hide']">{{add_receive_error}}</p>
            <div>
                <form @submit.prevent="add_edit_receive">
                    <label for="status">Supplier</label>
                    <select v-model="form.supplier_id" id="status" name="status">
                        <option value="">Select Supplier</option>
                        <option v-for="supplier in suppliers" v-if="suppliers.length > 0" :key="supplier.id" :value="supplier.id">{{supplier.name}}</option>
                    </select><br>
                    <label for="status">Item</label>
                    <select v-model="form.item_id" id="status" name="status">
                        <option value="">Select Item</option>
                        <option v-for="item in items" v-if="items.length > 0" :key="item.id" :value="item.id">{{item.name}}</option>
                    </select><br>
                    <label for="quantity">Quantity</label>
                    <input v-model="form.quantity" type="number" id="quantity" name="quantity"><br>
                    <label for="price">Price</label>
                    <input v-model="form.price" type="number" id="price" name="price"><br>
                    <button type="submit">Submit</button>
                </form>

            </div>
        </div>
    </div>


    <div id="modal" class="modal" :class="[isDetailModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="isDetailModalShow = false">&times;</span>
            <h2>Receive Details</h2>
            <div>
                
                <div class="added_receives">
                    <p class="mt_10 mb_10"><span class="bold">Name :</span> {{singleReceiveDetail.name}}</p>
                    <p class="mt_10 mb_10"><span class="bold">Description :</span> {{singleReceiveDetail.description}}</p>
                    <p class="mt_10 mb_10"><span class="bold">Status :</span> {{showStatus(singleReceiveDetail.status)}}</p>
                </div>

            </div>
        </div>
    </div>

    <div id="modal" class="modal" :class="[isDeleteModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="deleteCancel">&times;</span>
            <h2>Confirmation</h2>
            <div>
                <h3 class="mt_10 mb_10">Are you sure to delete this receive?</h3>
                <p class="mt_10 mb_10"><span class="bold">Name :</span> {{singleReceiveDetail.name}}</p>
                <p class="mt_10 mb_10"><span class="bold">Description :</span> {{singleReceiveDetail.description}}</p>
                <p class="mt_10 mb_10"><span class="bold">Status :</span> {{showStatus(singleReceiveDetail.status)}}</p>
                <button class="smart-button" @click="confirmDelete()">Confirm</button>

            </div>
        </div>
    </div>
    

</template>
<style>
    @import '../../layouts/base.css'
</style>