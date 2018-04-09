<template>

    <div class="panel panel-default">
        <div v-show="show_header" class="panel-heading">
             
            <div class="panel-title">
                 <h4 v-show="show_title"  v-html="title">
                 </h4>
            </div>
            <slot name="header"></slot>
              

            <div>   
                <button  v-if="showCreateBtn()" class="btn btn-primary btn-sm" @click.prevent="createClicked">
                    
                   <span  class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                  
                   <span v-text="create_text">  </span>
                </button>
                
                <slot name="btn"></slot>
                
               
                <button v-if="!showFilter"  class="btn btn-default btn-sm" @click="fetchData()">
                     <span  class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                </button>
                <button  v-if="canSearch()"   class="btn btn-default btn-sm" @click="changeMode">
                    <span v-if="!showFilter" class="glyphicon glyphicon-search" aria-hidden="true"></span>
                    <span v-if="showFilter" class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                </button>
            </div>
             
        </div>
        <div class="panel-body">
            <div class="filter" v-if="showFilter">
                <div class="filter-column">
                    <select class="form-control" v-model="params.search_column">
                        <option v-for="(column,index) in filter" :key="index" :value="column.key" v-text="column.title"></option>
                    </select>
                </div>
                <div class="filter-operator">
                    <select class="form-control" v-model="params.search_operator">
                        <option v-for="(value, key) in operators" :key="key" :value="key">{{value}}</option>
                    </select>
                </div>
                <div class="filter-input">
                    <input type="text" class="form-control" v-model="params.search_query_1"
                        @keyup.enter="doSearch" placeholder="Search">
                </div>
                <div class="filter-input" v-if="params.search_operator === 'between'">
                    <input type="text" class="form-control" v-model="params.search_query_2"
                        @keyup.enter="doSearch" placeholder="Search">
                </div>
                <div class="filter-btn">
                    <button class="btn btn-primary btn-sm btn-block" @click="doSearch">搜尋</button>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th v-for="(item,index) in thead" :key="index"  :style="{ width: item.width }" >
                            <div v-if="item.sort" style="color: #337ab7"  class="dataviewer-th" @click="sort(item.key)" >
                                <span>{{item.title}}</span>
                                <span v-if="params.column === item.key">
                                    <span v-if="params.direction === 'asc'">&#x25B2;</span>
                                    <span v-else>&#x25BC;</span>
                                </span>
                            </div>
                            <div v-else>
                                <span v-if="item.title">{{item.title}}</span>
                                <button v-if="item.edit" v-show="!itemEditting(item.key)" @click.prevent="onEdit(item.key)" class="btn btn-primary btn-xs">
                                    <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                                </button>
                                <button v-if="item.edit" v-show="itemEditting(item.key)" @click="onSubmitEdit(item.key)" class="btn btn-success btn-xs">
                                    <span aria-hidden="true" class="glyphicon glyphicon-floppy-disk" ></span>
                                </button>
                                <button v-if="item.edit" v-show="itemEditting(item.key)" @click="onCancelEdit(item.key)" class="btn btn-default btn-xs">
                                    <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
                                </button>
                                <checkbox v-if="item.checkall" v-show="hasData" :default="item.checked"
                                    @selected="checkAll"   @unselected="unCheckAll">                             
                                </checkbox>
                            </div>
                           
                        </th>
                    </tr>
                    
                </thead>
                <tbody>
                    <slot v-for="(item,index) in model.data"   :item="item" :index="index"></slot>                    
                </tbody>
            </table>
        </div>
        <div class="panel-footer pagination-footer">
            <div v-show="!no_page" class="pagination-item">
                <span>每頁: </span>
                    <select v-model="params.per_page" @change="fetchData()">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                     </select>
                 <span>筆資料</span>
            </div>
             <div class="pagination-item">
                <small>Showing {{model.from }} - {{model.to}} of {{model.total}}</small>
            </div>
            <div v-show="!no_page" class="pagination-item">
                 <pager   :total-page="model.last_page"  :init-page="this.params.page"  @go-page="goPage"></pager>
               
            </div>
           
        </div>
    </div>
    
   
