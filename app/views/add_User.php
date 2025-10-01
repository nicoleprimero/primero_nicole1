<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✨ Add Magic User</title>
    <link rel="stylesheet" href="<?=base_url();?>/public/css/style.css">
</head>
<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="center-box">
            <h1>✨ Add Magic User</h1>

            <?php flash_alert(); ?>

            <form id="addUserForm" method="post" action="<?=site_url('users/add_User');?>">

                <!-- CSRF -->
                <?php csrf_field(); ?>

                <!-- Username -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" type="text" class="form-control" name="username" placeholder="Enter username" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" class="form-control" name="email" placeholder="Enter email address" required>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" class="form-control" name="password" placeholder="Enter password" required minlength="8">
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="Re-enter password" required minlength="8">
                </div>
                
                <!-- Role -->
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">-- Select Role --</option>
                        <option value="admin">Admin</option>
                        <option value="fairy">Fairy</option>
                    </select>
                </div>

                <!-- Submit -->
                <button type="submit" class="submit-btn">✨ Add User</button>
            </form>

            <!-- Back link -->
            <p style="margin-top: 15px;">
                <a href="<?=site_url('users/view');?>" class="back-btn">← Go Back</a>
            </p>
        </div>
    </div>

    <!-- Validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <script>
        $(function() {
            $("#addUserForm").validate({
                rules: {
                    username: { required: true, minlength: 5, maxlength: 20 },
                    email: { required: true, email: true },
                    password: { required: true, minlength: 8 },
                    password_confirmation: { required: true, minlength: 8, equalTo: "#password" },
                    role: { required: true }
                },
                messages: {
                    username: { required: "Please input username.", minlength: "At least 5 characters.", maxlength: "Max 20 characters." },
                    email: { required: "Please input email address.", email: "Please enter a valid email." },
                    password: { required: "Please input password.", minlength: "Password must be at least 8 characters." },
                    password_confirmation: { required: "Please confirm password.", minlength: "At least 8 characters.", equalTo: "Passwords do not match." },
                    role: { required: "Please select a role." }
                }
            });
        });
    </script>
</body>
</html>
