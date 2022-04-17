import AppForm from '../app-components/Form/AppForm';

Vue.component('store-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                email:  '' ,
                phone:  '' ,
                city:  '' ,
                code_postal:  '' ,
                whatsapp:  '' ,
                country_id:  '' ,
                user_id:  '' ,
                
            }
        }
    }

});