</template>
<script>
    import Pager from 'vue-simple-pager'

    export default {
        name:'DataViewer',
        components: {
            Pager
        },
        props: {
            source: {
              type: String,
              default: ''
            },
            search_params: {
              type: Object,
              default: null
            },
            default_order:{
               type: String,
               default: 'updated_at'
            },
            default_direction:{
               type: String,
               default: 'desc'
            },
            default_search:{
               type: String,
               default: 'updated_at'
            },
            version:{
               type: Number,
               default: 0
            },
            thead:{
               type: Array,
               default: []
            },
            filter:{
               type: Array,
               default: []
            },
            title:{
               type: String,
               default: ''
            },
            create_text:{
               type: String,
               default: ''
            },
            no_search:{
               type: Boolean,
               default: false
            },
            no_page:{
               type: Boolean,
               default: false
            },
            page_size:{
               type: Number,
               default: 10
            },
            show_header:{
               type: Boolean,
               default: true
            },
            show_title:{
               type: Boolean,
               default: true
            },
            editting:{
               type: String,
               default: ''
            },
           
        },
        
        data() {
            return {
                
                originUrl: '',
                showFilter: false,
                model: {
                    data: []
                },
                params: {},
                operators: {
                },

            }
        },
        computed: {
            hasData(){
                return this.model.total
            },
        },
        
        watch: {
            search_params: {
              handler: function () {
                
                this.fetchData()
              },
              deep: true
            },
            version: function () {
               this.fetchData()
            }
           
        },
        beforeMount() {
            this.init()           
        },
        created(){
         
        },
        methods: {
            init(){
                
                this.originUrl= '',
                this.showFilter= false
                this.model= {
                    data: []
                }

                let perPage=this.page_size
                if(this.no_page) perPage=150
                this.params={
                    column: this.default_order,  
                    direction: this.default_direction,
                    per_page: perPage,
                    page: 1,
                    search_column: this.default_search,
                    search_operator: 'like',
                    search_query_1: '',
                    search_query_2: ''
                }
                this.operators= {
                    like: 'LIKE',
                    equal_to: '=',
                    not_equal: '<>',
                    less_than: '<',
                    greater_than: '>',
                    less_than_or_equal_to: '<=',
                    greater_than_or_equal_to: '>=',
                    in: 'IN',
                    not_in: 'NOT IN',
                    between: 'BETWEEN'
                }
                
                this.originUrl = this.buildURL();
                this.fetchData(this.originUrl)
            },  
            checkAll(){
                this.$emit('checkall')
            },   
            unCheckAll(){
                this.$emit('uncheckall')
            },
            onEdit(key){
                this.$emit('edit',key)
               
            },            
            itemEditting(key){
                return this.editting==key
            },    
            onCancelEdit(key){
                this.$emit('cancel-edit',key)
                
            },    
            onSubmitEdit(key){
                this.$emit('submit-edit',key)
               
            },     
            showCreateBtn() {
                if (this.create_text) return true
                return false
            },
            createClicked(){
                this.$emit('beginCreate')
            },
            doSearch(){
                this.params.page=1
                this.fetchData()
            },
            changeMode() {
                this.showFilter = !this.showFilter;
                if (!this.showFilter) {
                     this.init()
                }
            },
           
            goPage(data) {
                this.params.page = data.page
                this.fetchData()
            },
            next() {
                if (this.model.next_page_url) {
                    this.params.page++
                        this.fetchData()
                }
            },
            prev() {
                if (this.model.prev_page_url) {
                    this.params.page--
                        this.fetchData()
                }
            },
            sort(column) {
                if (column === this.params.column) {
                    if (this.params.direction === 'desc') {
                        this.params.direction = 'asc'
                    } else {
                        this.params.direction = 'desc'
                    }
                } else {
                    this.params.column = column
                    this.params.direction = 'asc'
                }

                this.fetchData()
            },
            fetchData(url) {
                var vm = this;
                if (!url) {
                    url = this.buildURL()
                }
                if(!url) return false
               
                axios.get(url)
                    .then(response=> {
                        let model=response.data.model
                        this.model=model
                        this.$emit('dataLoaded' , response.data)
                    })
                    .catch(error=> {
                        console.log(error)
                    })
            },
            buildURL() {
                if(!this.source) return false
                    
                let url=this.source + '?'
                if(this.search_params){
                    let searchParams=this.search_params
                    for (let field in searchParams) {

                      let value=searchParams[field]
                      url += field + '=' + value + '&'

                    }
                }
                var p = this.params
               
                url += `column=${p.column}&direction=${p.direction}&per_page=${p.per_page}&page=${p.page}&search_column=${p.search_column}&search_operator=${p.search_operator}&search_query_1=${p.search_query_1}&search_query_2=${p.search_query_2}`
             
                return url

            },
            canSearch(){
                if(this.no_search) return false;
                if(this.filter && this.filter.length > 0) return true
                return false 
            }
        },
        
    }
</script>
