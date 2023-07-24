<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet"
    />
    <!-- Google Fonts -->
    <link
    href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
    rel="stylesheet"
    />
    <!-- MDB -->
    <link
    href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css"
    rel="stylesheet"
    />
</head>
<body>
@include('blogs/navbar')
<div class="container w-50" style="margin-top:5%;">
<div class="row mt-5">
        <div class="col-md-12">
            <h1>Blogs List</h1>
            <ul class="list-group" id="blogs">
                @foreach($blogs as $blog)
                <li class="list-group-item" id="blog-{{ $blog->id }}">
                        <h3>{{ $blog->title }}</h3>
                        <!-- <p>{{ $blog->image }}</p> -->
                        <p>Author: {{ $blog->author_name }}</p>
                        <p>{{ $blog->body }}</p>
                        <p>Category: {{ $blog->category }}</p>
                        <button class="btn btn-sm btn-danger" onclick="deleteBlog({{ $blog->id }})">Delete</button>
                        <button class="btn btn-sm btn-primary" onclick="editBlog({{ $blog->id }})">Edit</button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    </div>
@include('blogs/footer')
   <script
  type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"
></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>

    function store() {
        const title = document.getElementById('title').value;
        const author_name = document.getElementById('author_name').value;
        const body = document.getElementById('body').value;
        const category = document.getElementById('category').value;
        // const imageUrl = document.getElementById('image').value;
        // const imagePreview = document.getElementById('image-preview');
        // imagePreview.src = imageUrl;

       
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
            // image: imageUrl
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
                    // document.getElementById('image').value = '';
                   
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


    function deleteBlog(id) {
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
               
                axios.delete('/api/blogs/' + id)
                    .then(response => {
                        console.log(response.data);
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: 'The blog has been deleted successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                           
                            fetchBlogs();
                        });
                    })
                    .catch(error => {
                        console.error(error.response.data);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again later.',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });
    }

    function editBlog(id) {
        const blogElement = document.getElementById('blog-' + id);

        axios.get('/api/blogs/' + id)
            .then(response => {
                const blog = response.data;

                const titleInput = document.createElement('input');
                titleInput.type = 'text';
                titleInput.value = blog.title;
                titleInput.className = 'form-control';

                const authorInput = document.createElement('input');
                authorInput.type = 'text';
                authorInput.value = blog.author_name;
                authorInput.className = 'form-control';

                const bodyInput = document.createElement('textarea');
                bodyInput.value = blog.body;
                bodyInput.className = 'form-control';

                const categoryInput = document.createElement('input');
                categoryInput.type = 'text';
                categoryInput.value = blog.category;
                categoryInput.className = 'form-control';

                blogElement.innerHTML = '';
                blogElement.appendChild(titleInput);
                blogElement.appendChild(authorInput);
                blogElement.appendChild(bodyInput);
                blogElement.appendChild(categoryInput);

                const updateButton = document.createElement('button');
                updateButton.className = 'btn btn-sm btn-primary mt-2';
                updateButton.textContent = 'Update';
                updateButton.addEventListener('click', () => {
                    updateBlog(id, {
                        title: titleInput.value,
                        author_name: authorInput.value,
                        body: bodyInput.value,
                        category: categoryInput.value,
                    });
                });

                blogElement.appendChild(updateButton);
            })
            .catch(error => console.error(error));
    }

    function updateBlog(id, updatedData) {
        axios.put('/api/blogs/' + id, updatedData)
            .then(response => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Blog updated successfully!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    fetchBlogs();
                });
            })
            .catch(error => {
                console.error(error.response.data);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again later.',
                    confirmButtonText: 'OK'
                });
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

   
    fetchBlogs();
</script>

</body>
</html>