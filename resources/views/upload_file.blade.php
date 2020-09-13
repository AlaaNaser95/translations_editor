<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- include vue -->
    <script src="//unpkg.com/vue"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <!-- axios -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.15.3/axios.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
</head>
<style>
    .my-custom-scrollbar {
        position: relative;
        height: 500px;
        overflow: auto;
    }
    .table-wrapper-scroll-y {
        display: block;
    }

</style>
<body>
<div id="app" class="container">
    <div class="text-center m-4">
        <h1>TRANSLATIONS EDITOR</h1>
    </div>
    <form method="POST" id='upload' v-on:submit.prevent="submit"  enctype="multipart/form-data">
        @csrf
        <div class="row align-items-center justify-content-center">
        <div class="form-group">
            <label for="file">Choose File</label>
            <input type="file" class="form-control-file" name="uploadedFile" id="file" accept=".strings,.xml" required>
            <small id="Help" class="form-text text-muted">Accept only .strings and .xml translation files.</small>
        </div>
        <button type="submit" class="btn btn-primary" >Submit</button>
        </div>
    </form>
<div v-if="translations">
    <div class="m-3">
        <form v-on:submit.prevent="addNew">
            <div class="d-flex justify-content-center">
                <div>

            <input type="text" class="form-control" id="new_translation_key" placeholder="Enter translation key" required>
                </div>
                <div>
            <button class="btn btn-primary" >Add New</button>
                </div>
            </div>
        </form>
    </div>
    <form method="POST" id="form" enctype="multipart/form-data" class='d-flex flex-column' action={{url('update')}}>
        @csrf
        <button type="submit" class="btn btn-success m-2 mr-auto ml-auto">Save Changes</button>
        <div class="table-wrapper-scroll-y my-custom-scrollbar">
            <table class="table table-striped table-dark">
                <thead>
                <tr>
                    <th scope="col">Key</th>
                    <th scope="col">Value</th>
                </tr>
                </thead>
                <tbody id="values">
                    <tr v-for="(translation,key,index) in translations">
                        <td><input type="text" class="form-control" :name="'keys['+ index +']'" :value="key"  readonly></td>
                        <td><input type="text" class="form-control" :name="'translations['+index+']'" v-model="translations[key]"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>
</div>
</body>
</html>
<script>
    var table=new Vue({
        el: '#app',
        data: {
            translations: null
        },
        methods: {
            submit() {
                var formData = new FormData(document.getElementById('upload'));
                axios.post('/upload', formData)
                    .then((response) => {
                        this.translations=response.data;
                    });
            },
            addNew: function(event) {
                var key = document.getElementById('new_translation_key').value;
                document.getElementById("new_translation_key").value = "";
                Vue.set(this.translations,key,"");
            }
        }
    });
</script>


