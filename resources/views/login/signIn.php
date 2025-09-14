<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <main role="main" aria-labelledby="signin-heading">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <header class="text-center mb-4">
                        <h1 id="signin-heading" class="font-weight-bold text-primary mb-2">Welcome Back</h1>
                        <p class="text-muted">Sign in to your account</p>
                    </header>

                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite" aria-atomic="true">
                        <i class="fas fa-exclamation-triangle mr-2" aria-hidden="true"></i>
                        <strong>Error:</strong> <?php echo htmlspecialchars($_SESSION['login_error']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close error message">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['login_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite" aria-atomic="true">
                        <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>
                        <strong>Success:</strong> <?php echo htmlspecialchars($_SESSION['login_success']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close success message">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['login_success']); ?>
                <?php endif; ?>

                <form method="post" action="/signIn.php" novalidate role="form" aria-labelledby="signin-heading">
                    <input type="hidden" name="sign" value="in">
                    <fieldset>
                        <legend class="sr-only">Sign in form</legend>
                        <div class="mb-4">
                            <label for="email" class="font-weight-bold">Email Address <span class="text-danger" aria-label="required">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" aria-hidden="true">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                </div>
                                <input name="email" type="email" class="form-control form-control-lg" id="email" 
                                       placeholder="Enter your email address" required 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       autocomplete="email" aria-describedby="email-help">
                            </div>
                            <small id="email-help" class="form-text text-muted sr-only">Enter your registered email address</small>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="font-weight-bold">Password <span class="text-danger" aria-label="required">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" aria-hidden="true">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                </div>
                                <input name="password" type="password" class="form-control form-control-lg" id="password" 
                                       placeholder="Enter your password" required
                                       autocomplete="current-password" aria-describedby="password-help">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" 
                                            aria-label="Toggle password visibility" aria-pressed="false"
                                            style="border-left: 0; border-color: #e3e6f0;">
                                        <i class="fas fa-eye" id="togglePasswordIcon" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <small id="password-help" class="form-text text-muted sr-only">Enter your account password</small>
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold" name="btn_submit">
                                <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>Sign In
                            </button>
                        </div>
                    </fieldset>

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
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');
    
    if (togglePassword && passwordInput && togglePasswordIcon) {
        togglePassword.addEventListener('click', function() {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Update aria-pressed attribute
            const isPressed = type === 'text';
            togglePassword.setAttribute('aria-pressed', isPressed);
            
            // Update aria-label
            togglePassword.setAttribute('aria-label', isPressed ? 'Hide password' : 'Show password');
            
            // Toggle the icon
            if (type === 'password') {
                togglePasswordIcon.classList.remove('fa-eye-slash');
                togglePasswordIcon.classList.add('fa-eye');
            } else {
                togglePasswordIcon.classList.remove('fa-eye');
                togglePasswordIcon.classList.add('fa-eye-slash');
            }
        });
        
        // Handle keyboard navigation
        togglePassword.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                togglePassword.click();
            }
        });
    }
});
</script>
