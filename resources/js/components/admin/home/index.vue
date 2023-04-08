<script setup>
    import {onMounted, ref} from 'vue'
    import {useRouter} from 'vue-router';
    const router = useRouter();
    
    let requisitions = ref([]);
    let items = ref([]);
    let form = ref({
        id : 0,
        status : 'new'
    })
    let singleRequisition = ref({});
    let add_requisition_error = ref("");
    let isEditModalShow = ref(false);
    let isDetailModalShow = ref(false);
    let singleRequisitionDetail = ref({})

    const logout = () => {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        localStorage.removeItem('user_info');
        router.push('/');
    }
    const changeStatus = () => {
        add_requisition_error.value = "";
        
        var data = {
            'status': form.value.status,
        };

        var config = {
            method: 'put',
            url: `/api/requisitions/update_status/${form.value.id}`,
            headers:{ 
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data : data
        };

        axios(config)
        .then(function (response) {
            form.value.id = 0;
            form.value.status = 'new'
            
            add_requisition_error.value = "";
            isEditModalShow.value = false;
            getRequisitions();
        })
        .catch(function (error) {
            console.log(error);
        });
    }
    const showStatusChangeModal = (id) => {
        form.value.id = id;
        isEditModalShow.value = !isEditModalShow.value;
    }
    const closeStatusChangeModal = () => {
        isEditModalShow.value = false;
        form.value.id = 0;
        form.value.status = 'new';
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
            url: '/api/requisitions/get_new',
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
				<li><router-link to="/admin/home">Requisitions</router-link></li>
				<li><router-link to="/admin/items">Items</router-link></li>
				<li><router-link to="/admin/suppliers">Suppliers</router-link></li>
			</ul>
		</div>

		<div class="content">
            <div class="main_content_header">
			    <h2>Requisitions</h2>
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

                    <tr v-for="(requisition, index, key) in requisitions" :key="requisition.id" v-if="requisitions.length > 0">
                        <td>{{index + 1}}</td>
                        <td>{{showStatus(requisition.status)}}</td>
                        <td>{{requisition.details.length}}</td>
                        <td>{{new Date(requisition.created_at).toISOString().slice(0, 10)}}</td>
                        <td>
                            <i class="fas fa-info-circle cursor-pointer" @click="showDetailRequisition(requisition.id)"></i>
                            <i class="fas fa-edit cursor-pointer ml_10" @click="showStatusChangeModal(requisition.id)"></i>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
		</div>
	</div>

    <div id="modal" class="modal" :class="[isEditModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="closeStatusChangeModal()">&times;</span>
            <h2>Change Status</h2>
            <div>

                <form @submit.prevent="changeStatus">
                    <label for="status">Status</label>
                    <select v-model="form.status" id="status" name="status">
                        <option value="new">New</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select><br><br>
                    <button type="submit">Submit</button>
                </form>

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