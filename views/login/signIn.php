<div class="row">
    <div class="col-md-4 offset-md-4">
        <h1>Sign In</h1>
        <form method="post" action="<?=$_SERVER['PHP_SELF']?>?sign=in">
            <div class="form-group">
                <label for="email">Email address</label>
                <input name="email" type="email" class="form-control" id="email" placeholder="name@example.com">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input name="password" type="password" class="form-control" id="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary" name="btn_submit">Sign In</button>
        </form>
    </div>
</div>
