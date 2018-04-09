<template>
    <div>
        <alert v-show="showAlert" :show="showAlert" :type="alertSetting.type"
			   width="400px" placement="top-right" :duration=alertSetting.duration
			   v-on:closed="closeAlert">
			<i :class="alertSetting.icon" aria-hidden="true"></i>
			<strong v-text="alertSetting.title"></strong>
			<p v-text="alertSetting.text"></p>
		</alert>
        <modal :showbtn="false" :title="editor.title" :show.sync="showUpdatedBy"
               effect="fade" width="800"
               v-on:closed="endShowUpdatedBy">
            <div slot="modal-body" class="modal-body">
                <admin-card v-if="showUpdatedBy" :id="editor.updated_by"></admin-card>
            </div>
        </modal>
    </div>
</template>

<script>
export default {
    name:'Footer',
    data() {
        return {
            
            showAlert: false,
            alertSetting: {
                type: 'success',
                title: '資料儲存成功',
                text: '',
                dismissable: false,
                duration: 2500,
                icon:'fa fa-check-circle'
                
            },
            showUpdatedBy:false,
            editor:{
                updated_by:'',
                title:'最後更新者'
            }
            
        }
    },
    created() {
        Bus.$on('errors',this.onErrors);
        Bus.$on('okmsg',this.showSuccessMsg);
        Bus.$on('show-editor',this.showEditor);
    },
    methods: {
        closeAlert() {
            this.showAlert = false;
        },
        setAlertText(title,text='') {
            if(!title)  title ='處理您的要求時發生錯誤';
            
            this.alertSetting.title = title;
            this.alertSetting.text = text;
        },
        // Bus Event Handlers
        onErrors(error,msg){ 
            this.showErrorMsg(msg);
        },
        showErrorMsg(msg) {
            
            this.setAlertText(msg);
            this.alertSetting.type = 'danger';
            this.alertSetting.icon = 'fa fa-exclamation-circle';
            this.showAlert = true;
            this.showModal = false;
        },
        showSuccessMsg(msg) {
            this.setAlertText(msg);
            this.alertSetting.type = 'success';
            this.alertSetting.icon = 'fa fa-check-circle';

            this.showAlert = true;
            this.showModal = false;
        },
        showEditor(id,title){
            this.editor.updated_by=id;
            if(title) this.editor.title=title;
            this.showUpdatedBy=true;
        },
        endShowUpdatedBy(){
            this.showUpdatedBy=false
        },
        
        

    },
}
</script>

