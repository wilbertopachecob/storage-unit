<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="font-weight-bold text-primary mb-2">Welcome Back</h2>
                    <p class="text-muted">Sign in to your account</p>
                </div>

                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <?php echo htmlspecialchars($_SESSION['login_error']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['login_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo htmlspecialchars($_SESSION['login_success']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['login_success']); ?>
                <?php endif; ?>

                <form method="post" action="/signIn.php" novalidate>
                    <input type="hidden" name="sign" value="in">
                    <div class="mb-4">
                        <label for="email" class="font-weight-bold">Email Address</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                            </div>
                            <input name="email" type="email" class="form-control form-control-lg" id="email" 
                                   placeholder="Enter your email" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="font-weight-bold">Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                            </div>
                            <input name="password" type="password" class="form-control form-control-lg" id="password" 
                                   placeholder="Enter your password" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword" 
                                        style="border-left: 0; border-color: #e3e6f0;">
                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold" name="btn_submit">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-muted mb-0">
                            Don't have an account? 
                            <a href="/signUp.php" class="text-primary font-weight-bold">
                                Sign up here
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');
    
    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle the icon
        if (type === 'password') {
            togglePasswordIcon.classList.remove('fa-eye-slash');
            togglePasswordIcon.classList.add('fa-eye');
        } else {
            togglePasswordIcon.classList.remove('fa-eye');
            togglePasswordIcon.classList.add('fa-eye-slash');
        }
    });
});
</script>
