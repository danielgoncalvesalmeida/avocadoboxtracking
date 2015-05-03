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

    <style type="text/css">
        body {
            padding: 20px;
            background-color: #fff;
        }

    </style>
    
    <!-- Controller output - CSS -->
    <?php echo output_css(); ?>
    <!-- /Controller output - CSS -->
    <script>
        var base_url = <?php echo '"'.  sbase_url().'"' ?>;
        var csrf_token_name = <?php echo '"'.$this->security->get_csrf_token_name().'"' ?>;
        var csrf_token_hash = <?php echo '"'.$this->security->get_csrf_hash().'"' ?>;
    </script>    

	</head>
<body <?php if(isset($page_name)): ?>id="<?php echo $page_name ?>"<?php endif; ?> >
    
<!-- container -->
<div class="container">

<nav class="navbar navbar-default" role="navigation">	
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?php echo sbase_url() ?>admin/dashboard">Dashboard</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="navbar-collapse-1">
        <ul class="nav navbar-nav">
            
        <?php if (_cr('boxes') || 1==1 ): ?>
        <!-- Children menu -->
          <li class="dropdown"><a class="dropdown-toggle"  data-toggle="dropdown" href="#">Enfants <b class="caret"></b></a>
                <ul class="dropdown-menu" >
                    <li><a href="<?php echo sbase_url() ?>admin/children">Liste des enfants</a></li>
                    <li class="divider"></li>
                    <li><a href="<?php echo sbase_url() ?>admin/parents">Liste des parents</a></li>
                </ul>
          </li>
        <?php endif; ?>
          
        <?php if (_cr('employees')): ?>
        <!-- Employees menu -->
          <li class="dropdown"><a class="dropdown-toggle"  data-toggle="dropdown" href="#">Employés <b class="caret"></b></a>
            <ul class="dropdown-menu" >
              <li><a href="<?php echo sbase_url() ?>admin/employees">Utilisateurs</a></li>
              <!--
              <li class="divider"></li>
              <li><a href="#">Congés</a></li>
            -->
            </ul>
          </li>
        <?php endif; ?>
          
        <?php if (_cr('admin')): ?>
        <!-- Admin -->
        <li class="dropdown"><a class="dropdown-toggle"  data-toggle="dropdown" href="#">Admin <b class="caret"></b></a>
          <ul class="dropdown-menu" >
              <li><a href="<?php echo sbase_url() ?>admin/profiles">Profiles</a></li>
          </ul>
        </li>
        <?php endif; ?>
        
        <!-- Close the left part of the navbar links -->
        </ul>
        

        <!-- right menu -->
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown"><a class="dropdown-toggle"  data-toggle="dropdown" href="#"><?php echo displayUserName($this->session->userdata('firstname'),$this->session->userdata('lastname')) ?> <b class="caret"></b></a>
            <ul class="dropdown-menu" >
              <!-- <li><a href="#">Congés</a></li> -->
              <li><a href="<?php echo sbase_url() ?>admin/employees/editpassword">Changer password</a></li>
              <li class="divider"></li>
              <li><a href="<?php echo sbase_url() ?>login/logout">Déconnecter</a></li>
            </ul>
          </li>
        </ul>
    </div>
</nav>


<!-- Views -->
<?php echo $output ?>
<!-- /Views -->    

<div class="clearfix"><br /></div>



<!-- Benchmark -->
<div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Information</strong>
    <br />
    Elapsed time : <?php echo $this->benchmark->elapsed_time();?>
    <br />
    Memory usage : <?php echo $this->benchmark->memory_usage();?>
    <br>
    <?php echo $this->benchmark->elapsed_time('qrybegin', 'qryend'); ?>
</div>

<?php
    if (1 == 1 && ENVIRONMENT === 'development'):
?>
    <!-- Benchmark -->
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Debug</strong>
        <br /> 
        isDomainAdmin : <?php  echo isDomainAdmin() ?>
        <br />
        Timestamp : <?php  echo $this->session->userdata('timestamp') ?>
        <br />
        Id User : <?php  echo $this->session->userdata('id_user') ?>
        <br />
        ID Domain : <?php  echo $this->session->userdata('id_domain') ?>
        <br />
        Domain Name : <?php  echo $this->session->userdata('domain_name') ?>
        <br />
    </div>
<?php
    endif;
?>

</div>
<!-- /container -->

    <script src="<?php echo sbase_url() ?>assets/jquery/jquery-1.11.2.min.js"></script>
    <script src="<?php echo sbase_url() ?>assets/bootstrap/js/bootstrap.min.js"></script>    
    
    <!-- Controller output - JS -->
    <?php echo output_js(); ?>
    <!-- /Controller output - JS -->
    
    <script type="text/javascript">  
        //$('body').on('touchstart.dropdown','.dropdown-menu',function(e) {e.stopPropagation();});
        $(document).ready(function () {  
            $('.dropdown-toggle').dropdown();  
        });  
    </script> 
</body>
</html>