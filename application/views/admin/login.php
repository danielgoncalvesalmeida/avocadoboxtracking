<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php if(!empty($meta['title'])): ?><title><?php echo $meta['title'] ?></title><?php endif; ?>
    
    <?php if(!empty($meta['description'])): ?><meta name="description" content="<?php echo $meta['description'] ?>"><?php endif; ?>


    <!-- Bootstrap -->
    <link href="<?php echo sbase_url() ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
    <!-- App specific -->
    <link href="<?php echo sbase_url() ?>assets/css/global.css" rel="stylesheet" media="all">


    <!-- Controller output - CSS -->
    <?php echo output_css(); ?>
    <!-- /Controller output - CSS -->




<div class="container">
 
<?php
  $attributes = array('class' => 'form-signin');
  echo form_open('',$attributes);
?>
    <?php if(validation_errors() || isset($authfailed)): ?>
        <?php echo form_error('edusr'); ?>
        <?php echo form_error('edpwd'); ?>
        <?php echo (isset($authfailed) ? $authfailed : ''); ?>
    <?php endif; ?>
    <h2 class="form-signin-heading"><img src="<?php echo sbase_url() ?>assets/img/favicon.ico.png">&nbsp;Avocado</h2>
    <label for="inputEmail" class="sr-only">Email address</label>
    <input name="edusr" type="text" id="inputEmail" class="form-control" placeholder="username" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input name="edpwd" type="password" id="inputPassword" class="form-control" placeholder="password" required>
    <div class="checkbox">
      <label>
        <input type="checkbox" value="remember-me"> Remember me
      </label>
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
  </form>
</div>

<!-- Controller output - JS -->
<?php echo output_js(); ?>
<!-- /Controller output - JS -->


</body>
</html>

