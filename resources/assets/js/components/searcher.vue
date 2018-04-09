<template>
    <form @submit.prevent="onSubmit" class="input-group">
        <input class="form-control" v-model="keyword" >
        <div class="input-group-btn">
            <button type="submit" class="btn btn-default">
                <i class="fa fa-search" aria-hidden="true"></i>
            </button>
        </div>
    </form>
</template>

<script>
export default {
    name:'Searcher',
    data(){
        return {
            keyword:'',
        }
    },
    methods:{
        onSubmit(){
            
            this.$emit('search',this.keyword);
            
        }
    }
}
</script>

