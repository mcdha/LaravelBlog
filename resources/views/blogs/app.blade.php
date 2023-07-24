<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet" />
</head>
<body>

@include('blogs/navbar')

<div class="container w-50" style="margin-top:5%; margin-bottom:6.5%;">

    <h1>Create Blog</h1>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" class="form-control">
                <div class="text-danger" id="title-error"></div>
            </div>
            <div class="form-group">
                <label for="author_name">Author Name:</label>
                <input type="text" name="author_name" id="author_name" class="form-control">
                <div class="text-danger" id="author_name-error"></div>
            </div>
            <div class="form-group">
                <label for="body">Body:</label>
                <textarea name="body" id="body" class="form-control"></textarea>
                <div class="text-danger" id="body-error"></div>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" name="category" id="category" class="form-control">
                <div class="text-danger" id="category-error"></div>
            </div>
            <div class="form-group">
                <label for="image">Image Address:</label>
                <input type="text" name="image" id="image" class="form-control">
                <div class="text-danger" id="image-error"></div>
            </div>
            <div class="form-group">
                <label for="image-preview">Image Preview:</label>
                <img id="image-preview" src="" alt="Image Preview" style="max-width: 100%;">
            </div>
            <button type="submit" onclick="store()" class="btn btn-primary">Create</button>
        </div>
    </div>

</div>

@include('blogs/footer')

<!-- MDB -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>


    function store() {
        const title = document.getElementById('title').value;
        const author_name = document.getElementById('author_name').value;
        const body = document.getElementById('body').value;
        const category = document.getElementById('category').value;
        const imageUrl = document.getElementById('image').value;
        const imagePreview = document.getElementById('image-preview');
        imagePreview.src = imageUrl;

        if (!title || !author_name || !body || !category) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'All fields are required!',
                confirmButtonText: 'OK'
            });
            return;
        }

        clearErrors();

        const data = {
            title,
            author_name,
            body,
            category,
            image: imageUrl
        };

        axios.post('{{ route("blogs.store") }}', data)
            .then(response => {
                console.log(response.data);
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Blog created successfully!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    document.getElementById('title').value = '';
                    document.getElementById('author_name').value = '';
                    document.getElementById('body').value = '';
                    document.getElementById('category').value = '';
                    document.getElementById('image').value = '';
                    imagePreview.src = '';
                    fetchBlogs();
                });
            })
            .catch(error => {
                console.error(error.response.data);
                if (error.response.status === 422) {
                    const errors = error.response.data.errors;
                    for (const key in errors) {
                        displayError(errors[key][0], key);
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check the form fields!',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
    }

    function displayError(errorMessage, field = null) {
        const errorDiv = document.getElementById(`${field}-error`);
        if (errorDiv) {
            errorDiv.textContent = errorMessage;
        }
    }

    function clearErrors() {
        const errorDivs = document.querySelectorAll('.text-danger');
        errorDivs.forEach(errorDiv => {
            errorDiv.textContent = '';
        });
    }

    function fetchBlogs() {
        axios.get('/blogs')
            .then(response => {
                const blogs = response.data;
                // Process the blogs data as needed
            })
            .catch(error => console.error(error));
    }

    fetchBlogs();
</script>

</body>
</html>
