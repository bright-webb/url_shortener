<template>
    <div>
        {{ message }}
    </div>
</template>

<script>
    const baseURL = import.meta.env.VITE_API_BASE_URL;
    export default {
        data(){
            return {
                message: 'Redirecting...'
            }
        },
        mounted() {
            const code = this.$route.params.code;
            this.getOriginalURL(code).then(url => {
                window.location.replace(url);
            });
        },
        methods: {
            async getOriginalURL(code){
                const response = await fetch(`${baseURL}/shorten/${code}`);
                if (!response.ok) {
                    this.message = "Link Expired or doesn't exists"
                }
                const data = await response.json();
                return data.url;
            }
        }
    }
</script>
