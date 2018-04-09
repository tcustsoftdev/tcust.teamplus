<template>
	<div class="row"> 
		<div v-if="hasPager" class="col-md-2 pagination-item paging-controll">
			<span>每頁: </span>
				<select v-model="model.pageSize" @change="onPageSizeChanged">
					<option v-for="(item,index) in pageSizeOptions" :key="index" :value="item" v-text="item"></option>
				</select>
			<span>筆資料</span>
		</div>
		<div  class="col-md-2 pagination-item paging-controll">
			<small>Showing {{ first }} - {{ last }} of {{model.totalItems}}</small>
		</div>
		<div v-if="hasPager" class="col-md-8 pagination-item">
			<pager   :total-page="model.totalPages"  :init-page="model.pageNumber"  @go-page="onPageChanged"></pager>
		</div>
	</div> 
</template>



<script>

import Pager from '../packages/components/pager';

export default {
	props: {
		model: {
			type: Object,
			default: null
		},
	},
	components: {
		Pager
	},
	data(){
		return {
			pageSizeOptions:[10,25,50]
		}
	},
	computed:{
		hasPager(){
			return this.model.pageSize <=100;
		},
		first(){
			if(!this.model) return 0;
			return this.model.pageSize * (this.model.pageNumber-1) + 1;
			
		},
		last(){
			if(!this.model) return 0;
			return this.first + this.model.viewList.length - 1;
		}
	},
	methods:{
		onPageChanged(params){
			this.$emit('page-changed', params.page);
		},
		onPageSizeChanged(){
			this.$emit('pagesize-changed');
		}
	}
	
}
</script>


<style scoped>

.paging-controll{
  margin-top: 1em;
}

</style>


