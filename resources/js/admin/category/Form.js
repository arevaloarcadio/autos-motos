import AppForm from '../app-components/Form/AppForm';

Vue.component('category-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                order_level:  '' ,
                icon:  '' ,
                slug:  '' ,
                ads_type:  false ,
                meta_title:  '' ,
                
            }
        }
    }

});