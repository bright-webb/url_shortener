<template>
    <div class="container" style="margin-top: 50px">
        <div class="row d-flex justify-content-center">
            <div class="col-md-4">
                <div class="ui header">{{ username }}</div>
                <p>URL shortener system</p>
                <div class="ui divider"></div>
                <form class="ui form" @submit.prevent="submit">
                    <div v-if="message" class="ui message" :class="{'positive': success}">
                        <i class="close icon"></i>    
                    <div class="header">
                        {{ message }}
                    </div>
                    </div>
                    <div class="field">
                        <label>Enter original URL</label>
                        <input type="url" v-model="url" placeholder="https://google.com" required>
                    </div>
                    <div class="field">
                        <button type="submit" class="ui primary button" style="width: 100%" :class="{'loading': isLoading}">Submit</button>
                    </div>
                    <div v-if="error" class="ui negative message">{{ error }}</div>
                </form>
            </div>
            <div class="col-md-4">
                <div class="ui header">History</div>
                <div class="ui divider"></div>
                <div v-if="history" class="ui relaxed divided list">
                    <div v-for="item in history" :key="item.id" class="item">
                        <div class="right floated content">
                            <div class="ui button mini copy" @click="copyToClipboard(item)">
                                <i v-if="!item.copied" class="icon copy"></i>
                                <span v-else>Copied</span>
                            </div>
                        </div>
                        <div class="content">
                        <div class="meta">{{ item.original_url }}</div>
                        <a class="header" :href="item.url">{{ item.url }}</a>
                        <div class="description">{{ item.time }}</div>
                       
                        </div>
                    </div>
                </div>
                <div v-else class="ui message info">No link available</div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../axios';
    import Clipboard from 'clipboard';

    export default {
        data(){
            return {
                url: '',
                isLoading: false,
                error: '',
                message: '',
                success: false,
                copied: false,
                history: []
            }
        },
        mounted(){
            
            this.linkHistory().then(response => {
                this.history = response.data.history;
            });
        },
        methods: {
           async submit() {
                if(this.url === ''){
                    this.error = 'Provide a url';
                    return;
                }
                try{
                    this.error = ''; // Clear the error messae
                    this.isLoading = true;
                    const response = await axios.post('/shorten', {
                        url: this.url
                    });

                    if(response.status === 200){
                        this.success = true;
                        this.message = "Link Created";
                        this.url = '';

                        this.linkHistory().then(response => {
                            this.history = response.data.history;
                        });
                    }

                    this.isLoading = false;
                }
                catch(error){
                    this.isLoading = false;
                    console.log(error);
                    if (error.code === 'ERR_NETWORK') {
                    // Handle network error
                    console.error('Request timed out:', error);
                    this.error = error.message;
                    } 
                    else if(error.code === 'ERR_BAD_RESPONSE'){
                        this.error = error.response.data.message;
                    }
                    
                    else {
                    this.error = 'An error occurred during the request.';
                    }
                }
            },

            async linkHistory(){
                try {
                    const response = await axios.get('/history');
                    return response;
                } catch (error) {
                    console.error('Error fetching history:', error);
                    this.message = error.response.data.message;
                    throw error; 
                } 
            },

            copyToClipboard(item) {
            if (!item.url) {
                console.error('URL is undefined or empty.');
                return;
            }
            navigator.clipboard.writeText(item.url)
                .then(() => {
                console.log('URL copied to clipboard:', item.url);
                item.copied = true; // Set copied state for the clicked item
                setTimeout(() => {
                    item.copied = false; // Reset copied state after 2 seconds
                }, 2000);
                })
                .catch((error) => {
                console.error('Failed to copy URL:', error);
                });
            },

        }
    }
</script>
