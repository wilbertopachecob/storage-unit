<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="font-weight-bold text-primary mb-2">Create Account</h2>
                    <p class="text-muted">Join us today</p>
                </div>

                <?php if (isset($_SESSION['signup_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <?php echo htmlspecialchars($_SESSION['signup_error']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['signup_error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['signup_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo htmlspecialchars($_SESSION['signup_success']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['signup_success']); ?>
                <?php endif; ?>

                <form method="post" action="/signUp.php" novalidate>
                    <input type="hidden" name="sign" value="up">
                    <div class="mb-4">
                        <label for="name" class="font-weight-bold">Full Name</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                            </div>
                            <input name="name" type="text" class="form-control form-control-lg" id="name" 
                                   placeholder="Enter your full name" required
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>
                    </div>

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
                            <input name="password" type="password" class="form-control form-control-lg" id="signupPassword" 
                                   placeholder="Enter your password" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="toggleSignupPassword" 
                                        style="border-left: 0; border-color: #e3e6f0;">
                                    <i class="fas fa-eye" id="toggleSignupPasswordIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold" name="btn_submit">
                            <i class="fas fa-user-plus mr-2"></i>Create Account
                        </button>
                    </div>

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
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleSignupPassword = document.getElementById('toggleSignupPassword');
    const signupPasswordInput = document.getElementById('signupPassword');
    const toggleSignupPasswordIcon = document.getElementById('toggleSignupPasswordIcon');
    
    toggleSignupPassword.addEventListener('click', function() {
        // Toggle the type attribute
        const type = signupPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        signupPasswordInput.setAttribute('type', type);
        
        // Toggle the icon
        if (type === 'password') {
            toggleSignupPasswordIcon.classList.remove('fa-eye-slash');
            toggleSignupPasswordIcon.classList.add('fa-eye');
        } else {
            toggleSignupPasswordIcon.classList.remove('fa-eye');
            toggleSignupPasswordIcon.classList.add('fa-eye-slash');
        }
    });
});
</script>
