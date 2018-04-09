class ErrorHandler {
   constructor(error) {
      
      this.error = error;
   }
   handle(){     
      let error= this.error;     
      

      let isAuthError=false;
      
      try {
         isAuthError=this.handleAuthError(error);          
      }
      catch (e) {
         isAuthError=false;
      }

      if(isAuthError) return null;
      
      let msg=null;
      try {
          msg=this.getErrorMsg(error)              
      }
      catch (e) {
         console.log(e);
      }

      if(!msg) msg=msg={
         title:'系統暫時無回應,請稍後再試'
       };
     
      return msg;
      
   }
   handleAuthError(error){
      if(error.response.data.code==401){
          this.onAuthFailed()
          return true
      }else{
          return false
      }
   }
   onAuthFailed(){
      alert('onAuthFailed');
      // if(!returnUrl) returnUrl= this.$route.fullPath
      // this.$auth.logout()
      // this.$router.push('/login?return=' + returnUrl)
   }
   getErrorMsg(error) {
      let msg = {}
      if (error.status == 500) {
          msg = {
              title: '處理您的要求時發生錯誤',
              text: '系統暫時無回應，請稍後再試'
          }
      }else if(error.status == 404){
          msg = {
              title: '查無資料',
              text: ''
          }
      }else {
          msg = {
              title: error.title,
              text: error.text
          }
      }

      return msg;
     
   }
}


export default ErrorHandler;