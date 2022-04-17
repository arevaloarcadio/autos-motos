import AppForm from '../app-components/Form/AppForm';

Vue.component('brand-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                logo:  '' ,
                top:  '' ,
                slug:  '' ,
                meta_title:  '' ,
                meta_description:  '' ,
                
            }
        }
    }

});