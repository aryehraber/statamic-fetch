Vue.component('fetch-fieldtype', {

    template: '<div><input class="form-control" style="margin-bottom: 20px;" v-model="apiKey" type="text" readonly><button @click="generate" class="btn">Generate</button></div>',

    props: ['data', 'config', 'name'],

    methods: {
        generate: function() {
            var key = '';
            var length = 60;
            var charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            
            for (var i = 0; i < length; i++) {
                key += charset.charAt(Math.floor(Math.random() * charset.length));
            }

            this.data = key;
        }
    },

    computed: {
        apiKey: function() {
            return this.data ? this.data : '';
        }
    }
});
