import AppForm from '../app-components/Form/AppForm';

Vue.component('vehicle-category-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                icon:  '' ,
                name:  '' ,
                type_ads:  '' ,
                
            }
        }
    }

});