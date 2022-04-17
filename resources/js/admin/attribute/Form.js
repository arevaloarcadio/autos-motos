import AppForm from '../app-components/Form/AppForm';

Vue.component('attribute-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                searched:  false ,
                featured:  false ,
                is_choice:  false ,
                order_level:  '' ,
                
            }
        }
    }

});