<template>
    <span :rel="id"  style="cursor:pointer; color:#666666" class="fas text-gray-600 fa-info-circle"></span>
</template>

<script>
    export default {
        name: 'tooltip',
        components: {},
        props:{
            title:{
                type: String,
                required:false,
            },
            content:{
                type: String,
                required:true,
            },
            id:{
                type:String,
                required:true,  
            },
            imgUrl:{
                type:String,
                required:false,
            }
        },


        mounted(){
    
            var app = this;

            if(app.imgUrl != null && app.imgUrl != ""){
                $('span[rel=' + this.id + ']').popover({
                    html: true,
                    title:app.title,
                    trigger: 'hover',
                    placement: 'top',
                    content: function(){ return '<p>' + app.content + '</p>  <img width="100" src="' + app.imgUrl + '" alt="">';}
                })
            } else {
                $('span[rel=' + this.id + ']').popover({
                    html: true,
                    title:app.title,
                    trigger: 'hover',
                    placement: 'top',
                    content: function(){return '<p>' + app.content + '</p>';}
                })
            }
        },

        data () {
            return {}
        },

        methods:{},
    }
</script> 