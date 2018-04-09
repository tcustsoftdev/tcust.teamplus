<template>
   <div v-if="user" class="show-data">
        <p>使用者名稱：{{ user.name }}</p>
        <p>姓名：{{ user.profile.fullname }}</p>
        <p>所屬中心：<span v-html="centers()"></span></p>
        <p>角色：
			<role-label v-for="(role,index) in user.roles" :key="index" :labelstyle="role.style" 
			:labeltext="role.display_name">
			</role-label>
		</p>
         
    </div>
</template>
<script>
   
    export default {
        name: 'AdminCard',  
        props: {
            id: {
              type: String,
              default: ''
            },
        },
        data() {
             return {
                
                user:null
            }
        }, 
        beforeMount() {
            this.init()
        }, 
        methods: {    
            init(){
                this.loaded=false
                this.user= {
                    profile: {}
                };
                if(this.id) this.getUser()
                
            },    
            getUser() {
                
                let show = User.show(this.id)
                
                show.then(data => {
                    this.user = data.user
                    this.loaded=true
                })
                .catch(error=> {
                      Helper.BusEmitError(error)
                })
            },
            centers(){
                return 'centers';
                // if(!this.user.admin) return '';
                // if(!this.user.admin.validCenters)  return '' 
                // let length=this.user.admin.validCenters.length
                // if(length < 1)  return ''   
                // let html=''
                // for (let i = 0; i < length; i++) { 
                //     html += this.user.admin.validCenters[i].name + '&nbsp;'
                // }
                // return html
             }
            
        }
    }
</script>