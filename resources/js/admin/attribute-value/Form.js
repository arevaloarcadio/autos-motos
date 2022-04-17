import AppForm from '../app-components/Form/AppForm';

Vue.component('attribute-value-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                attribute_id:  '' ,
                value:  '' ,
                color_code:  '' ,
                ads_type:  false ,
                
            }
        }
    }

});