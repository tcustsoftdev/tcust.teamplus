<template>
   <div>
        <div v-if="model" >
            <div class="row">
                <div class="col-sm-3" style="margin-top: 3px;"> 
                    <h3>
                        <i class="fa fa-envelope"></i>
                        公告訊息通知
                    </h3>
                </div>
                <div class="col-sm-5 form-inline" style="margin-top: 20px;">
                    <div class="form-group" style="padding-left:1em;">
                        
                        <drop-down :items="units" :selected="params.unit"
                            @selected="onUnitSelected">
                        </drop-down>
                    </div>
                
                    <div class="form-group" style="padding-left:1em;">
                        <toggle :items="reviewedOptions"   :default_val="params.reviewed" @selected="setReviewed"></toggle>
                        
                    </div>
                
                
                </div>
                <div class="col-sm-3" style="margin-top: 20px;">
                    <searcher @search="onSearch">
                    </searcher>
                </div>
                
                <div class="col-sm-1 pull-right" align="right" style="margin-top: 20px;">
                    <a href="/notices/create" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> 新增
                    </a>
                    
                </div>
            </div>

            <hr/>
            <notice-table :model="model"  >
               

                <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
					<page-controll   :model="model" @page-changed="onPageChanged"
						@pagesize-changed="onPageSizeChanged">
					</page-controll>
            
                </div>

            </notice-table>
            

        </div>

      
        
    </div> 
</template>


<script>
    import NoticeTable from '../../components/notice/table';
    export default {
        name:'NoticeIndexView',
        components: {
            'notice-table':NoticeTable,
        },
        props: {
            init_model: {
                type: Object,
                default: null
            },
            units:{
                type:Array,
                default:null
            },
            version:{
                type:Number,
                default:0
            },
        },
        data(){
            return {
                title: '使用者管理',

                loaded:false,

                model:null,
                
                params:{
                    page:1,
                    pageSize:999
                },

                params:{
                    unit:'0',
                   
                    reviewed:false,
                    keyword:'',

                    page:1,
                    pageSize:10
                },

              
                reviewedOptions:Helper.reviewedOptions(),

              
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            if(this.init_model){
                this.model={...this.init_model };
                this.params.page=this.init_model.pageNumber;
                this.params.pageSize=this.init_model.pageSize;
            }	

            this.params.unit=this.units[0].value;
        },
        computed:{
           
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onSelected(id){
               this.$emit('selected',id);
            },
            onUnitSelected(unit){
                this.params.unit=unit.value;
                this.fetchData();
            },
            setReviewed(val){
                this.params.reviewed=val;
                this.fetchData();
            },
            onSearch(keyword){
               
				this.params.keyword=keyword;
				this.fetchData();
			},
            onPageChanged(page){
				this.params.page=page;
				this.fetchData();
            },
            onPageSizeChanged(){
                
                this.params.pageSize=this.model.pageSize;
				this.fetchData();
            },
            fetchData() {
                    
                let getData = Notice.index(this.params);

                getData.then(model => {

                    this.model={ ...model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            
           
            
        }
    }
</script>





