<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        $.ajaxSetup({
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
        });
    </script>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse min-vh-100">
            <div class="position-sticky pt-3">
                <h4 class="text-white text-center mb-4">Dashboard</h4>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white active" href="#">User Management</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- MAIN CONTENT -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">

            <h2>User Management</h2>

            <!-- HEADER WITH ADD BUTTON LEFT -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                 <h4 class="mb-0"></h4>
                <button class="btn btn-success" id="openAddModal" data-bs-toggle="modal" data-bs-target="#userModal">
                    + Add User
                </button>

               
            </div>


            <!-- USER TABLE -->
            <div class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Profile Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userList"></tbody>
                </table>
            </div>

        </main>
    </div>
</div>


<!-- ADD/EDIT USER MODAL -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add User</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="userForm" enctype="multipart/form-data">

                    <input type="hidden" id="user_id">

                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" id="name" class="form-control">
                        <small class="text-danger" id="name_error"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subject *</label>
                        <input type="text" id="subject" class="form-control">
                        <small class="text-danger" id="subject_error"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" id="profile_image" class="form-control">
                        <img id="previewImage" width="80" class="mt-2 d-none">
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="saveUserBtn">Save</button>
            </div>

        </div>
    </div>
</div>


<!-- VIEW USER MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">User Details</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><strong>Name:</strong> <span id="view_name"></span></p>
                <p><strong>Subject:</strong> <span id="view_subject"></span></p>
                <p><strong>Profile Image:</strong></p>
                <img id="view_image" width="120" class="border mt-2">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<script>
const apiUrl = "http://127.0.0.1:8000/users";


// LOAD USERS
function loadUsers() {
    $.get(apiUrl, function(response) {

        let rows = "";
        response.data.forEach(user => {
            rows += `
                <tr>
                    <td>${user.name}</td>
                    <td>${user.subject}</td>
                    <td>${user.profile_image_url ? `<img src="${user.profile_image_url}" width="50">` : 'No Image'}</td>

                    <td>
                        <button class="btn btn-info btn-sm text-white" onclick="viewUser(${user.id})">View</button>
                        <button class="btn btn-warning btn-sm" onclick="editUser(${user.id})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">Delete</button>
                    </td>
                </tr>`;
        });

        $("#userList").html(rows);
    });
}


// OPEN ADD MODAL
$("#openAddModal").click(function () {
    $("#modalTitle").text("Add User");
    $("#saveUserBtn").text("Add User");
    $("#userForm")[0].reset();
    $("#user_id").val("");
    $("#previewImage").addClass("d-none");
    $(".text-danger").text("");
});


// SAVE USER (CREATE / UPDATE)
$("#saveUserBtn").click(function () {

    $(".text-danger").text("");

    const id = $("#user_id").val();
    const url = id ? `${apiUrl}/${id}` : apiUrl;

    const formData = new FormData();
    formData.append("name", $("#name").val());
    formData.append("subject", $("#subject").val());

    const file = $("#profile_image")[0].files[0];
    if (file) formData.append("profile_image", file);

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,

        success: function () {
            $("#userModal").modal("hide");
            loadUsers();
        },

        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                if (errors.name) $("#name_error").text(errors.name[0]);
                if (errors.subject) $("#subject_error").text(errors.subject[0]);
            }
        }
    });
});


// EDIT USER
function editUser(id) {
    $.get(`${apiUrl}/${id}`, function(response) {

        let user = response.data;

        $("#modalTitle").text("Edit User");
        $("#saveUserBtn").text("Update User");

        $("#user_id").val(user.id);
        $("#name").val(user.name);
        $("#subject").val(user.subject);

        if (user.profile_image_url) {
            $("#previewImage").attr("src", user.profile_image_url).removeClass("d-none");
        }

        $(".text-danger").text("");
        $("#userModal").modal("show");
    });
}


// VIEW USER
function viewUser(id) {
    $.get(`${apiUrl}/${id}`, function(response) {

        let user = response.data;

        $("#view_name").text(user.name);
        $("#view_subject").text(user.subject);

        if (user.profile_image_url) {
            $("#view_image").attr("src", user.profile_image_url).show();
        } else {
            $("#view_image").hide();
        }

        $("#viewModal").modal("show");
    });
}


// DELETE USER
function deleteUser(id) {
    if (!confirm("Are you sure?")) return;

    $.ajax({
        url: `${apiUrl}/${id}`,
        type: "POST",
        data: { _method: "DELETE" }, // <-- important
        success: function () {
            alert("User deleted successfully");
            loadUsers();
        },
        error: function () {
            alert("Error deleting user!");
        }
    });
}



// LOAD USERS ON PAGE LOAD
$(document).ready(function() {
    loadUsers();
});
</script>

</body>
</html>
