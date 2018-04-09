<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:10%">審核狀況</th>
                        <th>通知內容</th>
                        
                    
                        <th style="width:10%">建檔單位</th>
                        <th style="width:15%">建檔時間</th>
                       
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(notice,index) in model.viewList" :key="index">
                        <td v-html="$options.filters.reviewedLabel(notice.reviewed)" ></td>
                        <td>
                            <a :href="detailsUrl(notice)">
                                {{  notice.content }}
                            </a>
                        </td>
                        <td>{{  notice.unit.name }}</td>
                        <td> {{ notice.created_at }}  </td>
                        
                    </tr>    
                </tbody>

            </table>
        </div>
        <slot name="table-footer"> 
        
        </slot> 
            
    </div>
</template>

<script>
export default {
    name:'NoticeTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        can_edit:{
            type: Boolean,
            default: false
        },
        can_remove:{
            type: Boolean,
            default: false
        }
        
	},
	data() {
		return {
			
		};
	},
	computed:{
		
		
    }, 
	watch: {
		
	},
    methods:{
        detailsUrl(notice){
            return  `notices/${notice.id}`;
        },
        remove(notice){
            this.$emit('remove' , notice);
        },
        
    }
}
</script>

