import AppForm from '../app-components/Form/AppForm';

Vue.component('company-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                cif:  '' ,
                phone:  '' ,
                city:  '' ,
                code_postal:  '' ,
                whatsapp:  '' ,
                logo:  '' ,
                description:  '' ,
                country_id:  '' ,
                user_id:  '' ,
                
            }
        }
    }

});