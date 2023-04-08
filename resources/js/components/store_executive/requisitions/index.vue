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
    let add_issue_error = ref("");
    let isIssueGenerateModalShow = ref(false);
    let isDetailModalShow = ref(false);
    let singleRequisitionDetail = ref({})
    let issueAddErrors = ref([]);

    const logout = () => {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        localStorage.removeItem('user_info');
        router.push('/');
    }
    const add_issue = () => {
        add_issue_error.value = "";
        if(singleRequisition?.value?.details.length == 0){
            add_issue_error.value = "There is no item!";
            return false;
        }
        var data = new FormData();
        data.append('requisition_id', singleRequisition.value.id);
        singleRequisition?.value?.details.map((detail) => {
            data.append('item_id[]', detail?.item?.id);
            data.append('quantity[]', detail?.quantity);
        });
        var config = {
            method: 'post',
            url: '/api/issues',
            headers: { 
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            
            data : data
        };
        console.log(config);

        axios(config)
        .then(function (response) {
            singleRequisition.value = {};
            add_issue_error.value = "";
            isIssueGenerateModalShow.value = !isIssueGenerateModalShow.value;
            getRequisitions();
        })
        .catch(function (error) {
            issueAddErrors.value = error.response.data.errors;
        });
    }
    const showGenerateIssueModal = (id) => {
        form.value.id = id;
        singleRequisition.value = requisitions.value.find(requisition => requisition.id == id);
        isIssueGenerateModalShow.value = !isIssueGenerateModalShow.value;
    }
    const closeStatusChangeModal = () => {
        isIssueGenerateModalShow.value = false;
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
            url: '/api/requisitions/get_approved',
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
				<li><router-link to="/store_executive/home">Receives</router-link></li>
				<li><router-link to="/store_executive/requisitions">Requisitions</router-link></li>
				<li><router-link to="/store_executive/stock">Stock</router-link></li>
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
                            <!-- <i class="fas fa-info-circle cursor-pointer" @click="showDetailRequisition(requisition.id)"></i> -->
                            <i class="fa fa-wrench cursor-pointer" aria-hidden="true" @click="showGenerateIssueModal(requisition.id)"></i>
                            <!-- <i class="fas fa-edit cursor-pointer ml_10" @click="showStatusChangeModal(requisition.id)"></i> -->
                        </td>
                    </tr>
                    
                </tbody>
            </table>
		</div>
	</div>

    <div id="modal" class="modal" :class="[isIssueGenerateModalShow ? 'show' : 'hide']">
        <div class="modal-content">
            <span id="close-modal" class="close" @click="closeStatusChangeModal()">&times;</span>
            <h2>Issue</h2>
            <p class="error_text" v-for="error in issueAddErrors" v-if="issueAddErrors.length > 0">{{error}}</p>
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
                            <tr v-for="(detail, index, key) in singleRequisition?.details" :key="key" v-if="singleRequisition?.details?.length > 0">
                                <td>{{index + 1}}</td>
                                <td>{{detail.item.name}}</td>
                                <td>{{detail.quantity}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <button @click="add_issue()">Add Issue</button>
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