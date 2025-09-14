<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <main role="main" aria-labelledby="signup-heading">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <header class="text-center mb-4">
                        <h1 id="signup-heading" class="font-weight-bold text-primary mb-2">Create Account</h1>
                        <p class="text-muted">Join us today</p>
                    </header>

                <?php if (isset($_SESSION['signup_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite" aria-atomic="true">
                        <i class="fas fa-exclamation-triangle mr-2" aria-hidden="true"></i>
                        <strong>Error:</strong> <?php echo htmlspecialchars($_SESSION['signup_error']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close error message">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['signup_error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['signup_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite" aria-atomic="true">
                        <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>
                        <strong>Success:</strong> <?php echo htmlspecialchars($_SESSION['signup_success']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close success message">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['signup_success']); ?>
                <?php endif; ?>

                <form method="post" action="/signUp.php" novalidate role="form" aria-labelledby="signup-heading">
                    <input type="hidden" name="sign" value="up">
                    <fieldset>
                        <legend class="sr-only">Create account form</legend>
                        <div class="mb-4">
                            <label for="name" class="font-weight-bold">Full Name <span class="text-danger" aria-label="required">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" aria-hidden="true">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                </div>
                                <input name="name" type="text" class="form-control form-control-lg" id="name" 
                                       placeholder="Enter your full name" required
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                       autocomplete="name" aria-describedby="name-help">
                            </div>
                            <small id="name-help" class="form-text text-muted sr-only">Enter your first and last name</small>
                        </div>

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
                            <small id="email-help" class="form-text text-muted sr-only">Enter a valid email address for your account</small>
                        </div>

                        <div class="mb-4">
                            <label for="signupPassword" class="font-weight-bold">Password <span class="text-danger" aria-label="required">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" aria-hidden="true">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                </div>
                                <input name="password" type="password" class="form-control form-control-lg" id="signupPassword" 
                                       placeholder="Enter your password" required
                                       autocomplete="new-password" aria-describedby="password-help">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="toggleSignupPassword" 
                                            aria-label="Toggle password visibility" aria-pressed="false"
                                            style="border-left: 0; border-color: #e3e6f0;">
                                        <i class="fas fa-eye" id="toggleSignupPasswordIcon" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <small id="password-help" class="form-text text-muted sr-only">Create a strong password for your account</small>
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold" name="btn_submit">
                                <i class="fas fa-user-plus mr-2" aria-hidden="true"></i>Create Account
                            </button>
                        </div>
                    </fieldset>

                    <div class="text-center">
                        <p class="text-muted mb-0">
                            Already have an account? 
                            <a href="/signIn.php" class="text-primary font-weight-bold">
                                Sign in here
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
    const toggleSignupPassword = document.getElementById('toggleSignupPassword');
    const signupPasswordInput = document.getElementById('signupPassword');
    const toggleSignupPasswordIcon = document.getElementById('toggleSignupPasswordIcon');
    
    if (toggleSignupPassword && signupPasswordInput && toggleSignupPasswordIcon) {
        toggleSignupPassword.addEventListener('click', function() {
            // Toggle the type attribute
            const type = signupPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            signupPasswordInput.setAttribute('type', type);
            
            // Update aria-pressed attribute
            const isPressed = type === 'text';
            toggleSignupPassword.setAttribute('aria-pressed', isPressed);
            
            // Update aria-label
            toggleSignupPassword.setAttribute('aria-label', isPressed ? 'Hide password' : 'Show password');
            
            // Toggle the icon
            if (type === 'password') {
                toggleSignupPasswordIcon.classList.remove('fa-eye-slash');
                toggleSignupPasswordIcon.classList.add('fa-eye');
            } else {
                toggleSignupPasswordIcon.classList.remove('fa-eye');
                toggleSignupPasswordIcon.classList.add('fa-eye-slash');
            }
        });
        
        // Handle keyboard navigation
        toggleSignupPassword.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleSignupPassword.click();
            }
        });
    }
});
</script>
